<?php
// Simple test script to check teachers in database
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
chdir(__DIR__);

// Load the bootstrap
require_once 'system/bootstrap.php';

// Get database connection
$db = \Config\Database::connect();

// Check if users table exists and get teachers
try {
    $teachers = $db->table('users')
                   ->where('role', 'teacher')
                   ->where('status', 'active')
                   ->get()
                   ->getResultArray();

    echo "Found " . count($teachers) . " teachers:\n";
    foreach ($teachers as $teacher) {
        echo "- ID: {$teacher['id']}, Name: {$teacher['name']}, Email: {$teacher['email']}\n";
    }

    // Also check all users
    $allUsers = $db->table('users')->get()->getResultArray();
    echo "\nAll users (" . count($allUsers) . "):\n";
    foreach ($allUsers as $user) {
        echo "- {$user['role']}: {$user['name']} ({$user['email']}) - Status: {$user['status']}\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
