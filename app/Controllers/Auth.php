<?php

namespace App\Controllers;

use App\Models\EnrollmentModel;

class Auth extends BaseController
{

    public function login()
    {
        // Verify if user session is active
        if (session()->get('isAuthenticated')) {
            return redirect()->to('/dashboard');
        }

        // Process form submission
        if ($this->request->getMethod() === 'POST') {
            // Extract login credentials
            $userEmail = $this->request->getPost('email');
            $userPassword = $this->request->getPost('password');

            // Validate input fields
            if (empty($userEmail) || empty($userPassword)) {
                session()->setFlashdata('login_error', 'Please provide both email and password.');
                return view('login');
            }

            // Query user from database
            $userModel = new \App\Models\UserModel();
            $userRecord = $userModel->where('email', $userEmail)->first();

            // Verify user existence
            if (!$userRecord) {
                session()->setFlashdata('login_error', 'No account found with email: ' . $userEmail);
                return view('login');
            }

            // Authenticate password
            if (!password_verify($userPassword, $userRecord['password'])) {
                session()->setFlashdata('login_error', 'Incorrect password for: ' . $userEmail);
                return view('login');
            }

            // Check if user account is active
            $userStatus = $userRecord['status'] ?? 'active';
            if ($userStatus !== 'active') {
                session()->setFlashdata('login_error', 'Your account has been deactivated. Please contact an administrator.');
                return view('login');
            }

            // Store user session data
            $userSession = [
                'userId' => $userRecord['id'],
                'userName' => $userRecord['name'],
                'userEmail' => $userRecord['email'],
                'userRole' => $userRecord['role'],
                'isAuthenticated' => true
            ];
            
            session()->set($userSession);

            // Display success message and redirect
            $roleLabel = ucfirst((string) $userRecord['role']);
            session()->setFlashdata('success', 'Welcome back, ' . $userRecord['name'] . ' (' . $roleLabel . ').');

            // Role-based redirection
            $userRole = (string) $userRecord['role'];
            if ($userRole === 'student') {
                return redirect()->to('/announcements');
            } elseif ($userRole === 'admin') {
                return redirect()->to('/admin/dashboard');
            } else {
                // Teachers go to dashboard
                return redirect()->to('/dashboard');
            }
        }

        // Display login form for GET requests
        return view('login');
    }



    public function logout()
    {
        $userSession = session();
        $userSession->destroy();
        return redirect()->to('/login');
    }

    public function register()
    {
        $currentSession = session();
        if ($currentSession->get('isAuthenticated')) {
            return redirect()->to('/dashboard');
        }

        // Process registration form submission
        if ($this->request->getMethod() === 'POST') {
            $fullName = trim((string) $this->request->getPost('name'));
            $emailAddress = trim((string) $this->request->getPost('email'));
            $newPassword = (string) $this->request->getPost('password');
            $confirmPassword = (string) $this->request->getPost('password_confirm');

            // Validate required fields
            if ($fullName === '' || $emailAddress === '' || $newPassword === '' || $confirmPassword === '') {
                return redirect()->back()->withInput()->with('register_error', 'All fields must be completed.');
            }

            // Validate name format (only letters, spaces, hyphens, apostrophes)
            if (!preg_match('/^[a-zA-Z\s\-\']+$/', $fullName)) {
                return redirect()->back()->withInput()->with('register_error', 'Name can only contain letters, spaces, hyphens, and apostrophes.');
            }

            // Validate email format
            if (! filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
                return redirect()->back()->withInput()->with('register_error', 'Please enter a valid email address.');
            }

            // Verify password confirmation
            if ($newPassword !== $confirmPassword) {
                return redirect()->back()->withInput()->with('register_error', 'Password confirmation does not match.');
            }

            $userModel = new \App\Models\UserModel();

            // Check for duplicate email
            if ($userModel->where('email', $emailAddress)->first()) {
                return redirect()->back()->withInput()->with('register_error', 'This email is already in use.');
            }

            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            $newUserId = $userModel->insert([
                'name' => $fullName,
                'email' => $emailAddress,
                'role' => 'student',
                'password' => $hashedPassword,
            ], true);

            if (! $newUserId) {
                return redirect()->back()->withInput()->with('register_error', 'Account creation failed. Please try again.');
            }

            // Redirect to login with confirmation
            return redirect()
                ->to('/login')
                ->with('register_success', 'Registration completed successfully. You may now log in.');
        }

        // Display registration form
        return view('register');
    }

    

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

        $role = (string) session()->get('userRole');

        $data = [
            'role' => $role,
        ];


        // Load role-specific data
        try {
            $db = \Config\Database::connect();

            if ($role === 'admin') {
                $data['totalUsers'] = (int) $db->table('users')->countAllResults();
                $data['recentUsers'] = $db->table('users')
                    ->select('id, name, email, role, created_at')
                    ->orderBy('id', 'DESC')
                    ->limit(5)
                    ->get()
                    ->getResultArray();
            } elseif ($role === 'teacher') {
                $data['myCourses'] = $db->table('courses')
                    ->select('id, title, created_at')
                    ->where('teacher_id', $userId)
                    ->orderBy('id', 'DESC')
                    ->get()
                    ->getResultArray();
            } elseif ($role === 'student') {
                // Use EnrollmentModel to get enrolled and available courses
                $enrollmentModel = new EnrollmentModel();
                $data['enrolledCourses'] = $enrollmentModel->getUserEnrollments($userId);
                $data['availableCourses'] = $enrollmentModel->getAvailableCourses($userId);
            }
        } catch (\Throwable $e) {
            // Fallback without DB if tables are missing
        }

        return view('auth/dashboard', $data);
    }
    
}
