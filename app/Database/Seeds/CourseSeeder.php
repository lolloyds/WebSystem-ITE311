<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'title' => 'Introduction to Web Development',
                'description' => 'Learn the fundamentals of web development including HTML, CSS, and JavaScript. This course covers basic concepts, best practices, and hands-on projects to build your first website.',
                'teacher_id' => 3, // Jane Teacher
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'Advanced PHP Programming',
                'description' => 'Master PHP programming with advanced concepts including object-oriented programming, database integration, security practices, and modern PHP frameworks.',
                'teacher_id' => 3, // Jane Teacher
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'Database Design and Management',
                'description' => 'Comprehensive course on database design, SQL queries, normalization, indexing, and database administration. Learn MySQL, PostgreSQL, and database optimization techniques.',
                'teacher_id' => 3, // Jane Teacher
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
           
        ];

        $this->db->table('courses')->insertBatch($data);
    }
}
