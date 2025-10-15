<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateBlackListTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'row_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
                'null' => false,
            ],
            'token' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
            ],
            'inserted_at' => [
                'type' => 'TIMESTAMP',
                'null' => false,
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);

        $this->forge->addPrimaryKey('row_id');
        $this->forge->addKey('token');
        $this->forge->addKey('inserted_at');
        $this->forge->createTable('blacklist', true);
    }

    public function down() {
        $this->forge->dropTable('blacklist', true);
    }
}
