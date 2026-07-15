<?php

namespace App\Models;

class PenggunaModel extends BaseModel
{
    protected string $table = 'pengguna';

    public function getAll(string $orderBy = 'nama_lengkap'): array
    {
        return parent::getAll($orderBy);
    }

    public function getActive(string $orderBy = 'nama_lengkap'): array
    {
        return $this->qb($this->table)
            ->where('aktif', 1)
            ->orderBy($orderBy, 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getByRole(string $role, string $orderBy = 'nama_lengkap'): array
    {
        return $this->qb($this->table)
            ->where('role', $role)
            ->where('aktif', 1)
            ->orderBy($orderBy, 'ASC')
            ->get()
            ->getResultArray();
    }

    public function findByEmail(string $email, bool $activeOnly = true): ?array
    {
        $builder = $this->qb($this->table)->where('email', $email);
        
        if ($activeOnly) {
            $builder->where('aktif', 1);
        }
        
        $row = $builder->get()->getRowArray();
        return $row ?: null;
    }
}
