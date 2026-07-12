-- ============================================================
-- MASTER DATA TABLES
-- Pengguna, Kategori Aset, Kode Kerusakan
-- ============================================================

-- Pengguna
CREATE TABLE IF NOT EXISTS `pengguna` (
  `id` CHAR(36) NOT NULL,
  `email` VARCHAR(200) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `nama_lengkap` VARCHAR(100) NOT NULL,
  `role` VARCHAR(50) NOT NULL DEFAULT 'Pelapor',
  `unit` VARCHAR(100) NOT NULL,
  `aktif` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_pengguna_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Kategori Aset
CREATE TABLE IF NOT EXISTS `kategori_aset` (
  `id` CHAR(36) NOT NULL,
  `nama_kategori` VARCHAR(100) NOT NULL,
  `deskripsi` TEXT DEFAULT '',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_kategori_nama` (`nama_kategori`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Kode Kerusakan
CREATE TABLE IF NOT EXISTS `kode_kerusakan` (
  `id` CHAR(36) NOT NULL,
  `kode` VARCHAR(10) NOT NULL,
  `nama` VARCHAR(100) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_kode_kode` (`kode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed: Kategori
INSERT INTO `kategori_aset` (`id`, `nama_kategori`, `deskripsi`) VALUES
  ('a1000000-0000-0000-0000-000000000001', 'HVAC', 'Heating, Ventilation, Air Conditioning'),
  ('a1000000-0000-0000-0000-000000000002', 'Listrik', 'Instalasi dan peralatan kelistrikan'),
  ('a1000000-0000-0000-0000-000000000003', 'Mekanikal', 'Peralatan mekanikal'),
  ('a1000000-0000-0000-0000-000000000004', 'Elektronik', 'Peralatan elektronik medis dan non-medis'),
  ('a1000000-0000-0000-0000-000000000005', 'Bangunan', 'Prasarana bangunan dan gedung');

-- Seed: Kode Kerusakan
INSERT INTO `kode_kerusakan` (`id`, `kode`, `nama`) VALUES
  ('b1000000-0000-0000-0000-000000000001', 'AC', 'Air Conditioning'),
  ('b1000000-0000-0000-0000-000000000002', 'PR', 'Prasarana / Umum'),
  ('b1000000-0000-0000-0000-000000000003', 'NM', 'Non Medis / Lampu'),
  ('b1000000-0000-0000-0000-000000000004', 'AL', 'Alat / Sarana');
