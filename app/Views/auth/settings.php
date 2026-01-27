<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Settings - CredentiaTAU</title>
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
        <a href="<?= base_url('academic-records'); ?>" class="w-full flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg font-medium mb-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
          </svg>
          <span>Academic Records</span>
        </a>
        <a href="<?= base_url('settings'); ?>" class="w-full flex items-center gap-3 px-4 py-3 text-green-700 bg-green-50 rounded-lg font-medium">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
          </svg>
          <span>Settings</span>
        </a>
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

      <!-- Content -->
      <div class="p-8">
        <!-- Page Header -->
        <div class="bg-green-700 text-white p-8 rounded-xl mb-6">
          <h2 class="text-3xl font-bold mb-2">Admin Profile</h2>
          <p class="text-green-100">View your account information</p>
        </div>

        <!-- Info Notice -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6 flex items-start gap-3">
          <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <p class="text-blue-800 text-sm">Your profile is managed by the Super Admin. Contact your administrator to request changes.</p>
        </div>

        <!-- Profile Information Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
          <div class="flex items-center gap-3 mb-6">
            <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <div>
              <h3 class="text-xl font-bold text-gray-800">Profile Information</h3>
              <p class="text-sm text-gray-500">Your account details (read-only)</p>
            </div>
          </div>

          <div class="space-y-4">
            <!-- Full Name -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
              <input
                type="text"
                value="<?= esc($full_name); ?>"
                disabled
                class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-600 cursor-not-allowed"
              />
            </div>

            <!-- Email Address -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
              <div class="relative">
                <span class="absolute left-3 top-1/2 transform -translate-y-1/2">
                  <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                  </svg>
                </span>
                <input
                  type="email"
                  value="<?= esc($email); ?>"
                  disabled
                  class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-600 cursor-not-allowed"
                />
              </div>
            </div>

            <!-- Role -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
              <div class="flex gap-2">
                <span class="inline-flex items-center px-4 py-2 bg-green-100 text-green-800 rounded-lg border border-green-200 font-medium">
                  <?= esc(ucfirst($role)); ?>
                </span>
                <span class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-800 rounded-lg border border-blue-200 font-medium">
                  full access
                </span>
              </div>
            </div>

            <!-- User ID -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">User ID</label>
              <input
                type="text"
                value="<?= esc($user_id); ?>"
                disabled
                class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-600 cursor-not-allowed"
              />
            </div>
          </div>
        </div>

        <!-- Account Status Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
          <div class="flex items-center gap-3 mb-4">
            <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
            </svg>
            <h3 class="text-xl font-bold text-gray-800">Account Status</h3>
          </div>

          <div class="bg-green-50 border border-green-200 rounded-lg p-4 flex items-center gap-4">
            <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
              </svg>
            </div>
            <div>
              <h4 class="font-bold text-green-800 text-lg">Active</h4>
              <p class="text-green-700 text-sm">Your account is active and in good standing</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>