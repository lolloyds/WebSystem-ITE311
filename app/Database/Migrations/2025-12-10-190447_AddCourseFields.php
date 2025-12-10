<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCourseFields extends Migration
{
    public function up()
    {
        // Check if columns already exist before adding them
        $columns = ['course_code', 'school_year', 'semester', 'schedule', 'start_date', 'end_date', 'status'];
        $existingColumns = $this->db->getFieldNames('courses');

        $columnsToAdd = [];
        foreach ($columns as $column) {
            if (!in_array($column, $existingColumns)) {
                switch ($column) {
                    case 'course_code':
                        $columnsToAdd[$column] = [
                            'type' => 'VARCHAR',
                            'constraint' => '50',
                            'null' => false,
                            'after' => 'id'
                        ];
                        break;
                    case 'school_year':
                        $columnsToAdd[$column] = [
                            'type' => 'VARCHAR',
                            'constraint' => '20',
                            'null' => false,
                            'after' => 'title'
                        ];
                        break;
                    case 'semester':
                        $columnsToAdd[$column] = [
                            'type' => 'VARCHAR',
                            'constraint' => '20',
                            'null' => false,
                            'after' => 'school_year'
                        ];
                        break;
                    case 'schedule':
                        $columnsToAdd[$column] = [
                            'type' => 'VARCHAR',
                            'constraint' => '100',
                            'null' => false,
                            'after' => 'semester'
                        ];
                        break;
                    case 'start_date':
                        $columnsToAdd[$column] = [
                            'type' => 'DATE',
                            'null' => false,
                            'after' => 'schedule'
                        ];
                        break;
                    case 'end_date':
                        $columnsToAdd[$column] = [
                            'type' => 'DATE',
                            'null' => false,
                            'after' => 'start_date'
                        ];
                        break;
                    case 'status':
                        $columnsToAdd[$column] = [
                            'type' => 'ENUM',
                            'constraint' => ['active', 'inactive'],
                            'default' => 'active',
                            'after' => 'end_date'
                        ];
                        break;
                }
            }
        }

        if (!empty($columnsToAdd)) {
            $this->forge->addColumn('courses', $columnsToAdd);
        }
    }

    public function down()
    {
        $this->forge->dropColumn('courses', ['course_code', 'school_year', 'semester', 'schedule', 'start_date', 'end_date', 'status']);
    }
}
