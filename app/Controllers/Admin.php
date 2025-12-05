<?php

namespace App\Controllers;

class Admin extends BaseController
{
    public function dashboard()
    {
        // Verify user authentication status
        if (!session()->get('isAuthenticated')) {
            session()->setFlashdata('error', 'Authentication required to access this area.');
            return redirect()->to('/login');
        }

        // Check if user account is still active
        $userId = (int) session()->get('userId');
        $userModel = new \App\Models\UserModel();
        $userRecord = $userModel->find($userId);
        
        if (!$userRecord) {
            session()->destroy();
            session()->setFlashdata('error', 'User account not found.');
            return redirect()->to('/login');
        }
        
        $userStatus = $userRecord['status'] ?? 'active';
        if ($userStatus !== 'active') {
            session()->destroy();
            session()->setFlashdata('error', 'Your account has been deactivated. Please contact an administrator.');
            return redirect()->to('/login');
        }

        // Verify user role
        $userRole = session()->get('userRole');
        if ($userRole !== 'admin') {
            session()->setFlashdata('error', 'Access denied: Insufficient permissions.');
            return redirect()->to('/announcements');
        }

        $data = [
            'title' => 'Admin Dashboard'
        ];

        return view('admin_dashboard', $data);
    }
}