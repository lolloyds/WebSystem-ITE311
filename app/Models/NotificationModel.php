<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $allowedFields = [
        'user_id',
        'message',
        'is_read',
        'created_at',
    ];

    protected $useTimestamps = false; // timestamps handled manually

    /**
     * Get the count of unread notifications for a user
     *
     * @param int $userId User ID
     * @return int Unread notification count
     */
    public function getUnreadCount($userId)
    {
        $result = $this->where('user_id', $userId)
                      ->where('is_read', 0)
                      ->countAllResults();

        return $result;
    }

    /**
     * Get the latest notifications for a user
     *
     * @param int $userId User ID
     * @param int $limit Number of notifications to fetch (default: 5)
     * @return array Array of notifications
     */
    public function getNotificationsForUser($userId, $limit = 5)
    {
        return $this->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Get all unread notifications for a user
     *
     * @param int $userId User ID
     * @return array Array of unread notifications
     */
    public function getUnreadNotifications($userId)
    {
        return $this->where('user_id', $userId)
                    ->where('is_read', 0)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Mark a notification as read
     *
     * @param int $notificationId Notification ID
     * @return bool True on success, false on failure
     */
    public function markAsRead($notificationId)
    {
        return $this->update($notificationId, ['is_read' => 1]);
    }

    /**
     * Create a new notification
     *
     * @param array $data Notification data
     * @return bool|int Insert ID on success, false on failure
     */
    public function createNotification($data)
    {
        // Set created_at timestamp if not provided
        if (!isset($data['created_at'])) {
            $timezone = config('App')->appTimezone ?? 'UTC';
            $dt = new \DateTime('now', new \DateTimeZone($timezone));
            $data['created_at'] = $dt->format(\DateTime::ATOM);
        }

        // Ensure is_read is set to 0 by default
        if (!isset($data['is_read'])) {
            $data['is_read'] = 0;
        }

        return $this->insert($data);
    }
}

