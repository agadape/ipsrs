<?php

namespace App\Models;

use App\Config\IPSRS;

class LkpModel extends BaseModel
{
    protected string $table = 'lembar_kerja_preventif';

    public function getByJadwal(string $idJadwal): array
    {
        return $this->qb($this->table)
            ->where('id_jadwal', $idJadwal)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    /** Insert banyak baris detail checklist sekaligus (bulk). */
    public function addDetail(array $rows): void
    {
        if (!empty($rows)) {
            $this->qb('detail_checklist_lkp')->insertBatch($rows);
        }
    }

    public function getDetail(string $idLkp): array
    {
        return $this->qb('detail_checklist_lkp')
            ->where('id_lkp', $idLkp)
            ->orderBy('no_item', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function nextNoOrder(string $column = 'no_order', string $prefix = '', int $padLen = IPSRS::PAD_LKP): string
    {
        return parent::nextNoOrder($column, $prefix ?: IPSRS::PREFIX_LKP . date('Ym') . '-', $padLen);
    }
}
