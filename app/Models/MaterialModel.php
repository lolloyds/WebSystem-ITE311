<?php

namespace App\Models;

use CodeIgniter\Model;

class MaterialModel extends Model
{
    protected $table = 'materials';
    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $allowedFields = [
        'course_id',
        'file_name',
        'file_path',
        'created_at',
    ];

    protected $useTimestamps = false; // timestamps handled manually

    /**
     * Insert a new material record
     *
     * @param array $data Material data
     * @return bool|int Insert ID on success, false on failure
     */
    public function insertMaterial($data)
    {
        // Set created_at timestamp if not provided
        if (!isset($data['created_at'])) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }

        return $this->insert($data);
    }

    /**
     * Get all materials for a specific course
     *
     * @param int $course_id Course ID
     * @return array Array of materials
     */
    public function getMaterialsByCourse($course_id)
    {
        return $this->where('course_id', $course_id)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get a material by ID
     *
     * @param int $id Material ID
     * @return array|null Material data or null if not found
     */
    public function getMaterialById($id)
    {
        return $this->find($id);
    }

    /**
     * Delete a material by ID
     *
     * @param int $id Material ID
     * @return bool True on success, false on failure
     */
    public function deleteMaterial($id)
    {
        return $this->delete($id);
    }
}
