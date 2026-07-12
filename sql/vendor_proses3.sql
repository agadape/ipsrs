-- ============================================================
-- Vendor / Proses III — schema + seed
-- Run in Supabase SQL Editor.
-- ============================================================

create table if not exists vendor (
  id          bigint generated always as identity primary key,
  nama_vendor text not null,
  kontak      text,
  alamat      text,
  created_at  timestamptz default now()
);

create table if not exists detail_vendor_lk (
  id               bigint generated always as identity primary key,
  id_lk            text,
  id_vendor        bigint references vendor(id),
  nama_vendor      text,
  tanggal_kirim    date,
  tanggal_kembali  date,
  estimasi_selesai date,
  keterangan       text,                 -- catatan / ringkasan RAB
  created_at       timestamptz default now()
);

-- Access (permissive — samakan dengan tabel lain)
alter table vendor           enable row level security;
alter table detail_vendor_lk enable row level security;

do $$
declare t text;
begin
  foreach t in array array['vendor','detail_vendor_lk'] loop
    execute format('drop policy if exists "allow all" on %I;', t);
    execute format('create policy "allow all" on %I for all to anon, authenticated using (true) with check (true);', t);
    execute format('grant all on %I to anon, authenticated;', t);
  end loop;
end$$;

-- Seed contoh vendor
insert into vendor (nama_vendor, kontak, alamat) values
('PT Teknik Medika Sejahtera', '0274-555101', 'Jl. Kaliurang KM 5, Yogyakarta'),
('CV Sarana Listrik Mandiri',  '0274-555202', 'Jl. Magelang KM 7, Yogyakarta'),
('PT Cahaya Elektronik Service','0274-555303', 'Jl. Solo KM 9, Yogyakarta');
