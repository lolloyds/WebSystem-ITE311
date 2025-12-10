<?php

namespace App\Controllers;

use App\Models\UserModel;

class ManageUsers extends BaseController
{
    protected $userModel;
    protected $protectedAdminId;

    public function __construct()
    {
        $this->userModel = new UserModel();
        // Get the protected admin ID (first admin user - lowest ID with admin role)
        $protectedAdmin = $this->userModel->where('role', 'admin')->orderBy('id', 'ASC')->first();
        $this->protectedAdminId = $protectedAdmin ? (int) $protectedAdmin['id'] : null;
    }

    public function index()
    {
        // Verify authentication
        if (!session()->get('isAuthenticated')) {
            session()->setFlashdata('error', 'Authentication required to access this area.');
            return redirect()->to('/login');
        }

        // Verify admin role
        $userRole = session()->get('userRole');
        if ($userRole !== 'admin') {
            session()->setFlashdata('error', 'Access denied: Insufficient permissions.');
            return redirect()->to('/dashboard');
        }

        // Get all users
        $users = $this->userModel->orderBy('id', 'ASC')->findAll();

        $data = [
            'title' => 'Manage Users',
            'users' => $users,
            'protectedAdminId' => $this->protectedAdminId,
        ];

        return view('manage_users/index', $data);
    }

