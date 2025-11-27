<?php

namespace App\Controllers;

use App\Models\EnrollmentModel;
use App\Models\NotificationModel;
use CodeIgniter\Controller;

class Course extends BaseController
{
    protected $enrollmentModel;
    protected $notificationModel;

    public function __construct()
    {
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
            'enrolled_at' => date('Y-m-d H:i:s')
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
                    'created_at' => date('Y-m-d H:i:s')
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
}
