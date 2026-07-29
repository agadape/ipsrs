<?php
define('ENVIRONMENT', 'development');
require 'public/index.php';
$db = \Config\Database::connect();
print_r($db->getFieldNames('riwayat_lokasi_aset'));
