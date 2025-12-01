<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJobApplcationTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'application_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
                'null' => false,
            ],
            'job_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => false,
            ],
            'user_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => false,
            ],
            'user_name' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
            ],
            'user_email' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
            ],
            'user_phone' => [
                'type' => 'VARCHAR',
                'constraint' => 14,
                'null' => true,
            ],
            'user_education' => [
                'type' => 'VARCHAR',
                'constraint' => 600,
                'null' => true,
            ],
            'user_experience' => [
                'type' => 'VARCHAR',
                'constraint' => 600,
                'null' => true,
            ],
            'feedback' => [
                'type' => 'VARCHAR',
                'constraint' => 600,
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('application_id');
        $this->forge->addForeignKey('job_id', 'jobs', 'job_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'user_id', 'CASCADE', 'CASCADE');
        $this->forge->addKey('user_name');
        $this->forge->addKey('user_email');
        $this->forge->addKey('user_phone');
        $this->forge->addKey('user_education');
        $this->forge->addKey('user_experience');
        $this->forge->addKey('feedback');

        $this->forge->createTable('job_applications', true);
    }

    public function down() {
        $this->forge->dropTable('job_applications', true);
    }
}
