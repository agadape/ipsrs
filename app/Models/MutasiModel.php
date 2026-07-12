<?php

namespace App\Models;

class MutasiModel extends BaseModel
{
    protected string $table = 'riwayat_lokasi_aset';

    public function getAll(string $orderBy = 'tanggal'): array
    {
        return $this->qb($this->table)
            ->orderBy('tanggal', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function getByAset(string $idAset): array
    {
        return $this->qb($this->table)
            ->where('id_aset', $idAset)
            ->orderBy('tanggal', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function create(array $data): array
    {
        $row = parent::create($data);

        // Update lokasi aset
        $this->qb('aset')
            ->where('id', $data['id_aset'])
            ->update(['lokasi' => $data['lokasi_tujuan']]);

        return $row;
    }
}
