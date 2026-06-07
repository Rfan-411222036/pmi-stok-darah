<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIdProdusenToDistribusi extends Migration
{
    public function up()
    {
        $fields = [
            'id_produsen' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'unsigned' => true,
                'after' => 'id_rs'
            ]
        ];

        $this->forge->addColumn('distribusi', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('distribusi', 'id_produsen');
    }
}
