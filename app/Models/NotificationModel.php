<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'title', 'message', 'created_at', 'is_read'];
    protected $returnType = 'array';
    protected $useTimestamps = false;

    public function notifyPendingRequests(int $userId, array $requests): void
    {
        foreach ($requests as $request) {
            $requestId = (int) $request['id_permintaan'];
            $reference = 'Permintaan #' . $requestId . ' dari ';
            $existing = $this->where('user_id', $userId)
                ->where('title', 'Permintaan Baru Dari RS')
                ->like('message', $reference)
                ->first();

            if ($existing) {
                continue;
            }

            $this->insert([
                'user_id' => $userId,
                'title' => 'Permintaan Baru Dari RS',
                'message' => $reference . ($request['nama_rs'] ?? 'Rumah Sakit')
                    . ' menunggu persetujuan Anda.',
                'is_read' => 0,
                'created_at' => $request['created_at'] ?? date('Y-m-d H:i:s'),
            ]);
        }
    }
}
