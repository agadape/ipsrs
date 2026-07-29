-- =========================================================================
-- MIGRATION SCRIPT: MEMBUAT ASET MENJADI PARENT-CHILD (SERIES)
-- =========================================================================

-- 1. Buat Tabel aset_series
CREATE TABLE IF NOT EXISTS `aset_series` (
    `id` CHAR(36) NOT NULL,
    `id_aset` CHAR(36) NOT NULL COMMENT 'Relasi ke tabel aset (Parent)',
    `nomor_aset` VARCHAR(50) NOT NULL COMMENT 'Dipindah dari parent',
    `no_seri` VARCHAR(100),
    `lokasi` VARCHAR(200) NOT NULL,
    `gedung` VARCHAR(100) NOT NULL,
    `lantai` VARCHAR(20),
    `ruangan` VARCHAR(100) NOT NULL,
    `unit` VARCHAR(100) NOT NULL,
    `url_maps` TEXT,
    `kondisi` VARCHAR(50) NOT NULL DEFAULT 'Baik',
    `status` VARCHAR(50) NOT NULL DEFAULT 'Aktif',
    `qr_code` VARCHAR(255),
    `last_seen_at` TIMESTAMP NULL,
    `last_seen_lat` DOUBLE,
    `last_seen_lng` DOUBLE,
    `last_seen_by` VARCHAR(100),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_aset_series_nomor` (`nomor_aset`),
    KEY `idx_aset_series_parent` (`id_aset`),
    CONSTRAINT `fk_aset_series_parent` FOREIGN KEY (`id_aset`) REFERENCES `aset` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Pindahkan data unik fisik ke aset_series (Migration)
INSERT INTO `aset_series` 
(`id`, `id_aset`, `nomor_aset`, `no_seri`, `lokasi`, `gedung`, `lantai`, `ruangan`, `unit`, `kondisi`, `status`, `last_seen_at`, `last_seen_lat`, `last_seen_lng`, `last_seen_by`)
SELECT 
    UUID(), `id`, `nomor_aset`, `no_seri`, `lokasi`, `gedung`, `lantai`, `ruangan`, `unit`, `kondisi`, `status`, `last_seen_at`, `last_seen_lat`, `last_seen_lng`, `last_seen_by`
FROM `aset`;

-- 3. Hapus kolom fisik/unik dari tabel parent (aset)
ALTER TABLE `aset`
DROP INDEX `uk_aset_nomor`,
DROP COLUMN `nomor_aset`,
DROP COLUMN `no_seri`,
DROP COLUMN `lokasi`,
DROP COLUMN `gedung`,
DROP COLUMN `lantai`,
DROP COLUMN `ruangan`,
DROP COLUMN `unit`,
DROP COLUMN `kondisi`,
DROP COLUMN `status`,
DROP COLUMN `last_seen_at`,
DROP COLUMN `last_seen_lat`,
DROP COLUMN `last_seen_lng`,
DROP COLUMN `last_seen_by`;

-- E. komponen_aset
ALTER TABLE `komponen_aset` ADD COLUMN `id_aset_series` CHAR(36) DEFAULT NULL AFTER `id_aset`;
UPDATE `komponen_aset` ka JOIN `aset_series` aser ON ka.id_aset = aser.id_aset SET ka.id_aset_series = aser.id;
ALTER TABLE `komponen_aset` DROP FOREIGN KEY `fk_komponen_aset`;
ALTER TABLE `komponen_aset` DROP COLUMN `id_aset`;
ALTER TABLE `komponen_aset` ADD CONSTRAINT `fk_komponen_aset_series` FOREIGN KEY (`id_aset_series`) REFERENCES `aset_series` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
-- 4. Karena Mutasi, Preventif, dan Laporan Kerusakan merujuk pada BARANG FISIK, 
--    kita perlu mengubah foreign key pada tabel-tabel tersebut ke aset_series.

-- A. Mutasi Aset (Tabel mutasi_aset tidak ada di schema di atas? Mungkin namanya riwayat_lokasi_aset dan mutasi)
-- Misal jika ada tabel riwayat_lokasi_aset:
ALTER TABLE `riwayat_lokasi_aset` ADD COLUMN `id_aset_series` CHAR(36) DEFAULT NULL AFTER `id_aset`;
UPDATE `riwayat_lokasi_aset` rla JOIN `aset_series` aser ON rla.id_aset = aser.id_aset SET rla.id_aset_series = aser.id;
ALTER TABLE `riwayat_lokasi_aset` DROP FOREIGN KEY `fk_rla_aset`;
ALTER TABLE `riwayat_lokasi_aset` DROP COLUMN `id_aset`;
ALTER TABLE `riwayat_lokasi_aset` ADD CONSTRAINT `fk_rla_aset_series` FOREIGN KEY (`id_aset_series`) REFERENCES `aset_series` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- B. Lembar Kerja (lk)
ALTER TABLE `lembar_kerja` ADD COLUMN `id_aset_series` CHAR(36) DEFAULT NULL AFTER `id_aset`;
UPDATE `lembar_kerja` lk JOIN `aset_series` aser ON lk.id_aset = aser.id_aset SET lk.id_aset_series = aser.id;
ALTER TABLE `lembar_kerja` DROP FOREIGN KEY `fk_lk_aset`;
ALTER TABLE `lembar_kerja` DROP COLUMN `id_aset`;
ALTER TABLE `lembar_kerja` ADD CONSTRAINT `fk_lk_aset_series` FOREIGN KEY (`id_aset_series`) REFERENCES `aset_series` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- C. Jadwal Preventif
ALTER TABLE `jadwal_preventif` ADD COLUMN `id_aset_series` CHAR(36) DEFAULT NULL AFTER `id_aset`;
UPDATE `jadwal_preventif` jp JOIN `aset_series` aser ON jp.id_aset = aser.id_aset SET jp.id_aset_series = aser.id;
ALTER TABLE `jadwal_preventif` DROP FOREIGN KEY `fk_jp_aset`;
ALTER TABLE `jadwal_preventif` DROP COLUMN `id_aset`;
ALTER TABLE `jadwal_preventif` ADD CONSTRAINT `fk_jp_aset_series` FOREIGN KEY (`id_aset_series`) REFERENCES `aset_series` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- D. LKP (lembar_kerja_preventif)
ALTER TABLE `lembar_kerja_preventif` ADD COLUMN `id_aset_series` CHAR(36) DEFAULT NULL AFTER `id_aset`;
UPDATE `lembar_kerja_preventif` lkp JOIN `aset_series` aser ON lkp.id_aset = aser.id_aset SET lkp.id_aset_series = aser.id;
ALTER TABLE `lembar_kerja_preventif` DROP FOREIGN KEY `fk_lkp_aset`;
ALTER TABLE `lembar_kerja_preventif` DROP COLUMN `id_aset`;
ALTER TABLE `lembar_kerja_preventif` ADD CONSTRAINT `fk_lkp_aset_series` FOREIGN KEY (`id_aset_series`) REFERENCES `aset_series` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;


