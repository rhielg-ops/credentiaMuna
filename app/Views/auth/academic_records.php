<?= $this->extend('layouts/auth_layout') ?>

<?= $this->section('content') ?>
<!-- Folder card compact styles -->
<style>
  /* Google Drive-style compact folder card */
  .folder-card-compact {
    width: 220px;
    min-width: 180px;
    flex-shrink: 0;
  }
  /* Folder row: wrapping flex so cards sit side-by-side */
  .folder-row {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 16px;
  }
  /* List view: folders go full-width, slim row */
  .folder-row.list-mode .folder-card-compact {
    width: 100%;
    min-width: unset;
    flex-shrink: 1;
  }
</style>

<!-- Page Title -->
<div class="flex items-center justify-between mb-6">
  <div>
    <h2 class="text-3xl font-bold text-gray-800 mb-2">Academic Records</h2>
    <p class="text-gray-600">Manage student records and folders</p>
  </div>
</div>

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

<!-- Breadcrumb Navigation — JS-driven, updates only on folder open -->
<div class="mb-6">
  <nav id="breadcrumb" class="flex items-center flex-wrap gap-1 text-sm text-gray-600">
    <!-- populated by renderBreadcrumb() -->
  </nav>
</div>

<!-- Dynamic content — populated by loadFolder() via CI4 AJAX -->
<div id="masterList">
  <div class="flex items-center justify-center py-20 text-gray-400">
    <svg class="w-8 h-8 animate-spin mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
    </svg>
    Loading records…
  </div>
</div>

<!-- ── Preview Modal ── -->
<div id="previewModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col">
    <!-- Modal Header -->
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
      <h3 id="previewTitle" class="text-lg font-semibold text-gray-800">File Preview</h3>
      <div class="flex items-center gap-2">
        <!-- Print Icon -->
        <button
          onclick="printPreview()"
          class="p-2 rounded-lg hover:bg-gray-100 transition-colors"
          title="Print"
        >
          <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
          </svg>
        </button>
        <!-- Download Icon -->
        <button
          onclick="downloadPreviewFile()"
          class="p-2 rounded-lg hover:bg-gray-100 transition-colors"
          title="Download"
        >
          <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
          </svg>
        </button>
        <!-- Close Icon -->
        <button
          onclick="closePreviewModal()"
          class="p-2 rounded-lg hover:bg-gray-100 transition-colors"
          title="Close"
        >
          <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>
    </div>

    <!-- Modal Body - Preview Content -->
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

    <!-- Modal Footer -->
    <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200">
      <button
        onclick="closePreviewModal()"
        class="px-6 py-2.5 rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors font-medium text-gray-700"
      >
        Cancel
      </button>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('fab') ?>

<!-- FAB + anchored popup positioned above the button -->
<div class="fixed bottom-8 right-8 z-50">

  <!-- Chooser popup — sits above the FAB, hidden by default -->
  <div
    id="fabChooserPopup"
    class="absolute bottom-16 right-0 mb-2 w-64 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden
           opacity-0 scale-95 pointer-events-none
           transition-all duration-200 ease-out origin-bottom-right"
  >
    <!-- Header -->
    <div class="px-4 py-3 border-b border-gray-100">
      <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Create / Upload</p>
    </div>

    <!-- Options — always visible; permission only blocks the action -->
    <div class="p-2 space-y-1">

      <!-- New Folder -->
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

      <!-- Upload Folder -->
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

      <!-- Upload Record -->
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

  <!-- Green + FAB Button -->
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

<?= $this->endSection() ?>

<?= $this->section('modals') ?>

<!-- ============================================================
     1. NEW FOLDER MODAL — requires folders_add
     ============================================================ -->
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
        <input
          type="text"
          id="newFolderName"
          placeholder="Enter folder name"
          class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500"
        />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Subname <span class="text-gray-400 font-normal">(optional)</span></label>
        <input
          type="text"
          id="newFolderSubname"
          placeholder="Additional description"
          class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500"
        />
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


<!-- ============================================================
     2. UPLOAD RECORD MODAL — requires records_upload
     ============================================================ -->
<?php if ($can_upload ?? $priv_records_upload ?? false): ?>
<div id="uploadModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-2xl p-6 w-full max-w-lg shadow-2xl">

    <!-- Header -->
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
      <!-- Hidden: populated by JS when a folder is targeted -->
      <input type="hidden" name="folder_path" id="uploadFolderPath" value="">

      <!-- Select Folder (Optional) -->
      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Select Folder <span class="text-gray-400 font-normal">(Optional)</span>
        </label>
        <div class="relative">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="currentColor" viewBox="0 0 24 24">
            <path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/>
          </svg>
          <select name="folder_id"
            class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:border-green-700 focus:ring-2 focus:ring-green-700 appearance-none bg-white transition-colors">
            <option value="">— No folder —</option>
            <option value="academic-records">Academic Records</option>
            <option value="transcripts-2023">Transcripts 2023</option>
            <option value="certificates">Certificates</option>
          </select>
          <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
          </svg>
        </div>
      </div>

      <!-- Drag-and-Drop Zone -->
      <div class="mb-4">
        <label
          id="recordDropZone"
          for="recordFileInput"
          class="flex flex-col items-center justify-center w-full h-44 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-green-600 hover:bg-green-50 transition-colors"
          ondragover="event.preventDefault(); this.classList.add('border-green-600','bg-green-50')"
          ondragleave="this.classList.remove('border-green-600','bg-green-50')"
          ondrop="handleRecordDrop(event)"
        >
          <svg class="w-10 h-10 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
          </svg>
          <p class="text-sm font-medium text-gray-500">Drag &amp; drop file here</p>
          <p class="text-xs text-gray-400 mt-1">PDF, DOC, DOCX — max 10MB</p>
          <input id="recordFileInput" type="file" name="record_file" accept=".pdf,.doc,.docx" class="hidden" onchange="updateRecordFileLabel(this)" />
        </label>
        <p id="recordFileName" class="text-xs text-green-700 font-medium mt-2 hidden"></p>
      </div>

      <!-- Choose File from Browser -->
      <div class="mb-6">
        <button type="button" onclick="document.getElementById('recordFileInput').click()"
          class="w-full flex items-center justify-center gap-2 px-4 py-2.5 border border-gray-300 rounded-xl hover:bg-gray-50 font-medium text-gray-700 transition-colors">
          <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/>
          </svg>
          Choose File from Browser
        </button>
      </div>

      <!-- Action Buttons -->
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


<!-- ============================================================
     3. UPLOAD FOLDER MODAL — requires records_upload
     ============================================================ -->
<?php if ($can_upload ?? $priv_records_upload ?? false): ?>
<div id="uploadFolderModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-2xl p-6 w-full max-w-lg shadow-2xl">

    <!-- Header -->
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

      <!-- Select Folder (Optional) -->
      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Select Folder <span class="text-gray-400 font-normal">(Optional)</span>
        </label>
        <div class="relative">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="currentColor" viewBox="0 0 24 24">
            <path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/>
          </svg>
          <select name="folder_id"
            class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:border-green-700 focus:ring-2 focus:ring-green-700 appearance-none bg-white transition-colors">
            <option value="">— No folder —</option>
            <option value="academic-records">Academic Records</option>
            <option value="transcripts-2023">Transcripts 2023</option>
            <option value="certificates">Certificates</option>
          </select>
          <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
          </svg>
        </div>
      </div>

      <!-- Drag-and-Drop Zone -->
      <div class="mb-4">
        <label
          id="folderDropZone"
          for="folderFilesInput"
          class="flex flex-col items-center justify-center w-full h-44 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-green-600 hover:bg-green-50 transition-colors"
          ondragover="event.preventDefault(); this.classList.add('border-green-600','bg-green-50')"
          ondragleave="this.classList.remove('border-green-600','bg-green-50')"
          ondrop="handleFolderDrop(event)"
        >
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

      <!-- Choose Folder from Browser -->
      <div class="mb-6">
        <button type="button" onclick="document.getElementById('folderFilesInput').click()"
          class="w-full flex items-center justify-center gap-2 px-4 py-2.5 border border-gray-300 rounded-xl hover:bg-gray-50 font-medium text-gray-700 transition-colors">
          <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
            <path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/>
          </svg>
          Choose Folder from Browser
        </button>
      </div>

      <!-- Action Buttons -->
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


<!-- ============================================================
     4. RENAME MODAL — requires records_organize
     ============================================================ -->
<?php if ($can_organize ?? $priv_records_organize ?? false): ?>
<div id="renameModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
  <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-2xl">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <h3 class="text-xl font-bold text-gray-800">Rename</h3>
      <button onclick="closeRenameModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
    </div>
    <!-- Input -->
    <div class="mb-6">
      <label class="block text-sm font-medium text-gray-700 mb-2">New Name</label>
      <input
        type="text"
        id="renameInput"
        placeholder="Enter new name"
        class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:border-green-700 focus:ring-2 focus:ring-green-700 transition-colors"
      />
    </div>
    <!-- Buttons — right-aligned: Back (white/grey border) + Rename (dark green) -->
    <div class="flex justify-end gap-3">
      <button
        onclick="closeRenameModal()"
        class="px-5 py-2.5 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 font-medium text-gray-700 transition-colors"
      >Back</button>
      <button
        onclick="submitRename()"
        class="px-5 py-2.5 bg-green-700 text-white rounded-xl hover:bg-green-800 font-medium transition-colors"
      >Rename</button>
    </div>
  </div>
</div>
<?php else: ?>
<div id="renameModal" class="hidden"></div>
<?php endif; ?>


<!-- ============================================================
     5. MOVE TO FOLDER MODAL — requires records_organize
     ============================================================ -->
