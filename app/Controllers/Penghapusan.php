<?php
namespace App\Controllers;

class Penghapusan extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        
        $penghapusan = $db->table("penghapusan_aset")
            ->select("penghapusan_aset.*, aset.nama as nama_aset, aset_series.nomor_aset")
            ->join("aset_series", "aset_series.id = penghapusan_aset.id_aset_series")
            ->join("aset", "aset.id = aset_series.id_aset")
            ->orderBy("penghapusan_aset.tgl_ba", "DESC")
            ->get()
            ->getResultArray();

        return $this->render("pages/penghapusan/index", [
            "penghapusan" => $penghapusan
        ]);
    }
}

