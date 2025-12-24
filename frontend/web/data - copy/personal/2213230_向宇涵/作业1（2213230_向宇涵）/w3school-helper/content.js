(function () {
  const STYLE_ID = "w3helper-style";
  const TOC_ID = "w3helper-toc";
  const HL_CLASS = "w3helper-hl";
  let hlOriginalHtml = null;

  function ensureStyle() {
    if (document.getElementById(STYLE_ID)) return;
    const st = document.createElement("style");
    st.id = STYLE_ID;
    st.textContent = `
      html.w3helper-dark { filter: invert(1) hue-rotate(180deg); }
      html.w3helper-dark img, html.w3helper-dark video { filter: invert(1) hue-rotate(180deg); }

      html.w3helper-bigfont body { font-size: 25px !important; line-height: 1.7 !important; }
      html.w3helper-bigfont .w3-main,
      html.w3helper-bigfont .w3-main p,
      html.w3helper-bigfont .w3-main li {
          font-size: 22px !important;
          line-height: 1.8 !important;
      }
      html.w3helper-bigfont h1 { font-size: 1.8em !important; }
      html.w3helper-bigfont h2 { font-size: 1.5em !important; }
      html.w3helper-bigfont h3 { font-size: 1.2em !important; }
      html.w3helper-bigfont pre, html.w3helper-bigfont code { font-size: 1.05em !important; }

      html.w3helper-reading #navfirst,
      html.w3helper-reading #navsecond,
      html.w3helper-reading #navthird,
      html.w3helper-reading #sidebar,
      html.w3helper-reading #header,
      html.w3helper-reading .w3-sidebar,
      html.w3helper-reading .left,
      html.w3helper-reading .w3-col.l2,
      html.w3helper-reading .w3-col.l3 { display:none !important; }
      html.w3helper-reading .w3-main { margin-left:0 !important; }
      html.w3helper-reading body { max-width: 980px; margin: 0 auto !important; }

      #${TOC_ID}{
        position:fixed; top:80px; right:16px; width:260px; max-height:70vh;
        overflow:auto; background:#fff; border:1px solid #999; z-index:999999;
        padding:10px; font-size:13px; line-height:1.5;
      }
      #${TOC_ID} a{display:block; padding:4px 6px; text-decoration:none; color:#333;}
      #${TOC_ID} a:hover{background:#f2f2f2;}
      #${TOC_ID} .lv2{padding-left:14px;}
      #${TOC_ID} .lv3{padding-left:26px;}
      #${TOC_ID} .head{display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;}
      #${TOC_ID} .close{cursor:pointer; border:1px solid #999; padding:0 6px; border-radius:4px;}

      mark.${HL_CLASS}{ background: #ffeb3b; padding:0 2px; }

      .w3helper-copybtn{
        position:absolute; top:8px; right:8px; padding:4px 8px; cursor:pointer;
        border:1px solid #999; background:#fff; font-size:12px; z-index:2;
      }
      .w3helper-prewrap{ position:relative; }
    `;
    document.head.appendChild(st);
  }

  //基础功能
  window.__W3HelperToggleDark = function () {
    ensureStyle();
    document.documentElement.classList.toggle("w3helper-dark");
  };

  window.__W3HelperBiggerFont = function () {
    ensureStyle();
    document.documentElement.classList.toggle("w3helper-bigfont");
  };

  window.__W3HelperToggleReading = function () {
    ensureStyle();
    document.documentElement.classList.toggle("w3helper-reading");
  };

  window.__W3HelperToTop = function () {
    window.scrollTo({ top: 0, behavior: "smooth" });
  };

  //TOC 目录
  function buildTOC() {
    ensureStyle();
    const old = document.getElementById(TOC_ID);
    if (old) old.remove();

    const hs = Array.from(document.querySelectorAll("h1, h2, h3"))
      .filter((h) => h.innerText && h.innerText.trim().length > 0)
      .slice(0, 60);

    if (hs.length === 0) {
      alert("未找到标题（h1/h2/h3），无法生成目录。");
      return;
    }

    hs.forEach((h, i) => {
      if (!h.id) h.id = "w3h_" + h.tagName.toLowerCase() + "_" + i;
    });

    const box = document.createElement("div");
    box.id = TOC_ID;
    box.innerHTML = `
      <div class="head">
        <b>目录 TOC</b>
        <span class="close" title="关闭">×</span>
      </div>
      <div class="items"></div>
    `;

    const items = box.querySelector(".items");
    hs.forEach((h) => {
      const a = document.createElement("a");
      a.href = "#" + h.id;
      a.textContent = h.innerText.trim();
      if (h.tagName === "H2") a.className = "lv2";
      if (h.tagName === "H3") a.className = "lv3";
      a.addEventListener("click", (e) => {
        e.preventDefault();
        document.getElementById(h.id)?.scrollIntoView({ behavior: "smooth", block: "start" });
      });
      items.appendChild(a);
    });

    box.querySelector(".close").addEventListener("click", () => box.remove());
    document.body.appendChild(box);
  }

  window.__W3HelperToggleTOC = function () {
    ensureStyle();
    const box = document.getElementById(TOC_ID);
    if (box) box.remove();
    else buildTOC();
  };

  // 高亮搜索
  window.__W3HelperHighlight = function (keyword) {
    ensureStyle();
    if (!keyword) { alert("请输入关键词"); return; }

    window.__W3HelperClearHighlight();

    hlOriginalHtml = document.body.innerHTML;
    const esc = keyword.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
    const re = new RegExp(esc, "gi");

    const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT, {
      acceptNode(node) {
        const p = node.parentElement;
        if (!p) return NodeFilter.FILTER_REJECT;
        const tag = p.tagName?.toLowerCase();
        if (["script", "style", "noscript"].includes(tag)) return NodeFilter.FILTER_REJECT;
        if (!node.nodeValue || !node.nodeValue.trim()) return NodeFilter.FILTER_REJECT;

        re.lastIndex = 0;
        if (!re.test(node.nodeValue)) return NodeFilter.FILTER_REJECT;
        return NodeFilter.FILTER_ACCEPT;
      }
    });

    const nodes = [];
    while (walker.nextNode()) nodes.push(walker.currentNode);

    let count = 0;
    nodes.forEach((n) => {
      const span = document.createElement("span");
      re.lastIndex = 0;
      span.innerHTML = n.nodeValue.replace(re, (m) => {
        count++;
        return `<mark class="${HL_CLASS}">${m}</mark>`;
      });
      n.parentNode.replaceChild(span, n);
    });

    alert(`高亮完成：命中 ${count} 处`);
  };

  window.__W3HelperClearHighlight = function () {
    if (hlOriginalHtml !== null) {
      document.body.innerHTML = hlOriginalHtml;
      hlOriginalHtml = null;
      addCopyButtons(); // body 重写后需要重新加 Copy 按钮
    }
  };

  // 初次加载执行一次
  addCopyButtons();
})();
