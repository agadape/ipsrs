<?php

namespace App\Models;

use App\Config\IPSRS;

class LKModel extends BaseModel
{
    protected string $table = 'laporan_kerusakan';

    public function getAll(string $orderBy = 'tanggal'): array
    {
        $rows = $this->qb($this->table)
            ->orderBy('tanggal', 'DESC')
            ->orderBy('jam_laporan', 'DESC')
            ->get()
            ->getResultArray();
        return $rows;
    }

    public function getByAset(string $idAset): array
    {
        return $this->qb($this->table)
            ->where('id_aset_series', $idAset)
            ->orderBy('tanggal', 'DESC')
            ->get()
            ->getResultArray();
    }

    // Suku cadang linked to this LK

    public function getSukuCadang(string $lkId): array
    {
        return $this->qb('detail_suku_cadang_lk')
            ->where('id_lk', $lkId)
            ->get()
            ->getResultArray();
    }

    public function addSukuCadang(array $data): array
    {
        if (!isset($data['id'])) {
            $data['id'] = $this->generateUUID();
        }
        $this->qb('detail_suku_cadang_lk')->insert($data);
        return $data;
    }

    public function hasGudangSukuCadang(string $lkId, string $barangId): bool
    {
        return $this->qb('detail_suku_cadang_lk')
            ->where('id_lk', $lkId)
            ->where('id_barang', $barangId)
            ->where('sumber', 'Gudang')
            ->countAllResults() > 0;
    }

    public function getGudangSukuCadang(string $lkId): array
    {
        return $this->qb('detail_suku_cadang_lk')
            ->where('id_lk', $lkId)
            ->where('sumber', 'Gudang')
            ->where('id_barang IS NOT NULL', null, false)
            ->get()
            ->getResultArray();
    }

    // Vendor / Proses III linked to this LK

    public function getVendor(string $lkId): array
    {
        return $this->qb('detail_vendor_lk')
            ->where('id_lk', $lkId)
            ->get()
            ->getResultArray();
    }

    public function addVendor(array $data): array
    {
        if (!isset($data['id'])) {
            $data['id'] = $this->generateUUID();
        }
        $this->qb('detail_vendor_lk')->insert($data);
        return $data;
    }

    public function nextNoOrder(string $column = 'no_order', string $prefix = '', int $padLen = IPSRS::PAD_LK): string
    {
        return parent::nextNoOrder($column, $prefix ?: IPSRS::PREFIX_LK . date('Ym') . '-', $padLen);
    }

    /**
     * Claims an unassigned incoming ticket in one conditional write.
     *
     * The condition prevents two technicians from both succeeding after
     * reading the same unclaimed ticket.
     */
    public function claimAvailable(string $id, string $teknisi): bool
    {
        $this->qb($this->table)
            ->where('id', $id)
            ->where('status', IPSRS::STATUS_LK[0])
            ->groupStart()
                ->where('teknisi', null)
                ->orWhere('teknisi', '')
            ->groupEnd()
            ->update([
                'teknisi' => $teknisi,
                'status'  => IPSRS::STATUS_LK[1],
            ]);
        $this->throwIfError();

        return $this->conn->affectedRows() === 1;
    }
}

