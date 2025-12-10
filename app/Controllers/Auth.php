<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        // If already logged in
        if (session()->get('isAuthenticated')) {
            return redirect()->to('/dashboard');
        }

        // Handle POST (form submit)
        if ($this->request->getMethod() === 'POST') {
            $userEmail = $this->request->getPost('email');
            $userPassword = $this->request->getPost('password');

            if (empty($userEmail) || empty($userPassword)) {
                return redirect()->back()->with('login_error', 'Please provide both email and password.');
            }

            $userModel = new UserModel();
            $userRecord = $userModel->where('email', $userEmail)->first();

            if (!$userRecord) {
                return redirect()->back()->with('login_error', 'No account found with email: ' . $userEmail);
            }

            if (!password_verify($userPassword, $userRecord['password'])) {
                return redirect()->back()->with('login_error', 'Incorrect password.');
            }

            // Check if user is active
            if (($userRecord['status'] ?? 'active') !== 'active') {
                return redirect()->back()->with('login_error', 'Your account is inactive. Please contact administrator.');
            }

            // Save session
            $userSession = [
                'userId'          => $userRecord['id'],
                'userName'        => $userRecord['name'],
                'userEmail'       => $userRecord['email'],
                'userRole'        => $userRecord['role'],
                'isAuthenticated' => true
            ];

            session()->set($userSession);

            // Role-based redirection after successful login
            switch ($userRecord['role']) {
                case 'student':
                    return redirect()->to('/announcements')->with('success', 'Welcome back, ' . $userRecord['name'] . '!');
                case 'teacher':
                    return redirect()->to('/dashboard')->with('success', 'Welcome back, ' . $userRecord['name'] . '!');
                case 'admin':
                    return redirect()->to('/dashboard')->with('success', 'Welcome back, ' . $userRecord['name'] . '!');
                default:
                    return redirect()->to('/dashboard')->with('success', 'Welcome back, ' . $userRecord['name'] . '!');
            }
        }

        // GET request: Show login form
        return view('login');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'You have been logged out.');
    }

    public function register()
    {
        if (session()->get('isAuthenticated')) {
            return redirect()->to('/dashboard');
        }

        if ($this->request->getMethod() === 'POST') {
            $fullName       = trim((string) $this->request->getPost('name'));
            $emailAddress   = trim((string) $this->request->getPost('email'));
            $newPassword    = (string) $this->request->getPost('password');
            $confirmPassword = (string) $this->request->getPost('password_confirm');

            if ($fullName === '' || $emailAddress === '' || $newPassword === '' || $confirmPassword === '') {
                return redirect()->back()->withInput()->with('register_error', 'All fields must be completed.');
            }

            if (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
                return redirect()->back()->withInput()->with('register_error', 'Please enter a valid email address.');
            }

            if ($newPassword !== $confirmPassword) {
                return redirect()->back()->withInput()->with('register_error', 'Passwords do not match.');
            }

            $userModel = new UserModel();

            if ($userModel->where('email', $emailAddress)->first()) {
                return redirect()->back()->withInput()->with('register_error', 'This email is already in use.');
            }

            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            $newUserId = $userModel->insert([
                'name'     => $fullName,
                'email'    => $emailAddress,
                'role'     => 'student',
                'password' => $hashedPassword,
            ], true);

            if (!$newUserId) {
                return redirect()->back()->withInput()->with('register_error', 'Account creation failed. Please try again.');
            }

            return redirect()->to('/login')->with('register_success', 'Registration successful. Please log in.');
        }

        return view('register');
    }

    public function dashboard()
    {
        // Enhanced authorization check
        if (!session()->get('isAuthenticated')) {
            return redirect()->to('/login')->with('error', 'Please login first.');
        }

        // Get user session data
        $role = session()->get('userRole');
        $userName = session()->get('userName');
        $userId = session()->get('userId');
        $userEmail = session()->get('userEmail');

        // Validate role
        if (!in_array($role, ['admin', 'teacher', 'student'])) {
            return redirect()->to('/login')->with('error', 'Invalid user role. Please contact administrator.');
        }

        // Base data for all roles
        $data = [
            'title' => 'Dashboard',
            'role' => $role,
            'userName' => $userName,
            'userEmail' => $userEmail,
            'userId' => $userId,
            'unreadCount' => 0,
        ];

        // Role-specific data fetching
        switch ($role) {
            case 'admin':
                $userModel = new UserModel();
                $courseModel = new \App\Models\CourseModel();
                $enrollmentModel = new \App\Models\EnrollmentModel();

                // Admin dashboard data
                $data['users'] = $userModel->findAll();
                $data['totalUsers'] = count($data['users']);
                $data['totalCourses'] = $courseModel->countAll();
                $data['totalEnrollments'] = $enrollmentModel->countAll();

                // Get user statistics by role
                $data['adminCount'] = $userModel->where('role', 'admin')->countAllResults();
                $data['teacherCount'] = $userModel->where('role', 'teacher')->countAllResults();
                $data['studentCount'] = $userModel->where('role', 'student')->countAllResults();

                // Recent enrollments
                $data['recentEnrollments'] = $enrollmentModel->select('enrollments.*, users.name as student_name, courses.title as course_name')
                    ->join('users', 'users.id = enrollments.user_id', 'left')
                    ->join('courses', 'courses.id = enrollments.course_id', 'left')
                    ->orderBy('enrollments.enrolled_at', 'DESC')
                    ->limit(5)
                    ->findAll();
                break;

            case 'teacher':
                $courseModel = new \App\Models\CourseModel();
                $enrollmentModel = new \App\Models\EnrollmentModel();

                // Teacher dashboard data - now filter by teacher's courses
                $data['myCourses'] = $courseModel->getCoursesByTeacher($userId);
                $data['totalMyCourses'] = count($data['myCourses']);

                // Get total students enrolled in teacher's courses
                $teacherEnrollments = $courseModel->getTeacherCourseEnrollments($userId);
                $data['totalStudents'] = count(array_unique(array_column($teacherEnrollments, 'user_id')));

                // Recent enrollments in teacher's courses only
                $data['recentEnrollments'] = array_slice($teacherEnrollments, 0, 5);
                break;

            case 'student':
                $enrollmentModel = new \App\Models\EnrollmentModel();
                $courseModel = new \App\Models\CourseModel();
                $materialModel = new \App\Models\MaterialModel();

                // Student dashboard data
                $data['enrolledCourses'] = $enrollmentModel->getUserEnrollments($userId);
                $data['availableCourses'] = $courseModel->getAvailableCourses($userId);
                $data['totalEnrolled'] = count($data['enrolledCourses']);
                $data['totalAvailable'] = count($data['availableCourses']);

                // Add materials to enrolled courses
                foreach ($data['enrolledCourses'] as &$course) {
                    $course['materials'] = $materialModel->getMaterialsByCourse($course['course_id']);
                }

                // Get recent activity (enrollments)
                $data['recentActivity'] = $enrollmentModel->select('enrollments.*, courses.title as course_name, courses.course_code')
                    ->join('courses', 'courses.id = enrollments.course_id', 'left')
                    ->where('enrollments.user_id', $userId)
                    ->orderBy('enrollments.enrolled_at', 'DESC')
                    ->limit(3)
                    ->findAll();
                break;
        }

        return view('auth/dashboard', $data);
    }

}
