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
        <p class="text-xs text-gray-500">Requested: <?= date('M d, Y', strtotime($pending['requested_at'])); ?></p>
      </div>
      <div class="flex gap-2">
        <a href="<?= base_url('super-admin/approve-admin/' . $pending['user_id']); ?>" 
           class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 font-medium"
           onclick="return confirm('Approve this admin?');">
          ✓ Approve
        </a>
        <a href="<?= base_url('super-admin/reject-admin/' . $pending['user_id']); ?>" 
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

<!-- Add Admin Button + Group Privileges Button -->
<div class="mb-6 flex gap-3 flex-wrap">
  <button onclick="openAddModal()" class="bg-green-700 text-white px-6 py-3 rounded-lg hover:bg-green-800 font-semibold flex items-center gap-2">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
    </svg>
    Add User
  </button>
  <button onclick="openGroupPrivilegesModal()" class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 font-semibold flex items-center gap-2">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0
               01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622
               5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
    </svg>
    Group Privileges
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
    <?php
      $uid           = $user['user_id'];
      $canEdit       = $can_edit_map[$uid]         ?? true;
      $canToggle     = $can_toggle_status_map[$uid] ?? true;
      $canDelete     = $can_delete_map[$uid]        ?? true;
      $lockOwnerName = $lock_owner_map[$uid]        ?? 'another administrator';
      $lockedMsg     = 'This user was configured by ' . $lockOwnerName . ' and cannot be modified.';
    ?>

    <?php if ($canEdit): ?>
      <button onclick='openEditModal(<?= json_encode($user); ?>, <?= json_encode($user_privileges_map[$user["user_id"]] ?? []); ?>)'
              class="text-blue-600 hover:text-blue-800 font-medium text-sm">
        Edit
      </button>
    <?php else: ?>
      <button type="button"
              title="<?= esc($lockedMsg) ?>"
              onclick="showLockedNotice('<?= esc(addslashes($lockedMsg)) ?>')"
              class="text-gray-300 font-medium text-sm cursor-not-allowed select-none"
              style="pointer-events:auto">
        Edit
      </button>
    <?php endif; ?>

    <?php if ($canToggle): ?>
      <?php if ($user['status'] !== 'inactive'): ?>
        <a href="<?= base_url('super-admin/toggle-suspend/' . $uid); ?>"
           class="text-orange-600 hover:text-orange-800 font-medium text-sm"
           onclick="return confirm('Deactivate this user?');">
          Deactivate
        </a>
      <?php else: ?>
        <a href="<?= base_url('super-admin/toggle-suspend/' . $uid); ?>"
           class="text-green-600 hover:text-green-800 font-medium text-sm"
           onclick="return confirm('Reactivate this user?');">
          Reactivate
        </a>
      <?php endif; ?>
    <?php else: ?>
      <button type="button"
              title="<?= esc($lockedMsg) ?>"
              onclick="showLockedNotice('<?= esc(addslashes($lockedMsg)) ?>')"
              class="text-gray-300 font-medium text-sm cursor-not-allowed select-none"
              style="pointer-events:auto">
        <?= $user['status'] !== 'inactive' ? 'Deactivate' : 'Reactivate' ?>
      </button>
    <?php endif; ?>

    <?php if ($canDelete): ?>
      <a href="<?= base_url('super-admin/delete-admin/' . $uid); ?>"
         class="text-red-600 hover:text-red-800 font-medium text-sm"
         onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
        Delete
      </a>
    <?php else: ?>
      <button type="button"
              title="<?= esc($lockedMsg) ?>"
              onclick="showLockedNotice('<?= esc(addslashes($lockedMsg)) ?>')"
              class="text-gray-300 font-medium text-sm cursor-not-allowed select-none"
              style="pointer-events:auto">
        Delete
      </button>
    <?php endif; ?>

  </div>
</td>
        </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="9" class="px-6 py-12 text-center text-gray-500">
            No users found. Click "Add User" to create one.
          </td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<?= $this->endSection() ?>

<?= $this->section('modals') ?>

