# Rencana Struktur User Guide Book IPSRS RSUD Kota Yogyakarta

Dokumen ini merupakan kerangka dasar (Daftar Isi) dari buku panduan pengguna aplikasi Manajemen Aset IPSRS. 

## 1. Modul Manajemen Aset (Katalog & Inventaris Fisik)
*   **Pendaftaran Aset:**
    *   Cara membuat Master Aset (Katalog).
    *   Cara mendaftarkan unit aset (Aset Series / Inventaris fisik).
    *   Cara mencetak label QR Code Aset.
*   **Pemindahan & Pelacakan:**
    *   Cara melakukan Mutasi (pindah ruangan/gudang).
    *   Cara menggunakan fitur Verifikasi Posisi (Scan QR & Geofencing GPS).

## 2. Modul Laporan Kerusakan (Sisi Pelapor & Teknisi)
*   **Sisi Staf Ruangan / Pelapor (Portal Publik):**
    *   Cara mengakses halaman portal pelaporan (`/lapor`) tanpa perlu login.
    *   Cara membuat laporan kerusakan (LK) dengan atau tanpa *scan* alat.
*   **Sisi Teknisi IPSRS (Manajemen Tiket):**
    *   Cara mengklaim tiket laporan masuk (Disposisi otomatis).
    *   Proses Survei dan Perbaikan alat.
    *   Penggunaan suku cadang atau vendor pihak ketiga dalam perbaikan.
    *   Cara menutup tiket (Selesai).

## 3. Modul Pemeliharaan Preventif (Preventive Maintenance)
*   **Pembuatan Jadwal Preventif:**
    *   Cara membuat jadwal pemeliharaan rutin untuk sebuah aset.
*   **Eksekusi Lembar Preventif:**
    *   Cara teknisi mengisi form *checklist* pemeliharaan (Lembar Preventif).
    *   Fitur Auto-Template dan pembuatan tiket Kerusakan otomatis (Auto-Generate).

## 4. Modul Manajemen Suku Cadang (Inventory & Gudang)
*   **Pencatatan Gudang:**
    *   Cara mendaftarkan jenis suku cadang baru.
    *   Cara mencatat barang masuk / kulakan (Restok).
*   **Monitoring Otomatis:**
    *   Penjelasan pemotongan stok otomatis via Laporan Kerusakan.
    *   Penjelasan notifikasi/peringatan (Alert) Stok Kritis.

## 5. Modul Peminjaman & Penghapusan Aset (Mutasi Khusus)
*   **Peminjaman Alat:**
    *   Cara mencatat ruangan/unit yang meminjam alat IPSRS.
    *   Cara merubah status alat kembali saat dikembalikan.
*   **Penghapusan & Kanibal Alat:**
    *   Cara menghapus alat yang sudah tidak bisa dipakai (Afkir/Dihibahkan).
    *   Cara melakukan Kanibal Alat (mempreteli mesin/onderdil untuk suku cadang).

## 6. Modul Administrator & Laporan (Data Master)
*   **Pengaturan Sistem Dasar:**
    *   Manajemen Akun (Data Pengguna & Hak Akses).
    *   Pengaturan Kategori Aset, Kode Kerusakan, dan Data Vendor rekanan.
*   **Laporan & Analitik (Reporting):**
    *   Cara membaca dan mencetak rekapitulasi data (Export).



<div style='page-break-after: always;'></div>

Modul Manajemen Aset (Katalog & Inventaris Fisik)

Gambar 1.1. Halaman Daftar Master Aset (Katalog Utama).

Halaman Manajemen Aset merupakan pintu gerbang utama (pangkalan data) bagi seluruh sarana dan prasarana umum yang dikelola oleh IPSRS RSUD Kota Yogyakarta. Di dalam sistem ini, pendataan aset dibagi menjadi dua tahap yang sangat penting untuk Anda pahami:
1. Master Aset (Katalog): Ini adalah jenis atau merk sarana/alatnya. (Contoh: "AC Split Daikin").
2. Aset Series (Fisik Alat): Ini adalah wujud barang nyatanya yang bisa dipegang. (Contoh: AC Split Daikin milik ruang IGD, dan AC Split Daikin milik Poli Gigi).

Oleh karena itu, sebelum Anda bisa memasang atau menaruh alat di ruangan, Anda harus memastikan "Katalog-nya" sudah dibuat terlebih dahulu.

1. Menambahkan Master Aset Baru (Katalog Utama)
Langkah ini HANYA Anda lakukan jika pihak rumah sakit baru saja membeli jenis, model, atau merk alat/sarana baru yang belum pernah terdaftar sama sekali di dalam sistem.

Gambar 1.2. Halaman Formulir Tambah Master Aset.

Berikut adalah langkah-langkah untuk mendaftarkan alat/sarana baru:
A. Membuka Formulir Master
Di sebelah kiri layar komputer Anda, terdapat menu navigasi panjang (Sidebar). Cari dan klik menu bernama "Master Aset".
Perhatikan pojok kanan atas layar Anda, terdapat tombol berwarna cerah bertuliskan "+ Tambah Aset Baru". Klik tombol tersebut.
Sistem akan membuka sebuah halaman baru yang berisi kotak-kotak kosong (formulir) yang harus Anda isi.

