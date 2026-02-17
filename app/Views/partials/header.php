<?php
$email = session()->get('email');
$role = session()->get('role');
$initials = strtoupper(substr($email, 0, 2));

// map roles to display names
if ($role === 'admin') {
  $roleDisplay = 'Admin'; //old super_admin
  $is_super_admin = true; // grants full admin privileges
} else { //'user'
  $roleDisplay = 'User'; //old admin
  $is_super_admin = false;
  }
?>

<!-- Green Header -->
<div class="bg-green-700 border-b border-green-800 px-8 py-4">
  <div class="flex items-center justify-end">
    <div class="flex items-center gap-3">
      <div class="text-right">
        <p class="text-sm font-semibold text-white"><?= esc($email); ?></p>
        <p class="text-xs text-green-100"><?= esc($roleDisplay); ?></p>
      </div>
      <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-green-700 font-bold">
        <?= $initials ?>
      </div>
      <a href="<?= base_url('auth/logout'); ?>" class="ml-4 text-sm text-white hover:text-green-100 font-medium">Logout</a>
    </div>
  </div>
</div>