<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJobsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'job_id' => [
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
                'null' => true,
            ],
            'area' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'state' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
            ],
            'city' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
            ],
            'salary' => [
                'type'       => 'NUMERIC',
                'constraint' => '10,2',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('job_id');
        $this->forge->addForeignKey('company_id', 'companies', 'company_id', 'CASCADE', 'CASCADE');
        $this->forge->addKey('title');
        $this->forge->addKey('area');
        $this->forge->addKey('description');
        $this->forge->addKey('state');
        $this->forge->addKey('city');
        $this->forge->addKey('salary');

        $this->forge->createTable('jobs', true);
    }

    public function down() {
        $this->forge->dropTable('jobs', true);
    }
}