B. Mengisi Informasi Dasar Aset
Nama Aset (Wajib Diisi): Ketikkan nama murni dari alat/sarana tersebut secara jelas. (Contoh ketikan: "AC Split 1 PK" atau "Mesin Genset").
Jenis Aset (Wajib Diisi): Klik kotak ini, nanti akan muncul pilihan (dropdown) ke bawah. Karena wewenang IPSRS berfokus pada sarana umum, pilihlah "Non-Medis". (Peralatan medis khusus biasanya dikelola oleh tim Elektromedik).
Kategori (Wajib Diisi): Klik kotak pilihan ini untuk menentukan pengelompokannya. (Contoh: Pilih "Elektronik", "Perabot", "Instalasi Air", dsb). Pengelompokan ini akan sangat memudahkan Anda saat mencari data di kemudian hari.
Merek & Model (Opsional): Jika sarana tersebut memiliki merek khusus, ketikkan merek asli pabrikan dan nomor tipenya di kotak ini (Contoh: Merek "Daikin", Model "FTX-20"). Jika tidak ada (misalnya untuk pembuatan wastafel khusus), Anda boleh membiarkannya kosong.

C. Menyimpan Data
Setelah semua kotak formulir terisi dengan benar, gulir (scroll) layar Anda ke bagian paling bawah.
Klik tombol biru bertuliskan "Simpan".
Dalam hitungan detik, sistem akan menyimpan katalog ini dan layar akan otomatis kembali ke halaman tabel daftar utama.

2. Menambahkan Unit Aset Fisik (Aset Series / Inventaris)
Setelah "Katalog" berhasil dibuat (atau jika katalognya memang sudah ada dari dulu), Anda wajib mendaftarkan wujud barang fisiknya (unit per unit). Tanpa langkah ini, sarana tersebut tidak akan pernah bisa diletakkan atau dipasang di ruangan mana pun.

Gambar 1.3. Halaman Detail Master Aset & Tabel Unit Tersedia.

A. Membuka Profil Master Aset
Pada tabel daftar Master Aset, cari nama alat yang ingin Anda kelola (Misal: AC Split Daikin).
Di baris nama alat tersebut, lihat pada ujung paling kanan. Anda akan menemukan tombol "Detail" (atau ikon panah). Klik tombol tersebut. Anda akan dibawa masuk ke dalam halaman profil alat itu.

B. Membuka Formulir Unit Fisik
Gulir (scroll) layar Anda ke arah bawah secara perlahan hingga Anda menemukan sebuah tabel berjudul "Unit Tersedia".
Tepat di atas tabel tersebut, klik tombol "+ Tambah Unit (Series)". Sebuah jendela kecil (pop-up) akan muncul di tengah layar.

C. Mengisi Spesifikasi Fisik Unit
Nomor Aset / Inventaris (Wajib Diisi): Masukkan kode atau nomor unik inventaris resmi dari rumah sakit (Contoh: INV-AC-2026-001). 
⚠️ Peringatan Keras: Nomor ini bertindak sebagai KTP dari alat tersebut. Sistem otomatis akan menolak dan menampilkan pesan error warna merah jika Anda mengetikkan Nomor Aset yang sama persis dengan yang sudah dipakai oleh alat lain.
Serial Number (SN): Masukkan nomor seri mesin bawaan pabrik (biasanya tercetak di pelat bodi alat). Jika tidak ada, biarkan kosong.
Lokasi / Ruangan (Wajib Diisi): Klik kotak ini, lalu pilih nama ruangan dari daftar yang turun (dropdown). Ini menentukan di ruangan mana alat/sarana ini pertama kali dipasang atau didistribusikan (Misal: Pilih "Instalasi Gawat Darurat (IGD)").
Kondisi Aset (Wajib Diisi): Pilih kondisi fisik sarana saat Anda mendatanya pertama kali (Pilih: Baik / Rusak Ringan / Rusak Berat).

D. Menyimpan Unit Fisik
Setelah kotak-kotak di atas terisi, klik tombol "Simpan" di bagian bawah jendela tersebut. Layar akan memuat sejenak (loading), dan voila! Unit fisik (seperti AC di ruang IGD) ini sekarang sudah sah menjadi inventaris resmi.

3. Mencetak Label QR Code (Identitas Digital Aset)

Gambar 1.4. Tampilan Halaman Cetak Label QR Code Aset.

Tanpa ditempeli Label QR, teknisi lapangan tidak akan bisa melakukan pelacakan (scanning) fisik alat. Anda tidak perlu membuat QR secara manual; Kehebatan Sistem IPSRS adalah ia secara otomatis membuatkan (auto-generate) pola gambar QR Code yang unik dan terkunci untuk setiap unit fisik yang baru saja Anda simpan di langkah sebelumnya.

A. Membuka Pratinjau QR
Di dalam tabel "Unit Tersedia" (masih di halaman profil Aset), cari unit yang baru saja Anda buat.
Pada ujung kanan baris unit tersebut, terdapat tombol kecil bergambar ikon kode QR bertuliskan "Lihat/Cetak QR Code". Klik tombol itu.
Sebuah jendela atau tab internet (browser) baru akan terbuka.

B. Format Cetak (Print-Ready)
Di halaman baru tersebut, Anda akan melihat sebuah gambar kotak hitam-putih QR Code yang besar, lengkap dengan tulisan nama rumah sakit dan Nomor Inventaris di bawahnya.
Desain ini sudah sengaja dibuat dengan ukuran yang pas dan proporsional agar langsung siap dicetak ke kertas stiker berukuran kecil.

C. Proses Mencetak ke Kertas Stiker
Untuk mencetaknya, Anda bisa menekan klik kanan pada mouse Anda di area layar yang kosong, lalu pilih menu "Print". Atau cara cepatnya: tekan tahan tombol `Ctrl` lalu huruf `P` pada keyboard komputer Anda.
Pastikan mesin printer Anda sudah menyala dan terisi kertas stiker label tahan air. Setelah dicetak, tempelkan stiker tersebut ke bagian bodi sarana (misal: penutup depan AC atau bodi Genset) yang rata serta mudah dijangkau oleh kamera HP.

4. Mutasi Aset (Pindah Ruangan & Kanibal Alat)