<!-- ===== Add User Modal ===== -->
<div id="addModal" class="modal">
  <div class="modal-content bg-white rounded-xl p-8 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
    <div class="flex items-center justify-between mb-6">
      <h3 class="text-2xl font-bold text-gray-800">Add New User</h3>
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

        <!-- Initial Password -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Initial Password *</label>
          <div class="relative">
            <input type="password" name="initial_password" id="initial_password" required minlength="8"
                   class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                   placeholder="Minimum 8 characters">
            <button type="button" id="toggleInitialPassword"
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700 focus:outline-none">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
              </svg>
            </button>
          </div>
          <p class="text-xs text-gray-500 mt-1">User will be required to change this password on first login</p>
        </div>

        <!-- ✅ NEW: PIN field in Add modal -->
        <div>
           <label class="block text-sm font-medium text-gray-700 mb-2">PIN <span class="text-red-500">*</span> <span class="text-gray-400 font-normal">(Required — exactly 4 digits)</span></label>

          <div class="relative">
            <input type="password" name="mpin" id="add_mpin" maxlength="4"
                   class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                   placeholder="Required: e.g. 1234"
                   required
                   oninput="this.value=this.value.replace(/[^0-9]/g,'')">
            <button type="button" id="toggleAddMpin"
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700 focus:outline-none">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
              </svg>
            </button>
          </div>
          <p class="text-xs text-gray-500 mt-1">Exactly 4 digits. This PIN will be included in the welcome email. Valid for 30 days.</p>

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
            <label class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-green-50">
              <input type="checkbox" name="privileges[]" value="record_types"
                     class="add-priv-cb mt-0.5 h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
              <div>
                <p class="text-sm font-medium text-gray-800">Manage Record Types</p>
                <p class="text-xs text-gray-500">Add, edit, and delete OCR document types and keywords</p>
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
          Create User Account
        </button>
      </div>
    </form>
  </div>
</div>

<!-- ===== Edit User Modal ===== -->
<div id="editModal" class="modal">
  <div class="modal-content bg-white rounded-xl p-8 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
    <div class="flex items-center justify-between mb-6">
      <h3 class="text-2xl font-bold text-gray-800">Edit User</h3>
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

        <!-- New Password -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">New Password <span class="text-gray-400 font-normal">(Optional)</span></label>
          <div class="relative">
            <input type="password" name="new_password" id="edit_new_password" minlength="8"
                   class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                   placeholder="Leave blank to keep current password">
            <button type="button" id="toggleEditPassword"
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700 focus:outline-none">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
              </svg>
            </button>
          </div>
          <p class="text-xs text-gray-500 mt-1">Only fill this if you want to reset the password</p>
        </div>

        <!-- ✅ NEW: MPIN field in Edit modal -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">PIN <span class="text-gray-400 font-normal">(Optional)</span></label>
          <div class="relative">
            <input type="password" name="mpin" id="edit_mpin" maxlength="4"
                   class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                   placeholder="Leave blank to keep current MPIN"
                   oninput="this.value=this.value.replace(/[^0-9]/g,'')">
            <button type="button" id="toggleEditMpin"
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700 focus:outline-none">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
              </svg>
            </button>
          </div>
         <p class="text-xs text-gray-500 mt-1">Digits only, exactly 4 digits. Leave blank to keep current PIN.</p>
          <p id="edit_mpin_status" class="text-xs text-purple-600 mt-1 hidden"></p>
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
            <label class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-green-50">
              <input type="checkbox" id="ep_record_types" data-key="record_types"
                     class="edit-priv-cb mt-0.5 h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
              <div>
                <p class="text-sm font-medium text-gray-800">Manage Record Types</p>
                <p class="text-xs text-gray-500">Add, edit, and delete OCR document types and keywords</p>
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

          <!-- Folder Access -->
          <div class="border-t border-gray-200 pt-4 mt-2">
            <p class="text-sm font-semibold text-gray-700 mb-1">📁 Folder Access</p>
            <p class="text-xs text-gray-500 mb-3">
              Assign which top-level folders this user can see and access.
              Leave all unchecked to block access to all folders.
              Admins always have full access regardless of this setting.
            </p>
            <div id="folderCheckboxList" class="space-y-1 max-h-48 overflow-y-auto pr-1">
              <p class="text-xs text-gray-400 italic">Loading folders…</p>
            </div>
          </div>
          <input type="hidden" name="folder_access" id="folderAccessInput">
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
          Update User
        </button>
      </div>
    </form>
  </div>
