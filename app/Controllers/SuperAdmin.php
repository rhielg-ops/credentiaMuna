<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;
use App\Models\ActivityLogModel;
use App\Models\DashboardModel;
use App\Models\UserPrivilegeModel;
use App\Models\UserFolderAccessModel;
use App\Models\MpinModel;
use App\Models\GroupPrivilegeModel;
use App\Libraries\EmailService;


class SuperAdmin extends BaseController
{
    protected $userModel;
    protected $activityLogModel;
    protected $dashboardModel;
    protected $privilegeModel;
    protected $emailService;
    protected \App\Models\UserFolderAccessModel $folderAccessModel;
    protected MpinModel $mpinModel;
    protected GroupPrivilegeModel $groupPrivilegeModel;

    public function __construct()
    {
        $this->userModel           = new UserModel();
        $this->activityLogModel    = new ActivityLogModel();
        $this->dashboardModel      = new DashboardModel();
        $this->privilegeModel      = new UserPrivilegeModel();
        $this->emailService        = new EmailService();
        $this->folderAccessModel   = new UserFolderAccessModel();
        $this->mpinModel           = new MpinModel();
        $this->groupPrivilegeModel = new GroupPrivilegeModel();
    }


    /**
     * Check if user is super admin
     */
    protected function checkSuperAdmin()
{
    if (!session()->get('logged_in')) {
        return redirect()->to('/login')->with('error', 'Unauthorized access.');
    }
    return null;
}

    /**
     * Check if the logged-in user has a specific privilege.
     * Admins with access_level='full' always pass (they are the true super-admins).
     * Admins with access_level='limited' must have the privilege explicitly set to true.
     */
    protected function checkPrivilege(string $privilegeKey)
{
    // Full admins always pass — they have unrestricted access
    if (session()->get('role') === 'admin' 
        && session()->get('access_level') === 'full') {
        return null;
    }

    $userId = (int) session()->get('user_id');
    $privs  = $this->privilegeModel->getUserPrivileges($userId);

    if (!($privs[$privilegeKey] ?? false)) {
        // Redirect users to their own dashboard, admins to super-admin dashboard
        $redirectTo = session()->get('role') === 'admin'
            ? '/super-admin/dashboard'
            : '/dashboard';

        return redirect()->to($redirectTo)
            ->with('error', 'You do not have permission to access that page.');
    }
    return null;
}
    /**
     * Dashboard
     */
    public function index()
    {
        $redirect = $this->checkSuperAdmin();
        if ($redirect) return $redirect;

        // Load dashboard statistics dynamically
        $stats          = $this->dashboardModel->getSuperAdminStats();
        $recentActivity = $this->dashboardModel->getRecentActivity(5);

        // Compute KPI counts (admin always gets full access)
        $userId   = (int) session()->get('user_id');
        $basePath = WRITEPATH . '../public/uploads/academic_records';
        $kpi      = $this->countAccessibleItems($basePath, $userId);

        $data = [
            'title'               => 'Admin Dashboard - CredentiaTAU',
            'email'               => session()->get('email'),
            'role'                => session()->get('role'),
            'full_name'           => session()->get('full_name') ?? 'Admin',
            'stats'               => $stats,
            'recent_activity'     => $recentActivity,
            'total_files'         => $kpi['files'],
            'total_folders'       => $kpi['folders'],
            'file_types'          => $kpi['file_types'],
            'folder_distribution' => $kpi['folder_distribution'],
            'monthly_data'        => $kpi['monthly_data'],
        ];

        return view('super_admin/dashboard', $data);
    }