Gambar 1.5. Halaman Formulir Mutasi & Riwayat Perpindahan.

⚠️ Aturan Wajib: Anda DILARANG KERAS memindahkan catatan lokasi sarana prasarana menggunakan tombol "Edit" biasa! Seluruh pergerakan aset—baik yang dibongkar dan dipindahkan ke ruangan lain maupun yang ditarik ke gudang—WAJIB dicatat melalui menu khusus "Mutasi Aset". Hal ini agar jejak rekam pergerakannya (Audit Trail) terekam secara abadi dan jelas pertanggungjawabannya.

A. Membuka Formulir Mutasi
Lihat ke navigasi sebelah kiri layar Anda, cari dan klik menu "Mutasi Aset".
Pada halaman yang terbuka, perhatikan pojok kanan atas, lalu klik tombol biru bertuliskan "+ Mutasi Baru". 

B. Memilih Alat Fisik yang Akan Dipindah
Pada kotak paling atas (Pilih Aset), klik dan ketikkan nama atau nomor seri alat yang fisiknya ingin Anda pindahkan. Sistem akan mencarikan alat tersebut untuk Anda.

C. Menentukan Tujuan Mutasi (Aksi Pemindahan)
Di bawahnya, terdapat kotak dropdown berjudul "Jenis Mutasi". Klik kotak tersebut, dan pilihlah salah satu dari 3 kondisi berikut:
1. Pindah Ruangan: Pilih ini jika alat (misalnya kursi tunggu atau Kipas Angin) akan digeser ke unit lain. Saat opsi ini dipilih, sebuah kotak baru bernama "Ruangan Tujuan" akan muncul. Anda wajib mengekliknya dan memilih nama ruangan penerima sarana tersebut.
2. Simpan ke Gudang: Pilih ini jika sarana sedang dibongkar/ditarik dari lapangan untuk diistirahatkan atau diamankan ke dalam gudang utama IPSRS.
3. Jadikan Kanibal (Tindakan Ekstrem): Pilih opsi ini HANYA JIKA sarana tersebut telah hancur atau afkir (tidak bisa diperbaiki lagi), namun pihak teknisi memutuskan untuk mempreteli komponennya (seperti kompresor AC atau motor listriknya) guna disimpan sebagai suku cadang (sparepart) cadangan. Saat Anda memilih ini, sistem akan otomatis "mematikan" status alat tersebut menjadi benda mati (Kanibal) dan tidak lagi dihitung sebagai aset utuh.

D. Mengisi Petugas & Catatan
Petugas (Wajib Diisi): Tuliskan nama jelas Anda atau nama teknisi yang bertugas melakukan pembongkaran/pemindahan.
Catatan/Alasan (Wajib Diisi): Ketikkan alasan singkat mengapa alat ini dipindah (Contoh ketikan: "Dipindah sementara karena AC utama di ruang Rapat sedang rusak").

E. Menyelesaikan Proses Mutasi
Klik tombol "Proses Mutasi" di bagian paling bawah. 
Sistem akan melakukan loading (memproses data). Status lokasi aset akan seketika berubah saat itu juga, dan yang paling penting: riwayat (kapan dipindah, jam berapa, oleh siapa, dan alasannya) akan terkunci secara permanen di dalam tabel riwayat.

5. Verifikasi Lokasi via HP (QR Scan & Geofencing)

Gambar 1.6. Tampilan Layar HP saat Melakukan Scan QR Code.

Ini adalah fitur tingkat lanjut (Advanced) yang bertindak sebagai "Satpam Digital" rumah sakit. Saat teknisi berpatroli, mereka cukup menodongkan HP ke stiker sarana prasarana untuk memvalidasi apakah alat tersebut benar-benar ada di posisinya atau hilang.

A. Melakukan Pemindaian (Scan) Menggunakan HP
Nyalakan kamera bawaan di HP (Smartphone) Anda. Arahkan fokus kamera tepat ke stiker QR Code yang menempel di bodi alat (misal: di bodi Pompa Air).
Dalam hitungan detik, sebuah kotak tautan (link situs web) kuning/putih akan melayang di layar HP Anda. Ketuk (klik) tautan tersebut.

B. Memberikan Izin Akses Lokasi (Sangat Penting)
Layar HP Anda akan otomatis membuka aplikasi Browser (seperti Google Chrome atau Safari). 
Saat halaman terbuka, akan muncul kotak peringatan pop-up dari HP Anda yang berbunyi: "Situs ini ingin mengakses Lokasi perangkat Anda". Anda WAJIB menekan tombol "Izinkan" atau "Allow".
Jika Anda salah menekan tombol (Block), maka fitur canggih ini tidak akan mau bekerja.

C. Tombol Verifikasi
Di layar HP Anda sekarang akan terpampang dengan jelas foto alat, nomor inventaris, dan nama ruangannya. 
Di bagian bawah layar tersebut, terdapat satu tombol merah besar bertuliskan "Verifikasi Posisi Saat Ini". Klik tombol merah tersebut.

D. Hasil Validasi Radius GPS (Geofencing)
Begitu tombol merah ditekan, Kehebatan Sistem akan langsung bekerja. Sistem akan mengukur koordinat satelit (GPS) dari HP Anda dan membandingkannya dengan titik koordinat asli pagar rumah sakit.
Jika Posisi Anda VALID (Berada di area RSUD): Layar HP akan menampilkan spanduk warna Hijau bersimbol centang. Ini berarti alat aman berada di dalam jangkauan wajar.
Jika Posisi Anda MENCURIGAKAN (Berada di luar area RSUD): Layar HP akan langsung berubah menjadi Merah menyala! Layar akan menampilkan tulisan peringatan jarak pelanggaran (misal: "Aset terdeteksi 3 KM dari Rumah Sakit!"), dan HP Anda akan bergetar berulang-ulang (Haptic Feedback) sebagai tanda bahaya pencurian.

