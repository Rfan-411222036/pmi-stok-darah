<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTeleponToProdusen extends Migration
{
    public function up()
    {
        $fields = [
            'telepon' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'default' => null,
                'after' => 'alamat'
            ]
        ];

        $this->forge->addColumn('produsen', $fields);
    }

    public function down()
    {
        // Drop column if exists
        $this->forge->dropColumn('produsen', 'telepon');
    }
}
