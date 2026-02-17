<?= $this->extend('layouts/auth_layout') ?>

<?= $this->section('content') ?>
<!-- Page Header -->
<div class="bg-green-700 text-white p-8 rounded-xl mb-6">
  <h2 class="text-3xl font-bold mb-2"><?= ($user['role'] === 'admin' && $user['access_level'] === 'full') ? 'Super Admin' : 'Admin' ?> Profile</h2>
  <p class="text-green-100">
    <?= ($user['role'] === 'admin' && $user['access_level'] === 'full') 
        ? 'Manage your account and system settings' 
        : 'View your account information' ?>
  </p>
</div>

<?php if (session()->getFlashdata('success')): ?>
<div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6 flex items-start gap-3">
  <svg class="w-5 h-5 text-green-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
  </svg>
  <p class="text-green-800 text-sm"><?= session()->getFlashdata('success'); ?></p>
</div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
<div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6 flex items-start gap-3">
  <svg class="w-5 h-5 text-red-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
  </svg>
  <p class="text-red-800 text-sm"><?= session()->getFlashdata('error'); ?></p>
</div>
<?php endif; ?>

<?php $is_super_admin = ($user['role'] === 'admin' && $user['access_level'] === 'full'); ?>

<?php if (!$is_super_admin): ?>
<!-- Info Notice for Admin -->
<div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6 flex items-start gap-3">
  <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
  </svg>
  <p class="text-blue-800 text-sm">Your profile is managed by the Admin. Contact your administrator to request changes.</p>
</div>
<?php endif; ?>

<!-- Profile Information Card -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
  <div class="flex items-center gap-3 mb-6">
    <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
    </svg>
    <div>
      <h3 class="text-xl font-bold text-gray-800">Profile Information</h3>
      <p class="text-sm text-gray-500">
        <?= $is_super_admin ? 'Update your account details' : 'Your account details (read-only)' ?>
      </p>
    </div>
  </div>

  <form action="<?= base_url('settings/update-profile'); ?>" method="post">
    <?= csrf_field() ?>
    
    <div class="space-y-4">
      <!-- Full Name -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
        <input
          type="text"
          name="full_name"
          value="<?= esc($user['full_name']); ?>"
          <?= $is_super_admin ? '' : 'disabled' ?>
          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 <?= $is_super_admin ? '' : 'bg-gray-50 text-gray-600 cursor-not-allowed' ?>"
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
            name="email"
            value="<?= esc($user['email']); ?>"
            <?= $is_super_admin ? '' : 'disabled' ?>
            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 <?= $is_super_admin ? '' : 'bg-gray-50 text-gray-600 cursor-not-allowed' ?>"
          />
        </div>
      </div>

      <!-- Role -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
        <div class="flex gap-2">
          <span class="inline-flex items-center px-4 py-2 bg-green-100 text-green-800 rounded-lg border border-green-200 font-medium">
            <?= esc(ucfirst(str_replace('_', ' ', $user['role']))); ?>
          </span>
          <span class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-800 rounded-lg border border-blue-200 font-medium">
            <?= esc(ucfirst($user['access_level'])); ?> access
          </span>
        </div>
      </div>

      <!-- User ID -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">User ID</label>
        <input
          type="text"
          value="<?= esc($user['id']); ?>"
          disabled
          class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-600 cursor-not-allowed"
        />
      </div>
    </div>

    <?php if ($is_super_admin): ?>
    <div class="mt-6">
      <button
        type="submit"
        class="w-full px-6 py-3 bg-green-700 text-white rounded-lg hover:bg-green-800 font-semibold"
      >
        Update Profile
      </button>
    </div>
    <?php endif; ?>
  </form>
</div>

<!-- Account Status Card -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
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

<?php if ($is_super_admin): ?>
<!-- Change Password Card (Full Admin Only) -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
  <div class="flex items-center gap-3 mb-6">
    <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
    </svg>
    <div>
      <h3 class="text-xl font-bold text-gray-800">Change Password</h3>
      <p class="text-sm text-gray-500">Update your account password</p>
    </div>
  </div>

  <form action="<?= base_url('settings/change-password'); ?>" method="post">
    <?= csrf_field() ?>
    
    <div class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
        <input type="password" name="current_password" placeholder="Enter current password" required
          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"/>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
        <input type="password" name="new_password" placeholder="Enter new password (min. 8 characters)" required minlength="8"
          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"/>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
        <input type="password" name="confirm_password" placeholder="Re-enter new password" required minlength="8"
          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"/>
      </div>
    </div>

    <div class="mt-6">
      <button type="submit" class="w-full px-6 py-3 bg-green-700 text-white rounded-lg hover:bg-green-800 font-semibold">Change Password</button>
    </div>
  </form>
</div>

<!-- Advanced Settings -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
  <div class="flex items-center gap-3 mb-4">
    <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
    </svg>
    <h3 class="text-xl font-bold text-gray-800">Advanced Settings</h3>
  </div>

  <div class="space-y-3">
    <a href="<?= base_url('settings/activity-logs'); ?>" class="flex items-center justify-between p-4 bg-gray-50 hover:bg-gray-100 rounded-lg border border-gray-200 transition-colors">
      <div class="flex items-center gap-3">
        <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        <div>
          <p class="font-semibold text-gray-800">Activity Logs</p>
          <p class="text-sm text-gray-500">View system audit trails</p>
        </div>
      </div>
      <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
      </svg>
    </a>
  </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>
