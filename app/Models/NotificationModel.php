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

    public function notifyLowStock(int $produsenId, string $bloodGroup, ?string $bloodType, int $remaining): int
    {
        $produsen = (new ProdusenModel())->find($produsenId);
        $produsenName = $produsen['nama'] ?? ('BDRS #' . $produsenId);
        $title = 'Stok Rendah: ' . $bloodGroup . ($bloodType ? ' ' . $bloodType : '');
        $message = 'Stok kantong darah ' . $bloodGroup . ($bloodType ? ' ' . $bloodType : '')
            . ' untuk BDRS ' . $produsenName . ' tersisa ' . $remaining
            . ' dan di bawah batas minimal.';

        $users = (new UserModel())->where('role', 'admin')->findAll();
        $recipientIds = array_map('intval', array_column($users, 'id_user'));
        if (!empty($produsen['id_user'])) {
            $bdrsUser = (new UserModel())->find($produsen['id_user']);
            if ($bdrsUser && $bdrsUser['role'] === 'bdrs') {
                $recipientIds[] = (int) $produsen['id_user'];
            }
        }

        $created = 0;
        foreach (array_unique($recipientIds) as $recipientId) {
            $existing = $this->where('user_id', $recipientId)
                ->where('title', $title)
                ->where('message', $message)
                ->first();
            if ($existing) {
                continue;
            }

            $this->insert([
                'user_id' => $recipientId,
                'title' => $title,
                'message' => $message,
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            $created++;
        }

        return $created;
    }

    public function removeCitoForRequest(
        int $requestId,
        int $rsId,
        int $produsenId,
        string $rsName,
        string $produsenName
    ): void {
        $this->where('title', 'Permintaan Cito High')
            ->like('message', 'Permintaan #' . $requestId . ' -')
            ->delete();

        $otherActiveRequest = (new PermintaanModel())
            ->where('id_rs', $rsId)
            ->where('id_produsen', $produsenId)
            ->where('priority_cito', 'high')
            ->whereIn('status', ['pending', 'approved'])
            ->where('id_permintaan !=', $requestId)
            ->first();

        if (!$otherActiveRequest) {
            $legacyMessage = 'Permintaan darah PRIORITAS HIGH dari ' . $rsName . ' untuk '
                . $produsenName . ' membutuhkan perhatian segera.';
            $this->where('title', 'Permintaan Cito High')
                ->where('message', $legacyMessage)
                ->delete();
        }
    }
}
