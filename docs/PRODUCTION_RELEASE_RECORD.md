# Production Release Evidence Record

Isi satu salinan dokumen ini untuk build yang benar-benar akan dipakai demo atau release.

## Identitas release

- Tanggal/waktu:
- Commit/build:
- URL:
- Operator:
- Approver:
- Maintenance window:

## Runtime dan konfigurasi

- PHP version:
- MySQL/MariaDB version:
- `CI_ENVIRONMENT=production`: [ ]
- HTTPS aktif: [ ]
- Secure/HttpOnly/SameSite cookie: [ ]
- Document root tepat ke `public/`: [ ]
- `writable/` tidak publik dan dapat ditulis: [ ]
- `ext-zip` tersedia: [ ]
- Dedicated DB user, bukan root: [ ]
- Production preflight: PASS / FAIL
- Composer audit: PASS / FAIL
- Route compilation: PASS / FAIL

## Backup dan recovery

- Lokasi backup database:
- Lokasi backup upload:
- Checksum/ukuran/timestamp:
- Target restore non-production:
- Restore berhasil: PASS / FAIL
- Rekonsiliasi jumlah record: PASS / FAIL
- Catatan perbedaan:

## Smoke dan UAT

- Anonymous internal-route redirect: PASS / FAIL
- Login Admin/Teknisi/Pelapor: PASS / FAIL
- Corrective UAT: PASS / FAIL
- Preventive UAT: PASS / FAIL
- Kanibal/lifecycle UAT: PASS / FAIL
- BA upload/download: PASS / FAIL
- QR/GPS device: PASS / FAIL
- Export XLSX/print: PASS / FAIL
- Security headers/cookies: PASS / FAIL
- Screenshot/evidence path:

## Release decision

- Decision: GO / NO-GO / DEMO ONLY
- Accepted limitations:
- Rollback trigger:
- Rollback result bila dijalankan:
- Sign-off:
