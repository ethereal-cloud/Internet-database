const STORAGE_KEY = 'w3read_data_v1';

document.addEventListener('DOMContentLoaded', async () => {
  const btnAdd = document.getElementById('btn-add-bookmark');
  const btnRemoveAds = document.getElementById('btn-remove-ads');
  const btnClearAllHighlights = document.getElementById('btn-clear-all-highlights');
  const chkNight = document.getElementById('chk-night');
  const bookmarkList = document.getElementById('bookmark-list');

  const timerMinutes = document.getElementById('timer-minutes');
  const timerStart = document.getElementById('timer-start');
  const timerStop = document.getElementById('timer-stop');
  const timerVisual = document.getElementById('timer-visual');

  // 加载本地存储的数据并初始化书签列表
  const res = await chrome.storage.local.get([STORAGE_KEY]);
  const data = res[STORAGE_KEY] || {};
  const bookmarks = (data.bookmarks || []).slice().sort((a,b)=>b.time - a.time);

  function renderBookmarks() {
    bookmarkList.innerHTML = '';
    if (bookmarks.length === 0) {
      bookmarkList.innerHTML = `<div style="color:#9aa4b2">还没有收藏</div>`;
      return;
    }
    bookmarks.forEach(b => {
      const item = document.createElement('div');
      item.className = 'item';
      item.innerHTML = `<div class="title">${escapeHtml(b.title)}</div>
                        <div class="meta">${new Date(b.time).toLocaleString()} · <a href="#" data-url="${b.url}">打开</a> · <a href="#" data-del="${b.id}">删除</a></div>`;
      bookmarkList.appendChild(item);
    });
    // 为书签项添加打开与删除事件监听器
    bookmarkList.querySelectorAll('[data-url]').forEach(a => {
      a.addEventListener('click', (e) => {
        e.preventDefault();
        const url = a.dataset.url;
        chrome.tabs.create({ url });
      });
    });
    bookmarkList.querySelectorAll('[data-del]').forEach(a => {
      a.addEventListener('click', async (e) => {
        e.preventDefault();
        const id = a.dataset.del;
        const idx = bookmarks.findIndex(x => x.id === id);
        if (idx !== -1) bookmarks.splice(idx,1);
        data.bookmarks = bookmarks;
        await chrome.storage.local.set({[STORAGE_KEY]: data});
        renderBookmarks();
      });
    });
  }
  renderBookmarks();

  // 将当前活动页面添加为书签并更新 UI
  btnAdd.addEventListener('click', async () => {
    const [tab] = await chrome.tabs.query({active:true,currentWindow:true});
    chrome.storage.local.get([STORAGE_KEY], async (r) => {
      const d = r[STORAGE_KEY] || {};
      d.bookmarks = d.bookmarks || [];
      const exists = d.bookmarks.some(x => x.url === tab.url);
      if (exists) {
        alert('该页面已在收藏列表中，无需重复收藏');
        return;
      }
      d.bookmarks.push({
        id: 'w3r_' + Math.random().toString(36).slice(2,9),
        title: tab.title,
        url: tab.url,
        time: Date.now()
      });
      await chrome.storage.local.set({[STORAGE_KEY]: d});
      const cur = d.bookmarks.slice().sort((a,b)=>b.time-a.time);
      bookmarks.splice(0, bookmarks.length, ...cur);
      renderBookmarks();
      alert('收藏成功');
    });
  });

  // 请求内容脚本尝试移除当前页广告并提示结果
  btnRemoveAds.addEventListener('click', async () => {
    const [tab] = await chrome.tabs.query({active:true,currentWindow:true});
    chrome.tabs.sendMessage(tab.id, { cmd: 'removeAds' }, (resp) => {
      alert('已尝试移除广告，移除元素数：' + (resp?.removed || 0));
    });
  });

  // 在 popup 中清除当前页面所有高亮
  btnClearAllHighlights.addEventListener('click', async () => {
    const [tab] = await chrome.tabs.query({active:true,currentWindow:true});
    chrome.tabs.sendMessage(tab.id, { cmd: 'clearAllHighlights' }, (resp) => {
      alert('已请求清除所有高亮');
    });
  });

  // 切换夜间模式并通知内容脚本
  chkNight.addEventListener('change', async () => {
    const enable = chkNight.checked;
    const [tab] = await chrome.tabs.query({active:true,currentWindow:true});
    chrome.tabs.sendMessage(tab.id, { cmd: 'toggleNight', enable }, (resp) => {});
  });

  // 初始化当前页面的夜间模式复选框状态
  (async() => {
    const [tab] = await chrome.tabs.query({active:true,currentWindow:true});
    chrome.tabs.sendMessage(tab.id, { cmd: 'getPageData' }, (resp) => {
      if (resp && resp.page && resp.page.night) chkNight.checked = true;
    });
  })();

  // 计时器 UI，计时逻辑委托给后台 Service Worker
  let svgTimer = null;
  let currentTotal = 0;

  function createTimerSVG(size=140) {
    timerVisual.innerHTML = '';
    const ns = 'http://www.w3.org/2000/svg';
    const svg = document.createElementNS(ns, 'svg');
    svg.setAttribute('width', size);
    svg.setAttribute('height', size);
    const r = size/2 - 10;
    const cx = size/2, cy = size/2;
    const circleBg = document.createElementNS(ns,'circle');
    circleBg.setAttribute('cx',cx); circleBg.setAttribute('cy',cy); circleBg.setAttribute('r',r);
    circleBg.setAttribute('fill','none'); circleBg.setAttribute('stroke','#eef2ff'); circleBg.setAttribute('stroke-width','12');
    const circle = document.createElementNS(ns,'circle');
    circle.setAttribute('cx',cx); circle.setAttribute('cy',cy); circle.setAttribute('r',r);
    circle.setAttribute('fill','none'); circle.setAttribute('stroke','#3b82f6'); circle.setAttribute('stroke-width','12');
    circle.setAttribute('stroke-linecap','round');
    circle.setAttribute('transform',`rotate(-90 ${cx} ${cy})`);
    const circumference = 2*Math.PI*r;
    circle.style.strokeDasharray = circumference;
    circle.style.strokeDashoffset = circumference;
    svg.appendChild(circleBg); svg.appendChild(circle);

    const txt = document.createElement('div');
    txt.style.position='absolute'; txt.style.pointerEvents='none'; txt.style.fontSize='16px'; txt.style.fontWeight=600;
    txt.style.width=size+'px'; txt.style.textAlign='center'; txt.style.marginTop=-(size)+'px'; txt.style.paddingTop=(size/2 - 10)+'px';
    timerVisual.style.position='relative';
    timerVisual.appendChild(svg);
    timerVisual.appendChild(txt);

    return {svg, circle, txt, circumference};
  }

  function updateTimerUI(seconds, total) {
    if (!svgTimer) svgTimer = createTimerSVG();
    currentTotal = total || currentTotal || 1;
    const {circle, txt, circumference} = svgTimer;
    const progress = Math.max(0, Math.min(1, 1 - seconds/currentTotal));
    circle.style.strokeDashoffset = (circumference * (1 - progress)).toFixed(2);
    const mm = Math.floor(seconds/60);
    const ss = Math.floor(seconds%60).toString().padStart(2,'0');
    txt.innerText = `${mm}:${ss}`;
  }

  timerStart.addEventListener('click', async () => {
    const mins = Math.max(1, parseInt(timerMinutes.value)||25);
    const totalSeconds = mins*60;
    // 告诉 background 启动计时器
    chrome.runtime.sendMessage({ cmd: 'timerStart', totalSeconds }, (resp) => {
    });
  });

  timerStop.addEventListener('click', async () => {
    chrome.runtime.sendMessage({ cmd: 'timerStop' }, (resp) => {
      // 请求后台停止计时器并清理 UI
      svgTimer = null;
      timerVisual.innerHTML = '';
      timerVisual.classList.remove('w3r-timer-done');
    });
  });

  // 打开弹出层时请求当前计时器状态并同步 UI
  chrome.runtime.sendMessage({ cmd: 'getTimerState' }, (resp) => {
    if (resp && resp.state) {
      const s = resp.state;
      if (s && s.running && s.endAt) {
        const remain = Math.max(0, Math.ceil((s.endAt - Date.now())/1000));
        updateTimerUI(remain, s.totalSeconds || remain);
      }
    }
  });

  // 监听后台广播的计时器更新并刷新 UI
  chrome.runtime.onMessage.addListener((msg) => {
    if (msg && msg.cmd === 'timerUpdate') {
      const s = msg.state;
      if (!s || !s.running) {
        svgTimer = null; timerVisual.innerHTML = ''; timerVisual.classList.remove('w3r-timer-done');
        return;
      }
      const remain = Math.max(0, Math.ceil((s.endAt - Date.now())/1000));
      updateTimerUI(remain, s.totalSeconds || remain);
      if (remain <= 0) {
      }
    }
  });

  // 计时完成处理：保留空函数，实际通知由后台 chrome.notifications 发送
  function onTimerComplete() { }

  function escapeHtml(s) {
    return (s+'').replace(/[&<>"']/g, (m)=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[m]));
  }
});
