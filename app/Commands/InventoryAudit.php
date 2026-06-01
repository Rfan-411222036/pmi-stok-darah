<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\ProdusenModel;
use App\Models\StokModel;
use App\Models\ReplenishmentModel;

/**
 * Inventory Audit Command
 *
 * Checks each BDRS node's stock against its minimum threshold.
 * If a node is below threshold, a replenishment ticket is auto-generated
 * to request stock from the Central Hub (PMI).
 *
 * Run: php spark inventory:audit
 * Schedule weekly via cron: 0 6 * * 1 php /path/to/spark inventory:audit
 */
class InventoryAudit extends BaseCommand
{
    protected $group       = 'BloodJek';
    protected $name        = 'inventory:audit';
    protected $description = 'Audit BDRS inventory levels and auto-generate replenishment tickets if below threshold.';

    public function run(array $params)
    {
        CLI::write('[Inventory Audit] Starting...', 'yellow');

        $produsenModel      = new ProdusenModel();
        $stokModel          = new StokModel();
        $replenishmentModel = new ReplenishmentModel();

        // Get all active BDRS nodes (non-central hub)
        $nodes = $produsenModel->where('is_active', 1)
                               ->where('is_central_hub', 0)
                               ->findAll();

        if (empty($nodes)) {
            CLI::write('No active BDRS nodes found.', 'red');
            return;
        }

        $ticketsCreated = 0;
        $bloodTypes = [
            ['gol_dar' => 'A',  'rhesus' => '+', 'jenis_darah' => 'Whole'],
            ['gol_dar' => 'A',  'rhesus' => '+', 'jenis_darah' => 'PRC'],
            ['gol_dar' => 'A',  'rhesus' => '-', 'jenis_darah' => 'Whole'],
            ['gol_dar' => 'A',  'rhesus' => '-', 'jenis_darah' => 'PRC'],
            ['gol_dar' => 'B',  'rhesus' => '+', 'jenis_darah' => 'Whole'],
            ['gol_dar' => 'B',  'rhesus' => '+', 'jenis_darah' => 'PRC'],
            ['gol_dar' => 'B',  'rhesus' => '-', 'jenis_darah' => 'Whole'],
            ['gol_dar' => 'B',  'rhesus' => '-', 'jenis_darah' => 'PRC'],
            ['gol_dar' => 'AB', 'rhesus' => '+', 'jenis_darah' => 'Whole'],
            ['gol_dar' => 'AB', 'rhesus' => '+', 'jenis_darah' => 'PRC'],
            ['gol_dar' => 'AB', 'rhesus' => '-', 'jenis_darah' => 'Whole'],
            ['gol_dar' => 'AB', 'rhesus' => '-', 'jenis_darah' => 'PRC'],
            ['gol_dar' => 'O',  'rhesus' => '+', 'jenis_darah' => 'Whole'],
            ['gol_dar' => 'O',  'rhesus' => '+', 'jenis_darah' => 'PRC'],
            ['gol_dar' => 'O',  'rhesus' => '-', 'jenis_darah' => 'Whole'],
            ['gol_dar' => 'O',  'rhesus' => '-', 'jenis_darah' => 'PRC'],
        ];

        foreach ($nodes as $node) {
            $threshold = $node['min_threshold'] ?? 30;
            CLI::write("Auditing: {$node['nama']} (threshold: {$threshold})", 'cyan');

            // Count total available stock at this node (all blood types combined)
            $totalStock = $stokModel->where('id_produsen', $node['id_produsen'])
                                    ->where('status', 'tersedia')
                                    ->where('tanggal_expired >=', date('Y-m-d'))
                                    ->countAllResults();

            if ($totalStock < $threshold) {
                $deficit = $threshold - $totalStock;

                // Skip if a pending ticket already exists for this node (generic)
                if ($replenishmentModel->pendingExists($node['id_produsen'], null, null, null)) {
                    CLI::write("  → Pending ticket already exists, skipping.", 'yellow');
                    continue;
                }

                $replenishmentModel->insert([
                    'id_produsen'     => $node['id_produsen'],
                    'gol_dar'         => null,
                    'rhesus'          => null,
                    'jenis_darah'     => null,
                    'requested_units' => $deficit,
                    'fulfilled_units' => 0,
                    'status'          => 'pending',
                    'notes'           => "Auto-generated: stock {$totalStock} units, threshold {$threshold}, deficit {$deficit} units.",
                    'requested_at'    => date('Y-m-d H:i:s'),
                ]);

                $ticketsCreated++;
                CLI::write("  → Stock: {$totalStock} | Deficit: {$deficit} | Ticket created.", 'red');
            } else {
                CLI::write("  → Stock: {$totalStock} | OK (above threshold).", 'green');
            }
        }

        CLI::write("[Inventory Audit] Done. {$ticketsCreated} replenishment ticket(s) created.", 'green');
    }
}
