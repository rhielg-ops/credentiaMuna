<?= $this->extend('layouts/auth_layout') ?>

<?= $this->section('content') ?>
<!-- Page Header -->
<div class="bg-green-700 text-white p-8 rounded-xl mb-6">
  <h2 class="text-3xl font-bold mb-2">User Management</h2>
  <p class="text-green-100">Manage administrators and approve pending requests</p>
</div>

<!-- Pending Approvals Section -->
<?php if (!empty($pending_admins)): ?>
<div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 mb-6">
  <div class="flex items-center gap-2 mb-4">
    <svg class="w-6 h-6 text-yellow-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
    </svg>
    <h3 class="text-xl font-bold text-yellow-800">Pending Staff Approvals (<?= count($pending_admins); ?>)</h3>
  </div>

  <div class="space-y-3">
    <?php foreach ($pending_admins as $pending): ?>
    <div class="bg-white p-4 rounded-lg border border-yellow-300 flex items-center justify-between">
      <div>
        <p class="font-semibold text-gray-800"><?= esc($pending['full_name']); ?></p>
        <p class="text-sm text-gray-600"><?= esc($pending['email']); ?></p>
        <p class="text-xs text-gray-500">Requested: <?= date('M d, Y', strtotime($pending['created_at'])); ?></p>
      </div>
      <div class="flex gap-2">
        <a href="<?= base_url('super-admin/approve-admin/' . $pending['id']); ?>" 
           class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 font-medium"
           onclick="return confirm('Approve this admin?');">
          ✓ Approve
        </a>
        <a href="<?= base_url('super-admin/reject-admin/' . $pending['id']); ?>" 
           class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 font-medium"
           onclick="return confirm('Reject and remove this request?');">
          ✗ Reject
        </a>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<!-- Add Admin Button -->
<div class="mb-6">
  <button onclick="openAddModal()" class="bg-green-700 text-white px-6 py-3 rounded-lg hover:bg-green-800 font-semibold flex items-center gap-2">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
    </svg>
    Add User
  </button>
</div>

<!-- Search Bar -->
<div class="mb-6">
  <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Search by name, email, or username..." 
         class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
</div>

