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
                'title' => 'Advanced Web Development',
                'description' => 'Master modern web development with HTML5, CSS3, JavaScript frameworks, and responsive design. Build dynamic web applications with cutting-edge technologies.',
                'school_year' => '2025-2026',
                'semester' => '1st Semester',
                'schedule' => 'MWF 9:00-10:30 AM',
                'start_date' => '2025-08-15',
                'end_date' => '2025-12-15',
                'status' => 'active',
                'teacher_id' => $teacher_id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'course_code' => 'DB201',
                'title' => 'Database Systems',
                'description' => 'Learn database design, SQL programming, and data management. Covers relational databases, normalization, indexing, and database administration.',
                'school_year' => '2025-2026',
                'semester' => '1st Semester',
                'schedule' => 'TTH 10:00-11:30 AM',
                'start_date' => '2025-08-15',
                'end_date' => '2025-12-15',
                'status' => 'active',
                'teacher_id' => $teacher_id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'course_code' => 'CS301',
                'title' => 'Computer Science Principles',
                'description' => 'Explore fundamental computer science concepts including algorithms, data structures, programming paradigms, and computational thinking.',
                'school_year' => '2025-2026',
                'semester' => '2nd Semester',
                'schedule' => 'MWF 1:00-2:30 PM',
                'start_date' => '2026-01-15',
                'end_date' => '2026-05-15',
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
