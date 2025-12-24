// 管理阅读计时器，使其在 popup 关闭时仍继续运行。

const TIMER_KEY = 'w3read_timer_state_v1';

// 保存计时器状态到 chrome.storage.local
async function saveState(state) {
  return new Promise(resolve => chrome.storage.local.set({ [TIMER_KEY]: state }, resolve));
}

// 从 chrome.storage.local 读取计时器状态
async function loadState() {
  return new Promise(resolve => chrome.storage.local.get([TIMER_KEY], res => resolve(res[TIMER_KEY] || null)));
}

// 广播当前计时器状态给所有页面
function sendTimerUpdate() {
  loadState().then(state => {
    chrome.runtime.sendMessage({ cmd: 'timerUpdate', state });
  });
}

// 处理来自 popup 或其他脚本的消息
chrome.runtime.onMessage.addListener((msg, sender, sendResp) => {
  (async () => {
    if (!msg || !msg.cmd) return;
    if (msg.cmd === 'timerStart') {
      const total = msg.totalSeconds || 1500;
      const endAt = Date.now() + total * 1000;
      const state = { running: true, endAt, totalSeconds: total };
      await saveState(state);
      // 使用 alarms 以便在后台可靠唤醒
      chrome.alarms.create('w3read_timer_tick', { periodInMinutes: 1/60 }); // 每秒或近似
      sendTimerUpdate();
      sendResp && sendResp({ ok: true });
      return true;
    }
    if (msg.cmd === 'timerStop') {
      const state = { running: false, endAt: null, totalSeconds: 0 };
      await saveState(state);
      chrome.alarms.clear('w3read_timer_tick');
      sendTimerUpdate();
      sendResp && sendResp({ ok: true });
      return true;
    }
    if (msg.cmd === 'getTimerState') {
      const s = await loadState();
      sendResp && sendResp({ state: s });
      return true;
    }
  })();
  return true;
});

// 监听 alarms 事件，处理计时器的每次 tick 与完成逻辑
chrome.alarms.onAlarm.addListener(async (alarm) => {
  if (alarm && alarm.name === 'w3read_timer_tick') {
    const state = await loadState();
    if (!state || !state.running) return;
    const now = Date.now();
    if (now >= state.endAt) {
      // 计时结束
      const newState = { running: false, endAt: null, totalSeconds: 0 };
      await saveState(newState);
      chrome.alarms.clear('w3read_timer_tick');
      // 发送系统通知
      chrome.notifications.create({
        type: 'basic',
        iconUrl: 'icons/icon128.png',
        title: 'w3read：阅读计时完成',
        message: '时间到 — 可以休息或继续学习。'
      });
      // 广播更新
      sendTimerUpdate();
    } else {
      // 每次 tick 广播当前状态
      sendTimerUpdate();
    }
  }
});

// 初始化扩展，恢复未完成的计时器并创建必要的 alarm
(async function init(){
  const s = await loadState();
  if (s && s.running) {
    chrome.alarms.create('w3read_timer_tick', { periodInMinutes: 1/60 });
  }
})();
