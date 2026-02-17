<?= $this->extend('layouts/auth_layout') ?>

<?= $this->section('content') ?>

<!-- Page Title -->
<div class="flex items-center justify-between mb-6">
  <div>
    <h2 class="text-3xl font-bold text-gray-800 mb-2">Academic Records</h2>
    <p class="text-gray-600">Manage student records</p>
  </div>
</div>

<!-- Search and Filters -->
<div class="flex items-center gap-4 mb-6">
  <div class="flex-1 relative">
    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
    </svg>
    <input
      type="text"
      id="searchInput"
      placeholder="Search by student name or ID..."
      class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
    />
  </div>

  <div class="relative">
    <button
      id="filterButton"
      onclick="toggleFilterDropdown()"
      class="flex items-center gap-2 px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-50"
    >
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
      </svg>
      <span id="filterText">All Records</span>
    </button>
    
    <div id="filterDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-10">
      <button onclick="setFilter('All Records')" class="w-full text-left px-4 py-2 hover:bg-gray-50">All Records</button>
      <button onclick="setFilter('Transcripts')" class="w-full text-left px-4 py-2 hover:bg-gray-50">Transcripts</button>
      <button onclick="setFilter('Diplomas')" class="w-full text-left px-4 py-2 hover:bg-gray-50">Diplomas</button>
      <button onclick="setFilter('Certificates')" class="w-full text-left px-4 py-2 hover:bg-gray-50">Certificates</button>
      <button onclick="setFilter('Grades')" class="w-full text-left px-4 py-2 hover:bg-gray-50">Grades</button>
    </div>
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

<!-- Student Folders -->
<div id="recordsContainer">
  <!-- Section A -->
  <div class="mb-8 folder-section">
    <div class="bg-green-700 text-white px-4 py-2 rounded-lg mb-4 font-bold text-lg">A</div>
    
    <div class="student-folder mb-6">
      <div class="flex items-center justify-between mb-3">
        <div>
          <h3 class="text-lg font-bold text-gray-800">Ana Bautista</h3>
          <p class="text-sm text-gray-500">ID: 2020-11111</p>
        </div>
        <button class="text-green-700 border border-green-700 px-4 py-2 rounded-lg hover:bg-green-50 font-medium">
          View All Records
        </button>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <!-- Sample Record Cards -->
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
          <div class="flex items-start justify-between mb-3">
            <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
            </div>
          </div>
          <h4 class="font-medium text-gray-800 mb-1">Ana Bautista</h4>
          <p class="text-xs text-gray-500 mb-2">2020-11111</p>
          <div class="flex items-center gap-2 mb-2">
            <span class="text-sm font-medium text-gray-700">Transcript</span>
            <span class="px-2 py-0.5 bg-gray-200 text-gray-700 text-xs rounded">archived</span>
          </div>
          <div class="flex items-center justify-between text-xs text-gray-500">
            <span>2022-2023</span>
            <span>2.5 MB</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('fab') ?>
<!-- Floating Action Button -->
<div class="fixed bottom-8 right-8 z-50">
  <div id="fabMenu" class="hidden fab-menu absolute bottom-20 right-0 bg-white rounded-lg shadow-xl border border-gray-200 py-2 w-56">
    <button
      onclick="openNewFolderModal()"
      class="w-full flex items-center gap-3 px-4 py-3 hover:bg-gray-50 text-left"
    >
      <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
      </svg>
      <span class="font-medium text-gray-700">New Folder</span>
    </button>
    <hr class="my-1">
    <button
      onclick="openUploadModal()"
      class="w-full flex items-center gap-3 px-4 py-3 hover:bg-gray-50 text-left"
    >
      <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
      </svg>
      <span class="font-medium text-gray-700">Upload Record</span>
    </button>
  </div>

  <button
    id="fabButton"
    onclick="toggleFabMenu()"
    class="fab-button w-16 h-16 bg-green-700 hover:bg-green-800 text-white rounded-full shadow-lg flex items-center justify-center"
  >
    <svg id="fabIcon" class="w-8 h-8 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
    </svg>
  </button>
</div>
<?= $this->endSection() ?>

