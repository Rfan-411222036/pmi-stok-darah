<?php

namespace App\Controllers;

use App\Models\NotificationModel;

class Notifications extends BaseController
{
    protected $notificationModel;

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
    }

    public function index()
    {
        $userId = session()->get('id_user');
        if (!$userId) {
            return redirect()->to('/login');
        }

        $perPage = 20;
        $notes = $this->notificationModel->where('user_id', $userId)
                                         ->orderBy('created_at', 'DESC')
                                         ->paginate($perPage);

        $pager = $this->notificationModel->pager;

        return view('notifications/index', [
            'title' => 'Notifikasi',
            'page_title' => 'Notifikasi Saya',
            'notifications' => $notes,
            'pager' => $pager
        ]);
    }

    public function markRead($id)
    {
        $userId = session()->get('id_user');
        if (!$userId) return redirect()->to('/login');

        $note = $this->notificationModel->find($id);
        if (!$note || $note['user_id'] != $userId) return redirect()->back();

        $this->notificationModel->update($id, ['is_read' => 1]);
        return redirect()->to('/notifications');
    }
}
