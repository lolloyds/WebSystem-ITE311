<?php

namespace App\Models;

use CodeIgniter\Model;

class AssignmentSubmissionModel extends Model
{
    protected $table            = 'assignment_submissions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'assignment_id', 'student_id', 'file_path', 'notes', 'status',
        'grade', 'feedback', 'graded_at', 'graded_by'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
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
     * Get submission by assignment and student
     */
    public function getSubmission($assignmentId, $studentId)
    {
        return $this->where('assignment_id', $assignmentId)
                   ->where('student_id', $studentId)
                   ->first();
    }

    /**
     * Get all submissions for an assignment with student details
     */
    public function getSubmissionsWithStudents($assignmentId)
    {
        return $this->select('assignment_submissions.*, users.name as student_name, users.email as student_email')
                   ->join('users', 'users.id = assignment_submissions.student_id')
                   ->where('assignment_id', $assignmentId)
                   ->orderBy('assignment_submissions.created_at', 'ASC')
                   ->findAll();
    }

    /**
     * Get student's submissions with assignment details
     */
    public function getStudentSubmissions($studentId)
    {
        return $this->select('assignment_submissions.*, assignments.title, assignments.due_date, assignments.course_id, courses.title as course_name')
                   ->join('assignments', 'assignments.id = assignment_submissions.assignment_id')
                   ->join('courses', 'courses.id = assignments.course_id')
                   ->where('assignment_submissions.student_id', $studentId)
                   ->orderBy('assignment_submissions.created_at', 'DESC')
                   ->findAll();
    }
}
