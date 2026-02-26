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
     PERMISSION DENIED MODAL
     ============================================================ -->
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
    <p id="permissionDeniedMsg" class="text-gray-500 mb-6">
      You don't have permission to perform this action.
    </p>
    <button onclick="closePermissionDeniedModal()"
            class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition-colors w-full">
      OK
    </button>
  </div>
</div>


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

    <!-- Selected Path Preview -->
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

  // ─── Reusable pop-up dialog notification ────────────────────────────────────
  // Creates a clean modal-style dialog (not a browser alert) for user feedback.
  // type: 'warning' | 'error' | 'success'
  function showDialog(message, type = 'warning') {
    // Remove any existing dialog
    document.getElementById('_appDialog')?.remove();

    const icons = {
      warning: `<svg class="w-10 h-10 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>`,
      error:   `<svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>`,
      success: `<svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>`,
    };
    const borders = { warning: 'border-yellow-200', error: 'border-red-200', success: 'border-green-200' };
    const bgs     = { warning: 'bg-yellow-50',      error: 'bg-red-50',      success: 'bg-green-50' };

    const overlay = document.createElement('div');
    overlay.id = '_appDialog';
    overlay.className = 'fixed inset-0 z-[9999] flex items-center justify-center p-4';
    overlay.style.backgroundColor = 'rgba(0,0,0,0.45)';
    overlay.innerHTML = `
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden animate-[fadeIn_.15s_ease]">
        <div class="flex flex-col items-center px-8 py-7 ${bgs[type]} border-b ${borders[type]}">
          ${icons[type]}
          <p class="mt-4 text-center text-gray-800 font-medium text-base leading-snug">${message}</p>
        </div>
        <div class="px-8 py-4 flex justify-center bg-white">
          <button id="_appDialogOk"
                  class="px-8 py-2.5 bg-green-700 hover:bg-green-800 text-white rounded-xl font-semibold transition-colors text-sm">
            OK
          </button>
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

  // ─── Duplicate-name confirmation popup ────────────────────────────────────
  // Shows a modal asking the user whether to replace an existing file/folder.
  // onReplace: called when user clicks "Replace"
  // onCancel:  called when user clicks "Cancel" (or dismisses)
  function showDuplicateConfirm(name, onReplace, onCancel) {
    document.getElementById('_dupConfirmDialog')?.remove();

    const overlay = document.createElement('div');
    overlay.id = '_dupConfirmDialog';
    overlay.className = 'fixed inset-0 z-[9999] flex items-center justify-center p-4';
    overlay.style.backgroundColor = 'rgba(0,0,0,0.45)';
    overlay.innerHTML = `
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden">
        <div class="flex flex-col items-center px-8 py-7 bg-yellow-50 border-b border-yellow-200">
          <svg class="w-10 h-10 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
          </svg>
          <p class="mt-4 text-center text-gray-800 font-semibold text-base leading-snug">
            A file or folder with this name already exists.
          </p>
          <p class="mt-1 text-center text-gray-500 text-sm break-all">"${name}"</p>
          <p class="mt-2 text-center text-gray-700 text-sm">Do you want to replace it?</p>
        </div>
        <div class="px-8 py-4 flex gap-3 bg-white">
          <button id="_dupCancel"
                  class="flex-1 px-4 py-2.5 border border-gray-300 rounded-xl hover:bg-gray-50 font-medium text-gray-700 transition-colors text-sm">
            Cancel
          </button>
          <button id="_dupReplace"
                  class="flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl font-semibold transition-colors text-sm">
            Replace
          </button>
        </div>
      </div>`;

    document.body.appendChild(overlay);

    const close = () => overlay.remove();

    document.getElementById('_dupReplace').addEventListener('click', () => { close(); onReplace(); });
    document.getElementById('_dupCancel').addEventListener('click',  () => { close(); if (onCancel) onCancel(); });
    overlay.addEventListener('click', e => { if (e.target === overlay) { close(); if (onCancel) onCancel(); } });
    document.addEventListener('keydown', function esc(e) {
      if (e.key === 'Escape') { close(); document.removeEventListener('keydown', esc); if (onCancel) onCancel(); }
    });
  }

  // Current folder path the user has navigated into (empty = root)
  let currentFolderPath = '';

  let currentView   = 'grid';
  let currentFilter = 'All Records';

  // ═══════════════════════════════════════════════════════════════════════════
  // NEW: TEMP UPLOAD WORKFLOW GLOBAL VARIABLES
  // ═══════════════════════════════════════════════════════════════════════════
  let currentTempToken = null;
  let currentTempMetadata = null;
  let selectedFolderPath = '';
  let folderBrowserPath = '';
  let folderBreadcrumbStack = [{ label: 'Academic Records', path: '' }];

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

  // ─── Global Search (Windows File Explorer style) ──────────────────────────
  // Holds all folders+files fetched once for instant filtering
  let _searchIndex        = null;   // { folders: [...], files: [...] } flat, all paths
  let _searchBuildPromise = null;   // Promise lock — prevents concurrent/duplicate crawls
  let _searchDebounce     = null;
  let _searchActive       = false;

  // Called on every keystroke in the search bar
  function filterRecords() {
    const q = document.getElementById('searchInput').value.trim();

    clearTimeout(_searchDebounce);

    if (q === '') {
      // User cleared search — go back to normal folder view
      if (_searchActive) {
        _searchActive = false;
        document.getElementById('breadcrumb').style.display = '';
        loadFolder(currentFolderPath);
      }
      return;
    }

    // Debounce: wait 180 ms after the user stops typing
    _searchDebounce = setTimeout(() => runGlobalSearch(q), 180);
  }

  // Build a flat index of every folder and file under Academic Records.
  // Uses a Promise lock so concurrent calls all await the SAME crawl — no double-build,
  // no race condition where a partial (empty) index is returned.
  async function buildSearchIndex() {
    if (_searchIndex)        return _searchIndex;          // fully built — return immediately
    if (_searchBuildPromise) return _searchBuildPromise;   // crawl already in-flight — await it

    _searchBuildPromise = (async () => {
      const idx = { folders: [], files: [] };
      await _crawlPath('', [], idx);
      _searchIndex        = idx;
      _searchBuildPromise = null;
      return _searchIndex;
    })();

    return _searchBuildPromise;
  }

  // ancestorLabels = array of folder names from root down to (but not including) current path
  async function _crawlPath(path, ancestorLabels, index) {
    try {
      const data = await apiFetch(API.listFolder + '?path=' + encodeURIComponent(path));
      if (!data.success) return;
      const folders = data.folders || [];
      const files   = data.files   || [];

      const locationLabel = ancestorLabels.length
        ? 'Academic Records › ' + ancestorLabels.join(' › ')
        : 'Academic Records';

      folders.forEach(f => {
        index.folders.push({ ...f, _locationLabel: locationLabel });
      });
      files.forEach(f => {
        index.files.push({ ...f, _locationLabel: locationLabel });
      });

      // Recurse — pass this folder's name as the next ancestor
      await Promise.all(folders.map(f =>
        _crawlPath(f.path, [...ancestorLabels, f.name], index)
      ));
    } catch (e) { /* ignore individual path errors */ }
  }

  async function runGlobalSearch(q) {
    _searchActive = true;

    // Hide breadcrumb while in search mode
    document.getElementById('breadcrumb').style.display = 'none';

    const container = document.getElementById('masterList');
    container.innerHTML = `
      <div class="flex items-center justify-center py-10 text-gray-400">
        <svg class="w-6 h-6 animate-spin mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
        </svg>
        Searching…
      </div>`;

    const index = await buildSearchIndex();

    // Guard: if the user changed the query while index was building, skip stale render
    const currentQ = document.getElementById('searchInput').value.trim();
    if (currentQ !== q) return;

    const lower = q.toLowerCase();

    const matchedFolders = index.folders.filter(f =>
      f.name.toLowerCase().includes(lower)
    );
    const matchedFiles = index.files.filter(f =>
      f.name.toLowerCase().includes(lower)
    );

    renderSearchResults(q, matchedFolders, matchedFiles);
  }

  // Highlight matched substring in a name (case-insensitive)
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

    // ── Header ──
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
        </svg>
        Clear search
      </button>`;
    container.appendChild(header);

    if (total === 0) {
      container.insertAdjacentHTML('beforeend', `
        <div class="flex flex-col items-center justify-center py-20 text-gray-400">
          <svg class="w-14 h-14 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
          <p class="text-lg font-medium">No results found</p>
          <p class="text-sm mt-1">Try a different keyword or check the spelling.</p>
        </div>`);
      return;
    }

    // ── Flat list — folders first, then files ──
    const listWrap = document.createElement('div');
    listWrap.className = 'border border-gray-200 rounded-xl overflow-hidden divide-y divide-gray-100';

    const allResults = [
      ...folders.map(f => ({ ...f, _kind: 'folder' })),
      ...files.map(f   => ({ ...f, _kind: 'file' })),
    ];

    allResults.forEach(item => {
      const isFolder = item._kind === 'folder';

      // _locationLabel was stamped on during crawl — e.g. "Academic Records › CAF"
      const locationLabel = item._locationLabel || 'Academic Records';

      const safePath = item.path.replace(/'/g, "\\'");
      const safeName = item.name.replace(/'/g, "\\'");
      const highlightedName = highlightMatch(item.name, q);

      // Icon
      const iconHTML = isFolder
        ? `<div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
             <svg class="w-6 h-6 text-green-700" fill="currentColor" viewBox="0 0 24 24">
               <path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/>
             </svg>
           </div>`
        : (() => {
            const ext     = item.ext || 'pdf';
            const iconSvg = RECORD_ICONS[ext] || RECORD_ICONS['pdf'];
            const iconBg  = ICON_BG_MAP[ext]  || 'bg-gray-50';
            return `<div class="w-10 h-10 ${iconBg} rounded-lg flex items-center justify-center flex-shrink-0">${iconSvg}</div>`;
          })();

      // Action buttons
      const actionHTML = isFolder
        ? `<button onclick="openFolderFromSearch('${safePath}','${safeName}','${locationLabel.replace(/'/g, "\\'")}')"
                   class="text-xs px-3 py-1.5 rounded-lg bg-green-50 text-green-700 hover:bg-green-100 font-medium transition-colors whitespace-nowrap">
             Open
           </button>`
        : `<div class="flex items-center gap-2">
             <button onclick="fileAction_preview('${safePath}')"
                     class="text-xs px-3 py-1.5 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 font-medium transition-colors whitespace-nowrap">
               Preview
             </button>
             <button onclick="fileAction_download('${safePath}')"
                     class="text-xs px-3 py-1.5 rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 font-medium transition-colors whitespace-nowrap">
               Download
             </button>
           </div>`;

      const row = document.createElement('div');
      row.className = 'flex items-center gap-4 px-4 py-3 hover:bg-gray-50 transition-colors';
      row.innerHTML = `
        ${iconHTML}
        <div class="flex-1 min-w-0">
          <p class="font-semibold text-gray-800 truncate">${highlightedName}</p>
          <p class="text-xs text-gray-400 mt-0.5 flex items-center gap-1 truncate">
            <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/>
            </svg>
            ${locationLabel}
          </p>
        </div>
        <div class="flex-shrink-0">
          ${actionHTML}
        </div>`;

      listWrap.appendChild(row);
    });

    container.appendChild(listWrap);
  }

  // Clear the search bar and restore the current folder
  function clearSearch() {
    const input = document.getElementById('searchInput');
    input.value = '';
    _searchActive = false;
    document.getElementById('breadcrumb').style.display = '';
    renderBreadcrumb();
    loadFolder(currentFolderPath);
  }

  // Navigate directly into a folder from search results, rebuilding the breadcrumb trail
  function openFolderFromSearch(folderPath, folderName, locationLabel) {
    clearSearch();

    // Rebuild breadcrumb from the location label
    // locationLabel format: "Academic Records › CAF › 2026"
    const parts = locationLabel.split(' › ').map(s => s.trim());

    // Reset breadcrumb to base
    breadcrumbStack = [
      { label: 'My Files',         folderPath: null, isHome: true },
      { label: 'Academic Records', folderPath: '',   isHome: false },
    ];

    // Walk through ancestor parts (skip "Academic Records" which is already added)
    // We don't have ancestor paths stored individually, so we navigate straight to target
    breadcrumbStack.push({ label: folderName, folderPath, isHome: false });
    renderBreadcrumb();
    loadFolder(folderPath);
  }

  // Invalidate the index whenever the file system changes (upload, delete, rename, move)
  function invalidateSearchIndex() {
    _searchIndex        = null;
    _searchBuildPromise = null;
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
    invalidateSearchIndex();
    invalidateMoveFolderIndex();
    loadFolder(currentFolderPath); // refresh from disk
  }

  // ═══════════════════════════════════════════════════════════════════════════
  // MODIFIED: UPLOAD RECORD MODAL - WITH PROGRESS BAR
  // ═══════════════════════════════════════════════════════════════════════════
  function openUploadModal() {
    if (!PRIV_UPLOAD) { denyAction(); return; }
    document.getElementById('uploadFolderPath').value = currentFolderPath;
    document.getElementById('uploadModal').classList.remove('hidden');
  }
  function closeUploadModal() {
    document.getElementById('uploadModal').classList.add('hidden');
    document.getElementById('uploadForm')?.reset();
    // Always fully reset the submit button
    const submitBtn = document.querySelector('#uploadForm button[type="submit"]');
    if (submitBtn) {
      submitBtn.disabled = false;
      submitBtn.textContent = 'Upload Record';
    }
    // Clear file label
    const lbl = document.getElementById('recordFileName');
    if (lbl) { lbl.innerHTML = ''; lbl.classList.add('hidden'); }
    // Remove progress bar
    const progress = document.querySelector('#uploadForm .upload-progress-container');
    if (progress) progress.remove();
  }
  function openUploadModalForFolder(folderPath) {
    if (!PRIV_UPLOAD) { denyAction(); return; }
    document.getElementById('uploadFolderPath').value = folderPath;
    document.getElementById('uploadModal').classList.remove('hidden');
  }

  // Upload form handler with progress bar
  // NOTE: uses a named function so we can remove+re-add it cleanly if needed.
  // The submit button text is hardcoded so it is never read from a possibly-corrupt DOM state.
  const UPLOAD_BTN_LABEL = 'Upload Record';

  function _resetUploadModal() {
    const form      = document.getElementById('uploadForm');
    const submitBtn = form?.querySelector('button[type="submit"]');
    const progress  = form?.querySelector('.upload-progress-container');
    const lbl       = document.getElementById('recordFileName');
    if (submitBtn)  { submitBtn.disabled = false; submitBtn.textContent = UPLOAD_BTN_LABEL; }
    if (progress)   { progress.remove(); }
    if (lbl)        { lbl.innerHTML = ''; lbl.classList.add('hidden'); }
  }

  document.getElementById('uploadForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();

    if (!PRIV_UPLOAD) { denyAction(); return; }

    // Guard: if button already disabled, a previous upload is still in flight — bail out
    const submitBtn = this.querySelector('button[type="submit"]');
    if (submitBtn.disabled) return;

    // Validate: must have a file selected
    const recordFile = document.getElementById('recordFileInput');
    if (!recordFile || !recordFile.files || recordFile.files.length === 0) {
      showDialog('Please upload a file or folder first.', 'warning');
      return;
    }

    const formData = new FormData(this);

    // Remove any leftover progress bar from a previous attempt
    this.querySelector('.upload-progress-container')?.remove();

    // Build progress bar
    const progressContainer = document.createElement('div');
    progressContainer.className = 'upload-progress-container mt-4';
    progressContainer.innerHTML = `
      <div class="bg-gray-200 rounded-full h-3 overflow-hidden shadow-inner">
        <div id="uploadProgress" class="bg-gradient-to-r from-green-500 to-green-600 h-full transition-all duration-300 flex items-center justify-end" style="width:0%">
          <span id="uploadPercentText" class="text-xs font-bold text-white pr-2"></span>
        </div>
      </div>
      <div class="flex items-center justify-between mt-2">
        <p id="uploadStatus" class="text-sm text-gray-600 font-medium">
          <span class="inline-block animate-pulse">⬆️</span> Uploading...
        </p>
        <p id="uploadSize" class="text-xs text-gray-500"></p>
      </div>`;
    this.appendChild(progressContainer);

    // Disable button while uploading
    submitBtn.disabled = true;
    submitBtn.innerHTML = `
      <svg class="animate-spin h-5 w-5 mr-2 inline-block" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
      Uploading...`;

    // Helper: fully reset UI on any failure path
    const resetOnError = () => {
      submitBtn.disabled = false;
      submitBtn.textContent = UPLOAD_BTN_LABEL;
      progressContainer.remove();
    };

    try {
      const xhr = new XMLHttpRequest();

      xhr.upload.addEventListener('progress', (e) => {
        if (!e.lengthComputable) return;
        const pct          = (e.loaded / e.total) * 100;
        const progressBar  = document.getElementById('uploadProgress');
        const percentText  = document.getElementById('uploadPercentText');
        const sizeText     = document.getElementById('uploadSize');
        const statusEl     = document.getElementById('uploadStatus');
        if (progressBar)  progressBar.style.width = pct + '%';
        if (percentText)  percentText.textContent  = pct > 20 ? Math.round(pct) + '%' : '';
        if (sizeText)     sizeText.textContent      = `${(e.loaded/1048576).toFixed(1)} MB / ${(e.total/1048576).toFixed(1)} MB`;
        if (statusEl) {
          if (pct < 30)       statusEl.innerHTML = '<span class="inline-block animate-pulse">⬆️</span> Uploading...';
          else if (pct < 70)  statusEl.innerHTML = '<span class="inline-block animate-pulse">📤</span> Upload in progress...';
          else if (pct < 100) statusEl.innerHTML = '<span class="inline-block animate-pulse">⏱️</span> Almost done...';
          else                statusEl.innerHTML = '<span class="inline-block">✅</span> Processing...';
        }
      });

      xhr.addEventListener('load', () => {
        // Always re-enable button first so the modal is never stuck
        submitBtn.disabled = false;
        submitBtn.textContent = UPLOAD_BTN_LABEL;
        progressContainer.remove();

        if (xhr.status !== 200) {
          alert('Upload failed. Please try again.');
          return;
        }

        let data;
        try { data = JSON.parse(xhr.responseText); }
        catch(err) { alert('Unexpected server response. Please try again.'); return; }

        if (!data.success) {
          alert('Upload failed: ' + data.message);
          return;
        }

        // Store temp upload info
        currentTempToken    = data.token;
        currentTempMetadata = {
          original_name: data.original_name,
          size:          data.size,
          preview_url:   data.preview_url,
        };

        // Close upload modal (button already re-enabled above)
        closeUploadModal();

        // Open preview/save modal
        openTempPreviewModal(data.token, data.preview_url, data.original_name);
      });

      xhr.addEventListener('error', () => {
        alert('Network error. Please check your connection.');
        resetOnError();
      });

      xhr.addEventListener('abort', () => {
        alert('Upload cancelled.');
        resetOnError();
      });

      xhr.open('POST', API.listFolder.replace('/list-folder', '/temp-upload'));
      xhr.send(formData);

    } catch (error) {
      console.error('Upload error:', error);
      alert('Upload failed. Please try again.');
      resetOnError();
    }
  });

  // ═══════════════════════════════════════════════════════════════════════════
  // NEW: TEMP PREVIEW MODAL FUNCTIONS
  // ═══════════════════════════════════════════════════════════════════════════
  
  function openTempPreviewModal(token, previewUrl, fileName) {
    const modal = document.getElementById('previewModal');
    const titleEl = document.getElementById('previewTitle');
    const contentEl = document.getElementById('previewContent');
    
    if (!modal || !titleEl || !contentEl) {
      console.error('Preview modal elements not found');
      return;
    }
    
    titleEl.textContent = fileName;
    modal.classList.remove('hidden');
    
    // Determine file type from filename
    const ext = fileName.split('.').pop().toLowerCase();
    
    if (['jpg', 'jpeg', 'png'].includes(ext)) {
      // Image preview
      contentEl.innerHTML = `
        <img src="${previewUrl}" 
             alt="Preview" 
             class="max-w-full max-h-full mx-auto rounded-lg shadow-lg"/>`;
    } else if (ext === 'pdf') {
      // PDF preview
      contentEl.innerHTML = `
        <iframe src="${previewUrl}" 
                class="w-full border-0 rounded-lg" 
                style="min-height:520px;" 
                title="PDF Preview">
        </iframe>`;
    } else {
      // DOCX or other - show metadata
      contentEl.innerHTML = `
        <div class="text-center text-gray-500 py-10">
          <svg class="w-20 h-20 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                  d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
          </svg>
          <p class="text-lg font-semibold mb-2">Preview not available</p>
          <p class="text-sm text-gray-400 mb-4">.${ext.toUpperCase()} files cannot be previewed in the browser.</p>
          <button onclick="downloadTempFile()" 
                  class="px-6 py-2.5 bg-green-700 text-white rounded-lg hover:bg-green-800 transition-colors">
            Download to View
          </button>
        </div>`;
    }
    
    // Update footer buttons - replace Cancel with our new buttons
    const footer = modal.querySelector('.flex.items-center.justify-end.gap-3');
    if (footer) {
      footer.innerHTML = `
        <button onclick="cancelTempUpload()" 
                class="px-6 py-2.5 rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors font-medium text-gray-700">
          ✗ Cancel
        </button>
        <button onclick="openFolderBrowserModal()" 
                class="px-6 py-2.5 rounded-lg bg-green-700 text-white hover:bg-green-800 transition-colors font-medium">
          ✓ Save As
        </button>`;
    }
  }

  function downloadTempFile() {
    if (!currentTempToken) return;
    const downloadUrl = API.listFolder.replace('/list-folder', '/download-pending/' + currentTempToken);
    window.location.href = downloadUrl;
  }

  async function cancelTempUpload() {
    if (!currentTempToken) {
      closePreviewModal();
      return;
    }
    
    try {
      const formData = new FormData();
      formData.append(CSRF_NAME, CSRF_TOKEN);
      formData.append('token', currentTempToken);
      
      await fetch(API.listFolder.replace('/list-folder', '/cancel-pending'), {
        method: 'POST',
        body: formData
      });
      
    } catch (error) {
      console.error('Error cancelling upload:', error);
    } finally {
      currentTempToken = null;
      currentTempMetadata = null;
      closePreviewModal();
    }
  }

  // ═══════════════════════════════════════════════════════════════════════════
  // NEW: FOLDER BROWSER MODAL
  // ═══════════════════════════════════════════════════════════════════════════
  
  // ── Folder Browser: flat index (all folders, recursive) ─────────────────
  let _folderBrowserIndex = null;  // [{ name, path, locationLabel }]
  let _folderBrowserFilesCache = {};  // { [path]: files[] } — avoids extra API call at save time
  let _folderBrowserSearchDebounce = null;
  let _folderBrowserSearchActive = false;
  let _folderBrowserSaveMode = 'record'; // 'record' or 'folder'

  async function _crawlFolderBrowserFolders(path, ancestorLabels, index) {
    try {
      const data = await apiFetch(API.listFolder + '?path=' + encodeURIComponent(path));
      if (!data.success) return;
      const folders = data.folders || [];
      folders.forEach(f => {
        const locationLabel = ancestorLabels.length
          ? 'Academic Records > ' + ancestorLabels.join(' > ')
          : 'Academic Records';
        // Store parentPath explicitly — the path we queried — so renderFolderBrowserTree
        // can match direct children without fragile string parsing of f.path
        index.push({
          name: f.name,
          path: f.path,
          parentPath: path,   // <-- explicit parent, always reliable
          locationLabel,
          count: f.count,
          modified: f.modified
        });
      });
      await Promise.all(folders.map(f =>
        _crawlFolderBrowserFolders(f.path, [...ancestorLabels, f.name], index)
      ));
    } catch(e) {}
  }

  async function buildFolderBrowserIndex() {
    if (_folderBrowserIndex) return _folderBrowserIndex;
    _folderBrowserIndex = [];
    await _crawlFolderBrowserFolders('', [], _folderBrowserIndex);
    return _folderBrowserIndex;
  }

  function invalidateFolderBrowserIndex() { _folderBrowserIndex = null; }

  function openFolderBrowserModal() {
    // Close preview modal first
    document.getElementById('previewModal')?.classList.add('hidden');

    // Create folder browser modal dynamically if it doesn't exist
    if (!document.getElementById('folderBrowserModal')) {
      createFolderBrowserModal();
    }

    // Reset state
    _folderBrowserSearchActive = false;
    _folderBrowserSaveMode = 'record';
    folderBreadcrumbStack = [{ label: 'Academic Records', path: '' }];
    selectedFolderPath = '';

    // Reset title/subtitle back to record defaults
    const titleEl = document.querySelector('#folderBrowserModal h3');
    if (titleEl) titleEl.textContent = 'Select Destination Folder';
    const subtitleEl = document.querySelector('#folderBrowserModal p.text-sm.text-gray-500');
    if (subtitleEl) subtitleEl.textContent = 'Choose where to save this file';

    document.getElementById('folderBrowserModal').classList.remove('hidden');

    // Load and show all folders (no search yet)
    loadFolderBrowser('');
  }

  function createFolderBrowserModal() {
    const modal = document.createElement('div');
    modal.id = 'folderBrowserModal';
    modal.className = 'hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4';
    modal.innerHTML = `
      <div class="bg-white rounded-2xl w-full max-w-2xl max-h-[90vh] flex flex-col shadow-2xl">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
          <div>
            <h3 class="text-xl font-bold text-gray-800">Select Destination Folder</h3>
            <p class="text-sm text-gray-500 mt-1">Choose where to save this file</p>
          </div>
          <button onclick="closeFolderBrowserModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <!-- Breadcrumb -->
        <div class="px-6 pt-4">
          <nav id="folderBreadcrumb" class="flex items-center flex-wrap gap-1 text-sm text-gray-600">
            <!-- Populated by JS -->
          </nav>
        </div>

        <!-- Search -->
        <div class="px-6 pt-3 pb-3">
          <div class="relative">
            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input type="text" id="folderSearchInput" placeholder="Search folders..." 
                   class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                   oninput="filterFoldersInBrowser()">
          </div>
        </div>

        <!-- Folder List -->
        <div class="flex-1 overflow-y-auto px-6 pb-3">
          <div id="folderBrowserList" class="border border-gray-200 rounded-lg divide-y divide-gray-100 min-h-[200px]">
            <!-- Populated by JS -->
          </div>
        </div>

        <!-- Selected Path Preview -->
        <div id="folderBrowserPathPreviewWrap" class="hidden px-6 pb-0 pt-2">
          <div class="flex items-start gap-2 px-3 py-2.5 bg-green-50 border border-green-200 rounded-lg">
            <svg class="w-4 h-4 text-green-700 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/>
            </svg>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-semibold text-green-800 mb-0.5">Selected destination:</p>
              <p id="folderBrowserSelectedPath" class="text-xs text-green-700 break-all"></p>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="flex items-center gap-3 px-6 py-4 border-t border-gray-200 bg-gray-50">
          <button onclick="closeFolderBrowserModal()" 
                  class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg hover:bg-gray-100 font-medium text-gray-700 transition-colors">
            Cancel
          </button>
          <button onclick="_folderBrowserSaveMode === 'folder' ? finalizeUploadFolderToFolder() : finalizeUploadToFolder()" 
                  class="flex-1 px-4 py-2.5 bg-green-700 text-white rounded-lg hover:bg-green-800 font-medium transition-colors">
            Save to This Folder
          </button>
        </div>
      </div>
    `;
    
    document.body.appendChild(modal);
  }

  async function loadFolderBrowser(path) {
    folderBrowserPath = path;
    const listEl = document.getElementById('folderBrowserList');
    if (!listEl) return;

    listEl.innerHTML = '<div class="p-8 text-center text-gray-400">Loading folders…</div>';

    // Build full index in background AND cache the file listing for the current path
    // Both run in parallel so there's no extra wait time
    const [index, listData] = await Promise.all([
      buildFolderBrowserIndex(),
      apiFetch(API.listFolder + '?path=' + encodeURIComponent(path)).catch(() => ({}))
    ]);
    _folderBrowserFilesCache[path] = listData.files || [];
    // Also cache subfolder listings that were already fetched by the index crawl
    if (listData.folders) {
      (listData.folders || []).forEach(f => {
        if (!(_folderBrowserFilesCache[f.path])) _folderBrowserFilesCache[f.path] = [];
      });
    }

    // If search is active, re-apply the search term
    const searchInput = document.getElementById('folderSearchInput');
    const q = searchInput ? searchInput.value.trim() : '';
    if (q) {
      renderFolderBrowserResults(q);
    } else {
      renderFolderBrowserTree(path, index);
    }
    renderFolderBreadcrumb();
  }

  // Show only direct children of `path` (browse mode, no search)
  function renderFolderBrowserTree(path, index) {
    const listEl = document.getElementById('folderBrowserList');
    if (!listEl) return;
    listEl.innerHTML = '';

    // Use the explicit parentPath stamped during crawl — no fragile string parsing
    const children = index.filter(f => f.parentPath === path);

    if (children.length === 0) {
      listEl.innerHTML = '<div class="p-8 text-center text-gray-400">No subfolders here</div>';
      return;
    }

    children.forEach(folder => {
      const div = document.createElement('div');
      div.className = 'folder-browser-item p-4 hover:bg-gray-50 cursor-pointer transition-colors flex items-center gap-3 border-l-4 border-transparent';
      div.dataset.folderName = folder.name;
      div.dataset.folderPath = folder.path;
      div.innerHTML = `
        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
          <svg class="w-6 h-6 text-green-700" fill="currentColor" viewBox="0 0 24 24">
            <path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/>
          </svg>
        </div>
        <div class="flex-1 min-w-0">
          <p class="font-medium text-gray-800 truncate">${folder.name}</p>
          <p class="text-xs text-gray-500">${folder.count ?? 0} file${(folder.count ?? 0) !== 1 ? 's' : ''}</p>
        </div>
        <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>`;
      div.onclick = () => {
        selectFolderBrowserItem(div, folder.path);
        navigateToFolder(folder.path, folder.name);
      };
      listEl.appendChild(div);
    });
  }

  // Show flat search results across ALL folders (search mode)
  function renderFolderBrowserResults(q) {
    const listEl = document.getElementById('folderBrowserList');
    if (!listEl || !_folderBrowserIndex) return;
    listEl.innerHTML = '';

    const lower = q.toLowerCase().trim();
    const results = lower
      ? _folderBrowserIndex.filter(f => f.name.toLowerCase().includes(lower))
      : _folderBrowserIndex;

    if (results.length === 0) {
      listEl.innerHTML = `<div class="p-8 text-center text-gray-400">No folders match <strong>${q}</strong></div>`;
      return;
    }

    results.forEach(folder => {
      // Highlight matched text
      let displayName = folder.name;
      if (lower) {
        const idx = folder.name.toLowerCase().indexOf(lower);
        if (idx !== -1) {
          displayName =
            folder.name.slice(0, idx) +
            '<span class="bg-yellow-200 text-gray-900 rounded px-0.5 font-semibold">' +
            folder.name.slice(idx, idx + q.length) +
            '</span>' +
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
          <svg class="w-5 h-5 text-green-700" fill="currentColor" viewBox="0 0 24 24">
            <path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/>
          </svg>
        </div>
        <div class="flex-1 min-w-0">
          <p class="font-medium text-gray-800 text-sm truncate">${displayName}</p>
          <p class="text-xs text-gray-400 truncate mt-0.5">${folder.locationLabel}</p>
        </div>`;
      div.onclick = () => selectFolderBrowserItem(div, folder.path, folder.name, folder.locationLabel);
      listEl.appendChild(div);
    });
  }

  // Select a folder in the browser and show path preview
  function selectFolderBrowserItem(el, path, name, locationLabel) {
    document.querySelectorAll('.folder-browser-item').forEach(i => {
      i.classList.remove('bg-green-50', 'border-green-700');
      i.classList.add('border-transparent');
    });
    el.classList.add('bg-green-50', 'border-green-700');
    el.classList.remove('border-transparent');
    selectedFolderPath = path;

    // Show selected path preview
    const wrap = document.getElementById('folderBrowserPathPreviewWrap');
    const pathEl = document.getElementById('folderBrowserSelectedPath');
    if (wrap && pathEl && locationLabel) {
      pathEl.textContent = locationLabel + ' > ' + (name || path.split('/').pop());
      wrap.classList.remove('hidden');
    }
  }

  function navigateToFolder(path, label) {
    // Add to breadcrumb stack
    const existing = folderBreadcrumbStack.findIndex(item => item.path === path);
    if (existing !== -1) {
      folderBreadcrumbStack = folderBreadcrumbStack.slice(0, existing + 1);
    } else {
      folderBreadcrumbStack.push({ label, path });
    }

    folderBrowserPath = path;

    // Index is already built — just re-render from cache, no AJAX needed
    if (_folderBrowserIndex) {
      renderFolderBrowserTree(path, _folderBrowserIndex);
      renderFolderBreadcrumb();
      // Pre-cache the file listing for this path if not already cached
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
        nav.insertAdjacentHTML('beforeend', `
          <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
          </svg>`);
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
    _folderBrowserSearchDebounce = setTimeout(() => {
      const q = document.getElementById('folderSearchInput').value.trim();
      if (q === '') {
        // Back to browse mode
        _folderBrowserSearchActive = false;
        renderFolderBreadcrumb();
        renderFolderBrowserTree(folderBrowserPath, _folderBrowserIndex || []);
        // Hide path preview
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
    // Reset state
    folderBreadcrumbStack = [{ label: 'Academic Records', path: '' }];
    selectedFolderPath = '';
    _folderBrowserSearchActive = false;
    _folderBrowserSaveMode = 'record';
    _folderBrowserFilesCache = {};
    // Clear search input
    const inp = document.getElementById('folderSearchInput');
    if (inp) inp.value = '';
    // Hide path preview
    const wrap = document.getElementById('folderBrowserPathPreviewWrap');
    if (wrap) wrap.classList.add('hidden');
  }

  // ═══════════════════════════════════════════════════════════════════════════
  // NEW: FINALIZE UPLOAD TO PERMANENT LOCATION
  // ═══════════════════════════════════════════════════════════════════════════
  
  async function finalizeUploadToFolder() {
    if (!currentTempToken) {
      alert('No upload in progress.');
      return;
    }

    // ── Duplicate check: use cached file listing (populated when user browsed the folder)
    const uploadedName = currentTempMetadata?.original_name || '';
    if (uploadedName) {
      const cachedFiles = _folderBrowserFilesCache[selectedFolderPath] || [];
      const duplicate = cachedFiles.find(
        f => (f.name || '').toLowerCase() === uploadedName.toLowerCase()
      );
      if (duplicate) {
        const existingFilePath = duplicate.path || '';
        showDuplicateConfirm(uploadedName,
          () => _doFinalizeUploadToFolder(true, existingFilePath),   // Replace
          null                                                         // Cancel — do nothing, keep modal open
        );
        return;
      }
    }

    _doFinalizeUploadToFolder(false);
  }

  async function _doFinalizeUploadToFolder(isReplace = false, existingFilePath = '') {
    if (!currentTempToken) return;

    const formData = new FormData();
    formData.append(CSRF_NAME, CSRF_TOKEN);
    formData.append('token', currentTempToken);
    formData.append('folder_path', selectedFolderPath);
    
    // Show loading
    const btn = document.querySelector('#folderBrowserModal button[onclick*="finalizeUpload"]');
    const originalText = btn ? btn.textContent.trim() : 'Save to This Folder';
    if (btn) {
      btn.disabled = true;
      btn.innerHTML = `
        <svg class="animate-spin h-5 w-5 mr-2 inline-block" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Saving...`;
    }

    const resetBtn = () => {
      if (btn) { btn.disabled = false; btn.textContent = originalText; }
    };

    try {
      // If replacing, delete the existing file first so finalize-upload can save cleanly
      if (isReplace && existingFilePath) {
        await apiFetch(API.deleteFile, 'POST', { path: existingFilePath });
      }

      const response = await fetch(API.listFolder.replace('/list-folder', '/finalize-upload'), {
        method: 'POST',
        body: formData
      });
      
      const data = await response.json();
      
      if (!data.success) {
        alert('Failed to save: ' + data.message);
        btn.disabled = false;
        btn.textContent = originalText;
        return;
      }
      
      // Success!
      const successMsg = isReplace ? 'File replaced successfully.' : (data.message || 'Record saved successfully.');

      // Re-enable button before closing so the modal is never stuck in "Saving..." on next open
      btn.disabled = false;
      btn.textContent = originalText;

      // Close all modals
      closeFolderBrowserModal();
      closePreviewModal();

      showDialog(successMsg, 'success');
      
      // Reset state
      currentTempToken = null;
      currentTempMetadata = null;
      
      // Refresh file list
      invalidateSearchIndex();
      invalidateMoveFolderIndex();
      loadFolder(currentFolderPath);
      
    } catch (error) {
      console.error('Error finalizing upload:', error);
      alert('Failed to save file. Please try again.');
      btn.disabled = false;
      btn.textContent = originalText;
    }
  }

  // ─── Upload Folder ────────────────────────────────────────────────────────
  // Strategy:
  //   • Folder Name (required) + optional files selected by the user
  //   • On submit: validate → open folder browser to pick destination
  //   • On "Save to This Folder": createFolder at dest, then uploadFolder files (if any)
  //   • No temp tokens — uses the same existing API endpoints that already work
  // ──────────────────────────────────────────────────────────────────────────

  const UPLOAD_FOLDER_BTN_LABEL = 'Upload Folder';

  // Holds files chosen by the user while they browse for a destination folder
  let _pendingFolderFiles = [];
  let _pendingFolderName  = '';

  function _resetUploadFolderModal() {
    const submitBtn = document.getElementById('uploadFolderSubmitBtn');
    const form      = document.getElementById('uploadFolderForm');
    const progress  = form?.querySelector('.upload-folder-progress-container');
    const list      = document.getElementById('folderFileList');
    const nameInput = document.getElementById('uploadFolderName');
    if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = UPLOAD_FOLDER_BTN_LABEL; }
    if (progress)  { progress.remove(); }
    if (list)      { list.innerHTML = ''; list.classList.add('hidden'); }
    if (nameInput) { nameInput.value = ''; }
    // Clear both file inputs
    const folderDirInput = document.getElementById('folderDirInput');
    if (folderDirInput) folderDirInput.value = '';
    const folderFilesInput = document.getElementById('folderFilesInput');
    if (folderFilesInput) folderFilesInput.value = '';
    _pendingFolderFiles = [];
    _pendingFolderName  = '';
  }

  function openUploadFolderModal() {
    if (!PRIV_UPLOAD) { denyAction(); return; }
    _resetUploadFolderModal();

    // Force folder-picker dialog (not file-picker) every time the modal opens
    const _ffi = document.getElementById('folderFilesInput');
    if (_ffi) {
      _ffi.setAttribute('webkitdirectory', '');
      _ffi.setAttribute('mozdirectory', '');
      _ffi.removeAttribute('accept');
      _ffi.onchange = function() { handleFolderDirSelect(this); };
    }

    // Inject folder name input above the drop zone if missing (design-safe)
    const form = document.getElementById('uploadFolderForm');
    if (form && !document.getElementById('uploadFolderName')) {
      // Find the drop zone wrapper and insert before it
      const dropZoneWrap = document.getElementById('folderDropZone')?.closest('div.mb-4');
      const nameWrap = document.createElement('div');
      nameWrap.id = 'uploadFolderNameWrap';
      nameWrap.className = 'mb-4';
      nameWrap.innerHTML = `
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Folder Name <span class="text-red-500">*</span>
        </label>
        <input
          type="text"
          id="uploadFolderName"
          name="upload_folder_name"
          placeholder="Auto-filled from selected folder"
          class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500"
        />`;
      if (dropZoneWrap) {
        form.insertBefore(nameWrap, dropZoneWrap);
      } else {
        form.insertBefore(nameWrap, form.firstChild);
      }
    }

    // Assign id to submit button if missing
    if (form) {
      const submitBtn = form.querySelector('button[type="submit"]');
      if (submitBtn && !submitBtn.id) {
        submitBtn.id = 'uploadFolderSubmitBtn';
      }
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
    _pendingFolderFiles.forEach(file => {
      const sizeMB = (file.size / 1048576).toFixed(2);
      const li = document.createElement('li');
      li.className = 'flex items-center gap-2 text-sm text-gray-700 bg-gray-50 rounded-lg px-3 py-2';
      li.innerHTML = `
        <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <span class="flex-1 truncate">${file.name}</span>
        <span class="text-gray-400 flex-shrink-0">${sizeMB} MB</span>`;
      list.appendChild(li);
    });
  }

  document.getElementById('uploadFolderForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    if (!PRIV_UPLOAD) { denyAction(); return; }

    const submitBtn = document.getElementById('uploadFolderSubmitBtn');
    if (submitBtn?.disabled) return;

    // ── Derive folder name: from hidden input (set by handleFolderDirSelect) or _pendingFolderName ──
    const folderNameEl = document.getElementById('uploadFolderName');
    const folderName   = (folderNameEl?.value || _pendingFolderName || '').trim();

    if (!folderName) {
      showDialog('Please select a folder first.', 'warning');
      return;
    }

    // Capture current files from the input (may be 0 for empty folder — that's fine)
    const filesInput    = document.getElementById('folderFilesInput');
    _pendingFolderFiles = _pendingFolderFiles.length > 0
      ? _pendingFolderFiles
      : Array.from(filesInput?.files || []);
    _pendingFolderName  = folderName;
    // Sync hidden input
    if (folderNameEl) folderNameEl.value = folderName;

    // ── Go straight to folder browser — no XHR yet ──────────────────────────
    // Save pendingFolderName BEFORE closeUploadFolderModal() resets it
    const savedFolderName  = folderName;
    const savedFolderFiles = _pendingFolderFiles.slice();
    closeUploadFolderModal();
    // Restore after reset so the folder browser has the correct name
    _pendingFolderName  = savedFolderName;
    _pendingFolderFiles = savedFolderFiles;
    openFolderBrowserModalForFolder();
  });

  // Open folder browser in "folder-upload" mode
  function openFolderBrowserModalForFolder() {
    if (!document.getElementById('folderBrowserModal')) {
      createFolderBrowserModal();
    }

    _folderBrowserSaveMode    = 'folder';
    _folderBrowserSearchActive = false;
    folderBreadcrumbStack = [{ label: 'Academic Records', path: '' }];
    selectedFolderPath = '';

    // Hide stale path preview
    const wrap = document.getElementById('folderBrowserPathPreviewWrap');
    if (wrap) wrap.classList.add('hidden');

    document.getElementById('folderBrowserModal').classList.remove('hidden');
    loadFolderBrowser('');

    // Update modal copy
    const titleEl    = document.querySelector('#folderBrowserModal h3');
    const subtitleEl = document.querySelector('#folderBrowserModal p.text-sm.text-gray-500');
    if (titleEl)    titleEl.textContent    = 'Select Destination Folder';
    if (subtitleEl) subtitleEl.textContent = 'Choose where to create "' + _pendingFolderName + '"';
  }

  // Called when user clicks "Save to This Folder" in folder-upload mode.
  // Creates root folder, then every subfolder (shallow-first), then uploads
  // each file to its correct subfolder path — like Google Drive.
  async function finalizeUploadFolderToFolder() {
    if (!_pendingFolderName) {
      showDialog('No folder name found. Please try again.', 'warning');
      return;
    }

    // ── Duplicate check: use cached folder index (populated when user browsed) ──
    const cachedFolders = (_folderBrowserIndex || []).filter(f => f.parentPath === selectedFolderPath);
    const duplicateFolder = cachedFolders.find(
      f => (f.name || '').toLowerCase() === _pendingFolderName.toLowerCase()
    );
    if (duplicateFolder) {
      showDuplicateConfirm(_pendingFolderName,
        () => _doFinalizeUploadFolderToFolder(true),   // Replace — skip root folder creation
        null                                            // Cancel — do nothing, keep modal open
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
      btn.innerHTML = `
        <svg class="animate-spin h-5 w-5 mr-2 inline-block" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>${label || 'Saving...'}`;
    };
    const resetBtn = () => {
      if (btn) { btn.disabled = false; btn.textContent = originalText; }
    };

    setLoading('Saving...');

    try {
      const allFiles   = _pendingFolderFiles;
      const rootName   = _pendingFolderName;
      const destPrefix = selectedFolderPath ? selectedFolderPath + '/' : '';
      const rootPath   = destPrefix + rootName;

      // ── Step 1: Create root folder (skip if replacing — folder already exists) ─
      if (!skipRootCreation) {
        const createData = await apiFetch(API.createFolder, 'POST', {
          parent_path: selectedFolderPath,
          folder_name: rootName,
        });
        if (!createData.success) {
          showDialog('Could not create folder: ' + createData.message, 'error');
          resetBtn();
          return;
        }
      }

      // ── Step 2: Collect unique subfolder paths from webkitRelativePath ────
      // e.g. "example/2024/file.pdf" → register sub-path "2024" under root
      const subDirSet = new Set();
      allFiles.forEach(f => {
        const parts = (f.webkitRelativePath || f.name).split('/');
        for (let d = 2; d < parts.length; d++) {
          subDirSet.add(parts.slice(1, d).join('/')); // relative to root
        }
      });
      // Shallow-first so parent folders are always created before children
      const subDirs = [...subDirSet].sort((a, b) => a.split('/').length - b.split('/').length);

      // ── Step 3: Create every subfolder in order ─────────────────────
      for (const rel of subDirs) {
        const parts      = rel.split('/');
        const folderName = parts[parts.length - 1];
        const parentPath = rootPath + (parts.length > 1 ? '/' + parts.slice(0, -1).join('/') : '');
        await apiFetch(API.createFolder, 'POST', {
          parent_path: parentPath,
          folder_name: folderName,
        });
      }

      // ── Step 4: Upload files grouped by destination subfolder ─────────
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
        const uploadErrors = [];
        for (const [folderPath, files] of byFolder) {
          setLoading('Uploading ' + done + ' / ' + allFiles.length + '...');
          const fd = new FormData();
          fd.append(CSRF_NAME, CSRF_TOKEN);
          fd.append('folder_path', folderPath);
          files.forEach(f => fd.append('folder_files[]', f));
          try {
            const res  = await fetch(API.uploadFolder, { method: 'POST', body: fd });
            const data = await res.json();
            if (!data.success) uploadErrors.push(data.message);
            else if (data.errors && data.errors.length) uploadErrors.push(...data.errors);
          } catch(e) { uploadErrors.push(folderPath + ': network error'); }
          done += files.length;
        }
        if (uploadErrors.length) {
          showDialog('Some files were skipped: ' + uploadErrors.slice(0, 3).join(', '), 'warning');
        }
      }

      // ── Done ────────────────────────────────────────────────
      resetBtn(); // Re-enable button before closing so it's never stuck in "Saving..." on next open
      closeFolderBrowserModal();
      showDialog(skipRootCreation ? 'Folder replaced successfully.' : 'Folder uploaded successfully.', 'success');
      _pendingFolderFiles    = [];
      _pendingFolderName     = '';
      _folderBrowserSaveMode = 'record';

      invalidateSearchIndex();
      invalidateMoveFolderIndex();
      invalidateFolderBrowserIndex();
      loadFolder(currentFolderPath);

    } catch (err) {
      console.error('finalizeUploadFolderToFolder error:', err);
      showDialog('Failed to save. Please try again.', 'error');
      resetBtn();
    }
  }
  function updateRecordFileLabel(input) {
    const label = document.getElementById('recordFileName');
    if (!label) return;
    
    if (input.files.length > 0) {
        const file = input.files[0];
        const sizeMB = (file.size/1024/1024).toFixed(2);
        
        label.classList.remove('hidden');
        
        // Warn about large files
        if (file.size > 5 * 1024 * 1024) { // 5MB
            label.innerHTML = `
                <div class="flex items-start gap-2 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-yellow-800">Large File Detected</p>
                        <p class="text-xs text-yellow-700 mt-1">
                            ${file.name} (${sizeMB} MB) - Upload may take time on slow connections
                        </p>
                    </div>
                </div>
            `;
        } else {
            label.innerHTML = `
                <div class="flex items-center gap-2 text-sm text-gray-700">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span class="font-medium">${file.name}</span>
                    <span class="text-gray-500">(${sizeMB} MB)</span>
                </div>
            `;
        }
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
  // Handle folder selected via webkitdirectory input.
  // Keeps ALL files with their webkitRelativePath intact so the full
  // subfolder structure is preserved when finalizeUploadFolderToFolder runs.
  function handleFolderDirSelect(input) {
    const allFiles = Array.from(input.files || []);

    // Auto-fill folder name from the first file's relative path
    let dirName = '';
    if (allFiles.length > 0) {
      dirName = (allFiles[0].webkitRelativePath || '').split('/')[0] || '';
    }

    // If no files (empty folder), try to get name from the input element's value
    if (!dirName && input.value) {
      // input.value may be C:\fakepath\foldername or just foldername
      dirName = input.value.split(/[\\\/]/).filter(Boolean).pop() || '';
    }

    if (dirName) {
      _pendingFolderName = dirName;
      const nameInput = document.getElementById('uploadFolderName');
      if (nameInput) nameInput.value = dirName;
    }

    // Store every file with its relative path intact — structure rebuilt on upload
    _pendingFolderFiles = allFiles;

    // Sync to the visible input so updateFolderFileList shows the right count
    updateFolderFileList(input);
  }
  function handleFolderDrop(event) {
    event.preventDefault();
    const zone = document.getElementById('folderDropZone');
    zone.classList.remove('border-green-600','bg-green-50');

    const items = event.dataTransfer.items;
    if (!items || items.length === 0) return;

    // Use webkitGetAsEntry to traverse folder structure (like Google Drive)
    const entries = Array.from(items)
      .map(item => item.webkitGetAsEntry ? item.webkitGetAsEntry() : null)
      .filter(Boolean);

    if (entries.length === 0) return;

    // Collect all file entries recursively, preserving relative paths
    async function traverseEntry(entry, pathPrefix) {
      if (entry.isFile) {
        return new Promise((resolve) => {
          entry.file(file => {
            // Manually set webkitRelativePath-like structure via a wrapper object
            const wrappedFile = new File([file], file.name, { type: file.type, lastModified: file.lastModified });
            Object.defineProperty(wrappedFile, 'webkitRelativePath', {
              value: pathPrefix + file.name,
              writable: false
            });
            resolve([wrappedFile]);
          }, () => resolve([]));
        });
      } else if (entry.isDirectory) {
        return new Promise((resolve) => {
          const reader = entry.createReader();
          const allEntries = [];
          function readAll() {
            reader.readEntries(async (results) => {
              if (results.length === 0) {
                // No more entries — recurse into collected entries
                const nested = await Promise.all(
                  allEntries.map(e => traverseEntry(e, pathPrefix + entry.name + '/'))
                );
                resolve(nested.flat());
              } else {
                allEntries.push(...results);
                readAll(); // readEntries may return in batches
              }
            }, () => resolve([]));
          }
          readAll();
        });
      }
      return [];
    }

    // Process all top-level dropped entries
    Promise.all(entries.map(entry => {
      // If dropped item is a directory, use its name as root
      if (entry.isDirectory) {
        return traverseEntry(entry, entry.name + '/');
      }
      // If file dropped directly, treat parent as root
      return traverseEntry(entry, '');
    })).then(results => {
      const allFiles = results.flat();
      _pendingFolderFiles = allFiles;

      // Auto-fill folder name from the first entry that is a directory
      const dirEntry = entries.find(e => e.isDirectory);
      if (dirEntry) {
        _pendingFolderName = dirEntry.name;
        const nameInput = document.getElementById('uploadFolderName');
        if (nameInput) nameInput.value = dirEntry.name;
      }

      // Show file count in the drop zone
      const zone = document.getElementById('folderDropZone');
      if (zone) {
        const countEl = zone.querySelector('p.text-sm');
        if (countEl) {
          countEl.textContent = allFiles.length > 0
            ? `${allFiles.length} file${allFiles.length !== 1 ? 's' : ''} ready to upload`
            : 'Empty folder selected';
        }
      }

      // Update file list display
      const list = document.getElementById('folderFileList');
      if (list) {
        list.innerHTML = '';
        if (allFiles.length === 0) {
          list.classList.add('hidden');
        } else {
          list.classList.remove('hidden');
          allFiles.forEach(file => {
            const sizeMB = (file.size / 1048576).toFixed(2);
            const li = document.createElement('li');
            li.className = 'flex items-center gap-2 text-sm text-gray-700 bg-gray-50 rounded-lg px-3 py-2';
            li.innerHTML = `
              <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
              <span class="flex-1 truncate">${file.webkitRelativePath || file.name}</span>
              <span class="text-gray-400 flex-shrink-0">${sizeMB} MB</span>`;
            list.appendChild(li);
          });
        }
      }
    });
  }

  // ─── Delete file ──────────────────────────────────────────────────────────
  async function fileAction_delete(filePath, menuId) {
    if (!PRIV_DELETE_RECORD) { denyAction(); return; }
    if (!confirm('Permanently delete this file? This cannot be undone.')) return;

    const data = await apiFetch(API.deleteFile, 'POST', { path: filePath });
    if (!data.success) { alert('Delete failed: ' + data.message); return; }

    document.getElementById('file-menu-' + menuId)?.classList.add('hidden');
    invalidateSearchIndex();
    invalidateMoveFolderIndex();
    loadFolder(currentFolderPath);
  }

  // ─── Delete folder ────────────────────────────────────────────────────────
  async function fileAction_deleteFolder(folderPath, menuId) {
    if (!PRIV_DELETE_FOLDER) { denyAction(); return; }
    if (!confirm('Delete this folder and ALL its contents? This cannot be undone.')) return;

    const data = await apiFetch(API.deleteFolder, 'POST', { path: folderPath });
    if (!data.success) { alert('Delete failed: ' + data.message); return; }

    document.getElementById('folder-menu-' + menuId)?.classList.add('hidden');
    invalidateSearchIndex();
    invalidateMoveFolderIndex();
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
        contentEl.innerHTML = `
            <img src="${previewUrl}"
                 alt="Preview"
                 class="max-w-full max-h-full mx-auto rounded-lg shadow-lg"/>`;

    } else if (ext === 'pdf') {
        contentEl.innerHTML = `
            <iframe
                src="${previewUrl}"
                class="w-full border-0 rounded-lg"
                style="min-height:520px;"
                title="PDF Preview">
            </iframe>`;

    } else {
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
    invalidateSearchIndex();
    invalidateMoveFolderIndex();
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
    refreshMoveFolderList();
  }
  function closeMoveModal() {
    document.getElementById('moveModal').classList.add('hidden');
    currentMoveTarget = currentMoveType = selectedMoveFolder = null;
    // Reset path preview
    const wrap = document.getElementById('movePathPreviewWrap');
    if (wrap) wrap.classList.add('hidden');
    const inp = document.getElementById('searchFolders');
    if (inp) inp.value = '';
  }
  // ── Move Modal: flat folder index (all folders, recursive) ──────────────────
  let _moveFolderIndex = null;  // [{ name, path, locationLabel }]

  async function _crawlMoveFolders(path, ancestorLabels, index) {
    try {
      const data = await apiFetch(API.listFolder + '?path=' + encodeURIComponent(path));
      if (!data.success) return;
      const folders = data.folders || [];
      folders.forEach(f => {
        const locationLabel = ancestorLabels.length
          ? 'Academic Records > ' + ancestorLabels.join(' > ')
          : 'Academic Records';
        index.push({ name: f.name, path: f.path, locationLabel });
      });
      await Promise.all(folders.map(f =>
        _crawlMoveFolders(f.path, [...ancestorLabels, f.name], index)
      ));
    } catch(e) {}
  }

  async function buildMoveFolderIndex() {
    if (_moveFolderIndex) return _moveFolderIndex;
    _moveFolderIndex = [];
    await _crawlMoveFolders('', [], _moveFolderIndex);
    return _moveFolderIndex;
  }

  function invalidateMoveFolderIndex() { _moveFolderIndex = null; }

  async function refreshMoveFolderList() {
    const container = document.getElementById('folderListMove');
    container.innerHTML = '<p class="px-4 py-6 text-sm text-gray-400 text-center">Loading folders...</p>';
    await buildMoveFolderIndex();
    renderMoveFolderResults('');
  }

  function renderMoveFolderResults(q) {
    const container = document.getElementById('folderListMove');
    container.innerHTML = '';

    if (!_moveFolderIndex) return;

    const lower = q.toLowerCase().trim();

    // Filter: show all when empty, or match partial name (case-insensitive)
    const results = lower
      ? _moveFolderIndex.filter(f => f.name.toLowerCase().includes(lower))
      : _moveFolderIndex;

    if (results.length === 0) {
      container.innerHTML = lower
        ? '<p class="px-4 py-6 text-sm text-gray-400 text-center">No folders match <strong>' + q + '</strong></p>'
        : '<p class="px-4 py-6 text-sm text-gray-400 text-center">No folders found.</p>';
      return;
    }

    results.forEach(f => {
      // Highlight matched portion in folder name
      let displayName = f.name;
      if (lower) {
        const idx = f.name.toLowerCase().indexOf(lower);
        if (idx !== -1) {
          displayName =
            f.name.slice(0, idx) +
            '<span class="bg-yellow-200 text-gray-900 rounded px-0.5 font-semibold">' +
            f.name.slice(idx, idx + q.length) +
            '</span>' +
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
          <svg class="w-5 h-5 text-green-700" fill="currentColor" viewBox="0 0 24 24">
            <path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/>
          </svg>
        </div>
        <div class="flex-1 min-w-0">
          <p class="font-medium text-gray-800 truncate text-sm">${displayName}</p>
          <p class="text-xs text-gray-400 truncate mt-0.5">${f.locationLabel}</p>
        </div>
        ${isSelected ? '<svg class="w-4 h-4 text-green-700 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>' : ''}`;

      btn.addEventListener('click', () => {
        // Deselect all
        document.querySelectorAll('.folder-move-item').forEach(i => {
          i.classList.remove('bg-green-50', 'border-green-700');
          i.classList.add('border-transparent');
          const chk = i.querySelector('svg.text-green-700:last-child');
          if (chk && chk !== i.querySelector('div svg')) chk.remove();
        });
        // Select this one
        btn.classList.add('bg-green-50', 'border-green-700');
        btn.classList.remove('border-transparent');
        selectedMoveFolder = f.path;

        // Show selected path in footer
        const pathPreview = document.getElementById('moveSelectedPath');
        if (pathPreview) {
          pathPreview.textContent = f.locationLabel + ' > ' + f.name;
          pathPreview.closest('#movePathPreviewWrap').classList.remove('hidden');
        }
      });

      container.appendChild(btn);
    });
  }

  let _moveSearchDebounce = null;
  function filterFoldersInMoveModal() {
    clearTimeout(_moveSearchDebounce);
    _moveSearchDebounce = setTimeout(() => {
      const q = document.getElementById('searchFolders').value;
      renderMoveFolderResults(q);
    }, 150);
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
    invalidateSearchIndex();
    invalidateMoveFolderIndex();
    loadFolder(currentFolderPath);
  }

  // ─── Init ─────────────────────────────────────────────────────────────────
  renderBreadcrumb();
  loadFolder(''); // Initial load from disk

  // Pre-warm the search index in the background so the first search is instant
  setTimeout(() => buildSearchIndex(), 1500);

</script>

<script>
/* ── PHP → JS privilege flags ── */
var PRIV_UPLOAD     = <?= json_encode((bool)($priv_records_upload   ?? false)) ?>;
var PRIV_VIEW       = <?= json_encode((bool)($priv_files_view       ?? false)) ?>;
var PRIV_UPDATE     = <?= json_encode((bool)($priv_records_update   ?? false)) ?>;
var PRIV_ORGANIZE   = <?= json_encode((bool)($priv_records_organize ?? false)) ?>;
var PRIV_ADD_FOLDER = <?= json_encode((bool)($priv_folders_add      ?? false)) ?>;
var PRIV_DEL_FILE   = <?= json_encode((bool)($priv_records_delete   ?? false)) ?>;
var PRIV_DEL_FOLDER = <?= json_encode((bool)($priv_folders_delete   ?? false)) ?>;

/* ── Show/hide permission denied modal ── */
function showPermissionDenied() {
  var modal = document.getElementById('permissionDeniedModal');
  if (modal) modal.classList.remove('hidden');
}
function closePermissionDeniedModal() {
  var modal = document.getElementById('permissionDeniedModal');
  if (modal) modal.classList.add('hidden');
}

/* Close on backdrop click */
document.addEventListener('click', function(e) {
  var modal = document.getElementById('permissionDeniedModal');
  if (modal && e.target === modal) modal.classList.add('hidden');
});

/* ── Helper: PHP renders empty <div> when no permission ── */
function isModalReal(id) {
  var el = document.getElementById(id);
  return el && el.children.length > 0;
}

/* ── Wrap action functions AFTER page scripts have loaded ── */
window.addEventListener('load', function() {

  /* New Folder */
  var _openNewFolderModal = window.openNewFolderModal;
  window.openNewFolderModal = function() {
    if (!PRIV_ADD_FOLDER || !isModalReal('newFolderModal')) { showPermissionDenied(); return; }
    if (_openNewFolderModal) _openNewFolderModal();
  };

  /* Upload Record */
  var _openUploadModal = window.openUploadModal;
  window.openUploadModal = function() {
    if (!PRIV_UPLOAD || !isModalReal('uploadModal')) { showPermissionDenied(); return; }
    if (_openUploadModal) _openUploadModal();
  };

  /* Upload Folder */
  var _openUploadFolderModal = window.openUploadFolderModal;
  window.openUploadFolderModal = function() {
    if (!PRIV_UPLOAD || !isModalReal('uploadFolderModal')) { showPermissionDenied(); return; }
    if (_openUploadFolderModal) _openUploadFolderModal();
  };

  /* Rename */
  var _openRenameModal = window.openRenameModal;
  window.openRenameModal = function() {
    if (!PRIV_ORGANIZE || !isModalReal('renameModal')) { showPermissionDenied(); return; }
    if (_openRenameModal) _openRenameModal.apply(this, arguments);
  };

  /* Move */
  var _openMoveModal = window.openMoveModal;
  window.openMoveModal = function() {
    if (!PRIV_ORGANIZE || !isModalReal('moveModal')) { showPermissionDenied(); return; }
    if (_openMoveModal) _openMoveModal.apply(this, arguments);
  };

  /* Preview */
  var _openPreviewModal = window.openPreviewModal;
  window.openPreviewModal = function() {
    if (!PRIV_VIEW) { showPermissionDenied(); return; }
    if (_openPreviewModal) _openPreviewModal.apply(this, arguments);
  };

  /* Delete file */
  var _deleteFile = window.deleteFile;
  window.deleteFile = function() {
    if (!PRIV_DEL_FILE) { showPermissionDenied(); return; }
    if (_deleteFile) _deleteFile.apply(this, arguments);
  };

  /* Delete folder */
  var _deleteFolder = window.deleteFolder;
  window.deleteFolder = function() {
    if (!PRIV_DEL_FOLDER) { showPermissionDenied(); return; }
    if (_deleteFolder) _deleteFolder.apply(this, arguments);
  };

  /* Download */
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

/* ── Intercept AJAX responses from backend ── */
/* NOTE: We do NOT override window.fetch globally to avoid breaking
   the page. Instead we patch the specific functions that make fetch calls. */

/* Patch createFolder response handling */
var _origCreateFolder = window.createFolder;
if (typeof _origCreateFolder === 'function') {
  window.createFolder = function() {
    if (!PRIV_ADD_FOLDER) { showPermissionDenied(); return; }
    _origCreateFolder.apply(this, arguments);
  };
}

/* Download link guard — block <a href> clicks when view is off */
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
</script>

<?= $this->endSection() ?>