    /**
     * User Management
     */
    public function userManagement()
    {
        if (!session()->get('logged_in')) {
        return redirect()->to('/login');
    }

        $redirect = $this->checkPrivilege('user_management');
        if ($redirect) return $redirect;

        // Get all users with record counts
        $users = $this->userModel->getAllUsersWithRecordCounts();
        
        // Get only users who explicitly submitted an access request
        $approvalModel = new \App\Models\ApprovalRequestModel();
        $pendingRequests = $approvalModel->getPendingRequests();

        // Build a privileges map keyed by user_id for use in the view
        $userPrivilegesMap = [];
        foreach ($users as $user) {
            $userPrivilegesMap[$user['user_id']] = $this->privilegeModel->getUserPrivileges($user['user_id']);
        }

         // Build per-user edit permission map for the view.
        $actingAdminId     = (int) session()->get('user_id');
        $actingAccessLevel = session()->get('access_level') ?? 'limited';
        $canEditMap    = [];
        $lockOwnerMap  = [];

foreach ($users as $u) {
    $uid = (int) $u['user_id'];

    $canEdit = $this->privilegeModel->canEditPrivileges(
        $actingAdminId, $uid, $actingAccessLevel
    );
    $canEditMap[$uid] = $canEdit;
$canEditMap          = [];
$lockOwnerMap        = [];
$canToggleStatusMap  = [];
$canDeleteMap        = [];

foreach ($users as $u) {
    $uid            = (int) $u['user_id'];
    $targetIsAdmin  = ($u['role'] === 'admin');
    $isSelf         = ($uid === $actingAdminId);

    // Full admins can do everything except delete/deactivate themselves
    if ($actingAccessLevel === 'full') {
        $canEditMap[$uid]         = !$isSelf;
        $canToggleStatusMap[$uid] = !$isSelf;
        $canDeleteMap[$uid]       = !$isSelf;
        continue;
    }

    // Limited actors: resolve the lock owner once
    $lockOwnerId = $this->privilegeModel->getPrivilegeLockOwner($uid);
    $isLockOwner = ($lockOwnerId === $actingAdminId);
    $hasNoLock   = ($lockOwnerId === null);

    // Resolve display name for the tooltip
    if ($targetIsAdmin) {
        $lockOwnerMap[$uid] = 'system (admin accounts are protected)';
    } elseif ($lockOwnerId && !$isLockOwner) {
        $owner = $this->userModel->find($lockOwnerId);
        $lockOwnerMap[$uid] = $owner['full_name'] ?? 'another administrator';
    }

    // Edit privileges: same rule as before
    $canEdit = !$isSelf && !$targetIsAdmin && ($hasNoLock || $isLockOwner);
    $canEditMap[$uid] = $canEdit;

    // Deactivate/Reactivate: same rules as edit
    $canToggleStatusMap[$uid] = $canEdit;

    // Delete: same rules as edit (must own the config to delete)
    $canDeleteMap[$uid] = $canEdit;
}
    // Resolve the lock-owner name for the tooltip shown in the view
    if (!$canEdit) {
        $lockOwnerId = $this->privilegeModel->getPrivilegeLockOwner($uid);
        if ($lockOwnerId) {
            $owner = $this->userModel->find($lockOwnerId);
            $lockOwnerMap[$uid] = $owner['full_name'] ?? 'another administrator';
        } else {
            // Target is an admin account — blanket rule applies
            $lockOwnerMap[$uid] = 'system (admin accounts are protected)';
        }
    }
}

        $data = [
            'title'                  => 'User Management - CredentiaTAU',
            'email'                  => session()->get('email'),
            'role'                   => session()->get('role'),
            'full_name'              => session()->get('full_name') ?? 'Admin',
            'users'                  => $users,
            'pending_admins'         => $pendingRequests,
            'user_privileges_map'    => $userPrivilegesMap,
            'privilege_definitions'  => $this->privilegeModel->getPrivilegeDefinitions(),
            'group_privilege_matrix' => $this->groupPrivilegeModel->getFullMatrix(),
            'can_edit_map'           => $canEditMap,
            'lock_owner_map'         => $lockOwnerMap,
            'can_toggle_status_map'  => $canToggleStatusMap,  // ADD
            'can_delete_map'         => $canDeleteMap,         // ADD
        ];


        return view('super_admin/user_management', $data);
    }

    /**
     * All Records
     */
    public function allRecords()
    {
        $redirect = $this->checkSuperAdmin();
        if ($redirect) return $redirect;

        $data = [
            'title' => 'All Records - CredentiaTAU',
            'email' => session()->get('email'),
            'role'  => session()->get('role'),
            'full_name' => session()->get('full_name') ?? 'Admin'
        ];

        return view('auth/academic_records', $data);
    }

    /**
     * System Backup
     */
    public function systemBackup()
    {
        $redirect = $this->checkSuperAdmin();
        if ($redirect) return $redirect;

        $redirect = $this->checkPrivilege('system_backup');
        if ($redirect) return $redirect;

        $data = [
            'title' => 'System Backup - CredentiaTAU',
            'email' => session()->get('email'),
            'role'  => session()->get('role'),
            'full_name' => session()->get('full_name') ?? 'Admin'
        ];

        return view('super_admin/system_backup', $data);
    }

    /**
     * Settings
     */
   public function settings()
    {
        $redirect = $this->checkSuperAdmin();
        if ($redirect) return $redirect;

        // Only block limited admins who lack profile_edit.
        // Full admins always pass through.
        if (session()->get('access_level') !== 'full') {
            $redirect = $this->checkPrivilege('profile_edit');
            if ($redirect) return $redirect;
        }

        // Delegate to the Settings controller so the full settings page
        // (profile edit, password change, MPIN, activity logs) is shown.
        return (new \App\Controllers\Settings())->index();
    }


