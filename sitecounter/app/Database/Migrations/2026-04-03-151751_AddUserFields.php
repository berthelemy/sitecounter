<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUserFields extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'firstname' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'email',
            ],
            'lastname' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'firstname',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', ['firstname', 'lastname']);
    }
}
