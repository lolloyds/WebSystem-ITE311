<?php
require 'app/Config/Database.php';
$db = \Config\Database::connect();
$teachers = $db->table('users')->where('role', 'teacher')->get()->getResultArray();
echo 'Teachers found: ' . count($teachers) . PHP_EOL;
foreach ($teachers as $teacher) {
    echo 'ID: ' . $teacher['id'] . ', Name: ' . $teacher['name'] . ', Email: ' . $teacher['email'] . PHP_EOL;
}
?>
