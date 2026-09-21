# Route Security Inventory

Tanggal pemeriksaan: **18 September 2026**

## Ringkasan

- `php spark routes` memuat **77 route eksplisit**; improved auto-routing tidak digunakan untuk menambah endpoint aplikasi.
- Seluruh route internal berawalan `/ipsrs` berada di group `auth`, kecuali dua endpoint publik yang memang disengaja: scan QR dan telemetry ping.
- Semua mutation aplikasi menggunakan `POST`; tidak ditemukan mutation bisnis melalui `GET`.
- Filter CSRF mencakup `/ipsrs/*`, login, logout, dan portal laporan.
- `secureheaders` diterapkan pada seluruh response.
- File debug `public/check.php` dihapus; contract test membatasi PHP pada document root menjadi `public/index.php` saja.

## Public surface

| Route | Method | Tujuan | Guard |
| --- | --- | --- | --- |
| `/` | GET | Redirect ke aplikasi | Internal destination tetap melalui auth |
| `/lapor` | GET/POST | Portal pelaporan | CSRF, validation; UAT/rate limit portal tetap terpisah |
| `/lapor/sukses` | GET | Konfirmasi generik | Tidak memuat detail internal |
| `/login` | GET/POST | Autentikasi | CSRF, generic error, 5 attempts/account dan 20 attempts/IP per 60 seconds |
| `/register` | GET/POST | Tidak tersedia | Route publik sudah dihapus; request menghasilkan 404 |
| `/logout` | POST | Mengakhiri session | CSRF |
| `/ipsrs/aset/scan/{id}` | GET | Detail minimum QR | Payload minimum; device UAT pending |
| `/ipsrs/aset/{id}/ping` | POST | Telemetry GPS | CSRF, validation, server-side rate guard; bukan bukti lokasi fisik |

## Internal authorization boundary

`AuthFilter` memverifikasi session terhadap user database pada setiap request internal, menolak akun nonaktif/role tidak dikenal, lalu menerapkan batas role:

- Pelapor hanya dapat memasuki resource LK; object policy tetap membatasi record milik sendiri.
- Teknisi tidak dapat memasuki pengguna, vendor master, kategori, kode kerusakan, kanibal, penghapusan, laporan, atau peminjaman.
- Teknisi hanya membaca aset/stok dan hanya melakukan mutation LK atau submit LKP miliknya.
- Admin dapat memasuki seluruh module; controller tetap menerapkan state/object rule.

## Evidence otomatis

- `AuthFilterHttpTest`: anonymous denial, inactive-session revocation, role denial, cross-technician LK/LKP denial, CSRF-valid mutation denial, terminal LK immutability, dan BA direct-download denial.
- `AuthLoginHttpTest`: login berhasil, inactive/unknown generic error, rate limit, dan security headers.
- `ProductionSurfaceTest`: tidak ada PHP debug probe dalam document root dan storage BA berada di luar public root.
- `php spark routes`: route/filter compilation.

## Batas yang belum tertutup

- Matrix ini membuktikan konfigurasi source dan representative request-level paths, bukan seluruh interaksi browser.
- Ownership lama masih memakai nama display pada sejumlah record sampai migration actor-ID dikerjakan.
- Token regeneration/back/refresh, reverse-proxy HTTPS, dan header dari web server hosting tetap memerlukan smoke test deployment.
