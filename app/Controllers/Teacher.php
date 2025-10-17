<?php

namespace App\Controllers;

class Teacher extends BaseController
{
    public function dashboard()
    {
        // Verify user authentication status
        if (!session()->get('isAuthenticated')) {
            session()->setFlashdata('error', 'Authentication required to access this area.');
            return redirect()->to('/login');
        }

        // Verify user role
        $userRole = session()->get('userRole');
        if ($userRole !== 'teacher') {
            session()->setFlashdata('error', 'Access denied: Insufficient permissions.');
            return redirect()->to('/announcements');
        }

        $data = [
            'title' => 'Teacher Dashboard'
        ];

        return view('teacher_dashboard', $data);
    }
}
