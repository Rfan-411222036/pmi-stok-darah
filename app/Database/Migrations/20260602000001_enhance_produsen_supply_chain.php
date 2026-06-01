<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EnhanceProdusenSupplyChain extends Migration
{
    public function up()
    {
        $fields = [
            'is_central_hub' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'is_active',
            ],
            'min_threshold' => [
                'type'       => 'INT',
                'default'    => 30,
                'after'      => 'is_central_hub',
            ],
            'priority_order' => [
                'type'       => 'INT',
                'default'    => 0,
                'after'      => 'min_threshold',
            ],
        ];

        $this->forge->addColumn('produsen', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('produsen', ['is_central_hub', 'min_threshold', 'priority_order']);
    }
}
