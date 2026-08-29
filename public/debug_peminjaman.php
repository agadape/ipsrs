<?php
// A simple standalone script to test the DB query and find the exact error.
ini_set('display_errors', 1);
error_reporting(E_ALL);

define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
require FCPATH . '../app/Config/Paths.php';
\ = new \Config\Paths();
require FCPATH . '../system/bootstrap.php';

\ = \Config\Database::connect();
try {
    \ = \->table("peminjaman_aset");
    \->select("peminjaman_aset.*, aset.nama as nama_aset, aset_series.nomor_aset");
    \->join("aset_series", "aset_series.id = peminjaman_aset.id_aset_series");
    \->join("aset", "aset.id = aset_series.id_aset");
    \->orderBy("peminjaman_aset.status", "ASC");
    \->orderBy("peminjaman_aset.tgl_pinjam", "DESC");
    \ = \->get();
    
    echo "SUCCESS\n";
    print_r(\->getResultArray());
} catch (\Exception \) {
    echo "ERROR: " . \->getMessage();
}