    /**
     * Add Admin
     */
    public function addAdmin()
    {
        $redirect = $this->checkSuperAdmin();
        if ($redirect) return $redirect;

        // Validate input
        $validation = \Config\Services::validation();
        $validation->setRules([
            'full_name'        => 'required|min_length[3]|max_length[255]',
            'email'            => 'required|valid_email|is_unique[users.email]',
            'username'         => 'required|min_length[3]|max_length[100]|is_unique[users.username]|alpha_numeric_punct',
            'role'             => 'required|in_list[admin,user]',
            'access_level'     => 'required|in_list[full,limited]',
            'initial_password' => 'required|min_length[8]',
            'mpin'             => 'required|exact_length[4]|numeric',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->with('error', implode('<br>', $validation->getErrors()));
        }

        $initialPassword = $this->request->getPost('initial_password');
        $mpinValue       = $this->request->getPost('mpin');

        $userData = [
            'full_name'                => $this->request->getPost('full_name'),
            'email'                    => $this->request->getPost('email'),
            'username'                 => $this->request->getPost('username'),
            'password'                 => $initialPassword,
            'role'                     => $this->request->getPost('role'),
            'access_level'             => $this->request->getPost('access_level'),
            'status'                   => 'active',
            'initial_password_changed' => false,
            'created_by'               => session()->get('user_id'),
        ];

        if ($this->userModel->insert($userData)) {
            $newUserId = $this->userModel->getInsertID();

            // Save privileges
            $submittedPrivs = $this->request->getPost('privileges') ?? null;
            if (is_array($submittedPrivs) && count($submittedPrivs) > 0) {
                $allKeys = [
                    'records_upload', 'files_view', 'records_organize',
                    'folders_add', 'records_delete', 'folders_delete',
                    'profile_edit', 'user_management', 'system_backup',
                    'audit_logs', 'record_types', 'full_admin',
                ];
                $privileges = [];
                foreach ($allKeys as $key) {
                    $privileges[$key] = in_array($key, $submittedPrivs, true);
                }
                $this->privilegeModel->setPrivileges($newUserId, $privileges);
            } else {
                $this->privilegeModel->initializeUserPrivileges($newUserId, $userData['role']);
            }

            // Save MPIN (required)
            $this->mpinModel->setMpin($newUserId, $mpinValue, (int) session()->get('user_id'));

            // Audit log
            $roleDisplay = $this->userModel->getRoleDisplayName($userData['role']);
            $this->activityLogModel->logActivity(
                session()->get('user_id'),
                'user_created',
                "Created new {$roleDisplay}: {$userData['email']} (username: {$userData['username']})"
            );

            // Send welcome email WITH MPIN
            $this->emailService->sendWelcomeEmailWithMpin(
                $userData['email'],
                $userData['full_name'],
                $initialPassword,
                $mpinValue,
                session()->get('full_name')
            );

            return redirect()->to('/super-admin/user-management')
                ->with('success', 'User account created successfully! Welcome email with MPIN sent.');
        } else {
            return redirect()->back()->with('error', 'Failed to create user account.');
        }
    }


    /**
     * Edit Admin
     */
    public function editAdmin($id)
    {
        $redirect = $this->checkSuperAdmin();
        if ($redirect) return $redirect;

        // Get existing user
$existingUser = $this->userModel->find($id);
if (!$existingUser) {
    return redirect()->back()->with('error', 'User not found.');
}

// Prevent any user from editing their own account through this route.
if ((int) $id === (int) session()->get('user_id')) {
    return redirect()->back()->with('error', 'You cannot edit your own account here. Use the Settings page.');
}

// Lock guard: full admins always pass; limited actors use canEditPrivileges()
if (session()->get('access_level') !== 'full') {
    $actingAdminId = (int) session()->get('user_id');
    $canEdit = $this->privilegeModel->canEditPrivileges(
        $actingAdminId, (int) $id, 'limited'
    );
    if (!$canEdit) {
        $db     = \Config\Database::connect();
        $target = $db->table('users')->select('role')->where('user_id', $id)->get()->getRow();
        if ($target && $target->role === 'admin') {
            $reason = 'Admin accounts can only be modified by a full administrator.';
        } else {
            $lockOwnerId = $this->privilegeModel->getPrivilegeLockOwner((int) $id);
            $owner       = $lockOwnerId ? $this->userModel->find($lockOwnerId) : null;
            $reason      = 'This user was already configured by ' . ($owner['full_name'] ?? 'another administrator') . ' and cannot be modified.';
        }
        return redirect()->back()->with('error', $reason);
    }
}



        // Validate input
        $validation = \Config\Services::validation();
        $validation->setRules([
            'full_name' => 'required|min_length[3]|max_length[255]',
            'email' => "required|valid_email|is_unique[users.email,user_id,{$id}]",
            'username' => "required|min_length[3]|max_length[100]|is_unique[users.username,user_id,{$id}]|alpha_numeric_punct",
            'role' => 'required|in_list[admin,user]',
            'access_level' => 'required|in_list[full,limited]',
            'status' => 'required|in_list[active,inactive]',
            'new_password' => 'permit_empty|min_length[8]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->with('error', implode('<br>', $validation->getErrors()));
        }

        // Prepare update data
        $updateData = [
            'full_name' => $this->request->getPost('full_name'),
            'email' => $this->request->getPost('email'),
            'username' => $this->request->getPost('username'),
            'role' => $this->request->getPost('role'),
            'access_level' => $this->request->getPost('access_level'),
            'status' => $this->request->getPost('status')
        ];

        // Add password if provided
        $newPassword = $this->request->getPost('new_password');
        if (!empty($newPassword)) {
            $updateData['password'] = $newPassword; // Will be hashed by model
        }

        // NOTE: Privileges are saved separately via AJAX (update-user-privileges).
        // Do NOT reset them here — doing so would overwrite the admin's selections.
        // If you need to seed privileges for a brand-new role assignment, do it
        // only when no privileges exist yet for this user (handled in approveAdmin).


        if ($this->userModel->update($id, $updateData)) {
            // Log activity
            $changes = [];
            if ($existingUser['username'] !== $updateData['username']) {
                $changes[] = "username: {$existingUser['username']} → {$updateData['username']}";
            }
            if ($existingUser['email'] !== $updateData['email']) {
                $changes[] = "email: {$existingUser['email']} → {$updateData['email']}";
            }
            if ($existingUser['role'] !== $updateData['role']) {
                $oldRole = $this->userModel->getRoleDisplayName($existingUser['role']);
                $newRole = $this->userModel->getRoleDisplayName($updateData['role']);
                $changes[] = "role: {$oldRole} → {$newRole}";
            }
            if (!empty($newPassword)) {
                $changes[] = "password reset";
            }
            
            $changeLog = empty($changes) ? 'Updated user details' : 'Updated: ' . implode(', ', $changes);
            
            $this->activityLogModel->logActivity(
                session()->get('user_id'),
                'user_updated',
                "Updated user: {$updateData['email']} - {$changeLog}"
            );

            // Save folder access assignments submitted from the edit modal
            $folderJson = $this->request->getPost('folder_access');
            if ($folderJson !== null) {
                $folders = json_decode($folderJson, true) ?? [];
                $this->folderAccessModel->setFolders($id, $folders, (int) session()->get('user_id'));
            }

            return redirect()->to('/super-admin/user-management')
                ->with('success', 'User account updated successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to update user account.');
        }
    }

    /**
 * Approve pending user
 */
public function approveAdmin($id)
{
    if ($redirect = $this->checkSuperAdmin()) return $redirect;

    $user = $this->userModel->find($id);
    if (!$user) {
        return redirect()->back()->with('error', 'User not found.');
    }

    // Activate the user
    $this->userModel->update($id, ['status' => 'active']);

    // Mark their approval_request as approved (if one exists)
    $approvalModel = new \App\Models\ApprovalRequestModel();
    $request = $approvalModel->where('user_id', $id)->where('status', 'pending')->first();
    if ($request) {
        $approvalModel->update($request['approval_id'], [
            'status'      => 'approved',
            'reviewed_at' => date('Y-m-d H:i:s'),
            'reviewed_by' => session()->get('user_id'),
        ]);
    }

    // Initialize default privileges if they don't have any yet
    $existing = $this->privilegeModel->getUserPrivileges($id);
    if (empty($existing)) {
        $this->privilegeModel->initializeUserPrivileges($id, $user['role']);
    }

    $this->activityLogModel->logActivity(
        session()->get('user_id'),
        'user_approved',
        "Approved user: {$user['email']} (username: {$user['username']})"
    );

    return redirect()->back()->with('success', "User {$user['full_name']} has been approved and activated.");
}

/**
 * Reject pending user (delete account + mark request as rejected)
 */
public function rejectAdmin($id)
{
    if ($redirect = $this->checkSuperAdmin()) return $redirect;

    $user = $this->userModel->find($id);
    if (!$user) {
        return redirect()->back()->with('error', 'User not found.');
    }

    // Mark their approval_request as rejected first (before deleting user, due to FK)
    $approvalModel = new \App\Models\ApprovalRequestModel();
    $request = $approvalModel->where('user_id', $id)->where('status', 'pending')->first();
    if ($request) {
        $approvalModel->update($request['id'], [
            'status'      => 'rejected',
            'reviewed_at' => date('Y-m-d H:i:s'),
            'reviewed_by' => session()->get('user_id'),
        ]);
    }

    $email = $user['email'];
    $name  = $user['full_name'];

    $this->userModel->delete($id);

    $this->activityLogModel->logActivity(
        session()->get('user_id'),
        'user_rejected',
        "Rejected and removed user: {$email}"
    );

    return redirect()->back()->with('success', "User {$name} has been rejected and removed.");
}

/**
 * Deactivate or Reactivate a user
 * Route: super-admin/toggle-suspend/(:num)
 */
public function toggleSuspend($id)
{
    if ($redirect = $this->checkSuperAdmin()) return $redirect;

    $user = $this->userModel->find($id);
    if (!$user) {
        return redirect()->back()->with('error', 'User not found.');
    }

    if ((int)$id === (int)session()->get('user_id')) {
        return redirect()->back()->with('error', 'You cannot change your own status.');
    }

    // Lock guard — same rule as edit
    $actingAccessLevel = session()->get('access_level') ?? 'limited';
    if ($actingAccessLevel !== 'full') {
        $actingAdminId = (int) session()->get('user_id');
        $canAct = $this->privilegeModel->canEditPrivileges(
            $actingAdminId, (int) $id, 'limited'
        );
        if (!$canAct) {
            $targetIsAdmin = ($user['role'] === 'admin');
            if ($targetIsAdmin) {
                $reason = 'Admin accounts can only be deactivated by a full administrator.';
            } else {
                $lockOwnerId = $this->privilegeModel->getPrivilegeLockOwner((int) $id);
                $owner       = $lockOwnerId ? $this->userModel->find($lockOwnerId) : null;
                $reason      = 'This user was configured by ' . ($owner['full_name'] ?? 'another administrator') . ' and cannot be modified.';
            }
            return redirect()->back()->with('error', $reason);
        }
    }

    $newStatus = $user['status'] === 'inactive' ? 'active' : 'inactive';
    $this->userModel->update($id, ['status' => $newStatus]);

    $approvalModel = new \App\Models\ApprovalRequestModel();

    if ($newStatus === 'active') {
        // Resolve any pending reactivation request
        $pending = $approvalModel->where('user_id', $id)->where('status', 'pending')->first();
        if ($pending) {
            $approvalModel->update($pending['id'], [
                'status'      => 'approved',
                'reviewed_at' => date('Y-m-d H:i:s'),
                'reviewed_by' => session()->get('user_id'),
            ]);
        }

        // Initialize privileges if user has none yet
        $existing = $this->privilegeModel->getUserPrivileges($id);
        if (empty($existing)) {
            $this->privilegeModel->initializeUserPrivileges($id, $user['role']);
        }

        $this->activityLogModel->logActivity(
            session()->get('user_id'),
            'user_reactivated',
            "Reactivated user: {$user['email']} (username: {$user['username']})"
        );

        return redirect()->back()->with('success', "{$user['full_name']} has been reactivated successfully.");

    } else {
        // Deactivated — AuthFilter will kick them out on their next request
        $this->activityLogModel->logActivity(
            session()->get('user_id'),
            'user_deactivated',
            "Deactivated user: {$user['email']} (username: {$user['username']})"
        );

        return redirect()->back()->with('success', "{$user['full_name']} has been deactivated.");
    }
}
    /**
     * Delete Admin
     */
    public function deleteAdmin($id)
{
    if ($redirect = $this->checkSuperAdmin()) return $redirect;

    $user = $this->userModel->find($id);
    if (!$user) {
        return redirect()->back()->with('error', 'User not found.');
    }

    // Prevent deleting self
    if ((int)$id === (int)session()->get('user_id')) {
        return redirect()->back()->with('error', 'You cannot delete your own account.');
    }

    // Lock guard — same rule as edit
    $actingAccessLevel = session()->get('access_level') ?? 'limited';
    if ($actingAccessLevel !== 'full') {
        $actingAdminId = (int) session()->get('user_id');
        $canAct = $this->privilegeModel->canEditPrivileges(
            $actingAdminId, (int) $id, 'limited'
        );
        if (!$canAct) {
            $targetIsAdmin = ($user['role'] === 'admin');
            if ($targetIsAdmin) {
                $reason = 'Admin accounts can only be deleted by a full administrator.';
            } else {
                $lockOwnerId = $this->privilegeModel->getPrivilegeLockOwner((int) $id);
                $owner       = $lockOwnerId ? $this->userModel->find($lockOwnerId) : null;
                $reason      = 'This user was configured by ' . ($owner['full_name'] ?? 'another administrator') . ' and cannot be deleted.';
            }
            return redirect()->back()->with('error', $reason);
        }
    }

    // Prevent deleting the only super admin
    if ($user['role'] === 'admin') {
            $count = $this->userModel->where('role', 'admin')->countAllResults();
            if ($count <= 1) {
                return redirect()->back()->with('error', 'Cannot delete the only Admin account.');
            }
        }

        if ($this->userModel->delete($id)) {
            $roleDisplay = $this->userModel->getRoleDisplayName($user['role']);
            $this->activityLogModel->logActivity(
                session()->get('user_id'),
                'user_deleted',
                "Deleted user: {$user['email']} (username: {$user['username']}, role: {$roleDisplay})"
            );

            return redirect()->to('/super-admin/user-management')
                ->with('success', 'User account deleted successfully!');
        }

        return redirect()->back()->with('error', 'Failed to delete user account.');
    }

    /**
     * Toggle Inactive Status (Activate/Deactivate)
     */
    public function toggleStatus($id)
    {
        if ($redirect = $this->checkSuperAdmin()) return $redirect;

        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        // Prevent changing own status
        if ($id == session()->get('user_id')) {
            return redirect()->back()->with('error', 'You cannot change your own status.');
        }

        $newStatus = $user['status'] === 'inactive' ? 'active' : 'inactive';

        if ($this->userModel->update($id, ['status' => $newStatus])) {
            $action = $newStatus === 'inactive' ? 'deactivated' : 'activated';

            $this->activityLogModel->logActivity(
                session()->get('user_id'),
                "user_{$action}",
                "User {$action}: {$user['email']} (username: {$user['username']})"
            );

            return redirect()->back()->with('success', "User {$action} successfully!");
        }

        return redirect()->back()->with('error', 'Failed to update user status.');
    }

    /**
     * Get User Data (AJAX)
     */
    public function getUserData($id)
    {
        if ($redirect = $this->checkSuperAdmin()) return $redirect;

        $user = $this->userModel->find($id);

        if ($user) {
            unset($user['password']);
            return $this->response->setJSON([
                'success' => true,
                'user' => $user
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'User not found'
        ]);
    }

/**
     * Get folder list + user's current assignments (AJAX GET)
     * Called by the Edit modal to populate the folder checkboxes.
     */
    public function getUserFolders(int $userId)
    {
        if ($redirect = $this->checkSuperAdmin()) return $redirect;

        // Scan root of the uploads directory for top-level folders
        $uploadRoot  = FCPATH . 'uploads/academic_records/';
        $rootFolders = [];
        if (is_dir($uploadRoot)) {
            foreach (scandir($uploadRoot) as $entry) {
                if ($entry === '.' || $entry === '..') continue;
                if (is_dir($uploadRoot . $entry)) $rootFolders[] = $entry;
            }
            sort($rootFolders);
        }

        $assigned = $this->folderAccessModel->getAllowedFolders($userId);

        return $this->response->setJSON([
            'success'  => true,
            'folders'  => $rootFolders,   // all folders on disk
            'assigned' => $assigned,       // which ones this user has
        ]);
    }

    /**
     * Save folder assignments for a user (AJAX POST)
     * Called by the Edit modal JS before submitting the main form.
     */
    public function updateUserFolders(int $userId)
    {
        if ($redirect = $this->checkSuperAdmin()) return $redirect;
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false]);
        }

        $user = $this->userModel->find($userId);
        if (!$user) {
            return $this->response->setJSON(['success' => false, 'message' => 'User not found.']);
        }

        $body    = $this->request->getJSON(true);
        $folders = $body['folders'] ?? [];

        $this->folderAccessModel->setFolders(
            $userId,
            $folders,
            (int) session()->get('user_id')
        );

        $this->activityLogModel->logActivity(
            session()->get('user_id'),
            'folder_access_updated',
            "Updated folder access for user: {$user['email']} — folders: " . implode(', ', $folders)
        );

        return $this->response->setJSON(['success' => true, 'message' => 'Folder access saved.']);
    }

