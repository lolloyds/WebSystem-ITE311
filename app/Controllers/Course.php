<?php

namespace App\Controllers;

use App\Models\CourseModel;
use App\Models\EnrollmentModel;
use App\Models\NotificationModel;
use CodeIgniter\Controller;

class Course extends BaseController
{
    protected $courseModel;
    protected $enrollmentModel;
    protected $notificationModel;

    public function __construct()
    {
        $this->courseModel = new CourseModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->notificationModel = new NotificationModel();
    }

    /**
     * Handle course enrollment via AJAX
     *
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function enroll()
    {
        // Check if user is logged in
        $session = session();
        if (!$session->get('isAuthenticated')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You must be logged in to enroll in courses.'
            ]);
        }

        // Only students can enroll in courses
        $userRole = $session->get('userRole');
        if ($userRole !== 'student') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Only students can enroll in courses.'
            ]);
        }

        // Get user ID from session
        $user_id = $session->get('userId');
        
        // Get course_id from POST request
        $course_id = $this->request->getPost('course_id');

        // Validate course_id
        if (empty($course_id) || !is_numeric($course_id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid course ID.'
            ]);
        }

        // Check if user is already enrolled
        if ($this->enrollmentModel->isAlreadyEnrolled($user_id, $course_id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You are already enrolled in this course.'
            ]);
        }

        // Prepare enrollment data
        $enrollmentData = [
            'user_id' => $user_id,
            'course_id' => $course_id,
            'enrolled_at' => (new \DateTime('now', new \DateTimeZone(config('App')->appTimezone ?? 'UTC')))->format(\DateTime::ATOM)
        ];

        // Insert enrollment record
        $result = $this->enrollmentModel->enrollUser($enrollmentData);

        if ($result) {
            // Create a notification for the user
            $db = \Config\Database::connect();
            $course = $db->table('courses')
                        ->where('id', $course_id)
                        ->get()
                        ->getRowArray();
            
            if ($course) {
                $notificationData = [
                    'user_id' => $user_id,
                    'message' => 'You have been enrolled in ' . $course['title'],
                    'is_read' => 0,
                    'created_at' => (new \DateTime('now', new \DateTimeZone(config('App')->appTimezone ?? 'UTC')))->format(\DateTime::ATOM)
                ];
                $this->notificationModel->createNotification($notificationData);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Successfully enrolled in the course!',
                'enrollment_id' => $result
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to enroll in the course. Please try again.'
            ]);
        }
    }

    /**
     * Get course details
     *
     * @param int $id Course ID
     * @return string
     */
    public function index()
    {
        $db = \Config\Database::connect();

        $courses = $db->table('courses')
                    ->select('courses.*, users.name as teacher_name')
                    ->join('users', 'users.id = courses.teacher_id', 'left')
                    ->orderBy('courses.created_at', 'DESC')
                    ->get()
                    ->getResultArray();

        $data['courses'] = $courses;
        return view('course/index', $data);
    }

    public function search()
    {
        $request = $this->request;
        $searchTerm = $request->getGet('q') ?? '';

        $db = \Config\Database::connect();
        $builder = $db->table('courses')
                    ->select('courses.*, users.name as teacher_name')
                    ->join('users', 'users.id = courses.teacher_id', 'left');

        if (!empty($searchTerm)) {
            $builder->groupStart()
                    ->like('courses.title', $searchTerm)
                    ->orLike('courses.description', $searchTerm)
                    ->groupEnd();
        }

        $courses = $builder->orderBy('courses.created_at', 'DESC')
                           ->limit(50)
                           ->get()
                           ->getResultArray();

        if ($request->isAJAX()) {
            return $this->response->setJSON($courses);
        }

        $data['courses'] = $courses;
        $data['search_term'] = $searchTerm;
        return view('course/index', $data);
    }

    public function view($id)
    {
        $db = \Config\Database::connect();

        $course = $db->table('courses')
                    ->where('id', $id)
                    ->get()
                    ->getRowArray();

        if (!$course) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Course not found');
        }

