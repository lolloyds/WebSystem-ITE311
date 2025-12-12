<?php

namespace App\Controllers;

class Teacher extends BaseController
{
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

        // Verify user role
        $userRole = session()->get('userRole');
        if ($userRole !== 'teacher') {
            session()->setFlashdata('error', 'Access denied: Insufficient permissions.');
            return redirect()->to('/announcements');
        }

        // Get teacher's courses and enrollment information
        $courseModel = new \App\Models\CourseModel();
        $courses = $courseModel->where('teacher_id', $userId)->findAll();

        // Get enrollment statistics for each course
        $enrollmentStats = [];
        $db = \Config\Database::connect();

        // Count unique enrolled students across all teacher's courses
        $uniqueStudentsResult = $db->table('enrollments e')
            ->select('COUNT(DISTINCT e.user_id) as unique_students')
            ->join('courses c', 'c.id = e.course_id')
            ->where('c.teacher_id', $userId)
            ->get()
            ->getRowArray();

        $totalEnrollments = $uniqueStudentsResult['unique_students'] ?? 0;

        // Get enrollment counts per course (total enrollments per course)
        foreach ($courses as $course) {
            $enrollmentCount = $db->table('enrollments')
                ->where('course_id', $course['id'])
                ->countAllResults();

            $enrollmentStats[$course['id']] = $enrollmentCount;
        }

        // Get recent enrollments (last 10)
        $recentEnrollments = $courseModel->getTeacherCourseEnrollments($userId);
        $recentEnrollments = array_slice($recentEnrollments, 0, 10);

        // Get assignment statistics (pending grading)
        $assignmentStats = $this->getAssignmentStats($userId);

        // Get recent activity (submissions, grades, etc.)
        $recentActivity = $this->getRecentActivity($userId);

        // Get upcoming assignments (due soon)
        $upcomingAssignments = $this->getUpcomingAssignments($userId);

        $data = [
            'title' => 'Teacher Dashboard',
            'courses' => $courses,
            'enrollmentStats' => $enrollmentStats,
            'totalEnrollments' => $totalEnrollments,
            'recentEnrollments' => $recentEnrollments,
            'assignmentStats' => $assignmentStats,
            'recentActivity' => $recentActivity,
            'upcomingAssignments' => $upcomingAssignments
        ];