    /**
     * Get Group Privileges matrix (AJAX) — for the Group Privileges modal
     * Route: GET super-admin/group-privileges
     */
    public function getGroupPrivileges()
    {
        if ($redirect = $this->checkSuperAdmin()) return $redirect;

        $matrix      = $this->groupPrivilegeModel->getFullMatrix();
        $definitions = $this->privilegeModel->getPrivilegeDefinitions();

        return $this->response->setJSON([
            'success'     => true,
            'matrix'      => $matrix,
            'definitions' => $definitions,
        ]);
    }

    /**
     * Set or reset a user's MPIN (admin action from User Management)
     * Route: POST super-admin/set-mpin/:id
     */
    public function setUserMpin(int $userId)
    {
        if ($redirect = $this->checkSuperAdmin()) return $redirect;
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false]);
        }

        $user = $this->userModel->find($userId);
        if (!$user) {
            return $this->response->setJSON(['success' => false, 'message' => 'User not found.']);
        }

        $body = $this->request->getJSON(true);
        $mpin = (string) ($body['mpin'] ?? '');

        if (strlen($mpin) !== 4 || !ctype_digit($mpin)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'MPIN must be exactly 4 digits.',
            ]);
        }

        $ok = $this->mpinModel->setMpin($userId, $mpin, (int) session()->get('user_id'));

        if ($ok) {
            $this->activityLogModel->logActivity(
                session()->get('user_id'),
                'mpin_set',
                'Admin set/reset PIN for user: ' . $user['email']
            );
        }

        return $this->response->setJSON([
            'success' => $ok,
            'message' => $ok ? 'MPIN saved successfully.' : 'Failed to save MPIN.',
        ]);
    }


    /**
     * Get User Privileges (AJAX)
     */
  
    /**
     * Update User Privileges (AJAX)
     */
   
