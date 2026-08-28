-- =================================================================
-- SCRIPT PERBAIKAN ERD & INTEGRITAS DATABASE CMMS IPSRS
-- Eksekusi script ini di tab "SQL" phpMyAdmin cPanel Anda.
-- =================================================================

-- -----------------------------------------------------------------
-- POIN 1: FK Peminjaman & Penghapusan (KRITIS)
-- Menjamin rekam jejak tidak menunjuk ke aset gaib
-- -----------------------------------------------------------------
ALTER TABLE `peminjaman_aset`
  ADD CONSTRAINT `fk_pa_aset_series` FOREIGN KEY (`id_aset_series`) REFERENCES `aset_series` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `penghapusan_aset`
  ADD CONSTRAINT `fk_ph_aset_series` FOREIGN KEY (`id_aset_series`) REFERENCES `aset_series` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;


-- -----------------------------------------------------------------
-- POIN 2 & 3: Status Aset menjadi ENUM + Index (TINGGI)
-- Memaksa State Machine dan mempercepat load data Dashboard
-- -----------------------------------------------------------------
ALTER TABLE `aset_series`
  MODIFY COLUMN `status` ENUM('Tersedia','Dipinjam','Dalam Perbaikan','Rusak Berat','Dihapuskan') NOT NULL DEFAULT 'Tersedia',
  ADD KEY `idx_aset_series_status` (`status`);


-- -----------------------------------------------------------------
-- POIN 6: Komponen Aset Wajib Punya Induk (SEDANG)
-- -----------------------------------------------------------------
-- Hapus (jika ada) komponen yatim piatu sebelum enforcing NOT NULL
DELETE FROM `komponen_aset` WHERE `id_aset_series` IS NULL OR `id_aset_series` = '';

ALTER TABLE `komponen_aset` 
  MODIFY COLUMN `id_aset_series` char(36) NOT NULL;


-- -----------------------------------------------------------------
-- POIN 7A: Akuntabilitas Admin di Dokumen Peminjaman & BA Penghapusan (SEDANG)
-- -----------------------------------------------------------------
ALTER TABLE `peminjaman_aset`
  ADD CONSTRAINT `fk_pa_admin` FOREIGN KEY (`id_admin`) REFERENCES `pengguna` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `penghapusan_aset`
  ADD CONSTRAINT `fk_ph_admin` FOREIGN KEY (`id_admin`) REFERENCES `pengguna` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;


-- -----------------------------------------------------------------
-- POIN 8: Traceability Snapshot Preventif ke Template Master (RENDAH)
-- -----------------------------------------------------------------
ALTER TABLE `detail_checklist_lkp`
  ADD COLUMN `id_template` char(36) DEFAULT NULL COMMENT 'Referensi ke template_checklist.id' AFTER `id_lkp`,
  ADD KEY `idx_dcl_template` (`id_template`),
  ADD CONSTRAINT `fk_dcl_template` FOREIGN KEY (`id_template`) REFERENCES `template_checklist` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;


-- -----------------------------------------------------------------
-- POIN 5: Menegakkan Natural Key Constraints (SEDANG)
-- -----------------------------------------------------------------
-- Abaikan error di bagian ini jika ada data lama yang tidak sinkron.
-- Jika gagal, bersihkan dulu datanya, lalu jalankan baris yang gagal secara terpisah.
ALTER TABLE `aset` 
  ADD CONSTRAINT `fk_aset_kategori` FOREIGN KEY (`kategori`) REFERENCES `kategori_aset` (`nama_kategori`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `laporan_kerusakan` 
  ADD CONSTRAINT `fk_lk_kode_kerusakan` FOREIGN KEY (`kode`) REFERENCES `kode_kerusakan` (`kode`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `riwayat_kanibal` 
  ADD CONSTRAINT `fk_rk_no_order_lk` FOREIGN KEY (`no_order_lk`) REFERENCES `laporan_kerusakan` (`no_order`) ON DELETE RESTRICT ON UPDATE CASCADE;


-- -----------------------------------------------------------------
-- POIN 4: MIGRASI LOKASI (PERSIAPAN MASTER DATA)
-- -----------------------------------------------------------------
-- Tahap 1: Membuat tabel Master Lokasi
CREATE TABLE IF NOT EXISTS `master_lokasi` (
  `id` char(36) NOT NULL,
  `gedung` varchar(100) NOT NULL DEFAULT 'Utama',
  `lantai` varchar(20) DEFAULT NULL,
  `nama_ruangan` varchar(100) NOT NULL,
  `nama_unit` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_master_lokasi` (`gedung`,`lantai`,`nama_ruangan`,`nama_unit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tahap 2: Menarik data unit unik yang ada saat ini ke Master Lokasi
INSERT IGNORE INTO master_lokasi (id, gedung, lantai, nama_ruangan, nama_unit)
SELECT UUID(), 
       COALESCE(NULLIF(gedung, ''), 'Utama'), 
       NULLIF(lantai, ''), 
       COALESCE(NULLIF(ruangan, ''), '-'), 
       COALESCE(NULLIF(unit, ''), '-')
FROM aset_series
GROUP BY gedung, lantai, ruangan, unit;

-- Tahap 3: Menambahkan kolom relasi ke aset_series
ALTER TABLE `aset_series`
  ADD COLUMN `id_lokasi` char(36) DEFAULT NULL AFTER `unit`,
  ADD KEY `idx_aset_series_lokasi` (`id_lokasi`),
  ADD CONSTRAINT `fk_aset_series_lokasi` FOREIGN KEY (`id_lokasi`) REFERENCES `master_lokasi` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- Tahap 4: Mengisi id_lokasi berdasarkan data teks yang ada
UPDATE aset_series a
JOIN master_lokasi m 
  ON COALESCE(NULLIF(a.gedung, ''), 'Utama') = m.gedung 
 AND (a.lantai = m.lantai OR (a.lantai IS NULL AND m.lantai IS NULL) OR (a.lantai = '' AND m.lantai IS NULL))
 AND COALESCE(NULLIF(a.ruangan, ''), '-') = m.nama_ruangan
 AND COALESCE(NULLIF(a.unit, ''), '-') = m.nama_unit
SET a.id_lokasi = m.id;

-- CATATAN POIN 4: 
-- Kolom teks lama (gedung, lantai, ruangan, unit) SENGAJA BELUM DIHAPUS. 
-- Jangan di-DROP dulu sampai aplikasi CodeIgniter di-update untuk membaca dari `master_lokasi`.
