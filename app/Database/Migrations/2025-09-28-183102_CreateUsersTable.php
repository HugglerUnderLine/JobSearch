<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'user_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
                'null' => false,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 150, // Max characters
            ],
            'username' => [
                'type' => 'VARCHAR',
                'constraint' => 20, // Max characters
            ],
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => 256, // Max characters
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 150, // Max characters
            ],
            'phone' => [
                'type' => 'VARCHAR',
                'constraint' => 14, // Max characters
            ],
            'experience' => [
                'type' => 'VARCHAR',
                'constraint' => 600, // Max characters
            ],
            'education' => [
                'type' => 'VARCHAR',
                'constraint' => 600, // Max characters
            ],
            'account_role' => [
                'type' => 'VARCHAR',
                'constraint' => 10, // Max characters
            ],
        ]);

        $this->forge->addPrimaryKey('user_id');
        $this->forge->addKey('name');
        $this->forge->addKey('username');
        $this->forge->addKey('email');
        $this->forge->addKey('account_role');

        $this->forge->createTable('users', true);
    }

    public function down() {
        $this->forge->dropTable('users', true);
    }
}
