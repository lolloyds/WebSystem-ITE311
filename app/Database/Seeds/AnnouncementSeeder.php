<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'title' => 'Welcome to the Online Student Portal',
                'content' => 'We are excited to welcome you to our new Online Student Portal! This platform provides a comprehensive solution for managing your academic journey, connecting with teachers, and accessing course materials. Please explore all the features available and don\'t hesitate to reach out if you need assistance.',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'title' => 'Fall Semester Registration Now Open',
                'content' => 'Registration for the Fall semester is now open! Students can enroll in courses through the portal until August 15th. Please review the course catalog and meet with your academic advisor to plan your schedule. Early registration ensures you get your preferred courses and time slots.',
                'created_at' => date('Y-m-d H:i:s', strtotime('-3 days'))
            ],
            [
                'title' => 'New Learning Management System Features',
                'content' => 'We have implemented several new features in our Learning Management System including improved mobile responsiveness, enhanced discussion forums, and better course navigation. These updates are designed to provide a more seamless learning experience for all users.',
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 week'))
            ]
        ];

        $this->db->table('announcements')->insertBatch($data);
    }
}
