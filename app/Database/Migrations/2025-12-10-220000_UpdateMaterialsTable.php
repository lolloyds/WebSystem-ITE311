<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateMaterialsTable extends Migration
{
    public function up()
    {
        // Add new columns
        $this->forge->addColumn('materials', [
            'file_name_original' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'after'      => 'course_id',
            ],
            'file_name_stored' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'after'      => 'file_name_original',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'file_path',
            ],
            'uploaded_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'after'      => 'description',
            ],
            'uploaded_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'uploaded_by',
            ],
        ]);

        // Add foreign key for uploaded_by
        $this->forge->addForeignKey('uploaded_by', 'users', 'id', 'CASCADE', 'CASCADE');

        // Migrate existing data
        $this->db->query("UPDATE materials SET file_name_original = file_name, file_name_stored = file_name WHERE file_name_original IS NULL");
    }

    public function down()
    {
        // Remove foreign key first
        $this->forge->dropForeignKey('materials', 'materials_uploaded_by_foreign');

        // Remove columns
        $this->forge->dropColumn('materials', ['file_name_original', 'file_name_stored', 'description', 'uploaded_by', 'uploaded_at']);
    }
}
