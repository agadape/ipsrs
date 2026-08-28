# Prompt Analisis ERD (Entity Relationship Diagram)

**Petunjuk untuk Pengguna:**  
*Copy keseluruhan teks di bawah ini (mulai dari `<CONTEXT>` hingga `<INSTRUCTIONS>`), lalu paste ke AI / sesi chat baru. Jangan lupa lampirkan/paste juga isi file JSON struktur database kamu.*

---

<CONTEXT>
**Nama Sistem:** Computerized Maintenance Management System (CMMS) 
**Lingkup Kerja:** Instalasi Pemeliharaan Sarana dan Prasarana Rumah Sakit (IPSRS) RSUD Kota Yogyakarta.
**Fokus Utama Sistem:** Mengelola aset **NON-MEDIS** (sarana & prasarana kerumahtanggaan rumah sakit seperti AC, Genset, Kelistrikan, Furnitur, Kursi Roda). Sistem **TIDAK** menangani aset atau alat kesehatan (Alkes) medis.

**Proses Bisnis & Asset Lifecycle:**
1. **Master Data & Unit (Series):** Sistem membedakan antara "Master Aset" (Katalog induk, misal: "AC Daikin 1 PK") dan "Aset Series" (Fisik barang bernomor seri unik yang tersebar di ruangan-ruangan).
2. **State Machine / Status Aset:** Setiap aset series hanya boleh memiliki 1 dari 5 status utama:
   - `Tersedia`: Aset sedang beroperasi normal atau diam di ruangan/gudang.
   - `Dipinjam`: Aset sedang dipinjam oleh unit/ruangan lain.
   - `Dalam Perbaikan`: Aset sedang dilaporkan rusak (memiliki tiket Laporan Kerusakan aktif) dan dikerjakan teknisi.
   - `Rusak Berat`: Aset gagal diperbaiki oleh teknisi (Tindak lanjut dari Laporan Kerusakan). Aset ini menunggu untuk dievaluasi apakah akan di-kanibal (diambil komponennya) atau dihapuskan.
   - `Dihapuskan`: End of Life (EOL). Aset sudah resmi dibuang/dijual/dihibahkan, didasari dengan dokumen Berita Acara (BA) legal. Data tidak didelete dari DB untuk rekam jejak historis.
3. **Peminjaman:** Aset yang `Tersedia` dapat dipinjam. Peminjaman mencatat nama peminjam, unit, tanggal pinjam, rencana kembali, dan tanggal kembali aktual.
4. **Mutasi / Lokasi:** Perpindahan lokasi aset (baik gedung maupun lantai) dicatat dalam bentuk riwayat.
5. **Laporan Kerusakan (LK):** User melapor kerusakan, Admin melempar tugas ke Teknisi, Teknisi merespons (Response Time) dan menyelesaikan perbaikan (Downtime).
6. **Pemeliharaan Preventif (LKP):** Penjadwalan maintenance rutin dengan checklist inspeksi/service.
7. **Inventory / Sparepart:** Manajemen stok masuk & keluar untuk suku cadang perbaikan aset.
8. **Kanibalisasi:** Praktik mengambil komponen yang masih berfungsi dari aset donor (biasanya yang `Rusak Berat`) untuk dipasang ke aset penerima.
</CONTEXT>

<DATABASE_SCHEMA>
(USER: MASUKKAN/PASTE JSON STRUKTUR DATABASE ATAU FILE SQL DUMP KAMU DI SINI)
</DATABASE_SCHEMA>

<INSTRUCTIONS>
Sebagai AI Database Architect dan System Analyst Senior, tugas kamu adalah melakukan tinjauan kritis (Critical Review) terhadap struktur database (ERD) dari CMMS IPSRS di atas. 

Lakukan analisis berdasarkan konteks bisnis yang disediakan dan jawab pertanyaan-pertanyaan berikut secara terstruktur:

1. **Evaluasi Kebenaran Relasi (Entity Integrity & Relationships):** 
   - Apakah relasi antar tabel (terutama pemisahan Master Aset dan Aset Series) sudah terimplementasi dengan benar? 
   - Apakah tabel `peminjaman_aset` dan `penghapusan_aset` sudah berelasi tepat dengan entitas fisik (Aset Series)?

2. **Evaluasi Efisiensi & Normalisasi:** 
   - Apakah terdapat redundansi data yang tidak perlu?
   - Apakah tipe data yang digunakan (terutama Primary Key menggunakan UUID/CHAR(36)) sudah optimal untuk konteks framework CodeIgniter 4?

3. **Evaluasi Kesesuaian dengan Business Process:** 
   - Apakah struktur tabel sudah cukup kuat untuk mendukung semua 8 poin proses bisnis di atas (terutama perhitungan Response Time/Downtime di Laporan Kerusakan, dan pelacakan riwayat Mutasi)?
   - Apakah tabel Kanban/Checklist Preventif sudah mampu menampung data dinamis per aset?

4. **Saran Perbaikan / Refactoring (Opsional tapi Krusial):** 
   - Berikan rekomendasi konkrit (sebutkan nama tabel/kolom) apabila ada struktur yang harus diubah atau index yang wajib ditambahkan agar performa sistem tetap stabil saat data menumpuk.

Berikan analisis yang jujur, tajam, dan akademis. Fokus pada kelemahan arsitektur (jika ada) karena hasil analisismu akan dijadikan dasar evaluasi ERD untuk sidang skripsi/tugas akhir mahasiswa IT.
</INSTRUCTIONS>
