<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CredentiaTAU - Admin Dashboard</title>
  <link rel="icon" href="<?= base_url('assets/img/TAU.png'); ?>">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
    body { font-family: 'Poppins', sans-serif; }
  </style>
</head>
<body class="bg-gray-50">
  <div class="flex h-screen">
    <!-- Sidebar -->
    <div class="w-64 bg-white border-r border-gray-200">
      <!-- Logo Section -->
      <div class="flex items-center gap-3 p-6 border-b border-gray-200">
        <div class="w-12 h-12 bg-green-700 rounded-full flex items-center justify-center overflow-hidden">
          <img src="<?= base_url('assets/img/TAU.png'); ?>" alt="TAU Logo" class="w-10 h-10">
        </div>
        <div>
          <h1 class="text-lg font-bold text-gray-800">CredentiaTAU</h1>
          <p class="text-sm text-gray-500">Admin Portal</p>
        </div>
      </div>

      <!-- Navigation -->
      <nav class="p-4">
        <div class="mb-2">
          <a href="<?= base_url('dashboard'); ?>" class="w-full flex items-center gap-3 px-4 py-3 text-green-700 bg-green-50 rounded-lg font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span>Dashboard</span>
          </a>
        </div>
        <div class="mb-2">
  <a href="<?= base_url('academic-records'); ?>" class="w-full flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg font-medium">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
    </svg>
    <span>Academic Records</span>
  </a>
</div>
        <div class="mb-2">
          <a href="<?= base_url('settings'); ?>" class="w-full flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <span>Settings</span>
          </a>
        </div>
      </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1 overflow-auto">
      <!-- Header -->
      <div class="bg-white border-b border-gray-200 px-8 py-4">
        <div class="flex items-center justify-end">
          <div class="flex items-center gap-3">
            <div class="text-right">
              <p class="text-sm font-semibold text-gray-800"><?= esc($email); ?></p>
              <p class="text-xs text-gray-500"><?= esc(ucfirst($role)); ?></p>
            </div>
            <div class="w-10 h-10 bg-green-700 rounded-full flex items-center justify-center text-white font-bold">
              <?= strtoupper(substr($email, 0, 2)); ?>
            </div>
            <a href="<?= base_url('auth/logout'); ?>" class="ml-4 text-sm text-red-600 hover:text-red-800">Logout</a>
          </div>
        </div>
      </div>

      <!-- Dashboard Content -->
      <div class="p-8">
        <!-- Title -->
        <div class="mb-8">
          <h2 class="text-3xl font-bold text-gray-800 mb-2">Dashboard Overview</h2>
          <p class="text-gray-600">Academic records and enrollment statistics</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
          <!-- Total Students -->
          <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
              <span class="text-gray-600 font-medium">Total Students</span>
              <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
              </svg>
            </div>
            <p class="text-3xl font-bold text-gray-800 mb-1">6</p>
            <p class="text-sm text-gray-500">Unique students</p>
          </div>

          <!-- Transcripts -->
          <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
              <span class="text-gray-600 font-medium">Transcripts</span>
              <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
            </div>
            <p class="text-3xl font-bold text-gray-800 mb-1">6</p>
            <p class="text-sm text-gray-500">Total records</p>
          </div>

          <!-- Diplomas -->
          <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
              <span class="text-gray-600 font-medium">Diplomas</span>
              <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M12 14l9-5-9-5-9 5 9 5z"></path>
                <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path>
              </svg>
            </div>
            <p class="text-3xl font-bold text-gray-800 mb-1">6</p>
            <p class="text-sm text-gray-500">Total records</p>
          </div>

          <!-- Certificates -->
          <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
              <span class="text-gray-600 font-medium">Certificates</span>
              <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
              </svg>
            </div>
            <p class="text-3xl font-bold text-gray-800 mb-1">6</p>
            <p class="text-sm text-gray-500">Total records</p>
          </div>

          <!-- Grades -->
          <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
              <span class="text-gray-600 font-medium">Grades</span>
              <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
              </svg>
            </div>
            <p class="text-3xl font-bold text-gray-800 mb-1">6</p>
            <p class="text-sm text-gray-500">Total records</p>
          </div>
        </div>

        <!-- Coming Soon Message -->
        <div class="bg-white rounded-xl p-12 shadow-sm border border-gray-100 text-center">
          <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
          </svg>
          <h3 class="text-2xl font-bold text-gray-800 mb-2">Charts & Analytics Coming Soon</h3>
          <p class="text-gray-600">Student enrollment trends and statistics will be displayed here.</p>
        </div>
      </div>
    </div>
  </div>
</body>
</html>