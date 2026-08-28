<?php
namespace App\Controllers;

class Penghapusan extends BaseController
{
    public function index()
    {
        \ = new \App\Models\PenghapusanAsetModel();
        \ = \Config\Database::connect();
        
        \ = \->table('penghapusan_aset p')
            ->select('p.*, a.nama as nama_aset, a.nomor_aset')
            ->join('aset_series a', 'a.id = p.id_aset_series')
            ->orderBy('p.tgl_ba', 'DESC')
            ->get()
            ->getResultArray();

        return \->render('pages/penghapusan/index', [
            'penghapusan' => \
        ]);
    }
}

