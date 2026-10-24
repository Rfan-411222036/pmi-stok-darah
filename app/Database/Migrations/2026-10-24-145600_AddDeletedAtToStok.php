<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDeletedAtToStok extends Migration
{
    public function up()
    {
        $this->forge->addColumn('stok', [
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null,
                'after' => 'status'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('stok', 'deleted_at');
    }
}