E. Perekaman Otomatis
Tanpa Anda perlu mengetik apa pun lagi, informasi koordinat (titik persis di peta) beserta cap waktu (jam dan tanggal alat ini terakhir dilihat / Last Seen) akan otomatis dikirim ke server pusat. Jika teknisi IPSRS membuka layar komputer mereka, letak alat di peta akan langsung bergeser menyesuaikan titik Anda berdiri saat itu juga.


<div style='page-break-after: always;'></div>

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


<div style='page-break-after: always;'></div>

Modul Laporan Kerusakan Khusus Pengguna Ruangan (Sisi Pelapor Internal)

Gambar 2B.1. Halaman Daftar Tiket Laporan Milik Ruangan (Pelapor Internal).

Pada Modul 2 sebelumnya, kita telah membahas "Portal Publik" di mana siapa pun bisa melapor tanpa *login*. Namun, bagaimana jika Anda adalah seorang **Kepala Ruangan** (misalnya Kepala IGD) yang ingin memantau seluruh proses perbaikan fasilitas di ruangan Anda secara resmi?
Untuk itulah aplikasi IPSRS memberikan akun resmi dengan hak akses sebagai **"Pelapor"** kepada Anda.

1. Masuk ke Sistem (Login)
Buka halaman awal aplikasi, lalu ketikkan *Username* dan *Password* yang telah diberikan oleh Admin IPSRS kepada Anda.
Setelah menekan tombol Login, kehebatan sistem akan langsung mengarahkan Anda ke halaman khusus bernama **"Laporan Kerusakan (LK)"**. 
Anda tidak akan melihat menu-menu membingungkan seperti stok gudang atau jadwal teknisi, karena layar Anda disederhanakan khusus hanya untuk memantau keluhan ruangan Anda.

2. Memantau Status Perbaikan (Tracking Tiket)

Gambar 2B.2. Tampilan Indikator Status Perbaikan.

Kehebatan Sistem: Anda tidak perlu lagi menelepon IPSRS berkali-kali hanya untuk bertanya "AC saya sudah diperbaiki sampai mana?".
Di halaman Laporan Kerusakan, Anda akan melihat tabel yang memuat seluruh riwayat keluhan dari ruangan Anda. Fokuslah pada kolom **"Status"**.
Status laporan Anda akan berubah-ubah secara otomatis (*real-time*) sesuai dengan apa yang sedang dikerjakan oleh teknisi di lapangan:
- Laporan Masuk (Abu-abu): Laporan Anda sudah diterima, namun belum ada teknisi yang merespons.
- Survei (Kuning): Teknisi sudah mengklaim tiket Anda dan sedang berjalan menuju ruangan Anda untuk melakukan pengecekan.
- Dalam Perbaikan (Biru): Teknisi sedang membongkar atau memperbaiki alat Anda saat ini juga.
- Menunggu Suku Cadang (Oranye): Teknisi menunda perbaikan karena harus memesan atau mengambil onderdil dari gudang.
- Menunggu Pihak Ketiga (Oranye Tua): Kerusakan terlalu parah sehingga IPSRS harus memanggil vendor/mekanik luar untuk memperbaikinya.
- Selesai (Hijau): Perbaikan sudah tuntas dan alat sudah berfungsi normal.

3. Membuat Laporan Kerusakan dari Dalam Sistem (Internal)

Gambar 2B.3. Halaman Formulir Pembuatan Laporan Kerusakan Baru.

Sebagai pengguna terdaftar, Anda juga bisa membuat laporan baru tanpa harus keluar ke portal publik.
A. Membuka Formulir
Pada halaman Laporan Kerusakan, perhatikan pojok kanan atas layar Anda, lalu klik tombol biru bertuliskan "+ Buat LK Baru".

B. Mengisi Formulir Keluhan
Karena Anda sudah *login*, Anda tidak perlu lagi mengetik nama atau unit Anda, sistem sudah mengenalinya secara otomatis!
Aset (Wajib Diisi): Klik kotak dropdown ini, ketik dan cari nama fasilitas/alat yang rusak (Contoh: "AC Split Ruang Perawat").
Lokasi Spesifik: Jelaskan di sudut mana alat itu berada (Contoh: "Di atas meja pendaftaran").
Keluhan (Wajib Diisi): Ceritakan kerusakannya dengan sangat rinci agar teknisi membawa alat perkakas yang tepat (Contoh: "Kipas indoor AC tidak berputar dan ada suara berderit yang sangat keras").
Kirim Laporan: Klik tombol "Simpan" di bagian bawah. Laporan Anda akan seketika masuk ke tabel antrean dan sirine notifikasi akan berbunyi di komputer teknisi IPSRS!


<div style='page-break-after: always;'></div>

Modul Pemeliharaan Preventif (Preventive Maintenance)

Gambar 3.1. Halaman Dashboard Pemeliharaan Preventif (Jadwal PM).

Modul ini adalah buku agenda cerdas bagi teknisi IPSRS. Daripada menunggu alat rusak dan dikeluhkan oleh staf ruangan (perbaikan Kuratif), IPSRS wajib melakukan perawatan rutin terjadwal (Preventif) seperti membersihkan saringan AC atau mengecek voltase panel listrik, agar sarana rumah sakit berumur panjang.

1. Pembuatan Jadwal Preventif
Langkah ini biasanya dilakukan oleh Koordinator IPSRS untuk membagikan tugas pemeliharaan rutin kepada teknisi-teknisinya di awal bulan.

Gambar 3.2. Halaman Formulir Pembuatan Jadwal Baru.

