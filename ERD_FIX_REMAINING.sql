-- =================================================================
-- SCRIPT LANJUTAN (Hanya berisi yang belum tereksekusi tadi)
-- Eksekusi ini di tab "SQL" phpMyAdmin
-- =================================================================

-- -----------------------------------------------------------------
-- Sisa POIN 5: FK Laporan Kerusakan & Riwayat Kanibal
-- -----------------------------------------------------------------
ALTER TABLE laporan_kerusakan 
  ADD CONSTRAINT k_lk_kode_kerusakan FOREIGN KEY (kode) REFERENCES kode_kerusakan (kode) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE iwayat_kanibal 
  ADD CONSTRAINT k_rk_no_order_lk FOREIGN KEY (
o_order_lk) REFERENCES laporan_kerusakan (
o_order) ON DELETE RESTRICT ON UPDATE CASCADE;

-- -----------------------------------------------------------------
-- POIN 4: MIGRASI LOKASI (PERSIAPAN MASTER DATA)
-- -----------------------------------------------------------------
-- Tahap 1: Membuat tabel Master Lokasi
CREATE TABLE IF NOT EXISTS master_lokasi (
  id char(36) NOT NULL,
  gedung varchar(100) NOT NULL DEFAULT 'Utama',
  lantai varchar(20) DEFAULT NULL,
  
ama_ruangan varchar(100) NOT NULL,
  
ama_unit varchar(100) NOT NULL,
  created_at timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (id),
  UNIQUE KEY uk_master_lokasi (gedung,lantai,
ama_ruangan,
ama_unit)
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
ALTER TABLE set_series
  ADD COLUMN id_lokasi char(36) DEFAULT NULL AFTER unit,
  ADD KEY idx_aset_series_lokasi (id_lokasi);

ALTER TABLE set_series
  ADD CONSTRAINT k_aset_series_lokasi FOREIGN KEY (id_lokasi) REFERENCES master_lokasi (id) ON DELETE SET NULL ON UPDATE CASCADE;

-- Tahap 4: Mengisi id_lokasi berdasarkan data teks yang ada
UPDATE aset_series a
JOIN master_lokasi m 
  ON COALESCE(NULLIF(a.gedung, ''), 'Utama') = m.gedung 
 AND (a.lantai = m.lantai OR (a.lantai IS NULL AND m.lantai IS NULL) OR (a.lantai = '' AND m.lantai IS NULL))
 AND COALESCE(NULLIF(a.ruangan, ''), '-') = m.nama_ruangan
 AND COALESCE(NULLIF(a.unit, ''), '-') = m.nama_unit
SET a.id_lokasi = m.id;