/**
 * =============================================================================
 * INSTRUCTIONS: Add this method to your SuperAdmin controller
 * =============================================================================
 * 
 * File Location: app/Controllers/SuperAdmin.php (or similar)
 * 
 * This method handles the AJAX request to fetch user privileges for the modal.
 * Add this method to your existing SuperAdmin controller class.
 */


/**
 * =============================================================================
 * INSTRUCTIONS: Add this method to your SuperAdmin controller
 * =============================================================================
 * 
 * File Location: app/Controllers/SuperAdmin.php (or similar)
 * 
 * This method handles the AJAX request to fetch user privileges for the modal.
 * Add this method to your existing SuperAdmin controller class.
 */

public function getUserPrivileges($userId)
{
    // Return JSON response
    return $this->response->setJSON($this->_getUserPrivilegesData($userId));
}

/**
 * Helper method to get user privileges data
 * Private method to keep the logic separate and testable
 */
private function _getUserPrivilegesData($userId)
{
    try {
        $db = \Config\Database::connect();
        
        // Get user info
        $user = $db->table('users')
            ->where('user_id', $userId)
            ->get()
            ->getRow();
        
        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found'
            ];
        }
        
        // Get user privileges from database
        $privilegesQuery = $db->table('user_privileges')
            ->where('user_id', $userId)
            ->where('privilege_value', 1)
            ->get();
        
        $userPrivileges = [];
        foreach ($privilegesQuery->getResult() as $priv) {
            $userPrivileges[$priv->privilege_key] = true;
        }
        
        // Define all possible privilege definitions with categories
        // This defines what each privilege means and how it's displayed
        $definitions = [
            'records_upload' => [
                'label'       => 'Upload Records',
                'description' => 'Upload digitized academic records',
                'category'    => 'Records Management',
            ],
            'files_view' => [
                'label'       => 'View Files',
                'description' => 'View, download, and print archived records',
                'category'    => 'Records Management',
            ],
            'records_organize' => [
                'label'       => 'Organize Records',
                'description' => 'Move files/folders, rename files, and manage file structure',
                'category'    => 'Records Management',
            ],
            'folders_add' => [
                'label'       => 'Add Folders',
                'description' => 'Create new folders or categories',
                'category'    => 'Records Management',
            ],
            'records_delete' => [
                'label'       => 'Delete Records',
                'description' => 'Delete archived files',
                'category'    => 'Records Management',
            ],
            'folders_delete' => [
                'label'       => 'Delete Folders',
                'description' => 'Delete folders or categories',
                'category'    => 'Records Management',
            ],
            'profile_edit' => [
                'label'       => 'Edit Profile',
                'description' => 'Edit user profile and personal settings',
                'category'    => 'Profile Management',
            ],
            'user_management' => [
                'label'       => 'Manage Users',
                'description' => 'Add, edit, and assign roles to users',
                'category'    => 'Administration',
            ],
            'system_backup' => [
                'label'       => 'System Backup',
                'description' => 'Access system backup and restore features',
                'category'    => 'Administration',
            ],
            'audit_logs' => [
                'label'       => 'Audit Logs',
                'description' => 'View system activity logs',
                'category'    => 'Administration',
            ],
            'record_types' => [
                'label'       => 'Manage Record Types',
                'description' => 'Add, edit, and delete OCR document types and keywords',
                'category'    => 'Administration',
            ],
            'full_admin' => [
                'label'       => 'Full Admin Access',
                'description' => 'Automatically grants all privileges when selected',
                'category'    => 'Administration',
            ],
        ];

        
        // Build privileges object showing which privileges the user has
        $privileges = [];
        foreach ($definitions as $key => $def) {
            $privileges[$key] = isset($userPrivileges[$key]);
        }
        
       $actingAdminId     = (int) session()->get('user_id');
        $actingAccessLevel = session()->get('access_level') ?? 'limited';
        $canEdit = $this->privilegeModel->canEditPrivileges(
            $actingAdminId, (int) $user->user_id, $actingAccessLevel
        );

        return [
            'success'     => true,
            'privileges'  => $privileges,
            'definitions' => $definitions,
            'can_edit'    => $canEdit,
            'user'        => [
                'user_id'      => $user->user_id,
                'full_name'    => $user->full_name,
                'email'        => $user->email,
                'role'         => $user->role,
                'access_level' => $user->access_level,
            ],
        ];

        
    } catch (\Exception $e) {
        log_message('error', 'Error fetching user privileges: ' . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ];
    }
}

