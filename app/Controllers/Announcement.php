<?php

namespace App\Controllers;

use App\Models\AnnouncementModel;
use App\Models\NotificationModel;
use App\Models\UserModel;

class Announcement extends BaseController
{
    public function index()
    {
        // Verify user authentication status
        if (!session()->get('isAuthenticated')) {
            session()->setFlashdata('error', 'Authentication required to access this area.');
            return redirect()->to('/login');
        }

        $announcementModel = new AnnouncementModel();
        $announcements = $announcementModel->orderBy('created_at', 'DESC')->findAll();

        $data = [
            'announcements' => $announcements
        ];

        return view('announcements', $data);
    }

    /**
     * Create a new announcement (Admin and Teacher only)
     *
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function create()
    {
        // Check if user is logged in
        $session = session();
        if (!$session->get('isAuthenticated')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You must be logged in to create announcements.'
            ]);
        }

        // Only admins and teachers can create announcements
        $userRole = $session->get('userRole');
        if ($userRole !== 'admin' && $userRole !== 'teacher') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Only administrators and teachers can create announcements.'
            ]);
        }

        // Get POST data
        $title = $this->request->getPost('title');
        $content = $this->request->getPost('content');

        // Validate input
        if (empty($title) || empty($content)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Title and content are required.'
            ]);
        }

        // Prepare data
        $data = [
            'title' => trim($title),
            'content' => trim($content),
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Save announcement
        $announcementModel = new AnnouncementModel();
        $announcementId = $announcementModel->insert($data);

        if ($announcementId) {
            // Send notifications to all teachers and students
            $this->notifyUsers($title);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Announcement created successfully.'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create announcement.'
            ]);
        }
    }

    /**
     * Send notifications to all teachers and students about the new announcement
     *
     * @param string $title The title of the announcement
     */
    private function notifyUsers($title)
    {
        $userModel = new UserModel();
        $notificationModel = new NotificationModel();

        // Get all teachers and students
        $users = $userModel->whereIn('role', ['teacher', 'student'])->findAll();

        // Create notification for each user
        foreach ($users as $user) {
            $notificationData = [
                'user_id' => $user['id'],
                'message' => 'New announcement: ' . $title,
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $notificationModel->createNotification($notificationData);
        }
    }
}
