<?= $this->extend('layouts/auth_layout') ?>

<?= $this->section('content') ?>
<!-- Page Header -->
<div class="bg-green-700 text-white p-8 rounded-xl mb-6">
  <h2 class="text-3xl font-bold mb-2">Activity Logs</h2>
  <p class="text-green-100">System audit trails and user activity monitoring</p>
</div>

<!-- Filters Section -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
  <form method="get" action="<?= base_url('settings/activity-logs'); ?>" class="grid grid-cols-1 md:grid-cols-4 gap-4">
    
    <!-- Action Filter -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">Action Type</label>
      <select name="action" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
        <option value="all" <?= $actionFilter === 'all' ? 'selected' : '' ?>>All Actions</option>
        <option value="login" <?= $actionFilter === 'login' ? 'selected' : '' ?>>Login/Logout</option>
        <option value="user" <?= $actionFilter === 'user' ? 'selected' : '' ?>>User Management</option>
        <option value="record" <?= $actionFilter === 'record' ? 'selected' : '' ?>>Record Management</option>
        <option value="password" <?= $actionFilter === 'password' ? 'selected' : '' ?>>Password Changes</option>
      </select>
    </div>

    <!-- Date Filter -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">Time Period</label>
      <select name="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
        <option value="all" <?= $dateFilter === 'all' ? 'selected' : '' ?>>All Time</option>
        <option value="today" <?= $dateFilter === 'today' ? 'selected' : '' ?>>Today</option>
        <option value="week" <?= $dateFilter === 'week' ? 'selected' : '' ?>>Last 7 Days</option>
        <option value="month" <?= $dateFilter === 'month' ? 'selected' : '' ?>>Last 30 Days</option>
      </select>
    </div>

    <!-- User Filter -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">User</label>
      <select name="user" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
        <option value="all" <?= $userFilter === 'all' ? 'selected' : '' ?>>All Users</option>
        <?php foreach ($users as $user): ?>
          <option value="<?= $user['id']; ?>" <?= $userFilter == $user['id'] ? 'selected' : '' ?>>
            <?= esc($user['full_name']); ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- Filter Button -->
    <div class="flex items-end">
      <button type="submit" class="w-full px-6 py-2 bg-green-700 text-white rounded-lg hover:bg-green-800 font-semibold">
        Apply Filters
      </button>
    </div>
  </form>
</div>

<!-- Activity Logs Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full">
      <thead class="bg-gray-50 border-b border-gray-200">
        <tr>
          <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Timestamp</th>
          <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">User</th>
          <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Action</th>
          <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Description</th>
          <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">IP Address</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200">
        <?php if (!empty($logs)): ?>
          <?php foreach ($logs as $log): ?>
          <tr class="hover:bg-gray-50">
            <!-- Timestamp -->
            <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap">
              <?= date('M d, Y H:i:s', strtotime($log['created_at'])); ?>
            </td>

            <!-- User -->
            <td class="px-6 py-4">
              <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center text-green-700 font-semibold text-xs">
                  <?= strtoupper(substr($log['full_name'] ?? 'S', 0, 2)); ?>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-800"><?= esc($log['full_name'] ?? 'System'); ?></p>
                  <p class="text-xs text-gray-500"><?= esc($log['email'] ?? 'system@credentiatau.com'); ?></p>
                </div>
              </div>
            </td>

            <!-- Action -->
            <td class="px-6 py-4">
              <?php
                $actionBadges = [
                  'login_success' => 'bg-green-100 text-green-800',
                  'logout' => 'bg-gray-100 text-gray-800',
                  'user_created' => 'bg-blue-100 text-blue-800',
                  'user_updated' => 'bg-yellow-100 text-yellow-800',
                  'user_deleted' => 'bg-red-100 text-red-800',
                  'user_suspended' => 'bg-orange-100 text-orange-800',
                  'user_unsuspended' => 'bg-green-100 text-green-800',
                  'record_uploaded' => 'bg-blue-100 text-blue-800',
                  'record_updated' => 'bg-yellow-100 text-yellow-800',
                  'record_deleted' => 'bg-red-100 text-red-800',
                  'password_changed' => 'bg-purple-100 text-purple-800',
                  'profile_updated' => 'bg-blue-100 text-blue-800'
                ];
                
                $badgeClass = $actionBadges[$log['action']] ?? 'bg-gray-100 text-gray-800';
              ?>
              <span class="px-3 py-1 text-xs font-semibold rounded-full <?= $badgeClass; ?>">
                <?= esc(ucwords(str_replace('_', ' ', $log['action']))); ?>
              </span>
            </td>

            <!-- Description -->
            <td class="px-6 py-4 text-sm text-gray-600">
              <?= esc($log['description'] ?? '-'); ?>
            </td>

            <!-- IP Address -->
            <td class="px-6 py-4 text-sm text-gray-600 font-mono">
              <?= esc($log['ip_address'] ?? '-'); ?>
            </td>
          </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
              <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              <p class="text-lg font-semibold mb-1">No activity logs found</p>
              <p class="text-sm">Try adjusting your filters to see more results</p>
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Pagination Info -->
  <?php if (!empty($logs)): ?>
  <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
    <p class="text-sm text-gray-600">
      Showing <?= count($logs); ?> most recent activities
      <?php if ($actionFilter !== 'all' || $dateFilter !== 'all' || $userFilter !== 'all'): ?>
        (filtered)
      <?php endif; ?>
    </p>
  </div>
  <?php endif; ?>
</div>

<!-- Legend -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mt-6">
  <h3 class="text-lg font-bold text-gray-800 mb-4">Activity Types Legend</h3>
  <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
    <div class="flex items-center gap-2">
      <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Login</span>
      <span class="text-sm text-gray-600">Successful logins</span>
    </div>
    <div class="flex items-center gap-2">
      <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Logout</span>
      <span class="text-sm text-gray-600">User logouts</span>
    </div>
    <div class="flex items-center gap-2">
      <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Created</span>
      <span class="text-sm text-gray-600">New items</span>
    </div>
    <div class="flex items-center gap-2">
      <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Updated</span>
      <span class="text-sm text-gray-600">Modifications</span>
    </div>
    <div class="flex items-center gap-2">
      <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Deleted</span>
      <span class="text-sm text-gray-600">Removals</span>
    </div>
    <div class="flex items-center gap-2">
      <span class="px-3 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">Suspended</span>
      <span class="text-sm text-gray-600">Account suspensions</span>
    </div>
    <div class="flex items-center gap-2">
      <span class="px-3 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">Password</span>
      <span class="text-sm text-gray-600">Password changes</span>
    </div>
  </div>
</div>

<?= $this->endSection() ?>