<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;
use App\Models\ActivityLogModel;

class Settings extends BaseController
{
    protected $userModel;
    protected $activityLogModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->activityLogModel = new ActivityLogModel();
    }

    /**
     * Settings page - accessible by both Admin and Super Admin
     */
    public function index()
    {
        // Make sure user is logged in
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Please log in first.');
        }

        $userId = session()->get('user_id');
        $role = session()->get('role');

        // Get user data
        $user = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to('/login')->with('error', 'User not found.');
        }

        // Prepare data for the view
        $data = [
            'title' => 'Settings - CredentiaTAU',
            'email' => $user['email'],
            'role' => $role,
            'user_id' => $userId,
            'full_name' => $user['full_name'],
            'access_level' => $user['access_level'] ?? 'full',
            'user' => $user,
            'is_super_admin' => ($role === 'admin')
        ];

        return view('auth/settings', $data);
    }

    /**
     * Update profile (Super Admin only)
     */
    public function updateProfile()
    {
        // Check if user is logged in and is super admin
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Please log in first.');
        }

        if (session()->get('role') !== 'admin') {
            return redirect()->back()->with('error', 'Unauthorized. Only Admin can update profile.');
        }

        $userId = session()->get('user_id');

        // Validate input
        $validation = \Config\Services::validation();
        $validation->setRules([
            'full_name' => 'required|min_length[3]|max_length[255]',
            'email' => 'required|valid_email'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->with('error', implode('<br>', $validation->getErrors()));
        }

        $fullName = $this->request->getPost('full_name');
        $email = $this->request->getPost('email');

        // Check if email already exists for another user
        if ($this->userModel->emailExists($email, $userId)) {
            return redirect()->back()->with('error', 'Email already exists.');
        }

        // Update user data
        $updateData = [
            'full_name' => $fullName,
            'email' => $email
        ];

        if ($this->userModel->update($userId, $updateData)) {
            // Update session data
            session()->set([
                'full_name' => $fullName,
                'email' => $email
            ]);

            // Log activity
            $this->activityLogModel->logActivity(
                $userId,
                'profile_updated',
                "Profile updated: {$email}"
            );

            return redirect()->back()->with('success', 'Profile updated successfully!');
        }

        return redirect()->back()->with('error', 'Failed to update profile.');
    }

    /**
     * Change password (Super Admin only)
     */
    public function changePassword()
    {
        // Check if user is logged in and is super admin
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Please log in first.');
        }

        if (session()->get('role') !== 'admin') {
            return redirect()->back()->with('error', 'Unauthorized. Only Admin can change password.');
        }

        $userId = session()->get('user_id');

        // Validate input
        $validation = \Config\Services::validation();
        $validation->setRules([
            'current_password' => 'required|min_length[8]',
            'new_password' => 'required|min_length[8]',
            'confirm_password' => 'required|matches[new_password]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->with('error', implode('<br>', $validation->getErrors()));
        }

        $currentPassword = $this->request->getPost('current_password');
        $newPassword = $this->request->getPost('new_password');

        // Get user
        $user = $this->userModel->find($userId);

        // Verify current password
        if (!password_verify($currentPassword, $user['password'])) {
            return redirect()->back()->with('error', 'Current password is incorrect.');
        }

        // Update password
        if ($this->userModel->update($userId, ['password' => $newPassword])) {
            // Log activity
            $this->activityLogModel->logActivity(
                $userId,
                'password_changed',
                'Password changed successfully'
            );

            return redirect()->back()->with('success', 'Password changed successfully!');
        }

        return redirect()->back()->with('error', 'Failed to change password.');
    }

    /**
     * Activity Logs (Super Admin only)
     */
    public function activityLogs()
    {
        // Check if user is logged in and is super admin
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Please log in first.');
        }

        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Unauthorized. Super Admin access required.');
        }

        // Get filter parameters
        $actionFilter = $this->request->getGet('action') ?? 'all';
        $dateFilter = $this->request->getGet('date') ?? 'all';
        $userFilter = $this->request->getGet('user') ?? 'all';

        // Build query
        $builder = $this->activityLogModel
            ->select('activity_logs.*, users.full_name, users.email')
            ->join('users', 'activity_logs.user_id = users.id', 'left');

        // Apply filters
        if ($actionFilter !== 'all') {
            $builder->like('activity_logs.action', $actionFilter);
        }

        if ($dateFilter !== 'all') {
            switch ($dateFilter) {
                case 'today':
                    $builder->where('DATE(activity_logs.created_at)', date('Y-m-d'));
                    break;
                case 'week':
                    $builder->where('activity_logs.created_at >=', date('Y-m-d', strtotime('-7 days')));
                    break;
                case 'month':
                    $builder->where('activity_logs.created_at >=', date('Y-m-d', strtotime('-30 days')));
                    break;
            }
        }

        if ($userFilter !== 'all') {
            $builder->where('activity_logs.user_id', $userFilter);
        }

        // Get logs
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
            'actionFilter' => $actionFilter,
            'dateFilter' => $dateFilter,
            'userFilter' => $userFilter
        ];

        return view('super_admin/activity_logs', $data);
    }
}