<?php

namespace App\Models;

class MutasiModel extends BaseModel
{
    protected string $table = 'riwayat_lokasi_aset';

    public function getAll(string $orderBy = 'tanggal'): array
    {
        return $this->qb($this->table)
            ->select('riwayat_lokasi_aset.*, aset.nama as nama_aset, aset_series.nomor_aset')
            ->join('aset_series', 'aset_series.id = riwayat_lokasi_aset.id_aset_series', 'left')
            ->join('aset', 'aset.id = aset_series.id_aset', 'left')
            ->orderBy('tanggal', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function getByAset(string $idAset): array
    {
        return $this->qb($this->table)
            ->select('riwayat_lokasi_aset.*, aset.nama as nama_aset')
            ->join('aset_series', 'aset_series.id = riwayat_lokasi_aset.id_aset_series', 'left')
            ->join('aset', 'aset.id = aset_series.id_aset', 'left')
            ->where('id_aset_series', $idAset)
            ->orderBy('tanggal', 'DESC')
            ->get()
            ->getResultArray();
    }

}

