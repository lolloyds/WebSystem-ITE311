<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class LessonSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // Lessons for Course 1: Introduction to Web Development
            [
                'course_id' => 1,
                'title' => 'HTML Basics and Structure',
                'content' => 'Learn the fundamental building blocks of HTML including tags, elements, attributes, and document structure. Practice creating your first web page with proper semantic markup.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'course_id' => 1,
                'title' => 'CSS Styling and Layout',
                'content' => 'Master CSS selectors, properties, and layout techniques. Learn about flexbox, grid, responsive design, and modern CSS features.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'course_id' => 1,
                'title' => 'JavaScript Fundamentals',
                'content' => 'Introduction to JavaScript programming including variables, functions, objects, DOM manipulation, and event handling.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'course_id' => 1,
                'title' => 'Responsive Web Design',
                'content' => 'Learn how to create websites that work perfectly on all devices using responsive design principles and mobile-first approaches.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],

            // Lessons for Course 2: Advanced PHP Programming
            [
                'course_id' => 2,
                'title' => 'Object-Oriented PHP',
                'content' => 'Deep dive into PHP OOP concepts including classes, objects, inheritance, polymorphism, and design patterns.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'course_id' => 2,
                'title' => 'PHP Security Best Practices',
                'content' => 'Learn essential PHP security practices including input validation, SQL injection prevention, XSS protection, and secure authentication.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'course_id' => 2,
                'title' => 'PHP Frameworks and MVC',
                'content' => 'Introduction to popular PHP frameworks like Laravel, CodeIgniter, and Symfony. Learn MVC architecture and modern PHP development.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],

            // Lessons for Course 3: Database Design and Management
            [
                'course_id' => 3,
                'title' => 'Database Design Principles',
                'content' => 'Learn fundamental database design concepts including entity relationships, normalization, and database modeling.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'course_id' => 3,
                'title' => 'SQL Queries and Optimization',
                'content' => 'Master SQL query writing, joins, subqueries, indexing, and performance optimization techniques.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'course_id' => 3,
                'title' => 'Database Administration',
                'content' => 'Learn database administration tasks including backup, recovery, user management, and monitoring.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],

            // Lessons for Course 4: Mobile App Development
            [
                'course_id' => 4,
                'title' => 'React Native Fundamentals',
                'content' => 'Introduction to React Native for cross-platform mobile development. Learn components, navigation, and state management.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'course_id' => 4,
                'title' => 'Flutter Development',
                'content' => 'Learn Flutter framework for building beautiful, natively compiled applications for mobile, web, and desktop.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],

            // Lessons for Course 5: Cybersecurity Fundamentals
            [
                'course_id' => 5,
                'title' => 'Network Security Basics',
                'content' => 'Introduction to network security concepts, protocols, and common vulnerabilities in network infrastructure.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'course_id' => 5,
                'title' => 'Cryptography and Encryption',
                'content' => 'Learn about encryption algorithms, digital signatures, certificates, and cryptographic protocols.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],

            // Lessons for Course 6: Data Science with Python
            [
                'course_id' => 6,
                'title' => 'Python for Data Analysis',
                'content' => 'Learn Python programming specifically for data analysis using pandas, numpy, and matplotlib libraries.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'course_id' => 6,
                'title' => 'Machine Learning Basics',
                'content' => 'Introduction to machine learning concepts, algorithms, and implementation using scikit-learn.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],

            // Lessons for Course 7: Cloud Computing and AWS
            [
                'course_id' => 7,
                'title' => 'AWS Core Services',
                'content' => 'Learn about essential AWS services including EC2, S3, RDS, and VPC configuration.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'course_id' => 7,
                'title' => 'Cloud Architecture Patterns',
                'content' => 'Understand cloud architecture patterns, scalability, and best practices for designing cloud solutions.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],

            // Lessons for Course 8: Software Engineering Principles
            [
                'course_id' => 8,
                'title' => 'Software Development Lifecycle',
                'content' => 'Learn about SDLC phases, methodologies like Agile and Scrum, and project management in software development.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'course_id' => 8,
                'title' => 'Version Control with Git',
                'content' => 'Master Git version control system including branching, merging, collaboration workflows, and best practices.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('lessons')->insertBatch($data);
    }
}