<?= $this->section('modals') ?>
<!-- New Folder Modal -->
<div id="newFolderModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
  <div class="bg-white rounded-xl p-6 w-full max-w-md">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-xl font-bold text-gray-800">Create New Folder</h3>
      <button onclick="closeNewFolderModal()" class="text-gray-400 hover:text-gray-600">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
    </div>
    <div class="mb-4">
      <label class="block text-sm font-medium text-gray-700 mb-2">Student Name *</label>
      <input
        type="text"
        id="newFolderName"
        placeholder="Enter student name"
        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
      />
    </div>
    <div class="flex gap-3">
      <button
        onclick="closeNewFolderModal()"
        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
      >
        Cancel
      </button>
      <button
        onclick="createFolder()"
        class="flex-1 px-4 py-2 bg-green-700 text-white rounded-lg hover:bg-green-800"
      >
        Create Folder
      </button>
    </div>
  </div>
</div>

<!-- Upload Record Modal -->
<div id="uploadModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-xl p-6 w-full max-w-lg max-h-[90vh] overflow-y-auto">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h3 class="text-xl font-bold text-gray-800">Upload Academic Record</h3>
        <p class="text-sm text-gray-500">Add a new academic record to the system</p>
      </div>
      <button onclick="closeUploadModal()" class="text-gray-400 hover:text-gray-600">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
    </div>

    <form>
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Student Name *</label>
          <input type="text" placeholder="Juan Dela Cruz" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Student ID *</label>
          <input type="text" placeholder="2021-12345" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Record Type *</label>
          <select required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            <option value="">Select type</option>
            <option value="Transcript">Transcript</option>
            <option value="Diploma">Diploma</option>
            <option value="Certificate">Certificate</option>
            <option value="Grades">Grades</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Academic Year *</label>
          <input type="text" placeholder="2024-2025" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Upload File *</label>
          <input type="file" accept=".pdf,.doc,.docx" required class="w-full px-4 py-2 border border-gray-300 rounded-lg" />
        </div>
      </div>
      <button type="submit" class="w-full mt-6 bg-green-700 text-white px-6 py-3 rounded-lg hover:bg-green-800 font-semibold">
        Upload Record
      </button>
    </form>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
  let fabMenuOpen = false;

  function toggleFabMenu() {
    fabMenuOpen = !fabMenuOpen;
    const menu = document.getElementById('fabMenu');
    const icon = document.getElementById('fabIcon');
    
    if (fabMenuOpen) {
      menu.classList.remove('hidden');
      icon.style.transform = 'rotate(45deg)';
    } else {
      menu.classList.add('hidden');
      icon.style.transform = 'rotate(0deg)';
    }
  }

  function toggleFilterDropdown() {
    document.getElementById('filterDropdown').classList.toggle('hidden');
  }

  function setFilter(filter) {
    document.getElementById('filterText').textContent = filter;
    toggleFilterDropdown();
  }

  function setView(view) {
    const gridBtn = document.getElementById('gridViewBtn');
    const listBtn = document.getElementById('listViewBtn');
    
    if (view === 'grid') {
      gridBtn.classList.add('bg-green-700', 'text-white');
      gridBtn.classList.remove('bg-white', 'border');
      listBtn.classList.remove('bg-green-700', 'text-white');
      listBtn.classList.add('bg-white', 'border', 'border-gray-300');
    } else {
      listBtn.classList.add('bg-green-700', 'text-white');
      listBtn.classList.remove('bg-white', 'border');
      gridBtn.classList.remove('bg-green-700', 'text-white');
      gridBtn.classList.add('bg-white', 'border', 'border-gray-300');
    }
  }

  function openNewFolderModal() {
    document.getElementById('newFolderModal').classList.remove('hidden');
    if (fabMenuOpen) toggleFabMenu();
  }

  function closeNewFolderModal() {
    document.getElementById('newFolderModal').classList.add('hidden');
  }

  function createFolder() {
    const name = document.getElementById('newFolderName').value;
    if (name) {
      alert('Folder created: ' + name);
      closeNewFolderModal();
    }
  }

  function openUploadModal() {
    document.getElementById('uploadModal').classList.remove('hidden');
    if (fabMenuOpen) toggleFabMenu();
  }

  function closeUploadModal() {
    document.getElementById('uploadModal').classList.add('hidden');
  }

  document.addEventListener('click', function(event) {
    const fabButton = document.getElementById('fabButton');
    const fabMenu = document.getElementById('fabMenu');
    
    if (!fabButton.contains(event.target) && !fabMenu.contains(event.target)) {
      if (fabMenuOpen) toggleFabMenu();
    }
  });
</script>
<?= $this->endSection() ?>