<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Management - CredentiaTAU</title>
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
        <a href="<?= base_url('super-admin/dashboard'); ?>" class="w-full flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg font-medium mb-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
          </svg>
          <span>Dashboard</span>
        </a>
        <a href="<?= base_url('super-admin/user-management'); ?>" class="w-full flex items-center gap-3 px-4 py-3 text-green-700 bg-green-50 rounded-lg font-medium mb-2">
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

      <!-- Content -->
      <div class="p-8">
        <!-- Page Header -->
        <div class="bg-green-700 text-white p-8 rounded-xl mb-6">
          <h2 class="text-3xl font-bold mb-2">Super Admin Dashboard</h2>
          <p class="text-green-100">System management and oversight</p>
        </div>

        <!-- Tabs -->
        <div class="flex gap-2 mb-6 border-b border-gray-200">
          <button onclick="window.location.href='<?= base_url('super-admin/dashboard'); ?>'" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-t-lg font-medium">Overview</button>
          <button onclick="window.location.href='<?= base_url('super-admin/all-records'); ?>'" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-t-lg font-medium">Academic Records</button>
          <button class="px-4 py-2 bg-green-700 text-white rounded-t-lg font-medium">User Management</button>
          <button class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-t-lg font-medium">System Settings</button>
        </div>

        <!-- Pending Staff Approvals -->
        <?php if (!empty($pending_staff)): ?>
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 mb-6">
          <div class="flex items-center gap-2 mb-4">
            <svg class="w-6 h-6 text-yellow-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
            <h3 class="text-xl font-bold text-yellow-900">Pending Staff Approvals (<?= count($pending_staff); ?>)</h3>
          </div>

          <div class="space-y-3">
            <?php foreach ($pending_staff as $staff): ?>
            <div class="bg-white rounded-lg p-4 flex items-center justify-between">
              <div>
                <h4 class="font-semibold text-gray-800"><?= esc($staff['name']); ?></h4>
                <p class="text-sm text-gray-500"><?= esc($staff['email']); ?></p>
                <p class="text-xs text-gray-400 mt-1">Requested: <?= esc($staff['requested']); ?></p>
              </div>
              <div class="flex gap-2">
                <button
                  onclick="approveStaff(<?= $staff['id']; ?>)"
                  class="flex items-center gap-2 px-4 py-2 bg-green-700 text-white rounded-lg hover:bg-green-800 font-medium"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                  Approve
                </button>
                <button
                  onclick="rejectStaff(<?= $staff['id']; ?>)"
                  class="flex items-center gap-2 px-4 py-2 bg-white border-2 border-red-600 text-red-600 rounded-lg hover:bg-red-50 font-medium"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                  Reject
                </button>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <!-- User Management Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
          <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <div class="flex items-center gap-4">
              <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
              <input
                type="text"
                id="searchUsers"
                placeholder="Search users..."
                class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
              />
            </div>
            <button
              onclick="openAddAdminModal()"
              class="flex items-center gap-2 bg-green-700 text-white px-4 py-2 rounded-lg hover:bg-green-800 font-medium"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
              </svg>
              Add Admin
            </button>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Name</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Email</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Role</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Access Level</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Last Login</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Records</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($users as $user): ?>
                <tr class="border-b border-gray-100 hover:bg-gray-50">
                  <td class="px-6 py-4 text-sm text-gray-800 font-medium"><?= esc($user['name']); ?></td>
                  <td class="px-6 py-4 text-sm text-gray-600"><?= esc($user['email']); ?></td>
                  <td class="px-6 py-4">
                    <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                      <?= esc($user['role']); ?>
                    </span>
                  </td>
                  <td class="px-6 py-4">
                    <span class="px-3 py-1 <?= $user['access_level'] === 'full' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'; ?> text-xs font-semibold rounded-full border <?= $user['access_level'] === 'full' ? 'border-blue-200' : 'border-gray-300'; ?>">
                      <?= esc($user['access_level']); ?>
                    </span>
                  </td>
                  <td class="px-6 py-4">
                    <?php if ($user['status'] === 'active'): ?>
                      <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">active</span>
                    <?php elseif ($user['status'] === 'pending'): ?>
                      <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">pending</span>
                    <?php else: ?>
                      <span class="px-3 py-1 bg-gray-100 text-gray-800 text-xs font-semibold rounded-full">inactive</span>
                    <?php endif; ?>
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-600"><?= esc($user['last_login']); ?></td>
                  <td class="px-6 py-4 text-sm text-gray-600"><?= esc($user['records']); ?></td>
                  <td class="px-6 py-4">
                    <?php if ($user['status'] === 'pending'): ?>
                      <div class="flex gap-2">
                        <button
                          onclick="approveStaff(<?= $user['id']; ?>)"
                          class="text-green-600 hover:text-green-800"
                          title="Approve"
                        >
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                          </svg>
                        </button>
                        <button
                          onclick="rejectStaff(<?= $user['id']; ?>)"
                          class="text-red-600 hover:text-red-800"
                          title="Reject"
                        >
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                          </svg>
                        </button>
                      </div>
                    <?php else: ?>
                      <button
                        onclick="editUser(<?= $user['id']; ?>)"
                        class="text-green-600 hover:text-green-800 flex items-center gap-1"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                      </button>
                    <?php endif; ?>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Add Admin Modal -->
  <div id="addAdminModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl p-6 w-full max-w-md">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h3 class="text-xl font-bold text-gray-800">Add New Admin</h3>
          <p class="text-sm text-gray-500">Create a new administrator account</p>
        </div>
        <button onclick="closeAddAdminModal()" class="text-gray-400 hover:text-gray-600">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <form action="<?= base_url('super-admin/add-admin'); ?>" method="post">
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
            <input
              type="text"
              name="full_name"
              placeholder="Juan Dela Cruz"
              required
              class="w-full px-4 py-2 border-2 border-green-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
            <input
              type="email"
              name="email"
              placeholder="admin@tau.edu.ph"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Role *</label>
            <select
              name="role"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
            >
              <option value="admin">Admin</option>
              <option value="super_admin">Super Admin</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Initial Password *</label>
            <input
              type="password"
              name="password"
              placeholder="••••••••"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
            />
          </div>
        </div>

        <button
          type="submit"
          class="w-full mt-6 bg-green-700 text-white px-6 py-3 rounded-lg hover:bg-green-800 font-semibold"
        >
          Create Admin Account
        </button>
      </form>
    </div>
  </div>

  <script>
    function openAddAdminModal() {
      document.getElementById('addAdminModal').classList.remove('hidden');
    }

    function closeAddAdminModal() {
      document.getElementById('addAdminModal').classList.add('hidden');
    }

    function editUser(userId) {
      alert('Edit user: ' + userId);
      // In real implementation, open edit modal with user data
    }

    function approveStaff(userId) {
      if (confirm('Approve this staff member?')) {
        window.location.href = '<?= base_url('super-admin/approve-staff/'); ?>' + userId;
      }
    }

    function rejectStaff(userId) {
      if (confirm('Reject this staff member?')) {
        window.location.href = '<?= base_url('super-admin/reject-staff/'); ?>' + userId;
      }
    }

    // Search functionality
    document.getElementById('searchUsers').addEventListener('input', function(e) {
      const searchTerm = e.target.value.toLowerCase();
      const rows = document.querySelectorAll('tbody tr');
      
      rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
      });
    });
  </script>
</body>
</html>