        $data['course'] = $course;
        return view('course/view', $data);
    }

    /**
     * Get enrolled courses for the authenticated user via AJAX
     *
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function getEnrolledCourses()
    {
        // Check if user is logged in
        $session = session();
        if (!$session->get('isAuthenticated')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You must be logged in to view enrolled courses.'
            ]);
        }

        // Get user ID from session
        $user_id = $session->get('userId');

        // Get search term
        $searchTerm = $this->request->getGet('q') ?? '';

        // Get enrolled courses
        $enrolledCourses = $this->enrollmentModel->getUserEnrollments($user_id);

        // Add teacher name to each course and filter by search term
        $db = \Config\Database::connect();
        $filteredCourses = [];
        foreach ($enrolledCourses as $course) {
            $teacher = $db->table('courses')
                         ->select('users.name')
                         ->join('users', 'users.id = courses.teacher_id')
                         ->where('courses.id', $course['course_id'])
                         ->get()
                         ->getRowArray();
            $course['teacher_name'] = $teacher ? $teacher['name'] : 'Unknown';

            // Apply search filter
            if (empty($searchTerm) ||
                stripos($course['title'], $searchTerm) !== false ||
                stripos($course['description'], $searchTerm) !== false) {
                $filteredCourses[] = $course;
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'courses' => $filteredCourses
        ]);
    }

    /**
     * Admin dashboard for course management
     */
    public function admin()
    {
        $session = session();
        if (!$session->get('isAuthenticated') || $session->get('userRole') !== 'admin') {
            return redirect()->to('/login');
        }

        $data['totalCourses'] = $this->courseModel->getTotalCourses();
        $data['activeCourses'] = $this->courseModel->getActiveCourses();
        $data['courses'] = $this->courseModel->getCoursesWithTeacher();

        return view('course/admin', $data);
    }

    /**
     * Get courses for admin table via AJAX
     */
    public function adminCourses()
    {
        $session = session();
        if (!$session->get('isAuthenticated') || $session->get('userRole') !== 'admin') {
            return $this->response->setJSON(['error' => 'Unauthorized']);
        }

        $search = $this->request->getGet('search') ?? '';
        $courses = $this->courseModel->getCoursesWithTeacher($search);

        return $this->response->setJSON($courses);
    }

    /**
     * Update course details via AJAX
     */
    public function update()
    {
        $session = session();
        if (!$session->get('isAuthenticated') || $session->get('userRole') !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $courseId = $this->request->getPost('course_id');
        $startDate = $this->request->getPost('start_date');
        $endDate = $this->request->getPost('end_date');

        // Validate dates
        if (strtotime($startDate) > strtotime($endDate)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Start date cannot be after end date.'
            ]);
        }

        $data = [
            'course_code' => $this->request->getPost('course_code'),
            'school_year' => $this->request->getPost('school_year'),
            'semester' => $this->request->getPost('semester'),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'teacher_id' => $this->request->getPost('teacher_id'),
            'schedule' => $this->request->getPost('schedule'),
            'status' => $this->request->getPost('status')
        ];

        if ($this->courseModel->update($courseId, $data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Course updated successfully.'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update course.'
            ]);
        }
    }

    /**
     * Create a new course via AJAX
     */
    public function create()
    {
        $session = session();
        if (!$session->get('isAuthenticated') || $session->get('userRole') !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $startDate = $this->request->getPost('start_date');
        $endDate = $this->request->getPost('end_date');

        // Validate dates
        if (strtotime($startDate) > strtotime($endDate)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Start date cannot be after end date.'
            ]);
        }

        // Check if course code already exists
        $existingCourse = $this->courseModel->where('course_code', $this->request->getPost('course_code'))->first();
        if ($existingCourse) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Course code already exists. Please choose a different code.'
            ]);
        }

        $data = [
            'course_code' => $this->request->getPost('course_code'),
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'school_year' => $this->request->getPost('school_year'),
            'semester' => $this->request->getPost('semester'),
            'schedule' => $this->request->getPost('schedule'),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'teacher_id' => $this->request->getPost('teacher_id'),
            'status' => $this->request->getPost('status') ?? 'active',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->courseModel->insert($data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Course created successfully.'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create course.'
            ]);
        }
    }

    /**
     * Get course details for editing
     */
    public function getCourse($id)
    {
        $session = session();
        if (!$session->get('isAuthenticated') || $session->get('userRole') !== 'admin') {
            return $this->response->setJSON(['error' => 'Unauthorized']);
        }

        $course = $this->courseModel->getCourseWithTeacher($id);
        if ($course) {
            return $this->response->setJSON($course);
        } else {
            return $this->response->setJSON(['error' => 'Course not found']);
        }
    }

    /**
     * Teacher course management dashboard
     */
    public function teacherCourses()
    {
        $session = session();
        if (!$session->get('isAuthenticated') || $session->get('userRole') !== 'teacher') {
            return redirect()->to('/login');
        }

        $teacherId = $session->get('userId');
        $courses = $this->courseModel->where('teacher_id', $teacherId)->findAll();

        $data['courses'] = $courses;
        return view('course/teacher', $data);
    }

    /**
     * Create course for teacher (auto-assigns current teacher)
     */
    public function createTeacherCourse()
    {
        $session = session();
        if (!$session->get('isAuthenticated') || $session->get('userRole') !== 'teacher') {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $teacherId = $session->get('userId');
        $startDate = $this->request->getPost('start_date');
        $endDate = $this->request->getPost('end_date');

        // Validate dates
        if (strtotime($startDate) > strtotime($endDate)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Start date cannot be after end date.'
            ]);
        }

        // Check if course code already exists
        $existingCourse = $this->courseModel->where('course_code', $this->request->getPost('course_code'))->first();
        if ($existingCourse) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Course code already exists. Please choose a different code.'
            ]);
        }

        $data = [
            'course_code' => $this->request->getPost('course_code'),
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'school_year' => $this->request->getPost('school_year'),
            'semester' => $this->request->getPost('semester'),
            'schedule' => $this->request->getPost('schedule'),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'teacher_id' => $teacherId, // Auto-assign current teacher
            'status' => $this->request->getPost('status') ?? 'active',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->courseModel->insert($data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Course created successfully.'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create course.'
            ]);
        }
    }
}
