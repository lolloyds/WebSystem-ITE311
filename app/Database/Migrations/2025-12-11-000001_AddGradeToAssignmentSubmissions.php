<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddGradeToAssignmentSubmissions extends Migration
{
    public function up()
    {
        $this->forge->addColumn('assignment_submissions', [
            'grade' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'after' => 'status',
            ],
            'feedback' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'grade',
            ],
            'graded_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'feedback',
            ],
            'graded_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'graded_at',
            ],
        ]);

        // Add foreign key for graded_by
        $this->db->query('ALTER TABLE assignment_submissions ADD CONSTRAINT fk_graded_by FOREIGN KEY (graded_by) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE');
    }

    public function down()
    {
        $this->forge->dropForeignKey('assignment_submissions', 'fk_graded_by');
        $this->forge->dropColumn('assignment_submissions', ['grade', 'feedback', 'graded_at', 'graded_by']);
    }
}
