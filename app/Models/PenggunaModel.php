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

    public function findByEmail(string $email): ?array
    {
        $row = $this->qb($this->table)
            ->where('email', $email)
            ->where('aktif', 1)
            ->get()
            ->getRowArray();
        return $row ?: null;
    }
}