</div>

<!-- ===== Group Privileges Modal ===== -->
<div id="groupPrivilegesModal" class="modal">
  <div class="modal-content bg-white rounded-xl p-8 w-full max-w-4xl max-h-[90vh] overflow-y-auto">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h3 class="text-2xl font-bold text-gray-800">Group Privileges</h3>
        <p class="text-sm text-gray-500 mt-1">Default privileges per role across system modules</p>
      </div>
      <button onclick="closeGroupPrivilegesModal()" class="text-gray-400 hover:text-gray-600">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <!-- Group tab bar -->
    <div class="flex gap-1 mb-6 border-b border-gray-200">
      <button id="gpTab_default" onclick="switchGpTab('default')"
              class="gp-tab px-5 py-2 text-sm font-semibold rounded-t-lg border border-b-0 border-gray-200 bg-indigo-600 text-white">
        Default Privileges
      </button>
      <button id="gpTab_admin" onclick="switchGpTab('admin')"
              class="gp-tab px-5 py-2 text-sm font-semibold rounded-t-lg border border-b-0 border-gray-200 bg-white text-gray-600 hover:bg-gray-50">
        Admin
      </button>
      <button id="gpTab_user" onclick="switchGpTab('user')"
              class="gp-tab px-5 py-2 text-sm font-semibold rounded-t-lg border border-b-0 border-gray-200 bg-white text-gray-600 hover:bg-gray-50">
        User
      </button>
    </div>

    <div id="groupPrivilegesContent">
      <p class="text-gray-400 text-center py-8">Loading...</p>
    </div>
    <div class="flex gap-3 mt-6">
      <button type="button" onclick="closeGroupPrivilegesModal()"
              class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
        Close
      </button>
    </div>
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
    <div id="privilegesContent" class="space-y-4"></div>
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

  // PHP → JS privilege lock map  { user_id: bool }
var _canEditMap         = <?= json_encode($can_edit_map          ?? []) ?>;
var _lockOwnerMap       = <?= json_encode($lock_owner_map        ?? []) ?>;
var _canToggleStatusMap = <?= json_encode($can_toggle_status_map ?? []) ?>;
var _canDeleteMap       = <?= json_encode($can_delete_map        ?? []) ?>;

// ────────────────────────────────────────────────
//   Password / PIN toggle helper
// ────────────────────────────────────────────────
function togglePasswordVisibility(inputId, buttonId) {
  const input  = document.getElementById(inputId);
  const button = document.getElementById(buttonId);
  if (!input || !button) return;
  button.addEventListener('click', () => {
    const isPassword = input.type === 'password';
    input.type = isPassword ? 'text' : 'password';
    const svg = button.querySelector('svg');
    if (!svg) return;
    svg.innerHTML = isPassword
      ? `<path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
         <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />`
      : `<path d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19 12 19c.38 0 .747-.027 1.102-.08M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
         <path d="M2.999 3l14 14M9.172 9.172a3 3 0 014.656 4.656M16.02 11.777A10.477 10.477 0 0118.066 12c-1.292 4.338-5.31 7-10.066 7-1.066 0-2.09-.166-3.058-.477" />`;
  });
}

// ────────────────────────────────────────────────
//   Group Privileges Modal
// ────────────────────────────────────────────────
function openGroupPrivilegesModal() {
  document.getElementById('groupPrivilegesModal').classList.add('active');
  switchGpTab('default');
}
function closeGroupPrivilegesModal() {
  document.getElementById('groupPrivilegesModal').classList.remove('active');
}

// ── Tab switcher ──────────────────────────────────────────────────────────────
function switchGpTab(tab) {
  // Update tab button styles
  ['default', 'admin', 'user'].forEach(function(t) {
    var btn = document.getElementById('gpTab_' + t);
    if (!btn) return;
    if (t === tab) {
      btn.className = 'gp-tab px-5 py-2 text-sm font-semibold rounded-t-lg border border-b-0 border-gray-200 bg-indigo-600 text-white';
    } else {
      btn.className = 'gp-tab px-5 py-2 text-sm font-semibold rounded-t-lg border border-b-0 border-gray-200 bg-white text-gray-600 hover:bg-gray-50';
    }
  });

  if (tab === 'default') {
    loadGroupPrivileges();
  } else {
    loadGroupUserList(tab);
  }
}