<!-- Users Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-x-auto">
  <table class="w-full" id="usersTable">
    <thead class="bg-gray-50 border-b border-gray-200">
      <tr>
        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Name</th>
        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Email</th>
        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Username</th>
        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Role</th>
        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Access</th>
        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Last Login</th>
        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Records</th>
        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Actions</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-200">
      <?php if (!empty($users)): ?>
        <?php foreach ($users as $user): ?>
        <tr class="hover:bg-gray-50">
          <td class="px-6 py-4 text-sm font-medium text-gray-800"><?= esc($user['full_name']); ?></td>
          <td class="px-6 py-4 text-sm text-gray-600"><?= esc($user['email']); ?></td>
          <td class="px-6 py-4 text-sm text-gray-600">
            <code class="bg-gray-100 px-2 py-1 rounded text-xs"><?= esc($user['username'] ?? 'N/A'); ?></code>
          </td>
          <td class="px-6 py-4">
            <?php
            $roleDisplay = ($user['role'] === '' || $user['role'] === 'admin') ? 'Admin' : 'User';
            $badgeColor = ($user['role'] === '' || $user['role'] === 'admin') ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800';
            ?>
            <span class="px-3 py-1 text-xs font-semibold rounded-full <?= $badgeColor; ?>">
              <?= esc($roleDisplay); ?>
            </span>
          </td>
          <td class="px-6 py-4">
            <button onclick='openPrivilegesModal(<?= json_encode($user); ?>)' 
                    class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200 cursor-pointer">
              <?= esc(ucfirst($user['access_level'] ?? 'full')); ?>
            </button>
          </td>
          <td class="px-6 py-4">
            <span class="px-3 py-1 text-xs font-semibold rounded-full 
              <?= $user['status'] === 'active' ? 'bg-green-100 text-green-800' : 
                 ($user['status'] === 'suspended' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'); ?>">
              <?= esc(ucfirst($user['status'])); ?>
            </span>
          </td>
          <td class="px-6 py-4 text-sm text-gray-600">
            <?= $user['last_login'] ? date('M d, Y H:i', strtotime($user['last_login'])) : 'Never'; ?>
          </td>
          <td class="px-6 py-4 text-sm text-gray-600"><?= $user['total_records'] ?? 0; ?></td>
          <td class="px-6 py-4">
            <div class="flex gap-2">
              <button onclick='openEditModal(<?= json_encode($user); ?>, <?= json_encode($user_privileges_map[$user["id"]] ?? []); ?>)' 
                      class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                Edit
              </button>
              <?php if ($user['status'] !== 'inactive'): ?>
                <a href="<?= base_url('super-admin/toggle-suspend/' . $user['id']); ?>" 
                   class="text-orange-600 hover:text-orange-800 font-medium text-sm"
                   onclick="return confirm('Deactivate this admin?');">
                  Deactivate
                </a>
              <?php else: ?>
                <a href="<?= base_url('super-admin/toggle-suspend/' . $user['id']); ?>" 
                   class="text-green-600 hover:text-green-800 font-medium text-sm"
                   onclick="return confirm('Reactivate this admin?');">
                  Reactivate
                </a>
              <?php endif; ?>
              <a href="<?= base_url('super-admin/delete-admin/' . $user['id']); ?>" 
                 class="text-red-600 hover:text-red-800 font-medium text-sm"
                 onclick="return confirm('Are you sure you want to delete this admin? This action cannot be undone.');">
                Delete
              </a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="9" class="px-6 py-12 text-center text-gray-500">
            No users found. Click "Add Admin" to create one.
          </td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<?= $this->endSection() ?>

<?= $this->section('modals') ?>
<!-- Add Admin Modal -->
<div id="addModal" class="modal">
  <div class="modal-content bg-white rounded-xl p-8 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
    <div class="flex items-center justify-between mb-6">
      <h3 class="text-2xl font-bold text-gray-800">Add New Admin</h3>
      <button onclick="closeAddModal()" class="text-gray-400 hover:text-gray-600">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
    </div>

    <form action="<?= base_url('super-admin/add-admin'); ?>" method="post">
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
          <input type="text" name="full_name" required 
                 class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                 placeholder="Juan Dela Cruz">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
          <input type="email" name="email" required 
                 class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                 placeholder="admin@tau.edu.ph">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Username *</label>
          <input type="text" name="username" required 
                 class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                 placeholder="jdelacruz" pattern="[a-zA-Z0-9_]+" title="Only letters, numbers, and underscores allowed">
          <p class="text-xs text-gray-500 mt-1">Only letters, numbers, and underscores allowed</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Role *</label>
          <select name="role" required 
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            <option value="user">User</option>
            <option value="admin">Admin</option>
          </select>
          <p class="text-xs text-gray-500 mt-1">User = Limited access | Admin = Full system access</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Access Level *</label>
          <select name="access_level" required 
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            <option value="full" selected>Full Access</option>
            <option value="limited">Limited Access</option>
          </select>
        </div>

        <!-- Password field with eye toggle -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Initial Password *</label>
          <div class="relative">
            <input 
              type="password" 
              name="initial_password" 
              id="initial_password"
              required 
              minlength="8"
              class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
              placeholder="Minimum 8 characters"
            >
            <button 
              type="button"
              id="toggleInitialPassword"
              class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700 focus:outline-none"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
              </svg>
            </button>
          </div>
          <p class="text-xs text-gray-500 mt-1">User will be required to change this password on first login</p>
        </div>

        <!-- User Privileges -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">User Privileges</label>
          <p class="text-xs text-gray-500 mb-3">Select which privileges this user should have</p>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <label class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-green-50">
              <input type="checkbox" name="privileges[]" value="records_upload"
                     class="add-priv-cb mt-0.5 h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
              <div>
                <p class="text-sm font-medium text-gray-800">Upload Records</p>
                <p class="text-xs text-gray-500">Upload digitized academic records</p>
              </div>
            </label>
            <label class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-green-50">
              <input type="checkbox" name="privileges[]" value="files_view"
                     class="add-priv-cb mt-0.5 h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
              <div>
                <p class="text-sm font-medium text-gray-800">View Files</p>
                <p class="text-xs text-gray-500">View, download, and print archived records</p>
              </div>
            </label>
            <label class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-green-50">
              <input type="checkbox" name="privileges[]" value="records_organize"
                     class="add-priv-cb mt-0.5 h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
              <div>
                <p class="text-sm font-medium text-gray-800">Organize Records</p>
                <p class="text-xs text-gray-500">Move files/folders, rename files, manage file structure</p>
              </div>
            </label>
            <label class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-green-50">
              <input type="checkbox" name="privileges[]" value="folders_add"
                     class="add-priv-cb mt-0.5 h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
              <div>
                <p class="text-sm font-medium text-gray-800">Add Folders</p>
                <p class="text-xs text-gray-500">Create new folders or categories</p>
              </div>
            </label>
            <label class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-green-50">
              <input type="checkbox" name="privileges[]" value="records_delete"
                     class="add-priv-cb mt-0.5 h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
              <div>
                <p class="text-sm font-medium text-gray-800">Delete Records</p>
                <p class="text-xs text-gray-500">Delete archived files</p>
              </div>
            </label>
            <label class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-green-50">
              <input type="checkbox" name="privileges[]" value="folders_delete"
                     class="add-priv-cb mt-0.5 h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
              <div>
                <p class="text-sm font-medium text-gray-800">Delete Folders</p>
                <p class="text-xs text-gray-500">Delete folders or categories</p>
              </div>
            </label>
            <label class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-green-50">
              <input type="checkbox" name="privileges[]" value="profile_edit"
                     class="add-priv-cb mt-0.5 h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
              <div>
                <p class="text-sm font-medium text-gray-800">Edit Profile</p>
                <p class="text-xs text-gray-500">Edit user profile and personal settings</p>
              </div>
            </label>
            <label class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-green-50">
              <input type="checkbox" name="privileges[]" value="user_management"
                     class="add-priv-cb mt-0.5 h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
              <div>
                <p class="text-sm font-medium text-gray-800">Manage Users</p>
                <p class="text-xs text-gray-500">Add, edit, and assign roles to users</p>
              </div>
            </label>
            <label class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-green-50">
              <input type="checkbox" name="privileges[]" value="system_backup"
                     class="add-priv-cb mt-0.5 h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
              <div>
                <p class="text-sm font-medium text-gray-800">System Backup</p>
                <p class="text-xs text-gray-500">Access system backup and restore features</p>
              </div>
            </label>
            <label class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-green-50">
              <input type="checkbox" name="privileges[]" value="audit_logs"
                     class="add-priv-cb mt-0.5 h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
              <div>
                <p class="text-sm font-medium text-gray-800">Audit Logs</p>
                <p class="text-xs text-gray-500">View system activity logs</p>
              </div>
            </label>
            <label class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-green-50 sm:col-span-2">
              <input type="checkbox" name="privileges[]" value="full_admin" id="add_full_admin"
                     class="add-priv-cb mt-0.5 h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
              <div>
                <p class="text-sm font-medium text-gray-800">Full Admin Access</p>
                <p class="text-xs text-gray-500">Automatically grants all privileges when selected</p>
              </div>
            </label>
          </div>
        </div>
      </div>

      <div class="flex gap-3 mt-6">
        <button type="button" onclick="closeAddModal()" 
                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
          Cancel
        </button>
        <button type="submit" 
                class="flex-1 px-4 py-2 bg-green-700 text-white rounded-lg hover:bg-green-800">
          Create Admin Account
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Edit Admin Modal -->
<div id="editModal" class="modal">
  <div class="modal-content bg-white rounded-xl p-8 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
    <div class="flex items-center justify-between mb-6">
      <h3 class="text-2xl font-bold text-gray-800">Edit Admin</h3>
      <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
    </div>

    <form id="editForm" action="" method="post">
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
          <input type="text" name="full_name" id="edit_full_name" required 
                 class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
          <input type="email" name="email" id="edit_email" required 
                 class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Username *</label>
          <input type="text" name="username" id="edit_username" required 
                 class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                 pattern="[a-zA-Z0-9_]+" title="Only letters, numbers, and underscores allowed">
          <p class="text-xs text-gray-500 mt-1">Only letters, numbers, and underscores allowed</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Role *</label>
          <select name="role" id="edit_role" required 
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            <option value="user">User</option>
            <option value="admin">Admin</option>
          </select>
          <p class="text-xs text-gray-500 mt-1">User = Limited access | Admin = Full system access</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Access Level *</label>
          <select name="access_level" id="edit_access_level" required 
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            <option value="full">Full Access</option>
            <option value="limited">Limited Access</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
          <select name="status" id="edit_status" required 
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>

        <!-- Password field with eye toggle -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">New Password (Optional)</label>
          <div class="relative">
            <input 
              type="password" 
              name="new_password" 
              id="edit_new_password"
              minlength="8"
              class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
              placeholder="Leave blank to keep current password"
            >
            <button 
              type="button"
              id="toggleEditPassword"
              class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700 focus:outline-none"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
              </svg>
            </button>
          </div>
          <p class="text-xs text-gray-500 mt-1">Only fill this if you want to reset the password</p>
        </div>

        <!-- User Privileges -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">User Privileges</label>
          <p class="text-xs text-gray-500 mb-3">Select which privileges this user should have</p>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <label class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-green-50">
              <input type="checkbox" id="ep_records_upload" data-key="records_upload"
                     class="edit-priv-cb mt-0.5 h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
              <div>
                <p class="text-sm font-medium text-gray-800">Upload Records</p>
                <p class="text-xs text-gray-500">Upload digitized academic records</p>
              </div>
            </label>
            <label class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-green-50">
              <input type="checkbox" id="ep_files_view" data-key="files_view"
                     class="edit-priv-cb mt-0.5 h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
              <div>
                <p class="text-sm font-medium text-gray-800">View Files</p>
                <p class="text-xs text-gray-500">View, download, and print archived records</p>
              </div>
            </label>
    
            <label class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-green-50">
              <input type="checkbox" id="ep_records_organize" data-key="records_organize"
                     class="edit-priv-cb mt-0.5 h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
              <div>
                <p class="text-sm font-medium text-gray-800">Organize Records</p>
                <p class="text-xs text-gray-500">Move files/folders, rename files, manage file structure</p>
              </div>
            </label>
            <label class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-green-50">
              <input type="checkbox" id="ep_folders_add" data-key="folders_add"
                     class="edit-priv-cb mt-0.5 h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
              <div>
                <p class="text-sm font-medium text-gray-800">Add Folders</p>
                <p class="text-xs text-gray-500">Create new folders or categories</p>
              </div>
            </label>
            <label class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-green-50">
              <input type="checkbox" id="ep_records_delete" data-key="records_delete"
                     class="edit-priv-cb mt-0.5 h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
              <div>
                <p class="text-sm font-medium text-gray-800">Delete Records</p>
                <p class="text-xs text-gray-500">Delete archived files</p>
              </div>
            </label>
            <label class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-green-50">
              <input type="checkbox" id="ep_folders_delete" data-key="folders_delete"
                     class="edit-priv-cb mt-0.5 h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
              <div>
                <p class="text-sm font-medium text-gray-800">Delete Folders</p>
                <p class="text-xs text-gray-500">Delete folders or categories</p>
              </div>
            </label>
            <label class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-green-50">
              <input type="checkbox" id="ep_profile_edit" data-key="profile_edit"
                     class="edit-priv-cb mt-0.5 h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
              <div>
                <p class="text-sm font-medium text-gray-800">Edit Profile</p>
                <p class="text-xs text-gray-500">Edit user profile and personal settings</p>
              </div>
            </label>
            <label class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-green-50">
              <input type="checkbox" id="ep_user_management" data-key="user_management"
                     class="edit-priv-cb mt-0.5 h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
              <div>
                <p class="text-sm font-medium text-gray-800">Manage Users</p>
                <p class="text-xs text-gray-500">Add, edit, and assign roles to users</p>
              </div>
            </label>
            <label class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-green-50">
              <input type="checkbox" id="ep_system_backup" data-key="system_backup"
                     class="edit-priv-cb mt-0.5 h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
              <div>
                <p class="text-sm font-medium text-gray-800">System Backup</p>
                <p class="text-xs text-gray-500">Access system backup and restore features</p>
              </div>
            </label>
            <label class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-green-50">
              <input type="checkbox" id="ep_audit_logs" data-key="audit_logs"
                     class="edit-priv-cb mt-0.5 h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
              <div>
                <p class="text-sm font-medium text-gray-800">Audit Logs</p>
                <p class="text-xs text-gray-500">View system activity logs</p>
              </div>
            </label>
            <label class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-green-50 sm:col-span-2">
              <input type="checkbox" id="ep_full_admin" data-key="full_admin"
                     class="edit-priv-cb mt-0.5 h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
              <div>
                <p class="text-sm font-medium text-gray-800">Full Admin Access</p>
                <p class="text-xs text-gray-500">Automatically grants all privileges when selected</p>
              </div>
            </label>
          </div>
          <div id="editPrivilegeError" class="hidden mt-2 text-sm text-red-600"></div>
        </div>
      </div>

      <div class="flex gap-3 mt-6">
        <button type="button" onclick="closeEditModal()" 
                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
          Cancel
        </button>
        <button type="submit" 
                class="flex-1 px-4 py-2 bg-green-700 text-white rounded-lg hover:bg-green-800">
          Update Admin
        </button>
      </div>
    </form>
  </div>
</div>

<!-- User Privileges Modal -->
<div id="privilegesModal" class="modal">
  <div class="modal-content bg-white rounded-xl p-8 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
    <div class="flex items-center justify-between mb-6">
      <h3 class="text-2xl font-bold text-gray-800">User Privileges</h3>
      <button onclick="closePrivilegesModal()" class="text-gray-400 hover:text-gray-600">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
    </div>

    <div id="privilegesContent" class="space-y-4">
      <!-- Privileges will be loaded here dynamically -->
    </div>

    <div class="flex gap-3 mt-6">
      <button type="button" onclick="closePrivilegesModal()" 
              class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
        Close
      </button>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('additional_styles') ?>
.modal {
  display: none;
  position: fixed;
  z-index: 50;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0,0,0,0.5);
  animation: fadeIn 0.3s;
}

