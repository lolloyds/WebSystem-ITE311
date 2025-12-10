<?php

namespace App\Controllers;

class Admin extends BaseController
{
    public function dashboard()
    {
        // Debug: Check if method is being called
        log_message('info', 'Admin dashboard method called');

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

        // Get recent users (last 5 registered users)
        $recentUsers = $userModel->orderBy('created_at', 'DESC')->limit(5)->findAll();

        // Debug: Check if users are found
        log_message('info', 'Recent users count: ' . count($recentUsers));
        if (!empty($recentUsers)) {
            log_message('info', 'First user: ' . json_encode($recentUsers[0]));
        }

        $data = [
            'title' => 'Admin Dashboard',
            'recentUsers' => $recentUsers
        ];

        return view('admin_dashboard', $data);
    }

    public function debugUsers()
    {
        $userModel = new \App\Models\UserModel();
        $recentUsers = $userModel->orderBy('created_at', 'DESC')->limit(5)->findAll();

        echo "<h1>Debug: Recent Users</h1>";
        echo "<p>Count: " . count($recentUsers) . "</p>";

        if (!empty($recentUsers)) {
            echo "<table border='1'>";
            echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Created</th></tr>";
            foreach ($recentUsers as $user) {
                echo "<tr>";
                echo "<td>" . $user['id'] . "</td>";
                echo "<td>" . $user['name'] . "</td>";
                echo "<td>" . $user['email'] . "</td>";
                echo "<td>" . $user['role'] . "</td>";
                echo "<td>" . ($user['status'] ?? 'active') . "</td>";
                echo "<td>" . $user['created_at'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No users found!</p>";
        }
    }
}
