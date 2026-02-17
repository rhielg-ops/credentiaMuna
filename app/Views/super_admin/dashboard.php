<?= $this->extend('layouts/auth_layout') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="bg-green-700 text-white p-8 rounded-xl mb-6">
  <h2 class="text-3xl font-bold mb-2">Admin Dashboard</h2>
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
  
  <div class="bg-green-700 rounded-xl p-6 shadow-md border border-green-800 text-white transition-all duration-300 hover:bg-green-600 hover:shadow-lg cursor-default">
    <div class="flex items-center justify-between mb-4">
      <span class="text-green-100 font-medium">Total Records</span>
      <div class="w-12 h-12 bg-green-800/50 rounded-lg flex items-center justify-center">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
      </div>
    </div>
    <p class="text-3xl font-bold mb-1"><?= number_format($stats['total_records']) ?></p>
    <p class="text-sm text-green-200">Academic documents</p>
  </div>

  <div class="bg-green-700 rounded-xl p-6 shadow-md border border-green-800 text-white transition-all duration-300 hover:bg-green-600 hover:shadow-lg cursor-default">
    <div class="flex items-center justify-between mb-4">
      <span class="text-green-100 font-medium">Total Users</span>
      <div class="w-12 h-12 bg-green-800/50 rounded-lg flex items-center justify-center">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
        </svg>
      </div>
    </div>
    <p class="text-3xl font-bold mb-1"><?= $stats['total_admins'] ?></p>
    <p class="text-sm text-green-200"><?= $stats['active_admins'] ?> active</p>
  </div>

  <div class="bg-green-700 rounded-xl p-6 shadow-md border border-green-800 text-white transition-all duration-300 hover:bg-green-600 hover:shadow-lg cursor-default">
    <div class="flex items-center justify-between mb-4">
      <span class="text-green-100 font-medium">Recent Activity</span>
      <div class="w-12 h-12 bg-green-800/50 rounded-lg flex items-center justify-center">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
        </svg>
      </div>
    </div>
    <p class="text-3xl font-bold mb-1"><?= $stats['recent_activity'] ?></p>
    <p class="text-sm text-green-200">actions today</p>
  </div>

  <div class="bg-green-700 rounded-xl p-6 shadow-md border border-green-800 text-white transition-all duration-300 hover:bg-green-600 hover:shadow-lg cursor-default">
    <div class="flex items-center justify-between mb-4">
      <span class="text-green-100 font-medium">Pending Staff</span>
      <div class="w-12 h-12 bg-green-800/50 rounded-lg flex items-center justify-center">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
      </div>
    </div>
    <p class="text-3xl font-bold mb-1"><?= $stats['pending_staff'] ?></p>
    <p class="text-sm text-green-200">awaiting approval</p>
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
    <?php foreach ($recent_activity as $activity): ?>
    <!-- Activity Item -->
    <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-lg">
      <div class="w-12 h-12 bg-<?= $activity['icon_bg'] ?>-100 rounded-lg flex items-center justify-center flex-shrink-0">
        <svg class="w-6 h-6 text-<?= $activity['icon_bg'] ?>-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
        </svg>
      </div>
      <div class="flex-1">
        <p class="font-semibold text-gray-800"><?= esc($activity['title']) ?></p>
        <p class="text-sm text-gray-500">by <?= esc($activity['user']) ?></p>
      </div>
      <div class="text-right text-sm text-gray-500">
        <p><?= esc($activity['time']) ?></p>
        <p><?= esc($activity['timestamp']) ?></p>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<?= $this->endSection() ?>