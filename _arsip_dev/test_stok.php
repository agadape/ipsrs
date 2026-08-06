<?php
$paths = new Config\Paths();
require $paths->systemDirectory . '/Boot.php';
CodeIgniter\Boot::bootWeb($paths);

$m = new App\Models\StokModel();
$b = $m->getAll()[0];
echo "Before: " . $b['stok_tersedia'] . "\n";
$m->catatTransaksi([
    'id_barang' => $b['id'],
    'nama_barang' => $b['nama'] ?? 'Test',
    'jenis' => 'Keluar',
    'jumlah' => 1
]);
$b2 = $m->getById($b['id']);
echo "After: " . $b2['stok_tersedia'] . "\n";
