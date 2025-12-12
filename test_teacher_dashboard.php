<?php
// Test teacher dashboard logic
require_once 'app/Config/Database.php';

$db = \Config\Database::connect();

// Simulate teacher ID 15
$userId = 15;

// Get teacher's courses
$courseModel = new \App\Models\CourseModel();
$courses = $courseModel->where('teacher_id', $userId)->findAll();

echo "Teacher ID: $userId\n";
echo "Courses found: " . count($courses) . "\n";

foreach ($courses as $course) {
    echo "Course: {$course['id']} - {$course['title']}\n";
}

// Get enrollment statistics for each course
$enrollmentStats = [];
$totalEnrollments = 0;

foreach ($courses as $course) {
    $enrollmentCount = $db->table('enrollments')
        ->where('course_id', $course['id'])
        ->countAllResults();

    $enrollmentStats[$course['id']] = $enrollmentCount;
    $totalEnrollments += $enrollmentCount;

    echo "Course {$course['id']}: $enrollmentCount enrollments\n";
}

echo "Total enrollments: $totalEnrollments\n";

// Get recent enrollments
$recentEnrollments = $courseModel->getTeacherCourseEnrollments($userId);
echo "Recent enrollments count: " . count($recentEnrollments) . "\n";

foreach ($recentEnrollments as $enrollment) {
    echo "Enrollment: {$enrollment['student_name']} in {$enrollment['course_name']}\n";
}
?>
