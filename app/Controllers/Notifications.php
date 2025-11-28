<?php

namespace App\Controllers;

use App\Models\NotificationModel;
use CodeIgniter\RESTful\ResourceController;

class Notifications extends BaseController
{
    protected $notificationModel;

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
    }

    /**
     * Get notifications for the current user
     * Returns JSON response with unread count and list of notifications
     *
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function get()
    {
        // Check if user is logged in
        $session = session();
        if (!$session->get('isAuthenticated')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You must be logged in to view notifications.',
                'count' => 0,
                'notifications' => []
            ]);
        }

        // Get user ID from session
        $userId = $session->get('userId');

        // Get unread count
        $unreadCount = $this->notificationModel->getUnreadCount($userId);

        // Get notifications (limit to 5 latest)
        $notifications = $this->notificationModel->getNotificationsForUser($userId, 5);

        // Normalize timestamps: provide ISO and epoch-ms to avoid client-side parsing issues
        foreach ($notifications as &$n) {
            $created = $n['created_at'] ?? null;
            if ($created) {
                try {
                    $dt = new \DateTime($created);
                    $n['created_at_iso'] = $dt->format(\DateTime::ATOM);
                    // milliseconds for JS
                    $n['created_at_ts'] = (int) $dt->getTimestamp() * 1000;
                } catch (\Exception $e) {
                    // Fallback: keep original and set ts to null
                    $n['created_at_iso'] = $created;
                    $n['created_at_ts'] = null;
                }
            } else {
                $n['created_at_iso'] = null;
                $n['created_at_ts'] = null;
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'count' => $unreadCount,
            'notifications' => $notifications
        ]);
    }

    /**
     * Mark a notification as read
     *
     * @param int $id Notification ID
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function markAsRead($id)
    {
        // Check if user is logged in
        $session = session();
        if (!$session->get('isAuthenticated')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You must be logged in to mark notifications as read.'
            ]);
        }

        // Get user ID from session
        $userId = $session->get('userId');

        // Check if the notification belongs to this user
        $notification = $this->notificationModel->find($id);
        
        if (!$notification) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Notification not found.'
            ]);
        }

        if ($notification['user_id'] != $userId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You do not have permission to mark this notification as read.'
            ]);
        }

        // Mark as read
        $result = $this->notificationModel->markAsRead($id);

        if ($result) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Notification marked as read.'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to mark notification as read.'
            ]);
        }
    }
}

