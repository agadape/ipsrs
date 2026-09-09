Modul Laporan Kerusakan (Sisi Pelapor & Teknisi IPSRS)

Gambar 2.1. Halaman Dashboard Utama Laporan Kerusakan.

Modul ini adalah jantung dari operasional IPSRS. Sistem ini dirancang untuk mempermudah perawat, dokter, atau staf ruangan (Pelapor) dalam mengeluhkan kerusakan fasilitas bangunan/sarana, dan mempermudah Teknisi dalam merespons laporan tersebut secara cepat dan terukur. Modul ini terbagi menjadi dua sudut pandang: Sisi Pelapor (Publik) dan Sisi Teknisi (Admin).

---

Bagian 1: Sisi Staf Ruangan / Pelapor (Portal Publik)

Kehebatan Sistem: Kami merancang portal ini agar perawat/staf ruangan **tidak perlu** memiliki akun atau mengingat *password* untuk melaporkan AC bocor atau lampu mati. Mereka bisa melapor dengan sangat cepat melalui *Smartphone*!

1. Mengakses Portal Pelaporan

Gambar 2.2. Halaman Portal Laporan Kerusakan Publik.

Ada dua cara sangat mudah bagi pelapor untuk masuk ke halaman formulir laporan:
Cara A (Tanpa Scan): Buka situs web aplikasi di HP atau komputer, lalu langsung ketikkan `/lapor` di alamat pencarian, atau klik tombol besar **"Bukan teknisi? Buka Portal Pelapor Umum"** yang berada persis di bawah tombol *Login*.
Cara B (Jalur Cepat via Scan): Jika alat yang rusak sudah ditempeli stiker QR Code (misalnya Pompa Air), pelapor cukup men-scan stiker tersebut menggunakan kamera HP. Di layar HP mereka, cukup klik tombol merah **"+ Lapor Kerusakan"**. Sistem akan otomatis membuka portal dan nama alat yang rusak akan terisi sendiri!

2. Mengisi Formulir Laporan Kerusakan (LK)
Setelah halaman portal `/lapor` terbuka, staf ruangan akan melihat kotak-kotak formulir yang sangat sederhana.

Mengisi Identitas Pelapor:
Nama Pelapor (Wajib Diisi): Ketikkan nama Anda beserta gelar (Contoh: "Ns. Siska" atau "dr. Andi").
Unit / Ruangan Pelapor (Wajib Diisi): Ketikkan nama ruangan tempat Anda bekerja atau tempat kerusakan terjadi (Contoh: "Ruang Rawat Inap Anggrek").

Detail Kerusakan:
Lokasi Kerusakan Saat Ini (Wajib Diisi): Ketikkan titik persis di mana masalah itu berada (Contoh: "Kamar Mandi Pasien 203" atau "Plafon Lorong Utama").
Aset yang Rusak (Opsional): Jika Anda melapor melalui cara *scan*, kolom ini sudah terisi otomatis. Jika tidak, Anda bisa mengeklik kotak pilihan (dropdown) ini dan mencari nama sarana yang rusak (Contoh: mencari "AC Split Ruang Rapat"). Jika alat tidak terdaftar, biarkan kotak ini tetap di opsi "-- Tidak Tahu / Aset Tidak Terdaftar --".
Deskripsi Kerusakan (Wajib Diisi): Ceritakan secara jelas masalahnya di kotak besar ini. (Contoh ketikan: "AC bocor dan meneteskan air ke lantai sejak semalam, sangat mengganggu pasien").

Mengirim Laporan:
Setelah selesai, gulir layar ke bagian paling bawah lalu klik tombol besar berwarna merah bertuliskan **"Kirim Laporan Kerusakan"**.
Sistem akan memuat sejenak, lalu layar HP Anda akan berubah memunculkan spanduk hijau **"Laporan Terkirim!"** beserta **Nomor Tiket** besar (Contoh: PR202610-001).
Tugas pelapor sudah selesai! Notifikasi akan langsung berbunyi di komputer ruang teknisi IPSRS.

---

Bagian 2: Sisi Teknisi IPSRS (Manajemen Tiket & Perbaikan)

Begitu staf mengirim laporan, tiket tersebut akan masuk ke dalam tabel antrean di akun teknisi. Di sinilah tugas Anda sebagai Teknisi IPSRS dimulai.

1. Menerima dan Mengklaim Tiket Laporan Masuk

Gambar 2.3. Halaman Daftar Laporan Kerusakan di Akun Teknisi.

