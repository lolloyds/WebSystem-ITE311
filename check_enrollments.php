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

mysqli_close($db);
?>
