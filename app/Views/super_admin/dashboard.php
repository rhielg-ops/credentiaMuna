<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Super Admin Dashboard - CredentiaTAU</title>
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
      <div class="flex items-center gap-3 p-6 border-b border-gray-200">
        <div class="w-12 h-12 bg-green-700 rounded-full flex items-center justify-center overflow-hidden">
          <img src="<?= base_url('assets/img/TAU.png'); ?>" alt="TAU Logo" class="w-10 h-10">
        </div>
        <div>
          <h1 class="text-lg font-bold text-gray-800">CredentiaTAU</h1>
          <p class="text-sm text-gray-500">Super Admin Portal</p>
        </div>
      </div>

      <nav class="p-4">
        <a href="<?= base_url('super-admin/dashboard'); ?>" class="w-full flex items-center gap-3 px-4 py-3 text-green-700 bg-green-50 rounded-lg font-medium mb-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
          </svg>
          <span>Dashboard</span>
        </a>
        <a href="<?= base_url('super-admin/user-management'); ?>" class="w-full flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg font-medium mb-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
          </svg>
          <span>User Management</span>
        </a>
        <a href="<?= base_url('super-admin/all-records'); ?>" class="w-full flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg font-medium mb-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
          </svg>
          <span>All Records</span>
        </a>
        <a href="<?= base_url('super-admin/system-backup'); ?>" class="w-full flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg font-medium mb-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>
          </svg>
          <span>System Backup</span>
        </a>
        <a href="<?= base_url('super-admin/settings'); ?>" class="w-full flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg font-medium">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
          </svg>
          <span>Settings</span>
        </a>
      </nav>

      <!-- Super Admin Badge -->
      <div class="mx-4 mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
        <div class="flex items-center gap-2 mb-2">
          <svg class="w-5 h-5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
          </svg>
          <span class="font-bold text-green-800">Super Admin</span>
        </div>
        <p class="text-xs text-green-700">You have full system access and control</p>
      </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 overflow-auto">
      <!-- Header -->
      <div class="bg-white border-b border-gray-200 px-8 py-4">
        <div class="flex items-center justify-end">
          <div class="flex items-center gap-3">
            <div class="text-right">
              <p class="text-sm font-semibold text-gray-800"><?= esc($email); ?></p>
              <p class="text-xs text-gray-500">Super Admin</p>
            </div>
            <div class="w-10 h-10 bg-green-700 rounded-full flex items-center justify-center text-white font-bold">
              SA
            </div>
            <a href="<?= base_url('auth/logout'); ?>" class="ml-4 text-sm text-red-600 hover:text-red-800">Logout</a>
          </div>
        </div>
      </div>

      <!-- Dashboard Content -->
      <div class="p-8">
        <!-- Page Header -->
        <div class="bg-green-700 text-white p-8 rounded-xl mb-6">
          <h2 class="text-3xl font-bold mb-2">Super Admin Dashboard</h2>
          <p class="text-green-100">System management and oversight</p>
        </div>

        <!-- Tabs -->
        <div class="flex gap-2 mb-6 border-b border-gray-200">
          <button class="px-4 py-2 bg-green-700 text-white rounded-t-lg font-medium">Overview</button>
          <button onclick="window.location.href='<?= base_url('super-admin/all-records'); ?>'" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-t-lg font-medium">Academic Records</button>
          <button onclick="window.location.href='<?= base_url('super-admin/user-management'); ?>'" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-t-lg font-medium">User Management</button>
          <button class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-t-lg font-medium">System Settings</button>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <!-- Total Records -->
          <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
              <span class="text-gray-600 font-medium">Total Records</span>
              <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </div>
            </div>
            <p class="text-3xl font-bold text-gray-800 mb-1">1,247</p>
            <p class="text-sm text-gray-500">Academic documents</p>
          </div>

          <!-- Total Admins -->
          <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
              <span class="text-gray-600 font-medium">Total Admins</span>
              <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
              </div>
            </div>
            <p class="text-3xl font-bold text-gray-800 mb-1">8</p>
            <p class="text-sm text-gray-500">6 active</p>
          </div>

          <!-- Recent Activity -->
          <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
              <span class="text-gray-600 font-medium">Recent Activity</span>
              <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-orange-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
              </div>
            </div>
            <p class="text-3xl font-bold text-gray-800 mb-1">23</p>
            <p class="text-sm text-gray-500">actions today</p>
          </div>

          <!-- Pending Approvals -->
          <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
              <span class="text-gray-600 font-medium">Pending Staff</span>
              <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-yellow-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
            </div>
            <p class="text-3xl font-bold text-gray-800 mb-1">2</p>
            <p class="text-sm text-gray-500">awaiting approval</p>
          </div>
        </div>

        <!-- Recent System Activity -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
          <div class="flex items-center gap-2 mb-6">
            <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
            <h3 class="text-xl font-bold text-gray-800">Recent System Activity</h3>
          </div>

          <div class="space-y-4">
            <!-- Activity Item -->
            <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-lg">
              <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                </svg>
              </div>
              <div class="flex-1">
                <p class="font-semibold text-gray-800">Audit logging system initialized</p>
                <p class="text-sm text-gray-500">by System</p>
              </div>
              <div class="text-right text-sm text-gray-500">
                <p>Just now</p>
                <p>10:46 AM</p>
              </div>
            </div>

            <!-- More activity items can be added here -->
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>