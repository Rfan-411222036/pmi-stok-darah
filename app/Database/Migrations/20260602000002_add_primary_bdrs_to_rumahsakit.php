<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPrimaryBdrsToRumahsakit extends Migration
{
    public function up()
    {
        $fields = [
            'id_primary_bdrs' => [
                'type'       => 'INT',
                'null'       => true,
                'default'    => null,
                'after'      => 'is_active',
            ],
        ];

        $this->forge->addColumn('rumah_sakit', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('rumah_sakit', 'id_primary_bdrs');
    }
}
