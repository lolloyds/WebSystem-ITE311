<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table            = 'courses';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'course_code', 'title', 'description', 'school_year', 'semester',
        'schedule', 'teacher_id', 'start_date', 'end_date', 'status'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Get total number of courses
     */
    public function getTotalCourses()
    {
        return $this->countAll();
    }

    /**
     * Get total number of active courses
     */
    public function getActiveCourses()
    {
        return $this->where('status', 'active')->countAllResults();
    }

    /**
     * Get courses with teacher info for admin dashboard
     */
    public function getCoursesWithTeacher($search = '', $status = '')
    {
        $builder = $this->db->table('courses')
            ->select('courses.*, users.name as teacher_name')
            ->join('users', 'users.id = courses.teacher_id', 'left');

        if (!empty($search)) {
            $builder->groupStart()
                ->like('LOWER(courses.title)', strtolower($search))
                ->orLike('LOWER(courses.course_code)', strtolower($search))
                ->orLike('LOWER(users.name)', strtolower($search))
                ->orLike('LOWER(courses.description)', strtolower($search))
                ->groupEnd();
        }

        if (!empty($status)) {
            $builder->where('courses.status', $status);
        }

        return $builder->orderBy('courses.created_at', 'DESC')->get()->getResultArray();
    }

    /**
     * Get course by ID with teacher info
     */
    public function getCourseWithTeacher($id)
    {
        return $this->db->table('courses')
            ->select('courses.*, users.name as teacher_name')
            ->join('users', 'users.id = courses.teacher_id', 'left')
            ->where('courses.id', $id)
            ->get()
            ->getRowArray();
    }

    /**
     * Get courses by teacher ID
     */
    public function getCoursesByTeacher($teacherId)
    {
        return $this->where('teacher_id', $teacherId)->findAll();
    }

    /**
     * Get teacher course enrollments with student info
     */
    public function getTeacherCourseEnrollments($teacherId)
    {
        return $this->db->table('courses')
            ->select('enrollments.*, users.name as student_name, courses.title as course_name, courses.course_code')
            ->join('enrollments', 'enrollments.course_id = courses.id', 'left')
            ->join('users', 'users.id = enrollments.user_id', 'left')
            ->where('courses.teacher_id', $teacherId)
            ->orderBy('enrollments.enrolled_at', 'DESC')
            ->get()
            ->getResultArray();
    }
}