// ── Default Privileges tab ────────────────────────────────────────────────────
function loadGroupPrivileges() {
  const container = document.getElementById('groupPrivilegesContent');
  container.innerHTML = '<p class="text-gray-400 text-center py-8">Loading...</p>';
  fetch('<?= base_url('super-admin/group-privileges') ?>')
    .then(r => r.json())
    .then(data => {
      if (!data.success) { container.innerHTML = '<p class="text-red-500 text-center py-8">Failed to load.</p>'; return; }
      container.innerHTML = buildGroupPrivilegesTable(data.matrix, data.definitions);
    })
    .catch(() => { container.innerHTML = '<p class="text-red-500 text-center py-8">Network error.</p>'; });
}
function buildGroupPrivilegesTable(matrix, definitions) {
  const categories = {};
  Object.entries(definitions).forEach(([key, def]) => {
    if (!categories[def.category]) categories[def.category] = [];
    categories[def.category].push({ key, ...def });
  });
  const check = `<span class="text-green-600 font-bold text-lg">&#10003;</span>`;
  const dash  = `<span class="text-gray-300 text-lg">&ndash;</span>`;
  let html = `<table class="w-full text-sm border-collapse">
    <thead><tr class="bg-gray-100">
      <th class="text-left px-4 py-3 font-semibold text-gray-700 border border-gray-200 w-1/2">Privilege</th>
      <th class="text-center px-4 py-3 font-semibold text-indigo-700 border border-gray-200">ADMIN</th>
      <th class="text-center px-4 py-3 font-semibold text-green-700 border border-gray-200">USER</th>
    </tr></thead><tbody>`;
  Object.entries(categories).forEach(([catName, privs]) => {
    html += `<tr class="bg-gray-50"><td colspan="3" class="px-4 py-2 text-xs font-semibold uppercase tracking-wider text-gray-500 border border-gray-200">${catName}</td></tr>`;
    privs.forEach(priv => {
      const adminHas = matrix['admin'] && matrix['admin'][priv.key] !== undefined;
      const userHas  = matrix['user']  && matrix['user'][priv.key]  !== undefined;
      html += `<tr class="hover:bg-gray-50">
        <td class="px-4 py-2 border border-gray-200"><div class="font-medium text-gray-800">${priv.label}</div><div class="text-xs text-gray-400">${priv.description}</div></td>
        <td class="text-center px-4 py-2 border border-gray-200">${adminHas ? check : dash}</td>
        <td class="text-center px-4 py-2 border border-gray-200">${userHas  ? check : dash}</td>
      </tr>`;
    });
  });
  html += `</tbody></table><p class="text-xs text-gray-400 mt-4">* Group privileges are fixed defaults. Individual overrides are tracked in the Activity Log.</p>`;
  return html;
}

// ── Admin / User tabs — individual user privilege list ─────────────────────────
// ── Admin / User tabs — individual user privilege list ─────────────────────────
function loadGroupUserList(role) {
  var container = document.getElementById('groupPrivilegesContent');
  container.innerHTML = '<p class="text-gray-400 text-center py-8">Loading...</p>';

  var privKeys = [
    { key: 'records_upload',   label: 'Upload Records' },
    { key: 'files_view',       label: 'View Files' },
    { key: 'records_organize', label: 'Organize Records' },
    { key: 'folders_add',      label: 'Add Folders' },
    { key: 'records_delete',   label: 'Delete Records' },
    { key: 'folders_delete',   label: 'Delete Folders' },
    { key: 'profile_edit',     label: 'Edit Profile' },
    { key: 'user_management',  label: 'Manage Users' },
    { key: 'system_backup',    label: 'System Backup' },
    { key: 'audit_logs',       label: 'Audit Logs' },
    { key: 'full_admin',       label: 'Full Admin' },
  ];

  fetch('<?= base_url('super-admin/users-by-role/') ?>' + role)
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (!data.success) {
        container.innerHTML = '<p class="text-red-500 text-center py-8">Failed to load users.</p>';
        return;
      }
      if (!data.users || data.users.length === 0) {
        var label = (role === 'admin') ? 'admin' : 'user';
        container.innerHTML = '<p class="text-gray-400 text-center py-8">No ' + label + ' accounts found.</p>';
        return;
      }
      // Map API response to the shape expected by buildUserPrivilegesTable
      var enrichedUsers = data.users.map(function(u) {
        return {
          id:         u.user_id,
          name:       u.full_name,
          email:      u.email,
          privileges: u.privileges || {}
        };
      });
      container.innerHTML = buildUserPrivilegesTable(enrichedUsers, privKeys, role);
    })
    .catch(function() {
      container.innerHTML = '<p class="text-red-500 text-center py-8">Network error. Please try again.</p>';
    });
}


