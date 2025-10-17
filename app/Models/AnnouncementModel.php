<?php

namespace App\Models;

use CodeIgniter\Model;

class AnnouncementModel extends Model
{
    protected $table = 'announcements';
    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $allowedFields = [
        'title',
        'content',
        'created_at',
    ];

    protected $useTimestamps = false; // We'll handle timestamps manually in the migration
}
