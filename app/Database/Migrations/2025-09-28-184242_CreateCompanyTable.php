<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCompanyTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'company_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
                'null' => false,
            ],
            'user_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => false,
            ],
            'business' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
            ],
            'street' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
            ],
            'number' => [
                'type' => 'VARCHAR',
                'constraint' => 8,
            ],
            'city' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
            ],
            'state' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
            ],
        ]);

        $this->forge->addPrimaryKey('company_id');
        $this->forge->addForeignKey('user_id', 'users', 'user_id', 'CASCADE', 'RESTRICT');
        $this->forge->addKey('business');
        $this->forge->addKey('state');

        $this->forge->createTable('companies', true);
    }

    public function down() {
        $this->forge->dropTable('companies', true);
    }
}
