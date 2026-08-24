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

### UI/UX
- Halaman Detail Aset kini bersifat context-aware. Tombol aksi utama (Pinjamkan, Lapor Rusak, Ambil Komponen, Penghapusan) akan muncul atau disembunyikan berdasarkan status terkini aset.
- Dibuat modal terintegrasi (SweetAlert) di halaman Detail Aset agar aksi operasional terasa natural (bukan berpindah-pindah ke form terpisah).

### Business Process
- Status aset disederhanakan menjadi 5 state utama yang mendikte kemungkinan transisi berikutnya.
- Menambahkan prosedur Berita Acara (BA) yang wajib diisi dan diunggah (upload) sebelum aset resmi dinyatakan 'Dihapuskan'.

### Database
- Dibuat migration AddPeminjamanPenghapusan untuk menambahkan tabel peminjaman_aset dan penghapusan_aset.
- Keduanya berelasi dengan master data aset, sehingga master data tidak pernah di-delete, mempertahankan rekam jejak historis.

### Backend
- Menambahkan route dan method pinjam, kembali, dan hapus pada Aset Controller.

### Documentation
- Log revisi ini telah dibuat sebagai dokumentasi progress skripsi.

## 5. User Impact
User tidak perlu berpindah ke menu lain untuk melakukan aktivitas (pinjam, kanibal, hapus). Aksi akan otomatis menyesuaikan dengan status terkini aset. Proses Penghapusan menjadi jelas alurnya dan legal-compliant.

## 6. Implementation
- pp/Config/IPSRS.php (update list status)
- pp/Config/Routes.php (penambahan endpoint peminjaman & penghapusan)
- pp/Controllers/Aset.php (logika bisnis status dan database insertion)
- pp/Views/pages/aset/show_series.php (UX perombakan hub dan sweetalert modals)
- pp/Database/Migrations/2026-08-24-094333_AddPeminjamanPenghapusan.php (Schema)

## 7. Verification / Testing
- [x] Database migration scripting (siap dijalankan dengan php spark migrate)
- [x] Status adjustment di Config/IPSRS.php
- [x] Implementasi Peminjaman (Context Action)
- [x] Implementasi Kanibal button dari Detail Aset
- [x] Implementasi Penghapusan & Upload BA endpoints

## 8. Result
Implementasi utama berhasil diterapkan dan kode sudah diintegrasikan ke sistem existing (khususnya module Aset Detail). 

## 9. Related Revisions
N/A
