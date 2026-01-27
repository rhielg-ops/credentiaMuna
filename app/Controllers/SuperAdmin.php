<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;
use App\Models\ActivityLogModel;
use App\Libraries\EmailService;

class SuperAdmin extends BaseController
{
    protected $userModel;
    protected $activityLogModel;
    protected $emailService;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->activityLogModel = new ActivityLogModel();
        $this->emailService = new EmailService();
    }

    /**
     * Check if user is super admin
     */
    protected function checkSuperAdmin()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'super_admin') {
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

        $data = [
            'email' => session()->get('email'),
            'role'  => session()->get('role'),
            'full_name' => session()->get('full_name') ?? 'Super Admin',
            'stats' => $this->userModel->getUserStats()
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
        
        // Get pending admins
        $pendingAdmins = $this->userModel->getPendingAdmins();

        $data = [
            'email' => session()->get('email'),
            'role'  => session()->get('role'),
            'full_name' => session()->get('full_name') ?? 'Super Admin',
            'users' => $users,
            'pending_admins' => $pendingAdmins
        ];

        return view('super_admin/user_management', $data);
    }

    /**
     * Add new admin
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
            'role' => 'required|in_list[admin,super_admin]',
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
            'password' => $initialPassword, // Will be hashed by model
            'role' => $this->request->getPost('role'),
            'access_level' => $this->request->getPost('access_level'),
            'status' => 'active', // Activate immediately
            'initial_password_changed' => false,
            'created_by' => session()->get('user_id')
        ];

        if ($this->userModel->insert($userData)) {
            $newUserId = $this->userModel->getInsertID();
            
            // Log activity
            $this->activityLogModel->logActivity(
                session()->get('user_id'),
                'user_created',
                "Created new {$userData['role']}: {$userData['email']}"
            );

            // Send welcome email
            $this->emailService->sendWelcomeEmail(
                $userData['email'],
                $userData['full_name'],
                $initialPassword,
                session()->get('full_name')
            );

            return redirect()->to('/super-admin/user-management')
                ->with('success', 'Admin account created successfully! Welcome email sent.');
        } else {
            return redirect()->back()->with('error', 'Failed to create admin account.');
        }
    }

    /**
     * Edit admin
     */
    public function editAdmin($id)
    {
        $redirect = $this->checkSuperAdmin();
        if ($redirect) return $redirect;

        // Check if user exists
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        // Prevent editing self
        if ($id == session()->get('user_id')) {
            return redirect()->back()->with('error', 'You cannot edit your own account from here.');
        }

        // Validate input
        $validation = \Config\Services::validation();
        $validation->setRules([
            'full_name' => 'required|min_length[3]|max_length[255]',
            'email' => "required|valid_email|is_unique[users.email,id,{$id}]",
            'role' => 'required|in_list[admin,super_admin]',
            'access_level' => 'required|in_list[full,limited]',
            'status' => 'required|in_list[active,inactive,suspended]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->with('error', implode('<br>', $validation->getErrors()));
        }

        // Update user
        $updateData = [
            'full_name' => $this->request->getPost('full_name'),
            'email' => $this->request->getPost('email'),
            'role' => $this->request->getPost('role'),
            'access_level' => $this->request->getPost('access_level'),
            'status' => $this->request->getPost('status')
        ];

        // Update password if provided
        $newPassword = $this->request->getPost('new_password');
        if (!empty($newPassword)) {
            $updateData['password'] = $newPassword; // Will be hashed by model
            $updateData['initial_password_changed'] = false;
        }

        if ($this->userModel->update($id, $updateData)) {
            // Log activity
            $this->activityLogModel->logActivity(
                session()->get('user_id'),
                'user_updated',
                "Updated user: {$updateData['email']}"
            );

            return redirect()->to('/super-admin/user-management')
                ->with('success', 'Admin account updated successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to update admin account.');
        }
    }

    /**
     * Delete admin
     */
    public function deleteAdmin($id)
    {
        $redirect = $this->checkSuperAdmin();
        if ($redirect) return $redirect;

        // Check if user exists
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        // Prevent deleting self
        if ($id == session()->get('user_id')) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        // Prevent deleting the only super admin
        if ($user['role'] === 'super_admin') {
            $superAdminCount = $this->userModel->where('role', 'super_admin')->countAllResults();
            if ($superAdminCount <= 1) {
                return redirect()->back()->with('error', 'Cannot delete the only super admin account.');
            }
        }

        if ($this->userModel->delete($id)) {
            // Log activity
            $this->activityLogModel->logActivity(
                session()->get('user_id'),
                'user_deleted',
                "Deleted user: {$user['email']}"
            );

            return redirect()->to('/super-admin/user-management')
                ->with('success', 'Admin account deleted successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to delete admin account.');
        }
    }

    /**
     * Approve pending admin
     */
    public function approveAdmin($id)
    {
        $redirect = $this->checkSuperAdmin();
        if ($redirect) return $redirect;

        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        if ($this->userModel->update($id, ['status' => 'active'])) {
            $this->activityLogModel->logActivity(
                session()->get('user_id'),
                'user_approved',
                "Approved user: {$user['email']}"
            );

            return redirect()->back()->with('success', 'Admin approved successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to approve admin.');
        }
    }

    /**
     * Reject pending admin
     */
    public function rejectAdmin($id)
    {
        $redirect = $this->checkSuperAdmin();
        if ($redirect) return $redirect;

        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        if ($this->userModel->delete($id)) {
            $this->activityLogModel->logActivity(
                session()->get('user_id'),
                'user_rejected',
                "Rejected user: {$user['email']}"
            );

            return redirect()->back()->with('success', 'Admin rejected and removed.');
        } else {
            return redirect()->back()->with('error', 'Failed to reject admin.');
        }
    }

    /**
     * Suspend/Unsuspend admin
     */
    public function toggleSuspend($id)
    {
        $redirect = $this->checkSuperAdmin();
        if ($redirect) return $redirect;

        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        // Prevent suspending self
        if ($id == session()->get('user_id')) {
            return redirect()->back()->with('error', 'You cannot suspend your own account.');
        }

        $newStatus = $user['status'] === 'suspended' ? 'active' : 'suspended';
        
        if ($this->userModel->update($id, ['status' => $newStatus])) {
            $action = $newStatus === 'suspended' ? 'suspended' : 'unsuspended';
            
            $this->activityLogModel->logActivity(
                session()->get('user_id'),
                "user_{$action}",
                "User {$action}: {$user['email']}"
            );

            return redirect()->back()->with('success', "Admin {$action} successfully!");
        } else {
            return redirect()->back()->with('error', 'Failed to update admin status.');
        }
    }

    /**
     * Get user data as JSON (for edit modal)
     */
    public function getUserData($id)
    {
        $redirect = $this->checkSuperAdmin();
        if ($redirect) return $redirect;

        $user = $this->userModel->find($id);
        
        if ($user) {
            // Remove password from response
            unset($user['password']);
            return $this->response->setJSON(['success' => true, 'user' => $user]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'User not found']);
        }
    }

    /**
     * All Records
     */
    public function allRecords()
    {
        $redirect = $this->checkSuperAdmin();
        if ($redirect) return $redirect;

        $data = [
            'email' => session()->get('email'),
            'role'  => session()->get('role'),
            'full_name' => session()->get('full_name') ?? 'Super Admin'
        ];

        return view('super_admin/all_records', $data);
    }

    /**
     * System Backup
     */
    public function systemBackup()
    {
        $redirect = $this->checkSuperAdmin();
        if ($redirect) return $redirect;

        $data = [
            'email' => session()->get('email'),
            'role'  => session()->get('role'),
            'full_name' => session()->get('full_name') ?? 'Super Admin'
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
            'email' => session()->get('email'),
            'role'  => session()->get('role'),
            'full_name' => session()->get('full_name') ?? 'Super Admin',
            'user_id' => session()->get('user_id')
        ];

        return view('super_admin/settings', $data);
    }
}