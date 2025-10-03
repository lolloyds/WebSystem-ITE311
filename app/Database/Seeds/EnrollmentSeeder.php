<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class EnrollmentSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // Student enrollments (assuming student has ID 2 from UserSeeder)
            [
                'user_id' => 2, // John Student
                'course_id' => 1, // Introduction to Web Development
                'enrolled_at' => date('Y-m-d H:i:s', strtotime('-30 days')),
            ],
            [
                'user_id' => 2, // John Student
                'course_id' => 2, // Advanced PHP Programming
                'enrolled_at' => date('Y-m-d H:i:s', strtotime('-25 days')),
            ],
            [
                'user_id' => 2, // John Student
                'course_id' => 3, // Database Design and Management
                'enrolled_at' => date('Y-m-d H:i:s', strtotime('-20 days')),
            ],
            [
                'user_id' => 2, // John Student
                'course_id' => 4, // Mobile App Development
                'enrolled_at' => date('Y-m-d H:i:s', strtotime('-15 days')),
            ],
            [
                'user_id' => 2, // John Student
                'course_id' => 5, // Cybersecurity Fundamentals
                'enrolled_at' => date('Y-m-d H:i:s', strtotime('-10 days')),
            ],

            // Additional students for more realistic data
            // Let's create some additional users first (we'll need to update UserSeeder)
            // For now, let's assume we have more students with IDs 4, 5, 6, 7, 8
            
            // Student 4 enrollments
            [
                'user_id' => 4,
                'course_id' => 1, // Introduction to Web Development
                'enrolled_at' => date('Y-m-d H:i:s', strtotime('-28 days')),
            ],
            [
                'user_id' => 4,
                'course_id' => 6, // Data Science with Python
                'enrolled_at' => date('Y-m-d H:i:s', strtotime('-22 days')),
            ],
            [
                'user_id' => 4,
                'course_id' => 7, // Cloud Computing and AWS
                'enrolled_at' => date('Y-m-d H:i:s', strtotime('-18 days')),
            ],

            // Student 5 enrollments
            [
                'user_id' => 5,
                'course_id' => 2, // Advanced PHP Programming
                'enrolled_at' => date('Y-m-d H:i:s', strtotime('-26 days')),
            ],
            [
                'user_id' => 5,
                'course_id' => 3, // Database Design and Management
                'enrolled_at' => date('Y-m-d H:i:s', strtotime('-21 days')),
            ],
            [
                'user_id' => 5,
                'course_id' => 8, // Software Engineering Principles
                'enrolled_at' => date('Y-m-d H:i:s', strtotime('-16 days')),
            ],

            // Student 6 enrollments
            [
                'user_id' => 6,
                'course_id' => 1, // Introduction to Web Development
                'enrolled_at' => date('Y-m-d H:i:s', strtotime('-24 days')),
            ],
            [
                'user_id' => 6,
                'course_id' => 4, // Mobile App Development
                'enrolled_at' => date('Y-m-d H:i:s', strtotime('-19 days')),
            ],
            [
                'user_id' => 6,
                'course_id' => 5, // Cybersecurity Fundamentals
                'enrolled_at' => date('Y-m-d H:i:s', strtotime('-14 days')),
            ],

            // Student 7 enrollments
            [
                'user_id' => 7,
                'course_id' => 6, // Data Science with Python
                'enrolled_at' => date('Y-m-d H:i:s', strtotime('-23 days')),
            ],
            [
                'user_id' => 7,
                'course_id' => 7, // Cloud Computing and AWS
                'enrolled_at' => date('Y-m-d H:i:s', strtotime('-17 days')),
            ],
            [
                'user_id' => 7,
                'course_id' => 8, // Software Engineering Principles
                'enrolled_at' => date('Y-m-d H:i:s', strtotime('-12 days')),
            ],

            // Student 8 enrollments
            [
                'user_id' => 8,
                'course_id' => 2, // Advanced PHP Programming
                'enrolled_at' => date('Y-m-d H:i:s', strtotime('-27 days')),
            ],
            [
                'user_id' => 8,
                'course_id' => 3, // Database Design and Management
                'enrolled_at' => date('Y-m-d H:i:s', strtotime('-20 days')),
            ],
            [
                'user_id' => 8,
                'course_id' => 4, // Mobile App Development
                'enrolled_at' => date('Y-m-d H:i:s', strtotime('-13 days')),
            ],
            [
                'user_id' => 8,
                'course_id' => 5, // Cybersecurity Fundamentals
                'enrolled_at' => date('Y-m-d H:i:s', strtotime('-8 days')),
            ],
        ];

        $this->db->table('enrollments')->insertBatch($data);
    }
}
