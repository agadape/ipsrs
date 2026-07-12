-- ============================================================
-- Structured LKP (Lembar Kerja Preventif) — schema + seed
-- Run in Supabase SQL Editor. Mirrors permissive access of existing tables.
-- ============================================================

-- ── Tables ──────────────────────────────────────────────────
create table if not exists template_checklist (
  id           bigint generated always as identity primary key,
  kategori     text not null,                         -- jenis alat: Kursi Roda, Genset, ...
  no_item      int  not null,
  jenis_item   text not null check (jenis_item in ('Inspeksi','Service','Pengukuran')),
  nama_komponen text not null,
  satuan       text                                   -- untuk Pengukuran: V, A, °C, lpm
);

create table if not exists lembar_kerja_preventif (
  id                 bigint generated always as identity primary key,
  no_order           text unique not null,            -- LKP-YYYYMM-XXXX
  id_jadwal          text,
  id_aset            text,
  kategori           text,
  tanggal_pemeriksaan date,
  teknisi            text,
  nama_user_ttd      text,
  hasil_pemeriksaan  text check (hasil_pemeriksaan in ('Siap Pakai','Perlu Perbaikan')),
  catatan            text,
  created_at         timestamptz default now()
);

create table if not exists detail_checklist_lkp (
  id              bigint generated always as identity primary key,
  id_lkp          bigint references lembar_kerja_preventif(id) on delete cascade,
  no_item         int,
  jenis_item      text,
  nama_komponen   text,
  hasil_inspeksi  text,     -- 'Baik' / 'Tidak'
  hasil_service   text,     -- 'Ya' / 'Tidak'
  nilai_pengukuran text,
  satuan          text,
  keterangan      text,
  created_at      timestamptz default now()
);

-- ── Access (permissive — samakan dengan tabel lain yang sudah jalan) ──
alter table template_checklist      enable row level security;
alter table lembar_kerja_preventif  enable row level security;
alter table detail_checklist_lkp    enable row level security;

do $$
declare t text;
begin
  foreach t in array array['template_checklist','lembar_kerja_preventif','detail_checklist_lkp'] loop
    execute format('drop policy if exists "allow all" on %I;', t);
    execute format('create policy "allow all" on %I for all to anon, authenticated using (true) with check (true);', t);
    execute format('grant all on %I to anon, authenticated;', t);
  end loop;
end$$;

-- ── Seed: template checklist (8 jenis alat representatif) ────
insert into template_checklist (kategori, no_item, jenis_item, nama_komponen, satuan) values
-- Kursi Roda
('Kursi Roda',1,'Inspeksi','Cek Kondisi Chasis',null),
('Kursi Roda',2,'Inspeksi','Cek Kesetimbangan',null),
('Kursi Roda',3,'Inspeksi','Cek Sandaran Punggung',null),
('Kursi Roda',4,'Inspeksi','Cek Sandaran Kaki',null),
('Kursi Roda',5,'Inspeksi','Cek Kondisi Roda',null),
('Kursi Roda',6,'Inspeksi','Cek Pengunci Roda',null),
('Kursi Roda',7,'Service','Pelumasan Bagian Bergerak',null),
('Kursi Roda',8,'Service','Pengencangan Sambungan',null),
-- Genset
('Genset',1,'Inspeksi','Cek Chasis',null),
('Genset',2,'Inspeksi','Cek Kabel & Konektor',null),
('Genset',3,'Inspeksi','Cek Bahan Bakar (Solar)',null),
('Genset',4,'Inspeksi','Cek Oli',null),
('Genset',5,'Inspeksi','Cek Radiator',null),
('Genset',6,'Inspeksi','Cek Accu',null),
('Genset',7,'Service','Pemanasan Mesin 10 Menit',null),
('Genset',8,'Service','Pembersihan Unit',null),
-- AHU
('AHU',1,'Inspeksi','Cek Evaporator',null),
('AHU',2,'Inspeksi','Cek Filter',null),
('AHU',3,'Inspeksi','Cek Fan',null),
('AHU',4,'Inspeksi','Cek Drainage',null),
('AHU',5,'Service','Pembersihan Filter',null),
('AHU',6,'Service','Penggantian V-belt',null),
-- UPS
('UPS',1,'Inspeksi','Cek Chasis',null),
('UPS',2,'Inspeksi','Cek Display',null),
('UPS',3,'Inspeksi','Cek Fan Blower',null),
('UPS',4,'Inspeksi','Uji Saat PLN Off',null),
('UPS',5,'Service','Pembersihan Unit',null),
('UPS',6,'Pengukuran','Tegangan Input',' V'),
('UPS',7,'Pengukuran','Tegangan Output',' V'),
-- Refrigerator
('Refrigerator',1,'Inspeksi','Cek Chasis',null),
('Refrigerator',2,'Inspeksi','Cek Kabel',null),
('Refrigerator',3,'Inspeksi','Cek Thermostat',null),
('Refrigerator',4,'Inspeksi','Cek Lampu',null),
('Refrigerator',5,'Inspeksi','Cek Karet Pintu',null),
('Refrigerator',6,'Inspeksi','Cek Display Suhu',null),
('Refrigerator',7,'Service','Pembersihan Casing',null),
('Refrigerator',8,'Pengukuran','Suhu Ruang Pendingin','°C'),
-- Flowmeter Oksigen
('Flowmeter Oksigen',1,'Inspeksi','Cek Humidifier',null),
('Flowmeter Oksigen',2,'Inspeksi','Cek Filter',null),
('Flowmeter Oksigen',3,'Inspeksi','Cek Regulator',null),
('Flowmeter Oksigen',4,'Service','Pembersihan & Pengencangan',null),
('Flowmeter Oksigen',5,'Pengukuran','Uji Aliran',' lpm'),
-- Panel MDP
('Panel MDP',1,'Inspeksi','Cek Box Panel',null),
('Panel MDP',2,'Inspeksi','Cek Kabel & Konektor',null),
('Panel MDP',3,'Inspeksi','Cek Lampu Indikator',null),
('Panel MDP',4,'Pengukuran','Suhu MCCB','°C'),
('Panel MDP',5,'Pengukuran','Tegangan R-S','V'),
('Panel MDP',6,'Pengukuran','Arus Fasa R','A'),
-- Water Heater
('Water Heater',1,'Inspeksi','Cek Chasis',null),
('Water Heater',2,'Inspeksi','Cek Kabel',null),
('Water Heater',3,'Inspeksi','Cek Thermostat',null),
('Water Heater',4,'Inspeksi','Cek Water Level',null),
('Water Heater',5,'Service','Pembersihan & Kuras Air',null),
('Water Heater',6,'Pengukuran','Suhu Output','°C');