    public function addUser()
    {
        // Verify authentication and admin role
        if (!session()->get('isAuthenticated') || session()->get('userRole') !== 'admin') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied: Insufficient permissions.'
            ]);
        }

        if ($this->request->getMethod() !== 'POST') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request method.'
            ]);
        }

        // Get and sanitize input
        $name = trim((string) $this->request->getPost('name'));
        $email = trim((string) $this->request->getPost('email'));
        $password = (string) $this->request->getPost('password');
        $role = trim((string) $this->request->getPost('role'));

        // Validate required fields
        if (empty($name) || empty($email) || empty($password) || empty($role)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'All fields are required.'
            ]);
        }

        // Validate name format (only letters, spaces, hyphens, apostrophes)
        if (!preg_match('/^[a-zA-Z\s\-\']+$/', $name)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Name can only contain letters, spaces, hyphens, and apostrophes.'
            ]);
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please enter a valid email address.'
            ]);
        }

        // Validate role
        $allowedRoles = ['student', 'teacher', 'admin'];
        if (!in_array($role, $allowedRoles)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid role selected.'
            ]);
        }

        // Validate password strength (minimum 8 characters, at least one letter and one number)
        if (strlen($password) < 8) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Password must be at least 8 characters long.'
            ]);
        }

        if (!preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Password must contain at least one letter and one number.'
            ]);
        }

        // Check for duplicate email
        $existingUser = $this->userModel->where('email', $email)->first();
        if ($existingUser) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'This email is already in use by another account.'
            ]);
        }

        // Hash password securely
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user
        $data = [
            'name' => $name,
            'email' => $email,
            'password' => $hashedPassword,
            'role' => $role,
            'status' => 'active', // New users are active by default
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $userId = $this->userModel->insert($data);

        if ($userId) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'User created successfully.',
                'user_id' => $userId
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create user. Please try again.'
            ]);
        }
    }

    public function updateRole()
    {
        // Verify authentication and admin role
        if (!session()->get('isAuthenticated') || session()->get('userRole') !== 'admin') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied: Insufficient permissions.'
            ]);
        }

        if ($this->request->getMethod() !== 'POST') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request method.'
            ]);
        }

        // Get and sanitize input
        $userId = (int) $this->request->getPost('user_id');
        $newRole = trim((string) $this->request->getPost('role'));

        // Validate required fields
        if (empty($userId) || empty($newRole)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User ID and role are required.'
            ]);
        }

        // Prevent demoting the protected admin
        if ($userId === $this->protectedAdminId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Cannot change the role of the protected admin account.'
            ]);
        }

        // Validate role
        $allowedRoles = ['student', 'teacher', 'admin'];
        if (!in_array($newRole, $allowedRoles)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid role selected.'
            ]);
        }

        // Check if user exists
        $user = $this->userModel->find($userId);
        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User not found.'
            ]);
        }

        // Update role
        $updateData = [
            'role' => $newRole,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($this->userModel->update($userId, $updateData)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'User role updated successfully.'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update user role. Please try again.'
            ]);
        }
    }

    public function toggleStatus()
    {
        // Verify authentication and admin role
        if (!session()->get('isAuthenticated') || session()->get('userRole') !== 'admin') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied: Insufficient permissions.'
            ]);
        }

        if ($this->request->getMethod() !== 'POST') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request method.'
            ]);
        }

        // Get user ID
        $userId = (int) $this->request->getPost('user_id');

        // Prevent deactivating the protected admin
        if ($userId === $this->protectedAdminId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Cannot deactivate the protected admin account.'
            ]);
        }

        // Check if user exists
        $user = $this->userModel->find($userId);
        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User not found.'
            ]);
        }

        // Toggle status (active <-> inactive)
        $currentStatus = $user['status'] ?? 'active';
        $newStatus = ($currentStatus === 'active') ? 'inactive' : 'active';

        // Update status
        $updateData = [
            'status' => $newStatus,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($this->userModel->update($userId, $updateData)) {
            $action = ($newStatus === 'inactive') ? 'deactivated' : 'activated';
            return $this->response->setJSON([
                'success' => true,
                'message' => 'User account ' . $action . ' successfully.',
                'new_status' => $newStatus
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update user status. Please try again.'
            ]);
        }
    }

    public function editUser()
    {
        // Verify authentication and admin role
        if (!session()->get('isAuthenticated') || session()->get('userRole') !== 'admin') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied: Insufficient permissions.'
            ]);
        }

        if ($this->request->getMethod() !== 'POST') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request method.'
            ]);
        }

        // Get and sanitize input
        $userId = (int) $this->request->getPost('user_id');
        $name = trim((string) $this->request->getPost('name'));
        $email = trim((string) $this->request->getPost('email'));
        $password = (string) $this->request->getPost('password'); // Optional

        // Validate required fields
        if (empty($userId) || empty($name) || empty($email)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User ID, name, and email are required.'
            ]);
        }

        // Prevent editing protected admin
        if ($userId === $this->protectedAdminId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Cannot edit the protected admin account.'
            ]);
        }

        // Validate name format (only letters, spaces, hyphens, apostrophes)
        if (!preg_match('/^[a-zA-Z\s\-\']+$/', $name)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Name can only contain letters, spaces, hyphens, and apostrophes.'
            ]);
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please enter a valid email address.'
            ]);
        }

        // Check if user exists
        $user = $this->userModel->find($userId);
        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User not found.'
            ]);
        }

        // Check for duplicate email (only if email is different)
        if ($email !== $user['email']) {
            $existingUser = $this->userModel->where('email', $email)->first();
            if ($existingUser) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'This email is already in use by another account.'
                ]);
            }
        }

        // Prepare update data
        $updateData = [
            'name' => $name,
            'email' => $email,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        // Add password if provided
        if (!empty($password)) {
            // Validate password strength
            if (strlen($password) < 8) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Password must be at least 8 characters long.'
                ]);
            }

            if (!preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Password must contain at least one letter and one number.'
                ]);
            }

            // Hash password securely
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $updateData['password'] = $hashedPassword;
        }

        // Update user
        if ($this->userModel->update($userId, $updateData)) {
            // Check if user changed their own password - force logout
            $currentUserId = session()->get('userId');
            $passwordChanged = !empty($password);

            if ($passwordChanged && $userId == $currentUserId) {
                // Destroy session and redirect to login
                session()->destroy();
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Password updated successfully. You have been logged out for security.',
                    'force_logout' => true
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'User details updated successfully.',
                'user' => [
                    'id' => $userId,
                    'name' => $name,
                    'email' => $email
                ]
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update user details. Please try again.'
            ]);
        }
    }

    public function changePassword()
    {
        // Verify authentication and admin role
        if (!session()->get('isAuthenticated') || session()->get('userRole') !== 'admin') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied: Insufficient permissions.'
            ]);
        }

        if ($this->request->getMethod() !== 'POST') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request method.'
            ]);
        }

        // Get and sanitize input
        $userId = (int) $this->request->getPost('user_id');
        $newPassword = (string) $this->request->getPost('password');

        // Validate required fields
        if (empty($userId) || empty($newPassword)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User ID and password are required.'
            ]);
        }

        // Validate password strength
        if (strlen($newPassword) < 8) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Password must be at least 8 characters long.'
            ]);
        }

        if (!preg_match('/[A-Za-z]/', $newPassword) || !preg_match('/[0-9]/', $newPassword)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Password must contain at least one letter and one number.'
            ]);
        }

        // Check if user exists
        $user = $this->userModel->find($userId);
        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User not found.'
            ]);
        }

        // Hash password securely
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update password
        $updateData = [
            'password' => $hashedPassword,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($this->userModel->update($userId, $updateData)) {
            // Check if user changed their own password - force logout
            $currentUserId = session()->get('userId');
            if ($userId == $currentUserId) {
                // Destroy session and redirect to login
                session()->destroy();
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Password updated successfully. You have been logged out for security.',
                    'force_logout' => true
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Password updated successfully.'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update password. Please try again.'
            ]);
        }
    }

    /**
     * Get all teachers for dropdown
     */
    public function getTeachers()
    {
        // Verify authentication and admin role
        if (!session()->get('isAuthenticated') || session()->get('userRole') !== 'admin') {
            return $this->response->setJSON(['error' => 'Unauthorized']);
        }

        $teachers = $this->userModel->where('role', 'teacher')->findAll();

        return $this->response->setJSON($teachers);
    }
}
