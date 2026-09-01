<?php
// fix_mutasi.php
require 'vendor/autoload.php';
$db = \Config\Database::connect();
$db->query("
    UPDATE riwayat_lokasi_aset rla
    JOIN master_lokasi ml ON rla.lokasi_tujuan = ml.id
    SET rla.lokasi_tujuan = COALESCE(ml.nama_ruangan, ml.nama_unit)
    WHERE LENGTH(rla.lokasi_tujuan) = 36
");
echo 'Done';
