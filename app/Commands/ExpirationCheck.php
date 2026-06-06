<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\StokModel;
use App\Models\RecallModel;

/**
 * Expiration Check Command
 *
 * Scans all available blood units. Any unit expiring within 14 days
 * (the system's TTL threshold) is flagged and a recall ticket is created.
 *
 * Run: php spark expiration:check
 * Schedule daily via cron: 0 7 * * * php /path/to/spark expiration:check
 */
class ExpirationCheck extends BaseCommand
{
    protected $group       = 'BloodJek';
    protected $name        = 'expiration:check';
    protected $description = 'Flag blood units within 14-day TTL and generate recall tickets.';

    protected const TTL_DAYS = 14;

    public function run(array $params)
    {
        CLI::write('[Expiration Check] Starting (TTL threshold: ' . self::TTL_DAYS . ' days)...', 'yellow');

        $stokModel   = new StokModel();
        $recallModel = new RecallModel();

        $today       = date('Y-m-d');
        $ttlDate     = date('Y-m-d', strtotime('+' . self::TTL_DAYS . ' days'));

        // Find units that are available, not yet expired, but expire within TTL window
        $expiringUnits = $stokModel->where('status', 'tersedia')
                                   ->where('tanggal_expired >=', $today)
                                   ->where('tanggal_expired <=', $ttlDate)
                                   ->findAll();

        // Find units that are already expired but still marked as available
        $expiredUnits = $stokModel->where('status', 'tersedia')
                                  ->where('tanggal_expired <', $today)
                                  ->findAll();

        $flaggedCount = 0;

        foreach ($expiringUnits as $unit) {
            if ($recallModel->pendingExistsForBag($unit['id_bag'])) {
                continue;
            }

            $daysLeft = (int) ceil((strtotime($unit['tanggal_expired']) - strtotime($today)) / 86400);

            $recallModel->insert([
                'id_bag'      => $unit['id_bag'],
                'id_produsen' => $unit['id_produsen'],
                'reason'      => 'expiring_soon',
                'status'      => 'pending',
                'flagged_at'  => date('Y-m-d H:i:s'),
                'notes'       => "Auto-flagged: expires {$unit['tanggal_expired']} ({$daysLeft} day(s) remaining). Swap with fresh stock from Central Hub.",
            ]);

            $flaggedCount++;
            CLI::write("  → Flagged: {$unit['no_kantong']} | {$unit['gol_dar']}{$unit['rhesus']} {$unit['jenis_darah']} | Expires: {$unit['tanggal_expired']} ({$daysLeft}d)", 'yellow');
        }

        foreach ($expiredUnits as $unit) {
            if ($recallModel->pendingExistsForBag($unit['id_bag'])) {
                continue;
            }

            $recallModel->insert([
                'id_bag'      => $unit['id_bag'],
                'id_produsen' => $unit['id_produsen'],
                'reason'      => 'expired',
                'status'      => 'pending',
                'flagged_at'  => date('Y-m-d H:i:s'),
                'notes'       => "Auto-flagged: expired on {$unit['tanggal_expired']}. Must be destroyed.",
            ]);

            $flaggedCount++;
            CLI::write("  → EXPIRED: {$unit['no_kantong']} | {$unit['gol_dar']}{$unit['rhesus']} {$unit['jenis_darah']} | Expired: {$unit['tanggal_expired']}", 'red');
        }

        CLI::write("[Expiration Check] Done. {$flaggedCount} recall ticket(s) created.", 'green');
    }
}