/**
 * =============================================================================
 * ROUTE CONFIGURATION
 * =============================================================================
 * 
 * Add this route to app/Config/Routes.php inside your super-admin group:
 * 
 * $routes->get('super-admin/get-user-privileges/(:num)', 'SuperAdmin::getUserPrivileges/$1');
 * 
 * Or if you already have a routes group for super-admin:
 * 
 * $routes->group('super-admin', function($routes) {
 *     $routes->get('get-user-privileges/(:num)', 'SuperAdmin::getUserPrivileges/$1');
 *     // ... other routes
 * });
 */

    /**
     * Update User Privileges (AJAX POST)
     * Route: POST super-admin/update-user-privileges/(:num)
     */
    public function updateUserPrivileges($userId)
    {
        if ($redirect = $this->checkSuperAdmin()) return $redirect;

        // Must be an AJAX request
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)
                                  ->setJSON(['success' => false, 'message' => 'Forbidden']);
        }

        $user = $this->userModel->find($userId);
if (!$user) {
    return $this->response->setJSON(['success' => false, 'message' => 'User not found.']);
}

// Prevent self-privilege modification
if ((int) $userId === (int) session()->get('user_id')) {
    return $this->response->setJSON(['success' => false, 'message' => 'You cannot modify your own privileges.']);
}