A. Membuka Formulir Jadwal Baru
Di menu navigasi kiri layar Anda, klik menu bernama "Pemeliharaan Preventif".
Di sudut kanan atas halaman tersebut, terdapat tombol biru bertuliskan "+ Tambah Jadwal". Klik tombol itu, lalu sebuah jendela pop-up akan muncul.

B. Mengisi Detail Tugas Pemeliharaan
Aset (Wajib Diisi): Klik kotak pencarian ini lalu ketikkan nama alat yang harus dirawat (Contoh: "AC Split Ruang ICU" atau "Genset Utama"). Pastikan Anda memilih aset dari daftar yang muncul.
Teknisi Penanggung Jawab (Wajib Diisi): Klik kotak pilihan (dropdown) ini dan pilih nama teknisi yang diberi tugas (Contoh: "Budi Santoso"). Hanya teknisi yang namanya dipilih yang akan bertanggung jawab atas alat ini.
Tanggal & Jam (Wajib Diisi): Tentukan kapan tugas ini harus dilaksanakan. (Contoh: Tanggal 15 Oktober 2026, Jam 09:00). 
⚠️ Peringatan Keras: Sistem sangat disiplin. Anda dilarang memasukkan tanggal dan jam yang sudah berlalu/kedaluwarsa. Sistem otomatis akan menolak jadwal yang dibuat di masa lalu (backdate).

C. Menyimpan Jadwal
Klik tombol "Simpan" di bagian bawah.
Jadwal tugas tersebut sekarang akan berjejer rapi di dalam tabel halaman utama, lengkap dengan status abu-abu bertuliskan "Belum" dikerjakan.

2. Eksekusi Lembar Kerja Preventif (LKP)
Saat hari-H tiba, teknisi (misalnya Budi) harus pergi ke lokasi alat, melakukan perawatan (seperti membersihkan debu atau mengukur tegangan listrik), lalu melaporkan hasilnya langsung lewat sistem. 

Gambar 3.3. Halaman Pengisian Formulir LKP (Lembar Kerja Preventif).

A. Membuka Lembar LKP
Cari jadwal tugas Anda di tabel Pemeliharaan Preventif. Jika Anda terlambat mengerjakannya dari tanggal yang ditentukan, sistem akan memberikan teguran dengan mengubah status baris tersebut menjadi Merah menyala ("Terlambat").
Di ujung kanan baris tugas Anda, klik tombol hijau bergambar kertas bertuliskan "Isi LKP". 
Anda akan masuk ke dalam halaman formulir raksasa semacam "Checklist" (Daftar Periksa).

B. Mengisi Kategori & Kehebatan Sistem (Auto-Template)
Kategori Alat (Wajib Diisi): Ketikkan jenis alat tersebut (Contoh: "Pendingin Ruangan / AC").
Kehebatan Sistem (Mesin Pembelajar): Saat Anda mengetikkan Kategori, keajaiban akan terjadi. Sistem akan langsung memunculkan tabel berisi deretan komponen apa saja yang wajib Anda periksa secara spesifik untuk kategori AC (misal: "Cuci Filter", "Cek Tekanan Freon", "Cek Pipa Drainase"). 
Bagaimana jika komponennya belum ada? Cukup ketik saja nama komponen baru (Misal: "Cek Kelistrikan AC") di baris kosong yang disediakan. Begitu Anda menekan tombol simpan nanti, sistem otomatis "mempelajari" dan merekam komponen baru Anda secara permanen. Sehingga bulan depan, jika ada teknisi lain merawat AC, tulisan "Cek Kelistrikan AC" tersebut sudah langsung muncul di tabel *checklist* miliknya!

C. Mengisi Hasil Inspeksi Per Komponen
Anda harus menilai setiap komponen di tabel:
- Jika jenisnya Inspeksi (Pengamatan mata): Pilih "Baik" atau "Rusak".
- Jika jenisnya Pengukuran: Ketikkan angka alat ukur Anda (Contoh: ketik "220" pada komponen Voltase, lalu isi kolom Satuan dengan "Volt").

D. Menentukan Hasil Akhir (Sangat Krusial)
Di bagian atas formulir, Anda diwajibkan menyimpulkan kondisi keseluruhan alat tersebut di kotak dropdown "Hasil Pemeriksaan Akhir". Anda berhadapan dengan dua pilihan besar:
- Pilihan 1: "Laik Pakai / Normal" (Pilih ini jika perawatan berjalan lancar, alat dicuci bersih, dan berfungsi baik).
- Pilihan 2: "Perlu Perbaikan" (Pilih ini jika saat Anda merawat AC, Anda menemukan kerusakan parah seperti kompresor mati yang tidak bisa Anda selesaikan saat itu juga).

3. Tindak Lanjut Otomatis (Auto-Generate Tiket Kuratif)
Langkah ini menjelaskan apa yang terjadi saat Anda menekan tombol "Simpan LKP" di paling bawah formulir.

Skenario A (Jika Anda memilih "Laik Pakai"):
Sistem akan menyimpan laporan *checklist* Anda, mengubah status jadwal tugas Anda menjadi Hijau ("Selesai"), dan mengembalikan Anda ke halaman utama dengan ucapan sukses. Tugas Anda beres!

Skenario B (Jika Anda memilih "Perlu Perbaikan"):
Kehebatan Sistem: Karena alat tersebut ternyata rusak dan butuh turun mesin atau butuh penggantian *sparepart*, sistem TIDAK akan membiarkan masalah ini menggantung.
Begitu Anda mengeklik Simpan, sistem akan melakukan *Auto-Generate* (membuat otomatis) sebuah Tiket Laporan Kerusakan (LK) baru!
Anda akan langsung "dilempar" (diarahkan) masuk ke dalam halaman Tiket Perbaikan Kuratif. Sistem akan membuatkan Nomor Tiket baru, menyalin nama AC tersebut, dan menyalin catatan Anda menjadi "Temuan Preventif".
Di halaman perbaikan inilah, Anda baru diizinkan oleh sistem untuk meminta Suku Cadang dari gudang atau meminta bantuan Vendor Pihak Ketiga (Sesuai dengan cara kerja pada Panduan Modul 2).


