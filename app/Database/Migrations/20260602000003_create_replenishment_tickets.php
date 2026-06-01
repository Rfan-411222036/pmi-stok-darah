<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateReplenishmentTickets extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_replenishment' => [
                'type'           => 'INT',
                'auto_increment' => true,
            ],
            'id_produsen' => [
                'type' => 'INT',
            ],
            'gol_dar' => [
                'type'       => 'VARCHAR',
                'constraint' => 5,
                'null'       => true,
            ],
            'rhesus' => [
                'type'       => 'VARCHAR',
                'constraint' => 2,
                'null'       => true,
            ],
            'jenis_darah' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'requested_units' => [
                'type'    => 'INT',
                'default' => 0,
            ],
            'fulfilled_units' => [
                'type'    => 'INT',
                'default' => 0,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'fulfilled', 'cancelled'],
                'default'    => 'pending',
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'requested_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'fulfilled_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id_replenishment', true);
        $this->forge->addForeignKey('id_produsen', 'produsen', 'id_produsen', 'CASCADE', 'CASCADE');
        $this->forge->createTable('replenishment_tickets');
    }

    public function down()
    {
        $this->forge->dropTable('replenishment_tickets');
    }
}
