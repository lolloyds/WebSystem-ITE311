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
                'teacher_id' => 2, // Jane Teacher
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'Advanced PHP Programming',
                'description' => 'Master PHP programming with advanced concepts including object-oriented programming, database integration, security practices, and modern PHP frameworks.',
                'teacher_id' => 2, // Jane Teacher
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'Database Design and Management',
                'description' => 'Comprehensive course on database design, SQL queries, normalization, indexing, and database administration. Learn MySQL, PostgreSQL, and database optimization techniques.',
                'teacher_id' => 2, // Jane Teacher
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'Mobile App Development',
                'description' => 'Build mobile applications using modern frameworks. Learn React Native, Flutter, and native development for iOS and Android platforms.',
                'teacher_id' => 2, // Jane Teacher
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'Cybersecurity Fundamentals',
                'description' => 'Introduction to cybersecurity concepts, threat analysis, network security, encryption, and best practices for protecting digital assets.',
                'teacher_id' => 2, // Jane Teacher
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'Data Science with Python',
                'description' => 'Learn data analysis, machine learning, and statistical modeling using Python. Cover pandas, numpy, scikit-learn, and data visualization libraries.',
                'teacher_id' => 2, // Jane Teacher
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'Cloud Computing and AWS',
                'description' => 'Master cloud computing concepts and Amazon Web Services. Learn EC2, S3, RDS, Lambda, and cloud architecture patterns.',
                'teacher_id' => 2, // Jane Teacher
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'Software Engineering Principles',
                'description' => 'Learn software development methodologies, design patterns, version control, testing, and project management in software engineering.',
                'teacher_id' => 2, // Jane Teacher
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('courses')->insertBatch($data);
    }
}
