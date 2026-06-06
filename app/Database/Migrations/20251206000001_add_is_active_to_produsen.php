<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIsActiveToProdusen extends Migration
{
    public function up()
    {
        $fields = [
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'null'       => false,
                'after'      => 'email',
            ],
        ];

        $this->forge->addColumn('produsen', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('produsen', 'is_active');
    }
}
