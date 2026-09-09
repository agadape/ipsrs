# Panduan Gaya Penulisan (Style Guide) User Guide Book IPSRS

Dokumen ini merupakan acuan gaya penulisan (*writing style*) yang wajib digunakan untuk menyusun seluruh modul buku panduan IPSRS. Gaya penulisan ini diadaptasi dari dokumentasi SIM-DIKLAT yang sudah ada, dengan ciri khas yang sangat detail, ramah pengguna, dan berorientasi pada tata letak antarmuka (UI).

## 1. Asumsi Pengguna Awam (Extreme Detail)
*   **INGAT: Ini adalah buku panduan untuk *User* (Pengguna Akhir).** Mereka tidak ikut membuat aplikasi ini dan sangat mungkin buta teknologi (Gaptek).
*   Jangan pernah menggunakan instruksi singkat seperti "Isi formulir lalu simpan". 
*   Jelaskan secara spesifik **di mana letak tombolnya** (misal: "di pojok kanan atas", "di navigasi sebelah kiri"), **apa warna tombolnya**, dan **apa bentuk ikonnya**.
*   Jelaskan setiap fungsi kolom di dalam formulir satu per satu dengan detail. Jika ada *dropdown*, sebutkan itu *dropdown* dan berikan contoh pilihannya.

## 2. Batasan Contoh Aset (HANYA ALAT IPSRS / SARPRAS)
*   **DILARANG KERAS** menggunakan contoh alat medis/elektromedik (seperti Tensi Darah, Patient Monitor, EKG, dsb). Hal ini karena wewenang IPSRS berfokus pada Sarana dan Prasarana umum rumah sakit. Jika menggunakan alat medis, ruang lingkup (scope) panduan akan menjadi terlalu luas dan salah sasaran.
*   **Gunakan contoh alat sarpras/fasilitas:** AC Split, Mesin Genset, Pompa Air, Instalasi Listrik, Lift/Elevator, Meja/Kursi Kantor, Kipas Angin, dsb.

## 3. Struktur Halaman & Gambar Placeholder
*   Selalu gunakan format *placeholder* gambar di bawah judul utama atau sub-judul yang merujuk ke halaman baru. 
*   **Format:** `Gambar [Nomor Bab].[Nomor Urut]. [Deskripsi Halaman].`
*   *Contoh:* `Gambar 1.1. Halaman Daftar Master Aset.`

## 4. Format Penjelasan Tombol dan Fitur (Bold & Titik Dua)
*   Setiap kali menjelaskan sebuah tombol, kolom input, atau fitur spesifik, tuliskan nama fiturnya dengan **huruf tebal (Bold)** diikuti titik dua, lalu penjelasannya.
*   *Contoh:* **Nama Aset:** Ketikkan nama murni dari sarana tersebut (Contoh: AC Split 1 PK).

## 5. Gaya Bahasa (Tone of Voice)
*   **Ramah & Membimbing:** Gunakan kata ganti "Anda" untuk Admin/Pengguna. Gunakan penjelasan logis untuk menjelaskan *mengapa* sebuah fitur dibuat (Contoh: "Fitur ini luar biasa penting! Nantinya...").
*   **Edukasi UI:** Jelaskan istilah teknis UI dengan bahasa awam (Contoh: *dropdown* dijelaskan sebagai "kolom pilihan", *pop-up* dijelaskan sebagai "jendela peringatan yang muncul di tengah layar").

## 6. Kotak Peringatan & Keunggulan Sistem (Highlighting)
*   Gunakan penanda khusus untuk menarik perhatian pembaca pada konsekuensi fatal atau fitur otomatis dari sistem:
    *   **⚠️ Peringatan Keras:** (Untuk aksi destruktif seperti Hapus Data, atau aturan wajib).
    *   **Catatan Penting:** (Untuk aturan main sistem).
    *   **Kehebatan Sistem / Otomatis:** (Untuk menjelaskan apa yang dikerjakan sistem di latar belakang, seperti *Auto-Generate*, agar pengguna merasa tenang).

## 7. Tata Cara Menulis Langkah (Step-by-Step)
*   Gunakan penomoran (1, 2, 3...) atau urutan abjad (A, B, C...) untuk membedakan alur kerja yang berurutan. Pandu pembaca seolah-olah Anda berdiri di sebelah mereka dan menunjuk layarnya.