function buildUserPrivilegesTable(users, privKeys, role) {
  var roleLabel = role === 'admin' ? 'Admin' : 'User';
  var check = '<span class="text-green-600 font-bold text-lg">&#10003;</span>';
  var dash  = '<span class="text-gray-300 text-lg">&ndash;</span>';

  // Header row: first col = Privilege, then one col per user
  var html = '<div class="overflow-x-auto">';
  html += '<table class="w-full text-sm border-collapse">';
  html += '<thead><tr class="bg-gray-100">';
  html += '<th class="text-left px-4 py-3 font-semibold text-gray-700 border border-gray-200 sticky left-0 bg-gray-100" style="min-width:160px;">Privilege</th>';
  users.forEach(function(u) {
    var initials = u.name.split(' ').map(function(w){ return w[0]; }).slice(0,2).join('').toUpperCase();
    html += '<th class="text-center px-3 py-3 border border-gray-200" style="min-width:110px;">'
          + '<div class="flex flex-col items-center gap-1">'
          + '<div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-semibold">' + initials + '</div>'
          + '<span class="font-semibold text-gray-700 text-xs leading-tight text-center" style="max-width:100px;word-break:break-word;">' + u.name + '</span>'
          + '</div></th>';
  });
  html += '</tr></thead><tbody>';

  // One row per privilege
  privKeys.forEach(function(priv) {
    html += '<tr class="hover:bg-gray-50">';
    html += '<td class="px-4 py-2 border border-gray-200 font-medium text-gray-800 sticky left-0 bg-white">' + priv.label + '</td>';
    users.forEach(function(u) {
      var has = u.privileges[priv.key] === true || u.privileges[priv.key] === 1;
      html += '<td class="text-center px-3 py-2 border border-gray-200">' + (has ? check : dash) + '</td>';
    });
    html += '</tr>';
  });

  html += '</tbody></table></div>';
  html += '<p class="text-xs text-gray-400 mt-4">Showing individual privilege assignments for all <strong>' + roleLabel + '</strong> accounts. Edit via the User Management table.</p>';
  return html;
}
document.addEventListener('DOMContentLoaded', function() {
  const gpModal = document.getElementById('groupPrivilegesModal');
  if (gpModal) gpModal.addEventListener('click', function(e) { if (e.target === this) closeGroupPrivilegesModal(); });
});

// ────────────────────────────────────────────────
//   Add Modal
// ────────────────────────────────────────────────
function openAddModal() {
  document.getElementById('addModal').classList.add('active');
}
function closeAddModal() {
  document.getElementById('addModal').classList.remove('active');
}

