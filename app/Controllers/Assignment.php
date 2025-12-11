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
        $this->submissionModel = new \App\Models\AssignmentSubmissionModel();
        helper('url');
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

        // Handle file upload
        $attachmentPath = null;
        if ($this->request->getFile('attachment') && $this->request->getFile('attachment')->isValid()) {
            $file = $this->request->getFile('attachment');

            // Validate file
            $allowedTypes = ['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png'];
            $maxSize = 10 * 1024 * 1024; // 10MB

            if (!$file->isValid()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invalid file uploaded.'
                ]);
            }

            if (!in_array($file->getExtension(), $allowedTypes)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'File type not allowed. Allowed types: ' . implode(', ', $allowedTypes)
                ]);
            }

            if ($file->getSize() > $maxSize) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'File size too large. Maximum size: 10MB.'
                ]);
            }

            // Generate unique filename
            $newName = $file->getRandomName();
            $file->move(ROOTPATH . 'public/uploads/assignments', $newName);
            $attachmentPath = 'uploads/assignments/' . $newName;
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

        // Add attachment if uploaded
        if ($attachmentPath) {
            $data['attachment'] = $attachmentPath;
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

    /**
     * Get assignments for a specific course (with submission status for students)
     */
    public function getCourseAssignments($courseId)
    {
        $session = session();
        if (!$session->get('isAuthenticated') || $session->get('userRole') !== 'student') {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $studentId = $session->get('userId');

        // Check if student is enrolled in the course
        $enrollmentModel = new \App\Models\EnrollmentModel();
        if (!$enrollmentModel->isAlreadyEnrolled($studentId, $courseId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        // Get assignments for the course
        $assignments = $this->assignmentModel->where('course_id', $courseId)
                                           ->orderBy('created_at', 'DESC')
                                           ->findAll();

        // Add submission status for each assignment
        foreach ($assignments as &$assignment) {
            $submission = $this->submissionModel->getSubmission($assignment['id'], $studentId);
            $assignment['submission_status'] = $submission ? ($submission['grade'] ? 'graded' : 'submitted') : 'not_submitted';
            $assignment['grade'] = $submission ? $submission['grade'] : null;
            $assignment['feedback'] = $submission ? $submission['feedback'] : null;
            $assignment['submitted_at'] = $submission ? $submission['submitted_at'] : null;
        }

        return $this->response->setJSON([
            'success' => true,
            'assignments' => $assignments
        ]);
    }

    /**
     * Submit assignment (Student only)
     */
    public function submit()
    {
        $session = session();
        if (!$session->get('isAuthenticated') || $session->get('userRole') !== 'student') {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $assignmentId = $this->request->getPost('assignment_id');
        $studentId = $session->get('userId');

        // Validate assignment exists
        $assignment = $this->assignmentModel->find($assignmentId);
        if (!$assignment) {
            return $this->response->setJSON(['success' => false, 'message' => 'Assignment not found']);
        }

        // Check if student is enrolled in the course
        $enrollmentModel = new \App\Models\EnrollmentModel();
        if (!$enrollmentModel->isAlreadyEnrolled($studentId, $assignment['course_id'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        // Check if already submitted
        $existingSubmission = $this->submissionModel->getSubmission($assignmentId, $studentId);
        if ($existingSubmission) {
            return $this->response->setJSON(['success' => false, 'message' => 'You have already submitted this assignment']);
        }

        // Check if past due date
        $isLate = false;
        if ($assignment['due_date'] && strtotime($assignment['due_date']) < time()) {
            $isLate = true;
        }

        // Handle file upload
        $filePath = null;
        if ($this->request->getFile('file') && $this->request->getFile('file')->isValid()) {
            $file = $this->request->getFile('file');

            // Validate file
            $allowedTypes = ['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png', 'zip', 'rar'];
            $maxSize = 20 * 1024 * 1024; // 20MB

            if (!in_array($file->getExtension(), $allowedTypes)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'File type not allowed. Allowed types: ' . implode(', ', $allowedTypes)
                ]);
            }

            if ($file->getSize() > $maxSize) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'File size too large. Maximum size: 20MB.'
                ]);
            }

            // Generate unique filename
            $newName = $file->getRandomName();
            $file->move(ROOTPATH . 'public/uploads/assignments', $newName);
            $filePath = 'uploads/assignments/' . $newName;
        }

        // Create submission
        $data = [
            'assignment_id' => $assignmentId,
            'student_id' => $studentId,
            'file_path' => $filePath,
            'notes' => $this->request->getPost('notes'),
            'status' => $isLate ? 'late' : 'submitted',
            'submitted_at' => date('Y-m-d H:i:s')
        ];

        $submissionId = $this->submissionModel->insert($data);

        if ($submissionId) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Assignment submitted successfully!'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to submit assignment'
            ]);
        }
    }

    /**
     * Get student's submission for an assignment
     */
    public function getSubmission($assignmentId)
    {
        $session = session();
        if (!$session->get('isAuthenticated') || $session->get('userRole') !== 'student') {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $studentId = $session->get('userId');

        // Validate assignment exists
        $assignment = $this->assignmentModel->find($assignmentId);
        if (!$assignment) {
            return $this->response->setJSON(['success' => false, 'message' => 'Assignment not found']);
        }

        // Check if student is enrolled in the course
        $enrollmentModel = new \App\Models\EnrollmentModel();
        if (!$enrollmentModel->isAlreadyEnrolled($studentId, $assignment['course_id'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $submission = $this->submissionModel->getSubmission($assignmentId, $studentId);

        if ($submission) {
            return $this->response->setJSON([
                'success' => true,
                'submission' => $submission
            ]);
        } else {
            return $this->response->setJSON(['success' => false]);
        }
    }

    /**
     * Get all submissions for an assignment (Teacher/Admin only)
     */
    public function getSubmissions($assignmentId)
    {
        $session = session();
        if (!$session->get('isAuthenticated')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $userRole = $session->get('userRole');
        $userId = $session->get('userId');

        // Only teachers and admins can view submissions
        if ($userRole !== 'teacher' && $userRole !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        // Validate assignment exists
        $assignment = $this->assignmentModel->find($assignmentId);
        if (!$assignment) {
            return $this->response->setJSON(['success' => false, 'message' => 'Assignment not found']);
        }

        // Teachers can only view submissions for their courses
        if ($userRole === 'teacher') {
            $course = $this->courseModel->find($assignment['course_id']);
            if (!$course || $course['teacher_id'] != $userId) {
                return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
            }
        }

        $submissions = $this->submissionModel->getSubmissionsWithStudents($assignmentId);

        return $this->response->setJSON([
            'success' => true,
            'submissions' => $submissions
        ]);
    }

    /**
     * Show grading form for a submission
     */
    public function grade($submissionId)
    {
        $session = session();
        if (!$session->get('isAuthenticated')) {
            return redirect()->to('/login')->with('error', 'Please login first.');
        }

        $userRole = $session->get('userRole');
        $userId = $session->get('userId');

        // Only teachers and admins can grade
        if ($userRole !== 'teacher' && $userRole !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }

        // Get submission with assignment and student details
        $submission = $this->submissionModel->select('assignment_submissions.*, assignments.title as assignment_title, assignments.course_id, users.name as student_name, courses.title as course_title')
                                           ->join('assignments', 'assignments.id = assignment_submissions.assignment_id')
                                           ->join('users', 'users.id = assignment_submissions.student_id')
                                           ->join('courses', 'courses.id = assignments.course_id')
                                           ->find($submissionId);

        if (!$submission) {
            return redirect()->to('/dashboard')->with('error', 'Submission not found.');
        }

        // Teachers can only grade submissions for their courses
        if ($userRole === 'teacher') {
            $course = $this->courseModel->find($submission['course_id']);
            if (!$course || $course['teacher_id'] != $userId) {
                return redirect()->to('/dashboard')->with('error', 'Access denied.');
            }
        }

        $data = [
            'submission' => $submission,
            'title' => 'Grade Assignment: ' . $submission['assignment_title']
        ];

        return view('assignment/grade', $data);
    }

    /**
     * Save grade for a submission
     */
    public function saveGrade()
    {
        $session = session();
        if (!$session->get('isAuthenticated')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $userRole = $session->get('userRole');
        $userId = $session->get('userId');

        // Only teachers and admins can grade
        if ($userRole !== 'teacher' && $userRole !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $submissionId = $this->request->getPost('submission_id');
        $grade = trim($this->request->getPost('grade'));
        $feedback = trim($this->request->getPost('feedback'));

        // Get submission
        $submission = $this->submissionModel->find($submissionId);
        if (!$submission) {
            return $this->response->setJSON(['success' => false, 'message' => 'Submission not found']);
        }

        // Validate teacher has access
        if ($userRole === 'teacher') {
            $assignment = $this->assignmentModel->find($submission['assignment_id']);
            $course = $this->courseModel->find($assignment['course_id']);
            if (!$course || $course['teacher_id'] != $userId) {
                return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
            }
        }

        // Update submission
        $data = [
            'grade' => $grade,
            'feedback' => $feedback,
            'status' => 'graded',
            'graded_at' => date('Y-m-d H:i:s'),
            'graded_by' => $userId
        ];

        $updated = $this->submissionModel->update($submissionId, $data);

        if ($updated) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Grade saved successfully!'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to save grade'
            ]);
        }
    }

    /**
     * Get all grades for a student
     */
    public function getAllGrades()
    {
        $session = session();
        if (!$session->get('isAuthenticated') || $session->get('userRole') !== 'student') {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $studentId = $session->get('userId');

        // Get all graded submissions for the student
        $grades = $this->submissionModel->select('assignment_submissions.*, assignments.title, courses.title as course_name')
                                       ->join('assignments', 'assignments.id = assignment_submissions.assignment_id')
                                       ->join('courses', 'courses.id = assignments.course_id')
                                       ->where('assignment_submissions.student_id', $studentId)
                                       ->where('assignment_submissions.grade IS NOT NULL')
                                       ->orderBy('assignment_submissions.graded_at', 'DESC')
                                       ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'grades' => $grades
        ]);
    }
}
