<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AssignmentModel;
use App\Models\CourseModel;
use CodeIgniter\HTTP\ResponseInterface;

class Assignment extends BaseController
{
    protected $assignmentModel;
    protected $courseModel;

    public function __construct()
    {
        $this->assignmentModel = new AssignmentModel();
        $this->courseModel = new CourseModel();
    }

    /**
     * Display assignment creation form
     */
    public function createForm($courseId)
    {
        $session = session();
        if (!$session->get('isAuthenticated')) {
            return redirect()->to('/login')->with('error', 'Please login first.');
        }

        $userRole = $session->get('userRole');
        $userId = $session->get('userId');

        // Only admins and teachers can create assignments
        if ($userRole !== 'admin' && $userRole !== 'teacher') {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }

        // Validate course exists
        $course = $this->courseModel->find($courseId);
        if (!$course) {
            return redirect()->to('/courses')->with('error', 'Course not found.');
        }

        // Teachers can only create assignments for their own courses
        if ($userRole === 'teacher' && $course['teacher_id'] != $userId) {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }

        $data = [
            'course' => $course,
            'title' => 'Create Assignment'
        ];

        return view('assignment/create', $data);
    }

    /**
     * Create a new assignment (Admin and Teacher only)
     *
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function create()
    {
        // Check if user is logged in
        $session = session();
        if (!$session->get('isAuthenticated')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You must be logged in to create assignments.'
            ]);
        }

        // Only admins and teachers can create assignments
        $userRole = $session->get('userRole');
        if ($userRole !== 'admin' && $userRole !== 'teacher') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Only administrators and teachers can create assignments.'
            ]);
        }

        // Get POST data
        $courseId = $this->request->getPost('course_id');
        $title = $this->request->getPost('title');
        $description = $this->request->getPost('description');
        $dueDate = $this->request->getPost('due_date');

        // Validate input
        if (empty($courseId) || empty($title) || empty($description)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Course, title, and description are required.'
            ]);
        }

        // Validate course exists and user has permission
        $course = $this->courseModel->find($courseId);
        if (!$course) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid course selected.'
            ]);
        }

        // Teachers can only create assignments for their own courses
        if ($userRole === 'teacher' && $course['teacher_id'] != $session->get('userId')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You can only create assignments for your own courses.'
            ]);
        }

        // Prepare data
        $data = [
            'course_id' => $courseId,
            'title' => trim($title),
            'description' => trim($description),
            'created_by' => $session->get('userId')
        ];

        // Add due date if provided
        if (!empty($dueDate)) {
            $data['due_date'] = $dueDate;
        }

        // Save assignment
        $assignmentId = $this->assignmentModel->insert($data);
        if ($assignmentId) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Assignment created successfully.',
                'assignment_id' => $assignmentId
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create assignment.'
            ]);
        }
    }

    /**
     * Get courses for assignment creation (based on user role)
     */
    public function getCourses()
    {
        $session = session();
        if (!$session->get('isAuthenticated')) {
            return $this->response->setJSON(['error' => 'Unauthorized']);
        }

        $userRole = $session->get('userRole');
        $userId = $session->get('userId');

        if ($userRole === 'admin') {
            // Admin can see all courses
            $courses = $this->courseModel->findAll();
        } elseif ($userRole === 'teacher') {
            // Teacher can only see their courses
            $courses = $this->courseModel->where('teacher_id', $userId)->findAll();
        } else {
            return $this->response->setJSON(['error' => 'Access denied']);
        }

        return $this->response->setJSON(['courses' => $courses]);
    }

    /**
     * View assignment details (for students)
     */
    public function view($id)
    {
        $session = session();
        if (!$session->get('isAuthenticated')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $assignment = $this->assignmentModel->find($id);

        if (!$assignment) {
            return $this->response->setJSON(['success' => false, 'message' => 'Assignment not found']);
        }

        // Check if user has access to this assignment (must be enrolled in the course)
        $userId = $session->get('userId');
        $userRole = $session->get('userRole');

        if ($userRole === 'student') {
            // Check if student is enrolled in the course
            $enrollmentModel = new \App\Models\EnrollmentModel();
            $isEnrolled = $enrollmentModel->isAlreadyEnrolled($userId, $assignment['course_id']);

            if (!$isEnrolled) {
                return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
            }
        } elseif ($userRole === 'teacher') {
            // Check if teacher owns this course
            $course = $this->courseModel->find($assignment['course_id']);
            if (!$course || $course['teacher_id'] != $userId) {
                return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
            }
        }
        // Admin can view all assignments

        return $this->response->setJSON([
            'success' => true,
            'assignment' => $assignment
        ]);
    }

    /**
     * Show full assignment page for students
     */
    public function show($id)
    {
        $session = session();
        if (!$session->get('isAuthenticated')) {
            return redirect()->to('/login')->with('error', 'Please login first.');
        }

        $assignment = $this->assignmentModel->find($id);

        if (!$assignment) {
            return redirect()->to('/dashboard')->with('error', 'Assignment not found.');
        }

        // Check if user has access to this assignment (must be enrolled in the course)
        $userId = $session->get('userId');
        $userRole = $session->get('userRole');

        if ($userRole === 'student') {
            // Check if student is enrolled in the course
            $enrollmentModel = new \App\Models\EnrollmentModel();
            $isEnrolled = $enrollmentModel->isAlreadyEnrolled($userId, $assignment['course_id']);

            if (!$isEnrolled) {
                return redirect()->to('/dashboard')->with('error', 'Access denied.');
            }
        } elseif ($userRole === 'teacher') {
            // Check if teacher owns this course
            $course = $this->courseModel->find($assignment['course_id']);
            if (!$course || $course['teacher_id'] != $userId) {
                return redirect()->to('/dashboard')->with('error', 'Access denied.');
            }
        }
        // Admin can view all assignments

        // Get course information
        $course = $this->courseModel->find($assignment['course_id']);

        $data = [
            'assignment' => $assignment,
            'course' => $course,
            'title' => 'Assignment: ' . $assignment['title']
        ];

        return view('assignment/show', $data);
    }

    /**
     * Get assignments for a student (from enrolled courses)
     */
    public function getStudentAssignments()
    {
        $session = session();
        if (!$session->get('isAuthenticated') || $session->get('userRole') !== 'student') {
            return $this->response->setJSON(['error' => 'Unauthorized']);
        }

        $userId = $session->get('userId');

        // Get enrolled course IDs
        $enrollmentModel = new \App\Models\EnrollmentModel();
        $enrolledCourses = $enrollmentModel->where('user_id', $userId)->findAll();

        if (empty($enrolledCourses)) {
            return $this->response->setJSON(['assignments' => []]);
        }

        $courseIds = array_column($enrolledCourses, 'course_id');

        // Get assignments for enrolled courses
        $assignments = $this->assignmentModel->whereIn('course_id', $courseIds)
                                            ->orderBy('created_at', 'DESC')
                                            ->findAll();

        // Add course names to assignments
        $courseModel = new \App\Models\CourseModel();
        foreach ($assignments as &$assignment) {
            $course = $courseModel->find($assignment['course_id']);
            $assignment['course_name'] = $course ? $course['title'] : 'Unknown Course';
        }

        return $this->response->setJSON(['assignments' => $assignments]);
    }
}
