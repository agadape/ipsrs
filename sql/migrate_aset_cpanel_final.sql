-- =========================================================================
-- MIGRATION SCRIPT FINAL UNTUK CPANEL
-- =========================================================================
-- Jalankan ini di phpMyAdmin cPanel secara keseluruhan (pastikan DB dalam state asli belum termigrasi)

START TRANSACTION;

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

-- 4. Update relasi pada komponen_aset
ALTER TABLE `komponen_aset` ADD COLUMN `id_aset_series` CHAR(36) DEFAULT NULL AFTER `id_aset`;
UPDATE `komponen_aset` ka JOIN `aset_series` aser ON ka.id_aset = aser.id_aset SET ka.id_aset_series = aser.id;
ALTER TABLE `komponen_aset` DROP FOREIGN KEY `fk_ka_aset`;
ALTER TABLE `komponen_aset` DROP KEY `ka_id_aset`;
ALTER TABLE `komponen_aset` DROP COLUMN `id_aset`;
ALTER TABLE `komponen_aset` ADD CONSTRAINT `fk_ka_aset_series` FOREIGN KEY (`id_aset_series`) REFERENCES `aset_series` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 5. Update relasi pada riwayat_lokasi_aset
ALTER TABLE `riwayat_lokasi_aset` ADD COLUMN `id_aset_series` CHAR(36) DEFAULT NULL AFTER `id_aset`;
UPDATE `riwayat_lokasi_aset` rla JOIN `aset_series` aser ON rla.id_aset = aser.id_aset SET rla.id_aset_series = aser.id;
ALTER TABLE `riwayat_lokasi_aset` DROP FOREIGN KEY `fk_rla_aset`;
ALTER TABLE `riwayat_lokasi_aset` DROP COLUMN `id_aset`;
ALTER TABLE `riwayat_lokasi_aset` ADD CONSTRAINT `fk_rla_aset_series` FOREIGN KEY (`id_aset_series`) REFERENCES `aset_series` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 6. Update relasi pada lembar_kerja
ALTER TABLE `laporan_kerusakan` ADD COLUMN `id_aset_series` CHAR(36) DEFAULT NULL AFTER `id_aset`;
UPDATE `laporan_kerusakan` lk JOIN `aset_series` aser ON lk.id_aset = aser.id_aset SET lk.id_aset_series = aser.id;
ALTER TABLE `laporan_kerusakan` DROP FOREIGN KEY `fk_lk_aset`;
ALTER TABLE `laporan_kerusakan` DROP COLUMN `id_aset`;
ALTER TABLE `laporan_kerusakan` ADD CONSTRAINT `fk_lk_aset_series` FOREIGN KEY (`id_aset_series`) REFERENCES `aset_series` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- 7. Update relasi pada lembar_kerja_preventif
ALTER TABLE `lembar_kerja_preventif` ADD COLUMN `id_aset_series` CHAR(36) DEFAULT NULL AFTER `id_aset`;
UPDATE `lembar_kerja_preventif` lkp JOIN `aset_series` aser ON lkp.id_aset = aser.id_aset SET lkp.id_aset_series = aser.id;
ALTER TABLE `lembar_kerja_preventif` DROP FOREIGN KEY `fk_lkp_aset`;
ALTER TABLE `lembar_kerja_preventif` DROP COLUMN `id_aset`;
ALTER TABLE `lembar_kerja_preventif` ADD CONSTRAINT `fk_lkp_aset_series` FOREIGN KEY (`id_aset_series`) REFERENCES `aset_series` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- 8. Update relasi pada riwayat_kanibal
ALTER TABLE `riwayat_kanibal` ADD COLUMN `id_series_donor` CHAR(36) DEFAULT NULL AFTER `id_aset_donor`;
ALTER TABLE `riwayat_kanibal` ADD COLUMN `id_series_penerima` CHAR(36) DEFAULT NULL AFTER `id_aset_penerima`;
UPDATE `riwayat_kanibal` rk JOIN `aset_series` aser1 ON rk.id_aset_donor = aser1.id_aset JOIN `aset_series` aser2 ON rk.id_aset_penerima = aser2.id_aset SET rk.id_series_donor = aser1.id, rk.id_series_penerima = aser2.id;
ALTER TABLE `riwayat_kanibal` DROP FOREIGN KEY `fk_rk_donor`;
ALTER TABLE `riwayat_kanibal` DROP FOREIGN KEY `fk_rk_penerima`;
ALTER TABLE `riwayat_kanibal` DROP KEY `rk_id_aset_donor`;
ALTER TABLE `riwayat_kanibal` DROP KEY `rk_id_aset_penerima`;
ALTER TABLE `riwayat_kanibal` DROP COLUMN `id_aset_donor`;
ALTER TABLE `riwayat_kanibal` DROP COLUMN `id_aset_penerima`;
ALTER TABLE `riwayat_kanibal` ADD CONSTRAINT `fk_rk_series_donor` FOREIGN KEY (`id_series_donor`) REFERENCES `aset_series` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
ALTER TABLE `riwayat_kanibal` ADD CONSTRAINT `fk_rk_series_penerima` FOREIGN KEY (`id_series_penerima`) REFERENCES `aset_series` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- 9. Update tabel jadwal_preventif (hanya kolom aset yang diubah isinya karena tidak ada foreign key)
UPDATE `jadwal_preventif` jp JOIN `aset_series` aser ON jp.aset = aser.id_aset SET jp.aset = aser.id;

COMMIT;