<?php if ($can_organize ?? $priv_records_organize ?? false): ?>
<div id="moveModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl flex flex-col max-h-[90vh]">
    
    <!-- Modal Header -->
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

    <!-- Search Bar -->
    <div class="px-6 pt-4 pb-3">
      <div class="relative">
        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
        <input 
          type="text" 
          id="searchFolders" 
          placeholder="Search folders..."
          class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
          oninput="filterFoldersInMoveModal()" 
        />
      </div>
    </div>

    <!-- Permission Note -->
    <?php if (!($can_organize ?? $priv_records_organize ?? false)): ?>
    <div class="mx-6 mb-3 px-4 py-3 bg-amber-50 border border-amber-200 rounded-lg flex items-start gap-3">
      <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
      </svg>
      <div class="flex-1">
        <p class="text-sm font-medium text-amber-800">Limited Permissions</p>
        <p class="text-xs text-amber-700 mt-1">You may not have permission to move items to all folders. Some folders may be restricted.</p>
      </div>
    </div>
    <?php endif; ?>

    <!-- Scrollable Folder List -->
    <div class="flex-1 overflow-y-auto px-6 pb-3">
      <div class="border border-gray-200 rounded-lg overflow-hidden">
        <div id="folderListMove" class="divide-y divide-gray-100">
          
          <!-- Folder Item 1 -->
          <button 
            onclick="selectMoveFolder('ana-bautista')" 
            class="w-full text-left px-4 py-3 hover:bg-gray-50 flex items-center gap-3 folder-move-item transition-colors" 
            data-folder-name="Ana Bautista (2020-11111)"
          >
            <!-- Folder Icon -->
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-green-700" fill="currentColor" viewBox="0 0 24 24">
                <path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="font-medium text-gray-800 truncate">Ana Bautista (2020-11111)</p>
              <p class="text-xs text-gray-500 truncate">My Files › Ana Bautista</p>
            </div>
          </button>

          <!-- Folder Item 2 -->
          <button 
            onclick="selectMoveFolder('alice-santos')" 
            class="w-full text-left px-4 py-3 hover:bg-gray-50 flex items-center gap-3 folder-move-item transition-colors" 
            data-folder-name="Alice Santos (2020-11112)"
          >
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-green-700" fill="currentColor" viewBox="0 0 24 24">
                <path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="font-medium text-gray-800 truncate">Alice Santos (2020-11112)</p>
              <p class="text-xs text-gray-500 truncate">My Files › Alice Santos</p>
            </div>
          </button>

          <!-- Folder Item 3 -->
          <button 
            onclick="selectMoveFolder('antonio-cruz')" 
            class="w-full text-left px-4 py-3 hover:bg-gray-50 flex items-center gap-3 folder-move-item transition-colors" 
            data-folder-name="Antonio Cruz (2020-11113)"
          >
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-green-700" fill="currentColor" viewBox="0 0 24 24">
                <path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="font-medium text-gray-800 truncate">Antonio Cruz (2020-11113)</p>
              <p class="text-xs text-gray-500 truncate">My Files › Antonio Cruz</p>
            </div>
          </button>

          <!-- Folder Item 4 -->
          <button 
            onclick="selectMoveFolder('bryan-garcia')" 
            class="w-full text-left px-4 py-3 hover:bg-gray-50 flex items-center gap-3 folder-move-item transition-colors" 
            data-folder-name="Bryan Garcia (2021-22221)"
          >
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-green-700" fill="currentColor" viewBox="0 0 24 24">
                <path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="font-medium text-gray-800 truncate">Bryan Garcia (2021-22221)</p>
              <p class="text-xs text-gray-500 truncate">My Files › Bryan Garcia</p>
            </div>
          </button>

          <!-- Folder Item 5 -->
          <button 
            onclick="selectMoveFolder('beatrice-reyes')" 
            class="w-full text-left px-4 py-3 hover:bg-gray-50 flex items-center gap-3 folder-move-item transition-colors" 
            data-folder-name="Beatrice Reyes (2021-22222)"
          >
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-green-700" fill="currentColor" viewBox="0 0 24 24">
                <path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="font-medium text-gray-800 truncate">Beatrice Reyes (2021-22222)</p>
              <p class="text-xs text-gray-500 truncate">My Files › Beatrice Reyes</p>
            </div>
          </button>

        </div>
      </div>
    </div>

    <!-- Modal Footer -->
    <div class="flex items-center gap-3 px-6 py-4 border-t border-gray-200">
      <button 
        onclick="closeMoveModal()" 
        class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg hover:bg-gray-50 font-medium text-gray-700 transition-colors"
      >
        Cancel
      </button>
      <button 
        onclick="submitMove()" 
        class="flex-1 px-4 py-2.5 bg-green-700 text-white rounded-lg hover:bg-green-800 font-medium transition-colors"
      >
        Move Here
      </button>
    </div>

  </div>
