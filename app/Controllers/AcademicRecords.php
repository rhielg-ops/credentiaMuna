<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class AcademicRecords extends BaseController
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
            'role'  => session()->get('role')
        ];

        return view('auth/academic_records', $data);
    }

    public function upload()
    {
        // Handle file upload logic here
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        // Get form data
        $studentName = $this->request->getPost('student_name');
        $studentId = $this->request->getPost('student_id');
        $recordType = $this->request->getPost('record_type');
        $academicYear = $this->request->getPost('academic_year');
        $notes = $this->request->getPost('notes');

        // Handle file upload
        $file = $this->request->getFile('record_file');
        
        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Move file to uploads directory
            $newName = $file->getRandomName();
            $file->move(WRITEPATH . 'uploads/records/', $newName);
            
            // Save to database (you'll need to create this model and table later)
            // For now, just redirect with success message
            
            return redirect()->to('/academic-records')->with('success', 'Record uploaded successfully!');
        }

        return redirect()->back()->with('error', 'Failed to upload file.');
    }
}