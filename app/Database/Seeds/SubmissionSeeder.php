<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SubmissionSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // Submissions for John Student (user_id: 2)
            [
                'quiz_id' => 1, // HTML Fundamentals Quiz
                'user_id' => 2, // John Student
                'score' => 85,
                'submitted_at' => date('Y-m-d H:i:s', strtotime('-25 days')),
            ],
            [
                'quiz_id' => 2, // CSS Layout and Styling Quiz
                'user_id' => 2, // John Student
                'score' => 92,
                'submitted_at' => date('Y-m-d H:i:s', strtotime('-22 days')),
            ],
            [
                'quiz_id' => 3, // JavaScript Basics Quiz
                'user_id' => 2, // John Student
                'score' => 78,
                'submitted_at' => date('Y-m-d H:i:s', strtotime('-20 days')),
            ],
            [
                'quiz_id' => 5, // PHP OOP Concepts Quiz
                'user_id' => 2, // John Student
                'score' => 88,
                'submitted_at' => date('Y-m-d H:i:s', strtotime('-18 days')),
            ],
            [
                'quiz_id' => 6, // PHP Security Quiz
                'user_id' => 2, // John Student
                'score' => 90,
                'submitted_at' => date('Y-m-d H:i:s', strtotime('-15 days')),
            ],
            [
                'quiz_id' => 8, // Database Design Quiz
                'user_id' => 2, // John Student
                'score' => 82,
                'submitted_at' => date('Y-m-d H:i:s', strtotime('-12 days')),
            ],
            [
                'quiz_id' => 9, // SQL Queries Quiz
                'user_id' => 2, // John Student
                'score' => 95,
                'submitted_at' => date('Y-m-d H:i:s', strtotime('-10 days')),
            ],
            [
                'quiz_id' => 11, // React Native Quiz
                'user_id' => 2, // John Student
                'score' => 87,
                'submitted_at' => date('Y-m-d H:i:s', strtotime('-8 days')),
            ],
            [
                'quiz_id' => 13, // Network Security Quiz
                'user_id' => 2, // John Student
                'score' => 91,
                'submitted_at' => date('Y-m-d H:i:s', strtotime('-5 days')),
            ],

            // Submissions for Student 4
            [
                'quiz_id' => 1, // HTML Fundamentals Quiz
                'user_id' => 4,
                'score' => 90,
                'submitted_at' => date('Y-m-d H:i:s', strtotime('-23 days')),
            ],
            [
                'quiz_id' => 2, // CSS Layout and Styling Quiz
                'user_id' => 4,
                'score' => 85,
                'submitted_at' => date('Y-m-d H:i:s', strtotime('-20 days')),
            ],
            [
                'quiz_id' => 15, // Python Data Analysis Quiz
                'user_id' => 4,
                'score' => 93,
                'submitted_at' => date('Y-m-d H:i:s', strtotime('-16 days')),
            ],
            [
                'quiz_id' => 16, // Machine Learning Quiz
                'user_id' => 4,
                'score' => 89,
                'submitted_at' => date('Y-m-d H:i:s', strtotime('-13 days')),
            ],
            [
                'quiz_id' => 17, // AWS Services Quiz
                'user_id' => 4,
                'score' => 86,
                'submitted_at' => date('Y-m-d H:i:s', strtotime('-10 days')),
            ],

            // Submissions for Student 5
            [
                'quiz_id' => 5, // PHP OOP Concepts Quiz
                'user_id' => 5,
                'score' => 94,
                'submitted_at' => date('Y-m-d H:i:s', strtotime('-21 days')),
            ],
            [
                'quiz_id' => 6, // PHP Security Quiz
                'user_id' => 5,
                'score' => 88,
                'submitted_at' => date('Y-m-d H:i:s', strtotime('-18 days')),
            ],
            [
                'quiz_id' => 8, // Database Design Quiz
                'user_id' => 5,
                'score' => 92,
                'submitted_at' => date('Y-m-d H:i:s', strtotime('-15 days')),
            ],
            [
                'quiz_id' => 9, // SQL Queries Quiz
                'user_id' => 5,
                'score' => 96,
                'submitted_at' => date('Y-m-d H:i:s', strtotime('-12 days')),
            ],
            [
                'quiz_id' => 19, // SDLC and Methodologies Quiz
                'user_id' => 5,
                'score' => 87,
                'submitted_at' => date('Y-m-d H:i:s', strtotime('-9 days')),
            ],

            // Submissions for Student 6
            [
                'quiz_id' => 1, // HTML Fundamentals Quiz
                'user_id' => 6,
                'score' => 83,
                'submitted_at' => date('Y-m-d H:i:s', strtotime('-19 days')),
            ],
            [
                'quiz_id' => 2, // CSS Layout and Styling Quiz
                'user_id' => 6,
                'score' => 89,
                'submitted_at' => date('Y-m-d H:i:s', strtotime('-16 days')),
            ],
            [
                'quiz_id' => 11, // React Native Quiz
                'user_id' => 6,
                'score' => 91,
                'submitted_at' => date('Y-m-d H:i:s', strtotime('-13 days')),
            ],
            [
                'quiz_id' => 12, // Flutter Development Quiz
                'user_id' => 6,
                'score' => 85,
                'submitted_at' => date('Y-m-d H:i:s', strtotime('-10 days')),
            ],
            [
                'quiz_id' => 13, // Network Security Quiz
                'user_id' => 6,
                'score' => 88,
                'submitted_at' => date('Y-m-d H:i:s', strtotime('-7 days')),
            ],

            // Submissions for Student 7
            [
                'quiz_id' => 15, // Python Data Analysis Quiz
                'user_id' => 7,
                'score' => 95,
                'submitted_at' => date('Y-m-d H:i:s', strtotime('-17 days')),
            ],
            [
                'quiz_id' => 16, // Machine Learning Quiz
                'user_id' => 7,
                'score' => 92,
                'submitted_at' => date('Y-m-d H:i:s', strtotime('-14 days')),
            ],
            [
                'quiz_id' => 17, // AWS Services Quiz
                'user_id' => 7,
                'score' => 89,
                'submitted_at' => date('Y-m-d H:i:s', strtotime('-11 days')),
            ],
            [
                'quiz_id' => 18, // Cloud Architecture Quiz
                'user_id' => 7,
                'score' => 93,
                'submitted_at' => date('Y-m-d H:i:s', strtotime('-8 days')),
            ],
            [
                'quiz_id' => 19, // SDLC and Methodologies Quiz
                'user_id' => 7,
                'score' => 87,
                'submitted_at' => date('Y-m-d H:i:s', strtotime('-5 days')),
            ],

            // Submissions for Student 8
            [
                'quiz_id' => 5, // PHP OOP Concepts Quiz
                'user_id' => 8,
                'score' => 86,
                'submitted_at' => date('Y-m-d H:i:s', strtotime('-20 days')),
            ],
            [
                'quiz_id' => 6, // PHP Security Quiz
                'user_id' => 8,
                'score' => 91,
                'submitted_at' => date('Y-m-d H:i:s', strtotime('-17 days')),
            ],
            [
                'quiz_id' => 8, // Database Design Quiz
                'user_id' => 8,
                'score' => 88,
                'submitted_at' => date('Y-m-d H:i:s', strtotime('-14 days')),
            ],
            [
                'quiz_id' => 9, // SQL Queries Quiz
                'user_id' => 8,
                'score' => 94,
                'submitted_at' => date('Y-m-d H:i:s', strtotime('-11 days')),
            ],
            [
                'quiz_id' => 11, // React Native Quiz
                'user_id' => 8,
                'score' => 90,
                'submitted_at' => date('Y-m-d H:i:s', strtotime('-8 days')),
            ],
            [
                'quiz_id' => 12, // Flutter Development Quiz
                'user_id' => 8,
                'score' => 87,
                'submitted_at' => date('Y-m-d H:i:s', strtotime('-5 days')),
            ],
            [
                'quiz_id' => 13, // Network Security Quiz
                'user_id' => 8,
                'score' => 92,
                'submitted_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
            ],
        ];

        $this->db->table('submissions')->insertBatch($data);
    }
}
