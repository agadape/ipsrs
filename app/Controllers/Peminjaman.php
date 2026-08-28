<?php
namespace App\Controllers;

class Peminjaman extends BaseController
{
    public function index()
    {
        $model = new \App\Models\PeminjamanAsetModel();
        $db = \Config\Database::connect();
        
        $peminjaman = $db->table("peminjaman_aset p")
            ->select("p.*, parent.nama as nama_aset, a.nomor_aset")
            ->join("aset_series a", "a.id = p.id_aset_series")
            ->join("aset parent", "parent.id = a.id_aset")
            ->orderBy("p.status", "ASC")
            ->orderBy("p.tgl_pinjam", "DESC")
            ->get()
            ->getResultArray();

        return $this->render("pages/peminjaman/index", [
            "peminjaman" => $peminjaman
        ]);
    }
}

