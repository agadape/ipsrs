# Technical Deployment, Recovery, and Rollback Checklist

Jalankan pada environment hosting yang akan dipakai demo/final release. Jangan mengubah database saat checklist ini.

## Runtime dan dependency

1. Pastikan PHP **8.2+ 64-bit**. Release lock diuji dengan Composer platform `8.2.0` dan tidak mensyaratkan PHP 8.3.
2. Pastikan extension aktif: `intl`, `mbstring`, `fileinfo`, `gd`, `dom`, `xml`, `xmlreader`, `xmlwriter`, `simplexml`, `zlib`, dan terutama **`zip`**.
3. Pastikan source menggunakan release lock yang memuat CodeIgniter `4.7.4`, PhpSpreadsheet `5.10.0`, dan ZipStream `3.1.2`.
4. Jalankan dari root project:

   ```sh
   php -m | grep zip
   composer install --no-dev --optimize-autoloader
   composer validate --no-check-publish
   composer audit --locked
   php spark routes
   ```

Jangan menyalin `vendor/` dari mesin development atau menjalankan `composer update` di server. Instal tepat dari `composer.lock` dengan `composer install`.

5. Pastikan audit tidak menampilkan advisory. Lock sekarang menargetkan CodeIgniter 4.7.4 dan PhpSpreadsheet 5.10.0.

6. Jalankan preflight read-only:

   ```sh
   php scripts/production_preflight.php
   ```

   Deployment hanya boleh dilanjutkan jika seluruh baris otomatis `PASS`. Baris `MANUAL` harus diberi evidence terpisah. Script tidak membuka atau mengubah database.

## Konfigurasi aman

1. Atur `CI_ENVIRONMENT = production` pada hosting; jangan deploy `.env` lokal yang masih `development`.
2. Set `app.baseURL` ke URL HTTPS hosting yang sebenarnya.
3. Set `app.forceGlobalSecureRequests = true`, `cookie.secure = true`, `cookie.httponly = true`, dan `cookie.samesite = Lax`.
4. Verifikasi koneksi database memakai user khusus aplikasi, bukan akun root, dan set `database.default.DBDebug = false`.
5. Pastikan document root domain menunjuk tepat ke `public/`, bukan root repository.
6. Pastikan `writable/` dapat ditulis oleh web server, tetapi tidak dapat diakses langsung dari web.
7. Pastikan direktori upload BA berada di `writable/uploads` dan hanya tersedia melalui download endpoint yang diautorisasi.
8. Pastikan `public/` hanya memiliki front controller PHP `index.php`; debug probe seperti `check.php` dilarang.
9. Simpan backup database dan `writable/uploads` sebelum deploy, lalu lakukan restore test pada target non-produktif.

## Backup dan restore drill

1. Buat database staging kosong dengan versi MySQL/MariaDB yang setara hosting.
2. Buat dump memakai fasilitas backup cPanel/phpMyAdmin atau `mysqldump`. Jangan menulis password langsung pada command history.
3. Simpan checksum, waktu backup, ukuran file, versi database, dan build aplikasi.
4. Restore dump ke database staging, bukan database production aktif.
5. Cocokkan jumlah record penting: pengguna, aset, series, LK, jadwal, LKP, stok, ledger, kanibal, peminjaman, mutasi, dan penghapusan.
6. Jalankan login serta satu read-only smoke flow dari setiap modul.
7. Catat hasil pada `docs/PRODUCTION_RELEASE_RECORD.md`.

Untuk membuktikan mekanisme recovery lokal tanpa menyentuh data aplikasi:

```sh
php scripts/backup_restore_dry_run.php
```

Hasil ini hanya membuktikan file SQLite disposable; ia tidak menggantikan restore MySQL hosting.

## Deploy dan rollback

1. Catat commit/build, waktu maintenance, operator, backup database, dan backup upload.
2. Deploy source serta dependency lock yang sama dengan build yang diuji.
3. Jalankan preflight dan smoke proof sebelum membuka akses pengguna.
4. Jika preflight, migration, atau smoke test gagal, hentikan traffic ke build baru.
5. Pulihkan source release sebelumnya dan konfigurasi environment sebelumnya.
6. Restore database hanya jika deployment benar-benar mengubah schema/data dan rollback schema tidak cukup. Jangan restore otomatis untuk defect tampilan.
7. Verifikasi jumlah record dan workflow read-only setelah rollback.
8. Catat penyebab, tindakan, dan hasil rollback pada release record.

## Smoke proof setelah deploy

1. Login Admin → buka laporan → export Excel. File `.xlsx` harus berhasil diunduh dan dibuka.
2. Buka satu route internal tanpa login; harus diarahkan ke login.
3. Jalankan UAT di dokumen workflow yang relevan tanpa memakai data operasional penting.
4. Catat tanggal, URL hosting, versi PHP, status `zip`, hasil Composer audit, dan hasil export pada catatan release.
5. Pastikan `/check.php` dan file debug lain menghasilkan 404.
6. Pastikan header `X-Frame-Options`, `X-Content-Type-Options`, dan `Referrer-Policy` muncul.
7. Pastikan cookie session/CSRF memiliki `Secure`, `HttpOnly`, dan `SameSite` yang sesuai.

## Operasional minimum

1. Pastikan `writable/logs` tidak dapat diakses publik dan memiliki kebijakan rotasi/retention.
2. Pantau kapasitas disk, error HTTP 5xx, kegagalan login berulang, dan kegagalan database.
3. Tetapkan PIC yang dapat menjalankan rollback dan restore.
4. Jangan menyimpan dump, `.env`, log, atau dokumen BA di document root.
5. Ulangi backup/restore drill setelah perubahan schema besar.

## Batas

Checklist ini tidak menggantikan UAT workflow, MySQL concurrency test, atau acceptance pengguna. Ia membuktikan runtime, konfigurasi, recovery, dan dependency release hanya pada evidence yang benar-benar dicatat.