<div style='page-break-after: always;'></div>

Modul Manajemen Suku Cadang (Gudang & Inventori)

Gambar 4.1. Halaman Daftar Suku Cadang dan Stok Gudang.

Modul ini adalah buku besar (buku stok) digital bagi teknisi IPSRS. Di sini Anda mendata seluruh onderdil, bahan bangunan, maupun *sparepart* (Suku Cadang) yang tersimpan di dalam gudang. Modul ini terhubung langsung secara ajaib dengan Modul Laporan Kerusakan, sehingga stok Anda akan selalu *update* (terbaru) tanpa perlu Anda hitung manual setiap hari.

1. Menambahkan Daftar Barang Baru ke Gudang
Langkah ini HANYA Anda lakukan jika IPSRS membeli **jenis onderdil baru** yang belum pernah terdaftar sama sekali di gudang (Misal: Rumah sakit baru pertama kali membeli "Freon R32").

Gambar 4.2. Jendela Formulir Tambah Barang Baru.

A. Membuka Formulir Barang Baru
Di sebelah kiri layar komputer Anda, cari dan klik menu bernama "Suku Cadang" (atau "Stok").
Perhatikan pojok kanan atas layar Anda, terdapat tombol berwarna biru/hijau bertuliskan "+ Tambah Barang". Klik tombol tersebut.
Sebuah jendela kecil (pop-up) akan muncul di tengah layar Anda.

B. Mengisi Informasi Barang
Nama Barang (Wajib Diisi): Ketikkan nama murni dari onderdil tersebut secara jelas (Contoh: "Freon R32" atau "Kabel Roll 50m").
Kategori (Wajib Diisi): Pilih dari kotak pilihan (dropdown) ke kelompok mana barang ini masuk (Contoh: Pilih "Kelistrikan", "Pipa & Saluran", atau "Suku Cadang AC").
Satuan (Wajib Diisi): Ketikkan satuan hitung barang tersebut agar stok tidak membingungkan (Contoh ketikan: "Tabung", "Meter", "Buah", atau "Liter").
Stok Minimum (Wajib Diisi Angka): Ini adalah fitur peringatan dini! Ketikkan angka batas bahaya. (Contoh: Anda mengetik angka "5"). Artinya, sistem akan langsung berteriak memberi peringatan merah jika sisa Freon di gudang tinggal 5 tabung.
Keterangan (Opsional): Ketikkan catatan tambahan jika perlu (Contoh: "Khusus untuk AC Daikin").

C. Menyimpan Barang
Klik tombol "Simpan". Barang tersebut sekarang sudah terdaftar di rak digital Anda, namun **stoknya masih 0 (Nol)**. Anda harus memasukkan jumlah barangnya melalui fitur Restok (Catat Masuk) di langkah selanjutnya.

2. Restok Barang (Mencatat Barang Masuk / Kulakan)
Langkah ini Anda lakukan setiap kali ada truk *supplier* datang mengantarkan onderdil baru ke gudang IPSRS.

Gambar 4.3. Jendela Formulir Catat Barang Masuk.

A. Membuka Formulir Barang Masuk
Masih di halaman "Suku Cadang", tepat di sebelah tombol "Tambah Barang" tadi, terdapat tombol bertuliskan "+ Catat Masuk (Restok)". Klik tombol tersebut.

B. Mengisi Bukti Barang Masuk
Nama Barang (Wajib Diisi): Klik kotak pencarian ini, ketik dan cari barang yang baru saja datang (Contoh: "Freon R32").
Jumlah (Wajib Diisi): Ketikkan berapa banyak barang yang datang (Contoh: Ketik "20").
Tanggal Masuk (Wajib Diisi): Pilih tanggal hari ini.
Nomor Dokumen/Faktur (Sangat Penting): Ketikkan nomor resi, nomor kuitansi, atau nomor Surat Jalan dari *supplier*. Ini wajib diisi sebagai bukti bahwa barang masuk secara sah (Audit).
Keterangan: Ketikkan asal usul barang (Contoh: "Pembelian dari Toko Jaya Abadi").

C. Memasukkan ke Gudang
Klik tombol "Simpan Transaksi". 
Sistem akan memuat sejenak. Jika tadinya stok Freon Anda adalah 0, maka secara otomatis akan berubah menjadi 20 Tabung!

3. Pengurangan Stok Otomatis (Barang Keluar)

Kehebatan Sistem: Anda TIDAK PERLU mencatat "Barang Keluar" secara manual setiap kali teknisi mengambil obeng atau freon untuk memperbaiki kerusakan di ruangan pasien!

A. Integrasi dengan Laporan Kerusakan
Sistem ini dirancang sangat cerdas. Pengurangan stok (*Barang Keluar*) terjadi secara otomatis di belakang layar saat Teknisi mengisi **Modul 2 (Laporan Kerusakan)**.
Ingatkah Anda saat teknisi memperbaiki AC bocor di Modul 2? Saat teknisi tersebut mengetikkan bahwa ia menghabiskan "1 Tabung Freon R32" di formulir penyelesaian perbaikan AC, sistem akan diam-diam berlari ke gudang digital ini.

