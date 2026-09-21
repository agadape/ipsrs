# Production Readiness Evidence — Iteration 01Y

Tanggal: **18 September 2026**

Dokumen ini mencatat hasil 11 pekerjaan production-readiness yang diotorisasi. `Done` berarti pekerjaan lokal selesai dengan evidence; `External` berarti bukti tidak dapat dibuat secara sah tanpa browser/perangkat/hosting/MySQL production-equivalent.

| No. | Pekerjaan | Status | Evidence / hasil |
| ---: | --- | --- | --- |
| 1 | Sinkronisasi finding | Done | Matrix F01/F18/F23/F26/F35/F36 diperbarui tanpa menaikkan finding eksternal menjadi Verified |
| 2 | Production config/security audit | Done local | 23 PASS, 5 FAIL, 3 MANUAL; gap lokal dipetakan eksplisit |
| 3 | Route mutation dan RBAC/IDOR audit | Done local | 77 explicit routes; internal group auth; CSRF mutation; representative request-level denial; route inventory dibuat |
| 4 | Auth/error automated tests | Done | Generic inactive/unknown error, rate limit, session role, security headers, public surface |
| 5 | Deployment/rollback/backup runbook | Done | `TECHNICAL_DEPLOYMENT_CHECKLIST.md` dan release-record template |
| 6 | Backup–restore dry run | Done local | Disposable SQLite 3-row restore dan digest PASS; tidak membuka DB aplikasi |
| 7 | Performance measurement | Done local | 20k LK: load-all 38.082 ms; SQL page 2.224 ms; ratio 17.1x |
| 8 | Browser/E2E | Done limited / External workflow | Chrome headless merender DOM login; authenticated business-flow E2E dan user acceptance tetap manual |
| 9 | Hosting preflight read-only | Done tool / External run | `scripts/production_preflight.php`; harus dijalankan ulang di hosting |
| 10 | Requirement traceability | Done local | Traceability, route inventory, context, audit, plan, dan backlog diselaraskan |
| 11 | Final local release gate | Done | PHPUnit 115/296, routes, Composer validation, advisory audit PASS |

## Security changes proven

- `public/check.php` yang membuka koneksi root dan mencetak schema telah dihapus.
- Public PHP surface dibatasi oleh regression test menjadi `public/index.php`.
- Login throttle: 5 attempts/account dan 20 attempts/IP per 60 seconds, termasuk credential-spray proof.
- Unknown dan inactive account menerima error credential generik yang sama.
- Login sukses meregenerasi session dan menghapus bucket throttle.
- Session regeneration menghancurkan record ID lama.
- Security headers global diuji.
- Default DB config tidak lagi memilih user root/database aplikasi tanpa environment.
- Route registrasi publik yang tidak termasuk scope sudah dihapus.

## Local production preflight

PASS: PHP 8.3, extension utama selain zip, force HTTPS config, HttpOnly/SameSite, DB debug off, writable directories, public PHP surface, dan BA storage isolation.

FAIL pada environment lokal yang memang development:

1. `ext-zip` tidak tersedia.
2. `CI_ENVIRONMENT=development`.
3. Base URL memakai HTTP localhost.
4. Secure cookie nonaktif pada HTTP lokal.
5. Local DB user masih root melalui `.env` lokal.

MANUAL: document-root hosting, deployed HTTPS/header behavior, production-equivalent MySQL restore/concurrency.

## HTTP smoke lokal

- `GET /login` → 200 dengan `X-Frame-Options: SAMEORIGIN`, `X-Content-Type-Options: nosniff`, dan `Referrer-Policy: same-origin`.
- Anonymous `GET /ipsrs` → 302 ke login.
- `GET /check.php` → 404.

Redirect internal menggunakan base URL `.env` (`localhost:8080`) walaupun smoke server sementara memakai port 8099. Ini mempertegas bahwa base URL harus cocok dengan URL hosting pada release.

## Browser smoke lokal

Chrome headless berhasil merender `/login` menjadi DOM lengkap. Evidence runtime menunjukkan title aplikasi, form `POST /login`, hidden CSRF token, input email required, input password required dengan autocomplete, dan tombol submit. Profile serta DOM sementara dihapus setelah pemeriksaan. Ini membuktikan render halaman publik pada browser engine, bukan workflow login/LK terautentikasi atau acceptance pengguna.

## Performance finding

Benchmark memperlihatkan masalah aktual pada pola controller yang memuat semua LK lalu melakukan `array_filter`. Patch pagination tidak digabung ke hardening ini karena ia mengubah UX, query ownership legacy, dan export behavior. Tindakan berikutnya adalah desain paging/search SQL, feature test role-filter, lalu remeasure pada MySQL.

## Final gate

- Focused auth/public-surface: **8 tests / 40 assertions PASS**.
- Full PHPUnit: **115 tests / 296 assertions PASS**.
- Route compilation: PASS, 77 explicit routes.
- Composer manifest validation: PASS.
- Composer locked advisory audit: PASS, no known advisories.
- Disposable backup/restore: PASS.
- Targeted diff whitespace check: PASS setelah documentation closeout.

## Release dependency compatibility — 21 September 2026

- Composer resolves the release lock against PHP `8.2.0`/64-bit rather than the developer machine's PHP 8.3 runtime.
- Locked runtime packages include CodeIgniter `4.7.4`, PhpSpreadsheet `5.10.0`, and ZipStream `3.1.2`; the selected ZipStream line supports the documented PHP 8.2 hosting baseline.
- `composer install`, `composer validate --no-check-publish`, and `composer audit --locked --no-interaction` PASS. Full PHPUnit on the synchronized vendor tree remains **115 tests / 296 assertions PASS**.
- Local preflight still reports missing `ext-zip` and development-only environment values. These are explicit hosting gates and remain unverified until the server preflight is recorded.

## Release interpretation

Build ini lebih aman dan lebih dapat diaudit secara lokal. Ia belum boleh disebut production-ready sampai hosting preflight seluruhnya PASS, MySQL restore/concurrency dibuktikan, workflow UAT selesai, serta evidence pengguna dan perangkat dikumpulkan pada `docs/PRODUCTION_RELEASE_RECORD.md`.
