<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJobOpeningsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'job_opening_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
                'null' => false,
            ],
            'company_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => false,
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
            ],
            'area' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
            ],
            'description' => [
                'type' => 'VARCHAR',
                'constraint' => 4000,
            ],
            'location' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
            ],
            'salary' => [
                'type' => 'NUMERIC',
            ],
        ]);

        $this->forge->addPrimaryKey('job_opening_id');
        $this->forge->addForeignKey('company_id', 'companies', 'company_id', 'CASCADE', 'RESTRICT');
        $this->forge->addKey('title');
        $this->forge->addKey('area');
        $this->forge->addKey('description');
        $this->forge->addKey('location');
        $this->forge->addKey('salary');

        $this->forge->createTable('job_openings', true);
    }

    public function down() {
        $this->forge->dropTable('job_openings', true);
    }
}
