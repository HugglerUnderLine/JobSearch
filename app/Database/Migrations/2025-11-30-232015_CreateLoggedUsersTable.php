<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLoggedUsersTable extends Migration
{
    public function up() {
    $this->forge->addField([
            'logged_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
                'null' => false,
            ],
            'jwt_token' => [
                'type' => 'VARCHAR',
                'constraint' => 256,
                'null' => false,
            ],
            'user_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => false,
            ],
            'username' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
            ],
            'account_role' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => true,
            ],
            'ip' => [
                'type' => 'VARCHAR',
                'constraint' => 60,
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('logged_id');
        $this->forge->createTable('logged_users', true);
    }

    public function down() {
        $this->forge->dropTable('logged_users', true);
    }
}
