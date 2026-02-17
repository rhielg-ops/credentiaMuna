<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\DashboardModel;

class Dashboard extends BaseController
{
    protected $dashboardModel;

    public function __construct()
    {
        $this->dashboardModel = new DashboardModel();
    }

    public function index()
    {
        // Make sure user is logged in
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Please log in first.');
        }

        // Load dashboard statistics dynamically
        $stats = $this->dashboardModel->getSuperAdminStats();


        // Prepare data for the dashboard view
        $data = [
            'title' => 'Dashboard - CredentiaTAU',
            'email' => session()->get('email'),
            'role'  => session()->get('role'),
            'stats' => $stats
        ];

        return view('auth/dashboard', $data);
    }
}