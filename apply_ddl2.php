<?php
$pdo = new PDO("mysql:host=localhost;dbname=ipsrs", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = "ALTER TABLE jadwal_preventif MODIFY COLUMN id_aset char(36) DEFAULT NULL, ADD CONSTRAINT fk_jp_aset_series FOREIGN KEY (id_aset) REFERENCES aset_series (id) ON DELETE SET NULL ON UPDATE CASCADE;";
try { $pdo->exec($sql); echo "Added fk to jadwal_preventif.\n"; } catch (Exception $e) { echo $e->getMessage() . "\n"; }

$sql2 = "ALTER TABLE aset ADD CONSTRAINT uk_aset_nama_kategori UNIQUE (nama, kategori);";
try { $pdo->exec($sql2); echo "Added uk to aset.\n"; } catch (Exception $e) { echo $e->getMessage() . "\n"; }

