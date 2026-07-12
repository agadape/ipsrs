<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

$routes->get('/', function () { return redirect()->to('/ipsrs'); });

// Auth
$routes->get('login',  'Auth::login');
$routes->post('login', 'Auth::doLogin');
$routes->get('logout', 'Auth::logout');

$routes->group('ipsrs', function ($routes) {
    // Dashboard
    $routes->get('',          'Dashboard::index');

    // Aset
    $routes->get('aset',           'Aset::index');
    $routes->get('aset/tambah',    'Aset::create');
    $routes->post('aset/tambah',   'Aset::store');
    $routes->get('aset/mutasi',    'Aset::mutasi');
    $routes->post('aset/mutasi',   'Aset::storeMutasi');
    $routes->post('aset/(:segment)/ping',     'Aset::ping/$1');
    $routes->get('aset/(:segment)/qr',        'Aset::qr/$1');
    $routes->get('aset/(:segment)',          'Aset::show/$1');
    $routes->get('aset/(:segment)/edit',     'Aset::edit/$1');
    $routes->post('aset/(:segment)/edit',    'Aset::update/$1');

    // Laporan Kerusakan
    $routes->get('lk',          'LK::index');
    $routes->get('lk/baru',     'LK::create');
    $routes->post('lk/baru',    'LK::store');
    $routes->get('lk/claim/(:segment)',         'LK::claim/$1');
    $routes->get('lk/(:segment)',               'LK::show/$1');
    $routes->post('lk/(:segment)/status',       'LK::updateStatus/$1');
    $routes->post('lk/(:segment)/suku-cadang',  'LK::addSukuCadang/$1');
    $routes->post('lk/(:segment)/vendor',        'LK::storeVendor/$1');

    // Preventif
    $routes->get('preventif',               'Preventif::index');
    $routes->post('preventif/tambah',       'Preventif::store');
    $routes->post('preventif/(:segment)/selesai', 'Preventif::selesai/$1');
    $routes->post('preventif/(:segment)/hapus',   'Preventif::delete/$1');
    $routes->get('preventif/lkp/(:segment)',       'Preventif::lkp/$1');
    $routes->post('preventif/lkp/(:segment)',      'Preventif::simpanLkp/$1');
    $routes->get('preventif/lkp-hasil/(:segment)', 'Preventif::lihatLkp/$1');

    // Stok
    $routes->get('stok',                 'Stok::index');
    $routes->post('stok/tambah-barang',  'Stok::tambahBarang');
    $routes->post('stok/masuk',          'Stok::catatMasuk');
    $routes->post('stok/keluar',         'Stok::catatKeluar');
    $routes->get('stok/riwayat',         'Stok::riwayat');

    // Vendor (master data)
    $routes->get('vendor',                  'Vendor::index');
    $routes->post('vendor/tambah',          'Vendor::store');
    $routes->post('vendor/(:segment)/edit', 'Vendor::update/$1');

    // Pengguna (user management)
    $routes->get('pengguna',                  'Pengguna::index');
    $routes->post('pengguna/tambah',          'Pengguna::tambah');
    $routes->post('pengguna/(:segment)/edit', 'Pengguna::edit/$1');

    // Kategori Aset (master data)
    $routes->get('kategori-aset',                  'KategoriAset::index');
    $routes->post('kategori-aset/tambah',          'KategoriAset::tambah');
    $routes->post('kategori-aset/(:segment)/edit', 'KategoriAset::edit/$1');

    // Kode Kerusakan (master data)
    $routes->get('kode-kerusakan',                  'KodeKerusakan::index');
    $routes->post('kode-kerusakan/tambah',          'KodeKerusakan::tambah');
    $routes->post('kode-kerusakan/(:segment)/edit', 'KodeKerusakan::edit/$1');

    // Kanibal Alat
    $routes->get('kanibal',         'Kanibal::riwayat');
    $routes->post('kanibal',        'Kanibal::store');

    // Laporan
    $routes->get('laporan',              'Laporan::index');
    $routes->get('laporan/export-csv',   'Laporan::exportCsv');
    $routes->get('laporan/export-print', 'Laporan::exportPrint');
    $routes->get('laporan/export-excel-preventif', 'Laporan::exportExcelPreventif');
    $routes->get('laporan/export-print-preventif', 'Laporan::exportPrintPreventif');
});
