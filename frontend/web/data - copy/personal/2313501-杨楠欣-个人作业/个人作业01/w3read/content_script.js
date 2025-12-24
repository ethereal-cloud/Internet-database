(() => {
  // 内容脚本初始化和设置常量
  const STORAGE_KEY = 'w3read_data_v1';
  let PAGE_KEY = location.origin + location.pathname + (location.search||'');
  const HIGHLIGHT_COLORS = ['#fff7c0','#d6f7e6','#dfe8ff','#f4d7ff','#e6d8f7ff'];
  const MARKER_CLASS = 'w3read-marker';
  let dataCache = {};

  let lastSelectionRange = null;
  let lastSelectionText = '';
  // 选择快照调度
  let _snapshotScheduled = false;
  function scheduleSnapshot() {
    if (_snapshotScheduled) return;
    _snapshotScheduled = true;
    setTimeout(() => { try { snapshotSelectionIfAny(); } catch(e){} finally { _snapshotScheduled = false; } }, 50);
  }

  function loadData() {
    return new Promise(resolve => {
      chrome.storage.local.get([STORAGE_KEY], res => {
        dataCache = res[STORAGE_KEY] || {};
        if (!dataCache.pages) dataCache.pages = {};
        if (!dataCache.bookmarks) dataCache.bookmarks = [];
        resolve();
      });
    });
  }

  // 生成元素 XPath
  function getElementXPath(node) {
    if (!node) return '';
    if (node.nodeType === Node.DOCUMENT_NODE) return '/';
    let isText = false;
    let textIndex = null;
    if (node.nodeType === Node.TEXT_NODE) {
      isText = true;
      const textNode = node;
      node = node.parentNode;
      textIndex = Array.prototype.indexOf.call(node.childNodes, textNode) + 1;
    }
    const parts = [];
    let anchorId = null;
    let cur = node;
    while (cur && cur.nodeType === Node.ELEMENT_NODE && cur !== document) {
      if (cur.id) { anchorId = cur.id; break; }
      let tag = cur.tagName.toLowerCase();
      let ix = 1;
      let sib = cur.previousSibling;
      while (sib) {
        if (sib.nodeType === Node.ELEMENT_NODE && sib.tagName === cur.tagName) ix++;
        sib = sib.previousSibling;
      }
      parts.unshift(`${tag}[${ix}]`);
      cur = cur.parentNode;
    }
    if (anchorId) {
      const rel = parts.length ? '/' + parts.join('/') : '';
      return isText ? `//*[@id="${anchorId}"]${rel}/text()[${textIndex}]` : `//*[@id="${anchorId}"]${rel}`;
    }
    const abs = '/' + parts.join('/');
    return isText ? `${abs}/text()[${textIndex}]` : abs;
  }

  // 数据存储与辅助函数
  function saveData() {
    return new Promise(resolve => {
      try {
        chrome.storage.local.set({ [STORAGE_KEY]: dataCache }, () => { resolve(); });
      } catch (e) { resolve(); }
    });
  }

  function generateId() {
    return 'w3r_' + Date.now().toString(36) + '_' + Math.random().toString(36).slice(2,9);
  }

  function injectStyles() {
    if (document.getElementById('w3read-style')) return;
    const s = document.createElement('style');
    s.id = 'w3read-style';
    s.innerText = `
      .w3read-highlight{ background: #fff7c0; border-radius:2px; padding:0 2px; }
      .w3read-translate-tooltip{ position:absolute; z-index:2147483647; background:#fff; border:1px solid rgba(0,0,0,0.08); padding:6px 10px; border-radius:10px; box-shadow:0 8px 24px rgba(0,0,0,0.12); display:flex; align-items:center; gap:8px; white-space:nowrap; max-width:calc(100vw - 24px); overflow:auto; }
      .w3read-tooltip-action{ border:1px solid rgba(0,0,0,0.06); padding:6px 8px; border-radius:8px; cursor:pointer; background:#fff; font-size:13px; color:#111; box-shadow:0 1px 0 rgba(0,0,0,0.03); min-width:48px; text-align:center }
      .w3read-tooltip-action:hover{ transform:translateY(-1px); box-shadow:0 6px 14px rgba(0,0,0,0.08); }
      .w3read-tooltip-btn{ width:18px; height:18px; border-radius:50%; box-shadow:0 1px 0 rgba(0,0,0,0.04); border:1px solid rgba(0,0,0,0.06); cursor:pointer; flex:0 0 auto }
      .w3read-tooltip-btn:hover{ transform:scale(1.08); }
      .w3read-marker{ position: absolute; width:18px; height:18px; border-radius:50%; box-shadow:0 0 6px rgba(0,0,0,0.15); background:#ff6fa8; border:2px solid #fff }
      .w3read-annotation-popup{ max-width:320px; z-index:2147483647 }
    `;
    document.documentElement.appendChild(s);
  }

  function snapshotSelectionIfAny() {
    try {
      const sel = document.getSelection();
      if (sel && sel.rangeCount && sel.toString().trim().length>0) {
        try { lastSelectionRange = sel.getRangeAt(0).cloneRange(); } catch(e){ lastSelectionRange = null; }
        try { lastSelectionText = sel.toString(); } catch(e){ lastSelectionText = ''; }
        return true;
      }
    } catch (e) { }
    lastSelectionRange = null; lastSelectionText = '';
    return false;
  }

  function restoreSavedSelection() {
    try {
      const sel = window.getSelection();
      if (!sel) return false;
      sel.removeAllRanges();
      if (lastSelectionRange) {
        try { sel.addRange(lastSelectionRange.cloneRange()); return true; } catch(e){ return false; }
      }
    } catch (e) { }
    return false;
  }

  function detectSelectionContext() {
    try {
      const sel = document.getSelection();
      if (!sel || !sel.rangeCount || sel.toString().trim().length===0) return { rootType: 'no-selection' };
      const node = sel.anchorNode;
      if (!node) return { rootType: 'unknown' };
      let root = node.ownerDocument || document;
      if (root.defaultView && root.defaultView !== window) return { rootType: 'iframe', rootDocument: root };
      return { rootType: 'document', rootDocument: document };
    } catch (e) { return { rootType: 'error', error: e && e.message }; }
  }

  function escapeHtml(s) {
    return (s+'').replace(/[&<>"']/g, (m)=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[m]));
  }

  // 恢复并应用高亮
  function applyHighlightsForPage() {
    try {
      const page = dataCache.pages[PAGE_KEY] || {};
      const hs = page.highlights || [];
      hs.forEach(h => {
        try {
          if (!h || !h.id) return;
          if (document.querySelector(`[data-w3r-id="${h.id}"]`)) return;
          const text = (h.text || '').trim();
          if (!text) return;
          const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT, {
            acceptNode: function(node) {
              if (node.nodeValue && node.nodeValue.indexOf(text) !== -1) return NodeFilter.FILTER_ACCEPT;
              return NodeFilter.FILTER_REJECT;
            }
          });
          const node = walker.nextNode();
          if (!node) return;
          const idx = node.nodeValue.indexOf(text);
          const range = document.createRange();
          range.setStart(node, idx);
          range.setEnd(node, idx + text.length);
          const span = document.createElement('span');
          span.className = 'w3read-highlight';
          span.style.background = h.color || HIGHLIGHT_COLORS[0];
          span.dataset.w3rHighlightId = h.id;
          span.setAttribute('data-w3r-id', h.id);
          try { range.surroundContents(span); } catch (e) { const frag = range.extractContents(); span.appendChild(frag); range.insertNode(span); }
        } catch (e) { }
      });
    } catch (e) { }
  }

  // 批注标记与拖拽
  function addMarkerToDOM(a) {
    if (document.querySelector(`[data-w3r-annotation-id="${a.id}"]`)) return;
    const marker = document.createElement('div');
    marker.className = MARKER_CLASS;
    marker.dataset.w3rAnnotationId = a.id;
    marker.style.position = 'absolute';
    marker.style.left = (a.x) + 'px';
    marker.style.top = (a.y) + 'px';
    marker.style.width = '18px';
    marker.style.height = '18px';
    marker.style.borderRadius = '50%';
    marker.style.boxShadow = '0 0 6px rgba(0,0,0,0.15)';
    marker.style.background = '#ff6fa8';
    marker.style.border = '2px solid #fff';
    marker.style.cursor = 'grab';
    marker.style.zIndex = '2147483646';
    document.body.appendChild(marker);

    let isDragging = false;
    let startX = 0, startY = 0, origLeft = 0, origTop = 0;
    let moved = false;

    function clamp(v, min, max) { return Math.max(min, Math.min(max, v)); }

    marker.addEventListener('pointerdown', (ev) => {
      ev.preventDefault(); ev.stopPropagation();
      try { marker.setPointerCapture && marker.setPointerCapture(ev.pointerId); } catch(e){}
      isDragging = true; moved = false;
      startX = ev.clientX; startY = ev.clientY;
      origLeft = parseInt(marker.style.left || '0', 10);
      origTop = parseInt(marker.style.top || '0', 10);
      marker.style.cursor = 'grabbing';
      marker.style.transition = 'none';
    });

    window.addEventListener('pointermove', (ev) => {
      if (!isDragging) return;
      ev.preventDefault(); ev.stopPropagation();
      const dx = ev.clientX - startX;
      const dy = ev.clientY - startY;
      if (Math.abs(dx) > 2 || Math.abs(dy) > 2) moved = true;
      const pad = 6;
      const maxX = Math.max(8, window.innerWidth - pad);
      const maxY = Math.max(8, window.innerHeight - pad);
      const newLeft = clamp(origLeft + dx, pad, maxX);
      const newTop = clamp(origTop + dy, pad, maxY);
      marker.style.left = newLeft + 'px';
      marker.style.top = newTop + 'px';
    }, { passive: false });

    marker.addEventListener('pointerup', async (ev) => {
      if (isDragging) {
        try { marker.releasePointerCapture && marker.releasePointerCapture(ev.pointerId); } catch(e){}
        isDragging = false;
        marker.style.cursor = 'pointer';
        marker.style.transition = '';
        if (moved) {
          const newX = parseInt(marker.style.left || '0', 10);
          const newY = parseInt(marker.style.top || '0', 10);
          try {
            const page = dataCache.pages[PAGE_KEY] || {};
            page.annotations = page.annotations || [];
            const idx = page.annotations.findIndex(x => x.id === a.id);
            if (idx !== -1) {
              page.annotations[idx].x = newX;
              page.annotations[idx].y = newY;
            } else {
              page.annotations.push({ id: a.id, x: newX, y: newY, text: a.text, createdAt: Date.now() });
            }
            dataCache.pages[PAGE_KEY] = page;
            await saveData();
          } catch (e) { }
        }
        marker._draggedRecently = moved;
        setTimeout(() => { marker._draggedRecently = false; }, 200);
      }
    });

    marker.addEventListener('click', (e) => {
      e.stopPropagation();
      if (marker._draggedRecently) return;
      showAnnotationPopup(a, marker);
    });
  }

  // 应用已保存的批注
  function applyAnnotationsForPage() {
    const page = dataCache.pages[PAGE_KEY];
    if (!page || !page.annotations) return;
    page.annotations.forEach(a => addMarkerToDOM(a));
  }

  // 显示批注弹窗
  function showAnnotationPopup(annotation, markerEl) {
    let existing = document.getElementById('w3read-annotation-popup');
    if (existing) existing.remove();
    const popup = document.createElement('div');
    popup.id = 'w3read-annotation-popup';
    popup.style.position = 'absolute';
    popup.style.left = (parseInt(markerEl.style.left||0)+24)+'px';
    popup.style.top = (parseInt(markerEl.style.top||0)-6)+'px';
    popup.style.background = '#fff';
    popup.style.padding = '10px';
    popup.style.borderRadius = '8px';
    popup.style.boxShadow = '0 6px 18px rgba(0,0,0,0.15)';
    popup.style.maxWidth = '320px';
    popup.style.zIndex = 2147483647;
    popup.innerHTML = `<div style="font-weight:600;margin-bottom:6px">批注</div>
             <div style="white-space:pre-wrap; max-width:300px; word-break:break-word;">${escapeHtml(annotation.text)}</div>
             <div style="margin-top:8px;text-align:right"><button id="w3r-del-anno">删除</button></div>`;
    document.body.appendChild(popup);
    document.getElementById('w3r-del-anno').addEventListener('click', async () => {
      const page = dataCache.pages[PAGE_KEY] || {};
      page.annotations = (page.annotations||[]).filter(x => x.id !== annotation.id);
      dataCache.pages[PAGE_KEY] = page;
      await saveData();
      document.querySelector(`[data-w3r-annotation-id="${annotation.id}"]`)?.remove();
      popup.remove();
    });
    setTimeout(() => {
      const closeFn = (ev) => { if (!popup.contains(ev.target) && ev.target !== markerEl) { popup.remove(); document.removeEventListener('click', closeFn); } };
      document.addEventListener('click', closeFn);
    }, 0);
  }

  // 选择工具提示
  let tooltipEl = null;
  function removeSelectionTooltip() {
    if (tooltipEl) { tooltipEl.remove(); tooltipEl = null; document.removeEventListener('click', outsideTooltipClick); }
  }
  function outsideTooltipClick(e) { if (tooltipEl && !tooltipEl.contains(e.target)) removeSelectionTooltip(); }

  function buildTooltip(x,y,text) {
    removeSelectionTooltip();
    tooltipEl = document.createElement('div');
    tooltipEl.className = 'w3read-translate-tooltip';
    tooltipEl.style.left = x + 'px';
    tooltipEl.style.top = y + 'px';
    const btnTranslate = document.createElement('button');
    btnTranslate.className = 'w3read-tooltip-action';
    btnTranslate.textContent = '翻译';
    btnTranslate.addEventListener('pointerdown', (ev) => {
      ev.preventDefault(); ev.stopPropagation();
      restoreSavedSelection();
      const url = 'https://translate.google.com/?sl=auto&tl=zh-CN&text=' + encodeURIComponent(text) + '&op=translate';
      window.open(url, '_blank');
      removeSelectionTooltip();
    });

    const hlContainer = document.createElement('div');
    hlContainer.style.display = 'flex';
    hlContainer.style.gap = '6px';
    HIGHLIGHT_COLORS.forEach(c => {
      const b = document.createElement('button');
      b.className = 'w3read-tooltip-btn';
      b.style.background = c;
      b.title = '高亮';
      b.addEventListener('pointerdown', (ev) => {
        ev.preventDefault(); ev.stopPropagation();
        restoreSavedSelection();
        createHighlight(lastSelectionText || text, c);
        removeSelectionTooltip();
      });
      hlContainer.appendChild(b);
    });

    const btnAnno = document.createElement('button');
    btnAnno.className = 'w3read-tooltip-action';
    btnAnno.textContent = '添加批注';
    btnAnno.addEventListener('pointerdown', (ev) => {
      ev.preventDefault(); ev.stopPropagation();
      restoreSavedSelection();
      createAnnotationAtSelection(lastSelectionText || text);
      removeSelectionTooltip();
    });

    const btnClear = document.createElement('button');
    btnClear.className = 'w3read-tooltip-action';
    btnClear.textContent = '清除高亮';
    btnClear.addEventListener('pointerdown', (ev) => {
      ev.preventDefault(); ev.stopPropagation();
      clearHighlightsForPage();
      removeSelectionTooltip();
    });

    tooltipEl.appendChild(btnTranslate);
    tooltipEl.appendChild(hlContainer);
    tooltipEl.appendChild(btnAnno);
    tooltipEl.appendChild(btnClear);
    document.body.appendChild(tooltipEl);
    setTimeout(()=> document.addEventListener('click', outsideTooltipClick), 0);
  }

  // 高亮与批注创建 or 清除
  async function createHighlight(text, color) {
    try {
      let range = null;
      if (lastSelectionRange) {
        try { range = lastSelectionRange.cloneRange(); } catch(e){ range = null; }
      }
      if (!range) {
        const sel = document.getSelection();
        if (sel && sel.rangeCount) range = sel.getRangeAt(0).cloneRange();
      }
      if (!range || range.collapsed) {
        alert('请先选择要高亮的文本 (注意：某些 iframe/编辑器中的选择不受插件控制)。');
        return;
      }
      const id = generateId();
      let sNode = range.startContainer;
      let eNode = range.endContainer;
      const sOffset = range.startOffset;
      const eOffset = range.endOffset;
      let sXPath = '', eXPath = '';
      try { sXPath = getElementXPath(sNode); eXPath = getElementXPath(eNode); } catch (err) { }
      const span = document.createElement('span');
      span.dataset.w3rHighlightId = id;
      span.setAttribute('data-w3r-id', id);
      span.className = 'w3read-highlight';
      span.style.background = color;
      try { range.surroundContents(span); } catch (e) { const frag = range.extractContents(); span.appendChild(frag); range.insertNode(span); }
      const page = dataCache.pages[PAGE_KEY] || {};
      page.highlights = page.highlights || [];
      const hinfo = { id, color, text: text || (range && range.toString()) || '', serialized: span.outerHTML, createdAt: Date.now(), startXPath: sXPath, endXPath: eXPath, startOffset: sOffset, endOffset: eOffset };
      page.highlights.push(hinfo);
      dataCache.pages[PAGE_KEY] = page;
      await saveData();
    } catch (e) {
      alert('高亮失败');
    }
  }

  async function clearHighlightsForPage() {
    try {
      const page = dataCache.pages[PAGE_KEY] || {};
      try {
        const sel = document.getSelection();
        if (sel && sel.rangeCount > 0 && sel.toString().trim().length > 0) {
          const node = sel.anchorNode;
          const el = node && node.nodeType === Node.ELEMENT_NODE ? node.closest('.w3read-highlight') : (node && node.parentElement ? node.parentElement.closest('.w3read-highlight') : null);
          if (el) {
            const hid = el.dataset.w3rHighlightId;
            const parent = el.parentNode;
            if (parent) {
              while (el.firstChild) parent.insertBefore(el.firstChild, el);
              parent.removeChild(el);
              parent.normalize?.();
            }
            page.highlights = (page.highlights || []).filter(h => h.id !== hid);
            dataCache.pages[PAGE_KEY] = page;
            await saveData();
            return;
          } else {
            // 如果用户选中文本但选区不在任何高亮内，则认为用户没有要清除的高亮，保持现有高亮不变
            return;
          }
        }
      } catch (e) { }
      // 当没有有效选区时，执行清除页面上所有高亮的操作
      document.querySelectorAll('.w3read-highlight').forEach(el => {
        const parent = el.parentNode;
        if (!parent) return;
        while (el.firstChild) parent.insertBefore(el.firstChild, el);
        parent.removeChild(el);
        parent.normalize?.();
      });
      page.highlights = [];
      dataCache.pages[PAGE_KEY] = page;
      await saveData();
    } catch (e) { }
  }

  // 不依赖选区，直接清除页面上所有高亮并更新存储
  async function clearAllHighlightsForPage() {
    try {
      const page = dataCache.pages[PAGE_KEY] || {};
      document.querySelectorAll('.w3read-highlight').forEach(el => {
        const parent = el.parentNode;
        if (!parent) return;
        while (el.firstChild) parent.insertBefore(el.firstChild, el);
        parent.removeChild(el);
        parent.normalize?.();
      });
      page.highlights = [];
      dataCache.pages[PAGE_KEY] = page;
      await saveData();
    } catch (e) { }
  }

  async function createAnnotationAtSelection(text) {
    try {
      let range = null;
      if (lastSelectionRange) {
        try { range = lastSelectionRange.cloneRange(); } catch(e){ range = null; }
      }
      if (!range) {
        const sel = document.getSelection();
        if (sel && sel.rangeCount) range = sel.getRangeAt(0).cloneRange();
      }
      if (!range || range.collapsed) {
        alert('请先选择要添加批注的文本 (某些 iframe/编辑器的选择不受插件控制)。');
        return;
      }
      const rect = range.getBoundingClientRect();
      const tryRight = window.scrollX + rect.right + 8;
      const tryLeft = window.scrollX + Math.max(rect.left - 26, 8);
      const viewportRight = window.scrollX + document.documentElement.clientWidth;
      let x = (tryRight + 18 < viewportRight) ? tryRight : tryLeft;
      let y = window.scrollY + Math.max(rect.top - 6, 8);
      const note = await new Promise(resolve => {
        const id = 'w3r-note-editor';
        const existing = document.getElementById(id);
        if (existing) { existing.remove(); }
        const editor = document.createElement('div');
        editor.id = id;
        editor.style.position = 'absolute';
        editor.style.left = x + 'px';
        editor.style.top = y + 'px';
        editor.style.zIndex = 2147483647;
        editor.style.background = '#fff';
        editor.style.padding = '8px';
        editor.style.borderRadius = '8px';
        editor.style.boxShadow = '0 6px 18px rgba(0,0,0,0.15)';
        editor.innerHTML = `<div style="margin-bottom:6px;font-weight:600">添加批注</div>`;
        const ta = document.createElement('textarea');
        ta.style.width = '280px';
        ta.style.height = '120px';
        ta.value = text || '';
        const row = document.createElement('div');
        row.style.marginTop = '8px';
        const btnOk = document.createElement('button'); btnOk.textContent = '保存';
        const btnCancel = document.createElement('button'); btnCancel.textContent = '取消';
        btnOk.style.marginRight = '8px';
        row.appendChild(btnOk); row.appendChild(btnCancel);
        editor.appendChild(ta); editor.appendChild(row);
        document.body.appendChild(editor);
        btnOk.addEventListener('click', () => { const v = ta.value; editor.remove(); resolve(v); });
        btnCancel.addEventListener('click', () => { editor.remove(); resolve(null); });
      });
      if (note === null) return;
      const id = generateId();
      const page = dataCache.pages[PAGE_KEY] || {};
      page.annotations = page.annotations || [];
      page.annotations.push({ id, x, y, text: note, createdAt: Date.now() });
      dataCache.pages[PAGE_KEY] = page;
      await saveData();
      addMarkerToDOM({ id, x, y, text: note });
    } catch (e) {
      alert('添加批注失败');
    }
  }

  // 选择监听与工具提示触发
  document.addEventListener('selectionchange', () => {
    scheduleSnapshot();
  }, true);

  document.addEventListener('mouseup', (e) => {
    setTimeout(() => {
      const selText = (() => { try { return document.getSelection()?.toString()||'' } catch(e){ return '' } })();
      if (selText && selText.trim().length>0) {
        scheduleSnapshot();
        try {
          const rect = document.getSelection().getRangeAt(0).getBoundingClientRect();
          const x = Math.min(window.innerWidth - 320, rect.left + window.scrollX);
          const y = Math.min(window.innerHeight - 80, rect.top + window.scrollY - 40);
          buildTooltip(x, y, selText);
        } catch (err) {
          buildTooltip(window.innerWidth/2 - 100, window.innerHeight/2 - 20, selText);
        }
      } else {
        const ctx = detectSelectionContext();
        removeSelectionTooltip();
      }
    }, 10);
  }, true);

  // URL 变化处理
  function onUrlChange() {
    const newKey = location.origin + location.pathname + (location.search||'');
    if (newKey === PAGE_KEY) return;
    PAGE_KEY = newKey;
    removeSelectionTooltip();
    loadData().then(() => { applyHighlightsForPage(); applyAnnotationsForPage(); });
  }
  (function() {
    const _push = history.pushState;
    history.pushState = function() { _push.apply(this, arguments); window.dispatchEvent(new Event('popstate')); };
    const _replace = history.replaceState;
    history.replaceState = function() { _replace.apply(this, arguments); window.dispatchEvent(new Event('popstate')); };
    window.addEventListener('popstate', onUrlChange);
    window.addEventListener('hashchange', onUrlChange);
  })();

  // 夜间模式、去广告、书签处理
  async function setNightMode(enable) {
    removeNightMode();
    if (enable) {
      try {
        const s = document.createElement('style');
        s.id = 'w3read-night-style';
        s.innerText = `html { filter: invert(0.92) hue-rotate(180deg) !important; background:#111 !important; } img, video, iframe, svg { filter: invert(1) hue-rotate(180deg) !important; }`;
        document.documentElement.appendChild(s);
      } catch (e) { }
    }
    const page = dataCache.pages[PAGE_KEY] || {};
    page.night = !!enable;
    dataCache.pages[PAGE_KEY] = page;
    await saveData();
  }
  function removeNightMode() { const s = document.getElementById('w3read-night-style'); if (s) s.remove(); }

  function removeAds() {
    const selectors = [
      '[id*="ad"]',
      '[class*="ad-"]',
      '[class*="ads"]',
      '[class*="advert"]',
      'iframe[src*="ads"]',
      'iframe[data-ad]',
      'ins[data-ad]',
      'div[id^="google_ads"]',
      'div[class*="google-ad"]'
    ];
    let removed = 0;
    selectors.forEach(sel => {
      document.querySelectorAll(sel).forEach(el => {
        try {
          if (el.closest('[id*="w3read"], [class*="w3read"], [data-w3r-id], [data-w3r-annotation-id]')) {
            return;
          }
          el.remove();
          removed++;
        } catch (e) { }
      });
    });
    try { injectStyles(); } catch (e) { }
    return removed;
  }

  // 书签处理
  async function addBookmark() {
    const b = { id: generateId(), title: document.title, url: location.href, time: Date.now() };
    dataCache.bookmarks = dataCache.bookmarks || [];
    dataCache.bookmarks.push(b);
    await saveData();
    alert('已收藏到 w3read：' + b.title);
  }

  function handleMessage(msg, sender, sendResp) {
    (async () => {
      if (!msg || !msg.cmd) return;
      switch(msg.cmd) {
        case 'clearAllHighlights': await clearAllHighlightsForPage(); sendResp && sendResp({ ok:true }); break;
        case 'removeAds': {
          const removed = removeAds();
          sendResp({ ok:true, removed });
        } break;
        case 'addBookmark': await addBookmark(); sendResp({ ok:true }); break;
        case 'toggleNight': await setNightMode(!!msg.enable); sendResp({ ok:true }); break;
        case 'getPageData': sendResp({ page: dataCache.pages[PAGE_KEY] || {}, bookmarks: dataCache.bookmarks || [] }); break;
        case 'clearHighlights': await clearHighlightsForPage(); sendResp({ ok:true }); break;
        default: break;
      }
    })();
  }

  // 初始化
  (async function init() {
    try {
      injectStyles();
      await loadData();
      applyHighlightsForPage();
      applyAnnotationsForPage();
      const page = dataCache.pages[PAGE_KEY] || {};
      if (page.night) setNightMode(true);
      chrome.runtime.onMessage.addListener(handleMessage);
    } catch (e) {
    }
  })();

})();
