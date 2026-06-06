<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIsActiveToRumahSakit extends Migration
{
    public function up()
    {
        $fields = [
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'null'       => false,
                'after'      => 'jenis_rs',
            ],
        ];

        $this->forge->addColumn('rumah_sakit', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('rumah_sakit', 'is_active');
    }
}
