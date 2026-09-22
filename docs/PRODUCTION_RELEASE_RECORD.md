# Production Release Evidence Record

Dokumen ini merekam deployment aktual build IPSRS ke Rumahweb/cPanel. Nilai `PASS` di bawah hanya berlaku untuk bukti yang benar-benar diperiksa pada 22 September 2026. Item browser terautentikasi dan recovery drill yang belum dijalankan tetap ditandai `PENDING`.

## Identitas release

- Tanggal/waktu aktivasi: 22 September 2026, sekitar 08:00 WIB
- Waktu verifikasi HTTP terakhir: 22 September 2026, 08:37 WIB
- Commit aplikasi: `21be750ce4e76b70656bdbfff71bd4e2b2f4525c`
- Release directory: `/home/ipsc7141/releases/21be750`
- Active symlink: `/home/ipsc7141/current` -> `/home/ipsc7141/releases/21be750`
- URL: `https://ipsrs-rsud-jogja.my.id`
- IP server hasil migrasi hosting: `103.247.10.56`
- Operator: project owner dengan deployment assistance Codex
- Approver/UAT owner: project owner; sign-off pengguna belum diisi
- Maintenance window: migrasi paket hosting dan release pada 22 September 2026

## Runtime dan konfigurasi

- PHP web/CLI: **8.3.33** (`alt-php83`) — PASS
- MariaDB client pada hosting: **10.11.19**; koneksi aplikasi ke database — PASS
- Composer: **2.9.8**
- `CI_ENVIRONMENT=production`: **PASS** melalui hosted preflight
- HTTPS aktif: **PASS**
- Session cookie `Secure`, `HttpOnly`, `SameSite=Lax`: **PASS** dari response `/login`
- CSRF cookie `Secure`, `HttpOnly`, `SameSite=Lax`: **PASS** dari response `/login`
- Security headers: **PASS** (`SAMEORIGIN`, `nosniff`, `noopen`, cross-domain policy `none`, referrer policy `same-origin`)
- Document root cPanel: **PASS**; UAPI melaporkan `/home/ipsc7141/public_html`
- Front controller publik: **PASS**; `public_html/index.php` memuat source aktif melalui `/home/ipsc7141/current`
- Credential database pada front controller: **tidak ada**
- `.env` release: mode **600**; tidak disimpan di Git
- `public_html/error_log`: mode **600**
- `writable/`: berada di luar document root dan digunakan bersama release melalui symlink — PASS
- `ext-zip` dan extension runtime wajib: **PASS**
- Dedicated database user, bukan `root`: **PASS**
- Database password setelah migrasi: **sudah dirotasi**, nilai tidak dicatat dalam dokumen
- Temporary Codex SSH access: **REVOKED** setelah verifikasi; `authorized_keys` kosong, server-side public key dihapus, dan koneksi baru ditolak
- Production preflight: **28 PASS / 0 FAIL / 3 MANUAL**
- Composer install `--no-dev --optimize-autoloader`: **PASS**
- Composer manifest validation: **PASS**
- Composer locked advisory audit: **PASS**, tidak ada advisory yang diketahui; Packagist sempat timeout lalu audit diulang dengan unreachable-source handling
- Route/application source: **PASS**; active commit sama dengan release commit dan working tree `app`, `composer.json`, serta `composer.lock` bersih

Tiga item `MANUAL` pada preflight tidak berarti kegagalan runtime. Document root dan HTTPS telah dibuktikan terpisah melalui cPanel UAPI dan HTTP smoke. Restore MySQL production-equivalent serta workflow browser terautentikasi tetap belum dijalankan.

## Backup dan recovery

- Backup pre-deploy: `/home/ipsc7141/backups/ipsrs-predeploy-20260922-080253`
- Database dump: `database.sql` — 529,354 bytes
- Repository/application archive: `repository.tgz` — 15,957,331 bytes
- Public document-root archive: `public_html.tgz` — 5,166 bytes
- Checksum manifest: `SHA256SUMS` — 407 bytes
- Checksum verification setelah deployment: **PASS** untuk ketiga artefak
- Permission backup directory: **700**; file backup: **600**
- Target restore non-production: belum tersedia
- Restore MySQL pada target non-production: **PENDING**
- Rekonsiliasi jumlah record setelah restore: **PENDING**
- Catatan: disposable SQLite backup/restore drill lokal PASS, tetapi itu bukan pengganti restore dump MariaDB ini.

## Deployment layout dan rollback

Release memakai directory versioned dan symlink aktif agar rollback kode tidak bergantung pada working tree lama yang kotor:

```text
/home/ipsc7141/releases/21be750   exact application release
/home/ipsc7141/current            symlink ke release aktif
/home/ipsc7141/public_html        document root publik/front controller
/home/ipsc7141/home/ipsc7141/ipsrs/writable
                                  writable persisten yang dipakai release
```

Rollback kode dilakukan dengan mengarahkan `/home/ipsc7141/current` ke release sebelumnya dan mengembalikan front controller/public assets dari backup bila diperlukan. Rollback database hanya boleh dilakukan dari backup setelah dampak diverifikasi; belum pernah dieksekusi karena release ini tidak mengubah schema atau data aplikasi.

## Smoke dan UAT

### Smoke publik otomatis

- `GET /`: **302** ke `/ipsrs`
- `GET /login`: **200**
- `GET /lapor`: **200**
- anonymous `GET /ipsrs`: **302** ke login
- `GET /check.php`: **404**
- HTTPS/security headers/cookies: **PASS**
- Hardcoded database credential pada public front controller: **tidak ditemukan**
- Temporary deployment scripts pada home server: **sudah dihapus**
- Temporary deployment SSH authorization: **sudah dicabut**

### UAT browser/perangkat

- Login Admin/Teknisi/Pelapor: **PENDING**
- Corrective: login -> buat LK -> claim -> survei -> perbaikan -> selesai: **PENDING**
- Preventive: jadwal -> empat checklist -> `Perlu Perbaikan` -> LKP -> auto-LK: **PENDING**
- Kanibalisasi/lifecycle/mutasi: **PENDING**
- BA upload/download pada hosting: **PENDING**
- QR/GPS pada perangkat: **PENDING**
- Export XLSX/print: **PENDING**
- User testing/responden: **PENDING**
- Screenshot/evidence path: isi pada dokumen UAT terkait setelah pelaksanaan

## Release decision

- Technical deployment decision: **GO** — build aktif, konfigurasi hosting, dependency install, database connectivity, backup, permission, HTTPS, dan public smoke telah diverifikasi.
- Product acceptance decision: **DEMO ONLY / UAT PENDING** — belum boleh disebut production-ready penuh sebelum UAT browser, user testing, dan recovery/concurrency evidence selesai.
- Accepted limitations: external CDN dependency; GPS adalah telemetry yang dapat dimanipulasi; signature berupa bukti gambar operasional, bukan tanda tangan digital tersertifikasi; beberapa ownership legacy masih berbasis display name; server-side pagination belum diterapkan.
- Rollback trigger: HTTP 5xx berulang, login semua role gagal, mutation menghasilkan state/data salah, file upload membuka akses publik, atau koneksi database tidak stabil.
- Rollback result bila dijalankan: **NOT EXECUTED**; tidak ada trigger setelah smoke test.
- Sign-off operator: technical deployment completed 22 September 2026.
- Sign-off user/approver: **PENDING setelah UAT**.
