# 📋 Checklist Pengujian (UAT) IPSRS

Berikut adalah daftar hal-hal krusial yang wajib Anda tes secara berurutan untuk memastikan semua perombakan besar kita berjalan dengan sempurna.

---

### 1. Migrasi & Daftar Aset (Menu: Daftar Aset)
- [ ] **Data Katalog (Parent):** Pastikan di tabel Daftar Aset yang muncul adalah **Katalog Aset** (Nama, Merk, Model, Kategori).
- [ ] **Detail Aset:** Klik salah satu aset. Pastikan halaman detail menampilkan info Katalog di atas, dan tabel **Daftar Series / Unit Fisik** di bawahnya.
- [ ] **Tambah Katalog:** Tekan tombol "Tambah Aset". Pastikan formnya sekarang **lebih ringkas** (tidak ada lagi input Nomor Seri, Gedung, Lantai, dll karena sudah jadi milik Series). Pastikan field *Kapasitas* juga sudah hilang.

### 2. Pendaftaran Fisik Aset (Menu: Detail Aset -> Tambah Series)
- [ ] **Tambah Series Baru:** Di dalam Detail Aset, klik tombol `+ Tambah Series`.
- [ ] Isi form dengan Nomor Aset, Nomor Seri, Lokasi, Gedung, Ruangan, Kondisi, dll.
- [ ] Pastikan data berhasil tersimpan dan muncul di tabel *Daftar Series* pada halaman Detail Aset tersebut.

### 3. Pendaftaran Mutasi (Menu: Mutasi Aset)
- [ ] **Form Pindah Lokasi:** Buka form Mutasi Aset. 
- [ ] **Dropdown Aset:** Klik dropdown pencarian aset. Pastikan yang muncul adalah **Nomor Aset - Nama Alat (Lokasi)** (Ini mengarah ke Aset Series/Fisik).
- [ ] Pastikan field **Lokasi Saat Ini** otomatis terisi sesuai dengan aset fisik yang dipilih (Anda minta *view-only*).
- [ ] Simpan mutasi, lalu cek di tabel **Daftar Mutasi**. Pastikan field "Dari" (Lokasi Lama) dan "Catatan" sudah muncul dengan benar.

### 4. Laporan Kerusakan / Lembar Kerja (Role: Pelapor & Admin)
- [ ] **Pelapor View-Only:** Login sebagai *Pelapor*. Buka tiket Lembar Kerja yang sudah di-*acc* (Tahap Survei). Pastikan tab "Tambah Suku Cadang" **tidak bisa diedit** (hilang form/tombol simpan-nya). Pelapor murni *View Only*!
- [ ] **Admin - Dropdown Status:** Login sebagai *Admin*. Buka Lembar Kerja. Cek card "Update Status" yang pertama (saat survei). Pastikan design-nya sudah berubah menjadi **Dropdown** yang lebih rapi (bukan design visual yang jelek sebelumnya), dan pilihan yang redundant sudah dibuang.

### 5. Modul Preventif (Menu: Lembar Preventif) - *Sangat Krusial*
- [ ] **Validasi Waktu:** Coba buat jadwal/LKP dengan Waktu (Tanggal & Jam) mundur / di masa lalu. Pastikan sistem menolak dan mewajibkan waktu **setelah dari waktu sekarang**.
- [ ] **Lokasi Read-Only:** Di Lembar Preventif, pastikan field Lokasi sudah *read-only*.
- [ ] **Dropdown Teknisi:** Pastikan field Teknisi kini berupa dropdown pilihan (mengambil dari tabel pengguna) sehingga ID dan nama jelas, tidak bisa asal ketik dobel.
- [ ] **Verifikasi Lokasi:** Pastikan ada section konfirmasi "Apakah lokasi alat tepat?" di form LKP.
- [ ] **Tombol Aksi (Icon):** Di daftar preventif, pastikan tombol "Selesai" dan "Hapus" sudah diganti menggunakan **Icon**.
- [ ] **Tombol Selesai Disembunyikan:** Pastikan tombol icon Selesai **tidak muncul** kalau statusnya masih "Belum" (Memaksa petugas untuk klik LKP dulu).
- [ ] **Progres LKP Aman:** Isi progres LKP, simpan. Pastikan datanya **tidak hilang** (tersimpan permanen).
- [ ] **Checkbox Tambah Item:** Di dalam form LKP, pastikan ada opsi "Service" di samping Inspeksi dan Pengukuran.
- [ ] **Nama Pengguna Hilang:** Pastikan field "Nama Pengguna" yang tidak berguna sudah dihilangkan dari form.
- [ ] **Fix Error LocalStorage:** Saat membuka menu preventif, pastikan error `Access to storage is not allowed from this context` di console (terutama di Incognito) sudah lenyap!

### 6. Riwayat Kanibal (Menu: Kanibal Alat)
- [ ] **Dropdown Aset Fisik:** Coba buat riwayat Kanibal. Pastikan dropdown pencarian aset donor maupun aset penerima menunjuk ke **Aset Series** (fisik), bukan katalog parent-nya.

---

Cukup jalankan poin-poin ini satu per satu. Jika semuanya berhasil dichecklist tanpa error merah dari CodeIgniter, berarti aplikasi Anda sudah siap tempur 100%! 🚀
