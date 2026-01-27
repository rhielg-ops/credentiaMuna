<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Dashboard extends BaseController
{
    public function index()
    {
        // Make sure user is logged in
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Please log in first.');
        }

        // Prepare data for the dashboard view
        $data = [
            'email' => session()->get('email'),
            'role'  => session()->get('role')
        ];

        return view('auth/dashboard', $data);
    }
}
