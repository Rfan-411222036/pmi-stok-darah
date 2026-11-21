<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MakeReturnLinksNullable extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('return_darah', [
            'id_distribusi' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'default' => null,
            ],
            'id_rs' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'default' => null,
            ],
        ]);
    }

    public function down()
    {
        $hasUnlinkedReturns = $this->db->table('return_darah')
            ->groupStart()
            ->where('id_distribusi', null)
            ->orWhere('id_rs', null)
            ->groupEnd()
            ->countAllResults() > 0;

        if ($hasUnlinkedReturns) {
            throw new \RuntimeException('Cannot require distribution and hospital links while local-stock return records exist.');
        }

        $this->forge->modifyColumn('return_darah', [
            'id_distribusi' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'id_rs' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
        ]);
    }
}
