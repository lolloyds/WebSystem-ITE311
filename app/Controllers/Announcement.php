<?php

namespace App\Controllers;

use App\Models\AnnouncementModel;

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
        if ($announcementModel->insert($data)) {
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
}
