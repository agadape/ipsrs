<?php
namespace App\Controllers;

class Peminjaman extends BaseController
{
    public function index()
    {
        \ = new \App\Models\PeminjamanAsetModel();
        \ = \Config\Database::connect();
        
        \ = \->table('peminjaman_aset p')
            ->select('p.*, a.nama as nama_aset, a.nomor_aset')
            ->join('aset_series a', 'a.id = p.id_aset_series')
            ->orderBy('p.status', 'ASC')
            ->orderBy('p.tgl_pinjam', 'DESC')
            ->get()
            ->getResultArray();

        return \->render('pages/peminjaman/index', [
            'peminjaman' => \
        ]);
    }
}

