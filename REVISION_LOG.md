# Revision 1 - Asset Lifecycle and Workflow Overhaul

**Date:** 2026-08-24  
**Status:** Completed  
**Area:** Asset Management, EOL Workflow (Kanibal/Penghapusan), Peminjaman

## 1. Examiner Feedback
1. Batasan masalah hanya untuk sarana-prasarana (non-medis).
2. Perlu menguraikan aset siap kanibal dan aset dihapuskan.
3. Penghapusan aset memerlukan Berita Acara (BA) dan tindak lanjut.
4. Perlu catatan peminjaman aset.

## 2. Problem / Interpretation
Implementasi yang berbasis CRUD murni tidak mencerminkan business process riil di rumah sakit. Aset tidak semata-mata 'berubah status' atau 'ditambahkan data'. Terdapat asset lifecycle dari Tersedia -> Dipinjam, serta EOL (End of Life) saat aset Rusak Berat -> Dievaluasi (bisa dikanibal) -> Menunggu Legalitas (BA) -> Dihapuskan. UX perlu diubah menjadi context-aware hub (Halaman Detail Aset) daripada sebaran menu independen, dan status aset harus mencerminkan realitas operasional.

## 3. Our Response
1. Menetapkan 5 status utama: Tersedia, Dipinjam, Dalam Perbaikan, Rusak Berat, Dihapuskan.
2. Memusatkan aksi (context-aware actions) di Detail Aset: [Pinjamkan], [Kembalikan], [Ambil Komponen (Kanibal)], [Lakukan Penghapusan], [Lihat BA].
3. Kanibalisasi adalah action (mengambil parts) dari aset 'Rusak Berat', tanpa menghapus aset itu sendiri. 
4. Penghapusan adalah EOL, mengubah aset menjadi historikal (read-only) dengan prasyarat BA.
5. Pembuatan tabel peminjaman_aset dan penghapusan_aset untuk menampung data tanpa redundansi dan menjaga audit trail.

## 4. Changes Made
*(Lihat log sebelumnya)*

---

# Revision 1.1 - Legacy Status Compatibility (Hotfix)

**Date:** 2026-08-28  
**Status:** Completed  
**Area:** Asset List Filter & Asset Details

## 1. Examiner Feedback
- (Lanjutan) Pengguna mendapati "WHOOPS" error pada halaman filter dan hilangnya tombol pada halaman detail.

## 2. Problem / Interpretation
Error "WHOOPS" terjadi karena fungsi rray_filter di controller mencoba mengakses field status secara langsung pada Master Aset, padahal status menempel pada set_series dan Master Aset lawas tidak memiliki index array status. Selain itu, data *existing* di database masih menggunakan string status lama ('Aktif', 'Rusak'), sehingga logika IF untuk tombol baru (yang mengharapkan 'Tersedia') gagal tertrigger (tombol menghilang).

## 3. Our Response
- Memperbaiki controller agar filter status membaca dari relasi set_series ke database langsung alih-alih rray_filter string sederhana.
- Memasang fungsi "pemetaan mundur" (backward compatibility mapping) di View: Jika sistem menemukan kata 'Aktif', akan diperlakukan sebagai 'Tersedia' di sisi antarmuka pengguna agar tombol tetap muncul tanpa memaksa update seluruh tabel master.

## 4. Implementation
- pp/Controllers/Aset.php -> Penambahan query builder $db->table('aset_series')->whereIn(...) 
- pp/Views/pages/aset/show_series.php -> Mapping string PHP strtolower() === 'aktif' -> 'Tersedia'.

## 5. Result
Filter tabel di index dan tombol Peminjaman di show_series kembali bekerja normal walau dengan data lama.
