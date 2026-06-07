<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePermintaan extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_permintaan' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'id_rs' => [ 'type' => 'INT', 'constraint' => 11, 'null' => false ],
            'id_produsen' => [ 'type' => 'INT', 'constraint' => 11, 'null' => false ],
            'jumlah' => [ 'type' => 'INT', 'constraint' => 11, 'null' => false ],
            'gol_dar' => [ 'type' => 'VARCHAR', 'constraint' => 10, 'null' => true ],
            'jenis' => [ 'type' => 'VARCHAR', 'constraint' => 50, 'null' => true ],
            'keterangan' => [ 'type' => 'TEXT', 'null' => true ],
            'status' => [ 'type' => 'VARCHAR', 'constraint' => 20, 'default' => 'pending' ],
            'created_by' => [ 'type' => 'INT', 'constraint' => 11, 'null' => true ],
            'approved_by' => [ 'type' => 'INT', 'constraint' => 11, 'null' => true ],
            'approved_at' => [ 'type' => 'DATETIME', 'null' => true ],
            'created_at' => [ 'type' => 'DATETIME', 'null' => true ],
        ]);

        $this->forge->addKey('id_permintaan', true);
        $this->forge->createTable('permintaan');
    }

    public function down()
    {
        $this->forge->dropTable('permintaan');
    }
}