B. Pemotongan Otomatis & Catatan Riwayat
Sistem akan **langsung memotong** stok Freon Anda dari 20 Tabung menjadi 19 Tabung!
Luar biasanya, jika Anda membuka tab/halaman "Riwayat Transaksi", Anda akan melihat sistem menuliskan catatan otomatis: "Freon R32 keluar sebanyak 1 Tabung. Digunakan untuk perbaikan Laporan Kerusakan nomor tiket PR202610-001".
Hal ini membuat pertanggungjawaban aset (Audit) menjadi sangat transparan, karena setiap barang yang keluar pasti jelas menempel pada tiket kerusakan ruangan mana.

4. Monitoring Peringatan Stok Menipis (Alert)

Gambar 4.4. Tampilan Dashboard dengan Peringatan Stok Kritis (Merah).

A. Peringatan Dini di Halaman Utama (Dashboard)
Ingat angka "Stok Minimum" yang Anda ketik di Langkah 1? Jika freon terus-terusan dipakai oleh teknisi hingga stoknya menyentuh angka 5 atau di bawahnya, sistem tidak akan diam saja.
Saat Kepala IPSRS atau teknisi membuka halaman awal (Dashboard) komputer mereka di pagi hari, layar akan memunculkan spanduk Peringatan Kritis berwarna Merah/Kuning menyala.

B. Tindakan Lanjut
Peringatan tersebut akan berbunyi: "Peringatan! Stok Freon R32 Menipis (Tersisa 5 Tabung)".
Tepat di sebelah tulisan itu, terdapat tombol pintasan "Restok Sekarang". Tombol ini akan memudahkan Kepala IPSRS untuk segera menelpon *supplier* agar memesan freon baru sebelum gudang benar-benar kehabisan stok, sehingga pelayanan perbaikan rumah sakit tidak pernah terhambat!


<div style='page-break-after: always;'></div>

Modul Peminjaman & Penghapusan (Manajemen Khusus)

Gambar 5.1. Halaman Daftar Peminjaman Aset.

Selain memindahkan barang atau merawat barang, IPSRS terkadang harus menghadapi situasi khusus, seperti ruangan meminjam kipas angin ekstra, atau AC yang sudah benar-benar hancur dan harus dibuang. Modul ini digunakan untuk mencatat siklus hidup aset dari peminjaman hingga "kematian" aset.

1. Peminjaman Aset (Pinjam Pakai)
Gunakan menu ini jika ada unit/ruangan yang ingin meminjam sarana IPSRS secara sementara.

Gambar 5.2. Formulir Tambah Peminjaman.

A. Mencatat Peminjaman Baru
Buka menu "Peminjaman Aset" di navigasi sebelah kiri (berada di bawah kelompok "Inventaris & Lokasi").
Klik tombol "+ Pinjam Baru" di pojok kanan atas.
Pada kotak pencarian (Pilih Aset), cari dan pilih sarana yang akan dipinjamkan (Contoh: "Kipas Angin Berdiri").
Pada kolom Peminjam/Ruangan (Wajib Diisi), ketikkan nama orang atau ruangan yang meminjam (Contoh: "Ruang Rapat Utama").
Pada kolom Tanggal Pinjam (Wajib Diisi), tentukan tanggal barang diserahkan.
Klik "Simpan Peminjaman". Sistem akan mengubah status Kipas Angin tersebut menjadi "Dipinjam", sehingga teknisi lain tidak kebingungan mencari barang tersebut di gudang.

B. Mengembalikan Barang (Selesai Pinjam)
Jika acara rapat sudah selesai dan Kipas Angin dikembalikan, Anda wajib menutup catatan ini.
Cari baris peminjaman Kipas Angin tadi, lalu klik tombol "Kembalikan" (ikon centang).
Sistem akan mencatat Tanggal Kembali secara otomatis dan merubah status kipas angin kembali "Tersedia".

2. Penghapusan Aset (Afkir / Pemusnahan)
Ini adalah menu yang sangat krusial. Jika ada AC atau Genset yang sudah rusak parah, tidak bisa diperbaiki lagi, dan harus dilelang, dibuang, atau dihibahkan, Anda DILARANG menghapusnya begitu saja dari database! Gunakan menu ini agar terdapat Berita Acara (BA) pemusnahan yang sah.

Gambar 5.3. Formulir Penghapusan Aset dan Nomor BA.

A. Proses Penghapusan / Pemutihan
Buka menu "Penghapusan Aset".
Klik tombol "+ Hapus Aset (Afkir)".
Cari sarana yang rusak parah (Contoh: "AC Split Ruang ICU").
Nomor Berita Acara (Wajib Diisi): Ketikkan nomor surat resmi persetujuan direktur/pimpinan untuk menghapus barang ini. Ini membuktikan bahwa hilangnya barang dilakukan secara legal.
Metode Penghapusan (Wajib Diisi): Pilih dari dropdown apakah barang ini "Dihancurkan", "Dijual/Dilelang", atau "Dihibahkan".
Klik "Proses Penghapusan".
⚠️ Peringatan Keras: Setelah Anda mengeklik ini, sarana tersebut akan dicoret dari daftar inventaris aktif IPSRS selama-lamanya. Namun datanya tetap abadi di tabel ini sebagai sejarah/arsip.

3. Kanibal Alat (Mempreteli Suku Cadang)
Kanibal adalah istilah lapangan saat teknisi mencabut onderdil dari mesin yang sudah mati total (Aset Donor), untuk dipasangkan ke mesin lain yang masih hidup namun rusak komponennya (Aset Penerima).

Gambar 5.4. Halaman Riwayat Pencabutan Kanibal.

A. Mengakses Fitur Kanibal
Menu "Kanibal Alat" terletak di bawah kelompok navigasi "Pemeliharaan".