// ────────────────────────────────────────────────
//   Edit Modal
// ────────────────────────────────────────────────
function openEditModal(user, userPrivileges) {
  document.getElementById('edit_full_name').value    = user.full_name;
  document.getElementById('edit_email').value        = user.email;
  document.getElementById('edit_username').value     = user.username || '';
  document.getElementById('edit_role').value         = user.role;
  document.getElementById('edit_access_level').value = user.access_level || 'full';
  document.getElementById('edit_status').value       = user.status;

  // Clear MPIN field and status note when opening
  document.getElementById('edit_mpin').value = '';
  const mpinStatus = document.getElementById('edit_mpin_status');
  if (user.has_mpin) {
    mpinStatus.textContent = '🔒 This user already has a PIN set. Enter a new one to replace it.';
    mpinStatus.classList.remove('hidden');
  } else {
    mpinStatus.textContent = '';
    mpinStatus.classList.add('hidden');
  }

  document.getElementById('editForm').action = '<?= base_url('super-admin/edit-admin/'); ?>' + user.user_id;
  document.getElementById('editForm').dataset.userId  = user.user_id;
  // Stamp the privilege-edit permission from the PHP-generated map.
  const _editable = (typeof _canEditMap !== 'undefined' && _canEditMap[user.user_id] === false) ? 'false' : 'true';
document.getElementById('editForm').dataset.canEdit  = _editable;
document.getElementById('editForm').dataset.lockMsg  =
    (_editable === 'false' && typeof _lockOwnerMap !== 'undefined')
        ? ('This user was already configured by ' + (_lockOwnerMap[user.user_id] || 'another administrator') + ' and cannot be modified.')
        : '';

  // Populate privilege checkboxes
  var privMap = userPrivileges || {};
  document.querySelectorAll('.edit-priv-cb').forEach(function(cb) {
    cb.checked = privMap[cb.dataset.key] === true || privMap[cb.dataset.key] === 1;
  });

  document.getElementById('editPrivilegeError').classList.add('hidden');
  document.getElementById('editModal').classList.add('active');
  loadFolderAccess(user.user_id);
}
function closeEditModal() {
  document.getElementById('editModal').classList.remove('active');
}

// ────────────────────────────────────────────────
//   Privileges View Modal
// ────────────────────────────────────────────────
function openPrivilegesModal(user) {
  const modal   = document.getElementById('privilegesModal');
  const content = document.getElementById('privilegesContent');
  if (!modal || !content) return;
  modal.classList.add('active');
  displayPrivilegesForRole(user);
}
function displayPrivilegesForRole(user) {
  const content    = document.getElementById('privilegesContent');
  const dbRole     = user.role;
  const roleDisplay = (dbRole === '' || dbRole === 'admin') ? 'Admin' : 'User';
  const isAdmin    = (dbRole === '' || dbRole === 'admin');
  const definitions = {
    'records_upload':   { label: 'Upload Records',    description: 'Upload digitized academic records',                        category: 'Records Management' },
    'files_view':       { label: 'View Files',         description: 'View, download, and print archived records',               category: 'Records Management' },
    'records_organize': { label: 'Organize Records',   description: 'Move files/folders, rename files, manage file structure', category: 'Records Management' },
    'folders_add':      { label: 'Add Folders',        description: 'Create new folders or categories',                        category: 'Records Management' },
    'records_delete':   { label: 'Delete Records',     description: 'Delete archived files',                                   category: 'Records Management' },
    'folders_delete':   { label: 'Delete Folders',     description: 'Delete folders or categories',                            category: 'Records Management' },
    'profile_edit':     { label: 'Edit Profile',       description: 'Edit user profile and personal settings',                 category: 'Profile Management' },
    'user_management':  { label: 'Manage Users',       description: 'Add, edit, and assign roles to users',                   category: 'Administration' },
    'system_backup':    { label: 'System Backup',      description: 'Access system backup and restore features',               category: 'Administration' },
    'audit_logs':       { label: 'Audit Logs',         description: 'View system activity logs',                               category: 'Administration' },
    'full_admin':       { label: 'Full Admin Access',  description: 'Automatically grants all privileges when selected',       category: 'Administration' }
  };
  const privileges = {
    'records_upload': true, 'files_view': true,
    'records_organize': isAdmin, 'folders_add': isAdmin, 'records_delete': isAdmin,
    'folders_delete': isAdmin, 'profile_edit': isAdmin, 'user_management': isAdmin,
    'system_backup': isAdmin, 'audit_logs': isAdmin, 'full_admin': isAdmin
  };
  let html = `<div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
    <p class="text-sm text-blue-800"><strong>User:</strong> ${user.full_name} (${user.email})<br>
    <strong>Role:</strong> ${roleDisplay}<br>
    <strong>Access Level:</strong> ${isAdmin ? 'Full Access' : 'Limited Access'}</p></div>
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
    <p class="text-sm text-yellow-800"><strong>Note:</strong> Privileges are automatically assigned based on role and cannot be individually modified.</p></div>`;
  const categories = {};
  for (const [key, def] of Object.entries(definitions)) {
    const cat = def.category || 'Other';
    if (!categories[cat]) categories[cat] = [];
    categories[cat].push({ key, label: def.label, description: def.description, enabled: privileges[key] || false });
  }
  for (const [category, items] of Object.entries(categories)) {
    html += `<div class="mb-6"><h4 class="font-semibold text-gray-800 mb-3">${category}</h4><div class="space-y-2">`;
    for (const item of items) {
      html += `<div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
        <span class="text-xl ${item.enabled ? 'text-green-600' : 'text-gray-400'}">${item.enabled ? '✓' : '✗'}</span>
        <div class="flex-1"><p class="font-medium text-gray-800">${item.label}</p><p class="text-xs text-gray-600">${item.description}</p></div>
      </div>`;
    }
    html += `</div></div>`;
  }
  content.innerHTML = html;
}
function closePrivilegesModal() {
  document.getElementById('privilegesModal').classList.remove('active');
}

