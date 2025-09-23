<?php

namespace App\Controllers;

class Auth extends BaseController
{

    public function login()
    {
        // Verify if user session is active
        if (session()->get('isAuthenticated')) {
            $role = (string) session()->get('userRole');
            $target = '/student/dashboard';
            if ($role === 'admin') {
                $target = '/admin/dashboard';
            } elseif ($role === 'teacher') {
                $target = '/teacher/dashboard';
            }
            return redirect()->to($target);
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
            session()->setFlashdata('success', 'Hello ' . $userRecord['name'] . ', welcome back!');

            // Role-based redirection
            $role = (string) $userRecord['role'];
            $redirectUrl = '/student/dashboard';
            if ($role === 'admin') {
                $redirectUrl = '/admin/dashboard';
            } elseif ($role === 'teacher') {
                $redirectUrl = '/teacher/dashboard';
            }

            return redirect()->to($redirectUrl);
        }

        // Display login form for GET requests
        return view('login');
    }

    public function attempt()
    {
        $request = $this->request;
        $email = trim((string) $request->getPost('email'));
        $password = (string) $request->getPost('password');

        // Try database user first
        $userModel = new \App\Models\UserModel();
        $user = $userModel->where('email', $email)->first();
        if ($user && password_verify($password, $user['password'])) {
            $session = session();
            $session->set([
                'isLoggedIn' => true,
                'userEmail' => $email,
            ]);
            return redirect()->to(base_url('dashboard'));
        }

        return redirect()->back()->with('login_error', 'Invalid credentials');
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

    public function store()
    {
        $name = trim((string) $this->request->getPost('name'));
        $email = trim((string) $this->request->getPost('email'));
        $password = (string) $this->request->getPost('password');
        $passwordConfirm = (string) $this->request->getPost('password_confirm');

        if ($name === '' || $email === '' || $password === '' || $passwordConfirm === '') {
            return redirect()->back()->withInput()->with('register_error', 'All fields are required.');
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->withInput()->with('register_error', 'Invalid email address.');
        }

        if ($password !== $passwordConfirm) {
            return redirect()->back()->withInput()->with('register_error', 'Passwords do not match.');
        }

        $userModel = new \App\Models\UserModel();

        // Check for existing email
        if ($userModel->where('email', $email)->first()) {
            return redirect()->back()->withInput()->with('register_error', 'Email is already registered.');
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $userId = $userModel->insert([
            'name' => $name,
            'email' => $email,
            'role' => 'student',
            'password' => $passwordHash,
        ], true);

        if (! $userId) {
            return redirect()->back()->withInput()->with('register_error', 'Registration failed.');
        }

        // Redirect to login with success message
        return redirect()
            ->to(base_url('login'))
            ->with('register_success', 'Account created successfully. Please log in.');
    }

    public function dashboard()
    {
        // Verify user authentication status
        if (!session()->get('isAuthenticated')) {
            session()->setFlashdata('error', 'Authentication required to access this area.');
            return redirect()->to('/login');
        }

        // Render dashboard interface
        return view('dashboard');
    }
    
}   