.modal.active {
  display: flex;
  align-items: center;
  justify-content: center;
}

.modal-content {
  animation: slideUp 0.3s;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes slideUp {
  from { transform: translateY(50px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// ────────────────────────────────────────────────
//   Password toggle helper function
// ────────────────────────────────────────────────
function togglePasswordVisibility(inputId, buttonId) {
  const input = document.getElementById(inputId);
  const button = document.getElementById(buttonId);
  if (!input || !button) return;

  button.addEventListener('click', () => {
    const isPassword = input.type === 'password';
    input.type = isPassword ? 'text' : 'password';

    const svg = button.querySelector('svg');
    if (!svg) return;

    svg.innerHTML = isPassword
      ? `
    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
  `
  : `
    <path d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19 12 19c.38 0 .747-.027 1.102-.08M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
    <path d="M2.999 3l14 14M9.172 9.172a3 3 0 014.656 4.656M16.02 11.777A10.477 10.477 0 0118.066 12c-1.292 4.338-5.31 7-10.066 7-1.066 0-2.09-.166-3.058-.477" />
  `;
  });
}

// ────────────────────────────────────────────────
//   Modal functions
// ────────────────────────────────────────────────

function openAddModal() {
  document.getElementById('addModal').classList.add('active');
}

function closeAddModal() {
  document.getElementById('addModal').classList.remove('active');
}

function openEditModal(user, userPrivileges) {
  document.getElementById('edit_full_name').value = user.full_name;
  document.getElementById('edit_email').value = user.email;
  document.getElementById('edit_username').value = user.username || '';
  document.getElementById('edit_role').value = user.role;
  document.getElementById('edit_access_level').value = user.access_level || 'full';
  document.getElementById('edit_status').value = user.status;
  
  document.getElementById('editForm').action = '<?= base_url('super-admin/edit-admin/'); ?>' + user.id;

  // Store user id for the AJAX privilege save
  document.getElementById('editForm').dataset.userId = user.id;

  // Populate privilege checkboxes from the server-supplied map
  var privMap = userPrivileges || {};
  document.querySelectorAll('.edit-priv-cb').forEach(function(cb) {
    cb.checked = privMap[cb.dataset.key] === true || privMap[cb.dataset.key] === 1;
  });

  // Clear any previous error message
  document.getElementById('editPrivilegeError').classList.add('hidden');

  document.getElementById('editModal').classList.add('active');
}

function closeEditModal() {
  document.getElementById('editModal').classList.remove('active');
}

function openPrivilegesModal(user) {
  console.log('Opening privileges modal for user:', user);
  const modal = document.getElementById('privilegesModal');
  const content = document.getElementById('privilegesContent');
  
  if (!modal || !content) {
    console.error('Modal elements not found');
    return;
  }
  
  modal.classList.add('active');
  displayPrivilegesForRole(user);
}

function displayPrivilegesForRole(user) {
  const content = document.getElementById('privilegesContent');
  
  const dbRole = user.role;
  const roleDisplay = (dbRole === '' || dbRole === 'admin') ? 'Admin' : 'User';
  const isAdmin = (dbRole === '' || dbRole === 'admin');
  
  const definitions = {
    'records_upload':   { label: 'Upload Records',    description: 'Upload digitized academic records',                          category: 'Records Management' },
    'files_view':       { label: 'View Files',         description: 'View, download, and print archived records',                 category: 'Records Management' },
    'records_organize': { label: 'Organize Records',   description: 'Move files/folders, rename files, manage file structure',   category: 'Records Management' },
    'folders_add':      { label: 'Add Folders',        description: 'Create new folders or categories',                          category: 'Records Management' },
    'records_delete':   { label: 'Delete Records',     description: 'Delete archived files',                                     category: 'Records Management' },
    'folders_delete':   { label: 'Delete Folders',     description: 'Delete folders or categories',                              category: 'Records Management' },
    'profile_edit':     { label: 'Edit Profile',       description: 'Edit user profile and personal settings',                   category: 'Profile Management' },
    'user_management':  { label: 'Manage Users',       description: 'Add, edit, and assign roles to users',                     category: 'Administration' },
    'system_backup':    { label: 'System Backup',      description: 'Access system backup and restore features',                 category: 'Administration' },
    'audit_logs':       { label: 'Audit Logs',         description: 'View system activity logs',                                 category: 'Administration' },
    'full_admin':       { label: 'Full Admin Access',  description: 'Automatically grants all privileges when selected',         category: 'Administration' }
  };
  
  const privileges = {
    'records_upload':   true,
    'files_view':       true,
    'records_organize': isAdmin,
    'folders_add':      isAdmin,
    'records_delete':   isAdmin,
    'folders_delete':   isAdmin,
    'profile_edit':     isAdmin,
    'user_management':  isAdmin,
    'system_backup':    isAdmin,
    'audit_logs':       isAdmin,
    'full_admin':       isAdmin
  };
  
  let html = `
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
      <p class="text-sm text-blue-800">
        <strong>User:</strong> ${user.full_name} (${user.email})<br>
        <strong>Role:</strong> ${roleDisplay}<br>
        <strong>Access Level:</strong> ${isAdmin ? 'Full Access' : 'Limited Access'}
      </p>
    </div>
    
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
      <p class="text-sm text-yellow-800">
        <strong>Note:</strong> Privileges are automatically assigned based on role and cannot be individually modified.
      </p>
    </div>
  `;
  
  const categories = {};
  for (const [key, def] of Object.entries(definitions)) {
    const cat = def.category || 'Other';
    if (!categories[cat]) categories[cat] = [];
    categories[cat].push({
      key,
      label: def.label,
      description: def.description,
      enabled: privileges[key] || false
    });
  }
  
  for (const [category, items] of Object.entries(categories)) {
    html += `
      <div class="mb-6">
        <h4 class="font-semibold text-gray-800 mb-3">${category}</h4>
        <div class="space-y-2">
    `;
    
    for (const item of items) {
      const iconColor = item.enabled ? 'text-green-600' : 'text-gray-400';
      const icon = item.enabled ? '✓' : '✗';
      
      html += `
        <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
          <span class="text-xl ${iconColor}">${icon}</span>
          <div class="flex-1">
            <p class="font-medium text-gray-800">${item.label}</p>
            <p class="text-xs text-gray-600">${item.description}</p>
          </div>
        </div>
      `;
    }
    
    html += `</div></div>`;
  }
  
  content.innerHTML = html;
}

function closePrivilegesModal() {
  document.getElementById('privilegesModal').classList.remove('active');
}

// Search Table
function searchTable() {
  const input = document.getElementById('searchInput');
  const filter = input.value.toLowerCase();
  const table = document.getElementById('usersTable');
  const rows = table.getElementsByTagName('tr');

  for (let i = 1; i < rows.length; i++) {
    const cells = rows[i].getElementsByTagName('td');
    let found = false;
    
    for (let j = 0; j < cells.length; j++) {
      const cell = cells[j];
      if (cell) {
        const textValue = cell.textContent || cell.innerText;
        if (textValue.toLowerCase().indexOf(filter) > -1) {
          found = true;
          break;
        }
      }
    }
    
    rows[i].style.display = found ? '' : 'none';
  }
}

// ────────────────────────────────────────────────
//   Edit form: save privileges via AJAX, then submit
// ────────────────────────────────────────────────
document.getElementById('editForm').addEventListener('submit', function(e) {
  e.preventDefault();

  var form     = this;
  var userId   = form.dataset.userId;
  var errorBox = document.getElementById('editPrivilegeError');
  var submitBtn = form.querySelector('button[type="submit"]');
  var originalText = submitBtn.textContent;

  // Build privileges object from checkboxes
  var privileges = {};
  document.querySelectorAll('.edit-priv-cb').forEach(function(cb) {
    privileges[cb.dataset.key] = cb.checked;
  });

  submitBtn.disabled = true;
  submitBtn.textContent = 'Saving…';
  errorBox.classList.add('hidden');

  fetch('<?= base_url('super-admin/update-user-privileges/'); ?>' + userId, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest'
    },
    body: JSON.stringify({ privileges: privileges })
  })
  .then(function(res) { return res.json(); })
  .then(function(json) {
    if (!json.success) {
      errorBox.textContent = json.message || 'Failed to save privileges.';
      errorBox.classList.remove('hidden');
      submitBtn.disabled = false;
      submitBtn.textContent = originalText;
      return;
    }
    // Privilege save succeeded – now submit the main form
    form.submit();
  })
  .catch(function() {
    errorBox.textContent = 'Network error saving privileges. Please try again.';
    errorBox.classList.remove('hidden');
    submitBtn.disabled = false;
    submitBtn.textContent = originalText;
  });
});

// ────────────────────────────────────────────────
//   Full Admin checkbox: check/uncheck all others
// ────────────────────────────────────────────────
(function () {
  // The 11 individual privilege keys (everything except full_admin itself)
  var ALL_PRIV_KEYS = [
    'records_upload', 'files_view', 'records_organize',
    'folders_add', 'records_delete', 'folders_delete',
    'profile_edit', 'user_management', 'system_backup', 'audit_logs'
  ];

  // ── Add modal ──────────────────────────────────
  var addFullAdmin = document.getElementById('add_full_admin');
  if (addFullAdmin) {
    addFullAdmin.addEventListener('change', function () {
      var addCbs = document.querySelectorAll('.add-priv-cb');
      addCbs.forEach(function (cb) {
        if (cb !== addFullAdmin) cb.checked = addFullAdmin.checked;
      });
    });

    // If any individual add-checkbox is unchecked, uncheck Full Admin
    document.querySelectorAll('.add-priv-cb').forEach(function (cb) {
      if (cb === addFullAdmin) return;
      cb.addEventListener('change', function () {
        if (!cb.checked) addFullAdmin.checked = false;
        var allChecked = Array.from(document.querySelectorAll('.add-priv-cb')).every(function (c) {
          return c.checked;
        });
        addFullAdmin.checked = allChecked;
      });
    });
  }

  // ── Edit modal ─────────────────────────────────
  var editFullAdmin = document.getElementById('ep_full_admin');
  if (editFullAdmin) {
    editFullAdmin.addEventListener('change', function () {
      var editCbs = document.querySelectorAll('.edit-priv-cb');
      editCbs.forEach(function (cb) {
        if (cb !== editFullAdmin) cb.checked = editFullAdmin.checked;
      });
    });

    // If any individual edit-checkbox is unchecked, uncheck Full Admin
    document.querySelectorAll('.edit-priv-cb').forEach(function (cb) {
      if (cb === editFullAdmin) return;
      cb.addEventListener('change', function () {
        if (!cb.checked) editFullAdmin.checked = false;
        var allChecked = Array.from(document.querySelectorAll('.edit-priv-cb')).every(function (c) {
          return c.checked;
        });
        editFullAdmin.checked = allChecked;
      });
    });
  }
})();

// ────────────────────────────────────────────────
//   Attach password toggles
// ────────────────────────────────────────────────
togglePasswordVisibility('initial_password', 'toggleInitialPassword');
togglePasswordVisibility('edit_new_password', 'toggleEditPassword');

// ────────────────────────────────────────────────
//   Global event listeners
// ────────────────────────────────────────────────
window.onclick = function(event) {
  const addModal = document.getElementById('addModal');
  const editModal = document.getElementById('editModal');
  const privilegesModal = document.getElementById('privilegesModal');
  
  if (event.target === addModal) closeAddModal();
  if (event.target === editModal) closeEditModal();
  if (event.target === privilegesModal) closePrivilegesModal();
};

document.addEventListener('keydown', function(event) {
  if (event.key === 'Escape') {
    closeAddModal();
    closeEditModal();
    closePrivilegesModal();
  }
});
</script>
<?= $this->endSection() ?>