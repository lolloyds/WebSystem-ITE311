<?php

namespace App\Controllers;

class Settings extends BaseController
{
    public function index()
    {
        // Settings are now handled via modals in header.php
        // Redirect to dashboard since settings are accessible from the navbar
        return redirect()->to('/dashboard');
    }

    public function updateProfile()
    {
        // Check authentication
        if (!session()->get('isAuthenticated')) {
            session()->setFlashdata('error', 'Authentication required.');
            return redirect()->to('/login');
        }

        if ($this->request->getMethod() === 'POST') {
            $userId = (int) session()->get('userId');
            $name = trim((string) $this->request->getPost('name'));
            $email = trim((string) $this->request->getPost('email'));

            // Validate input
            if (empty($name) || empty($email)) {
                session()->setFlashdata('error', 'Name and email are required.');
                return redirect()->back();
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                session()->setFlashdata('error', 'Please enter a valid email address.');
                return redirect()->back();
            }

            $userModel = new \App\Models\UserModel();

            // Check if email is already taken by another user
            $existingUser = $userModel->where('email', $email)->where('id !=', $userId)->first();
            if ($existingUser) {
                session()->setFlashdata('error', 'This email is already in use by another account.');
                return redirect()->back();
            }

            // Update user profile
            $updateData = [
                'name' => $name,
                'email' => $email,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            if ($userModel->update($userId, $updateData)) {
                // Update session data
                session()->set('userName', $name);
                session()->set('userEmail', $email);
                
                session()->setFlashdata('success', 'Profile updated successfully.');
            } else {
                session()->setFlashdata('error', 'Failed to update profile. Please try again.');
            }
        }

        return redirect()->back();
    }

    public function changePassword()
    {
        // Check authentication
        if (!session()->get('isAuthenticated')) {
            session()->setFlashdata('error', 'Authentication required.');
            return redirect()->to('/login');
        }

        if ($this->request->getMethod() === 'POST') {
            $userId = (int) session()->get('userId');
            $currentPassword = (string) $this->request->getPost('current_password');
            $newPassword = (string) $this->request->getPost('new_password');
            $confirmPassword = (string) $this->request->getPost('confirm_password');

            // Validate input
            if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
                session()->setFlashdata('error', 'All password fields are required.');
                return redirect()->back();
            }

            if ($newPassword !== $confirmPassword) {
                session()->setFlashdata('error', 'New password and confirmation do not match.');
                return redirect()->back();
            }

            if (strlen($newPassword) < 6) {
                session()->setFlashdata('error', 'New password must be at least 6 characters long.');
                return redirect()->back();
            }

            $userModel = new \App\Models\UserModel();
            $user = $userModel->find($userId);

            // Verify current password
            if (!password_verify($currentPassword, $user['password'])) {
                session()->setFlashdata('error', 'Current password is incorrect.');
                return redirect()->back();
            }

            // Update password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            if ($userModel->update($userId, ['password' => $hashedPassword, 'updated_at' => date('Y-m-d H:i:s')])) {
                session()->setFlashdata('success', 'Password changed successfully.');
            } else {
                session()->setFlashdata('error', 'Failed to change password. Please try again.');
            }
        }

        return redirect()->back();
    }
}