</div>
<?php else: ?>
<div id="moveModal" class="hidden"></div>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
  // ─── CI4 API Base URLs (no WebDAV, no Nextcloud) ───────────────────────────
  const API = {
    listFolder:   '<?= base_url("academic-records/list-folder") ?>',
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

  // CSRF token for POST requests (CI4 requires this)
  const CSRF_TOKEN = '<?= csrf_hash() ?>';
  const CSRF_NAME  = '<?= csrf_token() ?>';

  // ─── Permission flags from PHP ─────────────────────────────────────────────
  const PRIV_UPLOAD        = <?= json_encode((bool)($can_upload        ?? $priv_records_upload   ?? false)) ?>;
  const PRIV_ADD_FOLDER    = <?= json_encode((bool)($can_add_folder    ?? $priv_folders_add      ?? false)) ?>;
  const PRIV_DELETE_RECORD = <?= json_encode((bool)($can_delete_record ?? $priv_records_delete   ?? false)) ?>;
  const PRIV_DELETE_FOLDER = <?= json_encode((bool)($can_delete_folder ?? $priv_folders_delete   ?? false)) ?>;
  const PRIV_UPDATE        = <?= json_encode((bool)($can_update        ?? $priv_records_update   ?? false)) ?>;
  const PRIV_ORGANIZE      = <?= json_encode((bool)($can_organize      ?? $priv_records_organize ?? false)) ?>;

  const PERMISSION_DENIED_MSG = 'Sorry, you do not have permission to perform this action.';

  // Current folder path the user has navigated into (empty = root)
  let currentFolderPath = '';

  let currentView   = 'grid';
  let currentFilter = 'All Records';

  function denyAction() { alert(PERMISSION_DENIED_MSG); }

  // ─── Generic fetch helper ──────────────────────────────────────────────────
  async function apiFetch(url, method = 'GET', body = null) {
    const opts = { method, headers: { 'X-Requested-With': 'XMLHttpRequest' } };
    if (body instanceof FormData) {
      body.append(CSRF_NAME, CSRF_TOKEN);
      opts.body = body;
    } else if (body) {
      const fd = new FormData();
      fd.append(CSRF_NAME, CSRF_TOKEN);
      for (const [k, v] of Object.entries(body)) fd.append(k, v);
      opts.body = fd;
    }
    const res  = await fetch(url, opts);
    const json = await res.json();
    return json;
  }

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

  // ─── Grid / List View ──────────────────────────────────────────────────────
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
    loadFolder(currentFolderPath); // re-render in new view
  }

  // ─── Breadcrumb ────────────────────────────────────────────────────────────
  const DASHBOARD_URL = '<?= base_url("dashboard") ?>';
  let breadcrumbStack = [
    { label: 'My Files',         folderPath: null,  isHome: true },
    { label: 'Academic Records', folderPath: '',    isHome: false }
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
          loadFolder(crumb.folderPath);
        });
        nav.appendChild(btn);
      }
    });
  }

  // ─── Load folder from CI4 ─────────────────────────────────────────────────
  async function loadFolder(path) {
    currentFolderPath = path ?? '';
    try {
      const data = await apiFetch(API.listFolder + '?path=' + encodeURIComponent(currentFolderPath));
      if (!data.success) { alert('Failed to load folder: ' + data.message); return; }
      renderMasterList(data.folders || [], data.files || []);
    } catch (e) {
      console.error('loadFolder error', e);
      alert('Network error loading folder.');
    }
  }

  function openFolder(folderPath, folderLabel) {
    if (breadcrumbStack[breadcrumbStack.length - 1].folderPath === folderPath) return;
    breadcrumbStack.push({ label: folderLabel, folderPath, isHome: false });
    renderBreadcrumb();
    loadFolder(folderPath);
  }

  // ─── Render master list ───────────────────────────────────────────────────
  function renderMasterList(folders, files) {
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

    // ── Group items by first letter ──
    const allItems = [
      ...folders.map(f => ({ ...f, _type: 'folder', _sortKey: f.name })),
      ...files.map(f   => ({ ...f, _type: 'file',   _sortKey: f.name })),
    ].sort((a, b) => a._sortKey.toLowerCase().localeCompare(b._sortKey.toLowerCase()));

    const groups = {};
    allItems.forEach(item => {
      const letter = item._sortKey.charAt(0).toUpperCase();
      if (!groups[letter]) groups[letter] = [];
      groups[letter].push(item);
    });

    Object.keys(groups).sort().forEach(letter => {
      const section = document.createElement('div');
      section.className = 'mb-8 folder-section';
      section.dataset.letter = letter;
      section.innerHTML = `<div class="bg-green-700 text-white px-4 py-2 rounded-lg mb-4 font-bold text-lg">${letter}</div>`;

      const folderItems = groups[letter].filter(i => i._type === 'folder');
      const fileItems   = groups[letter].filter(i => i._type === 'file');

      if (folderItems.length > 0) {
        const row = document.createElement('div');
        row.className = currentView === 'list' ? 'folder-row list-mode mb-4' : 'folder-row mb-4';
        folderItems.forEach(f => {
          const div = document.createElement('div');
          div.className = 'folder-card-compact';
          div.innerHTML = buildFolderCardHTML(f);
          row.appendChild(div);
        });
        section.appendChild(row);
      }

      if (fileItems.length > 0) {
        if (currentView === 'grid') {
          const grid = document.createElement('div');
          grid.className = 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4';
          fileItems.forEach(f => {
            const div = document.createElement('div');
            div.innerHTML = buildFileCardHTML(f);
            grid.appendChild(div.firstElementChild);
          });
          section.appendChild(grid);
        } else {
          section.appendChild(buildFileListTable(fileItems));
        }
      }

      container.appendChild(section);
    });
  }

  // ─── Build folder card HTML ───────────────────────────────────────────────
  let _menuIdx = 0;
  function buildFolderCardHTML(f) {
    const mid = 'folder-' + (++_menuIdx);
    const safeName = f.name.replace(/'/g, "\\'");
    const safePath = f.path.replace(/'/g, "\\'");
    let menuItems = '';
    if (PRIV_DELETE_FOLDER) {
      menuItems += `<button onclick="fileAction_deleteFolder('${safePath}', '${mid}')" class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50 text-red-600 flex items-center gap-2">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>Delete</button>`;
    }
    if (PRIV_UPLOAD) {
      menuItems += `<button onclick="openUploadModalForFolder('${safePath}')" class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50 text-gray-700 flex items-center gap-2">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>Upload Here</button>`;
    }
    if (PRIV_ORGANIZE) {
      menuItems += `<div class="relative folder-submenu-container">
        <button onclick="toggleSubmenu('org-${mid}', event)" class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50 text-gray-700 flex items-center justify-between">
          <span class="flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>Organise
          </span>
          <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>
        <div id="submenu-org-${mid}" class="hidden absolute left-full top-0 ml-1 w-36 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
          <button onclick="openRenameModal('${safePath}','folder')" class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50 text-gray-700">Rename</button>
          <button onclick="openMoveModal('${safePath}','folder')" class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50 text-gray-700">Move</button>
        </div>
      </div>`;
    }

    return `
      <div class="folder-card bg-white rounded-lg p-4 border border-gray-200 hover:border-green-500 transition-colors cursor-pointer relative" data-folder-name="${f.name}">
        <div class="flex items-start justify-between">
          <div class="flex items-center gap-3 flex-1" onclick="openFolder('${safePath}','${safeName}')">
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
            <button onclick="toggleFolderMenu('${mid}', event)" class="text-gray-400 hover:text-gray-600 p-1">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg>
            </button>
            <div id="folder-menu-${mid}" class="hidden absolute right-0 mt-1 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-50 py-1">
              ${menuItems}
            </div>
          </div>` : ''}
        </div>
      </div>`;
  }

  // ─── Build file card HTML (grid view) ────────────────────────────────────
  let _fileIdx = 0;
  function buildFileCardHTML(f) {
    const mid      = 'file-' + (++_fileIdx);
    const safePath = f.path.replace(/'/g, "\\'");
    const safeName = f.name.replace(/'/g, "\\'");
    const ext      = f.ext || 'pdf';
    const iconSvg  = RECORD_ICONS[ext] || RECORD_ICONS['pdf'];
    const iconBg   = ICON_BG_MAP[ext]  || 'bg-gray-50';
    const label    = ext.toUpperCase();

    let menuItems = '';
    if (PRIV_DELETE_RECORD) {
      menuItems += `<button onclick="fileAction_delete('${safePath}','${mid}')" class="w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>Delete</button>`;
    }
    if (PRIV_ORGANIZE) {
      menuItems += `<div class="relative">
        <button onclick="toggleFileSub('org-${mid}',event)" class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center justify-between">
          <span class="flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>Organise
          </span>
          <svg class="w-3 h-3 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>
        <div id="file-sub-org-${mid}" class="hidden absolute left-full top-0 ml-1 w-36 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
          <button onclick="openRenameModal('${safePath}','file')" class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">Rename</button>
          <button onclick="openMoveModal('${safePath}','file')" class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">Move</button>
        </div>
      </div>`;
    }
    menuItems += `<div class="relative">
      <button onclick="toggleFileSub('view-${mid}',event)" class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center justify-between">
        <span class="flex items-center gap-2">
          <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>View
        </span>
        <svg class="w-3 h-3 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
      </button>
      <div id="file-sub-view-${mid}" class="hidden absolute left-full top-0 ml-1 w-36 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
        <button onclick="fileAction_preview('${safePath}')" class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">Preview</button>
        <button onclick="fileAction_download('${safePath}')" class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">Download</button>
      </div>
    </div>`;

    return `
      <div class="record-card bg-white rounded-xl p-4 shadow-sm border border-gray-100 cursor-pointer hover:shadow-md transition-shadow"
           data-path="${f.path}" data-name="${f.name}"
           onclick="if(!event.target.closest('button') && !event.target.closest('[id^=file-menu-]')) fileAction_preview('${safePath}')">
        <div class="flex items-start justify-between mb-3">
          <div class="w-10 h-10 ${iconBg} rounded-lg flex items-center justify-center">${iconSvg}</div>
          <div class="relative">
            <button onclick="toggleFileMenu('${mid}',event)" class="text-gray-400 hover:text-gray-600 p-0.5 rounded hover:bg-gray-100 transition-colors">
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

  // ─── Build file list table (list view) ───────────────────────────────────
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
    files.forEach(f => {
      const mid      = 'file-' + (++_fileIdx);
      const safePath = f.path.replace(/'/g, "\\'");
      const ext      = f.ext || 'pdf';
      const iconSvg  = RECORD_ICONS[ext] || RECORD_ICONS['pdf'];
      const iconBg   = ICON_BG_MAP[ext]  || 'bg-gray-50';

      let menuItems = '';
      if (PRIV_DELETE_RECORD)
        menuItems += `<button onclick="fileAction_delete('${safePath}','${mid}')" class="w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-red-50">Delete</button>`;
      if (PRIV_ORGANIZE)
        menuItems += `<button onclick="openRenameModal('${safePath}','file')" class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">Rename</button>
                      <button onclick="openMoveModal('${safePath}','file')" class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">Move</button>`;
      menuItems += `<button onclick="fileAction_preview('${safePath}')" class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">Preview</button>
                    <button onclick="fileAction_download('${safePath}')" class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">Download</button>`;

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
            <button onclick="toggleFileMenu('${mid}',event)" class="text-gray-400 hover:text-gray-600 p-1 rounded hover:bg-gray-100">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg>
            </button>
            <div id="file-menu-${mid}" class="hidden absolute right-0 top-7 w-44 bg-white rounded-lg shadow-lg border border-gray-200 z-50 py-1">
              ${menuItems}
            </div>
          </div>
        </div>`;
      tbody.appendChild(row);
    });

    return wrap;
  }

  // ─── Search ───────────────────────────────────────────────────────────────
  function filterRecords() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    document.querySelectorAll('.record-card').forEach(card => {
      card.closest('div').style.display =
        (card.dataset.name || '').toLowerCase().includes(q) ? '' : 'none';
    });
    document.querySelectorAll('.folder-card').forEach(card => {
      card.closest('.folder-card-compact').style.display =
        (card.dataset.folderName || '').toLowerCase().includes(q) ? '' : 'none';
    });
  }

  // ─── Menu toggles ─────────────────────────────────────────────────────────
  function toggleFolderMenu(menuId, event) {
    event.stopPropagation();
    document.querySelectorAll('[id^="folder-menu-"]').forEach(m => {
      if (m.id !== 'folder-menu-' + menuId) m.classList.add('hidden');
    });
    document.getElementById('folder-menu-' + menuId).classList.toggle('hidden');
  }
  function toggleSubmenu(submenuId, event) {
    event.stopPropagation();
    document.getElementById('submenu-' + submenuId)?.classList.toggle('hidden');
  }
  function toggleFileMenu(menuId, event) {
    event.stopPropagation();
    document.querySelectorAll('[id^="file-menu-"]').forEach(m => {
      if (m.id !== 'file-menu-' + menuId) m.classList.add('hidden');
    });
    document.getElementById('file-menu-' + menuId)?.classList.toggle('hidden');
  }
  function toggleFileSub(subKey, event) {
    event.stopPropagation();
    const target = document.getElementById('file-sub-' + subKey);
    if (!target) return;
    const parent = target.closest('[id^="file-menu-"]');
    if (parent) parent.querySelectorAll('[id^="file-sub-"]').forEach(s => {
      if (s !== target) s.classList.add('hidden');
    });
    target.classList.toggle('hidden');
  }
  document.addEventListener('click', function() {
    document.querySelectorAll('[id^="folder-menu-"],[id^="file-menu-"]').forEach(m => m.classList.add('hidden'));
  });

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
    if (!rawName) { alert('Please enter a folder name.'); return; }

    const data = await apiFetch(API.createFolder, 'POST', {
      parent_path: currentFolderPath,
      folder_name: rawName,
    });

    if (!data.success) {
      alert('Error: ' + data.message);
      return;
    }

    closeNewFolderModal();
    loadFolder(currentFolderPath); // refresh from disk
  }

  // ─── Upload Record Modal ──────────────────────────────────────────────────
  function openUploadModal() {
    if (!PRIV_UPLOAD) { denyAction(); return; }
    document.getElementById('uploadFolderPath').value = currentFolderPath;
    document.getElementById('uploadModal').classList.remove('hidden');
  }
  function closeUploadModal() {
    document.getElementById('uploadModal').classList.add('hidden');
    document.getElementById('uploadForm')?.reset();
    const lbl = document.getElementById('recordFileName');
    if (lbl) lbl.classList.add('hidden');
  }
  function openUploadModalForFolder(folderPath) {
    if (!PRIV_UPLOAD) { denyAction(); return; }
    document.getElementById('uploadFolderPath').value = folderPath;
    document.getElementById('uploadModal').classList.remove('hidden');
  }

  // AJAX upload — intercept form submit so we can refresh without full reload
  document.getElementById('uploadForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    const fd   = new FormData(this);
    const data = await apiFetch(API.upload, 'POST', fd);
    if (!data.success) { alert('Upload failed: ' + data.message); return; }
    closeUploadModal();
    loadFolder(currentFolderPath);
  });

  // ─── Upload Folder Modal ──────────────────────────────────────────────────
  function openUploadFolderModal() {
    if (!PRIV_UPLOAD) { denyAction(); return; }
    document.getElementById('uploadFolderFolderPath').value = currentFolderPath;
    document.getElementById('uploadFolderModal').classList.remove('hidden');
  }
  function closeUploadFolderModal() {
    document.getElementById('uploadFolderModal').classList.add('hidden');
    document.getElementById('uploadFolderForm')?.reset();
    const list = document.getElementById('folderFileList');
    if (list) { list.innerHTML = ''; list.classList.add('hidden'); }
  }
  function updateFolderFileList(input) {
    const list = document.getElementById('folderFileList');
    list.innerHTML = '';
    if (!input.files.length) { list.classList.add('hidden'); return; }
    list.classList.remove('hidden');
    Array.from(input.files).forEach(file => {
      const li = document.createElement('li');
      li.className = 'flex items-center gap-2 text-sm text-gray-700 bg-gray-50 rounded-lg px-3 py-2';
      li.innerHTML = `<svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        <span class="flex-1 truncate">${file.name}</span>
        <span class="text-gray-400 flex-shrink-0">${(file.size/1024/1024).toFixed(2)} MB</span>`;
      list.appendChild(li);
    });
  }

  document.getElementById('uploadFolderForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    const fd   = new FormData(this);
    const data = await apiFetch(API.uploadFolder, 'POST', fd);
    if (!data.success) { alert('Upload failed: ' + data.message); return; }
    const errs = data.errors || [];
    if (errs.length) alert('Some files skipped:\n' + errs.join('\n'));
    closeUploadFolderModal();
    loadFolder(currentFolderPath);
  });

  function updateRecordFileLabel(input) {
    const label = document.getElementById('recordFileName');
    if (!label) return;
    if (input.files.length > 0) {
      label.textContent = '✓ ' + input.files[0].name + ' (' + (input.files[0].size/1024/1024).toFixed(2) + ' MB)';
      label.classList.remove('hidden');
    } else {
      label.classList.add('hidden');
    }
  }
  function handleRecordDrop(event) {
    event.preventDefault();
    const input = document.getElementById('recordFileInput');
    const zone  = document.getElementById('recordDropZone');
    zone.classList.remove('border-green-600','bg-green-50');
    if (event.dataTransfer.files.length) {
      const dt = new DataTransfer();
      dt.items.add(event.dataTransfer.files[0]);
      input.files = dt.files;
      updateRecordFileLabel(input);
    }
  }
  function handleFolderDrop(event) {
    event.preventDefault();
    const input = document.getElementById('folderFilesInput');
    const zone  = document.getElementById('folderDropZone');
    zone.classList.remove('border-green-600','bg-green-50');
    const dt = new DataTransfer();
    Array.from(event.dataTransfer.files).forEach(f => dt.items.add(f));
    input.files = dt.files;
    updateFolderFileList(input);
  }

  // ─── Delete file ──────────────────────────────────────────────────────────
  async function fileAction_delete(filePath, menuId) {
    if (!PRIV_DELETE_RECORD) { denyAction(); return; }
    if (!confirm('Permanently delete this file? This cannot be undone.')) return;

    const data = await apiFetch(API.deleteFile, 'POST', { path: filePath });
    if (!data.success) { alert('Delete failed: ' + data.message); return; }

    document.getElementById('file-menu-' + menuId)?.classList.add('hidden');
    loadFolder(currentFolderPath);
  }

  // ─── Delete folder ────────────────────────────────────────────────────────
  async function fileAction_deleteFolder(folderPath, menuId) {
    if (!PRIV_DELETE_FOLDER) { denyAction(); return; }
    if (!confirm('Delete this folder and ALL its contents? This cannot be undone.')) return;

    const data = await apiFetch(API.deleteFolder, 'POST', { path: folderPath });
    if (!data.success) { alert('Delete failed: ' + data.message); return; }

    document.getElementById('folder-menu-' + menuId)?.classList.add('hidden');
    loadFolder(currentFolderPath);
  }

  // ─── Preview ──────────────────────────────────────────────────────────────
  function fileAction_preview(filePath) {
    const modal     = document.getElementById('previewModal');
    const titleEl   = document.getElementById('previewTitle');
    const contentEl = document.getElementById('previewContent');

    titleEl.textContent = filePath.split('/').pop();
    modal.classList.remove('hidden');

    const ext        = filePath.split('.').pop().toLowerCase();
    const previewUrl = API.preview + '?path=' + encodeURIComponent(filePath);
    currentPreviewUrl = API.download + '?path=' + encodeURIComponent(filePath);

    if (['jpg', 'jpeg', 'png'].includes(ext)) {
        // Images render fine directly
        contentEl.innerHTML = `
            <img src="${previewUrl}"
                 alt="Preview"
                 class="max-w-full max-h-full mx-auto rounded-lg shadow-lg"/>`;

    } else if (ext === 'pdf') {
        // PDF — use inline endpoint so browser renders it, not downloads it
        contentEl.innerHTML = `
            <iframe
                src="${previewUrl}"
                class="w-full border-0 rounded-lg"
                style="min-height:520px;"
                title="PDF Preview">
            </iframe>`;

    } else {
        // Word docs, etc. — can't be previewed in browser natively
        contentEl.innerHTML = `
            <div class="text-center text-gray-500 py-10">
                <svg class="w-20 h-20 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                <p class="text-lg font-semibold mb-2">Preview not available</p>
                <p class="text-sm text-gray-400 mb-4">.${ext.toUpperCase()} files cannot be previewed in the browser.</p>
                <button onclick="fileAction_download('${filePath}')"
                        class="px-6 py-2.5 bg-green-700 text-white rounded-lg hover:bg-green-800 transition-colors">
                    Download to View
                </button>
            </div>`;
    }
}

  let currentPreviewUrl = null;
  function closePreviewModal() {
    document.getElementById('previewModal').classList.add('hidden');
    document.getElementById('previewContent').innerHTML = '';
    currentPreviewUrl = null;
  }
  function downloadPreviewFile() {
    if (currentPreviewUrl) window.location.href = currentPreviewUrl;
  }
  function printPreview() {
    const iframe = document.getElementById('previewContent')?.querySelector('iframe');
    if (iframe) {
      try { iframe.contentWindow.print(); } catch(e) { window.open(currentPreviewUrl,'_blank'); }
    } else if (currentPreviewUrl) {
      const w = window.open(currentPreviewUrl,'_blank');
      if (w) w.onload = () => w.print();
    }
  }
  document.addEventListener('click', e => {
    const modal = document.getElementById('previewModal');
    if (e.target === modal) closePreviewModal();
  });
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
      const modal = document.getElementById('previewModal');
      if (!modal.classList.contains('hidden')) closePreviewModal();
    }
  });

  // ─── Download ─────────────────────────────────────────────────────────────
  function fileAction_download(filePath) {
    window.location.href = API.download + '?path=' + encodeURIComponent(filePath);
  }

  // ─── Rename Modal ─────────────────────────────────────────────────────────
  let currentRenameTarget = null;
  let currentRenameType   = null;
  function openRenameModal(targetPath, type) {
    if (!PRIV_ORGANIZE) { denyAction(); return; }
    currentRenameTarget = targetPath;
    currentRenameType   = type;
    const inp = document.getElementById('renameInput');
    if (inp) {
      // Pre-fill with current name
      inp.value = targetPath.split('/').pop();
      inp.focus();
    }
    document.getElementById('renameModal').classList.remove('hidden');
  }
  function closeRenameModal() {
    document.getElementById('renameModal').classList.add('hidden');
    currentRenameTarget = currentRenameType = null;
  }
  async function submitRename() {
    if (!PRIV_ORGANIZE) { denyAction(); return; }
    const name = (document.getElementById('renameInput')?.value || '').trim();
    if (!name) { alert('Please enter a new name.'); return; }

    const data = await apiFetch(API.rename, 'POST', { path: currentRenameTarget, new_name: name });
    if (!data.success) { alert('Rename failed: ' + data.message); return; }
    closeRenameModal();
    loadFolder(currentFolderPath);
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
    // Refresh folder list in the modal
    refreshMoveFolderList();
  }
  function closeMoveModal() {
    document.getElementById('moveModal').classList.add('hidden');
    currentMoveTarget = currentMoveType = selectedMoveFolder = null;
  }
  async function refreshMoveFolderList() {
    const data = await apiFetch(API.listFolder + '?path=' + encodeURIComponent(currentFolderPath));
    if (!data.success) return;
    const container = document.getElementById('folderListMove');
    container.innerHTML = '';
    (data.folders || []).forEach(f => {
      const btn = document.createElement('button');
      btn.className = 'w-full text-left px-4 py-3 hover:bg-gray-50 flex items-center gap-3 folder-move-item transition-colors';
      btn.dataset.folderName = f.name;
      btn.innerHTML = `
        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
          <svg class="w-6 h-6 text-green-700" fill="currentColor" viewBox="0 0 24 24"><path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/></svg>
        </div>
        <div class="flex-1 min-w-0">
          <p class="font-medium text-gray-800 truncate">${f.name}</p>
          <p class="text-xs text-gray-500">${f.path}</p>
        </div>`;
      btn.addEventListener('click', () => {
        document.querySelectorAll('.folder-move-item').forEach(i => i.classList.remove('bg-green-50','border-l-4','border-green-700'));
        btn.classList.add('bg-green-50','border-l-4','border-green-700');
        selectedMoveFolder = f.path;
      });
      container.appendChild(btn);
    });
    if (!data.folders?.length) {
      container.innerHTML = '<p class="px-4 py-3 text-sm text-gray-400">No folders available here.</p>';
    }
  }
  function filterFoldersInMoveModal() {
    const q = document.getElementById('searchFolders').value.toLowerCase();
    document.querySelectorAll('.folder-move-item').forEach(item => {
      item.style.display = (item.dataset.folderName || '').toLowerCase().includes(q) ? '' : 'none';
    });
  }
  async function submitMove() {
    if (!PRIV_ORGANIZE) { denyAction(); return; }
    if (!selectedMoveFolder) { alert('Please select a destination folder.'); return; }

    const data = await apiFetch(API.move, 'POST', {
      src_path:  currentMoveTarget,
      dest_path: selectedMoveFolder,
    });

    if (!data.success) { alert('Move failed: ' + data.message); return; }
    closeMoveModal();
    loadFolder(currentFolderPath);
  }

  // ─── Init ─────────────────────────────────────────────────────────────────
  renderBreadcrumb();
  loadFolder(''); // Initial load from disk

</script>
<?= $this->endSection() ?>