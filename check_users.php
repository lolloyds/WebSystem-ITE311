<?php
require_once 'vendor/autoload.php';

try {
    $db = \Config\Database::connect();

    // Check if users table exists
    $tables = $db->listTables();
    echo "Available tables: " . implode(', ', $tables) . PHP_EOL;

    if (in_array('users', $tables)) {
        $users = $db->table('users')->orderBy('created_at', 'DESC')->limit(5)->get()->getResultArray();
        echo 'Found ' . count($users) . ' users:' . PHP_EOL;
        foreach($users as $user) {
            echo '- ' . $user['name'] . ' (' . $user['email'] . ') - ' . $user['role'] . ' - ' . ($user['created_at'] ?? 'No date') . PHP_EOL;
        }
    } else {
        echo "Users table does not exist!" . PHP_EOL;
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}
?>