        return view('teacher_dashboard', $data);
    }

    public function manageStudents()
    {
        // Debug logging
        log_message('info', 'Manage Students accessed by user: ' . session()->get('userId') . ', role: ' . session()->get('userRole'));

        // Verify user authentication status
        if (!session()->get('isAuthenticated')) {
            log_message('error', 'User not authenticated for manage students');
            session()->setFlashdata('error', 'Authentication required to access this area.');
            return redirect()->to('/login');
        }

        // Check if user account is still active
        $userId = (int) session()->get('userId');
        $userModel = new \App\Models\UserModel();
        $userRecord = $userModel->find($userId);

        if (!$userRecord) {
            log_message('error', 'User record not found for manage students: ' . $userId);
            session()->destroy();
            session()->setFlashdata('error', 'User account not found.');
            return redirect()->to('/login');
        }

        $userStatus = $userRecord['status'] ?? 'active';
        if ($userStatus !== 'active') {
            log_message('error', 'User account inactive for manage students: ' . $userId);
            session()->destroy();
            session()->setFlashdata('error', 'Your account has been deactivated. Please contact an administrator.');
            return redirect()->to('/login');
        }

        // Verify user role
        $userRole = session()->get('userRole');
        if ($userRole !== 'teacher') {
            log_message('error', 'Access denied for manage students - wrong role: ' . $userRole . ' for user: ' . $userId);
            session()->setFlashdata('error', 'Access denied: Insufficient permissions.');
            return redirect()->to('/announcements');
        }

        log_message('info', 'Manage Students access granted for teacher: ' . $userId);

        // Get teacher's courses for the course selection
        $courseModel = new \App\Models\CourseModel();
        $courses = $courseModel->where('teacher_id', $userId)->findAll();

        log_message('info', 'Found ' . count($courses) . ' courses for teacher: ' . $userId);

        // Get enrolled students for initial display (all enrolled students for this teacher)
        $db = \Config\Database::connect();
        $enrolledStudents = $db->table('enrollments e')
            ->select('u.id, u.student_id, u.name, u.email, u.program, u.year_level, u.section, e.status, e.enrolled_at, c.title as course_name, c.id as course_id, e.id as enrollment_id')
            ->join('users u', 'u.id = e.user_id')
            ->join('courses c', 'c.id = e.course_id')
            ->where('c.teacher_id', $userId)
            ->where('u.role', 'student')
            ->orderBy('u.name')
            ->get()
            ->getResultArray();

        log_message('info', 'Found ' . count($enrolledStudents) . ' enrolled students for teacher: ' . $userId);

        $data = [
            'title' => 'Manage Students',
            'courses' => $courses,
            'enrolledStudents' => $enrolledStudents,
            'studentCount' => count($enrolledStudents)
        ];

        return view('teacher/manage_students', $data);
    }

    public function getStudents()
    {
        // Verify user authentication and role
        if (!session()->get('isAuthenticated') || session()->get('userRole') !== 'teacher') {
            return $this->response->setJSON(['error' => 'Unauthorized']);
        }

        $teacherId = session()->get('userId');
        $courseId = $this->request->getGet('course_id');
        $search = $this->request->getGet('search') ?? '';
        $yearLevel = $this->request->getGet('year_level') ?? '';
        $status = $this->request->getGet('status') ?? '';
        $program = $this->request->getGet('program') ?? '';

        // Build query to get enrolled students for teacher's courses
        $db = \Config\Database::connect();
        $builder = $db->table('enrollments e')
            ->select('u.id, u.student_id, u.name, u.email, u.program, u.year_level, u.section, e.status, e.enrolled_at, c.title as course_name, c.id as course_id')
            ->join('users u', 'u.id = e.user_id')
            ->join('courses c', 'c.id = e.course_id')
            ->where('c.teacher_id', $teacherId)
            ->where('u.role', 'student');

        // Apply filters - course_id is optional, if not provided show all enrolled students
        if (!empty($courseId)) {
            $builder->where('e.course_id', $courseId);
        }
        if (!empty($search)) {
            $builder->groupStart()
                ->like('u.name', $search)
                ->orLike('u.student_id', $search)
                ->orLike('u.email', $search)
                ->groupEnd();
        }
        if (!empty($yearLevel)) {
            $builder->where('u.year_level', $yearLevel);
        }
        if (!empty($status)) {
            $builder->where('e.status', $status); // Changed from u.status to e.status
        }
        if (!empty($program)) {
            $builder->where('u.program', $program);
        }

        // Only group by student if we're filtering by a specific course
        // Otherwise show each enrollment as a separate row
        if (!empty($courseId)) {
            $builder->groupBy('u.id');
        }

        $students = $builder->orderBy('u.name')->get()->getResultArray();

        return $this->response->setJSON(['students' => $students]);
    }

    public function getStudentDetails($studentId)
    {
        // Verify user authentication and role
        if (!session()->get('isAuthenticated') || session()->get('userRole') !== 'teacher') {
            return $this->response->setJSON(['error' => 'Unauthorized']);
        }

        $teacherId = session()->get('userId');
        $userModel = new \App\Models\UserModel();
        $student = $userModel->find($studentId);

        if (!$student || $student['role'] !== 'student') {
            return $this->response->setJSON(['error' => 'Student not found']);
        }

        // Get enrollment details for this teacher's courses (for removal functionality)
        $db = \Config\Database::connect();
        $enrollments = $db->table('enrollments e')
            ->select('e.enrolled_at, c.title as course_name, c.course_code, c.id as course_id, e.id as enrollment_id')
            ->join('courses c', 'c.id = e.course_id')
            ->where('e.user_id', $studentId)
            ->where('c.teacher_id', $teacherId)
            ->get()
            ->getResultArray();

        $student['enrollments'] = $enrollments;

        return $this->response->setJSON($student);
    }

    public function updateStudentStatus()
    {

        // Verify user authentication and role
        if (!session()->get('isAuthenticated') || session()->get('userRole') !== 'teacher') {
            log_message('error', 'Unauthorized access to updateStudentStatus');
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized access']);
        }

        $studentId = $this->request->getPost('student_id');
        $courseId = $this->request->getPost('course_id');
        $newStatus = $this->request->getPost('new_status');
        $remarks = $this->request->getPost('remarks');
        $teacherId = session()->get('userId');

        log_message('info', "updateStudentStatus called - teacher: $teacherId, student: $studentId, course: $courseId, newStatus: $newStatus");

        // Validate required parameters
        if (!$studentId || !$courseId || !$newStatus) {
            log_message('error', "Missing required parameters: student_id=$studentId, course_id=$courseId, new_status=$newStatus");
            return $this->response->setJSON(['success' => false, 'message' => 'Missing required parameters']);
        }

        // For direct status updates (reject/approve), the status should already be the correct enrollment status
        if (in_array($newStatus, ['approved', 'rejected', 'pending'])) {
            $enrollmentStatus = $newStatus;
        } else {
            // Validate status - map UI statuses to enrollment statuses
            $statusMapping = [
                'active' => 'approved',
                'inactive' => 'rejected',
                'dropped' => 'rejected'
            ];

            if (!isset($statusMapping[$newStatus])) {
                log_message('error', "Invalid status provided: $newStatus");
                return $this->response->setJSON(['success' => false, 'message' => 'Invalid status']);
            }

            $enrollmentStatus = $statusMapping[$newStatus];
        }

        log_message('info', "Mapped status $newStatus to enrollment status $enrollmentStatus");

        // Verify the course belongs to this teacher
        $courseModel = new \App\Models\CourseModel();
        $course = $courseModel->where('id', $courseId)->where('teacher_id', $teacherId)->first();

        if (!$course) {
            log_message('error', "Course $courseId does not belong to teacher $teacherId - course not found or wrong teacher");
            return $this->response->setJSON(['success' => false, 'message' => 'Course not found or you do not have permission to modify this course']);
        }

        log_message('info', "Course ownership verified: course $courseId belongs to teacher $teacherId");

        // Check if the student is enrolled in this course
        $enrollmentModel = new \App\Models\EnrollmentModel();
        $enrollment = $enrollmentModel->where('user_id', $studentId)
                                     ->where('course_id', $courseId)
                                     ->first();

        if (!$enrollment) {
            log_message('error', "Student $studentId is not enrolled in course $courseId");
            return $this->response->setJSON(['success' => false, 'message' => "Student $studentId is not enrolled in course $courseId"]);
        }

        log_message('info', "Found enrollment ID: {$enrollment['id']} for student $studentId in course $courseId, current status: {$enrollment['status']}");

        // Update enrollment status
        try {
            log_message('info', "Attempting to update enrollment ID {$enrollment['id']} to status $enrollmentStatus");

            $updateResult = $enrollmentModel->update($enrollment['id'], ['status' => $enrollmentStatus]);

            log_message('info', "Update result: " . ($updateResult ? 'true' : 'false'));

            if ($updateResult) {
                log_message('info', "Successfully updated enrollment ID {$enrollment['id']} to status $enrollmentStatus");

                // Verify the update actually worked
                $verifiedEnrollment = $enrollmentModel->find($enrollment['id']);
                log_message('info', "Verified status in database: " . ($verifiedEnrollment ? $verifiedEnrollment['status'] : 'NOT FOUND'));

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Student enrollment status updated successfully'
                ]);
            } else {
                log_message('error', "Update returned false for enrollment ID {$enrollment['id']}");

                // Get the last query for debugging
                $lastQuery = $enrollmentModel->db->getLastQuery();
                log_message('error', "Last query: " . $lastQuery);

                // Check if there are any database errors
                $dbError = $enrollmentModel->db->error();
                log_message('error', "Database error: " . json_encode($dbError));

                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to update enrollment status in database. DB Error: ' . ($dbError['message'] ?? 'Unknown error')
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', "Exception during status update: " . $e->getMessage());
            log_message('error', "Exception trace: " . $e->getTraceAsString());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
    }

    public function removeStudentFromCourse()
    {
        // Verify user authentication and role
        if (!session()->get('isAuthenticated') || session()->get('userRole') !== 'teacher') {
            log_message('error', 'Unauthorized access to removeStudentFromCourse');
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $studentId = $this->request->getPost('student_id');
        $courseId = $this->request->getPost('course_id');
        $teacherId = session()->get('userId');

        log_message('info', "removeStudentFromCourse called - teacher: $teacherId, student: $studentId, course: $courseId");

        // Verify the course belongs to this teacher
        $courseModel = new \App\Models\CourseModel();
        $course = $courseModel->where('id', $courseId)->where('teacher_id', $teacherId)->first();

        if (!$course) {
            log_message('error', "Course $courseId does not belong to teacher $teacherId");
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized to modify this course']);
        }

        // Check if the student is actually enrolled in this course
        $db = \Config\Database::connect();
        $enrollment = $db->table('enrollments')
            ->where('user_id', $studentId)
            ->where('course_id', $courseId)
            ->get()
            ->getRowArray();

        if (!$enrollment) {
            log_message('error', "Student $studentId is not enrolled in course $courseId");
            return $this->response->setJSON(['success' => false, 'message' => 'Student is not enrolled in this course']);
        }

        log_message('info', "Found enrollment ID: {$enrollment['id']} for student $studentId in course $courseId");

        // Try to delete using the enrollment ID to be more specific
        try {
            $deleteResult = $db->table('enrollments')
                ->where('id', $enrollment['id'])
                ->delete();

            if ($deleteResult) {
                log_message('info', "Successfully deleted enrollment ID {$enrollment['id']}");
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Student removed from course successfully'
                ]);
            } else {
                log_message('error', "Delete query failed for enrollment ID {$enrollment['id']}");
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to remove student from course'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', "Exception during deletion: " . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
    }

    public function getEnrollmentStats()
    {
        // Verify user authentication and role
        if (!session()->get('isAuthenticated') || session()->get('userRole') !== 'teacher') {
            return $this->response->setJSON(['error' => 'Unauthorized']);
        }

        $teacherId = session()->get('userId');

        // Get teacher's courses
        $courseModel = new \App\Models\CourseModel();
        $courses = $courseModel->where('teacher_id', $teacherId)->findAll();

        // Count unique enrolled students across all teacher's courses
        $db = \Config\Database::connect();
        $uniqueStudentsResult = $db->table('enrollments e')
            ->select('COUNT(DISTINCT e.user_id) as unique_students')
            ->join('courses c', 'c.id = e.course_id')
            ->where('c.teacher_id', $teacherId)
            ->get()
            ->getRowArray();

        $totalEnrollments = $uniqueStudentsResult['unique_students'] ?? 0;

        // Get enrollment counts per course (total enrollments per course)
        $enrollmentStats = [];
        foreach ($courses as $course) {
            $enrollmentCount = $db->table('enrollments')
                ->where('course_id', $course['id'])
                ->countAllResults();

            $enrollmentStats[$course['id']] = $enrollmentCount;
        }

        // Get recent enrollments (last 10)
        $recentEnrollments = $courseModel->getTeacherCourseEnrollments($teacherId);
        $recentEnrollments = array_slice($recentEnrollments, 0, 10);

        return $this->response->setJSON([
            'totalEnrollments' => $totalEnrollments,
            'enrollmentStats' => $enrollmentStats,
            'recentEnrollments' => $recentEnrollments,
            'courses' => $courses
        ]);
    }

    /**
     * Get assignment statistics for teacher dashboard
     */
    private function getAssignmentStats($teacherId)
    {
        $db = \Config\Database::connect();

        // Get all assignments for teacher's courses
        $assignments = $db->table('assignments a')
            ->select('a.id, a.title, a.due_date, c.title as course_name')
            ->join('courses c', 'c.id = a.course_id')
            ->where('c.teacher_id', $teacherId)
            ->get()
            ->getResultArray();

        $totalAssignments = count($assignments);
        $pendingGrading = 0;
        $overdueAssignments = 0;
        $upcomingDue = 0;

        foreach ($assignments as $assignment) {
            // Count submissions that need grading
            $ungradedCount = $db->table('assignment_submissions')
                ->where('assignment_id', $assignment['id'])
                ->where('grade IS NULL')
                ->countAllResults();

            $pendingGrading += $ungradedCount;

            // Check if assignment is overdue
            if ($assignment['due_date'] && strtotime($assignment['due_date']) < time()) {
                $overdueAssignments++;
            }

            // Check if due within 7 days
            if ($assignment['due_date'] && strtotime($assignment['due_date']) > time() &&
                strtotime($assignment['due_date']) < strtotime('+7 days')) {
                $upcomingDue++;
            }
        }

        return [
            'totalAssignments' => $totalAssignments,
            'pendingGrading' => $pendingGrading,
            'overdueAssignments' => $overdueAssignments,
            'upcomingDue' => $upcomingDue
        ];
    }

    /**
     * Get recent activity for teacher dashboard
     */
    private function getRecentActivity($teacherId)
    {
        $db = \Config\Database::connect();

        // Get recent submissions (last 10)
        $recentSubmissions = $db->table('assignment_submissions asub')
            ->select('asub.submitted_at, asub.status, a.title as assignment_title, u.name as student_name, c.title as course_name')
            ->join('assignments a', 'a.id = asub.assignment_id')
            ->join('courses c', 'c.id = a.course_id')
            ->join('users u', 'u.id = asub.student_id')
            ->where('c.teacher_id', $teacherId)
            ->orderBy('asub.submitted_at', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        // Get recent grades given (last 10)
        $recentGrades = $db->table('assignment_submissions asub')
            ->select('asub.graded_at, asub.grade, a.title as assignment_title, u.name as student_name, c.title as course_name')
            ->join('assignments a', 'a.id = asub.assignment_id')
            ->join('courses c', 'c.id = a.course_id')
            ->join('users u', 'u.id = asub.student_id')
            ->where('c.teacher_id', $teacherId)
            ->where('asub.grade IS NOT NULL')
            ->orderBy('asub.graded_at', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        // Combine and sort activities
        $activities = [];

        foreach ($recentSubmissions as $submission) {
            $activities[] = [
                'type' => 'submission',
                'message' => $submission['student_name'] . ' submitted "' . $submission['assignment_title'] . '" for ' . $submission['course_name'],
                'timestamp' => $submission['submitted_at'],
                'status' => $submission['status']
            ];
        }

        foreach ($recentGrades as $grade) {
            $activities[] = [
                'type' => 'grade',
                'message' => 'Graded ' . $grade['student_name'] . ' for "' . $grade['assignment_title'] . '" - Score: ' . $grade['grade'],
                'timestamp' => $grade['graded_at'],
                'grade' => $grade['grade']
            ];
        }

        // Sort by timestamp (most recent first)
        usort($activities, function($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });

        return array_slice($activities, 0, 10);
    }

    /**
     * Get upcoming assignments due soon
     */
    private function getUpcomingAssignments($teacherId)
    {
        $db = \Config\Database::connect();

        // Get assignments due within 7 days
        $upcoming = $db->table('assignments a')
            ->select('a.id, a.title, a.due_date, c.title as course_name, c.id as course_id')
            ->join('courses c', 'c.id = a.course_id')
            ->where('c.teacher_id', $teacherId)
            ->where('a.due_date IS NOT NULL')
            ->where('a.due_date >', date('Y-m-d H:i:s'))
            ->where('a.due_date <=', date('Y-m-d H:i:s', strtotime('+7 days')))
            ->orderBy('a.due_date', 'ASC')
            ->limit(5)
            ->get()
            ->getResultArray();

        foreach ($upcoming as &$assignment) {
            // Count total submissions and graded submissions
            $totalSubmissions = $db->table('assignment_submissions')
                ->where('assignment_id', $assignment['id'])
                ->countAllResults();

            $gradedSubmissions = $db->table('assignment_submissions')
                ->where('assignment_id', $assignment['id'])
                ->where('grade IS NOT NULL')
                ->countAllResults();

            $assignment['total_submissions'] = $totalSubmissions;
            $assignment['graded_submissions'] = $gradedSubmissions;
            $assignment['pending_grading'] = $totalSubmissions - $gradedSubmissions;
        }

        return $upcoming;
    }
}
