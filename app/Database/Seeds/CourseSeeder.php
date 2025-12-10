<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run()
    {
        // Find teacher by email
        $teacher = $this->db->table('users')
                          ->where('email', 'teacher@example.com')
                          ->get()
                          ->getRowArray();

        if (!$teacher) {
            echo "Teacher not found. Please run UserSeeder first.\n";
            return;
        }

        $teacher_id = $teacher['id'];

        $data = [
            [
                'course_code' => 'WEB101',
                'title' => 'Introduction to Web Development',
                'description' => 'Learn the fundamentals of web development including HTML, CSS, and JavaScript. This course covers basic concepts, best practices, and hands-on projects to build your first website.',
                'school_year' => '2024-2025',
                'semester' => '1st Semester',
                'schedule' => 'MWF 9:00-10:00 AM',
                'start_date' => '2024-08-15',
                'end_date' => '2024-12-15',
                'status' => 'active',
                'teacher_id' => $teacher_id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'course_code' => 'PHP201',
                'title' => 'Advanced PHP Programming',
                'description' => 'Master PHP programming with advanced concepts including object-oriented programming, database integration, security practices, and modern PHP frameworks.',
                'school_year' => '2024-2025',
                'semester' => '2nd Semester',
                'schedule' => 'TTH 2:00-3:30 PM',
                'start_date' => '2025-01-15',
                'end_date' => '2025-05-15',
                'status' => 'active',
                'teacher_id' => $teacher_id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'course_code' => 'DB301',
                'title' => 'Database Design and Management',
                'description' => 'Comprehensive course on database design, SQL queries, normalization, indexing, and database administration. Learn MySQL, PostgreSQL, and database optimization techniques.',
                'school_year' => '2024-2025',
                'semester' => 'Summer',
                'schedule' => 'MWF 10:00-12:00 PM',
                'start_date' => '2025-06-01',
                'end_date' => '2025-07-31',
                'status' => 'inactive',
                'teacher_id' => $teacher_id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'course_code' => 'CS101',
                'title' => 'Computer Science Fundamentals',
                'description' => 'Introduction to computer science concepts including algorithms, data structures, programming paradigms, and computational thinking.',
                'school_year' => '2025-2026',
                'semester' => '1st Semester',
                'schedule' => 'TTH 9:00-10:30 AM',
                'start_date' => '2025-08-15',
                'end_date' => '2025-12-15',
                'status' => 'active',
                'teacher_id' => $teacher_id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        // Use upsert to handle existing records
        $this->db->table('courses')->upsertBatch($data, 'title');
    }
}
