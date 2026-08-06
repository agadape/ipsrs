<?php
$pdo = new PDO("mysql:host=localhost;dbname=ipsrs", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = "
ALTER TABLE aset_series 
  ADD COLUMN merk varchar(100) DEFAULT NULL AFTER id_aset,
  ADD COLUMN model varchar(100) DEFAULT NULL AFTER merk,
  ADD COLUMN kapasitas varchar(50) DEFAULT NULL AFTER model,
  ADD COLUMN tahun_perolehan year DEFAULT NULL AFTER kapasitas,
  ADD COLUMN spesifikasi_tambahan JSON DEFAULT NULL COMMENT \"Atribut domain-spesifik\",
  ADD COLUMN sumber_import varchar(150) DEFAULT NULL COMMENT \"Audit trail\";
";
try { $pdo->exec($sql); echo "Added columns to aset_series.\n"; } catch (Exception $e) { echo $e->getMessage() . "\n"; }

$sql2 = "UPDATE aset_series s JOIN aset a ON s.id_aset = a.id SET s.merk = a.merk, s.model = a.model, s.kapasitas = a.kapasitas, s.tahun_perolehan = a.tahun;";
try { $pdo->exec($sql2); echo "Migrated data to aset_series.\n"; } catch (Exception $e) { echo $e->getMessage() . "\n"; }

$sql3 = "ALTER TABLE aset DROP COLUMN merk, DROP COLUMN model, DROP COLUMN kapasitas, DROP COLUMN tahun;";
try { $pdo->exec($sql3); echo "Dropped columns from aset.\n"; } catch (Exception $e) { echo $e->getMessage() . "\n"; }

$sql4 = "CREATE TABLE IF NOT EXISTS atribut_spesifikasi_definisi (
  id char(36) NOT NULL,
  kategori varchar(100) NOT NULL,
  key_json varchar(50) NOT NULL,
  label varchar(100) NOT NULL,
  tipe_data enum(\"text\",\"number\",\"select\") NOT NULL DEFAULT \"text\",
  satuan varchar(20) DEFAULT NULL,
  urutan int DEFAULT 0,
  PRIMARY KEY (id),
  UNIQUE KEY uk_kategori_key (kategori, key_json)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
try { $pdo->exec($sql4); echo "Created atribut_spesifikasi_definisi.\n"; } catch (Exception $e) { echo $e->getMessage() . "\n"; }

$sql5 = "ALTER TABLE komponen_aset ADD COLUMN jumlah int NOT NULL DEFAULT 1 AFTER nama_komponen, MODIFY COLUMN id_aset_series char(36) NULL;";
try { $pdo->exec($sql5); echo "Modified komponen_aset.\n"; } catch (Exception $e) { echo $e->getMessage() . "\n"; }

