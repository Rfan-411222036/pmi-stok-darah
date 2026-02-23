<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'login';
    protected $primaryKey = 'iduser';
    protected $allowedFields = ['email', 'password', 'nama', 'role'];
    protected $useTimestamps = false;

    public function getUserByEmail($email)
    {
        return $this->where('email', $email)->first();
    }

    public function getUsers($search = '', $perPage = 10)
    {
        $builder = $this;
        
        if ($search) {
            $builder->groupStart()
                   ->like('email', $search)
                   ->orLike('nama', $search)
                   ->orLike('role', $search)
                   ->groupEnd();
        }
        
        $builder->orderBy('created_at', 'DESC');
        
        return [
            'users' => $builder->paginate($perPage),
            'pager' => $builder->pager
        ];
    }

    public function getTotalUsers()
    {
        return $this->countAll();
    }

    public function getTotalAdmins()
    {
        return $this->where('role', 'admin')->countAllResults();
    }

    public function getTotalStaff()
    {
        return $this->where('role', 'staff')->countAllResults();
    }
}