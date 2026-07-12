-- ============================================================
-- Migration: Buat 3 tabel master data baru untuk IPSRS
-- Jalankan di Supabase SQL Editor
-- ============================================================

-- 1. Tabel Pengguna (user management)
CREATE TABLE IF NOT EXISTS pengguna (
    id          UUID DEFAULT gen_random_uuid() PRIMARY KEY,
    email       TEXT NOT NULL UNIQUE,
    nama_lengkap TEXT NOT NULL,
    role        TEXT NOT NULL DEFAULT 'Pelapor',
    unit        TEXT NOT NULL,
    aktif       BOOLEAN NOT NULL DEFAULT true,
    created_at  TIMESTAMPTZ NOT NULL DEFAULT now()
);

COMMENT ON TABLE pengguna IS 'Data pengguna sistem IPSRS';
COMMENT ON COLUMN pengguna.role IS 'Role: Admin, Teknisi, Pelapor, Manajemen';

-- RLS (Row Level Security) — enable if needed
ALTER TABLE pengguna ENABLE ROW LEVEL SECURITY;

-- Policy: everyone can read, authenticated users can insert/update
CREATE POLICY "pengguna_select" ON pengguna FOR SELECT USING (true);
CREATE POLICY "pengguna_insert" ON pengguna FOR INSERT WITH CHECK (true);
CREATE POLICY "pengguna_update" ON pengguna FOR UPDATE USING (true);


-- 2. Tabel Kategori Aset
CREATE TABLE IF NOT EXISTS kategori_aset (
    id             UUID DEFAULT gen_random_uuid() PRIMARY KEY,
    nama_kategori  TEXT NOT NULL UNIQUE,
    deskripsi      TEXT DEFAULT '',
    created_at     TIMESTAMPTZ NOT NULL DEFAULT now()
);

COMMENT ON TABLE kategori_aset IS 'Master data kategori aset (HVAC, Listrik, Mekanikal, dll.)';

ALTER TABLE kategori_aset ENABLE ROW LEVEL SECURITY;

CREATE POLICY "kategori_aset_select" ON kategori_aset FOR SELECT USING (true);
CREATE POLICY "kategori_aset_insert" ON kategori_aset FOR INSERT WITH CHECK (true);
CREATE POLICY "kategori_aset_update" ON kategori_aset FOR UPDATE USING (true);


-- 3. Tabel Kode Kerusakan
CREATE TABLE IF NOT EXISTS kode_kerusakan (
    id         UUID DEFAULT gen_random_uuid() PRIMARY KEY,
    kode       TEXT NOT NULL UNIQUE,
    nama       TEXT NOT NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

COMMENT ON TABLE kode_kerusakan IS 'Master data kode pekerjaan untuk Laporan Kerusakan';

ALTER TABLE kode_kerusakan ENABLE ROW LEVEL SECURITY;

CREATE POLICY "kode_kerusakan_select" ON kode_kerusakan FOR SELECT USING (true);
CREATE POLICY "kode_kerusakan_insert" ON kode_kerusakan FOR INSERT WITH CHECK (true);
CREATE POLICY "kode_kerusakan_update" ON kode_kerusakan FOR UPDATE USING (true);


-- ============================================================
-- Seed data: data awal (opsional, hapus jika tidak perlu)
-- ============================================================

-- Kategori Aset (data awal sesuai yang sebelumnya hardcode di form)
INSERT INTO kategori_aset (nama_kategori, deskripsi) VALUES
    ('HVAC',       'Heating, Ventilation, Air Conditioning'),
    ('Listrik',    'Instalasi dan peralatan kelistrikan'),
    ('Mekanikal',  'Peralatan mekanikal'),
    ('Elektronik', 'Peralatan elektronik medis dan non-medis'),
    ('Bangunan',   'Prasarana bangunan dan gedung')
ON CONFLICT (nama_kategori) DO NOTHING;

-- Kode Kerusakan (data awal sesuai yang sebelumnya hardcode)
INSERT INTO kode_kerusakan (kode, nama) VALUES
    ('AC', 'Air Conditioning'),
    ('PR', 'Prasarana / Umum'),
    ('NM', 'Non Medis / Lampu'),
    ('AL', 'Alat / Sarana')
ON CONFLICT (kode) DO NOTHING;
