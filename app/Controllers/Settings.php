<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Settings extends BaseController
{
    public function index()
    {
        // Make sure user is logged in
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Please log in first.');
        }

        // Prepare data for the view
        $data = [
            'email' => session()->get('email'),
            'role'  => session()->get('role'),
            'user_id' => session()->get('user_id') ?? 1,
            'full_name' => session()->get('full_name') ?? 'Admin User'
        ];

        return view('auth/settings', $data);
    }
}