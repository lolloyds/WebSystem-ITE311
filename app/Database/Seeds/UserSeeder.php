<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'role' => 'admin',
            ],
            [
                'name' => 'John Student',
                'email' => 'student@example.com',
                'password' => password_hash('student123', PASSWORD_DEFAULT),
                'role' => 'student',
            ],
            [
                'name' => 'Jane Teacher',
                'email' => 'teacher@example.com',
                'password' => password_hash('teacher123', PASSWORD_DEFAULT),
                'role' => 'teacher',
            ],
        ];

        $this->db->table('users')->insertBatch($data);
    }
}
