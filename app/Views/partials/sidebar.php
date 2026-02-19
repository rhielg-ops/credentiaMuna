<?php
$currentPath = uri_string();
$role = session()->get('role'); // get current role
// Define functional super admin flag
$isSuperAdmin = ($role === 'admin'); // Previously 'super_admin'


// Define menu items based on role
$menuItems = [];
if ($role === 'admin') { // Previously super_admin
    $menuItems = [
        [
            'label' => 'Dashboard',
            'route' => 'super-admin/dashboard',
            'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'
        ],
        [
            'label' => 'User Management',
            'route' => 'super-admin/user-management',
            'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'
        ],
        [
            'label' => 'Academic Records',
            'route' => 'academic-records',
            'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'
        ],
        [
            'label' => 'System Backup',
            'route' => 'super-admin/system-backup',
            'icon' => 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4'
        ],
        [
            'label' => 'Settings',
            'route' => 'super-admin/settings',
            'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'
        ]
    ];
} else {
    $menuItems = [
        [
            'label' => 'Dashboard',
            'route' => 'dashboard',
            'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'
        ],
        [
            'label' => 'Academic Records',
            'route' => 'academic-records',
            'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'
        ],
        [
            'label' => 'Settings',
            'route' => 'settings',
            'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'
        ]
    ];
}
?>

<div class="w-64 <?= $isSuperAdmin ? 'bg-green-700 border-green-800' : 'bg-green-700 border-green-800' ?> border-r">
  <!-- Logo Section -->
  <!-- Logo Section -->
<div class="flex flex-col items-center gap-3 px-6 py-5 border-b border-green-800">

  <!-- Logo -->
  <div class="w-14 h-14 bg-white rounded-full flex items-center justify-center overflow-hidden">
    <img 
      src="<?= base_url('assets/img/TAU.png'); ?>" 
      alt="TAU Logo" 
      class="w-12 h-12 object-contain"
    >
  </div>

  <!-- Centered Text -->
  <div class="text-center leading-tight">
    <h1 class="text-xl font-bold text-white">
      CredentiaTAU
    </h1>
    <p class="text-sm text-green-100">
      Admission and Registration Services
    </p>
  </div>

</div>


  <!-- Navigation -->
  <nav class="p-4">
    <?php foreach ($menuItems as $item): ?>
      <?php 
        $isActive = (strpos($currentPath, $item['route']) !== false);
        $activeClasses = $isActive ? 'text-white bg-green-800' : 'text-white hover:bg-green-600 transition-colors';
      ?>
      <a href="<?= base_url($item['route']); ?>" class="w-full flex items-center gap-3 px-4 py-3 <?= $activeClasses ?> rounded-lg font-medium mb-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $item['icon'] ?>"></path>
        </svg>
        <span><?= $item['label'] ?></span>
      </a>
    <?php endforeach; ?>
  </nav>

  <!-- Super Admin Badge -->
  <?php if ($isSuperAdmin): ?>
  <div class="mx-4 mt-6 p-4 bg-green-800 border border-green-600 rounded-lg">
    <div class="flex items-center gap-2 mb-2">
      <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
      </svg>
      <span class="font-bold text-white">Admin</span>
    </div>
    <p class="text-xs text-green-100">You have full system access and control</p>
  </div>
  <?php endif; ?>
</div>