B. Syarat Melakukan Kanibal
Kehebatan Sistem: Anda tidak bisa sembarangan mencabut alat! Proses Kanibal hanya bisa dilakukan dan disahkan saat Anda sedang berada di dalam halaman penyelesaian Modul 2 (Laporan Kerusakan). 
Di dalam formulir perbaikan tiket LK, terdapat tombol khusus "+ Ambil Suku Cadang dari Alat Lain (Kanibal)".

C. Mencatat Proses Kanibalisme
Saat Anda mengeklik tombol tersebut, formulir akan muncul.
Pilih Aset Donor: Pilih alat mana yang sudah hancur/mati yang akan dipreteli (Contoh: "Pompa Air Lama Gudang").
Komponen yang Dicabut: Ketikkan apa yang Anda ambil (Contoh: "Dinamo Motor 2HP").
Alat Penerima: (Otomatis terisi dengan alat yang sedang Anda perbaiki saat ini).
Setelah disimpan, laporan ini akan menempel pada Riwayat Kanibal. Ini adalah cara IPSRS menyelamatkan anggaran rumah sakit dengan memanfaatkan bangkai mesin lama secara resmi dan tercatat!


<div style='page-break-after: always;'></div>

Modul Administrator & Laporan (Data Master)

Gambar 6.1. Halaman Daftar Pengguna (Manajemen Akun).

Modul ini khusus digunakan oleh Kepala IPSRS atau Administrator Sistem. Di bagian inilah segala aturan, klasifikasi, daftar pengguna, hingga pelaporan kinerja (Reporting) diatur. Menunya berada di paling bawah pada barisan navigasi (Sidebar) dengan tajuk "Sistem".

1. Data Pengguna (Manajemen Akun)
Di sini Admin dapat membuat, mengubah, atau mencabut hak akses seseorang ke dalam aplikasi IPSRS.

A. Menambahkan Akun Baru
Buka menu "Data Pengguna".
Klik tombol "+ Tambah Pengguna".
Isikan nama lengkap (Contoh: "dr. Andi" atau "Bapak Teknisi"), Username untuk login (Contoh: "andi123"), dan Password (Kata sandi).
Peran / Role (Sangat Penting): Pilih hak akses untuk orang tersebut:
- Admin: Memiliki akses ke seluruh menu tanpa batasan.
- Teknisi: Hanya bisa melihat jadwal kerja mereka, menerima tiket kerusakan, dan mengelola perbaikan.
- Pelapor: (Akun khusus staf ruangan, meskipun sistem kita sudah mendukung pelaporan tanpa login via Portal Publik).

2. Kategori Aset & Kode Kerusakan (Klasifikasi Dasar)
Menu ini berguna agar IPSRS memiliki keseragaman bahasa/standar dalam menamai kerusakan atau menamai barang.

Gambar 6.2. Halaman Daftar Kode Kerusakan Standar.

A. Kategori Aset
Gunakan menu ini untuk menambah kelompok alat baru. Misalnya IPSRS baru saja mengambil alih urusan IT, maka Admin bisa menambahkan Kategori "Informatika / Komputer". Hal ini akan mempermudah penyaringan data saat mencari aset di Modul 1.

B. Kode Kerusakan
Alih-alih teknisi mengetik "Rusak parah" atau "Mati total" dengan gaya bahasa yang beda-beda, Admin bisa membakukan keluhan.
Buka menu "Kode Kerusakan". Tambahkan kode standar, misalnya:
- "K-01": Mesin Mati Total
- "K-02": Kebocoran Pipa
- "K-03": Korsleting Listrik (Panel)
Nantinya, kode-kode ini tinggal dipilih oleh teknisi saat mereka mengisi laporan (Tinggal klik, tidak usah mengetik panjang).

3. Data Vendor (Rekanan Pihak Ketiga)
Terkadang IPSRS tidak bisa memperbaiki semua alat sendirian. Genset raksasa mungkin butuh didatangkan mekanik dari luar (pihak ketiga).

A. Mendaftarkan Vendor
Buka menu "Data Vendor".
Klik "+ Tambah Vendor".
Masukkan Nama Perusahaan (Contoh: "PT. Maju Mundur AC"), Kontak/Nomor Telepon, dan Alamatnya.
Jika sewaktu-waktu AC Sentral rusak parah (di Modul 2), teknisi tinggal mencari dan memilih nama vendor ini dari daftar *dropdown*, dan sistem akan mencatatkan Invoice (Tagihan) perbaikan tersebut atas nama perusahaan ini.

4. Laporan & Analitik (Reporting / Export)

Gambar 6.3. Halaman Pusat Laporan (Reporting Center).

Ini adalah senjata utama Kepala IPSRS saat ditanya oleh Direktur Rumah Sakit: "Berapa banyak AC yang rusak bulan ini? Dan berapa lama teknisi meresponsnya?"

A. Mengambil Laporan Kinerja
Buka menu "Laporan" (berikon grafik).
Pilih rentang tanggal (Contoh: 1 Oktober hingga 31 Oktober).
Pilih Jenis Laporan:
- Laporan Kerusakan: Untuk melihat berapa tiket keluhan yang masuk dan berapa yang sukses diperbaiki (Menghitung performa kecepatan/Response Time teknisi).
- Laporan Preventif: Untuk melihat apakah jadwal cuci AC dan cek Genset dijalankan tepat waktu atau banyak yang "Terlambat".
- Laporan Mutasi & Stok: Untuk melihat pergerakan inventaris dan pemakaian suku cadang bulanan.

B. Cetak ke PDF / Excel
Setelah laporan tampil di layar, Kepala IPSRS cukup menekan tombol "Export ke Excel" atau "Cetak PDF".
Sistem akan otomatis merapikan tabel tersebut menjadi format yang profesional dan siap di-print untuk dilampirkan pada rapat bulanan direksi!


<div style='page-break-after: always;'></div>


