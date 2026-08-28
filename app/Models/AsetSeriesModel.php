<?php

namespace App\Models;

class AsetSeriesModel extends BaseModel
{
    protected string $table = 'aset_series';

    public function getAllWithParent(): array
    {
        return $this->qb($this->table)
            ->select('aset_series.id, aset_series.id_aset, aset_series.merk, aset_series.model, aset_series.kapasitas, aset_series.tahun_perolehan, aset_series.nomor_aset, aset_series.no_seri, aset_series.url_maps, aset_series.kondisi, aset_series.status, aset_series.qr_code, aset_series.id_lokasi, ml.gedung, ml.lantai, ml.nama_ruangan as ruangan, ml.nama_unit as unit, ml.nama_ruangan as lokasi, aset.nama, aset.jenis, aset.kategori')
            ->join('aset', 'aset.id = aset_series.id_aset')
            ->join('master_lokasi ml', 'ml.id = aset_series.id_lokasi', 'left')
            ->orderBy('aset.nama', 'ASC')
            ->orderBy('aset_series.nomor_aset', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getByParent(string $idAset): array
    {
        return $this->qb($this->table)
            ->select('aset_series.id, aset_series.id_aset, aset_series.merk, aset_series.model, aset_series.kapasitas, aset_series.tahun_perolehan, aset_series.nomor_aset, aset_series.no_seri, aset_series.url_maps, aset_series.kondisi, aset_series.status, aset_series.qr_code, aset_series.id_lokasi, ml.gedung, ml.lantai, ml.nama_ruangan as ruangan, ml.nama_unit as unit, ml.nama_ruangan as lokasi')
            ->join('master_lokasi ml', 'ml.id = aset_series.id_lokasi', 'left')
            ->where('id_aset', $idAset)
            ->orderBy('nomor_aset', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getById(string $id): ?array
    {
        $res = $this->qb($this->table)
            ->select('aset_series.id, aset_series.id_aset, aset_series.merk, aset_series.model, aset_series.kapasitas, aset_series.tahun_perolehan, aset_series.nomor_aset, aset_series.no_seri, aset_series.url_maps, aset_series.kondisi, aset_series.status, aset_series.qr_code, aset_series.id_lokasi, ml.gedung, ml.lantai, ml.nama_ruangan as ruangan, ml.nama_unit as unit, ml.nama_ruangan as lokasi')
            ->join('master_lokasi ml', 'ml.id = aset_series.id_lokasi', 'left')
            ->where('aset_series.id', $id)
            ->get()
            ->getRowArray();
        return $res ?: null;
    }
}

