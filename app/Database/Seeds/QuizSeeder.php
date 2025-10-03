<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class QuizSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // Quizzes for Course 1: Introduction to Web Development
            [
                'lesson_id' => 1, // HTML Basics and Structure
                'title' => 'HTML Fundamentals Quiz',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'lesson_id' => 2, // CSS Styling and Layout
                'title' => 'CSS Layout and Styling Quiz',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'lesson_id' => 3, // JavaScript Fundamentals
                'title' => 'JavaScript Basics Quiz',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'lesson_id' => 4, // Responsive Web Design
                'title' => 'Responsive Design Quiz',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],

            // Quizzes for Course 2: Advanced PHP Programming
            [
                'lesson_id' => 5, // Object-Oriented PHP
                'title' => 'PHP OOP Concepts Quiz',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'lesson_id' => 6, // PHP Security Best Practices
                'title' => 'PHP Security Quiz',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'lesson_id' => 7, // PHP Frameworks and MVC
                'title' => 'PHP Frameworks Quiz',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],

            // Quizzes for Course 3: Database Design and Management
            [
                'lesson_id' => 8, // Database Design Principles
                'title' => 'Database Design Quiz',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'lesson_id' => 9, // SQL Queries and Optimization
                'title' => 'SQL Queries Quiz',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'lesson_id' => 10, // Database Administration
                'title' => 'Database Administration Quiz',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],

            // Quizzes for Course 4: Mobile App Development
            [
                'lesson_id' => 11, // React Native Fundamentals
                'title' => 'React Native Quiz',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'lesson_id' => 12, // Flutter Development
                'title' => 'Flutter Development Quiz',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],

            // Quizzes for Course 5: Cybersecurity Fundamentals
            [
                'lesson_id' => 13, // Network Security Basics
                'title' => 'Network Security Quiz',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'lesson_id' => 14, // Cryptography and Encryption
                'title' => 'Cryptography Quiz',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],

            // Quizzes for Course 6: Data Science with Python
            [
                'lesson_id' => 15, // Python for Data Analysis
                'title' => 'Python Data Analysis Quiz',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'lesson_id' => 16, // Machine Learning Basics
                'title' => 'Machine Learning Quiz',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],

            // Quizzes for Course 7: Cloud Computing and AWS
            [
                'lesson_id' => 17, // AWS Core Services
                'title' => 'AWS Services Quiz',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'lesson_id' => 18, // Cloud Architecture Patterns
                'title' => 'Cloud Architecture Quiz',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],

            // Quizzes for Course 8: Software Engineering Principles
            [
                'lesson_id' => 19, // Software Development Lifecycle
                'title' => 'SDLC and Methodologies Quiz',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'lesson_id' => 20, // Version Control with Git
                'title' => 'Git Version Control Quiz',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('quizzes')->insertBatch($data);
    }
}
