-- ============================================================
-- FULL MYSQL SCHEMA FOR IPSRS
-- RSUD Kota Yogyakarta
-- Generated: 2026-07-01
-- Matches actual Supabase schema + Kanibal Alat tables
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================
-- 1. KATEGORI ASET
-- ============================================================
CREATE TABLE IF NOT EXISTS `kategori_aset` (
  `id` CHAR(36) NOT NULL,
  `nama_kategori` VARCHAR(100) NOT NULL,
  `deskripsi` TEXT DEFAULT '',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_kategori_nama` (`nama_kategori`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 2. KODE KERUSAKAN
-- ============================================================
CREATE TABLE IF NOT EXISTS `kode_kerusakan` (
  `id` CHAR(36) NOT NULL,
  `kode` VARCHAR(10) NOT NULL,
  `nama` VARCHAR(100) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_kode_kode` (`kode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 3. ASET
-- ============================================================
CREATE TABLE IF NOT EXISTS `aset` (
  `id` CHAR(36) NOT NULL,
  `nomor_aset` VARCHAR(20) NOT NULL COMMENT 'Display code: A-00001',
  `nama` VARCHAR(200) NOT NULL,
  `jenis` VARCHAR(50) NOT NULL COMMENT 'Sarana / Prasarana / Alat Non Medis',
  `kategori` VARCHAR(100) NOT NULL COMMENT 'FK-like ke kategori_aset.nama_kategori',
  `lokasi` VARCHAR(200) NOT NULL,
  `gedung` VARCHAR(100) NOT NULL,
  `lantai` VARCHAR(20),
  `ruangan` VARCHAR(100) NOT NULL,
  `unit` VARCHAR(100) NOT NULL,
  `merk` VARCHAR(100),
  `model` VARCHAR(100) COMMENT 'Tipe/model aset',
  `no_seri` VARCHAR(100),
  `kapasitas` VARCHAR(50) COMMENT 'Kapasitas: 1 PK, 5000 VA, dll',
  `tahun` YEAR,
  `kondisi` VARCHAR(50) NOT NULL DEFAULT 'Baik',
  `status` VARCHAR(50) NOT NULL DEFAULT 'Aktif',
  `keterangan` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `last_seen_at` TIMESTAMP NULL,
  `last_seen_lat` DOUBLE,
  `last_seen_lng` DOUBLE,
  `last_seen_by` VARCHAR(100),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_aset_nomor` (`nomor_aset`),
  KEY `idx_aset_status` (`status`),
  KEY `idx_aset_kategori` (`kategori`),
  KEY `idx_aset_jenis` (`jenis`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 4. PENGGUNA
-- ============================================================
CREATE TABLE IF NOT EXISTS `pengguna` (
  `id` CHAR(36) NOT NULL,
  `email` VARCHAR(200) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL COMMENT 'akan diisi di Phase 4',
  `nama_lengkap` VARCHAR(100) NOT NULL,
  `role` VARCHAR(50) NOT NULL DEFAULT 'Pelapor',
  `unit` VARCHAR(100) NOT NULL,
  `aktif` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_pengguna_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 5. VENDOR
-- ============================================================
CREATE TABLE IF NOT EXISTS `vendor` (
  `id` CHAR(36) NOT NULL,
  `nama_vendor` VARCHAR(200) NOT NULL,
  `kontak` VARCHAR(200),
  `alamat` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 6. BARANG PERSEDIAAN (Stok Gudang)
-- ============================================================
CREATE TABLE IF NOT EXISTS `barang_persediaan` (
  `id` CHAR(36) NOT NULL,
  `no_barang` VARCHAR(20) NOT NULL COMMENT 'Display code: B-001',
  `nama` VARCHAR(200) NOT NULL,
  `kategori` VARCHAR(100) NOT NULL,
  `satuan` VARCHAR(20) NOT NULL DEFAULT 'pcs',
  `stok_tersedia` INT NOT NULL DEFAULT 0,
  `minimum_stok` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_barang_no` (`no_barang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 7. LAPORAN KERUSAKAN
-- ============================================================
CREATE TABLE IF NOT EXISTS `laporan_kerusakan` (
  `id` CHAR(36) NOT NULL,
  `no_order` VARCHAR(30) NOT NULL COMMENT 'Format: LK-YYYYMM-XXXX',
  `tanggal` DATE NOT NULL,
  `jam_laporan` TIME NOT NULL,
  `keluhan` TEXT NOT NULL,
  `kode` VARCHAR(10) NOT NULL COMMENT 'AC/PR/NM/AL',
  `pelapor` VARCHAR(100) NOT NULL,
  `unit_pelapor` VARCHAR(100) NOT NULL,
  `lokasi` VARCHAR(200) NOT NULL,
  `id_aset` CHAR(36),
  `nama_aset` VARCHAR(200),
  `teknisi` VARCHAR(100),
  `status` VARCHAR(50) NOT NULL DEFAULT 'Laporan Masuk',
  `tanggal_cek` DATE,
  `jam_cek` TIME,
  `tindakan` TEXT,
  `tanggal_selesai` DATE,
  `jam_selesai` TIME,
  `response_time` INT COMMENT 'menit',
  `down_time` INT COMMENT 'menit',
  `proses` VARCHAR(5) COMMENT 'I / II / III',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_lk_no_order` (`no_order`),
  KEY `idx_lk_status` (`status`),
  KEY `idx_lk_tanggal` (`tanggal`),
  KEY `idx_lk_id_aset` (`id_aset`),
  CONSTRAINT `fk_lk_aset` FOREIGN KEY (`id_aset`) REFERENCES `aset` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 8. DETAIL SUKU CADANG LK
-- ============================================================
CREATE TABLE IF NOT EXISTS `detail_suku_cadang_lk` (
  `id` CHAR(36) NOT NULL,
  `id_lk` CHAR(36) NOT NULL,
  `id_barang` CHAR(36),
  `sumber` VARCHAR(20) NOT NULL DEFAULT 'Gudang' COMMENT 'Gudang / Kanibal',
  `nama_barang` VARCHAR(200) NOT NULL,
  `jumlah` INT NOT NULL,
  `satuan` VARCHAR(20) DEFAULT 'pcs',
  `keterangan` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_dsc_id_lk` (`id_lk`),
  KEY `idx_dsc_id_barang` (`id_barang`),
  CONSTRAINT `fk_dsc_lk` FOREIGN KEY (`id_lk`) REFERENCES `laporan_kerusakan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_dsc_barang` FOREIGN KEY (`id_barang`) REFERENCES `barang_persediaan` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 9. DETAIL VENDOR LK
-- ============================================================
CREATE TABLE IF NOT EXISTS `detail_vendor_lk` (
  `id` CHAR(36) NOT NULL,
  `id_lk` CHAR(36) NOT NULL,
  `id_vendor` CHAR(36),
  `nama_vendor` VARCHAR(200),
  `tanggal_kirim` DATE,
  `tanggal_kembali` DATE,
  `estimasi_selesai` DATE,
  `keterangan` TEXT,
  PRIMARY KEY (`id`),
  KEY `idx_dvl_id_lk` (`id_lk`),
  KEY `idx_dvl_id_vendor` (`id_vendor`),
  CONSTRAINT `fk_dvl_lk` FOREIGN KEY (`id_lk`) REFERENCES `laporan_kerusakan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_dvl_vendor` FOREIGN KEY (`id_vendor`) REFERENCES `vendor` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 10. RIWAYAT TRANSAKSI STOK
-- ============================================================
CREATE TABLE IF NOT EXISTS `riwayat_transaksi_stok` (
  `id` CHAR(36) NOT NULL,
  `id_barang` CHAR(36) NOT NULL,
  `nama_barang` VARCHAR(200) NOT NULL,
  `jenis` VARCHAR(20) NOT NULL COMMENT 'Masuk / Keluar',
  `jumlah` INT NOT NULL CHECK (jumlah > 0),
  `tanggal` DATE NOT NULL,
  `no_dokumen` VARCHAR(30),
  `keterangan` TEXT,
  `petugas` VARCHAR(100),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `rts_id_barang` (`id_barang`),
  KEY `rts_tanggal` (`tanggal`),
  CONSTRAINT `fk_rts_barang` FOREIGN KEY (`id_barang`) REFERENCES `barang_persediaan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 11. RIWAYAT LOKASI ASET
-- ============================================================
CREATE TABLE IF NOT EXISTS `riwayat_lokasi_aset` (
  `id` CHAR(36) NOT NULL,
  `id_aset` CHAR(36) NOT NULL,
  `nama_aset` VARCHAR(200) NOT NULL,
  `lokasi_asal` VARCHAR(200),
  `lokasi_tujuan` VARCHAR(200) NOT NULL,
  `tanggal` DATE NOT NULL,
  `alasan` VARCHAR(100) NOT NULL,
  `petugas` VARCHAR(100) NOT NULL,
  `catatan` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `rla_id_aset` (`id_aset`),
  CONSTRAINT `fk_rla_aset` FOREIGN KEY (`id_aset`) REFERENCES `aset` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 12. JADWAL PREVENTIF
-- ============================================================
CREATE TABLE IF NOT EXISTS `jadwal_preventif` (
  `id` CHAR(36) NOT NULL,
  `aset` VARCHAR(200) NOT NULL COMMENT 'Denormalized nama aset',
  `lokasi` VARCHAR(200) NOT NULL,
  `tanggal` DATE NOT NULL,
  `jam` TIME NOT NULL,
  `teknisi` VARCHAR(100) NOT NULL,
  `status` VARCHAR(20) NOT NULL DEFAULT 'Belum' COMMENT 'Belum / Selesai / Terlewat',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `jp_tanggal` (`tanggal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 13. LEMBAR KERJA PREVENTIF
-- ============================================================
CREATE TABLE IF NOT EXISTS `lembar_kerja_preventif` (
  `id` CHAR(36) NOT NULL,
  `no_order` VARCHAR(30) NOT NULL COMMENT 'Format: LKP-YYYYMM-XXXX',
  `id_jadwal` CHAR(36),
  `id_aset` CHAR(36),
  `kategori` VARCHAR(100),
  `tanggal_pemeriksaan` DATE,
  `teknisi` VARCHAR(100),
  `nama_user_ttd` VARCHAR(100),
  `hasil_pemeriksaan` VARCHAR(50) COMMENT 'Siap Pakai / Perlu Perbaikan',
  `catatan` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_lkp_no_order` (`no_order`),
  KEY `lkp_id_jadwal` (`id_jadwal`),
  KEY `lkp_id_aset` (`id_aset`),
  CONSTRAINT `fk_lkp_jadwal` FOREIGN KEY (`id_jadwal`) REFERENCES `jadwal_preventif` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_lkp_aset` FOREIGN KEY (`id_aset`) REFERENCES `aset` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 14. DETAIL CHECKLIST LKP
-- ============================================================
CREATE TABLE IF NOT EXISTS `detail_checklist_lkp` (
  `id` CHAR(36) NOT NULL,
  `id_lkp` CHAR(36) NOT NULL,
  `no_item` INT,
  `jenis_item` VARCHAR(50) COMMENT 'Inspeksi / Service / Pengukuran',
  `nama_komponen` VARCHAR(200),
  `hasil_inspeksi` VARCHAR(20) COMMENT 'Baik / Tidak',
  `hasil_service` VARCHAR(20) COMMENT 'Ya / Tidak',
  `nilai_pengukuran` VARCHAR(50),
  `satuan` VARCHAR(20),
  `keterangan` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `dcl_id_lkp` (`id_lkp`),
  CONSTRAINT `fk_dcl_lkp` FOREIGN KEY (`id_lkp`) REFERENCES `lembar_kerja_preventif` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 15. TEMPLATE CHECKLIST
-- ============================================================
CREATE TABLE IF NOT EXISTS `template_checklist` (
  `id` CHAR(36) NOT NULL,
  `kategori` VARCHAR(100) NOT NULL,
  `no_item` INT NOT NULL,
  `jenis_item` VARCHAR(50) NOT NULL COMMENT 'Inspeksi / Service / Pengukuran',
  `nama_komponen` VARCHAR(200) NOT NULL,
  `satuan` VARCHAR(20),
  PRIMARY KEY (`id`),
  KEY `tc_kategori` (`kategori`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- KANIBAL ALAT (2 tabel baru)
-- ============================================================

-- ============================================================
-- 16. RIWAYAT KANIBAL
-- ============================================================
CREATE TABLE IF NOT EXISTS `riwayat_kanibal` (
  `id` CHAR(36) NOT NULL,
  `no_order_lk` VARCHAR(30) NOT NULL COMMENT 'Traceability ke LK',
  `id_aset_donor` CHAR(36) NOT NULL COMMENT 'Aset yang dipanen',
  `id_aset_penerima` CHAR(36) NOT NULL COMMENT 'Aset yang diperbaiki',
  `nama_komponen` VARCHAR(200) NOT NULL COMMENT 'Free text',
  `kondisi_komponen` VARCHAR(50) DEFAULT 'Baik' COMMENT 'Kondisi saat dipanen',
  `tanggal` DATE NOT NULL,
  `petugas` VARCHAR(100) NOT NULL COMMENT 'Teknisi eksekusi',
  `disetujui_oleh` VARCHAR(100) COMMENT 'Admin/Ka IPSRS',
  `keterangan` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `rk_no_order_lk` (`no_order_lk`),
  KEY `rk_id_aset_donor` (`id_aset_donor`),
  KEY `rk_id_aset_penerima` (`id_aset_penerima`),
  CONSTRAINT `fk_rk_donor` FOREIGN KEY (`id_aset_donor`) REFERENCES `aset` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_rk_penerima` FOREIGN KEY (`id_aset_penerima`) REFERENCES `aset` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 17. KOMPONEN ASET (Registry komponen per aset)
-- ============================================================
CREATE TABLE IF NOT EXISTS `komponen_aset` (
  `id` CHAR(36) NOT NULL,
  `id_aset` CHAR(36) NOT NULL,
  `nama_komponen` VARCHAR(200) NOT NULL,
  `kondisi` VARCHAR(50) DEFAULT 'Baik' COMMENT 'Baik / Kurang Baik / Rusak / Tidak Ada',
  `asal` VARCHAR(50) DEFAULT 'Original' COMMENT 'Original / Hasil Kanibal',
  `id_riwayat_kanibal` CHAR(36) COMMENT 'FK nullable, jika asal = Hasil Kanibal',
  `tanggal_dicatat` DATE,
  `keterangan` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `ka_id_aset` (`id_aset`),
  KEY `ka_id_kanibal` (`id_riwayat_kanibal`),
  CONSTRAINT `fk_ka_aset` FOREIGN KEY (`id_aset`) REFERENCES `aset` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_ka_kanibal` FOREIGN KEY (`id_riwayat_kanibal`) REFERENCES `riwayat_kanibal` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- SEED DATA
-- ============================================================

-- Kategori Aset
INSERT INTO `kategori_aset` (`id`, `nama_kategori`, `deskripsi`) VALUES
  ('a1000000-0000-0000-0000-000000000001', 'HVAC', 'Heating, Ventilation, Air Conditioning'),
  ('a1000000-0000-0000-0000-000000000002', 'Listrik', 'Instalasi dan peralatan kelistrikan'),
  ('a1000000-0000-0000-0000-000000000003', 'Mekanikal', 'Peralatan mekanikal'),
  ('a1000000-0000-0000-0000-000000000004', 'Elektronik', 'Peralatan elektronik medis dan non-medis'),
  ('a1000000-0000-0000-0000-000000000005', 'Bangunan', 'Prasarana bangunan dan gedung');

-- Kode Kerusakan
INSERT INTO `kode_kerusakan` (`id`, `kode`, `nama`) VALUES
  ('b1000000-0000-0000-0000-000000000001', 'AC', 'Air Conditioning'),
  ('b1000000-0000-0000-0000-000000000002', 'PR', 'Prasarana / Umum'),
  ('b1000000-0000-0000-0000-000000000003', 'NM', 'Non Medis / Lampu'),
  ('b1000000-0000-0000-0000-000000000004', 'AL', 'Alat / Sarana');

-- Vendor
INSERT INTO `vendor` (`id`, `nama_vendor`, `kontak`, `alamat`) VALUES
  ('c1000000-0000-0000-0000-000000000001', 'PT Teknik Medika Sejahtera', '0274-555101', 'Jl. Kaliurang KM 5, Yogyakarta'),
  ('c1000000-0000-0000-0000-000000000002', 'CV Sarana Listrik Mandiri', '0274-555202', 'Jl. Magelang KM 7, Yogyakarta'),
  ('c1000000-0000-0000-0000-000000000003', 'PT Cahaya Elektronik Service', '0274-555303', 'Jl. Solo KM 9, Yogyakarta');

-- Template Checklist (54 items dari Supabase)
INSERT INTO `template_checklist` (`id`, `kategori`, `no_item`, `jenis_item`, `nama_komponen`, `satuan`) VALUES
  ('d1000000-0000-0000-0000-000000000001', 'Kursi Roda', 1, 'Inspeksi', 'Cek Kondisi Chasis', NULL),
  ('d1000000-0000-0000-0000-000000000002', 'Kursi Roda', 2, 'Inspeksi', 'Cek Kesetimbangan', NULL),
  ('d1000000-0000-0000-0000-000000000003', 'Kursi Roda', 3, 'Inspeksi', 'Cek Sandaran Punggung', NULL),
  ('d1000000-0000-0000-0000-000000000004', 'Kursi Roda', 4, 'Inspeksi', 'Cek Sandaran Kaki', NULL),
  ('d1000000-0000-0000-0000-000000000005', 'Kursi Roda', 5, 'Inspeksi', 'Cek Kondisi Roda', NULL),
  ('d1000000-0000-0000-0000-000000000006', 'Kursi Roda', 6, 'Inspeksi', 'Cek Pengunci Roda', NULL),
  ('d1000000-0000-0000-0000-000000000007', 'Kursi Roda', 7, 'Service', 'Pelumasan Bagian Bergerak', NULL),
  ('d1000000-0000-0000-0000-000000000008', 'Kursi Roda', 8, 'Service', 'Pengencangan Sambungan', NULL),
  ('d1000000-0000-0000-0000-000000000009', 'Genset', 1, 'Inspeksi', 'Cek Chasis', NULL),
  ('d1000000-0000-0000-0000-000000000010', 'Genset', 2, 'Inspeksi', 'Cek Kabel & Konektor', NULL),
  ('d1000000-0000-0000-0000-000000000011', 'Genset', 3, 'Inspeksi', 'Cek Bahan Bakar (Solar)', NULL),
  ('d1000000-0000-0000-0000-000000000012', 'Genset', 4, 'Inspeksi', 'Cek Oli', NULL),
  ('d1000000-0000-0000-0000-000000000013', 'Genset', 5, 'Inspeksi', 'Cek Radiator', NULL),
  ('d1000000-0000-0000-0000-000000000014', 'Genset', 6, 'Inspeksi', 'Cek Accu', NULL),
  ('d1000000-0000-0000-0000-000000000015', 'Genset', 7, 'Service', 'Pemanasan Mesin 10 Menit', NULL),
  ('d1000000-0000-0000-0000-000000000016', 'Genset', 8, 'Service', 'Pembersihan Unit', NULL),
  ('d1000000-0000-0000-0000-000000000017', 'AHU', 1, 'Inspeksi', 'Cek Evaporator', NULL),
  ('d1000000-0000-0000-0000-000000000018', 'AHU', 2, 'Inspeksi', 'Cek Filter', NULL),
  ('d1000000-0000-0000-0000-000000000019', 'AHU', 3, 'Inspeksi', 'Cek Fan', NULL),
  ('d1000000-0000-0000-0000-000000000020', 'AHU', 4, 'Inspeksi', 'Cek Drainage', NULL),
  ('d1000000-0000-0000-0000-000000000021', 'AHU', 5, 'Service', 'Pembersihan Filter', NULL),
  ('d1000000-0000-0000-0000-000000000022', 'AHU', 6, 'Service', 'Penggantian V-belt', NULL),
  ('d1000000-0000-0000-0000-000000000023', 'UPS', 1, 'Inspeksi', 'Cek Chasis', NULL),
  ('d1000000-0000-0000-0000-000000000024', 'UPS', 2, 'Inspeksi', 'Cek Display', NULL),
  ('d1000000-0000-0000-0000-000000000025', 'UPS', 3, 'Inspeksi', 'Cek Fan Blower', NULL),
  ('d1000000-0000-0000-0000-000000000026', 'UPS', 4, 'Inspeksi', 'Uji Saat PLN Off', NULL),
  ('d1000000-0000-0000-0000-000000000027', 'UPS', 5, 'Service', 'Pembersihan Unit', NULL),
  ('d1000000-0000-0000-0000-000000000028', 'UPS', 6, 'Pengukuran', 'Tegangan Input', ' V'),
  ('d1000000-0000-0000-0000-000000000029', 'UPS', 7, 'Pengukuran', 'Tegangan Output', ' V'),
  ('d1000000-0000-0000-0000-000000000030', 'Refrigerator', 1, 'Inspeksi', 'Cek Chasis', NULL),
  ('d1000000-0000-0000-0000-000000000031', 'Refrigerator', 2, 'Inspeksi', 'Cek Kabel', NULL),
  ('d1000000-0000-0000-0000-000000000032', 'Refrigerator', 3, 'Inspeksi', 'Cek Thermostat', NULL),
  ('d1000000-0000-0000-0000-000000000033', 'Refrigerator', 4, 'Inspeksi', 'Cek Lampu', NULL),
  ('d1000000-0000-0000-0000-000000000034', 'Refrigerator', 5, 'Inspeksi', 'Cek Karet Pintu', NULL),
  ('d1000000-0000-0000-0000-000000000035', 'Refrigerator', 6, 'Inspeksi', 'Cek Display Suhu', NULL),
  ('d1000000-0000-0000-0000-000000000036', 'Refrigerator', 7, 'Service', 'Pembersihan Casing', NULL),
  ('d1000000-0000-0000-0000-000000000037', 'Refrigerator', 8, 'Pengukuran', 'Suhu Ruang Pendingin', '°C'),
  ('d1000000-0000-0000-0000-000000000038', 'Flowmeter Oksigen', 1, 'Inspeksi', 'Cek Humidifier', NULL),
  ('d1000000-0000-0000-0000-000000000039', 'Flowmeter Oksigen', 2, 'Inspeksi', 'Cek Filter', NULL),
  ('d1000000-0000-0000-0000-000000000040', 'Flowmeter Oksigen', 3, 'Inspeksi', 'Cek Regulator', NULL),
  ('d1000000-0000-0000-0000-000000000041', 'Flowmeter Oksigen', 4, 'Service', 'Pembersihan & Pengencangan', NULL),
  ('d1000000-0000-0000-0000-000000000042', 'Flowmeter Oksigen', 5, 'Pengukuran', 'Uji Aliran', ' lpm'),
  ('d1000000-0000-0000-0000-000000000043', 'Panel MDP', 1, 'Inspeksi', 'Cek Box Panel', NULL),
  ('d1000000-0000-0000-0000-000000000044', 'Panel MDP', 2, 'Inspeksi', 'Cek Kabel & Konektor', NULL),
  ('d1000000-0000-0000-0000-000000000045', 'Panel MDP', 3, 'Inspeksi', 'Cek Lampu Indikator', NULL),
  ('d1000000-0000-0000-0000-000000000046', 'Panel MDP', 4, 'Pengukuran', 'Suhu MCCB', '°C'),
  ('d1000000-0000-0000-0000-000000000047', 'Panel MDP', 5, 'Pengukuran', 'Tegangan R-S', 'V'),
  ('d1000000-0000-0000-0000-000000000048', 'Panel MDP', 6, 'Pengukuran', 'Arus Fasa R', 'A'),
  ('d1000000-0000-0000-0000-000000000049', 'Water Heater', 1, 'Inspeksi', 'Cek Chasis', NULL),
  ('d1000000-0000-0000-0000-000000000050', 'Water Heater', 2, 'Inspeksi', 'Cek Kabel', NULL),
  ('d1000000-0000-0000-0000-000000000051', 'Water Heater', 3, 'Inspeksi', 'Cek Thermostat', NULL),
  ('d1000000-0000-0000-0000-000000000052', 'Water Heater', 4, 'Inspeksi', 'Cek Water Level', NULL),
  ('d1000000-0000-0000-0000-000000000053', 'Water Heater', 5, 'Service', 'Pembersihan & Kuras Air', NULL),
  ('d1000000-0000-0000-0000-000000000054', 'Water Heater', 6, 'Pengukuran', 'Suhu Output', '°C');

-- Pengguna (default admin)
INSERT INTO `pengguna` (`id`, `email`, `password_hash`, `nama_lengkap`, `role`, `unit`, `aktif`) VALUES
  ('e1000000-0000-0000-0000-000000000001', 'admin@rsud-jogja.go.id', '$2y$10$YourHashedPasswordHere', 'Administrator', 'Admin', 'IPSRS', 1);
