<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIsReadToNotifications extends Migration
{
    public function up()
    {
        $fields = [
            'is_read' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'null' => false,
                'after' => 'message'
            ]
        ];

        $this->forge->addColumn('notifications', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('notifications', 'is_read');
    }
}
