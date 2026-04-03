<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInstallTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'installed_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'version' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => false,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('install');
    }

    public function down()
    {
        $this->forge->dropTable('install');
    }
}
