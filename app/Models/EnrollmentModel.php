<?php

namespace App\Models;

use CodeIgniter\Model;

class EnrollmentModel extends Model
{
    protected $table = 'enrollments';
    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $allowedFields = [
        'user_id',
        'course_id',
        'enrolled_at',
    ];

    protected $useTimestamps = false; // timestamps handled manually

    /**
     * Enroll a user in a course
     *
     * @param array $data Enrollment data
     * @return bool|int Insert ID on success, false on failure
     */
    public function enrollUser($data)
    {
        // Set enrolled_at timestamp if not provided
        if (!isset($data['enrolled_at'])) {
            $timezone = config('App')->appTimezone ?? 'UTC';
            $dt = new \DateTime('now', new \DateTimeZone($timezone));
            $data['enrolled_at'] = $dt->format(\DateTime::ATOM);
        }

        return $this->insert($data);
    }

    /**
     * Get all courses a user is enrolled in
     *
     * @param int $user_id User ID
     * @return array Array of enrolled courses with course details
     */
    public function getUserEnrollments($user_id)
    {
        return $this->db->table('enrollments e')
            ->select('e.*, c.title, c.description, c.created_at as course_created_at')
            ->join('courses c', 'c.id = e.course_id', 'left')
            ->where('e.user_id', $user_id)
            ->orderBy('e.enrolled_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Check if a user is already enrolled in a specific course
     *
     * @param int $user_id User ID
     * @param int $course_id Course ID
     * @return bool True if enrolled, false otherwise
     */
    public function isAlreadyEnrolled($user_id, $course_id)
    {
        $result = $this->where('user_id', $user_id)
                      ->where('course_id', $course_id)
                      ->first();

        return $result !== null;
    }

    /**
     * Get available courses for a user (courses they're not enrolled in)
     *
     * @param int $user_id User ID
     * @return array Array of available courses
     */
    public function getAvailableCourses($user_id)
    {
        // Get enrolled course IDs
        $enrolledCourseIds = $this->where('user_id', $user_id)
                                 ->select('course_id')
                                 ->get()
                                 ->getResultArray();

        $enrolledIds = array_column($enrolledCourseIds, 'course_id');

        // Get courses not in enrolled list
        $builder = $this->db->table('courses');
        
        if (!empty($enrolledIds)) {
            $builder->whereNotIn('id', $enrolledIds);
        }

        return $builder->select('id, title, description, created_at')
                      ->orderBy('created_at', 'DESC')
                      ->get()
                      ->getResultArray();
    }
}
