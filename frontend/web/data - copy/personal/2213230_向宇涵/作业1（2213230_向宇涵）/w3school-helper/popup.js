async function exec(fn, args = []) {
  const [tab] = await chrome.tabs.query({ active: true, currentWindow: true });
  await chrome.scripting.executeScript({
    target: { tabId: tab.id },
    func: fn,
    args
  });
}

const call = (name, ...args) => exec((n, a) => {
  const f = window[n];
  if (typeof f === "function") return f(...a);
  alert("content.js 未加载或函数不存在：" + n);
}, [name, args]);

document.getElementById("toggleDark").addEventListener("click", () => call("__W3HelperToggleDark"));
document.getElementById("biggerFont").addEventListener("click", () => call("__W3HelperBiggerFont"));
document.getElementById("readingMode").addEventListener("click", () => call("__W3HelperToggleReading"));
document.getElementById("toggleToc").addEventListener("click", () => call("__W3HelperToggleTOC"));
document.getElementById("toTop").addEventListener("click", () => call("__W3HelperToTop"));

document.getElementById("highlight").addEventListener("click", async () => {
  const kw = document.getElementById("kw").value.trim();
  await call("__W3HelperHighlight", kw);
});

document.getElementById("clearHighlight").addEventListener("click", () => call("__W3HelperClearHighlight"));
