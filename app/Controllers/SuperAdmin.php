<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;
use App\Models\ActivityLogModel;
use App\Models\DashboardModel;
use App\Models\UserPrivilegeModel;
use App\Libraries\EmailService;

class SuperAdmin extends BaseController
{
    protected $userModel;
    protected $activityLogModel;
    protected $dashboardModel;
    protected $privilegeModel;
    protected $emailService;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->activityLogModel = new ActivityLogModel();
        $this->dashboardModel = new DashboardModel();
        $this->privilegeModel = new UserPrivilegeModel();
        $this->emailService = new EmailService();
    }

    /**
     * Check if user is super admin
     */
    protected function checkSuperAdmin()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Unauthorized access.');
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
        $stats = $this->dashboardModel->getSuperAdminStats();
        $recentActivity = $this->dashboardModel->getRecentActivity(5);

        $data = [
            'title' => 'Admin Dashboard - CredentiaTAU',
            'email' => session()->get('email'),
            'role'  => session()->get('role'),
            'full_name' => session()->get('full_name') ?? 'Admin',
            'stats' => $stats,
            'recent_activity' => $recentActivity
        ];

        return view('super_admin/dashboard', $data);
    }

    /**
     * User Management
     */
    public function userManagement()
    {
        $redirect = $this->checkSuperAdmin();
        if ($redirect) return $redirect;

        // Get all users with record counts
        $users = $this->userModel->getAllUsersWithRecordCounts();
        
        // Get inactive users (pending reactivation)
        $inactiveUsers = $this->userModel->getInactiveUsers();

        // Build a privileges map keyed by user_id for use in the view
        $userPrivilegesMap = [];
        foreach ($users as $user) {
            $userPrivilegesMap[$user['id']] = $this->privilegeModel->getUserPrivileges($user['id']);
        }

        $data = [
            'title' => 'User Management - CredentiaTAU',
            'email' => session()->get('email'),
            'role'  => session()->get('role'),
            'full_name' => session()->get('full_name') ?? 'Admin',
            'users' => $users,
            'pending_admins' => $inactiveUsers,
            'user_privileges_map' => $userPrivilegesMap,
            'privilege_definitions' => $this->privilegeModel->getPrivilegeDefinitions()
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

        $data = [
            'title' => 'Settings - CredentiaTAU',
            'email' => session()->get('email'),
            'role'  => session()->get('role'),
            'full_name' => session()->get('full_name') ?? 'Admin',
            'user_id' => session()->get('user_id')
        ];

        return view('super_admin/settings', $data);
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
            'full_name' => 'required|min_length[3]|max_length[255]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'username' => 'required|min_length[3]|max_length[100]|is_unique[users.username]|alpha_numeric_punct',
            'role' => 'required|in_list[admin,user]',
            'access_level' => 'required|in_list[full,limited]',
            'initial_password' => 'required|min_length[8]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->with('error', implode('<br>', $validation->getErrors()));
        }

        // Generate initial password
        $initialPassword = $this->request->getPost('initial_password');
        
        // Create user
        $userData = [
            'full_name' => $this->request->getPost('full_name'),
            'email' => $this->request->getPost('email'),
            'username' => $this->request->getPost('username'),
            'password' => $initialPassword, // Will be hashed by model
            'role' => $this->request->getPost('role'),
            'access_level' => $this->request->getPost('access_level'),
            'status' => 'active', // Activate immediately
            'initial_password_changed' => false,
            'created_by' => session()->get('user_id')
        ];

        if ($this->userModel->insert($userData)) {
            $newUserId = $this->userModel->getInsertID();
            
            // Build privileges from submitted checkboxes, falling back to role defaults
            $submittedPrivs = $this->request->getPost('privileges') ?? null;

            if (is_array($submittedPrivs) && count($submittedPrivs) > 0) {
                $allKeys = [
                     'records_upload', 'files_view', 'records_organize',
    'folders_add', 'records_delete', 'folders_delete',
    'profile_edit', 'user_management', 'system_backup', 'audit_logs', 'full_admin'
                ];
                $privileges = [];
                foreach ($allKeys as $key) {
                    $privileges[$key] = in_array($key, $submittedPrivs, true);
                }
                $this->privilegeModel->setPrivileges($newUserId, $privileges);
            } else {
                // Fallback: initialize default privileges for the user's role
                $this->privilegeModel->initializeUserPrivileges($newUserId, $userData['role']);
            }
            
            // Log activity
            $roleDisplay = $this->userModel->getRoleDisplayName($userData['role']);
            $this->activityLogModel->logActivity(
                session()->get('user_id'),
                'user_created',
                "Created new {$roleDisplay}: {$userData['email']} (username: {$userData['username']})"
            );

            // Send welcome email
            $this->emailService->sendWelcomeEmail(
                $userData['email'],
                $userData['full_name'],
                $initialPassword,
                session()->get('full_name')
            );

            return redirect()->to('/super-admin/user-management')
                ->with('success', 'User account created successfully! Welcome email sent.');
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

        // Validate input
        $validation = \Config\Services::validation();
        $validation->setRules([
            'full_name' => 'required|min_length[3]|max_length[255]',
            'email' => "required|valid_email|is_unique[users.email,id,{$id}]",
            'username' => "required|min_length[3]|max_length[100]|is_unique[users.username,id,{$id}]|alpha_numeric_punct",
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

        // If role changed, update privileges
        if ($existingUser['role'] !== $updateData['role']) {
            $this->privilegeModel->deleteUserPrivileges($id);
            $this->privilegeModel->initializeUserPrivileges($id, $updateData['role']);
        }

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
        $approvalModel->update($request['id'], [
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

    if ($id == session()->get('user_id')) {
        return redirect()->back()->with('error', 'You cannot change your own status.');
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
        if ($id == session()->get('user_id')) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        // Prevent deleting the only super admin
        if ($user['role'] === 'admin') {
            $count = $this->userModel->where('role', 'admin')->countAllResults();
            if ($count <= 1) {
                return redirect()->back()->with('error', 'Cannot delete the only Admin (Super Admin) account.');
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
            ->where('id', $userId)
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
        
        return [
            'success' => true,
            'privileges' => $privileges,
            'definitions' => $definitions,
            'user' => [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'role' => $user->role,
                'access_level' => $user->access_level
            ]
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

        // All known privilege keys
        $allKeys = [
            'records_upload', 'files_view', 'records_organize',
    'folders_add', 'records_delete', 'folders_delete',
    'profile_edit', 'user_management', 'system_backup', 'audit_logs', 'full_admin'
        ];

        // Parse submitted JSON body
        $body       = $this->request->getJSON(true);
        $submitted  = $body['privileges'] ?? [];

        // Build a clean true/false map for every key
        $privileges = [];
        foreach ($allKeys as $key) {
            $privileges[$key] = isset($submitted[$key]) && $submitted[$key] === true;
        }

        if ($this->privilegeModel->setPrivileges($userId, $privileges)) {
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

        // Get filter parameters
        $action = $this->request->getGet('action');
        $timeRange = $this->request->getGet('time_range') ?? '7';
        $userId = $this->request->getGet('user_id');

        // Build query
        $builder = $this->activityLogModel
            ->select('activity_logs.*, users.full_name, users.email')
            ->join('users', 'activity_logs.user_id = users.id', 'left');

        // Apply filters
        if ($action) {
            $builder->like('activity_logs.action', $action);
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
        $users = $this->userModel->select('id, full_name, email')
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
}