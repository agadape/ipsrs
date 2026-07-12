<?php

namespace App\Models;

class RiwayatKanibalModel extends BaseModel
{
    protected string $table = 'riwayat_kanibal';

    public function getAll(string $orderBy = 'created_at'): array
    {
        return $this->qb($this->table)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function getByAset(string $idAset): array
    {
        return $this->qb($this->table)
            ->groupStart()
                ->where('id_aset_donor', $idAset)
                ->orWhere('id_aset_penerima', $idAset)
            ->groupEnd()
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function getByLk(string $idLk): array
    {
        return $this->qb($this->table)
            ->where('id', $idLk)
            ->get()
            ->getResultArray();
    }

    public function getByNoOrder(string $noOrder): array
    {
        return $this->qb($this->table)
            ->where('no_order_lk', $noOrder)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();
    }
}
