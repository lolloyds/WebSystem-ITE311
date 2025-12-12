<?php
// Simple script to check enrollments
$db = mysqli_connect('localhost', 'root', '', 'lms_doraido');

if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "Connected successfully\n";

// Check if enrollments table exists
$result = mysqli_query($db, "SHOW TABLES LIKE 'enrollments'");
if (mysqli_num_rows($result) == 0) {
    echo "Enrollments table does not exist\n";
    exit;
}

echo "Enrollments table exists\n";

// Check table structure
$result = mysqli_query($db, "DESCRIBE enrollments");
echo "\nEnrollments table structure:\n";
while ($row = mysqli_fetch_assoc($result)) {
    echo "Field: {$row['Field']}, Type: {$row['Type']}, Default: {$row['Default']}\n";
}

// Count total enrollments
$result = mysqli_query($db, "SELECT COUNT(*) as count FROM enrollments");
$row = mysqli_fetch_assoc($result);
echo "Total enrollments: " . $row['count'] . "\n";

// Check courses and their enrollments
$result = mysqli_query($db, "
    SELECT c.id, c.title, c.teacher_id, COUNT(e.id) as enrollment_count
    FROM courses c
    LEFT JOIN enrollments e ON c.id = e.course_id
    GROUP BY c.id, c.title, c.teacher_id
");

echo "\nCourses and their enrollments:\n";
while ($row = mysqli_fetch_assoc($result)) {
    echo "Course ID: {$row['id']}, Title: {$row['title']}, Teacher ID: {$row['teacher_id']}, Enrollments: {$row['enrollment_count']}\n";
}

// Check recent enrollments
$result = mysqli_query($db, "
    SELECT e.*, u.name as student_name, u.role, c.title as course_name
    FROM enrollments e
    JOIN users u ON e.user_id = u.id
    JOIN courses c ON e.course_id = c.id
    ORDER BY e.enrolled_at DESC
    LIMIT 10
");

echo "\nRecent enrollments:\n";
while ($row = mysqli_fetch_assoc($result)) {
    echo "User: {$row['student_name']} (Role: {$row['role']}), Course: {$row['course_name']}, Enrolled: {$row['enrolled_at']}\n";
}

// Check what teacher 15's courses are
$result = mysqli_query($db, "
    SELECT c.id, c.title, c.teacher_id
    FROM courses c
    WHERE c.teacher_id = 15
");

echo "\nTeacher 15's courses:\n";
while ($row = mysqli_fetch_assoc($result)) {
    echo "Course ID: {$row['id']}, Title: {$row['title']}\n";
}

// Check all teachers
$result = mysqli_query($db, "
    SELECT id, name, email, role
    FROM users
    WHERE role = 'teacher'
");

echo "\nAll teachers:\n";
while ($row = mysqli_fetch_assoc($result)) {
    echo "ID: {$row['id']}, Name: {$row['name']}, Email: {$row['email']}\n";
}

// Check what teacher 17's courses are
$result = mysqli_query($db, "
    SELECT c.id, c.title, c.teacher_id, COUNT(e.id) as enrollment_count
    FROM courses c
    LEFT JOIN enrollments e ON c.id = e.course_id
    WHERE c.teacher_id = 17
    GROUP BY c.id, c.title, c.teacher_id
");

echo "\nTeacher 17's courses:\n";
while ($row = mysqli_fetch_assoc($result)) {
    echo "Course ID: {$row['id']}, Title: {$row['title']}, Enrollments: {$row['enrollment_count']}\n";
}

// Fix the status column
echo "\nFixing status column...\n";

// First, update all existing 'active' statuses to 'approved'
$result = mysqli_query($db, "UPDATE `enrollments` SET `status` = 'approved' WHERE `status` = 'active'");
echo "Updated active to approved: " . mysqli_affected_rows($db) . " rows\n";

// Modify the status column to use correct enum values
$result = mysqli_query($db, "ALTER TABLE `enrollments` MODIFY COLUMN `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'approved'");
if ($result) {
    echo "Modified status column enum values successfully\n";
} else {
    echo "Error modifying status column: " . mysqli_error($db) . "\n";
}

// Update any remaining old values that don't match the new enum
$result = mysqli_query($db, "UPDATE `enrollments` SET `status` = 'approved' WHERE `status` NOT IN ('pending', 'approved', 'rejected')");
echo "Updated invalid statuses to approved: " . mysqli_affected_rows($db) . " rows\n";

// Check current enrollment statuses
$result = mysqli_query($db, "
    SELECT e.status, COUNT(*) as count
    FROM enrollments e
    GROUP BY e.status
");

echo "\nCurrent enrollment status distribution:\n";
while ($row = mysqli_fetch_assoc($result)) {
    echo "Status: {$row['status']}, Count: {$row['count']}\n";
}

// Test enrollment model update
echo "\nTesting EnrollmentModel update...\n";
require_once 'app/Config/Database.php';
require_once 'app/Models/EnrollmentModel.php';

$enrollmentModel = new \App\Models\EnrollmentModel();

// Get first enrollment
$firstEnrollment = $enrollmentModel->first();
if ($firstEnrollment) {
    echo "Found enrollment ID: {$firstEnrollment['id']}, current status: {$firstEnrollment['status']}\n";

    // Try to update it
    $newStatus = ($firstEnrollment['status'] === 'approved') ? 'rejected' : 'approved';
    $updateResult = $enrollmentModel->update($firstEnrollment['id'], ['status' => $newStatus]);

    if ($updateResult) {
        echo "Update successful! Changed status to: $newStatus\n";

        // Check if it actually changed
        $updatedEnrollment = $enrollmentModel->find($firstEnrollment['id']);
        echo "Verified status in database: {$updatedEnrollment['status']}\n";

        // Change it back
        $enrollmentModel->update($firstEnrollment['id'], ['status' => $firstEnrollment['status']]);
        echo "Reverted status back to: {$firstEnrollment['status']}\n";
    } else {
        echo "Update failed!\n";
        echo "Last query: " . $enrollmentModel->db->getLastQuery() . "\n";
    }
} else {
    echo "No enrollments found to test with\n";
}

// Check for foreign key constraints
$result = mysqli_query($db, "
    SELECT
        TABLE_NAME,
        COLUMN_NAME,
        CONSTRAINT_NAME,
        REFERENCED_TABLE_NAME,
        REFERENCED_COLUMN_NAME
    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
    WHERE REFERENCED_TABLE_SCHEMA = 'lms_doraido'
    AND REFERENCED_TABLE_NAME = 'enrollments'
");

echo "\nForeign key constraints referencing enrollments table:\n";
$hasConstraints = false;
while ($row = mysqli_fetch_assoc($result)) {
    echo "Table: {$row['TABLE_NAME']}, Column: {$row['COLUMN_NAME']}, Constraint: {$row['CONSTRAINT_NAME']}\n";
    $hasConstraints = true;
}

if (!$hasConstraints) {
    echo "No foreign key constraints found referencing enrollments table\n";
}

// Check assignment_submissions table structure
$result = mysqli_query($db, "DESCRIBE assignment_submissions");
echo "\nAssignment submissions table structure:\n";
while ($row = mysqli_fetch_assoc($result)) {
    echo "Field: {$row['Field']}, Type: {$row['Type']}, Default: {$row['Default']}\n";
}

mysqli_close($db);
?>
