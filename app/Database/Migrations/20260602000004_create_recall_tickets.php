<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRecallTickets extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_recall' => [
                'type'           => 'INT',
                'auto_increment' => true,
            ],
            'id_bag' => [
                'type' => 'INT',
            ],
            'id_produsen' => [
                'type' => 'INT',
            ],
            'reason' => [
                'type'       => 'ENUM',
                'constraint' => ['expiring_soon', 'expired'],
                'default'    => 'expiring_soon',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'swapped', 'destroyed', 'cancelled'],
                'default'    => 'pending',
            ],
            'flagged_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'swapped_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id_recall', true);
        $this->forge->addForeignKey('id_bag', 'stok', 'id_bag', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_produsen', 'produsen', 'id_produsen', 'CASCADE', 'CASCADE');
        $this->forge->createTable('recall_tickets');
    }

    public function down()
    {
        $this->forge->dropTable('recall_tickets');
    }
}
