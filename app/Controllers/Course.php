<?php

namespace App\Controllers;

use App\Models\EnrollmentModel;
use CodeIgniter\Controller;

class Course extends BaseController
{
    protected $enrollmentModel;

    public function __construct()
    {
        $this->enrollmentModel = new EnrollmentModel();
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