// ────────────────────────────────────────────────
//   Search Table
// ────────────────────────────────────────────────
function searchTable() {
  const filter = document.getElementById('searchInput').value.toLowerCase();
  const rows   = document.getElementById('usersTable').getElementsByTagName('tr');
  for (let i = 1; i < rows.length; i++) {
    const cells = rows[i].getElementsByTagName('td');
    let found = false;
    for (let j = 0; j < cells.length; j++) {
      if ((cells[j].textContent || cells[j].innerText).toLowerCase().indexOf(filter) > -1) { found = true; break; }
    }
    rows[i].style.display = found ? '' : 'none';
  }
}

// ────────────────────────────────────────────────
//   Folder Access helpers
// ────────────────────────────────────────────────
function loadFolderAccess(userId) {
  var container = document.getElementById('folderCheckboxList');
  container.innerHTML = '<p class="text-xs text-gray-400 italic">Loading…</p>';
  fetch('<?= base_url('super-admin/get-user-folders/') ?>' + userId, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (!data.success || !data.folders.length) {
        container.innerHTML = '<p class="text-xs text-gray-400 italic">No folders found in the archive root yet.</p>';
        return;
      }
      container.innerHTML = data.folders.map(function(folder) {
        var checked = data.assigned.includes(folder) ? 'checked' : '';
        return '<label class="flex items-center gap-2 p-2 bg-gray-50 rounded cursor-pointer hover:bg-green-50">'
          + '<input type="checkbox" class="folder-access-cb h-4 w-4 rounded border-gray-300 text-green-600" value="' + folder + '" ' + checked + '>'
          + '<span class="text-sm text-gray-700">📁 ' + folder + '</span></label>';
      }).join('');
    })
    .catch(function() { container.innerHTML = '<p class="text-xs text-red-500">Could not load folders.</p>'; });
}
function collectFolderAccess() {
  var checked = Array.from(document.querySelectorAll('.folder-access-cb:checked')).map(function(cb) { return cb.value; });
  document.getElementById('folderAccessInput').value = JSON.stringify(checked);
}

