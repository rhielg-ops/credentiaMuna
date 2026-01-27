<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Academic Records - CredentiaTAU</title>
  <link rel="icon" href="<?= base_url('assets/img/TAU.png'); ?>">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
    body { font-family: 'Poppins', sans-serif; }
    
    /* Floating button animation */
    .fab-button {
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .fab-button:hover {
      transform: scale(1.1);
      box-shadow: 0 8px 25px rgba(22, 163, 74, 0.4);
    }
    
    /* Menu animation */
    .fab-menu {
      animation: slideUp 0.3s ease-out;
    }
    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>
<body class="bg-gray-50">
  <div class="flex h-screen">
    <!-- Sidebar -->
    <div class="w-64 bg-white border-r border-gray-200">
      <div class="flex items-center gap-3 p-6 border-b border-gray-200">
        <div class="w-12 h-12 bg-green-700 rounded-full flex items-center justify-center overflow-hidden">
          <img src="<?= base_url('assets/img/TAU.png'); ?>" alt="TAU Logo" class="w-10 h-10">
        </div>
        <div>
          <h1 class="text-lg font-bold text-gray-800">CredentiaTAU</h1>
          <p class="text-sm text-gray-500">Admin Portal</p>
        </div>
      </div>

      <nav class="p-4">
        <a href="<?= base_url('dashboard'); ?>" class="w-full flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg font-medium mb-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
          </svg>
          <span>Dashboard</span>
        </a>
        <a href="<?= base_url('academic-records'); ?>" class="w-full flex items-center gap-3 px-4 py-3 text-green-700 bg-green-50 rounded-lg font-medium mb-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
          </svg>
          <span>Academic Records</span>
        </a>
        <a href="<?= base_url('settings'); ?>" class="w-full flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg font-medium">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
          </svg>
          <span>Settings</span>
        </a>
      </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1 overflow-auto relative">
      <!-- Header -->
      <div class="bg-white border-b border-gray-200 px-8 py-4">
        <div class="flex items-center justify-end">
          <div class="flex items-center gap-3">
            <div class="text-right">
              <p class="text-sm font-semibold text-gray-800"><?= esc($email ?? 'Admin User'); ?></p>
              <p class="text-xs text-gray-500"><?= esc(ucfirst($role ?? 'admin')); ?></p>
            </div>
            <div class="w-10 h-10 bg-green-700 rounded-full flex items-center justify-center text-white font-bold">
              <?= strtoupper(substr($email ?? 'AU', 0, 2)); ?>
            </div>
            <a href="<?= base_url('auth/logout'); ?>" class="ml-4 text-sm text-red-600 hover:text-red-800">Logout</a>
          </div>
        </div>
      </div>

      <!-- Content -->
      <div class="p-8">
        <!-- Page Title -->
        <div class="flex items-center justify-between mb-6">
          <div>
            <h2 class="text-3xl font-bold text-gray-800 mb-2">Academic Records</h2>
            <p class="text-gray-600">Manage student transcripts and documents</p>
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
              oninput="filterRecords()"
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

        <!-- Student Folders (Sample Data) -->
        <div id="recordsContainer">
          <!-- Section A -->
          <div class="mb-8 folder-section" data-letter="A">
            <div class="bg-green-700 text-white px-4 py-2 rounded-lg mb-4 font-bold text-lg">A</div>
            
            <div class="student-folder mb-6" data-name="Ana Bautista" data-id="2020-11111">
              <div class="flex items-center justify-between mb-3">
                <div>
                  <h3 class="text-lg font-bold text-gray-800">Ana Bautista</h3>
                  <p class="text-sm text-gray-500">ID: 2020-11111</p>
                </div>
                <button
                  onclick="viewFolderRecords('Ana Bautista', '2020-11111')"
                  class="text-green-700 border border-green-700 px-4 py-2 rounded-lg hover:bg-green-50 font-medium"
                >
                  View All Records
                </button>
              </div>

              <div id="gridView" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Transcript -->
                <div class="record-card bg-white rounded-xl p-4 shadow-sm border border-gray-100" data-type="transcript">
                  <div class="flex items-start justify-between mb-3">
                    <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                      <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                      </svg>
                    </div>
                    <button class="text-gray-400 hover:text-gray-600">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                      </svg>
                    </button>
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

                <!-- Diploma -->
                <div class="record-card bg-white rounded-xl p-4 shadow-sm border border-gray-100" data-type="diploma">
                  <div class="flex items-start justify-between mb-3">
                    <div class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center">
                      <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M12 14l9-5-9-5-9 5 9 5z"></path>
                        <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                      </svg>
                    </div>
                    <button class="text-gray-400 hover:text-gray-600">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                      </svg>
                    </button>
                  </div>
                  <h4 class="font-medium text-gray-800 mb-1">Ana Bautista</h4>
                  <p class="text-xs text-gray-500 mb-2">2020-11111</p>
                  <div class="flex items-center gap-2 mb-2">
                    <span class="text-sm font-medium text-gray-700">Diploma</span>
                    <span class="px-2 py-0.5 bg-gray-200 text-gray-700 text-xs rounded">archived</span>
                  </div>
                  <div class="flex items-center justify-between text-xs text-gray-500">
                    <span>2022-2023</span>
                    <span>1.9 MB</span>
                  </div>
                </div>

                <!-- Certificate -->
                <div class="record-card bg-white rounded-xl p-4 shadow-sm border border-gray-100" data-type="certificate">
                  <div class="flex items-start justify-between mb-3">
                    <div class="w-10 h-10 bg-yellow-50 rounded-lg flex items-center justify-center">
                      <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                      </svg>
                    </div>
                    <button class="text-gray-400 hover:text-gray-600">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                      </svg>
                    </button>
                  </div>
                  <h4 class="font-medium text-gray-800 mb-1">Ana Bautista</h4>
                  <p class="text-xs text-gray-500 mb-2">2020-11111</p>
                  <div class="flex items-center gap-2 mb-2">
                    <span class="text-sm font-medium text-gray-700">Certificate</span>
                    <span class="px-2 py-0.5 bg-gray-200 text-gray-700 text-xs rounded">archived</span>
                  </div>
                  <div class="flex items-center justify-between text-xs text-gray-500">
                    <span>2022-2023</span>
                    <span>1.2 MB</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Floating Action Button (FAB) like Google Drive -->
      <div class="fixed bottom-8 right-8 z-50">
        <!-- FAB Menu (Hidden by default) -->
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

        <!-- Main FAB Button -->
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
    </div>
  </div>

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

      <form id="uploadForm" action="<?= base_url('academic-records/upload'); ?>" method="post" enctype="multipart/form-data">
        <!-- Organize in Folder -->
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
          <div class="flex items-center justify-between mb-2">
            <span class="text-green-800 font-medium flex items-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
              </svg>
              Organize in Folder *
            </span>
            <button
              type="button"
              onclick="openNewFolderModal()"
              class="text-green-700 border border-green-700 px-3 py-1 rounded text-sm hover:bg-green-100"
            >
              New Folder
            </button>
          </div>
          <p class="text-sm text-gray-600 mb-2">No folders yet. Click "New Folder" to create one.</p>
        </div>

        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Student Name *</label>
            <input
              type="text"
              name="student_name"
              placeholder="Juan Dela Cruz"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Student ID *</label>
            <input
              type="text"
              name="student_id"
              placeholder="2021-12345"
              required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
/>
</div>
<div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Record Type *</label>
        <select
          name="record_type"
          required
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
        >
          <option value="">Select type</option>
          <option value="Transcript">Transcript</option>
          <option value="Diploma">Diploma</option>
          <option value="Certificate">Certificate</option>
          <option value="Grades">Grades</option>
        </select>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Academic Year *</label>
        <input
          type="text"
          name="academic_year"
          placeholder="2024-2025"
          required
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
        />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Upload File * (PDF, DOC, DOCX - Max 10MB)</label>
        <input
          type="file"
          name="record_file"
          accept=".pdf,.doc,.docx"
          required
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
        />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
        <textarea
          name="notes"
          placeholder="Additional information..."
          rows="3"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
        ></textarea>
      </div>
    </div>

    <button
      type="submit"
      class="w-full mt-6 bg-green-700 text-white px-6 py-3 rounded-lg hover:bg-green-800 font-semibold"
    >
      Upload Record
    </button>
  </form>
</div>
</div>
  <script>
    let currentView = 'grid';
    let currentFilter = 'All Records';
    let fabMenuOpen = false;

    // Toggle FAB Menu
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

    // Close FAB menu when clicking outside
    document.addEventListener('click', function(event) {
      const fabButton = document.getElementById('fabButton');
      const fabMenu = document.getElementById('fabMenu');
      
      if (!fabButton.contains(event.target) && !fabMenu.contains(event.target)) {
        if (fabMenuOpen) {
          toggleFabMenu();
        }
      }
    });

    function toggleFilterDropdown() {
      const dropdown = document.getElementById('filterDropdown');
      dropdown.classList.toggle('hidden');
    }

    function setFilter(filter) {
      currentFilter = filter;
      document.getElementById('filterText').textContent = filter;
      toggleFilterDropdown();
      filterRecords();
    }

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
    }

    function filterRecords() {
      const searchQuery = document.getElementById('searchInput').value.toLowerCase();
      const folders = document.querySelectorAll('.student-folder');
      
      folders.forEach(folder => {
        const name = folder.dataset.name.toLowerCase();
        const id = folder.dataset.id.toLowerCase();
        const matchesSearch = name.includes(searchQuery) || id.includes(searchQuery);
        
        if (currentFilter === 'All Records') {
          folder.style.display = matchesSearch ? 'block' : 'none';
        } else {
          const hasMatchingRecord = folder.querySelector(`.record-card[data-type="${currentFilter.toLowerCase().slice(0, -1)}"]`);
          folder.style.display = (matchesSearch && hasMatchingRecord) ? 'block' : 'none';
        }
      });
    }

    function openNewFolderModal() {
      document.getElementById('newFolderModal').classList.remove('hidden');
      if (fabMenuOpen) toggleFabMenu();
    }

    function closeNewFolderModal() {
      document.getElementById('newFolderModal').classList.add('hidden');
      document.getElementById('newFolderName').value = '';
    }

    function createFolder() {
      const folderName = document.getElementById('newFolderName').value;
      if (folderName.trim()) {
        alert('Folder created for: ' + folderName);
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

    function viewFolderRecords(name, id) {
      alert('Viewing all records for ' + name + ' (' + id + ')');
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
      const filterButton = document.getElementById('filterButton');
      const filterDropdown = document.getElementById('filterDropdown');
      
      if (!filterButton.contains(event.target) && !filterDropdown.contains(event.target)) {
        filterDropdown.classList.add('hidden');
      }
    });
  </script>
</body>
</html>
```