// ── Privilege lock guard ────────────────────────────────────
$actingAdminId     = (int) session()->get('user_id');
$actingAccessLevel = session()->get('access_level') ?? 'limited';

if (!$this->privilegeModel->canEditPrivileges($actingAdminId, (int) $userId, $actingAccessLevel)) {
    // Determine a user-friendly reason
    $db     = \Config\Database::connect();
    $target = $db->table('users')->select('role')->where('user_id', $userId)->get()->getRow();
    if ($target && $target->role === 'admin') {
        $reason = 'Admin accounts can only be configured by a full administrator.';
    } else {
        $lockOwnerId = $this->privilegeModel->getPrivilegeLockOwner((int) $userId);
        $owner       = $lockOwnerId ? $this->userModel->find($lockOwnerId) : null;
        $ownerName   = $owner['full_name'] ?? 'another administrator';
        $reason      = 'This user was already configured by ' . $ownerName . ' and cannot be modified.';
    }

    return $this->response->setJSON([
        'success' => false,
        'message' => $reason,
    ]);
}
        // ────────────────────────────────────────────────────────────

        // All known privilege keys
        $allKeys = [
            'records_upload', 'files_view', 'records_organize',
            'folders_add', 'records_delete', 'folders_delete',
            'profile_edit', 'user_management', 'system_backup',
            'audit_logs', 'record_types', 'full_admin'
        ];

        // Parse submitted JSON body
        $body      = $this->request->getJSON(true);
        $submitted = $body['privileges'] ?? [];

        // Build a clean true/false map for every key
        $privileges = [];
        foreach ($allKeys as $key) {
            $privileges[$key] = isset($submitted[$key]) && $submitted[$key] === true;
        }

        if ($this->privilegeModel->setPrivileges($userId, $privileges, $actingAdminId)) {

            $this->activityLogModel->logActivity(
                session()->get('user_id'),
                'privileges_updated',
                "Updated privileges for user: {$user['email']} (username: {$user['username']})"
            );
            return $this->response->setJSON(['success' => true, 'message' => 'Privileges updated successfully.']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to update privileges.']);
    }

    /**
     * Activity Logs
     */
    public function activityLogs()
    {
        $redirect = $this->checkSuperAdmin();
        if ($redirect) return $redirect;

        $redirect = $this->checkPrivilege('audit_logs');
        if ($redirect) return $redirect;

        // Get filter parameters
        $action = $this->request->getGet('action');
        $timeRange = $this->request->getGet('time_range') ?? '7';
        $userId = $this->request->getGet('user_id');

        // Build query
        $builder = $this->activityLogModel
            ->select('activity_logs.*, users.full_name, users.email')
            ->join('users', 'activity_logs.user_id = users.user_id', 'left');

        // Apply filters
        if ($action) {
            // file_ prefix: match any action starting with "file_"
            if ($action === 'file_') {
                $builder->like('activity_logs.action', 'file_', 'after');
            } else {
                $builder->like('activity_logs.action', $action);
            }
        }

        if ($timeRange !== 'all') {
            $days = intval($timeRange);
            $builder->where('activity_logs.created_at >=', date('Y-m-d H:i:s', strtotime("-{$days} days")));
        }

        if ($userId) {
            $builder->where('activity_logs.user_id', $userId);
        }

        $logs = $builder->orderBy('activity_logs.created_at', 'DESC')
                       ->limit(100)
                       ->findAll();

        // Get all users for filter dropdown
        $users = $this->userModel->select('user_id, full_name, email')
                                 ->orderBy('full_name', 'ASC')
                                 ->findAll();

        $data = [
            'title' => 'Activity Logs - CredentiaTAU',
            'email' => session()->get('email'),
            'role' => session()->get('role'),
            'full_name' => session()->get('full_name'),
            'logs' => $logs,
            'users' => $users,
            'current_action' => $action,
            'current_time_range' => $timeRange,
            'current_user_id' => $userId
        ];

        return view('super_admin/activity_logs', $data);
    }
    // -------------------------------------------------------------------------
    // KPI helpers (mirrored from Dashboard controller)
    // -------------------------------------------------------------------------

    /**
     * Scan $basePath and return counts. Super-admins always get full access.
     */
    private function countAccessibleItems(string $basePath, int $userId): array
    {
        $fileCount          = 0;
        $folderCount        = 0;
        $fileTypes          = [];
        $folderDistribution = [];
        $monthlyData        = array_fill(0, 12, 0);

        if (!is_dir($basePath)) {
            return [
                'files'               => 0,
                'folders'             => 0,
                'file_types'          => [],
                'folder_distribution' => [],
                'monthly_data'        => $monthlyData,
            ];
        }

        // Super-admins always bypass folder restrictions
        $hasFullAccess  = true;
        $allowedFolders = [];

        try {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($basePath, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($iterator as $item) {
                $relativePath = ltrim(
                    str_replace('\\', '/', str_replace($basePath, '', $item->getPathname())),
                    '/'
                );

                if ($item->isDir()) {
                    $folderCount++;
                    $topFolder = explode('/', $relativePath)[0];
                    $folderDistribution[$topFolder] = ($folderDistribution[$topFolder] ?? 0) + 1;
                } else {
                    $fileCount++;
                    $ext = strtolower(pathinfo($item->getFilename(), PATHINFO_EXTENSION));
                    $fileTypes[$ext] = ($fileTypes[$ext] ?? 0) + 1;

                    $mtime = filemtime($item->getPathname());
                    if ((int) date('Y', $mtime) === (int) date('Y')) {
                        $monthlyData[(int) date('n', $mtime) - 1]++;
                    }
                }
            }
        } catch (\Exception $e) {
            log_message('error', 'SuperAdmin KPI scan error: ' . $e->getMessage());
        }

        return [
            'files'               => $fileCount,
            'folders'             => $folderCount,
            'file_types'          => $fileTypes,
            'folder_distribution' => $folderDistribution,
            'monthly_data'        => $monthlyData,
        ];
    }

/**
     * Get all users of a given role with their privileges (AJAX)
     * Route: GET super-admin/users-by-role/{role}
     */
    public function getUsersByRole(string $role)
    {
        if ($redirect = $this->checkSuperAdmin()) return $redirect;

        if (!in_array($role, ['admin', 'user'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid role.']);
        }

        $db = \Config\Database::connect();

        $users = $db->table('users')
            ->select('user_id, full_name, email, username, role, status, access_level')
            ->where('role', $role)
            ->orderBy('full_name', 'ASC')
            ->get()
            ->getResultArray();

        if (empty($users)) {
            return $this->response->setJSON(['success' => true, 'users' => []]);
        }

        $userIds = array_column($users, 'user_id');

        $privRows = $db->table('user_privileges')
            ->select('user_id, privilege_key, privilege_value')
            ->whereIn('user_id', $userIds)
            ->where('status', 'active')
            ->get()
            ->getResultArray();

        $privMap = [];
        foreach ($privRows as $row) {
            $privMap[$row['user_id']][$row['privilege_key']] = (bool) $row['privilege_value'];
        }

        foreach ($users as &$user) {
            $user['privileges'] = $privMap[$user['user_id']] ?? [];
        }

        return $this->response->setJSON(['success' => true, 'users' => $users]);
    }

}