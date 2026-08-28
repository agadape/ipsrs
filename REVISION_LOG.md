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
- Halaman Detail Aset kini bersifat context-aware. Tombol aksi utama (Pinjamkan, Lapor Rusak, Ambil Komponen, Penghapusan) akan muncul atau disembunyikan berdasarkan status terkini aset.
- Menambahkan tab navigasi untuk filtering cepat di halaman index.
- Perbaikan logic filter pada backend menggunakan query relasional.

---

# Revision 1.1 - Database Data Migration (Clean Up)

**Date:** 2026-08-28  
**Status:** Completed  
**Area:** Database set_series

## 1. Problem / Interpretation
Data lama masih menggunakan nilai status ('Aktif', 'Rusak', 'Tidak Aktif') yang tidak dikenali oleh state machine baru ('Tersedia', 'Dalam Perbaikan', 'Rusak Berat', dll). Hal ini membuat tombol tidak muncul dan query relasional menjadi tidak sinkron.

## 2. Our Response
Alih-alih menggunakan logika IF bersarang di code (hardcoded mapping) yang bisa menjadi utang teknis (technical debt) saat sistem dibawa ke Production, kami menormalkan data langsung di database.

## 3. Implementation
Query SQL dijalankan di database production untuk membersihkan dan menormalkan nama status. Kodingan controller dan view dikembalikan (revert) ke bentuk yang lebih bersih.

---

# Revision 2 - ERD Normalization & Integrity Enforcement

**Date:** 2026-08-28  
**Status:** Completed  
**Area:** Database Schema & CodeIgniter Controllers (Locations, Constraints)

## 1. Problem / Interpretation
Hasil analisis mendalam terhadap struktur ERD menunjukkan beberapa celah arsitektur: ketiadaan Foreign Key di tabel historis (peminjaman, penghapusan, riwayat kanibal), pencatatan relasi menggunakan Natural Key tanpa constraint, pencatatan lokasi yang berulang dan berpotensi typo (free-text), serta ketiadaan Index di kolom yang sering difilter.

## 2. Our Response
Membangun ulang struktur relasi menjadi sangat ketat agar tidak ada data yatim piatu (orphan), menormalisasi tabel lokasi menjadi `master_lokasi`, dan menegakkan ENUM untuk status aset demi integritas proses bisnis.

## 3. Implementation
- **Database:** Eksekusi script SQL di Production untuk constraint FK, ENUM, penambahan UNIQUE KEY, Index `status`, dan ekstraksi 777 lokasi dari teks bebas menjadi tabel `master_lokasi`.
- **Codebase:** Refactor `AsetSeriesModel` dan `Aset` Controller untuk menggunakan `$id_lokasi` saat create/update. Modifikasi form HTML (`form_series.php`) dari input teks bebas menjadi `<select>` dropdown (Data Binding ke `master_lokasi`). View read-only otomatis menampilkan kolom dari hasil JOIN tanpa merusak flow lama.