Melihat Laporan Masuk:
Di sebelah kiri layar komputer Anda, klik menu navigasi bernama **"Laporan Kerusakan"** (atau LK).
Perhatikan tabel daftar laporan. Laporan yang baru saja dikirim oleh perawat tadi akan berada di urutan paling atas dengan status berwarna abu-abu/biru bertuliskan "Laporan Masuk".

Mengklaim Tiket (Disposisi Otomatis):
Untuk mengambil alih perbaikan, klik tulisan nomor tiket (atau tombol ikon mata) pada baris laporan tersebut. Anda akan masuk ke rincian laporannya.
Di pojok kanan atas, Anda akan melihat tombol hijau besar bertuliskan **"Klaim Pekerjaan Ini"**. Klik tombol tersebut!
Kehebatan Sistem: Begitu Anda mengkliknya, sistem akan otomatis mencatatkan nama Anda sebagai teknisi penanggung jawab tunggal untuk kerusakan AC bocor tersebut. Teknisi lain tidak akan bisa merebut tiket ini dari Anda.

2. Proses Survei dan Perbaikan Alat

Gambar 2.4. Halaman Panel Kendali Status Perbaikan.

Setelah diklaim, Anda bebas merubah status pekerjaan (Progress) sesuai kondisi lapangan. Di halaman rincian laporan, terdapat tombol **"Ubah Status"**.

Tahap Survei:
Jika Anda beranjak dari kursi dan sedang berjalan menuju ruangan untuk mengecek kondisi AC, klik tombol **"Ubah Status"**, lalu pilih **"Survei"**.
Tahap Perbaikan (Eksekusi):
Jika setelah disurvei ternyata filter AC kotor dan Anda sedang mencucinya/memperbaikinya saat itu juga, klik tombol **"Ubah Status"** dan pilih **"Dalam Perbaikan"**.

3. Penggunaan Suku Cadang atau Vendor Pihak Ketiga
Terkadang alat tidak bisa diperbaiki dengan tangan kosong. Jika AC tadi ternyata butuh *Freon* baru atau kompresornya mati total, Anda harus mencatatnya di sistem.

A. Menunggu Suku Cadang (Sparepart Gudang):
Ubah status menjadi **"Menunggu Suku Cadang"**.
Gulir (scroll) layar ke bagian tengah halaman rincian. Anda akan menemukan tab/tabel bertuliskan **"Suku Cadang Digunakan"**.
Klik tombol **"+ Tambah Penggunaan"**. Pilih nama barang (misal: Freon R32) dari kolom pilihan (dropdown), masukkan jumlah yang dipakai (misal: 1 tabung), lalu klik Simpan.
Kehebatan Sistem: Sistem akan otomatis memotong (mengurangi) stok Freon di gudang inventaris secara seketika! Anda tidak perlu repot-repot pergi ke menu gudang untuk menguranginya secara manual.

B. Memanggil Pihak Ketiga (Vendor Servis):
Jika kerusakan terlalu parah (misal: Pipa sentral AC pecah) dan harus dikerjakan oleh teknisi luar/rekanan, ubah status laporan menjadi **"Menunggu Pihak Ketiga"**.
Lalu gulir ke tabel **"Detail Vendor/Pihak Ke-3"**. Klik tombol **"+ Tambah Pekerjaan Vendor"**, pilih nama perusahaannya, isi deskripsi kerjanya, dan simpan. Ini sangat penting sebagai bukti penagihan (Invoice) tagihan jasa nantinya.

4. Menutup Tiket (Selesai Perbaikan)

Gambar 2.5. Halaman Konfirmasi Penyelesaian Pekerjaan (Tutup Tiket).

Jika AC sudah dingin kembali dan perawat sudah puas, Anda wajib menutup laporan ini.

Tombol Tutup Tiket:
Klik kembali tombol **"Ubah Status"**, lalu pilih opsi terakhir yaitu **"Selesai"**.

Mencatat Analisis Kerusakan:
Sebuah jendela penutupan (pop-up) akan muncul. Sistem mewajibkan Anda mengisi "Analisis Kerusakan" (Contoh ketikan: "Pipa pembuangan tersumbat lumut dan freon kurang tekanan").
Anda juga akan diminta mengisikan waktu penyelesaian (berapa jam Anda mengerjakannya).
Klik tombol **"Simpan & Tutup Laporan"**.

Status laporan akan seketika berubah menjadi Hijau (Selesai), dan waktu respons perbaikan Anda (Response Time) akan tercatat di dalam rapor kinerja teknisi di *Dashboard Analytics* pimpinan!