// ────────────────────────────────────────────────
//   Edit form submit: save privileges + MPIN via AJAX, then submit
// ────────────────────────────────────────────────
document.getElementById('editForm').addEventListener('submit', function(e) {
  e.preventDefault();
  collectFolderAccess();

  var form      = this;
  var userId    = form.dataset.userId;
  var errorBox  = document.getElementById('editPrivilegeError');
  var submitBtn = form.querySelector('button[type="submit"]');
  var origText  = submitBtn.textContent;

  // Client-side privilege lock guard (mirrors server-side check).
  if (form.dataset.canEdit === 'false') {
    const lockedMsg = form.dataset.lockMsg || 'This user was already configured by another administrator and cannot be modified.';
    errorBox.textContent = lockedMsg;
    errorBox.classList.remove('hidden');
    return;
}


  // Validate MPIN if filled in
  var mpinVal = document.getElementById('edit_mpin').value.trim();
  if (mpinVal !== '' && !/^\d{4}$/.test(mpinVal)) {
    errorBox.textContent = 'MPIN must be exactly 4 digits, or leave it blank to keep the current one.';
    errorBox.classList.remove('hidden');
    return;
  }

  var privileges = {};
  document.querySelectorAll('.edit-priv-cb').forEach(function(cb) { privileges[cb.dataset.key] = cb.checked; });

  submitBtn.disabled    = true;
  submitBtn.textContent = 'Saving…';
  errorBox.classList.add('hidden');

  // Step 1: Save privileges
  fetch('<?= base_url('super-admin/update-user-privileges/'); ?>' + userId, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
    body: JSON.stringify({ privileges: privileges })
  })
  .then(function(res) { return res.json(); })
  .then(function(json) {
    if (!json.success) {
      errorBox.textContent = json.message || 'Failed to save privileges.';
      errorBox.classList.remove('hidden');
      submitBtn.disabled    = false;
      submitBtn.textContent = origText;
      return;
    }

    // Step 2: Save MPIN if provided
    if (mpinVal !== '') {
      return fetch('<?= base_url('super-admin/set-mpin/'); ?>' + userId, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ mpin: mpinVal })
      })
      .then(function(res) { return res.json(); })
      .then(function(mpinJson) {
        if (!mpinJson.success) {
          errorBox.textContent = 'User updated but MPIN failed: ' + (mpinJson.message || 'Unknown error');
          errorBox.classList.remove('hidden');
          submitBtn.disabled    = false;
          submitBtn.textContent = origText;
          return;
        }
        form.submit();
      });
    } else {
      // No MPIN entered — just submit the form
      form.submit();
    }
  })
  .catch(function() {
    errorBox.textContent = 'Network error. Please try again.';
    errorBox.classList.remove('hidden');
    submitBtn.disabled    = false;
    submitBtn.textContent = origText;
  });
});

// ────────────────────────────────────────────────
//   Full Admin checkbox: check/uncheck all others
// ────────────────────────────────────────────────
(function() {
  var addFullAdmin = document.getElementById('add_full_admin');
  if (addFullAdmin) {
    addFullAdmin.addEventListener('change', function() {
      document.querySelectorAll('.add-priv-cb').forEach(function(cb) { if (cb !== addFullAdmin) cb.checked = addFullAdmin.checked; });
    });
    document.querySelectorAll('.add-priv-cb').forEach(function(cb) {
      if (cb === addFullAdmin) return;
      cb.addEventListener('change', function() {
        if (!cb.checked) addFullAdmin.checked = false;
        addFullAdmin.checked = Array.from(document.querySelectorAll('.add-priv-cb')).every(function(c) { return c.checked; });
      });
    });
  }
  var editFullAdmin = document.getElementById('ep_full_admin');
  if (editFullAdmin) {
    editFullAdmin.addEventListener('change', function() {
      document.querySelectorAll('.edit-priv-cb').forEach(function(cb) { if (cb !== editFullAdmin) cb.checked = editFullAdmin.checked; });
    });
    document.querySelectorAll('.edit-priv-cb').forEach(function(cb) {
      if (cb === editFullAdmin) return;
      cb.addEventListener('change', function() {
        if (!cb.checked) editFullAdmin.checked = false;
        editFullAdmin.checked = Array.from(document.querySelectorAll('.edit-priv-cb')).every(function(c) { return c.checked; });
      });
    });
  }
})();

// ────────────────────────────────────────────────
//   Attach password/PIN toggles
// ────────────────────────────────────────────────
togglePasswordVisibility('initial_password',  'toggleInitialPassword');
togglePasswordVisibility('edit_new_password', 'toggleEditPassword');
togglePasswordVisibility('add_mpin',          'toggleAddMpin');
togglePasswordVisibility('edit_mpin',         'toggleEditMpin');

// ────────────────────────────────────────────────
//   Global close on backdrop click / Escape key
// ────────────────────────────────────────────────
function showLockedNotice(msg) {
  // Reuses your existing showDialog helper already defined in this file
  showDialog(msg, 'warning');
}
window.onclick = function(event) {
  if (event.target === document.getElementById('addModal'))        closeAddModal();
  if (event.target === document.getElementById('editModal'))       closeEditModal();
  if (event.target === document.getElementById('privilegesModal')) closePrivilegesModal();
};
document.addEventListener('keydown', function(event) {
  if (event.key === 'Escape') { closeAddModal(); closeEditModal(); closePrivilegesModal(); }
});
</script>
<?= $this->endSection() ?>
