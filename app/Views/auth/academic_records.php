<?= $this->extend('layouts/auth_layout') ?>

<?= $this->section('content') ?>
<!-- Folder card compact styles -->
<style>
  .folder-card-compact {
    width: 220px;
    min-width: 180px;
    flex-shrink: 0;
  }
  .folder-row {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 16px;
  }
  .folder-row.list-mode .folder-card-compact {
    width: 100%;
    min-width: unset;
    flex-shrink: 1;
  }

  /* ── Hierarchy Tree Panel ──────────────────────────── */
  .ht-selected {
    background: #f0fdf4 !important;
  }
  .ht-row {
    display: flex;
    align-items: center;
    gap: 0;
    cursor: pointer;
    position: relative;
    transition: background .12s;
  }
  .ht-row.ht-selected .ht-inner {
    background: #dcfce7;
    border-right: 3px solid #16a34a;
    border-radius: 0 8px 8px 0;
  }
  .ht-row.ht-selected .ht-label { color: #166534 !important; font-weight: 700 !important; }
  .ht-row.ht-selected .ht-toggle { color: #16a34a; }

  /* Ancestor path highlight — dimmer than selected, no border */
  .ht-row.ht-ancestor .ht-inner {
    background: #f0fdf4;
  }
  .ht-row.ht-ancestor .ht-label {
    color: #15803d;
    font-weight: 600;
  }
  /* Ancestor path highlight — dimmer than selected, no border */
  .ht-row.ht-ancestor .ht-inner {
    background: #f0fdf4;
  }
  .ht-row.ht-ancestor .ht-label {
    color: #15803d;
    font-weight: 600;
  }
  .ht-inner {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 7px;
    padding: 7px 12px 7px 4px;
    border-radius: 0 8px 8px 0;
    transition: background .12s;
    min-width: 0;
  }

  .ht-toggle {
    width: 18px; height: 18px;
    flex-shrink: 0;
    background: none; border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    border-radius: 4px;
    color: #94a3b8;
    transition: color .12s, transform .15s;
  }
  .ht-toggle:hover { color: #16a34a; background: #dcfce7; }
  .ht-toggle.open { transform: rotate(90deg); }
  .ht-toggle.leaf { cursor: default; pointer-events: none; color: transparent; }

  .ht-label {
    font-size: 13px;
    color: #374151;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    flex: 1;
    line-height: 1.4;
  }

  .ht-badge {
    font-size: 11px;
    padding: 2px 6px;
    border-radius: 999px;
    flex-shrink: 0;
    font-weight: 600;
    line-height: 1.4;
  }

  .ht-children {
    overflow: hidden;
    transition: max-height .22s cubic-bezier(.4,0,.2,1), opacity .18s;
  }
  .ht-children.closed { max-height: 0 !important; opacity: 0; }
  .ht-children.open   { opacity: 1; }

  .ht-skeleton-bar {
    height: 8px; border-radius: 4px; background: #f1f5f9;
    animation: htPulse 1.2s infinite ease-in-out;
  }
  @keyframes htPulse {
    0%,100% { opacity: .5; } 50% { opacity: 1; }
  }

  .ht-d1 { --ht-c: #7c3aed; --ht-bg: #f5f3ff; }
  .ht-d2 { --ht-c: #0369a1; --ht-bg: #e0f2fe; }
  .ht-d3 { --ht-c: #b45309; --ht-bg: #fef3c7; }
  .ht-d4 { --ht-c: #be185d; --ht-bg: #fce7f3; }
  .ht-d5 { --ht-c: #15803d; --ht-bg: #dcfce7; }

  .ht-badge { background: var(--ht-bg, #f3f4f6); color: var(--ht-c, #6b7280); }

  .ht-legend-pill {
    font-size: 9.5px;
    padding: 2px 6px;
    border-radius: 999px;
    font-weight: 600;
    background: rgba(255,255,255,0.15);
    color: rgba(255,255,255,0.9);
  }

  #hierarchyTreeScroll::-webkit-scrollbar { width: 3px; }
  #hierarchyTreeScroll::-webkit-scrollbar-track { background: transparent; }
  #hierarchyTreeScroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 4px; }
/* Global drag-drop overlay */
  #globalDropOverlay {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 9000;
    background: rgba(22, 163, 74, 0.15);
    border: 4px dashed #16a34a;
    pointer-events: none;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    gap: 12px;
    transition: opacity .15s;
  }
  #globalDropOverlay.active {
    display: flex;
    pointer-events: all;
  }
  /* Folder card drop target highlight */
  .folder-card.drag-over {
    border-color: #16a34a !important;
    background: #f0fdf4 !important;
    box-shadow: 0 0 0 3px rgba(22,163,74,0.25);
  }
  /* File card being dragged */
  .record-card.dragging {
    opacity: 0.4;
    transform: scale(0.97);
  }

  /* Tree node drop-target highlight */
  .ht-row.ht-drop-target {
    background: rgba(22, 163, 74, 0.12) !important;
    outline: 2px solid #16a34a;
    border-radius: 6px;
    transition: background .1s, outline .1s;
  }

  /* ── Upload Tray rows ─────────────────────────────────────────── */
  .tray-row {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 16px;
    border-bottom: 1px solid rgba(22,101,52,.1); /* light green divider */
    transition: background .12s;
  }
  .tray-row:last-child { border-bottom: none; }
  .tray-row:hover { background: rgba(22,163,74,.06); } /* subtle green hover */

  .tray-row-icon {
    width: 28px; height: 28px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; font-size: 14px;
  }
  /* Icon state colors — all green/white tones */
  .tray-row-icon.queued    { background: #f0fdf4; color: #86efac; }
  .tray-row-icon.uploading { background: #dcfce7; color: #16a34a; }
  .tray-row-icon.reviewing { background: #bbf7d0; color: #15803d; }
  .tray-row-icon.done      { background: #16a34a; color: #ffffff; }
  .tray-row-icon.error     { background: #fee2e2; color: #ef4444; }

  .tray-row-body {
    flex: 1; min-width: 0;
  }
  .tray-row-name {
    font-size: 12px; font-weight: 600; color: #166534; /* dark green text */
    overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
  }
  .tray-row-status {
    font-size: 10.5px; color: #86efac; margin-top: 1px; /* light green status */
  }
  .tray-row-bar-wrap {
    height: 3px; background: #dcfce7; border-radius: 2px; margin-top: 4px; overflow: hidden;
  }
  .tray-row-bar {
    height: 100%; border-radius: 2px;
    background: linear-gradient(90deg,#16a34a,#4ade80); /* green progress bar */
    transition: width .25s ease;
  }
  .tray-row-bar.done  { background: #16a34a; }
  .tray-row-bar.error { background: #ef4444; }

  /* Minimised state — hide the list but keep header visible */
  #uploadProgressTray.minimised #trayFileList,
  #uploadProgressTray.minimised #trayOverallWrap {
    display: none;
  }

  /* Scrollbar inside tray */
  #trayFileList::-webkit-scrollbar { width: 3px; }
  #trayFileList::-webkit-scrollbar-track { background: transparent; }
  #trayFileList::-webkit-scrollbar-thumb { background: #bbf7d0; border-radius: 2px; }

</style>


<!-- Page Title -->
<div class="flex items-center justify-between mb-6">
  <div>
    <h2 class="text-3xl font-bold text-gray-800 mb-2">Academic Records</h2>
    <p class="text-gray-600">Manage student records and folders</p>
  </div>
</div>

<!-- ── Main layout: tree panel + content ───────────────────────────────────── -->
<div class="flex gap-0 min-h-0" id="academicRecordsLayout">

  <!-- ── Hierarchy Tree Panel ─────────────────────────────────────────────── -->
  <div id="hierarchyPanel"
     style="width:320px; min-width:320px; display:flex; flex-direction:column; flex-shrink:0; margin-right:20px; position:sticky; top:16px; align-self:flex-start; max-height:calc(100vh - 32px);">
    <div class="bg-white rounded-2xl overflow-hidden flex flex-col"
         style="border:1px solid #e2e8f0; box-shadow:0 2px 12px rgba(0,0,0,0.06); min-height:520px;">

      <!-- Header -->
      <div style="background:linear-gradient(135deg,#166534 0%,#15803d 100%); padding:14px 16px 12px;">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-2">
            <div style="width:28px;height:28px;background:rgba(255,255,255,0.15);border-radius:8px;display:flex;align-items:center;justify-content:center;">
              <svg width="15" height="15" fill="none" stroke="white" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
              </svg>
            </div>
            <div>
              <p style="color:white;font-size:12px;font-weight:700;letter-spacing:0.5px;line-height:1.2;">STORAGE TREE</p>
              <p style="color:rgba(255,255,255,0.65);font-size:10px;line-height:1.2;" id="hierarchyNodeCount">Loading…</p>
            </div>
          </div>
          <button onclick="toggleHierarchyPanel()" title="Collapse"
                  style="width:26px;height:26px;background:rgba(255,255,255,0.12);border:none;border-radius:6px;cursor:pointer;display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,0.8);"
                  onmouseover="this.style.background='rgba(255,255,255,0.22)'"
                  onmouseout="this.style.background='rgba(255,255,255,0.12)'">
            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
            </svg>
          </button>
        </div>
        <div class="flex flex-wrap gap-1 mt-2.5" id="hierarchyLevelLegend"></div>
      </div>

      <!-- Search -->
      <div style="padding:10px 12px;border-bottom:1px solid #f1f5f9;background:#fafafa;">
        <div style="position:relative;">
          <svg style="position:absolute;left:8px;top:50%;transform:translateY(-50%);width:13px;height:13px;color:#94a3b8;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
          <input id="treeSearchInput" type="text" placeholder="Search locations…"
                 oninput="filterTreeNodes(this.value)"
                 style="width:100%;padding:6px 28px 6px 26px;font-size:11.5px;border:1px solid #e2e8f0;border-radius:8px;background:white;outline:none;box-sizing:border-box;color:#374151;"
                 onfocus="this.style.borderColor='#16a34a';this.style.boxShadow='0 0 0 2px rgba(22,163,74,0.12)'"
                 onblur="this.style.borderColor='#e2e8f0';this.style.boxShadow='none'"/>
          <button id="treeSearchClear" onclick="clearTreeSearch()"
                  style="display:none;position:absolute;right:7px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#94a3b8;padding:0;">
            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
      </div>

      <!-- Tree scroll area -->
      <div id="hierarchyTreeScroll" style="flex:1;overflow-y:auto;overflow-x:hidden;max-height:calc(100vh - 200px);">
        <div id="hierarchyRoot" data-path=""
             onclick="hierarchySelectFolder('','Academic Records')"
             style="display:flex;align-items:center;gap:8px;padding:10px 14px;cursor:pointer;border-bottom:1px solid #f1f5f9;transition:background .15s;"
             onmouseover="if(!this.classList.contains('ht-selected'))this.style.background='#f0fdf4'"
             onmouseout="if(!this.classList.contains('ht-selected'))this.style.background=''">
          <div style="width:24px;height:24px;background:#dcfce7;border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="13" height="13" fill="#166634" viewBox="0 0 24 24">
              <path d="M12 3L2 9v12h7v-7h6v7h7V9L12 3z"/>
            </svg>
          </div>
          <span style="font-size:12.5px;font-weight:700;color:#166534;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">Academic Records</span>
          <span id="hierarchyRootBadge" style="font-size:10px;background:#dcfce7;color:#166534;padding:1px 6px;border-radius:999px;flex-shrink:0;font-weight:600;"></span>
        </div>
        <ul id="hierarchyTree" style="list-style:none;margin:0;padding:4px 0;" role="tree"></ul>
        <div id="hierarchyEmptyState" style="display:none;padding:24px 16px;text-align:center;">
          <p style="font-size:11px;color:#94a3b8;">No folders found</p>
        </div>
      </div>

      <!-- Footer -->
      <div style="border-top:1px solid #f1f5f9;padding:8px 10px;display:flex;gap:6px;background:#fafafa;">
        <button onclick="collapseAllTreeNodes()"
                style="flex:1;font-size:10.5px;color:#64748b;background:none;border:1px solid #e2e8f0;border-radius:7px;padding:5px 0;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:4px;"
                onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='none'">
          <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
          </svg> Collapse All
        </button>
        <button onclick="expandAllTreeNodes()"
                style="flex:1;font-size:10.5px;color:#64748b;background:none;border:1px solid #e2e8f0;border-radius:7px;padding:5px 0;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:4px;"
                onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='none'">
          <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
          </svg> Expand All
        </button>
      </div>
    </div>
  </div>

  <!-- ── Collapsed pill ──────────────────────────────────────────────────── -->
  <div id="hierarchyTab" onclick="toggleHierarchyPanel()" title="Show Storage Tree"
       style="display:none;width:34px;min-width:34px;flex-shrink:0;margin-right:16px;cursor:pointer;
              background:white;border:1px solid #e2e8f0;border-radius:12px;
              box-shadow:0 2px 8px rgba(0,0,0,0.06);
              flex-direction:column;align-items:center;justify-content:center;gap:6px;min-height:160px;
              position:sticky; top:16px; align-self:flex-start;"
       onmouseover="this.style.boxShadow='0 4px 16px rgba(22,163,74,0.18)'"
       onmouseout="this.style.boxShadow='0 2px 8px rgba(0,0,0,0.06)'">
    <svg width="14" height="14" fill="none" stroke="#16a34a" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
    <span style="font-size:10px;color:#16a34a;font-weight:700;writing-mode:vertical-rl;transform:rotate(180deg);letter-spacing:1px;">TREE</span>
  </div>

  <!-- ── Right side: search + master list ────────────────────────────────── -->
  <div class="flex-1 min-w-0">

<!-- Search and View Controls -->
<div class="flex items-center gap-4 mb-6">
  <div class="flex-1 relative">
    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
    </svg>
    <input
      type="text"
      id="searchInput"
      placeholder="Search by student name or student ID..."
      class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
      oninput="filterRecords()"
    />
  </div>

  <div class="flex gap-2">
    <button
      id="gridViewBtn"
      onclick="setView('grid')"
      class="p-3 rounded-lg bg-green-700 text-white"
    >
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
      </svg>
    </button>
    <button
      id="listViewBtn"
      onclick="setView('list')"
      class="p-3 rounded-lg bg-white border border-gray-300"
    >
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
      </svg>
    </button>
  </div>
</div>

<!-- Breadcrumb Navigation -->
<div class="mb-6">
  <nav id="breadcrumb" class="flex items-center flex-wrap gap-1 text-sm text-gray-600">
  </nav>
</div>

<!-- Dynamic content -->
<div id="masterList">
  <div class="flex items-center justify-center py-20 text-gray-400">
    <svg class="w-8 h-8 animate-spin mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
    </svg>
    Loading records…
  </div>
</div>

  </div>

  <!-- Global drag-drop overlay (shown when dragging files from desktop) -->
  <div id="globalDropOverlay">
    <svg class="w-16 h-16 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
    </svg>
    <p class="text-green-800 font-bold text-xl">Drop files to upload here</p>
    <p class="text-green-700 text-sm">Files will be uploaded to the current folder</p>
  </div>


</div>

<!-- ── Preview Modal ── -->
<div id="previewModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col">
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
      <h3 id="previewTitle" class="text-lg font-semibold text-gray-800">File Preview</h3>
      <div class="flex items-center gap-2">
        <button onclick="printPreview()" class="p-2 rounded-lg hover:bg-gray-100 transition-colors" title="Print">
          <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
          </svg>
        </button>
        <button onclick="downloadPreviewFile()" class="p-2 rounded-lg hover:bg-gray-100 transition-colors" title="Download">
          <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
          </svg>
        </button>
        <button onclick="closePreviewModal()" class="p-2 rounded-lg hover:bg-gray-100 transition-colors" title="Close">
          <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>
    </div>
    <div class="flex-1 overflow-auto p-6">
      <div id="previewContent" class="w-full h-full flex items-center justify-center">
        <div class="text-gray-400">
          <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
          </svg>
          <p class="text-center">Loading preview...</p>
        </div>
      </div>
    </div>
    <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200">
      <button onclick="closePreviewModal()" class="px-6 py-2.5 rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors font-medium text-gray-700">
        Cancel
      </button>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('fab') ?>

<div class="fixed bottom-8 right-8 z-50">
  <div
    id="fabChooserPopup"
    class="absolute bottom-16 right-0 mb-2 w-64 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden
           opacity-0 scale-95 pointer-events-none
           transition-all duration-200 ease-out origin-bottom-right"
  >
    <div class="px-4 py-3 border-b border-gray-100">
      <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Create / Upload</p>
    </div>
    <div class="p-2 space-y-1">
      <button
        onclick="closeFabChooserPopup(); openNewFolderModal();"
        class="w-full flex items-center gap-3 px-3 py-3 rounded-xl hover:bg-green-50 border border-transparent hover:border-green-200 transition-all text-left group"
      >
        <span class="w-9 h-9 rounded-lg bg-green-100 flex items-center justify-center flex-shrink-0 group-hover:bg-green-200 transition-colors">
          <svg class="w-5 h-5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
          </svg>
        </span>
        <div>
          <p class="font-semibold text-gray-800 text-sm">New Folder</p>
          <p class="text-xs text-gray-400">Create an empty folder</p>
        </div>
      </button>
      <button
        onclick="closeFabChooserPopup(); openUploadFolderModal();"
        class="w-full flex items-center gap-3 px-3 py-3 rounded-xl hover:bg-yellow-50 border border-transparent hover:border-yellow-200 transition-all text-left group"
      >
        <span class="w-9 h-9 rounded-lg bg-yellow-100 flex items-center justify-center flex-shrink-0 group-hover:bg-yellow-200 transition-colors">
          <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11v5m-2.5-2.5L12 11l2.5 2.5"></path>
          </svg>
        </span>
        <div>
          <p class="font-semibold text-gray-800 text-sm">Upload Folder</p>
          <p class="text-xs text-gray-400">Upload multiple records</p>
        </div>
      </button>
      <button
        onclick="closeFabChooserPopup(); openUploadModal();"
        class="w-full flex items-center gap-3 px-3 py-3 rounded-xl hover:bg-blue-50 border border-transparent hover:border-blue-200 transition-all text-left group"
      >
        <span class="w-9 h-9 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0 group-hover:bg-blue-200 transition-colors">
          <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
          </svg>
        </span>
        <div>
          <p class="font-semibold text-gray-800 text-sm">Upload Record</p>
          <p class="text-xs text-gray-400">Single academic record</p>
        </div>
      </button>
    </div>
    <div class="pb-1"></div>
  </div>

  <button
    id="fabButton"
    onclick="toggleFabChooserPopup()"
    class="w-14 h-14 bg-green-700 hover:bg-green-800 active:scale-95 text-white rounded-full shadow-lg flex items-center justify-center transition-all duration-150"
  >
    <svg id="fabIcon" class="w-7 h-7 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
    </svg>
  </button>
</div>

<!-- ── Google Drive–style Upload Progress Tray ─────────────────────────────── -->
<div id="uploadProgressTray"
     style="display:none;position:fixed;bottom:24px;right:24px;z-index:10000;
            width:340px;background:#ffffff;border-radius:16px;
            box-shadow:0 12px 40px rgba(22,101,52,0.18);overflow:hidden;
            font-family:inherit;transition:transform .25s,opacity .25s;
            border:1px solid #bbf7d0;">

  <!-- Tray header — dark green background, white text -->
  <div style="display:flex;align-items:center;justify-content:space-between;
              padding:12px 16px;background:#166534;border-bottom:1px solid #15803d;">
    <div style="display:flex;align-items:center;gap:10px;">
      <!-- Animated upload icon -->
      <svg id="trayHeaderIcon" style="width:18px;height:18px;flex-shrink:0;color:#ffffff;
                                       animation:trayIconSpin 1.4s linear infinite;"
           fill="none" viewBox="0 0 24 24">
        <style>
          @keyframes trayIconSpin { to { transform:rotate(360deg); } }
        </style>
        <circle cx="12" cy="12" r="10" stroke="rgba(255,255,255,.35)" stroke-width="2.5"/>
        <path fill="rgba(255,255,255,.95)"
              d="M4 12a8 8 0 018-8v3.5a4.5 4.5 0 00-4.5 4.5H4z"/>
      </svg>
      <span id="trayHeaderLabel"
            style="color:#ffffff;font-size:13px;font-weight:700;letter-spacing:.3px;">
        Uploading…
      </span>
    </div>
    <div style="display:flex;gap:6px;">
      <button id="trayMinimiseBtn"
              onclick="toggleTrayMinimise()"
              title="Minimise"
              style="background:none;border:none;cursor:pointer;color:rgba(255,255,255,.75);
                     display:flex;align-items:center;justify-content:center;
                     width:24px;height:24px;border-radius:6px;transition:background .12s;"
              onmouseover="this.style.background='rgba(255,255,255,.15)'"
              onmouseout="this.style.background='none'">
        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 12h14"/>
        </svg>
      </button>
      <button onclick="cancelAllUploads()"
              title="Cancel uploads"
              style="background:none;border:none;cursor:pointer;color:rgba(255,255,255,.75);
                     display:flex;align-items:center;justify-content:center;
                     width:24px;height:24px;border-radius:6px;transition:background .12s;"
              onmouseover="this.style.background='rgba(255,255,255,.15)'"
              onmouseout="this.style.background='none'">
        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>
  </div>

  <!-- File rows list (scrollable) — white background -->
  <div id="trayFileList"
       style="max-height:260px;overflow-y:auto;padding:6px 0;background:#ffffff;">
    <!-- Rows injected by JS -->
  </div>

  <!-- Overall progress bar — white background, green bar -->
  <div id="trayOverallWrap"
       style="padding:8px 16px 12px;border-top:1px solid #dcfce7;background:#f0fdf4;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:5px;">
      <span style="color:#15803d;font-size:11px;">Overall progress</span>
      <span id="trayOverallPct" style="color:#15803d;font-size:11px;">0%</span>
    </div>
    <div style="height:4px;background:#dcfce7;border-radius:2px;overflow:hidden;">
      <div id="trayOverallBar"
           style="height:100%;width:0%;background:linear-gradient(90deg,#16a34a,#4ade80);
                  border-radius:2px;transition:width .3s ease;"></div>
    </div>
  </div>
</div>
<!-- ── End Upload Progress Tray ─────────────────────────────────────────────── -->

<?= $this->endSection() ?>

<?= $this->section('modals') ?>


<!-- PERMISSION DENIED MODAL -->
<div id="permissionDeniedModal"
     class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999]">
  <div class="bg-white rounded-2xl p-8 w-full max-w-sm shadow-2xl text-center">
    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
      <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
      </svg>
    </div>
    <h3 class="text-xl font-bold text-gray-800 mb-2">Access Denied</h3>
    <p id="permissionDeniedMsg" class="text-gray-500 mb-6">You don't have permission to perform this action.</p>
    <button onclick="closePermissionDeniedModal()"
            class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition-colors w-full">
      OK
    </button>
  </div>
</div>

<!-- NEW FOLDER MODAL -->
<?php if ($can_add_folder ?? $priv_folders_add ?? false): ?>
<div id="newFolderModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
  <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-2xl">
    <div class="flex items-center gap-3 mb-6">
      <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
        <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
        </svg>
      </div>
      <h3 class="text-xl font-bold text-gray-800">New Folder</h3>
      <button onclick="closeNewFolderModal()" class="ml-auto text-gray-400 hover:text-gray-600">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
    </div>
    <div class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Folder Name <span class="text-red-500">*</span></label>
        <input type="text" id="newFolderName" placeholder="Enter folder name"
          class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500"/>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Subname <span class="text-gray-400 font-normal">(optional)</span></label>
        <input type="text" id="newFolderSubname" placeholder="Additional description"
          class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500"/>
      </div>
    </div>
    <div class="flex gap-3 mt-6">
      <button onclick="closeNewFolderModal()" class="flex-1 px-4 py-2.5 border border-gray-300 rounded-xl hover:bg-gray-50 font-medium text-gray-700">Cancel</button>
      <button onclick="createFolder()" class="flex-1 px-4 py-2.5 bg-green-700 text-white rounded-xl hover:bg-green-800 font-medium">Create</button>
    </div>
  </div>
</div>
<?php else: ?>
<div id="newFolderModal" class="hidden"></div>
<?php endif; ?>

<!-- UPLOAD RECORD MODAL -->
<?php if ($can_upload ?? $priv_records_upload ?? false): ?>
<div id="uploadModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-2xl p-6 w-full max-w-lg shadow-2xl">
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center">
          <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
          </svg>
        </div>
        <div>
          <h3 class="text-xl font-bold text-gray-800">Upload Record</h3>
          <p class="text-sm text-gray-500">Add a new academic record to the system</p>
        </div>
      </div>
      <button onclick="closeUploadModal()" class="text-gray-400 hover:text-gray-600 transition-colors ml-2">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
    </div>
    <form id="uploadForm" action="<?= base_url('academic-records/upload'); ?>" method="post" enctype="multipart/form-data">
      <?= csrf_field() ?>
      <input type="hidden" name="folder_path" id="uploadFolderPath" value="">
     
      <div class="mb-4">
        <label id="recordDropZone" for="recordFileInput"
          class="flex flex-col items-center justify-center w-full h-44 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-green-600 hover:bg-green-50 transition-colors"
          ondragenter="event.stopPropagation(); event.preventDefault();"
          ondragover="event.stopPropagation(); event.preventDefault(); this.classList.add('border-green-600','bg-green-50')"
          ondragleave="event.stopPropagation(); this.classList.remove('border-green-600','bg-green-50')"
          ondrop="event.stopPropagation(); handleRecordDrop(event)">
          <svg class="w-10 h-10 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
          </svg>
          <p class="text-sm font-medium text-gray-500">Drag &amp; drop file here</p>
          <p class="text-xs text-gray-400 mt-1">PDF, DOC, DOCX, JPG, PNG — max 10MB</p>
          <input id="recordFileInput" type="file" name="record_file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="hidden" onchange="updateRecordFileLabel(this)" />
        </label>
        <p id="recordFileName" class="text-xs text-green-700 font-medium mt-2 hidden"></p>
      </div>
      <div class="mb-6">
        <button type="button" onclick="document.getElementById('recordFileInput').click()"
          class="w-full flex items-center justify-center gap-2 px-4 py-2.5 border border-gray-300 rounded-xl hover:bg-gray-50 font-medium text-gray-700 transition-colors">
          <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/>
          </svg>
          Choose File from Browser
        </button>
      </div>
      <div class="flex gap-3">
        <button type="button" onclick="closeUploadModal()"
          class="flex-1 px-4 py-2.5 border border-gray-300 rounded-xl hover:bg-gray-50 font-medium text-gray-700 transition-colors">Cancel</button>
        <button type="submit"
          class="flex-1 bg-green-700 text-white px-6 py-2.5 rounded-xl hover:bg-green-800 font-semibold transition-colors">Upload Record</button>
      </div>
    </form>
  </div>
</div>
<?php else: ?>
<div id="uploadModal" class="hidden"></div>
<?php endif; ?>

<!-- UPLOAD FOLDER MODAL -->
<?php if ($can_upload ?? $priv_records_upload ?? false): ?>
<div id="uploadFolderModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-2xl p-6 w-full max-w-lg shadow-2xl">
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-yellow-50 rounded-xl flex items-center justify-center">
          <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11v5m-2.5-2.5L12 11l2.5 2.5"></path>
          </svg>
        </div>
        <div>
          <h3 class="text-xl font-bold text-gray-800">Upload Folder</h3>
          <p class="text-sm text-gray-500">Upload a folder containing multiple records</p>
        </div>
      </div>
      <button onclick="closeUploadFolderModal()" class="text-gray-400 hover:text-gray-600 transition-colors ml-2">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
    </div>
    <form id="uploadFolderForm" action="<?= base_url('academic-records/upload-folder'); ?>" method="post" enctype="multipart/form-data">
      <?= csrf_field() ?>
      <input type="hidden" name="folder_path" id="uploadFolderFolderPath" value="">
     
      <div class="mb-4">
        <label id="folderDropZone" for="folderFilesInput"
          class="flex flex-col items-center justify-center w-full h-44 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-green-600 hover:bg-green-50 transition-colors"
          ondragenter="event.stopPropagation(); event.preventDefault();"
          ondragover="event.stopPropagation(); event.preventDefault(); this.classList.add('border-green-600','bg-green-50')"
          ondragleave="event.stopPropagation(); this.classList.remove('border-green-600','bg-green-50')"
          ondrop="event.stopPropagation(); handleFolderDrop(event)">
          <svg class="w-10 h-10 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 11v5m-2.5-2.5L12 11l2.5 2.5"/>
          </svg>
          <p class="text-sm font-medium text-gray-500">Drag &amp; drop folder here</p>
          <p class="text-xs text-gray-400 mt-1">PDF, DOC, DOCX — max 10MB each</p>
          <input id="folderFilesInput" type="file" name="folder_files[]" accept=".pdf,.doc,.docx" multiple class="hidden" onchange="updateFolderFileList(this)" />
        </label>
        <ul id="folderFileList" class="mt-2 space-y-1 hidden"></ul>
      </div>
      <div class="mb-6">
        <button type="button" onclick="document.getElementById('folderFilesInput').click()"
          class="w-full flex items-center justify-center gap-2 px-4 py-2.5 border border-gray-300 rounded-xl hover:bg-gray-50 font-medium text-gray-700 transition-colors">
          <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
            <path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/>
          </svg>
          Choose Folder from Browser
        </button>
      </div>
      <div class="flex gap-3">
        <button type="button" onclick="closeUploadFolderModal()"
          class="flex-1 px-4 py-2.5 border border-gray-300 rounded-xl hover:bg-gray-50 font-medium text-gray-700 transition-colors">Cancel</button>
        <button type="submit"
          class="flex-1 bg-green-700 text-white px-6 py-2.5 rounded-xl hover:bg-green-800 font-semibold transition-colors">Upload Folder</button>
      </div>
    </form>
  </div>
</div>
<?php else: ?>
<div id="uploadFolderModal" class="hidden"></div>
<?php endif; ?>

<!-- RENAME MODAL -->
<?php if ($can_organize ?? $priv_records_organize ?? false): ?>
<div id="renameModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-2xl p-6 w-full max-w-lg shadow-2xl">
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center">
          <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5
                     m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
          </svg>
        </div>
        <div>
          <h3 class="text-xl font-bold text-gray-800">Rename</h3>
          <p class="text-sm text-gray-500">Update record type and filename</p>
        </div>
      </div>
      <button onclick="closeRenameModal()" class="text-gray-400 hover:text-gray-600 transition-colors ml-2">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
    </div>

    <!-- Document Type (Record Type) selector — reuses editFilenameModal data -->
    <div class="mb-4">
      <label class="block text-xs font-semibold text-gray-600 mb-1">Document Type Label</label>
      <?php
        $recordTypeModel = new \App\Models\RecordTypeModel();
        $renameDocTypes  = $recordTypeModel->getAllActive();
      ?>
      <select id="renameDocType"
              class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm text-gray-700 mb-1 focus:outline-none focus:ring-2 focus:ring-amber-400"
              onchange="onRenameDocTypeChange()">
        <option value="">-- Select document type (optional) --</option>
        <?php foreach ($renameDocTypes as $keyName => $type): ?>
          <option value="<?= esc($type['suffix']) ?>"><?= esc($type['label']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- Filename input -->
    <div class="mb-6">
      <label class="block text-xs font-semibold text-gray-600 mb-1">
        New Name <span class="text-gray-400 font-normal">(without extension)</span>
      </label>
      <div class="flex items-center border border-gray-300 rounded-xl overflow-hidden focus-within:ring-2 focus-within:ring-amber-400">
        <input id="renameInput" type="text"
               class="flex-1 px-3 py-2.5 text-sm text-gray-800 outline-none"
               placeholder="e.g. 2023-001_Juan_dela_Cruz_Transcript_Record" />
        <span id="renameExtBadge" class="px-3 py-2.5 text-sm text-gray-400 bg-gray-50 border-l border-gray-300"></span>
      </div>
    </div>

    <div class="flex justify-end gap-3">
      <button onclick="closeRenameModal()"
              class="px-5 py-2.5 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 font-medium text-gray-700 transition-colors text-sm">
        Cancel
      </button>
      <button onclick="submitRename()"
              class="px-5 py-2.5 bg-green-700 text-white rounded-xl hover:bg-green-800 font-medium transition-colors text-sm">
        Rename
      </button>
    </div>
  </div>
</div>
<?php else: ?>
<div id="renameModal" class="hidden"></div>
<?php endif; ?>

<!-- RENAME FOLDER MODAL -->
<?php if ($can_organize ?? $priv_records_organize ?? false): ?>
<div id="renameFolderModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-2xl">
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center">
          <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5
                     m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
          </svg>
        </div>
        <div>
          <h3 class="text-xl font-bold text-gray-800">Rename Folder</h3>
          <p class="text-sm text-gray-500">Enter a new name for this folder</p>
        </div>
      </div>
      <button onclick="closeRenameFolderModal()" class="text-gray-400 hover:text-gray-600 transition-colors ml-2">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
    </div>

    <!-- Folder Name input -->
    <div class="mb-6">
      <label class="block text-xs font-semibold text-gray-600 mb-1">
        New Folder Name <span class="text-red-500">*</span>
      </label>
      <input id="renameFolderInput" type="text"
             class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-amber-400"
             placeholder="e.g. 2024_Student_Records" />
    </div>

    <div class="flex justify-end gap-3">
      <button onclick="closeRenameFolderModal()"
              class="px-5 py-2.5 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 font-medium text-gray-700 transition-colors text-sm">
        Cancel
      </button>
      <button onclick="submitRenameFolder()"
              class="px-5 py-2.5 bg-green-700 text-white rounded-xl hover:bg-green-800 font-medium transition-colors text-sm">
        Save
      </button>
    </div>
  </div>
</div>
<?php else: ?>
<div id="renameFolderModal" class="hidden"></div>
<?php endif; ?>


<!-- MOVE TO FOLDER MODAL -->
<?php if ($can_organize ?? $priv_records_organize ?? false): ?>
<div id="moveModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl flex flex-col max-h-[90vh]">
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
      <div>
        <h3 class="text-xl font-bold text-gray-800">Move to Folder</h3>
        <p class="text-sm text-gray-500 mt-1">Select a destination folder</p>
      </div>
      <button onclick="closeMoveModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
    </div>
    <div class="px-6 pt-4 pb-3">
      <div class="relative">
        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
        <input type="text" id="searchFolders" placeholder="Search folders..."
          class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
          oninput="filterFoldersInMoveModal()"/>
      </div>
    </div>
    <div class="flex-1 overflow-y-auto px-6 pb-3">
      <div class="border border-gray-200 rounded-lg overflow-hidden">
        <div id="folderListMove" class="divide-y divide-gray-100"></div>
      </div>
    </div>
    <div id="movePathPreviewWrap" class="hidden px-6 pb-0 pt-3">
      <div class="flex items-start gap-2 px-3 py-2.5 bg-green-50 border border-green-200 rounded-lg">
        <svg class="w-4 h-4 text-green-700 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/>
        </svg>
        <div class="flex-1 min-w-0">
          <p class="text-xs font-semibold text-green-800 mb-0.5">Selected destination:</p>
          <p id="moveSelectedPath" class="text-xs text-green-700 break-all"></p>
        </div>
      </div>
    </div>
    <div class="flex items-center gap-3 px-6 py-4 border-t border-gray-200">
      <button onclick="closeMoveModal()" class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg hover:bg-gray-50 font-medium text-gray-700 transition-colors">Cancel</button>
      <button onclick="submitMove()" class="flex-1 px-4 py-2.5 bg-green-700 text-white rounded-lg hover:bg-green-800 font-medium transition-colors">Move Here</button>
    </div>
  </div>
</div>
<?php else: ?>
<div id="moveModal" class="hidden"></div>
<?php endif; ?>

<!-- EDIT FILENAME MODAL -->
<div id="editFilenameModal" class="hidden fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm">
  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
    <div class="px-6 pt-6 pb-4 border-b border-gray-100">
      <h3 class="text-lg font-bold text-gray-800">Edit Filename</h3>
      <p class="text-sm text-gray-500 mt-1">Review the suggested filename or type your own. Format: <span id="editFilenameFormatHint" class="font-mono text-teal-700 text-xs">StudentId_StudentName_DocType_Label.pdf</span></p>
    </div>
    <div class="px-6 py-5">
      <label class="block text-xs font-semibold text-gray-600 mb-1">Document Type Label</label>
      <?php
$recordTypeModel  = new \App\Models\RecordTypeModel();
$dynamicDocTypes  = $recordTypeModel->getAllActive();
?>
<select id="editFilenameDocType"
        class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm text-gray-700 mb-4 focus:outline-none focus:ring-2 focus:ring-teal-400"
        onchange="onDocTypeLabelChange()">
  <option value="">-- Select document type --</option>
  <?php foreach ($dynamicDocTypes as $keyName => $type): ?>
    <option value="<?= esc($type['suffix']) ?>"><?= esc($type['label']) ?></option>
  <?php endforeach; ?>
</select>
      <label class="block text-xs font-semibold text-gray-600 mb-1">Full Filename <span class="text-gray-400 font-normal">(without extension)</span></label>
      <div class="flex items-center border border-gray-300 rounded-xl overflow-hidden focus-within:ring-2 focus-within:ring-teal-400">
        <input id="editFilenameInput" type="text"
               class="flex-1 px-3 py-2.5 text-sm text-gray-800 outline-none"
               placeholder="e.g. 2023-001_Juan_dela_Cruz_Transcript_Record" />
        <span id="editFilenameExt" class="px-3 py-2.5 text-sm text-gray-400 bg-gray-50 border-l border-gray-300">.pdf</span>
      </div>
    </div>
    <div class="px-6 pb-6 flex gap-3">
      <button onclick="closeEditFilenameModal()"
              class="flex-1 px-4 py-2.5 border border-gray-300 rounded-xl hover:bg-gray-50 font-medium text-gray-700 transition-colors text-sm">
        Cancel
      </button>
      <button onclick="confirmEditFilename()"
              class="flex-1 px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-xl transition-colors text-sm">
        ✓ Use This Filename
      </button>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
  // ─── CI4 API Base URLs ─────────────────────────────────────────────────────
  const API = {
    listFolder:   '<?= base_url("academic-records/list-folder") ?>',
    listAllFolders:'<?= base_url("academic-records/list-all-folders") ?>',
    createFolder: '<?= base_url("academic-records/create-folder") ?>',
    upload:       '<?= base_url("academic-records/upload") ?>',
    uploadFolder: '<?= base_url("academic-records/upload-folder") ?>',
    download:     '<?= base_url("academic-records/download") ?>',
    preview:      '<?= base_url("academic-records/preview") ?>',
    deleteFile:   '<?= base_url("academic-records/delete-file") ?>',
    deleteFolder: '<?= base_url("academic-records/delete-folder") ?>',
    rename:       '<?= base_url("academic-records/rename") ?>',
    move:         '<?= base_url("academic-records/move") ?>',
  };

  const CSRF_TOKEN = '<?= csrf_hash() ?>';
  const CSRF_NAME  = '<?= csrf_token() ?>';

  // ─── Permission flags ──────────────────────────────────────────────────────
  const PRIV_UPLOAD        = <?= json_encode((bool)($can_upload        ?? $priv_records_upload   ?? false)) ?>;
  const PRIV_VIEW          = <?= json_encode((bool)($can_view          ?? $priv_files_view       ?? false)) ?>;
  const PRIV_ADD_FOLDER    = <?= json_encode((bool)($can_add_folder    ?? $priv_folders_add      ?? false)) ?>;
  const PRIV_DELETE_RECORD = <?= json_encode((bool)($can_delete_record ?? $priv_records_delete   ?? false)) ?>;
  const PRIV_DELETE_FOLDER = <?= json_encode((bool)($can_delete_folder ?? $priv_folders_delete   ?? false)) ?>;
  const PRIV_UPDATE        = <?= json_encode((bool)($can_update        ?? $priv_records_update   ?? false)) ?>;
  const PRIV_ORGANIZE      = <?= json_encode((bool)($can_organize      ?? $priv_records_organize ?? false)) ?>;

  const PERMISSION_DENIED_MSG = 'Sorry, you do not have permission to perform this action.';

  // =========================================================================
  // FIX #7: apiFetch — skip FormData on GET, no unnecessary CSRF appending
  // =========================================================================
  async function apiFetch(url, method = 'GET', body = null) {
    const opts = {
      method,
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
    };

    if (body) {
      // Reuse existing FormData directly; only build a new one when body is a plain object
      const fd = body instanceof FormData ? body : (() => {
        const f = new FormData();
        for (const [k, v] of Object.entries(body)) f.append(k, v);
        return f;
      })();
      // Only POST/PUT/PATCH need CSRF token
      if (method !== 'GET') fd.append(CSRF_NAME, CSRF_TOKEN);
      opts.body = fd;
    }

    const res = await fetch(url, opts);
    if (!res.ok) throw new Error(`HTTP ${res.status}: ${url}`);
    return res.json();
  }

  // ─── Dialog helpers ────────────────────────────────────────────────────────
  function showDialog(message, type = 'warning') {
    document.getElementById('_appDialog')?.remove();
    const icons = {
      warning: `<svg class="w-10 h-10 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>`,
      error:   `<svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
      success: `<svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
    };
    const borders = { warning: 'border-yellow-200', error: 'border-red-200', success: 'border-green-200' };
    const bgs     = { warning: 'bg-yellow-50',      error: 'bg-red-50',      success: 'bg-green-50' };
    const overlay = document.createElement('div');
    overlay.id = '_appDialog';
    overlay.className = 'fixed inset-0 z-[9999] flex items-center justify-center p-4';
    overlay.style.backgroundColor = 'rgba(0,0,0,0.45)';
    overlay.innerHTML = `
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden">
        <div class="flex flex-col items-center px-8 py-7 ${bgs[type]} border-b ${borders[type]}">
          ${icons[type]}
          <p class="mt-4 text-center text-gray-800 font-medium text-base leading-snug">${message}</p>
        </div>
        <div class="px-8 py-4 flex justify-center bg-white">
          <button id="_appDialogOk" class="px-8 py-2.5 bg-green-700 hover:bg-green-800 text-white rounded-xl font-semibold transition-colors text-sm">OK</button>
        </div>
      </div>`;
    document.body.appendChild(overlay);
    const close = () => overlay.remove();
    document.getElementById('_appDialogOk').addEventListener('click', close);
    overlay.addEventListener('click', e => { if (e.target === overlay) close(); });
    document.addEventListener('keydown', function esc(e) {
      if (e.key === 'Escape') { close(); document.removeEventListener('keydown', esc); }
    });
  }

  // duplicate_action: 'replace' | 'keep_both' | null (cancel)
  function showDuplicateConfirm(name, onReplace, onCancel, onKeepBoth) {
    document.getElementById('_dupConfirmDialog')?.remove();
    const overlay = document.createElement('div');
    overlay.id = '_dupConfirmDialog';
    overlay.className = 'fixed inset-0 z-[9999] flex items-center justify-center p-4';
    overlay.style.backgroundColor = 'rgba(0,0,0,0.45)';
    overlay.innerHTML = `
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden">
        <div class="flex flex-col items-center px-8 py-7 bg-yellow-50 border-b border-yellow-200">
          <svg class="w-10 h-10 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
          </svg>
          <p class="mt-4 text-center text-gray-800 font-semibold text-base leading-snug">A file with this name already exists.</p>
          <p class="mt-1 text-center text-gray-500 text-sm break-all">"${name}"</p>
          <p class="mt-2 text-center text-gray-700 text-sm">What would you like to do?</p>
        </div>
        <div class="px-8 py-4 flex flex-col gap-2 bg-white">
          <button id="_dupReplace"  class="w-full px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl font-semibold transition-colors text-sm">Replace Existing File</button>
          <button id="_dupKeepBoth" class="w-full px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold transition-colors text-sm">Keep Both <span class="font-normal opacity-80">(auto-rename new file)</span></button>
          <button id="_dupCancel"   class="w-full px-4 py-2.5 border border-gray-300 rounded-xl hover:bg-gray-50 font-medium text-gray-700 transition-colors text-sm">Cancel Upload</button>
        </div>
      </div>`;
    document.body.appendChild(overlay);
    const close = () => overlay.remove();
    document.getElementById('_dupReplace').addEventListener('click',  () => { close(); onReplace(); });
    document.getElementById('_dupKeepBoth').addEventListener('click', () => { close(); if (onKeepBoth) onKeepBoth(); });
    document.getElementById('_dupCancel').addEventListener('click',   () => { close(); if (onCancel) onCancel(); });
    overlay.addEventListener('click', e => { if (e.target === overlay) { close(); if (onCancel) onCancel(); } });
    document.addEventListener('keydown', function esc(e) {
      if (e.key === 'Escape') { close(); document.removeEventListener('keydown', esc); if (onCancel) onCancel(); }
    });
  }

  // =========================================================================
  // FIX #1: Single Unified Index — replaces three separate crawlers
  // (was: _crawlPath, _crawlFolderBrowserFolders, _crawlMoveFolders)
  // Now one crawl feeds search, move modal, AND folder browser
  // =========================================================================
  let _unifiedIndex        = null;
  let _unifiedIndexPromise = null;
  let _unifiedIndexTs      = 0;
  const INDEX_TTL_MS       = 60_000; // 60 s — reuse within same session

  async function _crawlUnified(path, ancestorLabels, index) {
    try {
      const data = await apiFetch(API.listFolder + '?path=' + encodeURIComponent(path));
      if (!data.success) return;

      const locationLabel = ancestorLabels.length
        ? 'Academic Records › ' + ancestorLabels.join(' › ')
        : 'Academic Records';

      for (const f of (data.folders || [])) {
        index.push({
          kind: 'folder', name: f.name, path: f.path,
          parentPath: path,   // explicit parent — no fragile string parsing
          locationLabel,
          count: f.count, modified: f.modified,
        });
      }
      for (const f of (data.files || [])) {
        index.push({
          kind: 'file', name: f.name, path: f.path,
          parentPath: path, locationLabel,
          ext: f.ext, size: f.size, modified: f.modified,
        });
      }

      // Parallel fan-out — much faster than sequential awaits
      await Promise.all(
        (data.folders || []).map(f =>
          _crawlUnified(f.path, [...ancestorLabels, f.name], index)
        )
      );
    } catch (_) { /* individual path errors are non-fatal */ }
  }

  // Option 5: listAllFolders now returns both folders AND files in one
  // request — no more Promise.all loop firing one request per folder.
  async function buildUnifiedIndex() {
    const now = Date.now();
    if (_unifiedIndex && now - _unifiedIndexTs < INDEX_TTL_MS) return _unifiedIndex;
    if (_unifiedIndexPromise) return _unifiedIndexPromise;

    _unifiedIndexPromise = (async () => {
        try {
            // ONE request — server returns folders AND files together
            const data = await apiFetch(API.listAllFolders);
            _unifiedIndex = data.success
                ? [...(data.folders || []), ...(data.files || [])]
                : [];
        } catch (_) {
            _unifiedIndex = [];
        }
        _unifiedIndexTs      = Date.now();
        _unifiedIndexPromise = null;
        return _unifiedIndex;
    })();

    return _unifiedIndexPromise;
  }


  // Single invalidation point — replaces invalidateSearchIndex,
  // invalidateMoveFolderIndex, and invalidateFolderBrowserIndex
  function invalidateUnifiedIndex() {
    _unifiedIndex        = null;
    _unifiedIndexPromise = null;
    _unifiedIndexTs      = 0;
  }

  // Convenience views — zero extra API calls
  const getFolderEntries = async () => (await buildUnifiedIndex()).filter(e => e.kind === 'folder');
  const getAllEntries     = async () => (await buildUnifiedIndex());

  // =========================================================================
  // FIX #2: Folder cache with TTL — stale entries expire after 30 s,
  // preventing ghost files after upload/delete/rename/move
  // =========================================================================
  const folderCache    = new Map(); // key → { folders, files, ts }
  const FOLDER_TTL_MS  = 30_000;   // 30 s

  function invalidateFolderCache(path) {
    // Invalidate the specific path (defaults to current) AND the unified index
    folderCache.delete(path ?? currentFolderPath);
    invalidateUnifiedIndex();
  }

  // State
  let currentFolderPath = '';
  let currentView       = 'grid';
  let currentFilter     = 'All Records';

  // Temp upload workflow
  let currentTempToken    = null;
  let currentTempMetadata = null;
  let selectedFolderPath  = '';
  let folderBrowserPath   = '';
  let folderBreadcrumbStack = [{ label: 'Academic Records', path: '' }];

  function denyAction() { showPermissionDenied(); }

  // ─── FAB Chooser Popup ────────────────────────────────────────────────────
  let fabPopupOpen = false;
  function toggleFabChooserPopup() { fabPopupOpen ? closeFabChooserPopup() : openFabChooserPopup(); }
  function openFabChooserPopup() {
    fabPopupOpen = true;
    const popup = document.getElementById('fabChooserPopup');
    const icon  = document.getElementById('fabIcon');
    popup.classList.remove('opacity-0', 'scale-95', 'pointer-events-none');
    popup.classList.add('opacity-100', 'scale-100');
    icon.style.transform = 'rotate(45deg)';
  }
  function closeFabChooserPopup() {
    fabPopupOpen = false;
    const popup = document.getElementById('fabChooserPopup');
    const icon  = document.getElementById('fabIcon');
    popup.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
    popup.classList.remove('opacity-100', 'scale-100');
    icon.style.transform = 'rotate(0deg)';
  }
  document.addEventListener('click', function(e) {
    const btn   = document.getElementById('fabButton');
    const popup = document.getElementById('fabChooserPopup');
    if (fabPopupOpen && btn && popup && !btn.contains(e.target) && !popup.contains(e.target)) {
      closeFabChooserPopup();
    }
  });

  // ─── Icons ────────────────────────────────────────────────────────────────
  const RECORD_ICONS = {
    transcript: `<svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>`,
    pdf:        `<svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>`,
    doc:        `<svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>`,
    docx:       `<svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>`,
    jpg:        `<svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>`,
    png:        `<svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>`,
  };
  const ICON_BG_MAP = { pdf: 'bg-red-50', doc: 'bg-blue-50', docx: 'bg-blue-50', jpg: 'bg-green-50', png: 'bg-green-50' };

  function setView(view) {
    currentView = view;
    const gridBtn = document.getElementById('gridViewBtn');
    const listBtn = document.getElementById('listViewBtn');
    if (view === 'grid') {
      gridBtn.classList.add('bg-green-700', 'text-white');
      gridBtn.classList.remove('bg-white', 'border', 'border-gray-300');
      listBtn.classList.remove('bg-green-700', 'text-white');
      listBtn.classList.add('bg-white', 'border', 'border-gray-300');
    } else {
      listBtn.classList.add('bg-green-700', 'text-white');
      listBtn.classList.remove('bg-white', 'border', 'border-gray-300');
      gridBtn.classList.remove('bg-green-700', 'text-white');
      gridBtn.classList.add('bg-white', 'border', 'border-gray-300');
    }
    loadFolder(currentFolderPath);
  }

  // ─── Breadcrumb ────────────────────────────────────────────────────────────
  const DASHBOARD_URL = '<?= base_url("dashboard") ?>';
  let breadcrumbStack = [
    { label: 'My Files',         folderPath: null, isHome: true  },
    { label: 'Academic Records', folderPath: '',   isHome: false },
  ];

  const BC_CHEVRON  = `<svg class="w-4 h-4 mx-1 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>`;
  const BC_HOME_SVG = `<svg class="w-4 h-4 mr-1.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>`;

  function renderBreadcrumb() {
    const nav = document.getElementById('breadcrumb');
    if (!nav) return;
    nav.innerHTML = '';
    breadcrumbStack.forEach((crumb, i) => {
      const isLast = i === breadcrumbStack.length - 1;
      if (i > 0) nav.insertAdjacentHTML('beforeend', BC_CHEVRON);
      if (isLast) {
        const span = document.createElement('span');
        span.className = 'text-green-700 font-semibold flex items-center';
        if (crumb.isHome) span.innerHTML = BC_HOME_SVG;
        span.insertAdjacentText('beforeend', crumb.label);
        nav.appendChild(span);
      } else if (crumb.isHome) {
        const a = document.createElement('a');
        a.href = DASHBOARD_URL;
        a.className = 'hover:text-green-700 flex items-center transition-colors';
        a.innerHTML = BC_HOME_SVG + crumb.label;
        nav.appendChild(a);
      } else {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'hover:text-green-700 flex items-center transition-colors';
        btn.textContent = crumb.label;
        btn.addEventListener('click', () => {
          breadcrumbStack = breadcrumbStack.slice(0, i + 1);
          renderBreadcrumb();
          _htSetSelected(crumb.folderPath ?? '');
          _htExpandToPath(crumb.folderPath ?? '');
          loadFolder(crumb.folderPath);
        });
        nav.appendChild(btn);
      }
    });
  }

  // =========================================================================
  // FIX #2 (continued): loadFolder with TTL-aware Map cache
  // Stale entries expire; mutations call invalidateFolderCache(path)
  // =========================================================================
  async function loadFolder(path) {
    currentFolderPath = path ?? '';
    const cached = folderCache.get(currentFolderPath);

    if (cached && Date.now() - cached.ts < FOLDER_TTL_MS) {
      renderMasterList(cached.folders, cached.files);
      return;
    }

    document.getElementById('masterList').innerHTML =
      '<div class="p-6 text-gray-400 text-sm flex items-center gap-2"><svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>Loading...</div>';

    try {
      const data = await apiFetch(API.listFolder + '?path=' + encodeURIComponent(currentFolderPath));
      if (!data.success) { showDialog('Failed to load: ' + data.message, 'error'); return; }

      folderCache.set(currentFolderPath, {
        folders: data.folders || [],
        files:   data.files   || [],
        ts:      Date.now(),
      });

      renderMasterList(data.folders || [], data.files || []);

      // FIX #9: Pre-warm unified index during idle time AFTER first render
      // Uses requestIdleCallback so it never competes with user interaction
      if (path === '' && !_unifiedIndex) {
        const schedule = window.requestIdleCallback || (fn => setTimeout(fn, 2000));
        schedule(() => buildUnifiedIndex(), { timeout: 5000 });
      }
    } catch (e) {
      showDialog('Network error loading folder.', 'error');
    }
  }

  function openFolder(folderPath, folderLabel) {
    // Only skip navigation if the path is identical AND the folder cache is
    // still warm. After an upload, the cache is invalidated, so we must allow
    // re-entry even when the breadcrumb already shows the same path.
    const alreadyHere = breadcrumbStack[breadcrumbStack.length - 1].folderPath === folderPath;
    const cacheWarm   = folderCache.has(folderPath) &&
                        (Date.now() - (folderCache.get(folderPath)?.ts ?? 0)) < FOLDER_TTL_MS;
    if (alreadyHere && cacheWarm) return;

    if (!alreadyHere) {
      breadcrumbStack.push({ label: folderLabel, folderPath, isHome: false });
      renderBreadcrumb();
    }
    _htSetSelected(folderPath ?? '');
    _htExpandToPath(folderPath);
    loadFolder(folderPath);
  }

  // =========================================================================
  // FIX #3: renderMasterList — lazy IntersectionObserver sections
  // Only renders items in sections as they scroll into view.
  // FIX #10: Reset _menuIdx / _fileIdx on every render so IDs stay small
  // =========================================================================
  let _menuIdx = 0;
  let _fileIdx = 0;

  function renderMasterList(folders, files) {
    // FIX #10: Reset counters so menu IDs never grow unboundedly
    _menuIdx = 0;
    _fileIdx = 0;

    const container = document.getElementById('masterList');
    container.innerHTML = '';

    if (folders.length === 0 && files.length === 0) {
      container.innerHTML = `
        <div class="flex flex-col items-center justify-center py-20 text-gray-400">
          <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/>
          </svg>
          <p class="text-lg font-medium">This folder is empty</p>
          <p class="text-sm mt-1">Upload a file or create a new folder to get started.</p>
        </div>`;
      return;
    }

    // Group by first letter, sorted
    const allItems = [
      ...folders.map(f => ({ ...f, _type: 'folder' })),
      ...files.map(f   => ({ ...f, _type: 'file'   })),
    ].sort((a, b) => a.name.toLowerCase().localeCompare(b.name.toLowerCase()));

    const groups = allItems.reduce((acc, item) => {
      const key = item.name.charAt(0).toUpperCase();
      (acc[key] = acc[key] || []).push(item);
      return acc;
    }, {});

    // Use DocumentFragment for a single DOM insertion — one reflow total
    const frag = document.createDocumentFragment();

    // IntersectionObserver: only populate a section when it enters the viewport
    const io = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (!entry.isIntersecting) return;
        const section = entry.target;
        if (section.dataset.rendered) return;
        section.dataset.rendered = '1';
        io.unobserve(section);
        _populateSection(section, groups[section.dataset.letter]);
      });
    }, { rootMargin: '200px' }); // pre-render 200 px before visible

    Object.keys(groups).sort().forEach(letter => {
      const section = document.createElement('div');
      section.className = 'mb-8 folder-section';
      section.dataset.letter = letter;
      // Estimated placeholder height prevents layout shift while section is invisible
      section.style.minHeight = Math.ceil(groups[letter].length / 3) * 120 + 56 + 'px';
      section.innerHTML =
        `<div class="bg-green-700 text-white px-4 py-2 rounded-lg mb-4 font-bold text-lg">${letter}</div>`;
      frag.appendChild(section);
      io.observe(section);
    });

    container.appendChild(frag); // single DOM write
  }

  // Populate one letter-section (called lazily by IntersectionObserver)
  function _populateSection(section, items) {
    const folderItems = items.filter(i => i._type === 'folder');
    const fileItems   = items.filter(i => i._type === 'file');

    if (folderItems.length > 0) {
      const row  = document.createElement('div');
      row.className = currentView === 'list' ? 'folder-row list-mode mb-4' : 'folder-row mb-4';
      // DocumentFragment: build all folder cards before touching the live DOM
      const frag = document.createDocumentFragment();
      folderItems.forEach(f => {
        const div = document.createElement('div');
        div.className = 'folder-card-compact';
        div.innerHTML = buildFolderCardHTML(f);
        frag.appendChild(div);
      });
      row.appendChild(frag);
      section.appendChild(row);
    }

    if (fileItems.length > 0) {
      const frag = document.createDocumentFragment();
      if (currentView === 'grid') {
        const grid = document.createElement('div');
        grid.className = 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4';
        fileItems.forEach(f => {
          const div = document.createElement('div');
          div.innerHTML = buildFileCardHTML(f);
          grid.appendChild(div.firstElementChild);
        });
        frag.appendChild(grid);
      } else {
        frag.appendChild(buildFileListTable(fileItems));
      }
      section.appendChild(frag);
    }

    // Remove the placeholder min-height now that real content is rendered
    section.style.minHeight = '';
  }

  // =========================================================================
  // FIX #4: Event delegation on #masterList
  // One listener replaces thousands of inline onclick handlers.
  // Cards use data-action attributes instead of onclick="..." strings.
  // =========================================================================
  document.getElementById('masterList').addEventListener('click', function (e) {
    // Folder open (click on the folder card area, not the menu button)
    const folderOpen = e.target.closest('[data-action="open-folder"]');
    if (folderOpen) {
      openFolder(folderOpen.dataset.path, folderOpen.dataset.label);
      return;
    }

    // File preview (click anywhere on card that isn't a button)
    const fileCard = e.target.closest('[data-action="preview-file"]');
    if (fileCard && !e.target.closest('button')) {
      fileAction_preview(fileCard.dataset.path);
      return;
    }

    // All button actions
    const btn = e.target.closest('button[data-action]');
    if (!btn) return;
    const { action, path, menuId, type } = btn.dataset;

    switch (action) {
      case 'toggle-folder-menu': openMenuById('folder-menu-' + menuId, e); break;
      case 'toggle-file-menu':   openMenuById('file-menu-'   + menuId, e); break;
      case 'toggle-submenu':     openMenuById('submenu-'     + menuId, e); break;
      case 'toggle-file-sub':    openMenuById('file-sub-'    + menuId, e); break;
      case 'delete-file':        fileAction_delete(path, menuId);       break;
      case 'delete-folder':      fileAction_deleteFolder(path, menuId); break;
      case 'preview':            fileAction_preview(path);              break;
      case 'download':           fileAction_download(path);             break;
     case 'rename':
        if (type === 'folder') { openRenameFolderModal(path); }
        else                   { openRenameModal(path, type); }
        break;

      case 'move':               openMoveModal(path, type);             break;
      case 'upload-here':        openUploadModalForFolder(path);        break;
    }
  });

  // =========================================================================
  // FIX #5: Single tracked open-menu reference — no querySelectorAll scans
  // =========================================================================
  let _openMenuEl = null;
  let _openSubmenuEl = null;

  function openMenuById(id, event) {
    event.stopPropagation();
    const next = document.getElementById(id);
    if (!next) return;

    const isSubmenu = id.startsWith('submenu-') || id.startsWith('file-sub-');

    if (isSubmenu) {
      if (_openSubmenuEl && _openSubmenuEl !== next) {
        _openSubmenuEl.classList.add('hidden');
      }
      next.classList.toggle('hidden');
      _openSubmenuEl = next.classList.contains('hidden') ? null : next;
    } else {
      if (_openSubmenuEl) { _openSubmenuEl.classList.add('hidden'); _openSubmenuEl = null; }
      if (_openMenuEl && _openMenuEl !== next) {
        _openMenuEl.classList.add('hidden');
      }
      next.classList.toggle('hidden');
      _openMenuEl = next.classList.contains('hidden') ? null : next;
    }
  }

  document.addEventListener('click', (e) => {
    if (_openSubmenuEl && !_openSubmenuEl.contains(e.target) && !e.target.closest('[data-action="toggle-submenu"],[data-action="toggle-file-sub"]')) {
      _openSubmenuEl.classList.add('hidden');
      _openSubmenuEl = null;
    }
    if (_openMenuEl && !_openMenuEl.contains(e.target) && !e.target.closest('[data-action="toggle-folder-menu"],[data-action="toggle-file-menu"]')) {
      _openMenuEl.classList.add('hidden');
      _openMenuEl = null;
    }
  }, true);

  // ─── Build folder card HTML ───────────────────────────────────────────────
  function buildFolderCardHTML(f) {
    const mid      = 'fm' + (++_menuIdx); // short prefix, resets each render
    const safeName = f.name.replace(/'/g, "\\'");
    const safePath = f.path.replace(/'/g, "\\'");
    let menuItems  = '';

    if (PRIV_DELETE_FOLDER) {
      menuItems += `<button data-action="delete-folder" data-path="${safePath}" data-menu-id="${mid}"
        class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50 text-red-600 flex items-center gap-2">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>Delete</button>`;
    }
    if (PRIV_UPLOAD) {
      menuItems += `<button data-action="upload-here" data-path="${safePath}"
        class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50 text-gray-700 flex items-center gap-2">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>Upload Here</button>`;
    }
    if (PRIV_ORGANIZE) {
      menuItems += `<div class="relative folder-submenu-container">
        <button data-action="toggle-submenu" data-menu-id="org-${mid}"
          class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50 text-gray-700 flex items-center justify-between">
          <span class="flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>Organise
          </span>
          <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>
        <div id="submenu-org-${mid}" class="hidden absolute left-full top-0 ml-1 w-36 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
          <button data-action="rename" data-path="${safePath}" data-type="folder"
            class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50 text-gray-700">Rename</button>
          <button data-action="move" data-path="${safePath}" data-type="folder"
            class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50 text-gray-700">Move</button>
        </div>
      </div>`;
    }

    return `
      <div class="folder-card bg-white rounded-lg p-4 border border-gray-200 hover:border-green-500 transition-colors cursor-pointer relative" data-folder-name="${f.name}">
        <div class="flex items-start justify-between">
          <div class="flex items-center gap-3 flex-1"
               data-action="open-folder" data-path="${safePath}" data-label="${safeName}">
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
              <svg class="w-7 h-7 text-green-700" fill="currentColor" viewBox="0 0 24 24"><path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/></svg>
            </div>
            <div>
              <h4 class="font-bold text-gray-800 truncate max-w-[120px]">${f.name}</h4>
              <p class="text-xs text-gray-500">${f.count} file${f.count !== 1 ? 's' : ''} · ${f.modified}</p>
            </div>
          </div>
          ${menuItems ? `
          <div class="relative">
            <button data-action="toggle-folder-menu" data-menu-id="${mid}"
              class="text-gray-400 hover:text-gray-600 p-1">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg>
            </button>
            <div id="folder-menu-${mid}" class="hidden absolute right-0 mt-1 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-50 py-1">
              ${menuItems}
            </div>
          </div>` : ''}
        </div>
      </div>`;
  }

  // ─── Build file card HTML (grid view) ─────────────────────────────────────
  function buildFileCardHTML(f) {
    const mid      = 'fi' + (++_fileIdx);
    const safePath = f.path.replace(/'/g, "\\'");
    const ext      = f.ext || 'pdf';
    const iconSvg  = RECORD_ICONS[ext] || RECORD_ICONS['pdf'];
    const iconBg   = ICON_BG_MAP[ext]  || 'bg-gray-50';
    const label    = ext.toUpperCase();

    let menuItems = '';
    if (PRIV_DELETE_RECORD) {
      menuItems += `<button data-action="delete-file" data-path="${safePath}" data-menu-id="${mid}"
        class="w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>Delete</button>`;
    }
    if (PRIV_ORGANIZE) {
      menuItems += `<div class="relative">
        <button data-action="toggle-file-sub" data-menu-id="org-${mid}"
          class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center justify-between">
          <span class="flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>Organise
          </span>
          <svg class="w-3 h-3 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>
        <div id="file-sub-org-${mid}" class="hidden absolute left-full top-0 ml-1 w-36 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
          <button data-action="rename" data-path="${safePath}" data-type="file"
            class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">Rename</button>
          <button data-action="move" data-path="${safePath}" data-type="file"
            class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">Move</button>
        </div>
      </div>`;
    }
    menuItems += `<div class="relative">
      <button data-action="toggle-file-sub" data-menu-id="view-${mid}"
        class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center justify-between">
        <span class="flex items-center gap-2">
          <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>View
        </span>
        <svg class="w-3 h-3 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
      </button>
      <div id="file-sub-view-${mid}" class="hidden absolute left-full top-0 ml-1 w-36 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
        <button data-action="preview" data-path="${safePath}"
          class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">Preview</button>
        <button data-action="download" data-path="${safePath}"
          class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">Download</button>
      </div>
    </div>`;

    return `
      <div class="record-card bg-white rounded-xl p-4 shadow-sm border border-gray-100 cursor-pointer hover:shadow-md transition-shadow"
           data-action="preview-file" data-path="${f.path}" data-name="${f.name}">
        <div class="flex items-start justify-between mb-3">
          <div class="w-10 h-10 ${iconBg} rounded-lg flex items-center justify-center">${iconSvg}</div>
          <div class="relative">
            <button data-action="toggle-file-menu" data-menu-id="${mid}"
              class="text-gray-400 hover:text-gray-600 p-0.5 rounded hover:bg-gray-100 transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg>
            </button>
            <div id="file-menu-${mid}" class="hidden absolute right-0 top-7 w-44 bg-white rounded-lg shadow-lg border border-gray-200 z-50 py-1">
              ${menuItems}
            </div>
          </div>
        </div>
        <h4 class="font-medium text-gray-800 mb-1 truncate" title="${f.name}">${f.name}</h4>
        <div class="flex items-center justify-between text-xs text-gray-500 mt-2">
          <span class="px-2 py-0.5 bg-gray-100 rounded">${label}</span>
          <span>${f.size}</span>
        </div>
        <p class="text-xs text-gray-400 mt-1">${f.modified}</p>
      </div>`;
  }

  // ─── Build file list table (list view) ────────────────────────────────────
  function buildFileListTable(files) {
    const wrap = document.createElement('div');
    wrap.className = 'w-full border border-gray-200 rounded-xl overflow-hidden mb-4';
    wrap.innerHTML = `
      <div class="grid grid-cols-12 gap-2 bg-gray-50 px-4 py-2 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider">
        <div class="col-span-5">Name</div>
        <div class="col-span-2">Type</div>
        <div class="col-span-2">Modified</div>
        <div class="col-span-2">Size</div>
        <div class="col-span-1 text-right">Actions</div>
      </div>
      <div class="list-table-body divide-y divide-gray-100"></div>`;

    const tbody = wrap.querySelector('.list-table-body');
    // DocumentFragment: one reflow for entire table body
    const frag  = document.createDocumentFragment();

    files.forEach(f => {
      const mid      = 'fi' + (++_fileIdx);
      const safePath = f.path.replace(/'/g, "\\'");
      const ext      = f.ext || 'pdf';
      const iconSvg  = RECORD_ICONS[ext] || RECORD_ICONS['pdf'];
      const iconBg   = ICON_BG_MAP[ext]  || 'bg-gray-50';

      let menuItems = '';
      if (PRIV_DELETE_RECORD)
        menuItems += `<button data-action="delete-file" data-path="${safePath}" data-menu-id="${mid}"
          class="w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-red-50">Delete</button>`;
      if (PRIV_ORGANIZE)
        menuItems += `<button data-action="rename" data-path="${safePath}" data-type="file"
          class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">Rename</button>
          <button data-action="move" data-path="${safePath}" data-type="file"
          class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">Move</button>`;
      menuItems += `<button data-action="preview" data-path="${safePath}"
        class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">Preview</button>
        <button data-action="download" data-path="${safePath}"
        class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">Download</button>`;

      const row = document.createElement('div');
      row.className = 'grid grid-cols-12 gap-2 px-4 py-3 hover:bg-gray-50 items-center text-sm';
      row.innerHTML = `
        <div class="col-span-5 flex items-center gap-3">
          <div class="w-8 h-8 ${iconBg} rounded-lg flex items-center justify-center flex-shrink-0">${iconSvg}</div>
          <span class="font-medium text-gray-800 truncate" title="${f.name}">${f.name}</span>
        </div>
        <div class="col-span-2 text-gray-500 uppercase text-xs">${ext}</div>
        <div class="col-span-2 text-gray-500">${f.modified}</div>
        <div class="col-span-2 text-gray-500">${f.size}</div>
        <div class="col-span-1 flex justify-end">
          <div class="relative">
            <button data-action="toggle-file-menu" data-menu-id="${mid}"
              class="text-gray-400 hover:text-gray-600 p-1 rounded hover:bg-gray-100">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg>
            </button>
            <div id="file-menu-${mid}" class="hidden absolute right-0 top-7 w-44 bg-white rounded-lg shadow-lg border border-gray-200 z-50 py-1">
              ${menuItems}
            </div>
          </div>
        </div>`;
      frag.appendChild(row);
    });

    tbody.appendChild(frag);
    return wrap;
  }

  // =========================================================================
  // FIX #6: Search — 300 ms debounce (was 180), clearSearch reuses cache
  // =========================================================================
  let _searchActive   = false;
  let _searchDebounce = null;

  

  function filterRecords() {
    const q = document.getElementById('searchInput').value.trim();
    clearTimeout(_searchDebounce);

    if (q === '') {
      if (_searchActive) {
        _searchActive = false;
        document.getElementById('breadcrumb').style.display = '';
        // FIX #6: reuse cached folder data — zero API calls on clear
        const cached = folderCache.get(currentFolderPath);
        if (cached) {
          renderMasterList(cached.folders, cached.files);
        } else {
          loadFolder(currentFolderPath);
        }
      }
      return;
    }

    // FIX #6: 300 ms debounce (was 180) — halves lookup frequency for fast typists
    _searchDebounce = setTimeout(() => runGlobalSearch(q), 300);
  }

  async function runGlobalSearch(q) {
    _searchActive = true;
    document.getElementById('breadcrumb').style.display = 'none';

    document.getElementById('masterList').innerHTML = `
      <div class="flex items-center justify-center py-10 text-gray-400">
        <svg class="w-6 h-6 animate-spin mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
        </svg>Searching…
      </div>`;

    // Run filesystem index + DB metadata search in PARALLEL for speed.
    // The unified index is pre-warmed on first folder load so getAllEntries()
    // returns from memory on subsequent searches — no extra API round-trip.
    const [index, dbResults] = await Promise.all([
      getAllEntries(),                  // in-memory, instant after first load
      fetch('<?= base_url('academic-records/metadata-search') ?>?q=' + encodeURIComponent(q))
        .then(r => r.ok ? r.json() : { results: [] })
        .catch(() => ({ results: [] })) // never crash search on DB error
    ]);

    const currentQ = document.getElementById('searchInput').value.trim();
    if (currentQ !== q) return; // stale — user typed more

    const lower = q.toLowerCase();

    // Filesystem name matches (folders + files)
    const matchedFolders = index.filter(e => e.kind === 'folder' && e.name.toLowerCase().includes(lower));
    const matchedFiles   = index.filter(e => e.kind === 'file'   && e.name.toLowerCase().includes(lower));

    // DB/OCR matches — convert to same shape as filesystem files, avoid duplicates
    const fsFilePaths = new Set(matchedFiles.map(f => f.path));
    const dbFiles = (dbResults.results ?? [])
      .filter(r => !fsFilePaths.has(r.file_path))  // skip if already found by name
      .map(r => ({
        kind:         'file',
        name:         r.original_name ?? r.filename,
        path:         r.file_path,
        download_url: r.download_url,
        preview_url:  r.preview_url,
        _fromDb:      true,  // flag so we can show an "OCR match" badge
        _docType:     r.document_type_label ?? '',
        _studentName: r.student_name ?? '',
      }));

    // Merge: filesystem matches first, then DB-only OCR matches
    const allFiles = [...matchedFiles, ...dbFiles];

    renderSearchResults(q, matchedFolders, allFiles);
  }

  function highlightMatch(text, q) {
    if (!q) return text;
    const idx = text.toLowerCase().indexOf(q.toLowerCase());
    if (idx === -1) return text;
    return text.slice(0, idx)
      + `<mark class="bg-yellow-200 text-gray-900 rounded px-0.5">${text.slice(idx, idx + q.length)}</mark>`
      + text.slice(idx + q.length);
  }

  function renderSearchResults(q, folders, files) {
    const container = document.getElementById('masterList');
    container.innerHTML = '';
    const total = folders.length + files.length;

    const header = document.createElement('div');
    header.className = 'mb-5 flex items-center gap-3';
    header.innerHTML = `
      <div class="flex-1">
        <p class="text-sm text-gray-500">
          Results for <span class="font-semibold text-gray-800">"${q}"</span>
          &mdash; <span class="text-green-700 font-semibold">${total}</span> result${total !== 1 ? 's' : ''} found
        </p>
      </div>
      <button onclick="clearSearch()"
              class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1.5 px-3 py-1.5 rounded-lg hover:bg-gray-100 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>Clear search
      </button>`;
    container.appendChild(header);

    if (total === 0) {
      container.insertAdjacentHTML('beforeend', `
        <div class="flex flex-col items-center justify-center py-20 text-gray-400">
          <svg class="w-14 h-14 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
          <p class="text-lg font-medium">No results found</p>
          <p class="text-sm mt-1">Try a different keyword or check the spelling.</p>
        </div>`);
      return;
    }

    const listWrap = document.createElement('div');
    listWrap.className = 'border border-gray-200 rounded-xl overflow-hidden divide-y divide-gray-100';

    const allResults = [
      ...folders.map(f => ({ ...f, _kind: 'folder' })),
      ...files.map(f   => ({ ...f, _kind: 'file'   })),
    ];

    // DocumentFragment for search results — one reflow
    const frag = document.createDocumentFragment();
    allResults.forEach(item => {
      const isFolder  = item._kind === 'folder';
      const safePath  = item.path.replace(/'/g, "\\'");
      const safeName  = item.name.replace(/'/g, "\\'");
      const highlighted = highlightMatch(item.name, q);
      const locationLabel = item.locationLabel || 'Academic Records';

      const iconHTML = isFolder
        ? `<div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
             <svg class="w-6 h-6 text-green-700" fill="currentColor" viewBox="0 0 24 24"><path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/></svg>
           </div>`
        : (() => {
            const ext    = item.ext || 'pdf';
            const icon   = RECORD_ICONS[ext] || RECORD_ICONS['pdf'];
            const iconBg = ICON_BG_MAP[ext]  || 'bg-gray-50';
            return `<div class="w-10 h-10 ${iconBg} rounded-lg flex items-center justify-center flex-shrink-0">${icon}</div>`;
          })();

      const actionHTML = isFolder
        ? `<button onclick="openFolderFromSearch('${safePath}','${safeName}','${locationLabel.replace(/'/g, "\\'")}')"
                   class="text-xs px-3 py-1.5 rounded-lg bg-green-50 text-green-700 hover:bg-green-100 font-medium transition-colors whitespace-nowrap">Open</button>`
        : `<div class="flex items-center gap-2">
             <button data-action="preview" data-path="${safePath}"
                     class="text-xs px-3 py-1.5 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 font-medium transition-colors whitespace-nowrap">Preview</button>
             <button data-action="download" data-path="${safePath}"
                     class="text-xs px-3 py-1.5 rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 font-medium transition-colors whitespace-nowrap">Download</button>
           </div>`;

      const row = document.createElement('div');
      row.className = 'flex items-center gap-4 px-4 py-3 hover:bg-gray-50 transition-colors';
      row.innerHTML = `
        ${iconHTML}
        <div class="flex-1 min-w-0">
          <p class="font-semibold text-gray-800 truncate">${highlighted}</p>
          <p class="text-xs text-gray-400 mt-0.5 flex items-center gap-1 truncate">
            <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/>
            </svg>${locationLabel}
          </p>
        </div>
        <div class="flex-shrink-0">${actionHTML}</div>`;
      frag.appendChild(row);
    });

    listWrap.appendChild(frag);
    container.appendChild(listWrap);
  }

  function clearSearch() {
    document.getElementById('searchInput').value = '';
    _searchActive = false;
    document.getElementById('breadcrumb').style.display = '';
    renderBreadcrumb();
    // FIX #6: reuse cached data — never re-fetches on clear
    const cached = folderCache.get(currentFolderPath);
    if (cached) {
      renderMasterList(cached.folders, cached.files);
    } else {
      loadFolder(currentFolderPath);
    }
  }

  function openFolderFromSearch(folderPath, folderName, locationLabel) {
    clearSearch();
    breadcrumbStack = [
      { label: 'My Files',         folderPath: null, isHome: true  },
      { label: 'Academic Records', folderPath: '',   isHome: false },
    ];
    breadcrumbStack.push({ label: folderName, folderPath, isHome: false });
    renderBreadcrumb();
    loadFolder(folderPath);
  }

  // ─── New Folder Modal ─────────────────────────────────────────────────────
  function openNewFolderModal() {
    if (!PRIV_ADD_FOLDER) { denyAction(); return; }
    document.getElementById('newFolderModal').classList.remove('hidden');
  }
  function closeNewFolderModal() {
    document.getElementById('newFolderModal').classList.add('hidden');
    const nf = document.getElementById('newFolderName');
    const ns = document.getElementById('newFolderSubname');
    if (nf) nf.value = '';
    if (ns) ns.value = '';
  }
  async function createFolder() {
    if (!PRIV_ADD_FOLDER) { denyAction(); return; }
    const rawName = (document.getElementById('newFolderName')?.value || '').trim();
    if (!rawName) { showDialog('Please enter a folder name.', 'warning'); return; }

    const data = await apiFetch(API.createFolder, 'POST', {
      parent_path: currentFolderPath,
      folder_name: rawName,
    });
    if (!data.success) { showDialog('Error: ' + data.message, 'error'); return; }

    closeNewFolderModal();
    invalidateFolderCache(currentFolderPath); // FIX #2: targeted invalidation
    loadFolder(currentFolderPath);
    // FIX #8: surgical tree refresh — only parent path, not full re-crawl
    _htRefreshPath(currentFolderPath);
  }

  // ─── Upload Record Modal ──────────────────────────────────────────────────
  const UPLOAD_BTN_LABEL = 'Upload Record';

  function openUploadModal() {
    if (!PRIV_UPLOAD) { denyAction(); return; }
    document.getElementById('uploadFolderPath').value = currentFolderPath;
    document.getElementById('uploadModal').classList.remove('hidden');
  }
  function closeUploadModal() {
    // If user clicks Cancel/X during a queued multi-upload, mark that tray row as errored
    if (window.UploadTray && currentTempMetadata && currentTempMetadata._trayId) {
      window.UploadTray.setError(currentTempMetadata._trayId, 'Cancelled');
    }

    document.getElementById('uploadModal').classList.add('hidden');
    document.getElementById('uploadForm')?.reset();
    const submitBtn = document.querySelector('#uploadForm button[type="submit"]');
    if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = UPLOAD_BTN_LABEL; }
    const lbl = document.getElementById('recordFileName');
    if (lbl) { lbl.innerHTML = ''; lbl.classList.add('hidden'); }
    document.querySelector('#uploadForm .upload-progress-container')?.remove();
  }
  function openUploadModalForFolder(folderPath) {
    if (!PRIV_UPLOAD) { denyAction(); return; }
    document.getElementById('uploadFolderPath').value = folderPath;
    document.getElementById('uploadModal').classList.remove('hidden');
  }

  document.getElementById('uploadForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    if (!PRIV_UPLOAD) { denyAction(); return; }
    const submitBtn = this.querySelector('button[type="submit"]');
    if (submitBtn.disabled) return;

    const recordFile = document.getElementById('recordFileInput');
    if (!recordFile?.files?.length) {
      showDialog('Please upload a file or folder first.', 'warning');
      return;
    }

    const formData = new FormData(this);
    this.querySelector('.upload-progress-container')?.remove();

    const progressContainer = document.createElement('div');
    progressContainer.className = 'upload-progress-container mt-4';
    progressContainer.innerHTML = `
      <div class="bg-gray-200 rounded-full h-3 overflow-hidden shadow-inner">
        <div id="uploadProgress" class="bg-gradient-to-r from-green-500 to-green-600 h-full transition-all duration-300 flex items-center justify-end" style="width:0%">
          <span id="uploadPercentText" class="text-xs font-bold text-white pr-2"></span>
        </div>
      </div>
      <div class="flex items-center justify-between mt-2">
        <p id="uploadStatus" class="text-sm text-gray-600 font-medium"><span class="inline-block animate-pulse">⬆️</span> Uploading...</p>
        <p id="uploadSize" class="text-xs text-gray-500"></p>
      </div>`;
    this.appendChild(progressContainer);

    submitBtn.disabled = true;
    submitBtn.innerHTML = `<svg class="animate-spin h-5 w-5 mr-2 inline-block" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Uploading...`;

    const resetOnError = () => {
      submitBtn.disabled = false;
      submitBtn.textContent = UPLOAD_BTN_LABEL;
      progressContainer.remove();
    };

    try {
      const xhr = new XMLHttpRequest();
      xhr.upload.addEventListener('progress', (e) => {
        if (!e.lengthComputable) return;
        const pct = (e.loaded / e.total) * 100;
        const progressBar = document.getElementById('uploadProgress');
        const percentText = document.getElementById('uploadPercentText');
        const sizeText    = document.getElementById('uploadSize');
        const statusEl    = document.getElementById('uploadStatus');
        if (progressBar)  progressBar.style.width    = pct + '%';
        if (percentText)  percentText.textContent     = pct > 20 ? Math.round(pct) + '%' : '';
        if (sizeText)     sizeText.textContent        = `${(e.loaded/1048576).toFixed(1)} MB / ${(e.total/1048576).toFixed(1)} MB`;
        if (statusEl) {
          if (pct < 30)       statusEl.innerHTML = '<span class="inline-block animate-pulse">⬆️</span> Uploading...';
          else if (pct < 70)  statusEl.innerHTML = '<span class="inline-block animate-pulse">📤</span> Upload in progress...';
          else if (pct < 100) statusEl.innerHTML = '<span class="inline-block animate-pulse">⏱️</span> Almost done...';
          else                statusEl.innerHTML = '<span class="inline-block">✅</span> Processing...';
        }
      });
      xhr.addEventListener('load', () => {
        submitBtn.disabled = false;
        submitBtn.textContent = UPLOAD_BTN_LABEL;
        progressContainer.remove();
        if (xhr.status !== 200) { showDialog('Upload failed. Please try again.', 'error'); return; }
        let data;
        try { data = JSON.parse(xhr.responseText); }
        catch(err) { showDialog('Unexpected server response.', 'error'); return; }
        if (!data.success) { showDialog('Upload failed: ' + data.message, 'error'); return; }

        currentTempToken    = data.token;
        currentTempMetadata = {
            original_name: data.original_name,
            size:          data.size,
            preview_url:   data.preview_url,
            // Always use the extension the server determined (from actual file bytes)
            ext: data.file_ext || data.original_name.split('.').pop().toLowerCase() || 'pdf',
        };

        closeUploadModal();
        openTempPreviewModal(data.token, data.preview_url, data.original_name);
        applyOcrSuggestions(data);
      });
      xhr.addEventListener('error', () => { showDialog('Network error. Check your connection.', 'error'); resetOnError(); });
      xhr.addEventListener('abort', () => { showDialog('Upload cancelled.', 'warning'); resetOnError(); });
      xhr.open('POST', API.listFolder.replace('/list-folder', '/temp-upload'));
      xhr.send(formData);
    } catch (error) {
      showDialog('Upload failed. Please try again.', 'error');
      resetOnError();
    }
  });

  // ─── OCR Suggestions ──────────────────────────────────────────────────────
  function applyOcrSuggestions(res) {
    document.getElementById('ocrSuggestionPanel')?.remove();
    const suggestions       = res.ocr_suggestions || {};
    const suggestedFolder   = suggestions.folder   || '';
    const suggestedFilename = suggestions.filename  || '';
    if (!res.ocr_success && !suggestedFolder && !suggestedFilename) return;
    window._ocrSuggestedFolder   = suggestedFolder;
    window._ocrSuggestedFilename = suggestedFilename;
    const panel = document.createElement('div');
    panel.id = 'ocrSuggestionPanel';
    panel.className = 'mx-6 mb-3 p-3 bg-teal-50 border border-teal-200 rounded-xl text-sm';
    panel.innerHTML = `
      <p class="font-semibold text-teal-700 mb-2">✦ OCR Detected — suggestions ready</p>
      <div class="flex flex-col gap-1 text-gray-700 mb-2">
        <span>Folder: <strong class="text-teal-800">${suggestedFolder || '(none)'}</strong></span>
        <span>Filename: <strong class="text-teal-800">${suggestedFilename || '(none)'}</strong></span>
      </div>
      <button onclick="applyOcrToFolderBrowser()"
              class="px-3 py-1.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold rounded-lg transition-colors">
        ✦ Apply or Edit Suggestions
      </button>`;
    const footer = document.querySelector('#previewModal .flex.items-center.justify-end.gap-3');
    if (footer) footer.parentNode.insertBefore(panel, footer);
  }

  function applyOcrToFolderBrowser() { openEditFilenameModal(); }

  function openEditFilenameModal() {
    const filename = window._ocrSuggestedFilename || '';

    // Detect the real extension from the original uploaded file
    const originalExt = (currentTempMetadata?.ext || 'pdf').toLowerCase();

    const nameWithoutExt = filename.replace(/\.[a-z0-9]+$/i, '');
    document.getElementById('editFilenameInput').value = nameWithoutExt;

    // Update the extension badge and format hint to show the real extension
    const extBadge = document.getElementById('editFilenameExt');
    const fmtHint  = document.getElementById('editFilenameFormatHint');
    if (extBadge) extBadge.textContent = '.' + originalExt;
    if (fmtHint)  fmtHint.textContent  = 'StudentId_StudentName_DocType_Label.' + originalExt;

    const select = document.getElementById('editFilenameDocType');
    const knownLabels = Array.from(select.options).map(o => o.value).filter(Boolean);
    const matched = knownLabels.find(label => nameWithoutExt.includes(label));
    select.value = matched || '';
    document.getElementById('editFilenameModal').classList.remove('hidden');
}
  function closeEditFilenameModal() {
    document.getElementById('editFilenameModal').classList.add('hidden');
  }
  function onDocTypeLabelChange() {
    const select = document.getElementById('editFilenameDocType');
    const label  = select.value;
    const input  = document.getElementById('editFilenameInput');
    if (!label) return;
    let current = input.value.trim();
    const knownLabels = Array.from(select.options).map(o => o.value).filter(Boolean);
    knownLabels.forEach(l => {
      current = current.replace(new RegExp('_?' + l + '$', 'i'), '').replace(/_+$/, '');
    });
    input.value = (current ? current + '_' : '') + label;
  }

  async function confirmEditFilename() {
    let finalName = document.getElementById('editFilenameInput').value.trim();
    if (!finalName) { showDialog('Please enter a filename before continuing.', 'warning'); return; }
    finalName = finalName.replace(/\s+/g, '_').replace(/[^\w\-]/g, '');
    const originalExt = (currentTempMetadata?.ext || 'pdf').toLowerCase();
    window._ocrSuggestedFilename = finalName + '.' + originalExt;
    if (currentTempMetadata) currentTempMetadata.suggested_filename = finalName + '.' + originalExt;

    // Update the OCR suggestion panel's displayed filename to match what the user typed
    const ocrFilenameDisplay = document.querySelector('#ocrSuggestionPanel .flex.flex-col span:last-child strong');
    if (ocrFilenameDisplay) ocrFilenameDisplay.textContent = window._ocrSuggestedFilename;

    closeEditFilenameModal();
    // Derive the best folder search term:
    // Prefer the OCR-suggested folder (contains student ID + full name).
    // If the user has edited the filename, also try to extract the student ID
    // from the beginning of the new filename as a fallback search term,
    // since the OCR folder suggestion remains the most reliable signal.
    const suggestedFolder = (window._ocrSuggestedFolder || '').trim();

    // Extract student ID prefix from the confirmed filename (digits at the start)
    // e.g. "2022101042_jayron_Certificate_Of_Registration" → "2022101042"
    // Falls back to empty string if the filename doesn't start with digits.
    const filenameIdMatch = window._ocrSuggestedFilename
        ? window._ocrSuggestedFilename.match(/^(\d{5,20})/)
        : null;
    const filenameIdPrefix = filenameIdMatch ? filenameIdMatch[1] : '';

    // Use the raw input the user typed — grab the first underscore-separated word
    // after stripping any leading digits (student ID prefix).
    // e.g. user typed "rafael"                          → effectiveSearch = "rafael"
    // e.g. user typed "rafael_Certificate_Of_Registration" → effectiveSearch = "rafael"
    // e.g. user typed "2022101042_rafael_Certificate"   → effectiveSearch = "rafael"
    const rawInput = document.getElementById('editFilenameInput')?.value?.trim() || finalName;
    const strippedInput = rawInput.replace(/^\d+_?/, ''); // strip leading student ID
    const effectiveSearch = strippedInput.split('_')[0] || suggestedFolder;

    invalidateFolderBrowserIndex(); // calls invalidateUnifiedIndex internally
    invalidateUnifiedIndex();       // explicit second call ensures _unifiedIndex is
                                    // fully cleared even if the shim is ever changed
    openFolderBrowserModal();
    if (!effectiveSearch) return;
    await new Promise(resolve => requestAnimationFrame(resolve));
    const index = await getFolderEntries(); // uses unified index
    const searchInput = document.getElementById('folderSearchInput');
    if (!searchInput) return;
    const lower = effectiveSearch.toLowerCase();
    const match =
      index.find(f => f.name.toLowerCase() === lower) ||
      index.find(f => f.name.toLowerCase().startsWith(lower) && f.name.length > 8) ||
      index.find(f => lower.startsWith(f.name.toLowerCase()) && f.name.length > 8) ||
      index.find(f => f.name.toLowerCase().includes(lower) && f.name.length > 8);
       searchInput.value = match ? match.name : effectiveSearch;
    _folderBrowserSearchActive = true;
    renderFolderBrowserResults(searchInput.value);
    if (!match) return;
    requestAnimationFrame(() => {
      document.querySelectorAll('#folderBrowserList .folder-browser-item').forEach(item => {
        if (item.dataset.folderPath === match.path) {
          selectFolderBrowserItem(item, match.path, match.name, match.locationLabel);
        }
      });
    });

  }

  // ─── Temp Preview Modal ───────────────────────────────────────────────────
  function openTempPreviewModal(token, previewUrl, fileName) {
    const modal     = document.getElementById('previewModal');
    const titleEl   = document.getElementById('previewTitle');
    const contentEl = document.getElementById('previewContent');
    if (!modal || !titleEl || !contentEl) return;
    titleEl.textContent = fileName;
    modal.classList.remove('hidden');
    const ext = fileName.split('.').pop().toLowerCase();
    if (['jpg', 'jpeg', 'png'].includes(ext)) {
      contentEl.innerHTML = `<img src="${previewUrl}" alt="Preview" class="max-w-full max-h-full mx-auto rounded-lg shadow-lg"/>`;
    } else if (ext === 'pdf') {
      contentEl.innerHTML = `<iframe src="${previewUrl}" class="w-full border-0 rounded-lg" style="min-height:520px;" title="PDF Preview"></iframe>`;
    } else {
      contentEl.innerHTML = `
        <div class="text-center text-gray-500 py-10">
          <svg class="w-20 h-20 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
          </svg>
          <p class="text-lg font-semibold mb-2">Preview not available</p>
          <p class="text-sm text-gray-400 mb-4">.${ext.toUpperCase()} files cannot be previewed in the browser.</p>
          <button onclick="downloadTempFile()" class="px-6 py-2.5 bg-green-700 text-white rounded-lg hover:bg-green-800 transition-colors">Download to View</button>
        </div>`;
    }
    const footer = modal.querySelector('.flex.items-center.justify-end.gap-3');
    if (footer) {
      footer.innerHTML = `
        <button onclick="cancelTempUpload()" class="px-6 py-2.5 rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors font-medium text-gray-700">✗ Cancel</button>
        <button onclick="openFolderBrowserModal()" class="px-6 py-2.5 rounded-lg bg-green-700 text-white hover:bg-green-800 transition-colors font-medium">✓ Save As</button>`;
    }
  }

  function downloadTempFile() {
    if (!currentTempToken) return;
    window.location.href = API.listFolder.replace('/list-folder', '/download-pending/' + currentTempToken);
  }
  async function cancelTempUpload() {
    if (!currentTempToken) { closePreviewModal(); return; }
    const _nextInQueue = currentTempMetadata?._onComplete || null;
    try {
      const fd = new FormData();
      fd.append(CSRF_NAME, CSRF_TOKEN);
      fd.append('token', currentTempToken);
      await fetch(API.listFolder.replace('/list-folder', '/cancel-pending'), { method: 'POST', body: fd });
    } catch (e) { console.error('Error cancelling upload:', e); }
    finally {
      currentTempToken    = null;
      currentTempMetadata = null;
      closePreviewModal();

      // If there are more files queued, the user intentionally cancelled —
      // stop the queue and hide the tray instead of continuing to the next file.
      if (typeof _nextInQueue === 'function') {
        if (window.UploadTray) window.UploadTray.cancelAll();
      }
    }
  }



  // =========================================================================
  // FIX #1 (continued): Folder Browser — now backed by unified index
  // invalidateFolderBrowserIndex → invalidateUnifiedIndex
  // =========================================================================
  let _folderBrowserFilesCache    = {};
  let _folderBrowserSearchDebounce = null;
  let _folderBrowserSearchActive  = false;
  let _folderBrowserSaveMode      = 'record';

  // Shim: keep old name working so callers don't break
  function invalidateFolderBrowserIndex() { invalidateUnifiedIndex(); }

  async function openFolderBrowserModal() {
    document.getElementById('previewModal')?.classList.add('hidden');
    if (!document.getElementById('folderBrowserModal')) createFolderBrowserModal();
    _folderBrowserSearchActive = false;
    _folderBrowserSaveMode     = 'record';
    folderBreadcrumbStack      = [{ label: 'Academic Records', path: '' }];
    selectedFolderPath         = '';
    const titleEl    = document.querySelector('#folderBrowserModal h3');
    const subtitleEl = document.querySelector('#folderBrowserModal p.text-sm.text-gray-500');
    if (titleEl)    titleEl.textContent    = 'Select Destination Folder';
    if (subtitleEl) subtitleEl.textContent = 'Choose where to save this file';
    document.getElementById('folderBrowserModal').classList.remove('hidden');
    loadFolderBrowser('');
  }

  function createFolderBrowserModal() {
    const modal = document.createElement('div');
    modal.id = 'folderBrowserModal';
    modal.className = 'hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4';
    modal.innerHTML = `
      <div class="bg-white rounded-2xl w-full max-w-2xl max-h-[90vh] flex flex-col shadow-2xl">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
          <div>
            <h3 class="text-xl font-bold text-gray-800">Select Destination Folder</h3>
            <p class="text-sm text-gray-500 mt-1">Choose where to save this file</p>
          </div>
          <button onclick="closeFolderBrowserModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>
        <div class="px-6 pt-4">
          <nav id="folderBreadcrumb" class="flex items-center flex-wrap gap-1 text-sm text-gray-600"></nav>
        </div>
        <div class="px-6 pt-3 pb-3">
          <div class="relative">
            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <input type="text" id="folderSearchInput" placeholder="Search folders..."
                   class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                   oninput="filterFoldersInBrowser()">
          </div>
        </div>
        <div class="flex-1 overflow-y-auto px-6 pb-3">
          <div id="folderBrowserList" class="border border-gray-200 rounded-lg divide-y divide-gray-100 min-h-[200px]"></div>
        </div>
        <div id="folderBrowserPathPreviewWrap" class="hidden px-6 pb-0 pt-2">
          <div class="flex items-start gap-2 px-3 py-2.5 bg-green-50 border border-green-200 rounded-lg">
            <svg class="w-4 h-4 text-green-700 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/></svg>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-semibold text-green-800 mb-0.5">Selected destination:</p>
              <p id="folderBrowserSelectedPath" class="text-xs text-green-700 break-all"></p>
            </div>
          </div>
        </div>
        <div class="flex items-center gap-3 px-6 py-4 border-t border-gray-200 bg-gray-50">
          <button onclick="closeFolderBrowserModal()" class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg hover:bg-gray-100 font-medium text-gray-700 transition-colors">Cancel</button>
          <button onclick="_folderBrowserSaveMode === 'folder' ? finalizeUploadFolderToFolder() : finalizeUploadToFolder()"
                  class="flex-1 px-4 py-2.5 bg-green-700 text-white rounded-lg hover:bg-green-800 font-medium transition-colors">Save to This Folder</button>
        </div>
      </div>`;
    document.body.appendChild(modal);
  }

  async function loadFolderBrowser(path) {
    folderBrowserPath = path;
    const listEl = document.getElementById('folderBrowserList');
    if (!listEl) return;
    listEl.innerHTML = '<div class="p-8 text-center text-gray-400">Loading folders…</div>';

    // FIX #1: uses unified index — no separate crawler
    const [index, listData] = await Promise.all([
      getFolderEntries(),
      apiFetch(API.listFolder + '?path=' + encodeURIComponent(path)).catch(() => ({}))
    ]);
    _folderBrowserFilesCache[path] = listData.files || [];

    const searchInput = document.getElementById('folderSearchInput');
    const q = searchInput ? searchInput.value.trim() : '';
    if (q) {
      renderFolderBrowserResults(q);
    } else {
      renderFolderBrowserTree(path, index);
    }
    renderFolderBreadcrumb();
  }

  function renderFolderBrowserTree(path, index) {
    const listEl = document.getElementById('folderBrowserList');
    if (!listEl) return;
    listEl.innerHTML = '';
    const children = index.filter(f => f.parentPath === path);
    if (children.length === 0) {
      listEl.innerHTML = '<div class="p-8 text-center text-gray-400">No subfolders here</div>';
      return;
    }
    const frag = document.createDocumentFragment();
    children.forEach(folder => {
      const div = document.createElement('div');
      div.className = 'folder-browser-item p-4 hover:bg-gray-50 cursor-pointer transition-colors flex items-center gap-3 border-l-4 border-transparent';
      div.dataset.folderName = folder.name;
      div.dataset.folderPath = folder.path;
      div.innerHTML = `
        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
          <svg class="w-6 h-6 text-green-700" fill="currentColor" viewBox="0 0 24 24"><path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/></svg>
        </div>
        <div class="flex-1 min-w-0">
          <p class="font-medium text-gray-800 truncate">${folder.name}</p>
          <p class="text-xs text-gray-500">${folder.count ?? 0} file${(folder.count ?? 0) !== 1 ? 's' : ''}</p>
        </div>
        <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>`;
      div.onclick = () => {
        selectFolderBrowserItem(div, folder.path);
        navigateToFolder(folder.path, folder.name);
      };
      frag.appendChild(div);
    });
    listEl.appendChild(frag);
  }

  function renderFolderBrowserResults(q) {
    const listEl = document.getElementById('folderBrowserList');
    if (!listEl || !_unifiedIndex) return;
    listEl.innerHTML = '';
    const lower   = q.toLowerCase().trim();
    // FIX #1: filter from unified index — folder entries only
    const results = lower
      ? _unifiedIndex.filter(f => f.kind === 'folder' && f.name.toLowerCase().includes(lower))
      : _unifiedIndex.filter(f => f.kind === 'folder');
    if (results.length === 0) {
      listEl.innerHTML = `<div class="p-8 text-center text-gray-400">No folders match <strong>${q}</strong></div>`;
      return;
    }
    const frag = document.createDocumentFragment();
    results.forEach(folder => {
      let displayName = folder.name;
      if (lower) {
        const idx = folder.name.toLowerCase().indexOf(lower);
        if (idx !== -1) {
          displayName =
            folder.name.slice(0, idx) +
            '<span class="bg-yellow-200 text-gray-900 rounded px-0.5 font-semibold">' +
            folder.name.slice(idx, idx + q.length) + '</span>' +
            folder.name.slice(idx + q.length);
        }
      }
      const isSelected = selectedFolderPath === folder.path;
      const div = document.createElement('div');
      div.className = 'folder-browser-item px-4 py-3 hover:bg-gray-50 cursor-pointer transition-colors flex items-center gap-3 border-l-4 ' +
        (isSelected ? 'bg-green-50 border-green-700' : 'border-transparent');
      div.dataset.folderName = folder.name;
      div.dataset.folderPath = folder.path;
      div.innerHTML = `
        <div class="w-9 h-9 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
          <svg class="w-5 h-5 text-green-700" fill="currentColor" viewBox="0 0 24 24"><path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/></svg>
        </div>
        <div class="flex-1 min-w-0">
          <p class="font-medium text-gray-800 text-sm truncate">${displayName}</p>
          <p class="text-xs text-gray-400 truncate mt-0.5">${folder.locationLabel}</p>
        </div>`;
      div.onclick = () => selectFolderBrowserItem(div, folder.path, folder.name, folder.locationLabel);
      frag.appendChild(div);
    });
    listEl.appendChild(frag);
  }

  function selectFolderBrowserItem(el, path, name, locationLabel) {
    document.querySelectorAll('.folder-browser-item').forEach(i => {
      i.classList.remove('bg-green-50', 'border-green-700');
      i.classList.add('border-transparent');
    });
    el.classList.add('bg-green-50', 'border-green-700');
    el.classList.remove('border-transparent');
    selectedFolderPath = path;
    const wrap   = document.getElementById('folderBrowserPathPreviewWrap');
    const pathEl = document.getElementById('folderBrowserSelectedPath');
    if (wrap && pathEl && locationLabel) {
      pathEl.textContent = locationLabel + ' > ' + (name || path.split('/').pop());
      wrap.classList.remove('hidden');
    }
  }

  function navigateToFolder(path, label) {
    const existing = folderBreadcrumbStack.findIndex(item => item.path === path);
    if (existing !== -1) {
      folderBreadcrumbStack = folderBreadcrumbStack.slice(0, existing + 1);
    } else {
      folderBreadcrumbStack.push({ label, path });
    }
    folderBrowserPath = path;
    // FIX #1: index already built — render instantly, no extra fetch
    if (_unifiedIndex) {
      renderFolderBrowserTree(path, _unifiedIndex.filter(e => e.kind === 'folder'));
      renderFolderBreadcrumb();
      if (!_folderBrowserFilesCache[path]) {
        apiFetch(API.listFolder + '?path=' + encodeURIComponent(path))
          .then(d => { _folderBrowserFilesCache[path] = d.files || []; })
          .catch(() => {});
      }
    } else {
      loadFolderBrowser(path);
    }
  }

  function renderFolderBreadcrumb() {
    const nav = document.getElementById('folderBreadcrumb');
    if (!nav) return;
    nav.innerHTML = '';
    folderBreadcrumbStack.forEach((crumb, index) => {
      if (index > 0) {
        nav.insertAdjacentHTML('beforeend', `<svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>`);
      }
      const isLast = index === folderBreadcrumbStack.length - 1;
      if (isLast) {
        nav.insertAdjacentHTML('beforeend', `<span class="font-semibold text-green-700">${crumb.label}</span>`);
      } else {
        const btn = document.createElement('button');
        btn.className = 'hover:text-green-700 transition-colors';
        btn.textContent = crumb.label;
        btn.onclick = () => navigateToFolder(crumb.path, crumb.label);
        nav.appendChild(btn);
      }
    });
    selectedFolderPath = folderBrowserPath;
  }

  function filterFoldersInBrowser() {
    clearTimeout(_folderBrowserSearchDebounce);
    // FIX #6: 150 ms debounce in folder browser also (was no debounce on keydown)
    _folderBrowserSearchDebounce = setTimeout(() => {
      const q = document.getElementById('folderSearchInput').value.trim();
      if (q === '') {
        _folderBrowserSearchActive = false;
        renderFolderBreadcrumb();
        renderFolderBrowserTree(folderBrowserPath, (_unifiedIndex || []).filter(e => e.kind === 'folder'));
        const wrap = document.getElementById('folderBrowserPathPreviewWrap');
        if (wrap) wrap.classList.add('hidden');
      } else {
        _folderBrowserSearchActive = true;
        renderFolderBrowserResults(q);
      }
    }, 150);
  }

  function closeFolderBrowserModal() {
    document.getElementById('folderBrowserModal')?.classList.add('hidden');
    folderBreadcrumbStack      = [{ label: 'Academic Records', path: '' }];
    selectedFolderPath         = '';
    _folderBrowserSearchActive = false;
    _folderBrowserSaveMode     = 'record';
    _folderBrowserFilesCache   = {};
    const inp = document.getElementById('folderSearchInput');
    if (inp) inp.value = '';
    const wrap = document.getElementById('folderBrowserPathPreviewWrap');
    if (wrap) wrap.classList.add('hidden');
  }

  // ─── Finalize Upload ──────────────────────────────────────────────────────
 async function finalizeUploadToFolder() {
    if (!currentTempToken) { showDialog('No upload in progress.', 'warning'); return; }
    // Duplicate check is handled server-side (409 response).
    // No JS pre-check needed — server is the source of truth.
    _doFinalizeUploadToFolder('');
  }

  async function _doFinalizeUploadToFolder(duplicateAction = '') {
    if (!currentTempToken) return;
    const formData = new FormData();
    formData.append(CSRF_NAME, CSRF_TOKEN);
    formData.append('token', currentTempToken);
    formData.append('folder_path', selectedFolderPath);
    if (currentTempMetadata?.suggested_filename)
      formData.append('suggested_filename', currentTempMetadata.suggested_filename);
    if (duplicateAction)
      formData.append('duplicate_action', duplicateAction);

    const btn = document.querySelector('#folderBrowserModal button[onclick*="finalizeUpload"]');
    const originalText = btn ? btn.textContent.trim() : 'Save to This Folder';
    if (btn) {
      btn.disabled = true;
      btn.innerHTML = `<svg class="animate-spin h-5 w-5 mr-2 inline-block" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Saving...`;
    }
    const resetBtn = () => { if (btn) { btn.disabled = false; btn.textContent = originalText; } };

    try {
      const response = await fetch(API.listFolder.replace('/list-folder', '/finalize-upload'), { method: 'POST', body: formData });

      // 409 = server detected a duplicate — show the 3-option modal
      if (response.status === 409) {
        resetBtn();
        const conflict = await response.json();
        const dupName  = conflict.duplicate_name || currentTempMetadata?.original_name || 'this file';
        showDuplicateConfirm(
          dupName,
          () => _doFinalizeUploadToFolder('replace'),    // Replace existing
          () => { resetBtn(); },                          // Cancel — keep temp, do nothing
          () => _doFinalizeUploadToFolder('keep_both')   // Keep Both — auto-rename
        );
        return;
      }

      const data = await response.json();
      if (!data.success) { showDialog('Failed to save: ' + data.message, 'error'); resetBtn(); return; }

      // ── Capture everything we need BEFORE closing any modal ──────────────
      // closeFolderBrowserModal() zeroes out selectedFolderPath, so capture first.
      const _savedDest    = selectedFolderPath;
      const _destParent   = _savedDest.includes('/')
        ? _savedDest.slice(0, _savedDest.lastIndexOf('/'))
        : '';
      const _nextInQueue  = currentTempMetadata?._onComplete || null;
      const _trayId       = currentTempMetadata?._trayId || null;

      // ── Close modals & clear state ────────────────────────────────────────
      resetBtn();
      closeFolderBrowserModal();
      closePreviewModal();

      // ── Tray: mark done ───────────────────────────────────────────────────
      if (window.UploadTray && _trayId) {
        window.UploadTray.setDone(_trayId);
      }

      currentTempToken    = null;
      currentTempMetadata = null;

      // ── Show success message ──────────────────────────────────────────────
      showDialog(duplicateAction === 'replace' ? 'File replaced successfully.' : (data.message || 'Record saved successfully.'), 'success');

      // ── Navigate INTO the destination folder so the user sees the file ────
      // Only navigate if the destination differs from where the user currently is.
      if (_savedDest !== currentFolderPath) {
        // Push it onto the breadcrumb stack so the user can navigate back naturally
        const destLabel = _savedDest.split('/').pop() || 'Academic Records';
        breadcrumbStack.push({ label: destLabel, folderPath: _savedDest, isHome: false });
        renderBreadcrumb();
        _htSetSelected(_savedDest);
        _htExpandToPath(_savedDest);
      }

      // ── Bust caches for destination, its parent, and previously viewed folder
      invalidateFolderCache(_savedDest);
      invalidateFolderCache(_destParent);
      invalidateFolderCache(currentFolderPath);

      delete _htCache[_savedDest];
      delete _htCache[_destParent];
      delete _htCache[currentFolderPath];

      // ── Reload: always load the destination so the new file appears ───────
      await loadFolder(_savedDest);

      // ── Refresh tree ──────────────────────────────────────────────────────
      await _htRefreshPath(_savedDest);
      if (_destParent && _destParent !== _savedDest) {
        await _htRefreshPath(_destParent);
      }

      // ── Patch file-count badges ───────────────────────────────────────────
      _htPatchBadgeFromCache(_savedDest);
      if (_destParent) _htPatchBadgeFromCache(_destParent);

      // ── Trigger next queued file (drag-drop multi-upload) ─────────────────
      if (typeof _nextInQueue === 'function') _nextInQueue();

    } catch (error) {
      showDialog('Failed to save file. Please try again.', 'error');
      resetBtn();
    }
  }
  // ─── Upload Folder ────────────────────────────────────────────────────────
  const UPLOAD_FOLDER_BTN_LABEL = 'Upload Folder';
  let _pendingFolderFiles = [];
  let _pendingFolderName  = '';

  function _resetUploadFolderModal() {
    const submitBtn   = document.getElementById('uploadFolderSubmitBtn');
    const form        = document.getElementById('uploadFolderForm');
    const progress    = form?.querySelector('.upload-folder-progress-container');
    const list        = document.getElementById('folderFileList');
    const nameInput   = document.getElementById('uploadFolderName');
    if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = UPLOAD_FOLDER_BTN_LABEL; }
    if (progress)  progress.remove();
    if (list)      { list.innerHTML = ''; list.classList.add('hidden'); }
    if (nameInput) nameInput.value = '';
    const ffi = document.getElementById('folderFilesInput');
    if (ffi) ffi.value = '';
    _pendingFolderFiles = [];
    _pendingFolderName  = '';
  }

  function openUploadFolderModal() {
    if (!PRIV_UPLOAD) { denyAction(); return; }
    _resetUploadFolderModal();
    const _ffi = document.getElementById('folderFilesInput');
    if (_ffi) {
      _ffi.setAttribute('webkitdirectory', '');
      _ffi.setAttribute('mozdirectory', '');
      _ffi.removeAttribute('accept');
      _ffi.onchange = function() { handleFolderDirSelect(this); };
    }
    const form = document.getElementById('uploadFolderForm');
    if (form && !document.getElementById('uploadFolderName')) {
      const dropZoneWrap = document.getElementById('folderDropZone')?.closest('div.mb-4');
      const nameWrap = document.createElement('div');
      nameWrap.id = 'uploadFolderNameWrap';
      nameWrap.className = 'mb-4';
      nameWrap.innerHTML = `
        <label class="block text-sm font-medium text-gray-700 mb-2">Folder Name <span class="text-red-500">*</span></label>
        <input type="text" id="uploadFolderName" name="upload_folder_name" placeholder="Auto-filled from selected folder"
          class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500"/>`;
      if (dropZoneWrap) form.insertBefore(nameWrap, dropZoneWrap);
      else form.insertBefore(nameWrap, form.firstChild);
    }
    const form2 = document.getElementById('uploadFolderForm');
    if (form2) {
      const submitBtn = form2.querySelector('button[type="submit"]');
      if (submitBtn && !submitBtn.id) submitBtn.id = 'uploadFolderSubmitBtn';
    }
    document.getElementById('uploadFolderModal').classList.remove('hidden');
    setTimeout(() => document.getElementById('uploadFolderName')?.focus(), 50);
  }

  function closeUploadFolderModal() {
    document.getElementById('uploadFolderModal').classList.add('hidden');
    document.getElementById('uploadFolderForm')?.reset();
    _resetUploadFolderModal();
  }

  function updateFolderFileList(input) {
    const list = document.getElementById('folderFileList');
    list.innerHTML = '';
    _pendingFolderFiles = Array.from(input.files || []);
    if (_pendingFolderFiles.length === 0) { list.classList.add('hidden'); return; }
    list.classList.remove('hidden');
    const frag = document.createDocumentFragment();
    _pendingFolderFiles.forEach(file => {
      const sizeMB = (file.size / 1048576).toFixed(2);
      const li = document.createElement('li');
      li.className = 'flex items-center gap-2 text-sm text-gray-700 bg-gray-50 rounded-lg px-3 py-2';
      li.innerHTML = `
        <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <span class="flex-1 truncate">${file.name}</span>
        <span class="text-gray-400 flex-shrink-0">${sizeMB} MB</span>`;
      frag.appendChild(li);
    });
    list.appendChild(frag);
  }

  document.getElementById('uploadFolderForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    if (!PRIV_UPLOAD) { denyAction(); return; }
    const submitBtn = document.getElementById('uploadFolderSubmitBtn');
    if (submitBtn?.disabled) return;
    const folderNameEl = document.getElementById('uploadFolderName');
    const folderName   = (folderNameEl?.value || _pendingFolderName || '').trim();
    if (!folderName) { showDialog('Please select a folder first.', 'warning'); return; }
    const filesInput    = document.getElementById('folderFilesInput');
    _pendingFolderFiles = _pendingFolderFiles.length > 0 ? _pendingFolderFiles : Array.from(filesInput?.files || []);
    _pendingFolderName  = folderName;
    if (folderNameEl) folderNameEl.value = folderName;
    const fileCount = _pendingFolderFiles.length;
    const fileLabel = fileCount === 1 ? '1 file' : fileCount > 1 ? `${fileCount} files` : 'all files (empty folder)';
    _showFolderUploadConfirm(folderName, fileLabel, () => {
      const savedName  = folderName;
      const savedFiles = _pendingFolderFiles.slice();
      closeUploadFolderModal();
      _pendingFolderName  = savedName;
      _pendingFolderFiles = savedFiles;
      openFolderBrowserModalForFolder();
    });
  });

  function _showFolderUploadConfirm(folderName, fileLabel, onConfirm) {
    document.getElementById('_folderUploadConfirm')?.remove();
    const overlay = document.createElement('div');
    overlay.id = '_folderUploadConfirm';
    overlay.className = 'fixed inset-0 z-[9999] flex items-center justify-center p-4';
    overlay.style.backgroundColor = 'rgba(0,0,0,0.45)';
    overlay.innerHTML = `
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden">
        <div class="flex flex-col items-center px-8 py-7 bg-yellow-50 border-b border-yellow-200">
          <div class="w-14 h-14 bg-yellow-100 rounded-full flex items-center justify-center mb-4">
            <svg class="w-7 h-7 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11v5m-2.5-2.5L12 11l2.5 2.5"/>
            </svg>
          </div>
          <p class="text-lg font-bold text-gray-800 text-center mb-1">Upload Folder?</p>
          <p class="text-sm text-gray-600 text-center leading-relaxed">This will upload <span class="font-semibold text-gray-800">${fileLabel}</span> from the folder</p>
          <p class="mt-1 text-sm font-semibold text-yellow-700 text-center break-all px-2">"${folderName}"</p>
          <p class="mt-3 text-xs text-gray-400 text-center">You will choose the destination in the next step.</p>
        </div>
        <div class="flex gap-3 px-6 py-4 bg-white">
          <button id="_folderUploadCancel" class="flex-1 px-4 py-2.5 border border-gray-300 rounded-xl hover:bg-gray-50 font-medium text-gray-700 transition-colors text-sm">Cancel</button>
          <button id="_folderUploadConfirmBtn" class="flex-1 px-4 py-2.5 bg-green-700 hover:bg-green-800 text-white rounded-xl font-semibold transition-colors text-sm flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
            Upload
          </button>
        </div>
      </div>`;
    document.body.appendChild(overlay);
    const close = () => overlay.remove();
    document.getElementById('_folderUploadConfirmBtn').addEventListener('click', () => { close(); onConfirm(); });
    document.getElementById('_folderUploadCancel').addEventListener('click', close);
    overlay.addEventListener('click', e => { if (e.target === overlay) close(); });
    document.addEventListener('keydown', function esc(e) {
      if (e.key === 'Escape') { close(); document.removeEventListener('keydown', esc); }
    });
  }

  function openFolderBrowserModalForFolder() {
    if (!document.getElementById('folderBrowserModal')) createFolderBrowserModal();
    _folderBrowserSaveMode     = 'folder';
    _folderBrowserSearchActive = false;
    folderBreadcrumbStack      = [{ label: 'Academic Records', path: '' }];
    selectedFolderPath         = '';
    const wrap = document.getElementById('folderBrowserPathPreviewWrap');
    if (wrap) wrap.classList.add('hidden');
    document.getElementById('folderBrowserModal').classList.remove('hidden');
    if (_unifiedIndex) {
      renderFolderBrowserTree('', _unifiedIndex.filter(e => e.kind === 'folder'));
      renderFolderBreadcrumb();
    } else {
      loadFolderBrowser('');
    }
    const titleEl    = document.querySelector('#folderBrowserModal h3');
    const subtitleEl = document.querySelector('#folderBrowserModal p.text-sm.text-gray-500');
    if (titleEl)    titleEl.textContent    = 'Select Destination Folder';
    if (subtitleEl) subtitleEl.textContent = 'Choose where to create "' + _pendingFolderName + '"';
  }

  async function finalizeUploadFolderToFolder() {
    if (!_pendingFolderName) { showDialog('No folder name found. Please try again.', 'warning'); return; }
    const cachedFolders = (_unifiedIndex || []).filter(f => f.kind === 'folder' && f.parentPath === selectedFolderPath);
    const duplicateFolder = cachedFolders.find(f => (f.name || '').toLowerCase() === _pendingFolderName.toLowerCase());
    if (duplicateFolder) {
      showDuplicateConfirm(_pendingFolderName,
        () => _doFinalizeUploadFolderToFolder(true),
        null
      );
      return;
    }
    _doFinalizeUploadFolderToFolder(false);
  }

  async function _doFinalizeUploadFolderToFolder(skipRootCreation = false) {
    if (!_pendingFolderName) return;
    const btn = document.querySelector('#folderBrowserModal button[onclick*="finalizeUpload"]');
    const originalText = btn ? btn.textContent.trim() : 'Save to This Folder';
    const setLoading = (label) => {
      if (!btn) return;
      btn.disabled = true;
      btn.innerHTML = `<svg class="animate-spin h-5 w-5 mr-2 inline-block" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>${label || 'Saving...'}`;
    };
    const resetBtn = () => { if (btn) { btn.disabled = false; btn.textContent = originalText; } };
    setLoading('Saving...');

    // ── Show upload tray for folder upload (same UX as file uploads) ──────────
    const allFiles  = _pendingFolderFiles;
    const rootName  = _pendingFolderName;
    let _trayIds    = [];
    if (window.UploadTray && allFiles.length > 0) {
      _trayIds = window.UploadTray.initBatch(allFiles);
      window.UploadTray.resetCancelled();
    }

     try {
      const destPrefix = selectedFolderPath ? selectedFolderPath + '/' : '';
      // FIX BUG 1: When skipRootCreation is true the root folder already exists
      // at selectedFolderPath (the user navigated INTO it in the folder browser).
      // Use selectedFolderPath directly as rootPath to avoid the double-nesting
      // "dummy records/dummy records" problem.
      const rootPath = skipRootCreation
        ? selectedFolderPath
        : destPrefix + rootName;

      // ── Step 1: create root folder ─────────────────────────────────────────
      if (!skipRootCreation) {
        const createData = await apiFetch(API.createFolder, 'POST', { parent_path: selectedFolderPath, folder_name: rootName });
        if (!createData.success) { showDialog('Could not create folder: ' + createData.message, 'error'); resetBtn(); return; }
      }


      // ── Step 2: create sub-directories ────────────────────────────────────
      const subDirSet = new Set();
      allFiles.forEach(f => {
        const parts = (f.webkitRelativePath || f.name).split('/');
        for (let d = 2; d < parts.length; d++) subDirSet.add(parts.slice(1, d).join('/'));
      });
      const subDirs = [...subDirSet].sort((a, b) => a.split('/').length - b.split('/').length);
      for (const rel of subDirs) {
        const parts      = rel.split('/');
        const folderName = parts[parts.length - 1];
        const parentPath = rootPath + (parts.length > 1 ? '/' + parts.slice(0, -1).join('/') : '');
        await apiFetch(API.createFolder, 'POST', { parent_path: parentPath, folder_name: folderName });
      }

      // ── Step 3: upload files ───────────────────────────────────────────────
      if (allFiles.length > 0) {
        const byFolder = new Map();
        allFiles.forEach(f => {
          const parts    = (f.webkitRelativePath || f.name).split('/');
          const dirParts = parts.slice(1, -1);
          const target   = rootPath + (dirParts.length ? '/' + dirParts.join('/') : '');
          if (!byFolder.has(target)) byFolder.set(target, []);
          byFolder.get(target).push(f);
        });
        let done = 0;
        let trayIdx = 0;
        const uploadErrors = [];
        for (const [folderPath, files] of byFolder) {
          setLoading('Uploading ' + done + ' / ' + allFiles.length + '...');
          const fd = new FormData();
          fd.append(CSRF_NAME, CSRF_TOKEN);
          fd.append('folder_path', folderPath);
          files.forEach(f => fd.append('folder_files[]', f));

          // Mark tray rows as "uploading" for this batch
          if (window.UploadTray) {
            files.forEach((_, i) => {
              const tid = _trayIds[trayIdx + i];
              if (tid) window.UploadTray.setReviewing(tid);
            });
          }

          try {
            const res  = await fetch(API.uploadFolder, { method: 'POST', body: fd });
            const data = await res.json();
            if (!data.success) {
              uploadErrors.push(data.message);
              // Mark batch as errored in tray
              if (window.UploadTray) {
                files.forEach((_, i) => {
                  const tid = _trayIds[trayIdx + i];
                  if (tid) window.UploadTray.setError(tid, data.message || 'Failed');
                });
              }
            } else {
              if (data.errors?.length) uploadErrors.push(...data.errors);
              // Mark batch as done in tray
              if (window.UploadTray) {
                files.forEach((_, i) => {
                  const tid = _trayIds[trayIdx + i];
                  if (tid) window.UploadTray.setDone(tid);
                });
              }
            }
          } catch(e) {
            uploadErrors.push(folderPath + ': upload failed');
            if (window.UploadTray) {
              files.forEach((_, i) => {
                const tid = _trayIds[trayIdx + i];
                if (tid) window.UploadTray.setError(tid, 'Upload failed');
              });
            }
          }
          done    += files.length;
          trayIdx += files.length;
        }
        if (uploadErrors.length) showDialog('Some files were skipped: ' + uploadErrors.slice(0, 3).join(', '), 'warning');
      }

      // ── Step 4: close modal and update UI ─────────────────────────────────
      // Capture destination BEFORE closeFolderBrowserModal() zeroes selectedFolderPath
      const _savedDest        = selectedFolderPath;
      // FIX BUG 1: rootPath is already correctly set above (handles skipRootCreation).
      // Use it directly instead of rebuilding the path (which caused the duplicate).
      const _uploadedRootPath = rootPath;



      resetBtn();
      closeFolderBrowserModal();

      // ── Show success — NOT an error — this is the correct success path ─────
      showDialog(skipRootCreation ? 'Folder replaced successfully.' : 'Folder uploaded successfully.', 'success');

      _pendingFolderFiles    = [];
      _pendingFolderName     = '';
      _folderBrowserSaveMode = 'record';

       // ── Bust ALL relevant caches so loadFolder fetches fresh data ──────────
      invalidateFolderCache(_savedDest);
      invalidateFolderCache(_uploadedRootPath);
      invalidateFolderCache(currentFolderPath);
      invalidateUnifiedIndex();
      delete _htCache[_savedDest];
      delete _htCache[_uploadedRootPath];
      delete _htCache[currentFolderPath];

     // ── Bust cache for every subfolder created during upload ───────────────
      // subDirs contains relative paths like "SubA" or "SubA/SubB".
      // Convert each to an absolute path and invalidate both folderCache and
      // _htCache so no level ever returns stale empty data.
      subDirs.forEach(rel => {
        const absPath = _uploadedRootPath + '/' + rel;
        invalidateFolderCache(absPath);
        delete _htCache[absPath];
        // Also invalidate the parent of each subfolder so that parent's
        // child-list is refetched and includes the new nested directories.
        const parentOfSub = absPath.includes('/')
          ? absPath.slice(0, absPath.lastIndexOf('/'))
          : '';
        if (parentOfSub && parentOfSub !== _uploadedRootPath) {
          invalidateFolderCache(parentOfSub);
          delete _htCache[parentOfSub];
        }
      });
      // Finally, force a full unified index rebuild so search and folder browser
      // reflect all new paths immediately.
      invalidateUnifiedIndex();



     // ── Navigate into the newly-created folder and refresh the tree ────────
      breadcrumbStack.push({ label: rootName, folderPath: _uploadedRootPath, isHome: false });
      renderBreadcrumb();

      // Step A: Refresh the parent level so the new root folder node appears
      // in the storage tree immediately. We await this so the DOM is updated
      // before we try to expand into it.
      await _htRefreshPath(_savedDest);

     // Step B: If the uploaded folder contains subfolders, refresh root level
      // AND each intermediate subfolder level so the full hierarchy is visible.
      if (subDirs.length > 0) {
        await _htRefreshLevel(_uploadedRootPath);
        // Refresh each subfolder level in depth order so parent nodes exist
        // in the DOM before their children are appended.
        const sortedDirs = [...subDirs].sort((a, b) => a.split('/').length - b.split('/').length);
        for (const rel of sortedDirs) {
          const absPath = _uploadedRootPath + '/' + rel;
          // Only refresh levels that have children (i.e. not leaf directories)
          const hasChildren = sortedDirs.some(d => d !== rel && d.startsWith(rel + '/'));
          if (hasChildren) {
            delete _htCache[absPath];
            await _htRefreshLevel(absPath);
          }
        }
      }

      // Flush microtask queue so all freshly appended <li> nodes are queryable
      await new Promise(r => setTimeout(r, 0));

      // Step C: Expand the full path in the tree and highlight the new folder
      await _htExpandToPath(_uploadedRootPath);
      _htSetSelected(_uploadedRootPath);

      // Step D: Load the main content panel (fire-and-forget — runs in parallel
      // with the tree highlight above since the tree DOM is already updated)
      loadFolder(_uploadedRootPath);


      if (_savedDest && _savedDest !== _uploadedRootPath) {
        _htPatchBadgeFromCache(_savedDest);
      }



    } catch (err) {
      showDialog('Failed to save. Please try again.', 'error');
      resetBtn();
    }
  }


  function updateRecordFileLabel(input) {
    const label = document.getElementById('recordFileName');
    if (!label) return;
    if (input.files.length > 0) {
      const file   = input.files[0];
      const sizeMB = (file.size / 1024 / 1024).toFixed(2);
      label.classList.remove('hidden');
      if (file.size > 5 * 1024 * 1024) {
        label.innerHTML = `
          <div class="flex items-start gap-2 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
            <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <div class="flex-1">
              <p class="text-sm font-semibold text-yellow-800">Large File Detected</p>
              <p class="text-xs text-yellow-700 mt-1">${file.name} (${sizeMB} MB) - Upload may take time on slow connections</p>
            </div>
          </div>`;
      } else {
        label.innerHTML = `
          <div class="flex items-center gap-2 text-sm text-gray-700">
            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span class="font-medium">${file.name}</span>
            <span class="text-gray-500">(${sizeMB} MB)</span>
          </div>`;
      }
    } else {
      label.classList.add('hidden');
    }
  }

  function handleRecordDrop(event) {
    event.preventDefault();
    event.stopPropagation(); // prevent global drop handler from also firing
    const input = document.getElementById('recordFileInput');
    const zone  = document.getElementById('recordDropZone');
    zone.classList.remove('border-green-600', 'bg-green-50');
    const files = event.dataTransfer.files;
    if (files.length) {
      // Only accept first file; validate extension before assigning
      const file = files[0];
      const ext  = file.name.split('.').pop().toLowerCase();
      if (!['pdf','doc','docx','jpg','jpeg','png'].includes(ext)) {
        showDialog('File type not allowed. Accepted: PDF, DOC, DOCX, JPG, PNG', 'warning');
        return;
      }
      const dt = new DataTransfer();
      dt.items.add(file);
      input.files = dt.files;
      updateRecordFileLabel(input);
    }
  }

  function handleFolderDirSelect(input) {
    const allFiles = Array.from(input.files || []);
    let dirName = '';
    if (allFiles.length > 0) {
      dirName = (allFiles[0].webkitRelativePath || '').split('/')[0] || '';
    }
    if (!dirName && input.value) {
      dirName = input.value.split(/[\\\/]/).filter(Boolean).pop() || '';
    }
    if (dirName) {
      _pendingFolderName = dirName;
      const nameInput = document.getElementById('uploadFolderName');
      if (nameInput) nameInput.value = dirName;
    }
    _pendingFolderFiles = allFiles;
    updateFolderFileList(input);
  }

  function handleFolderDrop(event) {
    event.preventDefault();
    const zone = document.getElementById('folderDropZone');
    zone.classList.remove('border-green-600', 'bg-green-50');
    const items   = event.dataTransfer.items;
    if (!items || items.length === 0) return;
    const entries = Array.from(items).map(item => item.webkitGetAsEntry ? item.webkitGetAsEntry() : null).filter(Boolean);
    if (entries.length === 0) return;
    async function traverseEntry(entry, pathPrefix) {
      if (entry.isFile) {
        return new Promise((resolve) => {
          entry.file(file => {
            const wrappedFile = new File([file], file.name, { type: file.type, lastModified: file.lastModified });
            Object.defineProperty(wrappedFile, 'webkitRelativePath', { value: pathPrefix + file.name, writable: false });
            resolve([wrappedFile]);
          }, () => resolve([]));
        });
      } else if (entry.isDirectory) {
        return new Promise((resolve) => {
          const reader     = entry.createReader();
          const allEntries = [];
          function readAll() {
            reader.readEntries(async (results) => {
              if (results.length === 0) {
                // pathPrefix already has this directory's name from the caller.
                // Do NOT append entry.name again — pass pathPrefix directly so
                // children inherit the correct cumulative path without duplication.
                const nested = await Promise.all(allEntries.map(e => traverseEntry(e, pathPrefix)));
                resolve(nested.flat());
              } else { allEntries.push(...results); readAll(); }
            }, () => resolve([]));
          }
          readAll();
        });
      }
      return [];
    }


    Promise.all(entries.map(entry => {
      if (entry.isDirectory) return traverseEntry(entry, entry.name + '/');
      return traverseEntry(entry, '');
    })).then(results => {
      const allFiles = results.flat();
      _pendingFolderFiles = allFiles;
      const dirEntry = entries.find(e => e.isDirectory);
      if (dirEntry) {
        _pendingFolderName = dirEntry.name;
        const nameInput = document.getElementById('uploadFolderName');
        if (nameInput) nameInput.value = dirEntry.name;
      }
      const zone = document.getElementById('folderDropZone');
      if (zone) {
        const countEl = zone.querySelector('p.text-sm');
        if (countEl) countEl.textContent = allFiles.length > 0 ? `${allFiles.length} file${allFiles.length !== 1 ? 's' : ''} ready to upload` : 'Empty folder selected';
      }
      const list = document.getElementById('folderFileList');
      if (list) {
        list.innerHTML = '';
        if (allFiles.length === 0) { list.classList.add('hidden'); }
        else {
          list.classList.remove('hidden');
          const frag = document.createDocumentFragment();
          allFiles.forEach(file => {
            const sizeMB = (file.size / 1048576).toFixed(2);
            const li = document.createElement('li');
            li.className = 'flex items-center gap-2 text-sm text-gray-700 bg-gray-50 rounded-lg px-3 py-2';
            li.innerHTML = `
              <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
              <span class="flex-1 truncate">${file.webkitRelativePath || file.name}</span>
              <span class="text-gray-400 flex-shrink-0">${sizeMB} MB</span>`;
            frag.appendChild(li);
          });
          list.appendChild(frag);
        }
      }
    });
  }

  // ─── Delete ───────────────────────────────────────────────────────────────
 async function fileAction_delete(filePath, menuId) {
    if (!PRIV_DELETE_RECORD) { denyAction(); return; }
    // Show styled confirmation modal instead of browser confirm()
    showDeleteConfirmModal(filePath);
  }

  async function _doDeleteFile(filePath) {
    const data = await apiFetch(API.deleteFile, 'POST', { path: filePath });
    if (!data.success) { showDialog('Delete failed: ' + data.message, 'error'); return; }

    // Bust the folder cache so loadFolder re-fetches from the server
    invalidateFolderCache(currentFolderPath);

    // Explicitly clear the hierarchy-tree cache for this path AND its parent
    // before calling _htRefreshPath, otherwise _htLoadChildren will short-circuit
    // and return the stale cached result (file count won't decrease).
    const _htParentOfCurrent = currentFolderPath.includes('/')
      ? currentFolderPath.slice(0, currentFolderPath.lastIndexOf('/'))
      : '';
    delete _htCache[currentFolderPath];
    delete _htCache[_htParentOfCurrent];

    // Reload the main list and refresh the tree node (now guaranteed fresh)
    await loadFolder(currentFolderPath);
    await _htRefreshPath(currentFolderPath);

    // Patch the badge in the tree immediately from the freshly-loaded cache
    _htPatchBadgeFromCache(currentFolderPath);
  }


  function showDeleteConfirmModal(filePath) {
    // Remove any previous instance
    document.getElementById('_deleteConfirmModal')?.remove();

    const fileName = filePath.split('/').pop();
    const overlay  = document.createElement('div');
    overlay.id     = '_deleteConfirmModal';
    overlay.className = 'fixed inset-0 z-[9999] flex items-center justify-center p-4';
    overlay.style.backgroundColor = 'rgba(0,0,0,0.5)';

    overlay.innerHTML = `
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden">
        <div class="flex flex-col items-center px-8 py-7 bg-red-50 border-b border-red-200">
          <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mb-4">
            <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5
                   4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
          </div>
          <p class="text-lg font-bold text-gray-900 text-center mb-1">
            Are you sure you want to delete this file?
          </p>
          <p class="text-sm text-gray-500 text-center break-all px-2">"${fileName}"</p>
          <p class="text-xs text-red-600 mt-2 text-center">This action cannot be undone.</p>
        </div>
        <div class="flex gap-3 px-6 py-4 bg-white">
          <button id="_deleteConfirmCancelBtn"
                  class="flex-1 px-4 py-2.5 border border-gray-300 rounded-xl
                         hover:bg-gray-50 font-medium text-gray-700 transition-colors text-sm">
            Cancel
          </button>
          <button id="_deleteConfirmYesBtn"
                  class="flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white
                         font-semibold rounded-xl transition-colors text-sm">
            Yes, Delete
          </button>
        </div>
      </div>`;

    document.body.appendChild(overlay);

    const close = () => overlay.remove();

    document.getElementById('_deleteConfirmYesBtn').addEventListener('click', () => {
      close();
      _doDeleteFile(filePath);
    });
    document.getElementById('_deleteConfirmCancelBtn').addEventListener('click', close);
    overlay.addEventListener('click', e => { if (e.target === overlay) close(); });
    document.addEventListener('keydown', function escHandler(e) {
      if (e.key === 'Escape') { close(); document.removeEventListener('keydown', escHandler); }
    });
  }


 async function fileAction_deleteFolder(folderPath, menuId) {
    if (!PRIV_DELETE_FOLDER) { denyAction(); return; }
    // Show styled confirmation modal instead of native browser confirm()
    showDeleteFolderConfirmModal(folderPath);
  }

  function showDeleteFolderConfirmModal(folderPath) {
    document.getElementById('_deleteFolderConfirmModal')?.remove();

    const folderName = folderPath.split('/').pop();
    const overlay    = document.createElement('div');
    overlay.id       = '_deleteFolderConfirmModal';
    overlay.className = 'fixed inset-0 z-[9999] flex items-center justify-center p-4';
    overlay.style.backgroundColor = 'rgba(0,0,0,0.5)';

    overlay.innerHTML = `
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden">
        <div class="flex flex-col items-center px-8 py-7 bg-red-50 border-b border-red-200">
          <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mb-4">
            <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/>
            </svg>
          </div>
          <p class="text-lg font-bold text-gray-900 text-center mb-1">
            Are you sure you want to delete this folder and all its records?
          </p>
          <p class="text-sm text-gray-500 text-center break-all px-2">"${folderName}"</p>
          <p class="text-xs text-red-600 mt-2 text-center">This action cannot be undone.</p>
        </div>
        <div class="flex gap-3 px-6 py-4 bg-white">
          <button id="_deleteFolderConfirmCancelBtn"
                  class="flex-1 px-4 py-2.5 border border-gray-300 rounded-xl
                         hover:bg-gray-50 font-medium text-gray-700 transition-colors text-sm">
            Cancel
          </button>
          <button id="_deleteFolderConfirmYesBtn"
                  class="flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white
                         font-semibold rounded-xl transition-colors text-sm">
            Yes, Delete
          </button>
        </div>
      </div>`;

    document.body.appendChild(overlay);

    const close = () => overlay.remove();

    document.getElementById('_deleteFolderConfirmYesBtn').addEventListener('click', async () => {
      close();
      const data = await apiFetch(API.deleteFolder, 'POST', { path: folderPath });
      if (!data.success) { showDialog('Delete failed: ' + data.message, 'error'); return; }
      const parentPath = folderPath.includes('/') ? folderPath.slice(0, folderPath.lastIndexOf('/')) : '';
      invalidateFolderCache(currentFolderPath);
      invalidateUnifiedIndex();
      delete _htCache[folderPath];
      delete _htCache[parentPath];
      loadFolder(currentFolderPath);
      _htRefreshPath(parentPath);
    });

    document.getElementById('_deleteFolderConfirmCancelBtn').addEventListener('click', close);
    overlay.addEventListener('click', e => { if (e.target === overlay) close(); });
    document.addEventListener('keydown', function escHandler(e) {
      if (e.key === 'Escape') { close(); document.removeEventListener('keydown', escHandler); }
    });
  }


  // ─── Preview ──────────────────────────────────────────────────────────────
  let currentPreviewUrl = null;
  function fileAction_preview(filePath) {
    const modal     = document.getElementById('previewModal');
    const titleEl   = document.getElementById('previewTitle');
    const contentEl = document.getElementById('previewContent');
    titleEl.textContent = filePath.split('/').pop();
    modal.classList.remove('hidden');

    // FIX Issue 3: Reset footer to "Close only" — prevents "Save As" button
    // from a previous upload session bleeding into plain file preview.
    const previewFooter = modal.querySelector('.flex.items-center.justify-end.gap-3');
    if (previewFooter) {
      previewFooter.innerHTML = `
        <button onclick="closePreviewModal()"
                class="px-6 py-2.5 rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors font-medium text-gray-700">
          Close
        </button>`;
    }
    // Also remove any leftover OCR suggestion panel from a prior upload session
    document.getElementById('ocrSuggestionPanel')?.remove();
    const ext        = filePath.split('.').pop().toLowerCase();
    const previewUrl = API.preview   + '?path=' + encodeURIComponent(filePath);
    currentPreviewUrl = API.download + '?path=' + encodeURIComponent(filePath);
    if (['jpg', 'jpeg', 'png'].includes(ext)) {
      contentEl.innerHTML = `<img src="${previewUrl}" alt="Preview" class="max-w-full max-h-full mx-auto rounded-lg shadow-lg"/>`;
    } else if (ext === 'pdf') {
      contentEl.innerHTML = `<iframe src="${previewUrl}" class="w-full border-0 rounded-lg" style="min-height:520px;" title="PDF Preview"></iframe>`;
    } else {
      contentEl.innerHTML = `
        <div class="text-center text-gray-500 py-10">
          <svg class="w-20 h-20 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
          </svg>
          <p class="text-lg font-semibold mb-2">Preview not available</p>
          <p class="text-sm text-gray-400 mb-4">.${ext.toUpperCase()} files cannot be previewed in the browser.</p>
          <button data-action="download" data-path="${filePath}" class="px-6 py-2.5 bg-green-700 text-white rounded-lg hover:bg-green-800 transition-colors">Download to View</button>
        </div>`;
    }
  }
  function closePreviewModal() {
    document.getElementById('previewModal').classList.add('hidden');
    document.getElementById('previewContent').innerHTML = '';
    document.getElementById('ocrSuggestionPanel')?.remove();
    currentPreviewUrl = null;
  }
  function downloadPreviewFile() { if (currentPreviewUrl) window.location.href = currentPreviewUrl; }
 function printPreview() {
    const content = document.getElementById('previewContent');
    if (!content) return;

    // Case 1 — PDF inside an iframe
    const iframe = content.querySelector('iframe');
    if (iframe) {
      try {
        iframe.contentWindow.focus();
        iframe.contentWindow.print();
      } catch (e) {
        // cross-origin fallback: open the preview URL in a new tab and print
        const w = window.open(iframe.src, '_blank');
        if (w) w.onload = () => { w.focus(); w.print(); };
      }
      return;
    }

    // Case 2 — Image (jpg/jpeg/png) shown directly
    const img = content.querySelector('img');
    if (img) {
      // Build a minimal printable page with just the image.
      // NOTE: Do NOT call window.close() inside onload — window.print() is
      // non-blocking on modern browsers, so calling close() immediately causes
      // the print window to disappear before the dialog appears.
      const printWin = window.open('', '_blank', 'width=800,height=600');
      if (!printWin) return;
      printWin.document.write(
        '<!DOCTYPE html><html><head><title>Print</title>' +
        '<style>' +
        '  body { margin:0; display:flex; justify-content:center; align-items:center; min-height:100vh; }' +
        '  img  { max-width:100%; max-height:100vh; object-fit:contain; }' +
        '  @media print { body { margin:0; } img { max-width:100%; max-height:100%; } }' +
        '</style>' +
        '</head><body>' +
        '<img src="' + img.src + '" onload="window.focus(); window.print();">' +
        '<script>window.addEventListener("afterprint", () => window.close());<\/script>' +
        '</body></html>'
      );
      printWin.document.close();
      return;
    }


    // Case 3 — Fallback: use the preview URL (not download URL)
    if (currentPreviewUrl) {
      const w = window.open(currentPreviewUrl, '_blank');
      if (w) w.onload = () => { w.focus(); w.print(); };
    }
  }

  document.addEventListener('click', e => { if (e.target === document.getElementById('previewModal')) closePreviewModal(); });
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape' && !document.getElementById('previewModal').classList.contains('hidden')) closePreviewModal();
  });

  function fileAction_download(filePath) {
    window.location.href = API.download + '?path=' + encodeURIComponent(filePath);
  }

   // ─── Rename Modal ─────────────────────────────────────────────────────────
  let currentRenameTarget = null;
  let currentRenameType   = null;

  // ─── Rename FOLDER Modal ──────────────────────────────────────────────────
  let currentRenameFolderTarget = null;

  function openRenameFolderModal(targetPath) {
    if (!PRIV_ORGANIZE) { denyAction(); return; }
    currentRenameFolderTarget = targetPath;

    const folderName = targetPath.split('/').pop();
    const inp = document.getElementById('renameFolderInput');
    if (inp) { inp.value = folderName; }

    document.getElementById('renameFolderModal').classList.remove('hidden');
    setTimeout(() => inp?.focus(), 50);
  }

  function closeRenameFolderModal() {
    document.getElementById('renameFolderModal').classList.add('hidden');
    currentRenameFolderTarget = null;
  }

  async function submitRenameFolder() {
    if (!PRIV_ORGANIZE) { denyAction(); return; }
    let name = (document.getElementById('renameFolderInput')?.value || '').trim();
    if (!name) { showDialog('Please enter a folder name.', 'warning'); return; }

    // Sanitise: replace spaces with underscores, strip unsafe chars
    name = name.replace(/\s+/g, '_').replace(/[^\w\-]/g, '');
    if (!name) { showDialog('Folder name contains invalid characters.', 'warning'); return; }

    // Save target before closing (closeRenameFolderModal nulls it)
    const targetPath = currentRenameFolderTarget;
    const parentPath = targetPath.includes('/')
      ? targetPath.slice(0, targetPath.lastIndexOf('/'))
      : currentFolderPath;

    const saveBtn = document.querySelector('#renameFolderModal button[onclick="submitRenameFolder()"]');
    if (saveBtn) { saveBtn.disabled = true; saveBtn.textContent = 'Saving…'; }
    const resetBtn = () => {
      if (saveBtn) { saveBtn.disabled = false; saveBtn.textContent = 'Save'; }
    };

    try {
      const data = await apiFetch(API.rename, 'POST', { path: targetPath, new_name: name });
      if (!data.success) {
        showDialog('Rename failed: ' + (data.message || 'Unknown error'), 'error');
        resetBtn();
        return;
      }
      closeRenameFolderModal();
      invalidateFolderCache(currentFolderPath);
      loadFolder(currentFolderPath);
      _htRefreshPath(parentPath);
    } catch (err) {
      showDialog('Rename failed. Please check your connection and try again.', 'error');
      resetBtn();
    }
  }



  function openRenameModal(targetPath, type) {
    if (!PRIV_ORGANIZE) { denyAction(); return; }
    currentRenameTarget = targetPath;
    currentRenameType   = type;

    // Split filename into stem + extension
    const fullName  = targetPath.split('/').pop();
    const dotIdx    = fullName.lastIndexOf('.');
    const stem      = dotIdx > -1 ? fullName.slice(0, dotIdx) : fullName;
    const ext       = dotIdx > -1 ? fullName.slice(dotIdx)    : '';

    // Populate filename input (stem only) and extension badge
    const inp = document.getElementById('renameInput');
    const badge = document.getElementById('renameExtBadge');
    if (inp)   { inp.value = stem; }
    if (badge) { badge.textContent = ext; }

    // Reset doc-type selector; try to pre-select if the stem ends with a known suffix
    const select = document.getElementById('renameDocType');
    if (select) {
      const knownLabels = Array.from(select.options).map(o => o.value).filter(Boolean);
      const matched = knownLabels.find(label => stem.toLowerCase().endsWith('_' + label.toLowerCase()) || stem.toLowerCase() === label.toLowerCase());
      select.value = matched || '';
    }

    document.getElementById('renameModal').classList.remove('hidden');
    setTimeout(() => inp?.focus(), 50);
  }

  function closeRenameModal() {
    document.getElementById('renameModal').classList.add('hidden');
    currentRenameTarget = currentRenameType = null;
    const select = document.getElementById('renameDocType');
    if (select) select.value = '';
  }

  // Mirrors onDocTypeLabelChange() from editFilenameModal — appends suffix to stem
  function onRenameDocTypeChange() {
    const select = document.getElementById('renameDocType');
    const label  = select?.value;
    const input  = document.getElementById('renameInput');
    if (!label || !input) return;
    let current = input.value.trim();
    const knownLabels = Array.from(select.options).map(o => o.value).filter(Boolean);
    knownLabels.forEach(l => {
      current = current.replace(new RegExp('_?' + l + '$', 'i'), '').replace(/_+$/, '');
    });
    input.value = (current ? current + '_' : '') + label;
  }

  async function submitRename() {
    if (!PRIV_ORGANIZE) { denyAction(); return; }
    let stem = (document.getElementById('renameInput')?.value || '').trim();
    if (!stem) { showDialog('Please enter a new name.', 'warning'); return; }

    // Sanitise: replace spaces with underscores, strip unsafe chars
    stem = stem.replace(/\s+/g, '_').replace(/[^\w\-]/g, '');

    // Re-attach the original extension
    const badge = document.getElementById('renameExtBadge');
    const ext   = badge?.textContent || '';
    const newName = stem + ext;

    const data = await apiFetch(API.rename, 'POST', { path: currentRenameTarget, new_name: newName });
    if (!data.success) { showDialog('Rename failed: ' + data.message, 'error'); return; }
    closeRenameModal();
    invalidateFolderCache(currentFolderPath);
    loadFolder(currentFolderPath);
    _htRefreshPath(currentFolderPath);
  }



  // ─── Move Modal ───────────────────────────────────────────────────────────
  let currentMoveTarget  = null;
  let currentMoveType    = null;
  let selectedMoveFolder = null;

  function openMoveModal(targetPath, type) {
    if (!PRIV_ORGANIZE) { denyAction(); return; }
    currentMoveTarget  = targetPath;
    currentMoveType    = type;
    selectedMoveFolder = null;
    document.getElementById('moveModal').classList.remove('hidden');
    refreshMoveFolderList();
  }
  function closeMoveModal() {
    document.getElementById('moveModal').classList.add('hidden');
    currentMoveTarget = currentMoveType = selectedMoveFolder = null;
    const wrap = document.getElementById('movePathPreviewWrap');
    if (wrap) wrap.classList.add('hidden');
    const inp = document.getElementById('searchFolders');
    if (inp) inp.value = '';
  }

  // FIX #1: Move modal also uses unified index — no separate _moveFolderIndex
  function invalidateMoveFolderIndex() { invalidateUnifiedIndex(); }

  async function refreshMoveFolderList() {
    const container = document.getElementById('folderListMove');
    container.innerHTML = '<p class="px-4 py-6 text-sm text-gray-400 text-center">Loading folders...</p>';
    await getFolderEntries(); // warms unified index
    renderMoveFolderResults('');
  }

  function renderMoveFolderResults(q) {
    const container = document.getElementById('folderListMove');
    container.innerHTML = '';
    if (!_unifiedIndex) return;
    const lower   = q.toLowerCase().trim();
    const results = lower
      ? _unifiedIndex.filter(f => f.kind === 'folder' && f.name.toLowerCase().includes(lower))
      : _unifiedIndex.filter(f => f.kind === 'folder');
    if (results.length === 0) {
      container.innerHTML = lower
        ? `<p class="px-4 py-6 text-sm text-gray-400 text-center">No folders match <strong>${q}</strong></p>`
        : '<p class="px-4 py-6 text-sm text-gray-400 text-center">No folders found.</p>';
      return;
    }
    const frag = document.createDocumentFragment();
    results.forEach(f => {
      let displayName = f.name;
      if (lower) {
        const idx = f.name.toLowerCase().indexOf(lower);
        if (idx !== -1) {
          displayName =
            f.name.slice(0, idx) +
            '<span class="bg-yellow-200 text-gray-900 rounded px-0.5 font-semibold">' +
            f.name.slice(idx, idx + q.length) + '</span>' +
            f.name.slice(idx + q.length);
        }
      }
      const isSelected = selectedMoveFolder === f.path;
      const btn = document.createElement('button');
      btn.className = 'folder-move-item w-full text-left px-4 py-3 hover:bg-gray-50 flex items-center gap-3 transition-colors border-l-4 ' +
        (isSelected ? 'bg-green-50 border-green-700' : 'border-transparent');
      btn.dataset.folderName = f.name;
      btn.dataset.folderPath = f.path;
      btn.innerHTML = `
        <div class="w-9 h-9 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
          <svg class="w-5 h-5 text-green-700" fill="currentColor" viewBox="0 0 24 24"><path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/></svg>
        </div>
        <div class="flex-1 min-w-0">
          <p class="font-medium text-gray-800 truncate text-sm">${displayName}</p>
          <p class="text-xs text-gray-400 truncate mt-0.5">${f.locationLabel}</p>
        </div>`;
      btn.addEventListener('click', () => {
        document.querySelectorAll('.folder-move-item').forEach(i => {
          i.classList.remove('bg-green-50', 'border-green-700');
          i.classList.add('border-transparent');
        });
        btn.classList.add('bg-green-50', 'border-green-700');
        btn.classList.remove('border-transparent');
        selectedMoveFolder = f.path;
        const pathPreview = document.getElementById('moveSelectedPath');
        if (pathPreview) {
          pathPreview.textContent = f.locationLabel + ' > ' + f.name;
          pathPreview.closest('#movePathPreviewWrap').classList.remove('hidden');
        }
      });
      frag.appendChild(btn);
    });
    container.appendChild(frag);
  }

  let _moveSearchDebounce = null;
  function filterFoldersInMoveModal() {
    clearTimeout(_moveSearchDebounce);
    _moveSearchDebounce = setTimeout(() => {
      renderMoveFolderResults(document.getElementById('searchFolders').value);
    }, 150);
  }

  async function submitMove() {
    if (!PRIV_ORGANIZE) { denyAction(); return; }
    if (!selectedMoveFolder) { showDialog('Please select a destination folder.', 'warning'); return; }
    const data = await apiFetch(API.move, 'POST', { src_path: currentMoveTarget, dest_path: selectedMoveFolder });
    if (!data.success) { showDialog('Move failed: ' + data.message, 'error'); return; }
    closeMoveModal();
    invalidateFolderCache(currentFolderPath); // FIX #2
    loadFolder(currentFolderPath);
    _htRefreshPath(currentFolderPath);        // FIX #8
  }

  // ─── Init ─────────────────────────────────────────────────────────────────
  renderBreadcrumb();
  loadFolder(''); // First load — pre-warm triggered inside loadFolder after render (FIX #9)

  // =========================================================================
  // HIERARCHY TREE PANEL
  // =========================================================================
  const HT_LEVELS = [
    null,
    { label: 'Location', c:'#7c3aed' },
    { label: 'Room',     c:'#0369a1' },
    { label: 'Cabinet',  c:'#b45309' },
    { label: 'Layer',    c:'#be185d' },
    { label: 'Folder',   c:'#15803d' },
  ];

  let _htCache        = {};
  let _htSelectedPath = '';
  let _htVisible      = true;

  function _htRenderLegend() {
    const el = document.getElementById('hierarchyLevelLegend');
    if (!el) return;
    el.innerHTML = HT_LEVELS.slice(1).map(l => `<span class="ht-legend-pill">${l.label}</span>`).join('');
  }

  async function initHierarchyTree() {
    _htRenderLegend();
    const ul = document.getElementById('hierarchyTree');
    ul.innerHTML = Array.from({length:3}, (_, i) =>
      `<li style="display:flex;align-items:center;gap:6px;padding:6px 20px;">
         <div class="ht-skeleton-bar" style="width:${55+(i%3)*20}%;"></div>
       </li>`
    ).join('');
    const folders = await _htLoadChildren('');
    ul.innerHTML = '';
    if (folders.length === 0) {
      document.getElementById('hierarchyEmptyState').style.display = '';
    } else {
      document.getElementById('hierarchyEmptyState').style.display = 'none';
      folders.forEach((f, i) => _htAppendNode(ul, f, 1, i === folders.length - 1));
    }
    _htUpdateCount();
    _htSetSelected('');
    const badge = document.getElementById('hierarchyRootBadge');
    if (badge) badge.textContent = folders.length + ' folder' + (folders.length !== 1 ? 's' : '');
  }

  async function _htLoadChildren(path) {
    if (_htCache[path]?.loaded) return _htCache[path].folders;
    try {
      const data    = await apiFetch(API.listFolder + '?path=' + encodeURIComponent(path));
      const folders = data.success ? (data.folders || []) : [];
      const files   = data.success ? (data.files   || []) : [];
      _htCache[path] = { folders, files, loaded: true };
      return folders;
    } catch(e) {
      _htCache[path] = { folders: [], files: [], loaded: true };
      return [];
    }
  }

  function _htDepthClass(depth) { return 'ht-d' + Math.min(depth, 5); }

  function _htAppendNode(parentUl, folder, depth, isLast) {
    const dc     = _htDepthClass(depth);
    const indent = 14 + depth * 16;
    const li = document.createElement('li');
    li.dataset.path  = folder.path;
    li.dataset.name  = folder.name;
    li.dataset.depth = depth;
    li.setAttribute('role', 'treeitem');
    li.setAttribute('aria-expanded', 'false');
    li.innerHTML = `
      <div class="ht-row ${dc}" data-path="${folder.path}" style="padding-left:${indent-24}px;">
        <button class="ht-toggle" data-toggle="${folder.path}" title="Expand/collapse" style="flex-shrink:0;margin-left:2px;">
          <svg width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
          </svg>
        </button>
        <div class="ht-inner" style="padding-left:4px;">
          <span style="width:7px;height:7px;border-radius:50%;flex-shrink:0;background:var(--ht-c);display:inline-block;" class="${dc}"></span>
          <span class="ht-label ${dc}" title="${folder.name}">${folder.name}</span>
          ${folder.count > 0 ? `<span class="ht-badge ${dc}">${folder.count}</span>` : ''}
        </div>
      </div>
      <ul class="ht-children closed" style="max-height:0;list-style:none;margin:0;padding:0;"
          data-children-of="${folder.path}" role="group"></ul>`;

    // ── Click: select + expand ────────────────────────────────────────────────
    li.querySelector('.ht-row').addEventListener('click', e => {
      if (e.target.closest('.ht-toggle')) return;
      hierarchySelectFolder(folder.path, folder.name);
      _htToggle(li, folder, depth);
    });
    li.querySelector('.ht-toggle').addEventListener('click', e => {
      e.stopPropagation();
      _htToggle(li, folder, depth);
    });
    li.querySelector('.ht-row').addEventListener('mouseenter', () => {
      // Pre-load children data (original behaviour — keeps it fast)
      if (!_htCache[folder.path]?.loaded) _htLoadChildren(folder.path);

      // ── Auto-expand on hover (file-explorer style) ────────────────────────
      // Start a 600 ms timer. If the cursor is still over this folder when
      // it fires, expand it — exactly like Windows Explorer / Google Drive.
      // The timer is stored on the element so mouseleave can cancel it.
      const row = li.querySelector('.ht-row');
      if (!row._htHoverTimer) {
        row._htHoverTimer = setTimeout(async () => {
          row._htHoverTimer = null;
          const childrenUl  = li.querySelector('ul[data-children-of]');
          const isCollapsed = !childrenUl || childrenUl.classList.contains('closed');
          if (isCollapsed) {
            await _htToggle(li, { path: folder.path, name: folder.name }, depth);
          }
        }, 600);
      }
      // ─────────────────────────────────────────────────────────────────────
    });

    li.querySelector('.ht-row').addEventListener('mouseleave', () => {
      // Cancel pending hover-expand when the cursor moves away
      const row = li.querySelector('.ht-row');
      if (row._htHoverTimer) {
        clearTimeout(row._htHoverTimer);
        row._htHoverTimer = null;
      }
    });


    // ── Drag-and-Drop: make this folder node a valid DROP TARGET ─────────────
    // Files and folder-cards dragged from #masterList can be dropped here.
    // The folder node itself is also draggable so you can move whole folders
    // by dragging a tree node onto another tree node.
    const row = li.querySelector('.ht-row');

    // Make the row draggable (to move this folder)
    row.setAttribute('draggable', 'true');

    row.addEventListener('dragstart', e => {
      e.stopPropagation(); // don't let global handlers interfere
      _dragSrcPath = folder.path;
      _dragSrcType = 'folder';
      e.dataTransfer.effectAllowed = 'move';
      e.dataTransfer.setData('text/plain', folder.path);
      // Dim the node being dragged
      row.style.opacity = '0.4';
    });

    row.addEventListener('dragend', () => {
      row.style.opacity = '';
      // Clear any leftover drop-highlight on tree nodes
      document.querySelectorAll('.ht-row.ht-drop-target').forEach(el => {
        el.classList.remove('ht-drop-target');
        el.style.background = '';
        el.style.outline    = '';
      });
      _dragSrcPath = null;
      _dragSrcType = null;
    });

    // Accept drops from #masterList card drags OR other tree-node drags
    row.addEventListener('dragenter', e => {
      e.preventDefault();
      e.stopPropagation();
      // Do not highlight if this IS the source node
      if (folder.path === _dragSrcPath) return;
      row.classList.add('ht-drop-target');
      row.style.background = 'rgba(22,163,74,0.12)';
      row.style.outline    = '2px solid #16a34a';
      row.style.borderRadius = '6px';


      // ── AUTO-EXPAND on hover (recursive, Windows Explorer style) ──────────
      // Cancels and restarts whenever the drag enters a new row.
      // After 700 ms of staying over this folder it expands it, then
      // automatically schedules the same logic for any newly revealed
      // child rows so that deeper nesting keeps expanding while the
      // user holds the drag over nested folders.
       // Always clear any stale timer before starting a fresh one so that
      // re-entering the same row after the previous timer fired (and set
      // _htExpandTimer back to null) correctly starts a new countdown.
      if (row._htExpandTimer) clearTimeout(row._htExpandTimer);
      row._htExpandTimer = setTimeout(async () => {
        row._htExpandTimer = null;
        const li = row.closest('li');
        if (!li) return;
        const childrenUl  = li.querySelector('ul[data-children-of]');
        const isCollapsed = !childrenUl || childrenUl.classList.contains('closed');
        if (isCollapsed) {
          // Trigger the normal toggle so children load if not yet cached
          await _htToggle(li, { path: folder.path, name: folder.name }, depth);
          // Child rows rendered by _htToggle inherit their own dragenter
          // handlers (attached by _htAppendNode), so deep navigation works
          // automatically as the user drags over newly revealed subfolders.
        }
      }, 700);

      // ─────────────────────────────────────────────────────────────────────
    });

    row.addEventListener('dragover', e => {
      if (folder.path === _dragSrcPath) return;
      e.preventDefault();
      e.stopPropagation();
      e.dataTransfer.dropEffect = 'move';
    });

    row.addEventListener('dragleave', e => {
      e.stopPropagation();
      // Only clear highlight if leaving to outside this row (not into a child)
      if (!row.contains(e.relatedTarget)) {
        row.classList.remove('ht-drop-target');
        row.style.background = '';
        row.style.outline    = '';

        // ── Cancel pending auto-expand if drag left before timer fires ───
        if (row._htExpandTimer) {
          clearTimeout(row._htExpandTimer);
          row._htExpandTimer = null;
        }
        // ─────────────────────────────────────────────────────────────────
      }
    });

    row.addEventListener('drop', async e => {
      e.preventDefault();
      e.stopPropagation();
      row.classList.remove('ht-drop-target');
      row.style.background = '';
      row.style.outline    = '';

      const src  = _dragSrcPath || e.dataTransfer.getData('text/plain');
      const dest = folder.path;

      if (!src || src === dest) return;

      // Prevent dropping a folder INTO itself or one of its own children
      if (dest.startsWith(src + '/')) {
        showDialog('Cannot move a folder into one of its own subfolders.', 'warning');
        return;
      }

      if (!PRIV_ORGANIZE) { showPermissionDenied(); return; }

      const data = await apiFetch(API.move, 'POST', { src_path: src, dest_path: dest });

      if (data.success) {
        // Refresh both the parent folder view and the tree
        const srcParent = src.includes('/') ? src.slice(0, src.lastIndexOf('/')) : '';
        invalidateFolderCache(currentFolderPath);
        invalidateUnifiedIndex();
        loadFolder(currentFolderPath);
        _htRefreshPath(srcParent || '');
        _htRefreshPath(dest);
        showDialog('Moved successfully.', 'success');
      } else {
        showDialog('Move failed: ' + (data.message || 'Unknown error'), 'error');
      }
    });

    parentUl.appendChild(li);
  }

  async function _htToggle(li, folder, depth) {
    const childrenUl = li.querySelector(`ul[data-children-of="${CSS.escape(folder.path)}"]`);
    const toggleBtn  = li.querySelector('.ht-toggle');
    const isOpen     = !childrenUl.classList.contains('closed');
    if (isOpen) {
      childrenUl.style.maxHeight = childrenUl.scrollHeight + 'px';
      requestAnimationFrame(() => {
        childrenUl.style.maxHeight = '0';
        childrenUl.classList.remove('open');
        childrenUl.classList.add('closed');
      });
      toggleBtn.classList.remove('open');
      li.setAttribute('aria-expanded', 'false');
      return;
    }
    toggleBtn.classList.add('open');
    li.setAttribute('aria-expanded', 'true');
    if (!_htCache[folder.path]?.loaded) {
      childrenUl.innerHTML = `<li style="display:flex;align-items:center;gap:6px;padding:5px ${14+(depth+1)*16}px;">
        <div class="ht-skeleton-bar" style="width:60%;"></div></li>`;
      childrenUl.classList.remove('closed'); childrenUl.classList.add('open');
      childrenUl.style.maxHeight = '60px';
      const subs = await _htLoadChildren(folder.path);
      childrenUl.innerHTML = '';
      if (subs.length === 0) {
        childrenUl.innerHTML = `<li style="padding:4px 10px 4px ${14+(depth+1)*16}px;font-size:11px;color:#94a3b8;font-style:italic;">No subfolders</li>`;
        toggleBtn.classList.add('leaf');
      } else {
        subs.forEach((s, i) => _htAppendNode(childrenUl, s, depth + 1, i === subs.length - 1));
      }
    }
    childrenUl.classList.remove('closed'); childrenUl.classList.add('open');
    childrenUl.style.maxHeight = childrenUl.scrollHeight + 'px';
    childrenUl.addEventListener('transitionend', () => {
      if (!childrenUl.classList.contains('closed')) childrenUl.style.maxHeight = 'none';
    }, {once: true});
    _htUpdateCount();
  }

  function hierarchySelectFolder(path, name) {
    // 1. Highlight immediately — zero delay, no waiting for data
    _htSetSelected(path);

    // 2. Clear search if active
    if (_searchActive) {
      const si = document.getElementById('searchInput');
      if (si) si.value = '';
      _searchActive = false;
      document.getElementById('breadcrumb').style.display = '';
    }

    // 3. Build breadcrumb from path segments — no API call needed
    currentFolderPath = path;
    if (path === '') {
      breadcrumbStack = [
        { label: 'My Files',         folderPath: null, isHome: true  },
        { label: 'Academic Records', folderPath: '',   isHome: false },
      ];
    } else {
      breadcrumbStack = [
        { label: 'My Files',         folderPath: null, isHome: true  },
        { label: 'Academic Records', folderPath: '',   isHome: false },
      ];
      let acc = '';
      path.split('/').forEach(p => {
        acc = acc ? acc + '/' + p : p;
        breadcrumbStack.push({ label: p, folderPath: acc, isHome: false });
      });
    }
    renderBreadcrumb();

    // 4. If the tree already loaded this folder's children via _htLoadChildren,
    //    seed folderCache with that data so loadFolder() renders instantly
    //    from cache instead of showing a spinner and fetching again.
    if (_htCache[path]?.loaded && !folderCache.has(path)) {
      folderCache.set(path, {
        folders: _htCache[path].folders || [],
        files:   _htCache[path].files   || [],
        ts:      Date.now(),
      });
    }

    // 5. loadFolder handles both cache-hit (instant) and cache-miss (fetch)
    loadFolder(path);
  }

  function _htSetSelected(path) {
    _htSelectedPath = path;

    // Clear all previous highlights — class-only, no leftover inline styles
    const root = document.getElementById('hierarchyRoot');
    if (root) {
      root.classList.remove('ht-selected');
      root.style.background = ''; root.style.borderRight = '';
    }
    document.querySelectorAll('#hierarchyTree .ht-row').forEach(r => {
      r.classList.remove('ht-selected', 'ht-ancestor');
      const inner = r.querySelector('.ht-inner');
      if (inner) { inner.style.background = ''; inner.style.borderRight = ''; }
    });

    // Apply highlight to the active item
    if (path === '') {
      if (root) {
        root.classList.add('ht-selected');
        root.style.background   = '#dcfce7';
        root.style.borderRight  = '3px solid #16a34a';
      }
    } else {
      const row = document.querySelector(`#hierarchyTree .ht-row[data-path="${CSS.escape(path)}"]`);
      if (row) {
        row.classList.add('ht-selected');
        // Remove any leftover inline styles so the CSS class fully controls the look
        const inner = row.querySelector('.ht-inner');
        if (inner) { inner.style.background = ''; inner.style.borderRight = ''; }
        row.scrollIntoView({ block: 'nearest', behavior: 'smooth' });

        // Highlight ancestor rows so the user can trace the path in the tree
        let parentLi = row.closest('li[data-path]')?.parentElement?.closest('li[data-path]');
        while (parentLi) {
          const ancestorRow = parentLi.querySelector(':scope > div.ht-row');
          if (ancestorRow) ancestorRow.classList.add('ht-ancestor');
          parentLi = parentLi.parentElement?.closest('li[data-path]');
        }
      }
    }
  }

  async function _htExpandToPath(path) {
    if (!path) return;
    const segments = path.split('/');
    let accumulated = '';
    for (let i = 0; i < segments.length; i++) {
      accumulated = i === 0 ? segments[0] : accumulated + '/' + segments[i];
      const isTarget = i === segments.length - 1;
      const parentPath = i === 0 ? '' : segments.slice(0, i).join('/');
      let li = document.querySelector(`#hierarchyTree li[data-path="${CSS.escape(accumulated)}"]`);
      if (!li) {
        const parentLi = parentPath === ''
          ? null
          : document.querySelector(`#hierarchyTree li[data-path="${CSS.escape(parentPath)}"]`);
        if (parentLi) {
          const childrenUl = parentLi.querySelector(`ul[data-children-of="${CSS.escape(parentPath)}"]`);
          if (childrenUl && childrenUl.classList.contains('closed')) {
            const folder = { path: parentPath, name: parentLi.dataset.name };
            const depth  = parseInt(parentLi.dataset.depth ?? 1);
            await _htToggle(parentLi, folder, depth);
            await new Promise(r => requestAnimationFrame(r));
          }
        } else {
          if (!_htCache['']?.loaded) {
            await _htLoadChildren('');
            const ul = document.getElementById('hierarchyTree');
            if (ul && _htCache['']?.folders) {
              ul.innerHTML = '';
              _htCache[''].folders.forEach((f, idx) =>
                _htAppendNode(ul, f, 1, idx === _htCache[''].folders.length - 1)
              );
            }
            await new Promise(r => requestAnimationFrame(r));
          }
        }
        li = document.querySelector(`#hierarchyTree li[data-path="${CSS.escape(accumulated)}"]`);
      }
      if (!li) continue;
      if (!isTarget) {
        const childrenUl = li.querySelector(`ul[data-children-of="${CSS.escape(accumulated)}"]`);
        if (childrenUl && childrenUl.classList.contains('closed')) {
          const folder = { path: accumulated, name: li.dataset.name };
          const depth  = parseInt(li.dataset.depth ?? 1);
          await _htToggle(li, folder, depth);
          await new Promise(r => requestAnimationFrame(r));
        }
      }
    }
    _htSetSelected(path);
  }

  function filterTreeNodes(q) {
    const clearBtn = document.getElementById('treeSearchClear');
    if (clearBtn) clearBtn.style.display = q ? 'block' : 'none';
    const lower = q.toLowerCase().trim();
    document.querySelectorAll('#hierarchyTree li[data-path]').forEach(li => {
      const name = (li.dataset.name || '').toLowerCase();
      const show = !lower || name.includes(lower);
      li.style.display = show ? '' : 'none';
      const label = li.querySelector('.ht-label');
      if (label) {
        if (lower && name.includes(lower)) {
          const t = li.dataset.name, idx = t.toLowerCase().indexOf(lower);
          label.innerHTML = t.slice(0, idx) + `<mark style="background:#fef08a;color:#713f12;border-radius:3px;padding:0 2px;">${t.slice(idx, idx + q.length)}</mark>` + t.slice(idx + q.length);
          let parent = li.parentElement?.closest('li[data-path]');
          while (parent) {
            parent.style.display = '';
            const cu = parent.querySelector('ul.ht-children');
            if (cu) { cu.classList.remove('closed'); cu.classList.add('open'); cu.style.maxHeight = 'none'; }
            const tb = parent.querySelector('.ht-toggle');
            if (tb) tb.classList.add('open');
            parent = parent.parentElement?.closest('li[data-path]');
          }
        } else { label.textContent = li.dataset.name; }
      }
    });
    const rootEl = document.getElementById('hierarchyRoot');
    if (rootEl) rootEl.style.display = (!lower || 'academic records'.includes(lower)) ? '' : 'none';
    document.getElementById('hierarchyEmptyState').style.display = 'none';
  }

  function clearTreeSearch() {
    const input = document.getElementById('treeSearchInput');
    if (input) { input.value = ''; filterTreeNodes(''); }
  }

  function collapseAllTreeNodes() {
    document.querySelectorAll('#hierarchyTree ul.ht-children.open').forEach(ul => {
      ul.style.maxHeight = ul.scrollHeight + 'px';
      requestAnimationFrame(() => { ul.style.maxHeight = '0'; ul.classList.remove('open'); ul.classList.add('closed'); });
    });
    document.querySelectorAll('#hierarchyTree .ht-toggle.open').forEach(b => b.classList.remove('open'));
  }

  async function expandAllTreeNodes() {
    const depth1 = document.querySelectorAll('#hierarchyTree > li[data-depth="1"]');
    for (const li of depth1) {
      const cu = li.querySelector('ul.ht-children');
      if (cu && cu.classList.contains('closed')) {
        await _htToggle(li, { path: li.dataset.path, name: li.dataset.name }, 1);
      }
    }
  }

  function _htUpdateCount() {
    const el    = document.getElementById('hierarchyNodeCount');
    const total = document.querySelectorAll('#hierarchyTree li[data-path]').length;
    if (el) el.textContent = total + ' location' + (total !== 1 ? 's' : '') + ' loaded';
  }

  function toggleHierarchyPanel() {
    _htVisible = !_htVisible;
    document.getElementById('hierarchyPanel').style.display = _htVisible ? 'flex' : 'none';
    document.getElementById('hierarchyTab').style.display   = _htVisible ? 'none' : 'flex';
  }

  function _htInvalidate() {
    _htCache = {};
    initHierarchyTree();
  }

  // =========================================================================
  // FIX #8: Surgical tree refresh — only re-fetch the affected path and parent
  // (was: _htInvalidate() which re-crawled the entire tree on every mutation)
  // =========================================================================
  async function _htRefreshPath(affectedPath) {
    if (!affectedPath) {
      await _htRefreshLevel('');
      return;
    }
    const parentPath = affectedPath.includes('/')
      ? affectedPath.slice(0, affectedPath.lastIndexOf('/'))
      : '';
    delete _htCache[parentPath];
    delete _htCache[affectedPath];
    await _htRefreshLevel(parentPath);
  }

  async function _htRefreshLevel(path) {
    delete _htCache[path];
    const folders = await _htLoadChildren(path);
    if (path === '') {
      const ul = document.getElementById('hierarchyTree');
      ul.innerHTML = '';
      folders.forEach((f, i) => _htAppendNode(ul, f, 1, i === folders.length - 1));
      _htSetSelected(_htSelectedPath);
      _htUpdateCount();
    } else {
      const parentLi = document.querySelector(`#hierarchyTree li[data-path="${CSS.escape(path)}"]`);
      let cu = document.querySelector(`#hierarchyTree ul[data-children-of="${CSS.escape(path)}"]`);

      // FIX BUG 2: If the children UL doesn't exist yet (parent never expanded),
      // create and attach it so the new subfolder appears immediately without
      // requiring a manual expand or browser refresh.
      if (!cu && parentLi) {
        cu = document.createElement('ul');
        cu.className = 'ht-children open';
        cu.dataset.childrenOf = path;
        cu.style.maxHeight = 'none';
        parentLi.appendChild(cu);
        // Update the toggle button to show it now has children
        const toggleBtn = parentLi.querySelector('.ht-toggle');
        if (toggleBtn) {
          toggleBtn.classList.remove('leaf');
          toggleBtn.classList.add('open');
        }
      }

      if (cu) {
        // If closed, open it so the user can see the new subfolder
        if (cu.classList.contains('closed')) {
          cu.classList.remove('closed');
          cu.classList.add('open');
          cu.style.maxHeight = 'none';
          const toggleBtn = parentLi?.querySelector('.ht-toggle');
          if (toggleBtn) {
            // FIX: always remove 'leaf' when we know children now exist,
            // so the toggle remains clickable even if it was previously
            // rendered as a leaf node (zero children).
            toggleBtn.classList.remove('leaf');
            toggleBtn.classList.add('open');
          }
        } else {
          // Parent is already open — still ensure toggle is not stuck in leaf state
          const toggleBtn = parentLi?.querySelector('.ht-toggle');
          if (toggleBtn) toggleBtn.classList.remove('leaf');
        }
        const depth = parseInt(cu.closest('li[data-depth]')?.dataset.depth ?? 1);

        cu.innerHTML = '';
        if (folders.length === 0) {
          cu.innerHTML = `<li style="padding:4px 10px 4px ${14+(depth+1)*16}px;font-size:11px;color:#94a3b8;font-style:italic;">No subfolders</li>`;
        } else {
          folders.forEach((f, i) => _htAppendNode(cu, f, depth + 1, i === folders.length - 1));
        }
        cu.style.maxHeight = 'none';
        _htUpdateCount();
      }
    }
  }


  // ── Patch a single tree-node badge from already-fetched cache data ───────
  // Reads folderCache[path].files.length so there is NO extra network request.
  // Call this after await loadFolder() to guarantee the cache is fresh.
  function _htPatchBadgeFromCache(folderPath) {
    // Pull file count from the folder cache (populated by loadFolder)
    const cached   = folderCache.get(folderPath);
    const newCount = cached ? (cached.files || []).length : null;
    if (newCount === null) return; // cache miss — nothing to patch

    // Locate this folder's rendered tree row
    const row = document.querySelector(
      `#hierarchyTree .ht-row[data-path="${CSS.escape(folderPath)}"]`
    );
    if (!row) return; // node not yet visible in the tree — skip

    const inner = row.querySelector('.ht-inner');
    if (!inner) return;

    // Determine the depth-colour class for badge styling
    const li = row.closest('li[data-depth]');
    const dc = _htDepthClass(parseInt(li?.dataset.depth ?? 1));

    // Remove the stale badge and re-stamp with the new count
    inner.querySelector('.ht-badge')?.remove();
    if (newCount > 0) {
      const badge = document.createElement('span');
      badge.className   = `ht-badge ${dc}`;
      badge.textContent = newCount;
      inner.appendChild(badge);
    }
  }

  // Boot hierarchy tree after initial folder load settles
  setTimeout(() => initHierarchyTree(), 600);

</script>

<script>
/* ── PHP → JS privilege flags ── */

var PRIV_DEL_FILE   = <?= json_encode((bool)($priv_records_delete   ?? false)) ?>;
var PRIV_DEL_FOLDER = <?= json_encode((bool)($priv_folders_delete   ?? false)) ?>;

function showPermissionDenied() {
  var modal = document.getElementById('permissionDeniedModal');
  if (modal) modal.classList.remove('hidden');
}
function closePermissionDeniedModal() {
  var modal = document.getElementById('permissionDeniedModal');
  if (modal) modal.classList.add('hidden');
}
document.addEventListener('click', function(e) {
  var modal = document.getElementById('permissionDeniedModal');
  if (modal && e.target === modal) modal.classList.add('hidden');
});

function isModalReal(id) {
  var el = document.getElementById(id);
  return el && el.children.length > 0;
}

window.addEventListener('load', function() {
  var _openNewFolderModal = window.openNewFolderModal;
  window.openNewFolderModal = function() {
    if (!PRIV_ADD_FOLDER || !isModalReal('newFolderModal')) { showPermissionDenied(); return; }
    if (_openNewFolderModal) _openNewFolderModal();
  };
  var _openUploadModal = window.openUploadModal;
  window.openUploadModal = function() {
    if (!PRIV_UPLOAD || !isModalReal('uploadModal')) { showPermissionDenied(); return; }
    if (_openUploadModal) _openUploadModal();
  };
  var _openUploadFolderModal = window.openUploadFolderModal;
  window.openUploadFolderModal = function() {
    if (!PRIV_UPLOAD || !isModalReal('uploadFolderModal')) { showPermissionDenied(); return; }
    if (_openUploadFolderModal) _openUploadFolderModal();
  };
 var _openRenameModal = window.openRenameModal;
  window.openRenameModal = function() {
    if (!PRIV_ORGANIZE || !isModalReal('renameModal')) { showPermissionDenied(); return; }
    if (_openRenameModal) _openRenameModal.apply(this, arguments);
  };
  var _openRenameFolderModal = window.openRenameFolderModal;
  window.openRenameFolderModal = function() {
    if (!PRIV_ORGANIZE || !isModalReal('renameFolderModal')) { showPermissionDenied(); return; }
    if (_openRenameFolderModal) _openRenameFolderModal.apply(this, arguments);
  };

  var _openMoveModal = window.openMoveModal;
  window.openMoveModal = function() {
    if (!PRIV_ORGANIZE || !isModalReal('moveModal')) { showPermissionDenied(); return; }
    if (_openMoveModal) _openMoveModal.apply(this, arguments);
  };
  var _openPreviewModal = window.openPreviewModal;
  window.openPreviewModal = function() {
    if (!PRIV_VIEW) { showPermissionDenied(); return; }
    if (_openPreviewModal) _openPreviewModal.apply(this, arguments);
  };
  var _deleteFile = window.deleteFile;
  window.deleteFile = function() {
    if (!PRIV_DEL_FILE) { showPermissionDenied(); return; }
    if (_deleteFile) _deleteFile.apply(this, arguments);
  };
  var _deleteFolder = window.deleteFolder;
  window.deleteFolder = function() {
    if (!PRIV_DEL_FOLDER) { showPermissionDenied(); return; }
    if (_deleteFolder) _deleteFolder.apply(this, arguments);
  };
  var _downloadFile = window.downloadFile;
  window.downloadFile = function() {
    if (!PRIV_VIEW) { showPermissionDenied(); return; }
    if (_downloadFile) _downloadFile.apply(this, arguments);
  };
  var _downloadPreviewFile = window.downloadPreviewFile;
  window.downloadPreviewFile = function() {
    if (!PRIV_VIEW) { showPermissionDenied(); return; }
    if (_downloadPreviewFile) _downloadPreviewFile.apply(this, arguments);
  };
});

/* Download link guard */
document.addEventListener('click', function(e) {
  var anchor = e.target.closest('a[href]');
  if (!anchor) return;
  var href = anchor.getAttribute('href') || '';
  if ((href.indexOf('academic-records/download') !== -1 ||
       href.indexOf('academic-records/preview')  !== -1) && !PRIV_VIEW) {
    e.preventDefault();
    showPermissionDenied();
  }
}, true);

// ══════════════════════════════════════════════════════════════════════════════
// UPLOAD PROGRESS TRAY  — Google Drive–style multi-file status panel
// ══════════════════════════════════════════════════════════════════════════════
(function UploadTray() {

  // ── Internal state ──────────────────────────────────────────────────────────
  const _files   = [];          // { id, name, status, progress, rowEl, barEl, statusEl, iconEl }
  let   _total   = 0;           // total bytes across all files
  let   _loaded  = 0;           // bytes confirmed uploaded so far
  let   _trayVisible = false;
  let   _cancelled   = false;   // true when user clicks × during an active batch
  let   _activeXhr   = null;    // reference to the in-flight XHR (so we can abort it)


  // Status → display config
  const _cfg = {
    queued:    { icon: '⏳', label: 'Queued',       barColor: null },
    uploading: { icon: '🔄', label: 'Uploading…',   barColor: 'linear-gradient(90deg,#3b82f6,#60a5fa)' },
    reviewing: { icon: '🔍', label: 'Reviewing…',   barColor: '#34d399' },
    done:      { icon: '✅', label: 'Saved',         barColor: '#22c55e' },
    error:     { icon: '❌', label: 'Failed',        barColor: '#ef4444' },
  };

  // ── Public API ───────────────────────────────────────────────────────────────
  window.UploadTray = {

    /**
     * Register a batch of File objects before uploading begins.
     * Returns an array of IDs you can pass to the update* methods.
     */
    initBatch(files) {
      _files.length = 0;
      _total  = files.reduce((s, f) => s + f.size, 0);
      _loaded = 0;

      files.forEach((file, i) => {
        const id = 'trf_' + Date.now() + '_' + i;
        _files.push({ id, name: file.name, status: 'queued', progress: 0,
                      rowEl: null, barEl: null, statusEl: null, iconEl: null });
      });

      _renderTray();
      _showTray();
      return _files.map(f => f.id);
    },

    /** Call as XHR upload.onprogress fires for a specific file-id. */
    updateProgress(id, loaded, total) {
      const entry = _files.find(f => f.id === id);
      if (!entry) return;
      const prev   = entry.progress;
      entry.progress = total > 0 ? Math.round((loaded / total) * 100) : 0;
      entry.status   = 'uploading';
      _loaded += (loaded - prev * total / 100);   // approximate delta
      _updateRow(entry);
      _updateOverall();
    },

    /** Mark file as temp-uploaded, now waiting for user review. */
    setReviewing(id) {
      const entry = _files.find(f => f.id === id);
      if (!entry) return;
      entry.status   = 'reviewing';
      entry.progress = 100;
      _updateRow(entry);
    },

    /** Mark file as fully saved (after user confirms the review modal). */
    setDone(id) {
      const entry = _files.find(f => f.id === id);
      if (!entry) return;
      entry.status   = 'done';
      entry.progress = 100;
      _updateRow(entry);
      _updateOverall();
      _maybeAutoClose();
    },

    /** Mark file as failed. */
    setError(id, msg) {
      const entry = _files.find(f => f.id === id);
      if (!entry) return;
      entry.status   = 'error';
      entry.statusMsg = msg || 'Failed';
      _updateRow(entry);
      _updateOverall();
    },

   /** Programmatically hide the tray (e.g. on cancel-all). */
    hide() { _hideTray(); },

    /** Register the active XHR so the tray can abort it on cancel. */
    setActiveXhr(xhr) { _activeXhr = xhr; },

    /** Called by the × button — aborts live XHR and sets the stop flag. */
    cancelAll() {
      _cancelled = true;
      if (_activeXhr) { _activeXhr.abort(); _activeXhr = null; }
      _hideTray();
    },

    /** Reset the cancelled flag (called before starting a new batch). */
    resetCancelled() { _cancelled = false; },

    /** Read the flag — drop queue loop checks this before each file. */
    isCancelled() { return _cancelled; },
  };


  // ── Render helpers ───────────────────────────────────────────────────────────

  function _renderTray() {
    const list = document.getElementById('trayFileList');
    if (!list) return;
    list.innerHTML = '';

    _files.forEach(entry => {
      const row = document.createElement('div');
      row.className = 'tray-row';
      row.id = 'tray_row_' + entry.id;

      const cfg = _cfg[entry.status];
      row.innerHTML = `
        <div class="tray-row-icon ${entry.status}" id="tray_icon_${entry.id}">${cfg.icon}</div>
        <div class="tray-row-body">
          <div class="tray-row-name" title="${_esc(entry.name)}">${_esc(entry.name)}</div>
          <div class="tray-row-status" id="tray_status_${entry.id}">${cfg.label}</div>
          <div class="tray-row-bar-wrap">
            <div class="tray-row-bar" id="tray_bar_${entry.id}" style="width:0%"></div>
          </div>
        </div>`;

      entry.rowEl    = row;
      entry.barEl    = row.querySelector('#tray_bar_' + entry.id);
      entry.statusEl = row.querySelector('#tray_status_' + entry.id);
      entry.iconEl   = row.querySelector('#tray_icon_' + entry.id);

      list.appendChild(row);
    });
  }

  function _updateRow(entry) {
    if (!entry.rowEl) return;
    const cfg = _cfg[entry.status];

    // Icon
    if (entry.iconEl) {
      entry.iconEl.textContent = cfg.icon;
      entry.iconEl.className   = 'tray-row-icon ' + entry.status;
    }
    // Status text
    if (entry.statusEl) {
      entry.statusEl.textContent = entry.statusMsg || cfg.label;
    }
    // Progress bar
    if (entry.barEl) {
      entry.barEl.style.width      = entry.progress + '%';
      if (cfg.barColor) entry.barEl.style.background = cfg.barColor;
    }
  }

  function _updateOverall() {
    const pctEl = document.getElementById('trayOverallPct');
    const barEl = document.getElementById('trayOverallBar');
    const done  = _files.filter(f => f.status === 'done' || f.status === 'error').length;
    const pct   = _files.length > 0 ? Math.round((done / _files.length) * 100) : 0;

    const headerLabel = document.getElementById('trayHeaderLabel');
    if (headerLabel) {
      const pending = _files.filter(f => f.status !== 'done' && f.status !== 'error').length;
      headerLabel.textContent = pending > 0
        ? `Uploading ${_files.length - pending + 1} of ${_files.length}…`
        : `${done} file${done !== 1 ? 's' : ''} uploaded`;
    }

    if (pctEl) pctEl.textContent = pct + '%';
    if (barEl) barEl.style.width = pct + '%';
  }

  function _maybeAutoClose() {
    const allDone = _files.every(f => f.status === 'done' || f.status === 'error');
    if (!allDone) return;

    const headerIcon = document.getElementById('trayHeaderIcon');
    if (headerIcon) headerIcon.style.animation = 'none';

    const headerLabel = document.getElementById('trayHeaderLabel');
    if (headerLabel) {
      const errors = _files.filter(f => f.status === 'error').length;
      headerLabel.textContent = errors === 0
        ? `Upload complete ✅`
        : `Done — ${errors} error${errors > 1 ? 's' : ''}`;
    }

    // Auto-hide after 6 s when everything is done
    setTimeout(_hideTray, 6000);
  }

  function _showTray() {
    const tray = document.getElementById('uploadProgressTray');
    if (!tray) return;
    tray.style.display  = 'block';
    tray.style.opacity  = '0';
    tray.style.transform = 'translateY(20px)';
    _trayVisible = true;
    requestAnimationFrame(() => {
      tray.style.opacity   = '1';
      tray.style.transform = 'translateY(0)';
    });
  }

  function _hideTray() {
    const tray = document.getElementById('uploadProgressTray');
    if (!tray) return;
    tray.style.opacity   = '0';
    tray.style.transform = 'translateY(20px)';
    _trayVisible = false;
    setTimeout(() => { tray.style.display = 'none'; }, 280);
  }

  function _esc(str) {
    return String(str)
      .replace(/&/g,'&amp;').replace(/</g,'&lt;')
      .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }

  // ── Global tray controls (called from HTML onclick) ──────────────────────────
  window.toggleTrayMinimise = function() {
    const tray = document.getElementById('uploadProgressTray');
    if (tray) tray.classList.toggle('minimised');
  };
  window.closeTray = function() { _hideTray(); };

})(); // end UploadTray

// Cancels the entire active upload batch (called by tray × button)
window.cancelAllUploads = function() {
  if (window.UploadTray) window.UploadTray.cancelAll();
  // Also cancel the preview modal's current temp token if one is open
  if (typeof cancelTempUpload === 'function' && currentTempToken) {
    cancelTempUpload();
  }
};


// ── Part A: Global drag-from-desktop upload ───────────────────────────────
  (function initGlobalDrop() {
    let _dragEnterCount = 0;
    const overlay = document.getElementById('globalDropOverlay');

    // ── Helper: show/hide a lightweight upload-progress toast ────────────────
    function _showUploadToast(msg, type = 'progress') {
      let toast = document.getElementById('_globalDropToast');
      if (!toast) {
        toast = document.createElement('div');
        toast.id = '_globalDropToast';
        // Positioned above the FAB button so it's always visible
        toast.style.cssText =
          'position:fixed;bottom:100px;right:32px;z-index:9999;' +
          'min-width:260px;max-width:360px;padding:14px 18px;' +
          'border-radius:14px;font-size:13px;font-weight:600;' +
          'display:flex;align-items:center;gap:12px;' +
          'box-shadow:0 8px 32px rgba(0,0,0,0.18);transition:opacity .2s;';
        document.body.appendChild(toast);
      }

      const styles = {
        progress: { bg: '#166534', text: '#ffffff', border: '#15803d' }, // green header style
        success:  { bg: '#14532d', text: '#f0fdf4', border: '#166534' },
        error:    { bg: '#7f1d1d', text: '#fef2f2', border: '#991b1b' },
      };
      const s = styles[type] || styles.progress;

      const spinner = type === 'progress'
        ? '<svg style="width:18px;height:18px;flex-shrink:0;animation:spin 1s linear infinite" ' +
          'fill="none" viewBox="0 0 24 24"><style>@keyframes spin{to{transform:rotate(360deg)}}</style>' +
          '<circle cx="12" cy="12" r="10" stroke="rgba(255,255,255,.25)" stroke-width="3"/>' +
          '<path fill="rgba(255,255,255,.9)" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg>'
        : (type === 'success'
            ? '<span style="font-size:18px">✅</span>'
            : '<span style="font-size:18px">❌</span>');

      toast.style.background   = s.bg;
      toast.style.color        = s.text;
      toast.style.border       = '1.5px solid ' + s.border;
      toast.style.opacity      = '1';
      toast.innerHTML          = spinner + '<span>' + msg + '</span>';

      // Auto-hide after 4 s for success/error
      clearTimeout(toast._timer);
      if (type !== 'progress') {
        toast._timer = setTimeout(() => {
          toast.style.opacity = '0';
          setTimeout(() => toast.remove(), 300);
        }, 4000);
      }
    }

    function _hideUploadToast() {
      const toast = document.getElementById('_globalDropToast');
      if (toast) { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 200); }
    }

    // ── Drag-enter / leave guard (skip if dragging INSIDE a modal) ───────────
    document.addEventListener('dragenter', e => {
      // Do not activate the overlay when dragging inside an open modal
      if (e.target.closest('.modal.active, #folderBrowserModal:not(.hidden),' +
                            '#uploadModal:not(.hidden), #uploadFolderModal:not(.hidden)')) return;
      if (!e.dataTransfer.types.includes('Files')) return;
      if (!PRIV_UPLOAD) return;
      _dragEnterCount++;
      overlay.classList.add('active');
    }, false);

    document.addEventListener('dragleave', e => {
      if (!e.dataTransfer.types.includes('Files')) return;
      _dragEnterCount = Math.max(0, _dragEnterCount - 1);
      if (_dragEnterCount === 0) overlay.classList.remove('active');
    }, false);

    document.addEventListener('dragover', e => {
      // Allow drop everywhere EXCEPT inside open modals (they handle themselves)
      if (e.target.closest('.modal.active, #folderBrowserModal:not(.hidden),' +
                            '#uploadModal:not(.hidden), #uploadFolderModal:not(.hidden)')) return;
      if (!e.dataTransfer.types.includes('Files')) return;
      e.preventDefault();
    }, false);

     document.addEventListener('drop', async e => {
      // Skip if the drop landed inside an open modal — let the modal handle it
      if (e.target.closest('.modal.active, #folderBrowserModal:not(.hidden),' +
                            '#uploadModal:not(.hidden), #uploadFolderModal:not(.hidden)')) return;

      e.preventDefault();
      _dragEnterCount = 0;
      overlay.classList.remove('active');

      if (!PRIV_UPLOAD) { showPermissionDenied(); return; }

      const ALLOWED_EXT = ['pdf','doc','docx','jpg','jpeg','png'];

      // ── Collect files via FileSystemEntry API so folders are traversed ──────
      async function _getFilesFromEntry(entry, pathPrefix) {
        if (entry.isFile) {
          return new Promise(resolve => {
            entry.file(f => {
              const ext = f.name.split('.').pop().toLowerCase();
              if (!ALLOWED_EXT.includes(ext)) { resolve([]); return; }
              const wrapped = new File([f], f.name, { type: f.type, lastModified: f.lastModified });
              Object.defineProperty(wrapped, 'webkitRelativePath', { value: pathPrefix + f.name, writable: false });
              resolve([wrapped]);
            }, () => resolve([]));
          });
        } else if (entry.isDirectory) {
          return new Promise(resolve => {
            const reader     = entry.createReader();
            const collected  = [];
            function readAll() {
              reader.readEntries(async results => {
                if (results.length === 0) {
                  // Pass pathPrefix + entry.name + '/' so nested directory entries
                  // carry their full relative path, preserving the subfolder structure.
                  const nested = await Promise.all(
                    collected.map(e =>
                      _getFilesFromEntry(e, pathPrefix + entry.name + '/')
                    )
                  );
                  resolve(nested.flat());
                } else { collected.push(...results); readAll(); }
              }, () => resolve([]));
            }
            readAll();
          });
        }

        return [];
      }


      // Use FileSystemEntry API when available (covers folder drops)
      let files   = [];
      let _droppedFolderEntry = null; // tracks a top-level folder entry if present

      if (e.dataTransfer.items && e.dataTransfer.items.length > 0) {
        const entries = Array.from(e.dataTransfer.items)
          .map(item => item.webkitGetAsEntry ? item.webkitGetAsEntry() : null)
          .filter(Boolean);

        // ── Detect folder drop: route through Upload Folder modal path ────────
        // This gives the user the same tray + confirmation UX as the modal.
        const folderEntries = entries.filter(en => en.isDirectory);
        if (folderEntries.length > 0) {
          // Use the first dropped folder (multi-folder drops are rare; handle one at a time)
          _droppedFolderEntry = folderEntries[0];
          const allDroppedFiles = await (async () => {
            const results = await Promise.all(
              entries.map(en => en.isDirectory
                ? _getFilesFromEntry(en, en.name + '/')
                : _getFilesFromEntry(en, '')
              )
            );
            return results.flat();
          })();

          if (allDroppedFiles.length === 0) {
            showDialog('The dropped folder appears to be empty.', 'warning');
            return;
          }

          // Populate the pending folder state and open the folder browser directly
          // (same path as the Upload Folder modal submit button)
          _pendingFolderFiles = allDroppedFiles;
          _pendingFolderName  = _droppedFolderEntry.name;

          // Show the folder browser so the user can choose a destination,
          // which will then call _doFinalizeUploadFolderToFolder() — that function
          // now shows the upload tray automatically (see Change 1 of 3 above).
          if (!PRIV_UPLOAD) { showPermissionDenied(); return; }
          _folderBrowserSaveMode = 'folder';
          openFolderBrowserModalForFolder();
          return; // handled — skip the per-file temp-upload loop below
        }

        if (entries.length > 0) {
          const results = await Promise.all(entries.map(entry =>
            entry.isDirectory
              ? _getFilesFromEntry(entry, entry.name + '/')
              : _getFilesFromEntry(entry, '')
          ));
          files = results.flat();
        }
      }

      // Fallback: plain file drop (no folder involved)
      if (files.length === 0) {
        files = Array.from(e.dataTransfer.files).filter(f => {
          const ext = f.name.split('.').pop().toLowerCase();
          return ALLOWED_EXT.includes(ext);
        });
      }

      if (files.length === 0) {
        showDialog('No supported files dropped. Accepted: PDF, DOC, DOCX, JPG, PNG', 'warning');
        return;
      }

      // ── Build queue, register with Tray, process files one at a time ──────
      const _dropQueue = [...files];


      // Register all files with the Tray so rows appear immediately
      const _trayIds = window.UploadTray
        ? window.UploadTray.initBatch(files)
        : files.map(() => null);

      // Reset any previous cancel flag before starting a fresh batch
      if (window.UploadTray) window.UploadTray.resetCancelled();

      let _dropQueueIndex = 0;  // tracks which tray ID to use

      async function _processNextDropped() {
        // Stop the loop if the user clicked × on the tray
        if (window.UploadTray && window.UploadTray.isCancelled()) return;

        if (_dropQueue.length === 0) return;
        const file   = _dropQueue.shift();
        const trayId = _trayIds[_dropQueueIndex++];

        _showUploadToast(`Scanning: ${file.name}…`, 'progress');

        try {
          // Use XHR instead of fetch so we get real upload progress events
          const responseData = await new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();
            const fd  = new FormData();
            fd.append(CSRF_NAME, CSRF_TOKEN);
            fd.append('record_file', file);
            fd.append('folder_path', currentFolderPath);

            // Register XHR so the tray × button can abort it mid-flight
            if (window.UploadTray) window.UploadTray.setActiveXhr(xhr);

            xhr.upload.onprogress = e => {
              if (e.lengthComputable && window.UploadTray && trayId) {
                window.UploadTray.updateProgress(trayId, e.loaded, e.total);
              }
            };

            xhr.onload = () => {
              if (window.UploadTray) window.UploadTray.setActiveXhr(null);
              if (xhr.status >= 200 && xhr.status < 300) {
                try { resolve(JSON.parse(xhr.responseText)); }
                catch { reject(new Error('Bad JSON')); }
              } else {
                reject(new Error('HTTP ' + xhr.status));
              }
            };
            xhr.onerror = () => { if (window.UploadTray) window.UploadTray.setActiveXhr(null); reject(new Error('Network error')); };
            xhr.onabort = () => { if (window.UploadTray) window.UploadTray.setActiveXhr(null); reject(new Error('Aborted')); };

            xhr.open('POST', API.listFolder.replace('/list-folder', '/temp-upload'));
            xhr.send(fd);
          });

          _hideUploadToast();

          if (!responseData.success) {
            if (window.UploadTray && trayId) window.UploadTray.setError(trayId, responseData.message || 'Failed');
            showDialog('Upload failed: ' + (responseData.message || 'Unknown error'), 'error');
            return;
          }

          // Mark file as "in review" in the Tray
          if (window.UploadTray && trayId) window.UploadTray.setReviewing(trayId);

          // Store temp token and metadata (same as the Upload Record Modal flow)
          currentTempToken    = responseData.token;
          currentTempMetadata = {
            original_name: responseData.original_name,
            size:          responseData.size,
            preview_url:   responseData.preview_url,
            ext: responseData.file_ext || responseData.original_name.split('.').pop().toLowerCase() || 'pdf',
            _trayId:      trayId,
            _onComplete:  _processNextDropped,
          };

          // ── Phase 2: Open the Upload Record Modal preview (reuse existing flow)
          openTempPreviewModal(responseData.token, responseData.preview_url, responseData.original_name);
          applyOcrSuggestions(responseData);

        } catch (err) {
          _hideUploadToast();
          if (err.message === 'Aborted' || (window.UploadTray && window.UploadTray.isCancelled())) return;
          if (window.UploadTray && trayId) window.UploadTray.setError(trayId, 'Failed');
          showDialog('Upload failed. Please try again.', 'error');
        }
      }

      _processNextDropped();

    }, false);


  })();

  // ── Part B: Drag-to-move file cards onto folder cards ────────────────────
  // We use event delegation on #masterList to handle dynamically rendered cards.

  let _dragSrcPath = null;
  let _dragSrcType = null;

  document.getElementById('masterList').addEventListener('dragstart', e => {
    const card = e.target.closest('[data-action="preview-file"]');
    if (card) {
      _dragSrcPath = card.dataset.path;
      _dragSrcType = 'file';
      card.classList.add('dragging');
      e.dataTransfer.effectAllowed = 'move';
      e.dataTransfer.setData('text/plain', _dragSrcPath);
      return;
    }
    // Folder cards — allow moving folders too
    const folderCard = e.target.closest('[data-action="open-folder"]');
    if (folderCard) {
      _dragSrcPath = folderCard.dataset.path;
      _dragSrcType = 'folder';
      e.dataTransfer.effectAllowed = 'move';
      e.dataTransfer.setData('text/plain', _dragSrcPath);
    }
  });

  document.getElementById('masterList').addEventListener('dragend', e => {
    document.querySelectorAll('.dragging').forEach(el => el.classList.remove('dragging'));
    document.querySelectorAll('.drag-over').forEach(el => el.classList.remove('drag-over'));
    _dragSrcPath = null;
    _dragSrcType = null;
  });

  document.getElementById('masterList').addEventListener('dragover', e => {
    const folderCard = e.target.closest('[data-action="open-folder"]');
    if (folderCard && _dragSrcPath) {
      e.preventDefault();
      e.dataTransfer.dropEffect = 'move';
      document.querySelectorAll('.drag-over').forEach(el => el.classList.remove('drag-over'));
      folderCard.closest('.folder-card, .folder-card-compact')?.classList.add('drag-over');
    }
  });

  document.getElementById('masterList').addEventListener('dragleave', e => {
    const folderCard = e.target.closest('[data-action="open-folder"]');
    if (!folderCard) return;
    folderCard.closest('.folder-card, .folder-card-compact')?.classList.remove('drag-over');
  });

  document.getElementById('masterList').addEventListener('drop', async e => {
    e.preventDefault();
    const folderCard = e.target.closest('[data-action="open-folder"]');
    if (!folderCard || !_dragSrcPath) return;

    folderCard.closest('.folder-card, .folder-card-compact')?.classList.remove('drag-over');

    const destPath = folderCard.dataset.path;
    if (!destPath || destPath === _dragSrcPath) return;

    if (!PRIV_ORGANIZE) { showPermissionDenied(); return; }

    const data = await apiFetch(API.move, 'POST', {
      src_path:  _dragSrcPath,
      dest_path: destPath,
    });

    if (data.success) {
      invalidateFolderCache(currentFolderPath);
      invalidateUnifiedIndex();
      loadFolder(currentFolderPath);
      _htRefreshPath(currentFolderPath);
    } else {
      showDialog('Move failed: ' + (data.message || 'Unknown error'), 'error');
    }
  });

  // Make file/folder cards draggable (set HTML attribute at render time is
  // difficult with event delegation, so we set it via a MutationObserver)
  (function makeDraggable() {
    const ml = document.getElementById('masterList');
    const observer = new MutationObserver(() => {
      ml.querySelectorAll('[data-action="preview-file"]:not([draggable])').forEach(el => {
        el.setAttribute('draggable', 'true');
      });
      ml.querySelectorAll('[data-action="open-folder"]:not([draggable])').forEach(el => {
        el.setAttribute('draggable', 'true');
      });
    });
    observer.observe(ml, { childList: true, subtree: true });
  })();

</script>

<?= $this->endSection() ?>