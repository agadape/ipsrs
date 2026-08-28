<?php
namespace App\Controllers;

class Peminjaman extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        
        $peminjaman = $db->table("peminjaman_aset")
            ->select("peminjaman_aset.*, aset.nama as nama_aset, aset_series.nomor_aset")
            ->join("aset_series", "aset_series.id = peminjaman_aset.id_aset_series")
            ->join("aset", "aset.id = aset_series.id_aset")
            ->orderBy("peminjaman_aset.status", "ASC")
            ->orderBy("peminjaman_aset.tgl_pinjam", "DESC")
            ->get()
            ->getResultArray();

        return $this->render("pages/peminjaman/index", [
            "peminjaman" => $peminjaman
        ]);
    }
}

