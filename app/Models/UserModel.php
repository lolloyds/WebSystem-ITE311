<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $allowedFields = [
        'student_id',
        'name',
        'program',
        'year_level',
        'section',
        'email',
        'password',
        'role',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = false; // timestamps handled by DB defaults in migration
}
