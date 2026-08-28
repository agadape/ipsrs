<?php
namespace App\Models;

class PeminjamanAsetModel extends BaseModel
{
    protected string \ = 'peminjaman_aset';

    public function getActiveByAset(string \)
    {
        return \->qb(\->table)
            ->where('id_aset_series', \)
            ->where('status', 'Dipinjam')
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getRowArray();
    }
}

