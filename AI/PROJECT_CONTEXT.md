# IPSRS Project Context — Technical Memory for Future Agents

> **Purpose of this file:** this is the primary cold-start memory for future AI agents working in this repository. Read this file before proposing or changing code. Verify any time-sensitive statement against the current branch and current database before acting.
>
> **Snapshot date:** 2026-09-10 (Asia/Jakarta)
>
> **Code snapshot inspected:** `main` at commit `9dfc1d4`
>
> **Evidence used:** current source tree, routes, controllers, models, views, configuration, SQL and migration files, tests, user-guide documents, Git history, `PROJECT_STATE.md`, `REVISION_LOG.md`, `UAT_Checklist.md`, and the exported Antigravity conversation in `AI/transcript_percakapan_kita_full.jsonl`.

## 1. Project identity and purpose

This project is a web-based information system for **IPSRS RSUD Kota Yogyakarta**. IPSRS is the hospital unit responsible for facilities, infrastructure, and general non-medical equipment. The application supports operational asset management, corrective maintenance, preventive maintenance, spare-parts inventory, physical asset movement, borrowing, cannibalization, deletion, and reporting.

The scope is intentionally limited to **sarana, prasarana, and non-medical equipment**. Examples and user-facing documentation must use assets such as AC units, generators, pumps, lifts, electrical installations, furniture, fans, and other IPSRS-managed facilities. Do not use medical or electromedical equipment such as ECG machines, patient monitors, or blood-pressure devices because that expands the domain beyond the thesis scope.

The system exists to replace fragmented or manual operational records with linked workflows:

1. Create an asset catalog entry representing a logical asset type.
2. Register one or more physical units as `aset_series` records.
3. Assign each physical unit to a normalized hospital location.
4. Track movement, borrowing, return, QR scans, and GPS verification for physical units.
5. Receive damage reports from logged-in staff or the public reporting portal.
6. Assign and process repair work through an LK status workflow.
7. Record spare parts and vendor involvement.
8. Schedule and execute preventive maintenance through LKP checklists.
9. Convert preventive findings into corrective LK tickets when needed.
10. Track end-of-life actions: heavy damage, cannibalization, formal deletion, and Berita Acara records.
11. Export management reports and operational metrics.

## 2. Users and roles

Observed roles in current code and conversation:

- **Admin:** intended full operational access, user management, master data, reporting, and asset lifecycle actions.
- **Teknisi:** intended to claim and process repair tickets, execute preventive maintenance, use stock, and record field operations.
- **pelapor:** a logged-in hospital reporter, usually staff or a head of room. The lowercase spelling is significant because many checks compare the exact string `pelapor`.
- **Public reporter:** unauthenticated user of `/lapor`; this is not a stored role and does not receive access to the internal application.

Current authorization architecture is incomplete. The protected route group only requires a valid session. Most modules do not enforce Admin-versus-Teknisi permission on the server. The sidebar hides non-pelapor menus, and `LK` explicitly blocks several pelapor mutations, but UI visibility is not a complete authorization system. Future work must treat this as a known security boundary, not as finished RBAC.

## 3. Production and deployment context

- **Production URL:** `https://ipsrs-rsud-jogja.my.id`
- **Repository remote:** `https://github.com/agadape/ipsrs.git`
- **Primary branch:** `main`
- **Hosting:** Rumahweb/cPanel shared hosting.
- **Deployment flow used during prior development:** commit and push local code to GitHub, then use cPanel Git tooling or `git pull` on the server.
- **Production database:** MySQL managed separately from Git deployment, usually through phpMyAdmin or one-off SQL scripts.
- A code pull does **not** apply database changes. Any schema change needs a separately reviewed migration or SQL deployment step.
- Live production state cannot be inferred only from repository migrations because several schema changes were executed manually through SQL scripts.
- A logical dump supplied on 15 September 2026 provides a **time-bounded schema/data snapshot**: `C:\Users\Advan\Downloads\ipsc7141_ipsrs_db (6).sql`, generated at 19:58 on MariaDB 10.11.14. It confirms the deployment has `aset_series`, `master_lokasi`, physical-series FKs in LK/LKP/jadwal/kanibal/lifecycle, and TTD columns. It is not live introspection and must not be imported into an existing database because it includes `DROP TABLE` and sensitive data.
- Read [`DB-DUMP-ADDENDUM-2026-09-15.md`](DB-DUMP-ADDENDUM-2026-09-15.md) and [`SCHEMA-BASELINE.md`](SCHEMA-BASELINE.md) before schema or data work. The dump is sensitive: never commit, publish, or paste its user data/password hashes.
- Do not test destructive behavior against production. Prefer local tests, schema inspection, or read-only checks.
- When a production fix appears ineffective, first confirm the target commit exists on `origin/main` and that cPanel pulled that exact commit.

Local development configuration observed in ignored `.env`:

- `CI_ENVIRONMENT = development`
- `app.baseURL = 'http://localhost:8080/'`
- MySQL database `ipsrs` on `localhost:3306`
- local username `root`, empty local password

The `.env` file is ignored by Git. The committed `env` file remains the generic CodeIgniter example and does not document the real production variables.

## 4. Technology stack

### Backend

- PHP `^8.2`; locally verified with PHP `8.3.30`.
- CodeIgniter `4.7.4` (release lock).
- Server-rendered MVC monolith.
- MySQL through CI4 Query Builder and `MySQLi`.
- File-backed CI4 sessions under `writable/session`.
- Composer dependency management.
- PhpSpreadsheet `5.10.0` for Excel exports; `maennchen/zipstream-php` is pinned to `3.1.2` so the lock remains installable on the documented PHP 8.2 baseline.
- cURL-based placeholder integration with Fonnte for WhatsApp delivery.

### Frontend

- PHP views rendered on the server.
- Tailwind CSS loaded from `cdn.tailwindcss.com`; no local Tailwind build pipeline.
- Inter font loaded from `rsms.me` or Google Fonts depending on page.
- jQuery, DataTables, Select2, SweetAlert2, and Chart.js loaded from public CDNs.
- QRCode.js from cdnjs.
- Leaflet and OpenStreetMap tiles for QR location verification.
- Nominatim reverse-geocoding request from the browser.
- Signature Pad loaded from jsDelivr on the LK detail page.

### Development and QA tooling

- PHPUnit `10.5.64`.
- RTK `0.48.0` is configured for Codex through root `AGENTS.md` and `RTK.md`.
- Caveman skills are installed project-locally in `.agents/skills`.
- No Node package manifest, compiled asset pipeline, static-analysis configuration, E2E framework, or CI workflow was found in the inspected tree.

## 5. Architectural shape

The application is a conventional CI4 monolith with several custom decisions:

```text
HTTP request
  -> app/Config/Routes.php
  -> optional route-group AuthFilter
  -> controller extending BaseController
  -> custom model extending App\Models\BaseModel
  -> CI4 Query Builder
  -> MySQL
  -> PHP view, redirect with flash message, JSON response, or export
```

`App\Models\BaseModel` is **not** `CodeIgniter\Model`. It is a project-specific Query Builder wrapper. Never assume CI4 model methods such as `findAll()`, `save()`, or native allowed-field behavior exist. Supported common methods are:

- `getAll()`
- `find()` / `getById()`
- `create()`
- `createWithRetry()`
- `update()`
- `delete()`
- `generateUUID()`
- protected `qb()`, `nextId()`, and `nextNoOrder()`

`BaseController` loads helpers `url`, `form`, `text`, `ui`, and `rs`. It provides:

- `render($view, $data)`: wraps an internal view in `layout/main`.
- `validateOrFail($rules, $message)`: executes CI4 validation and redirects with old input plus detailed validation errors.
- `whitelist($allowed)`: removes unexpected POST fields, including the CSRF token, before database insertion.

Most business operations still live directly in controllers. There is no service or domain layer. Multi-table workflows often call several models or Query Builders sequentially without a transaction.

## 6. Important repository map

### Runtime application

- `app/Config/Routes.php`: complete explicit route map.
- `app/Config/Filters.php`: CSRF configuration and filter aliases.
- `app/Config/IPSRS.php`: central constants for SLA, prefixes, asset states, LK states, and status synchronization.
- `app/Config/Database.php`: default MySQL connection and SQLite in-memory test connection.
- `app/Filters/AuthFilter.php`: session gate with special QR-related bypass logic.
- `app/Controllers`: HTTP orchestration and most business logic.
- `app/Models`: Query Builder access objects.
- `app/Views/layout`: authenticated shell, navigation, alerts, and shared scripts.
- `app/Views/pages`: feature views.
- `app/Helpers/ui_helper.php`: date/time and badge-class formatting.
- `app/Helpers/rs_helper.php`: standard hospital unit list and physical-asset labels.
- `app/Libraries/Metrics.php`: pure calculations for stock status, time differences, SLA, and sequential identifiers.
- `app/Libraries/WhatsAppAPI.php`: Fonnte request wrapper with placeholder credentials.

### Data definition and migration material

- `app/Database/Migrations/2026-07-01-092000_buat_tabel_master_data.sql`
- `app/Database/Migrations/2026-07-01-100000_full_mysql_schema.sql`
- `app/Database/Migrations/2026-08-24-094333_AddPeminjamanPenghapusan.php`
- `sql/migrate_aset_cpanel_final.sql`
- `ERD_FIX_SCRIPT.sql`
- `ERD_FIX_REMAINING.sql`
- `ERD_FIX_REMAINING_V2.sql`
- `FIX_TTD_COLUMNS.sql`
- `fix_ttd.sql`

There is also a misspelled `app/Database/igrations` directory containing older SQL variants, including PostgreSQL/Supabase RLS syntax. Treat this directory as historical residue unless current code or deployment instructions explicitly reference it.

### Documentation

- `docs/00_user_guide_plan.md`: functional user-guide outline.
- `docs/style_guide.md`: mandatory writing style and non-medical scope.
- `docs/01_modul_manajemen_aset.md`
- `docs/02_modul_laporan_kerusakan.md`
- `docs/02b_modul_user_pelapor.md`
- `docs/03_modul_pemeliharaan_preventif.md`
- `docs/04_modul_manajemen_suku_cadang.md`
- `docs/05_modul_peminjaman_penghapusan.md`
- `docs/06_modul_administrator.md`
- `docs/Panduan_Lengkap_IPSRS.md`: combined Markdown guide.
- `docs/Panduan_Pengguna_IPSRS_Formal.docx`: generated/formatted guide artifact.
- `PROJECT_STATE.md`: useful historical snapshot from July 2026, but materially outdated.
- `REVISION_LOG.md`: asset lifecycle and ERD revision history.
- `UAT_Checklist.md`: manual acceptance checklist; boxes remain unchecked in Git.
- `PROMPT_ERD_ANALYSIS.md`: prior schema-analysis brief.

### Conversation memory

- `AI/transcript_percakapan_kita_full.jsonl`: complete exported Antigravity session with more than 8,000 steps.
- `AI/transcript_percakapan_kita.jsonl`: smaller transcript/export variant.
- This file is the curated memory. Read raw transcripts only when exact historical wording or an omitted decision is needed.

## 7. Route map and access boundary

### Public routes

| Method | Path | Handler | Notes |
|---|---|---|---|
| GET | `/` | closure | Redirects to `/ipsrs`, which then requires login. |
| GET | `/lapor` | `Portal::lapor` | Public damage-report form. |
| POST | `/lapor` | `Portal::storeLapor` | Public report submission; CSRF-protected since Iteration 1B. |
| GET | `/lapor/sukses` | `Portal::sukses` | Displays order number from query string. |
| GET | `/login` | `Auth::login` | Login form. |
| POST | `/login` | `Auth::doLogin` | CSRF-protected by path configuration. |
| GET | `/register` | `Auth::register` | Explicit route, but handler always throws 404. |
| POST | `/register` | `Auth::doRegister` | Explicit route, but handler always throws 404. |
| POST | `/logout` | `Auth::logout` | CSRF-protected session logout since Iteration 1B. |
| GET | `/ipsrs/aset/scan/{seriesId}` | `Aset::scan` | Standalone public QR scan page. |
| POST | `/ipsrs/aset/{seriesId}/ping` | `Aset::ping` | Public GPS update; CSRF-protected and returns a refreshed token for repeated scans. |

### Authenticated `/ipsrs` routes

All following routes sit in `group('ipsrs', ['filter' => 'auth'])`:

- Dashboard: `GET /ipsrs`.
- Asset catalog: list, create, store, show, edit, update.
- Physical series: create, store, show, edit, update, and QR page.
- Asset mutation: list/form and submit.
- Lifecycle actions: loan, return, and formal deletion.
- LK: list, create, submit, claim, show, complete detail, update status, delete, add spare part, add vendor.
- Preventive maintenance: list, create schedule, mark complete, delete, fill LKP, save LKP, and view result.
- Stock: list, add item, incoming transaction, outgoing transaction, and history.
- Master vendor, user, asset category, and damage-code management.
- Cannibalization list and submit.
- Borrowing list and deletion list.
- Reports: screen, LK Excel, LK print, preventive Excel, and preventive print.

Since Iteration 1B, `Aset::tandaiRusakBerat()` has an explicit CSRF-protected POST route. Claim LK and return asset are also POST-only. The detail view renders token-bearing forms; WhatsApp notification links open the LK detail rather than mutating it directly.

F03/F16 were verified on 15 September 2026 with authenticated HTTP against an isolated SQLite database: tokenless POST was rejected, token-valid pinjam/return/disposal/claim/logout reached their controllers, and resulting database state was checked. The temporary database and temporary `.env` override were removed afterward. Chrome headless crashed before a page loaded, so this is not browser/device UAT evidence.

## 8. Authentication, sessions, and authorization

### Login flow

1. `Auth::doLogin()` trims email and reads password.
2. `PenggunaModel::findByEmail($email)` defaults to active users only.
3. Password verification uses `password_verify()` against `password_hash`.
4. Session ID is regenerated after successful authentication.
5. Session fields are written:
   - `user_id`
   - `user_email`
   - `user_name`
   - `user_role`
   - `user_unit`
   - `user_initial`
6. A previously stored `redirect_url` is used after login; otherwise redirect to `/ipsrs`.
7. Logout destroys the session and redirects to `/login`.

Session storage uses `FileHandler`, expiration is 7,200 seconds, ID update interval is 300 seconds, and `regenerateDestroy` is false.

### Registration

Self-registration was deliberately disabled after QA/security review. Both registration handlers return 404. Users are created through internal user management.

### Current RBAC behavior

- `AuthFilter` checks only whether `session('user_id')` exists.
- `Dashboard` redirects a `pelapor` to `/ipsrs/lk`.
- The sidebar hides all modules except LK from `pelapor`.
- `LK` filters pelapor-visible records and blocks pelapor from delete, claim, detail update, status update, spare-part use, and vendor assignment.
- Other controllers do not consistently check Admin or Teknisi roles.
- A user with any non-pelapor authenticated role can directly request user-management, master-data, asset lifecycle, stock, report, and deletion endpoints.

### AuthFilter asset-route bypass (resolved, 17 September 2026)

`AuthFilter::before()` previously contained legacy special-case logic that returned before session validation for `/ipsrs/aset/{uuid}` and `/ipsrs/aset/{uuid}/qr`. This could bypass the `auth` group for internal asset detail and QR pages. The special case was removed: the genuinely public scan and ping endpoints remain public because their routes sit outside the `auth` group. `AuthFilterHttpTest` now proves an anonymous UUID-shaped internal asset-detail request redirects to `/login`.

## 9. CSRF, cookies, and transport configuration

- CSRF uses cookie mode.
- Token name: `csrf_test_name`.
- Header: `X-CSRF-TOKEN`.
- CSRF token regenerates after every submission.
- CSRF runs for `ipsrs/*`, `login`, `lapor`, and `logout`.
- GPS ping is public but CSRF-protected; the scan page sends the configured CSRF header.
- Public `POST /lapor` is CSRF-protected.
- `App::$forceGlobalSecureRequests` is true in committed config.
- `Cookie::$httponly` is true and SameSite is `Lax`.
- `Cookie::$secure` is false in committed config; production environment overrides were not available in repository evidence.
- CSP is disabled.
- SecureHeaders is defined as an alias but not globally enabled.
- Production hides displayed errors and logs through CI4 file logging.

## 10. Domain model and current intended database

The original July schema had 17 main tables. Later changes added physical series, normalized locations, borrowing, and deletion. The current intended logical schema has at least 21 domain tables:

### Identity and master data

| Table | Purpose | Important keys/fields |
|---|---|---|
| `pengguna` | Internal accounts | UUID `id`, unique `email`, `password_hash`, `role`, `unit`, `aktif`. |
| `kategori_aset` | Asset categories | UUID `id`, unique `nama_kategori`. |
| `kode_kerusakan` | Damage classification | UUID `id`, unique natural key `kode`, `nama`. |
| `vendor` | Repair vendors | UUID `id`, `nama_vendor`, contact and address. |
| `master_lokasi` | Normalized physical locations | UUID `id`, `gedung`, `lantai`, `nama_ruangan`, `nama_unit`; intended composite unique key. |
| `template_checklist` | Learned preventive checklist templates | category, item number/type, component, unit. |

### Asset and lifecycle

| Table | Purpose | Important keys/fields |
|---|---|---|
| `aset` | Logical catalog or parent asset | UUID `id`, `nama`, `jenis`, category reference/name, description. |
| `aset_series` | Physical inventoried unit | UUID `id`, FK `id_aset`, unique `nomor_aset`, serial, brand/model/capacity/year, `id_lokasi`, condition, lifecycle status, GPS last-seen fields. |
| `riwayat_lokasi_aset` | Movement history | physical series FK, origin, destination, reason, date, officer, notes. |
| `peminjaman_aset` | Borrowing audit trail | series FK, borrower/unit, planned and actual return dates, status, admin FK. |
| `penghapusan_aset` | Formal deletion records | series FK, BA number/date, follow-up, uploaded document filename, admin FK. |
| `riwayat_kanibal` | Component cannibalization history | donor and recipient series references, LK order reference, component, condition, approver, officer. |
| `komponen_aset` | Components associated with a physical unit | series FK, component name and condition, optional cannibal history reference. |

### Corrective maintenance

| Table | Purpose | Important keys/fields |
|---|---|---|
| `laporan_kerusakan` | LK ticket | UUID, unique `no_order`, optional series FK, reporter/unit/location, complaint, code, technician, process, status, timestamps, response/down time, signature. |
| `detail_suku_cadang_lk` | Spare parts used by LK | LK FK, optional stock FK, source/name/quantity/unit/notes. |
| `detail_vendor_lk` | Vendor involvement | LK FK, vendor FK, send/return/estimate dates, notes. |

### Preventive maintenance

| Table | Purpose | Important keys/fields |
|---|---|---|
| `jadwal_preventif` | Preventive schedule | physical asset reference or legacy field, asset/location snapshot, date/time, technician, status. |
| `lembar_kerja_preventif` | LKP header/result | unique `no_order`, schedule FK, optional series FK, category, examination date, technician, result, notes, signature fields. |
| `detail_checklist_lkp` | LKP checklist rows | LKP FK, item number/type, component, inspection/service/measurement result, unit, notes. |

### Inventory

| Table | Purpose | Important keys/fields |
|---|---|---|
| `barang_persediaan` | Spare-parts master and balance | UUID, unique `no_barang`, name/category/unit, `stok_tersedia`, `minimum_stok`. |
| `riwayat_transaksi_stok` | Immutable-style stock movement history | stock FK, movement type, quantity, date, document reference, officer, notes. |

The current runtime session driver is file-based, so the old `session` table documented in `PROJECT_STATE.md` is not part of the active session flow.

## 11. Schema evolution and source-of-truth warning

The repository does not have one reliable migration chain that can reconstruct the current production schema from an empty database.

The 15 September logical dump confirms this is repository-to-deployment **schema drift**, not merely an unknown production schema. The target DB already has the series-oriented relations described above, but no authoritative CI migration chain or migration-history manifest in the dump recreates it. It also exposes a current compatibility blocker: `aset_series.status` is still an enum with only `Tersedia`, `Dipinjam`, `Dalam Perbaikan`, `Rusak Berat`, and `Dihapuskan`, while LK workflow uses waiting statuses. Do not add new lifecycle writers until value mapping/migration compatibility is designed.

The dump has four historical detail/history records with empty-string primary keys. Treat those as a data-migration preflight issue; map references and verify backup before UUID/constraint/idempotency work. See F37 in the audit addendum.

Observed evolution:

1. July full MySQL schema created original parent-level asset relations.
2. Asset architecture changed to parent catalog plus physical `aset_series`.
3. `sql/migrate_aset_cpanel_final.sql` moved LK, LKP, movement, component, and cannibal relations toward physical series.
4. Peminjaman and penghapusan tables were added through a CI4 migration.
5. `ERD_FIX_SCRIPT.sql` and later remaining-fix scripts added foreign keys, status ENUM changes, indexes, unique keys, and `master_lokasi`.
6. Location text was normalized into `master_lokasi`; production reportedly imported approximately 777 locations.
7. Signature columns were added with separate `FIX_TTD_COLUMNS.sql` and `fix_ttd.sql` variants.

Consequences:

- The full July migration is outdated relative to runtime code.
- Several follow-up SQL scripts overlap or contain malformed control characters from earlier editing.
- `sql/lkp_checklist.sql` and `sql/vendor_proses3.sql` contain Supabase/PostgreSQL RLS syntax and are not canonical MySQL migrations.
- `AddPeminjamanPenghapusan` creates tables without the foreign keys later added by manual ERD SQL.
- A fresh developer cannot reliably run one command to obtain the production-equivalent schema.
- Before database work, inspect the actual target schema with read-only `SHOW CREATE TABLE`, `SHOW INDEX`, and `information_schema` queries.

## 12. Asset architecture and workflow

### Parent catalog versus physical unit

This distinction is central:

- `aset` represents the logical catalog entry: name, type, category, description.
- `aset_series` represents the real physical inventory unit: inventory number, serial number, brand/model, location, condition, status, acquisition year, and QR/GPS tracking.

Movement, repair association, preventive work, borrowing, deletion, and cannibalization should reference `aset_series`, not the parent catalog.

### Asset catalog flow

- `Aset::index()` loads all parents and aggregates physical-unit count and `GROUP_CONCAT` status values.
- Search and type filtering run in PHP after all rows are loaded.
- Status filtering queries matching physical parent IDs, then filters the parent result in PHP.
- `Aset::store()` and `update()` whitelist logical fields only.
- Parent UUID generation is duplicated inline instead of using `BaseModel::generateUUID()`.

### Physical series flow

- `Aset::storeSeries()` validates unique `nomor_aset`, required location, and condition enum.
- New unit status is `Tersedia`.
- Legacy physical text columns `lokasi`, `gedung`, `ruangan`, and `unit` are written as `-` stubs because production may still require them.
- Actual display location comes from `master_lokasi` through `id_lokasi` joins.
- `AsetSeriesModel::getAllWithParent()`, `getByParent()`, and `getById()` expose joined parent and location fields.

### Mutation flow

Supported UI actions include:

- Pindah Ruangan
- Simpan ke Gudang
- Jadikan Kanibal
- Dibuang

`Aset::storeMutasi()` resolves a submitted location UUID to a display string, stores a movement snapshot, and updates the physical series status and `id_lokasi`. The code still uses legacy statuses `Di Gudang`, `Kanibal`, `Dibuang`, and `Aktif`, although the newer intended `aset_series.status` ENUM contains only `Tersedia`, `Dipinjam`, `Dalam Perbaikan`, `Rusak Berat`, and `Dihapuskan`. This is a major unresolved consistency risk.

### Borrowing and return

- Borrowing inserts a `peminjaman_aset` record and changes the unit status to `Dipinjam`.
- Return is a GET endpoint, completes every active loan matching the series, and changes status to `Tersedia`.
- These writes are not wrapped in a database transaction.
- The controller currently does not validate existence, current status, required values, or competing active loans before writing.

### Deletion

- Formal deletion accepts series ID, BA number/date, follow-up, notes, and optional file.
- Since Iteration 1A (15 September 2026), newly uploaded BA files are stored with a random key under `writable/uploads/ba`, outside the public web root. Server detection only permits PDF, JPEG, and PNG at up to 5 MB; direct-view links now call an authenticated Admin download route that forces attachment and sets `nosniff`.
- The series status then changes to `Dihapuskan`.
- The disposal record insert and status update now share a transaction; a newly stored BA is removed when that transaction fails. Legacy BA files under `public/uploads/ba` are only reachable through the protected controller and are additionally blocked by a directory `.htaccess` when the hosting permits it.
- F01 remains `Implemented — unverified`: HTTP multipart behavior, denial for anonymous/pelapor users, database-failure cleanup, and cPanel/Apache `AllowOverride` plus document-root configuration still need verification. Required BA fields and lifecycle preconditions remain work for later findings.

### QR and GPS tracking

- QR generation points to `/ipsrs/aset/scan/{seriesId}`.
- Scan view is standalone and intentionally public.
- Browser geolocation captures latitude and longitude.
- `POST /ipsrs/aset/{seriesId}/ping` validates numeric ranges and updates `last_seen_at`, `last_seen_lat`, `last_seen_lng`, and `last_seen_by` on `aset_series`.
- Rate limiting is a per-asset ten-second check based on `last_seen_at`.
- Scan view renders Leaflet/OpenStreetMap and calls Nominatim for reverse geocoding.
- GPS data is browser-provided and unauthenticated; the system proves that a browser reported coordinates, not that an authenticated technician physically verified the asset.
- Prior iPhone testing showed denied location permissions can require a user-facing Settings guide because Safari will not always reopen the browser prompt.

## 13. Corrective maintenance: LK workflow

Configured LK states:

1. `Laporan Masuk`
2. `Didisposisi`
3. `Survei`
4. `Dalam Perbaikan`
5. `Menunggu Suku Cadang`
6. `Menunggu Vendor`
7. `Selesai`

### Sources of LK records

- Internal Admin/Teknisi form under `/ipsrs/lk/baru`.
- Logged-in pelapor form under the same route with a specialized view.
- Public `/lapor` portal.
- Automatic creation from a preventive result of `Perlu Perbaikan`.

### Internal creation

`LK::store()` validates date, time, reporter, unit, complaint, and location. Non-pelapor users must also provide damage code. It computes a monthly order number such as `LK-202609-0001` and attempts a WhatsApp broadcast.

Known current defect: the internal form submits `id_aset`, but `LK::store()` does not whitelist `id_aset` or `id_aset_series`. It then evaluates an undefined `$post` variable and normally sets `id_aset_series` to null. Internal LK creation therefore appears unable to persist the selected physical asset link. This was observed directly in current code and has not been fixed in this memory-building task.

### Public creation

`Portal::storeLapor()` accepts reporter, unit, complaint, location, and optional `id_aset_series`. It whitelists input, assigns current date/time, status `Laporan Masuk`, and default code `PR`. If a valid asset is selected, it snapshots `nama_aset` and updates asset status/location before creating the LK.

Public errors are currently returned as `Gagal: ` plus the exception message. That helped diagnose production incidents but may expose database or internal implementation details.

### Claim and processing

- `claim()` blocks pelapor, rejects already assigned tickets, assigns current session name as technician, and sets `Didisposisi`.
- Claim uses read-then-update without a conditional write or transaction, so simultaneous technicians may race.
- `updateDetail()` fills damage code, selected physical asset, location, complaint, and asset name.
- `updateStatus()` accepts the posted next state, technician, action, timestamps, and reporter signature.
- Process code is inferred: I for internal, II when spare parts exist or waiting for parts, III when a vendor exists or waiting for vendor.
- Response time is calculated when entering Didisposisi or Survei with check time.
- Down time is calculated on Selesai.
- Asset status is synchronized through `IPSRS::LK_TO_ASET_STATUS`.

### Status consistency defect

`IPSRS::LK_TO_ASET_STATUS` still maps LK states to legacy asset statuses:

- `Menunggu Suku Cadang` maps to `Menunggu Suku Cadang`.
- `Menunggu Vendor` maps to `Tidak Aktif`.
- `Selesai` maps to `Aktif`.

These values conflict with the new intended `aset_series.status` set. The valid modern equivalents need an explicit business decision, likely keeping the unit `Dalam Perbaikan` while waiting and returning it to `Tersedia` when complete.

`updateStatus()` also does not validate `status_baru` against `IPSRS::STATUS_LK` and does not enforce allowed transitions. A crafted authenticated request can skip stages or submit an unknown state if the database column permits it.

### Spare parts

- `addSukuCadang()` checks role, LK, stock item, positive quantity, and available balance.
- It inserts the LK detail first, then calls `StokModel::catatTransaksi()` to insert stock history and update balance.
- The overall action is not transactional.
- `StokModel` inserts history before attempting its optimistic balance update.
- If balance update fails after retries, history can exist without the matching balance change.
- Under concurrent outgoing requests, retry logic recalculates with `max(0, current - requested)` without rechecking that current balance still covers the requested quantity. This can record more consumption than the remaining stock.

### Vendor

- Vendor detail references a master vendor and stores date snapshots and notes.
- WhatsApp integration remains a placeholder unless credentials are supplied in code.

### LK deletion

- Pelapor is blocked.
- The controller attempts to return every used spare part to inventory, then deletes the LK.
- The rollback and deletion are not transactional.
- Cannibal-sourced detail rows may have a null stock ID, which is incompatible with ordinary stock-return logic.

## 14. Preventive maintenance workflow

- `Preventif::index()` loads schedules, physical assets, and active Admin/Teknisi users.
- Schedule creation validates required asset, technician, date, and time, including server-side future-time rules added during UAT.
- New schedule status is `Belum`.
- LKP form loads all checklist templates and category choices.
- LKP save creates a header, creates detail rows, learns new checklist items into `template_checklist`, marks schedule complete, and may create a corrective LK.
- Result `Perlu Perbaikan` triggers automatic LK creation with code `PR`.
- LKP supports `Inspeksi`, `Service`, and `Pengukuran` item types.

This workflow performs many dependent writes without a transaction. A failure after creating the LKP header can leave partial checklist data, a completed schedule without a complete LKP, duplicated learned templates, or missing automatic LK.

The separate `Preventif::selesai()` endpoint directly marks a schedule complete without requiring LKP creation. The UI may hide this action for `Belum`, but the backend endpoint remains callable by any authenticated non-pelapor role unless additional checks exist outside the inspected controller.

## 15. Inventory workflow

- New stock item receives sequential display number `B-001` style and starts with balance zero.
- Incoming and outgoing transactions require a known item and quantity greater than zero.
- Controller rejects an outgoing quantity larger than the balance it read.
- Stock status is computed by `Metrics::statusStok()`:
  - balance at or below zero: `Habis`
  - balance at or below minimum: `Menipis`
  - otherwise: `Aman`
- List and history filters run in PHP after loading all matching model rows.
- There is no stock reservation concept.
- Transaction integrity and concurrency concerns are described in the LK spare-parts section.

## 16. Cannibalization workflow

- Intended domain rule: only an eligible heavily damaged/cannibal asset should donate a component.
- `Kanibal::store()` requires LK ID, donor, recipient, and component name.
- It blocks identical donor and recipient IDs.
- It verifies only that donor series exists.
- It inserts `riwayat_kanibal`, adds a Kanibal-sourced spare-part detail to LK, and marks a matching donor component as `Tidak Ada` when found.
- It does not verify recipient existence, LK existence, donor eligibility, recipient state, component ownership, or that the submitted LK order number matches the LK ID.
- Writes are not transactional.
- The standalone Kanibal page filters series with status `Kanibal`, which conflicts with the modern asset status ENUM unless production still permits legacy states.

## 17. Reporting and dashboard

### Dashboard

Dashboard loads all LK, all stock, and all preventive schedules into PHP, then calculates:

- un-surveyed reports
- waiting spare-part/vendor reports
- overdue preventive work
- low stock
- completions today
- SLA percentage and average response time
- active LK count
- preventive completion percentage
- repair pipeline counts
- five recent tickets
- four upcoming schedules
- priority cards

This is adequate for small thesis/demo data but will scale poorly because aggregation, sorting, and filtering happen in application memory.

### Reports

`Laporan` supports period-filtered pages plus Excel and printable output for corrective and preventive maintenance. PhpSpreadsheet is the only specialized export dependency. Head-of-IPSRS name and NIP come from `IPSRS::NAMA_KEPALA` and `IPSRS::NIP_KEPALA`, which are placeholder values and must be configured before official use.

## 18. UI and design decisions

The visual direction changed from generic starter UI to a restrained RSUD public-sector system:

- primary institutional red around `#c62828`
- slate/white backgrounds
- compact Vercel/Linear-inspired tables and controls
- Inter typography
- light borders and limited shadows
- contextual asset actions on physical-unit detail pages
- responsive sidebar with stored scroll position
- standalone public pages for login, QR scan, public report, and success state

User preferences established in conversation:

- Interfaces must not look like generic AI-generated dashboards.
- Prefer clean hierarchy, restrained badges, readable tables, and strong mobile behavior.
- Use the installed `design-taste-frontend` skill for visually important redesigns.
- Keep `/login`, QR scan, and public portal visually consistent with the red RSUD theme.
- On QR scan success, avoid irrelevant navigation such as returning to the internal asset module.
- Public and logged-in pelapor flows must be visible and understandable during presentations.

Known frontend operational dependencies:

- Main layout depends on six external CDN families.
- DataTables Indonesian language JSON adds another runtime network dependency.
- QR map and reverse geocoding require third-party services.
- CDN or internet failure can leave core UI unstyled or partially nonfunctional.

Output-safety update (16 September 2026): `old()` CI4 escapes HTML by default in this installation, so it is not automatically an XSS finding. The main-layout flash sink no longer uses `addslashes`; it uses JSON with `JSON_HEX_*` and SweetAlert `text`. LKP dynamic fields inserted via `innerHTML` now escape HTML attribute characters. Both Excel exporters use `SpreadsheetText::set()` to emit every dynamic report cell as `PhpSpreadsheet` `TYPE_STRING`; unit coverage proves `=`, `+`, `-`, and `@` prefixes remain literal. This is `Implemented — partially verified`: PHP lint and source/library tests passed, but browser DOM smoke and real report-download/open verification remain required before making end-to-end security claims.

## 19. Validation and error handling

Positive patterns:

- Most standard create/update flows call `validateOrFail()`.
- Most mutations whitelist accepted POST fields.
- Views frequently use `esc()` for database output.
- Controllers log caught exceptions.
- Production disables detailed browser errors.

Current limitations:

- Validation coverage varies sharply by controller.
- Borrowing, return, deletion, claim, status transition, and cannibalization lack complete server-side rule enforcement.
- Many catch blocks display raw exception text in flash messages.
- Multi-write operations lack rollback.
- Some not-found cases redirect silently without a clear user-facing reason.
- WhatsApp failure does not fail the primary business transaction and is visible only in logs.
- File upload errors can be silently ignored because an invalid upload simply leaves the filename null.

## 20. External integrations

### Fonnte WhatsApp

`WhatsAppAPI` posts to `https://api.fonnte.com/send`. Current code contains placeholders:

- token: `TOKEN_DARI_USER`
- default target: `NOMOR_ATAU_GRUP_TARGET`

With placeholders present, send attempts log an error and return false. The current caller generally continues successfully, so WhatsApp is best described as an inactive optional integration, not a delivered notification channel.

### Maps and geocoding

- Leaflet renders maps.
- OpenStreetMap serves tiles.
- Nominatim receives latitude and longitude for reverse geocoding.
- Google Maps links are generated on physical-unit detail pages.

Future privacy documentation should disclose that coordinates may be sent to third-party map services.

## 21. Testing and QA state

Current automated tests are concentrated in `tests/unit`:

- `MetricsTest`: stock status, time calculations, sequential identifiers, SLA.
- `UiHelperTest`: date/time and badge helpers.
- `BaseModelTest`: extraction compatibility and LKP number format.
- `HealthTest`: basic project health.

CI4 example database/session tests and support fixtures remain in the repository. They are not broad coverage of IPSRS workflows.

Latest local verification during this Codex session:

```text
php vendor/bin/phpunit --no-coverage --no-logging --do-not-cache-result
53 tests, 65 assertions, all passed
```

Two test-only fixes are present in the working tree and are not committed at this snapshot:

- `BaseModelTest` creates the required prefixed in-memory LKP table.
- `UiHelperTest` expects the current red AC badge instead of the old indigo badge.

Running plain `composer test` emits `No code coverage driver available`; because PHPUnit is configured with `failOnWarning=true`, the command exits nonzero even when assertions pass. Use `--no-coverage` locally unless Xdebug or PCOV is installed, or later split ordinary and coverage scripts.

Major untested workflows include:

- login/session and redirect behavior
- role and direct-route authorization
- public portal submission
- internal LK creation with physical asset
- valid and invalid LK status transitions
- concurrent claim
- spare-part deduction, failure rollback, and concurrent stock use
- LKP multi-write workflow and automatic LK creation
- borrowing and return invariants
- deletion upload validation and rollback
- cannibalization eligibility and rollback
- QR route privacy and GPS ping authorization/rate limiting
- report export content
- browser/mobile responsive behavior
- browser DOM handling of escaped flash/LKP values and report-download/open behavior for formula-like fields

Technical UAT update (16 September 2026): a disposable SQLite + local HTTP run verified the authenticated corrective happy path: login Teknisi → create LK linked to a physical series → claim → Survei → Dalam Perbaikan → Selesai with tindakan/TTD. Persistence assertions confirmed the series FK, technician, terminal LK, completion data, and series `Tersedia`. The database/server/scripts and `.env` override were removed afterward. This does not replace browser/responder UAT, production/MySQL concurrency, or metric-correctness evidence.

`UAT_Checklist.md` documents manual checks but remains unchecked in Git. Its final claim that passing a short checklist means the application is fully ready should not replace regression testing.

## 22. Documentation state

### Strong documentation

The user-guide modules are detailed, task-oriented, and cover sidebar-visible workflows. `docs/style_guide.md` records critical domain and writing constraints. `REVISION_LOG.md` explains why the asset lifecycle and ERD changed.

### Outdated or misleading documentation

- Root `README.md` is still the generic CodeIgniter AppStarter README. It does not identify IPSRS, setup, schema deployment, roles, production deployment, or tests.
- `PROJECT_STATE.md` is dated 2026-07-10 and predates public portal, asset series, normalized locations, borrowing, deletion, later routes, and production hosting.
- `PROJECT_STATE.md` still describes old table names and old asset statuses.
- The committed `env` is generic and lacks project-specific variable documentation.
- No canonical deployment runbook explains cPanel document root, writable permissions, environment mode, database deployment order, rollback, backup, or health checks.
- No canonical database bootstrap guide resolves overlapping migrations and one-off SQL scripts.
- No explicit role/permission matrix exists.
- No known-limitations or operational-support document exists.

### User-guide writing rules

When editing user documentation:

1. Assume a nontechnical user who did not build the system.
2. Explain exact control location, visual appearance, purpose, and result.
3. Define UI terms in simple language.
4. Use numbered, step-by-step instructions.
5. Use image placeholders with chapter-based numbering.
6. Use bold control labels followed by a colon.
7. Add strong warnings for destructive actions.
8. Explain automation so users understand what the system does after a click.
9. Use only IPSRS/non-medical asset examples.

## 23. Development chronology and durable decisions

The Git history and transcript show these major phases:

1. Initial CI4 application and MySQL migration from a former Supabase/PostgreSQL direction.
2. Core modules: authentication, dashboard, asset CRUD, LK, preventive maintenance, inventory, vendor, master data, reporting, and cannibalization.
3. UI overhaul using Tailwind and the RSUD red visual identity.
4. UAT-driven fixes for forms, dropdowns, report workflow, LKP, sidebar, and master-data deletion.
5. Major asset refactor from one-table asset records to `aset` parent plus `aset_series` physical units.
6. Manual cPanel data migration and repeated production hotfixes for old/new column mismatches.
7. Asset lifecycle addition: borrowing, return, heavy-damage handling, cannibalization, deletion, and BA.
8. ERD integrity work: foreign keys, unique constraints, status ENUM, indexes, and normalized location master.
9. QR flow isolation from login, public GPS ping, tracking-column fixes, iOS permission guidance, and scan-page redesign.
10. Public reporter portal addition, production 500 debugging, CSRF-field insert fix, and premium portal redesign.
11. Complete user-guide authoring and DOCX generation.
12. Antigravity transcript export for continuity.
13. RTK update/configuration and project-local Caveman skill installation.

Durable product decisions:

- The system is for IPSRS sarpras/non-medical assets.
- Physical operations must target `aset_series`.
- Public reporting at `/lapor` does not require login.
- QR scan and GPS ping are public but must not expose unrelated internal modules.
- Self-registration is disabled.
- Pelapor should only view reports associated with their identity or unit and must not operate repair controls.
- Asset lifecycle actions should be contextual on physical-unit detail pages.
- Deletion requires a BA record.
- Preventive findings may automatically create corrective work.
- Production deployment is Git pull plus a separate database step.

## 24. Known verified defects and high-risk inconsistencies

This section records issues found while building context. It is not a completed full audit, and no production code was changed for these items.

### A. Internal LK loses selected asset link

- **Location:** `app/Controllers/LK.php`, `store()`.
- **Evidence:** form uses `id_aset`; whitelist omits it; `$post` is referenced without definition; `id_aset_series` becomes null.
- **Remediation 16 September 2026:** `LK::store()` maps selected `id_aset` only after a server-side series lookup, then writes `id_aset_series`; the inline/IIFE JavaScript mismatch was removed. An isolated authenticated HTTP flow persisted the expected FK and synchronized the series status. Browser Select2/location smoke and lifecycle/RBAC guards remain open, so F08 is `Implemented — partially verified`, not fully verified.

### B. Asset status vocabularies conflict

- **Locations:** `app/Config/IPSRS.php`, `Aset::storeMutasi()`, `Kanibal::riwayat()`, `ui_helper.php`, manual ERD ENUM SQL.
- **Evidence:** modern set uses `Tersedia`, `Dipinjam`, `Dalam Perbaikan`, `Rusak Berat`, `Dihapuskan`; older code still writes or expects `Aktif`, `Tidak Aktif`, `Menunggu Suku Cadang`, `Menunggu Vendor`, `Di Gudang`, `Kanibal`, and `Dibuang`.
- **Likely effect:** rejected MySQL updates, hidden contextual actions, wrong filters, or states that cannot be displayed consistently.
- **Remediation 16 September 2026 (partial):** LK status synchronization now maps waiting states to `Dalam Perbaikan` and completion to `Tersedia`, all values accepted by the actual series enum. Other lifecycle writers, enum migration, history, and terminal guards remain unresolved; do not describe the lifecycle as fully unified.

### C. No centralized server-side RBAC

- **Locations:** route group, `AuthFilter`, most controllers.
- **Evidence:** protected routes check authentication only; role restrictions are mainly sidebar conditions and pelapor-specific checks in LK.
- **Likely effect:** authenticated users may invoke privileged endpoints by direct HTTP request.

### D. AuthFilter may expose more asset routes than intended

- **Location:** `app/Filters/AuthFilter.php`.
- **Evidence:** UUID-shaped `/ipsrs/aset/{uuid}` and `/ipsrs/aset/{uuid}/qr` paths return early before session validation.
- **Likely effect:** asset detail or QR generation may be public despite the intended narrow public scope.

### E. Public reporting CSRF remediation requires browser verification

- **Locations:** `app/Config/Filters.php`, public `/lapor` route.
- **Evidence:** CSRF paths now include `lapor`; local HTTP POST without a token returns 403, while a valid login token reaches controller validation.
- **Remaining verification:** public browser submission with a valid token and normal validation/error feedback must still be smoke-tested. CSRF does not replace rate limiting or anti-abuse controls for anonymous reporting.

### F. Multi-table business workflows are non-transactional

- **Locations:** asset loan/delete/mutation, LK spare parts/delete, preventive LKP save, cannibalization, public report asset update plus LK insert.
- **Evidence:** no transaction calls found in controllers or models.
- **Likely effect:** partial writes and inconsistent state after database, upload, or application failure.

### G. Stock optimistic locking records history too early

- **Location:** `app/Models/StokModel.php`.
- **Evidence:** history insert occurs before the conditional balance update and outside a transaction.
- **Likely effect:** transaction history can disagree with actual balance; concurrent outgoing operations can over-record consumption.

### H. BA upload remediation requires deployment verification

- **Location:** `BaDocumentStorage`, `Aset::hapus()`, `Aset::downloadBa()`, `public/uploads/ba/.htaccess`.
- **Evidence:** new BA uploads use the server-side PDF/JPEG/PNG MIME allowlist, 5 MB limit, writable storage, transaction cleanup, and authenticated Admin attachment download. Unit policy test passes, as documented in `PROJECT-REMEDIATION-PLAN.md`.
- **Remaining verification:** run real multipart and authorization tests and verify cPanel/Apache honors the legacy directory rule. Metadata persistence and BA-required business policy are not yet implemented.

### I. State transition validation is absent

- **Location:** `LK::updateStatus()` and asset lifecycle actions.
- **Evidence:** posted state is accepted without `in_list` or transition graph; lifecycle endpoints do not enforce current state.
- **Likely effect:** skipped workflow stages, reopening completed items, duplicate loans, deletion from invalid states, and inconsistent metrics.

### J. External CDN dependence

- **Location:** main layout and standalone public pages.
- **Evidence:** CSS, table behavior, dropdowns, alerts, charts, maps, QR generation, signature input, and fonts load remotely.
- **Likely effect:** hospital UI degrades when internet access, DNS, or a CDN is unavailable.

### K. Debug/utility scripts remain under public or root paths

- **Example:** tracked `public/check.php` directly connects to local MySQL and prints `SHOW CREATE TABLE template_checklist`.
- **Likely effect:** if deployed under the public document root, it exposes schema details and confirms database connectivity.

### L. Fresh database installation is not reproducible

- **Evidence:** old full schema, manual follow-up migrations, duplicate/misspelled migration directory, SQL dialect remnants, and production-only changes.
- **Likely effect:** new environments can silently differ from production and fail in routes that depend on later columns or constraints.

## 25. Performance characteristics

Actual current patterns:

- Dashboard fetches complete LK, stock, and schedule datasets and aggregates in PHP.
- Asset, LK, stock, vendor, user, category, and code searches commonly load all data then use `array_filter`.
- No backend pagination is implemented for major lists.
- DataTables enhances some tables client-side, so initial payload still contains all rows.
- `AsetModel::getAll()` performs `GROUP_CONCAT` of every series status per parent.
- Physical asset dropdowns load all series and joined location data.
- LKP form loads all checklist templates before category selection.
- No application cache strategy is documented.

This is acceptable for a small thesis dataset, but not a proven production-scale design. Optimize only after measuring real row counts and query timings. The first likely thresholds are physical asset dropdown size, LK table size, stock history size, dashboard aggregation, and report exports.

## 26. Current Git and workspace state at snapshot

At the time this file was created:

- `HEAD`, `origin/main`, and `origin/HEAD` all pointed to `9dfc1d4`.
- The production portal redesign was already committed and pushed.
- Working tree contained unrelated or uncommitted files. Future agents must run `rtk git status --short` and preserve them.
- Known modified files included the formal DOCX guide, `skills-lock.json`, and two PHPUnit files.
- A Word lock/temp file was deleted in the working tree.
- Project-local Caveman skill directories, `AGENTS.md`, `RTK.md`, AI transcript files, signature SQL files, and `writable/ipsrs.db` were untracked.
- Do not stage everything with `git add -A`. Stage only files explicitly belonging to the active task.
- Do not discard DOCX or SQL changes without asking because they may be user work.

## 27. How a future agent should start

Use this order:

1. Read `AGENTS.md` and `RTK.md`.
2. Read this file completely.
3. Run `rtk git status --short`.
4. Run `rtk git log -8 --oneline --decorate`.
5. Confirm whether the task targets local code, production behavior, database state, documentation, or UI.
6. Read only the current route, controller, model, view, schema fragment, and tests for that task.
7. If database behavior matters, verify actual production or local table shape; do not rely solely on July migrations.
8. Reproduce the problem before editing when economical.
9. Preserve the parent-versus-series distinction.
10. Enforce permission and validation on the server, even when UI already hides a control.
11. Use transactions for new multi-table mutations.
12. Run focused tests, then `rtk php vendor/bin/phpunit --no-coverage`.
13. Review the diff and Git status before any commit.
14. Commit and push only when the user requests it or the active task already includes that authorization.
15. For production delivery, provide the exact Git pull and separate database steps.

## 28. Commands and operational notes

Common safe commands:

```powershell
rtk git status --short
rtk git log -8 --oneline --decorate
rtk read AI/PROJECT_CONTEXT.md
rtk read app/Config/Routes.php
rtk php vendor/bin/phpunit --no-coverage
rtk composer show --direct
rtk gain
```

RTK must prefix shell commands according to `RTK.md`. On Windows, use `rtk proxy powershell` or `rtk proxy <command>` when a native wrapper cannot resolve a Unix command.

## 29. Documentation-versus-implementation quick reference

| Documentation statement | Current implementation reality |
|---|---|
| `README.md` describes a generic CodeIgniter starter. | Repository is a domain-specific IPSRS production deployment. |
| `PROJECT_STATE.md` describes one asset table and old statuses. | Runtime uses parent `aset`, physical `aset_series`, normalized locations, and lifecycle records. |
| `PROJECT_STATE.md` lists old routes and CSV export. | Routes include portal, series, borrowing, deletion, claim, and Excel exports. |
| Old documentation lists a database session table. | Current Session config uses filesystem storage. |
| Revision log says five asset statuses are enforced. | Several controllers and helpers still write or display legacy statuses. |
| UAT document implies readiness after checklist completion. | Checklist is unchecked and critical workflows lack automated tests. |
| User guide describes public reporting and logged-in pelapor. | Both flows exist, but internal LK asset association is currently defective and RBAC remains incomplete. |
| User guide describes automatic stock deduction. | Deduction exists, but multi-write consistency and concurrency handling remain risky. |

## 30. Glossary

- **IPSRS:** Instalasi Pemeliharaan Sarana Rumah Sakit.
- **Aset parent/catalog:** logical description of an asset type in `aset`.
- **Aset series/unit fisik:** individually inventoried physical asset in `aset_series`.
- **LK:** Laporan Kerusakan, the corrective-maintenance ticket.
- **LKP:** Lembar Kerja Preventif, the preventive-maintenance result and checklist.
- **PM:** preventive maintenance.
- **BA:** Berita Acara required for formal asset deletion.
- **Kanibal:** removal of a usable component from a donor physical asset for another repair.
- **Pelapor:** internal reporter role with limited access to relevant LK records.
- **Portal publik:** unauthenticated `/lapor` flow.
- **Nomor aset:** human-facing inventory identifier on `aset_series`.
- **UUID:** internal primary identifier, typically `CHAR(36)`.
- **Response time:** minutes from report time to initial technical response/check.
- **Down time:** minutes from report time to completion.
- **Proses I/II/III:** repair classification inferred from internal work, spare parts, or vendor involvement.

## 31. Confidence and verification limits

High-confidence statements in this file come directly from current source, Git history, or explicit user conversation. Production database constraints, active `.env`, cPanel document-root configuration, upload permissions, and the exact deployed commit were not queried directly while creating this file. Treat those as deployment facts to re-verify before high-risk work.

This memory is deliberately specific, but current implementation remains the final source of truth. Update this file after major architecture, schema, route, deployment, or workflow changes so the next session does not inherit stale assumptions.

## 32. Iteration 01F timing contract (16 September 2026)

- Corrective LK response time is now the server-validated minute difference from `laporan_kerusakan.tanggal` + `jam_laporan` to the first `Survei` timestamp. A value of `0` is valid and is not overwritten.
- Disposition no longer stores `tanggal_cek`/`jam_cek`. The progress form enables those inputs only for `Survei`; the controller rejects missing, invalid, or pre-report survey times even if a request bypasses the UI.
- `Selesai` ignores request-supplied `tanggal_selesai` and `jam_selesai`; completion uses the server date/time, rejects a report timestamp later than that server time, and records down time separately. Missing response time stays `NULL`; it is never inferred from down time.
- Regression evidence: `tests/unit/LkTimingContractTest.php` PASS (4 tests / 7 assertions); full PHPUnit PASS (60 tests / 97 assertions), plus lint and `git diff --check` PASS. This is not browser/respondent UAT, HTTP timestamp-tampering proof, MySQL concurrency evidence, or a repair of historical metrics.

## 33. Iteration 01G reporting-period contract (16 September 2026)

- `app/Libraries/ReportPeriod.php` is the canonical period definition for reports: `minggu` means the inclusive Monday–Sunday calendar week; `bulan` and `tahun` use calendar boundaries; unknown values become `bulan`.
- LK report/KPI filters use LK `tanggal` (the report date). PM KPI uses scheduled `jadwal_preventif.tanggal`. Preventive print/Excel filter LKP by `tanggal_pemeriksaan`; their labels state the precise date range and data source.
- Preventive report hydration now prioritizes the schedule text snapshot (`jadwal_preventif.aset` and `lokasi`) before reading current series/master values. `aset_series.nomor_aset` is labelled as an inventory number, not a serial number. This is only a best available snapshot: LKP does not persist immutable identity/location columns, and a changed/deleted schedule can still force fallback behavior.
- Regression evidence: `tests/unit/ReportPeriodTest.php` PASS (4 tests / 7 assertions); full PHPUnit PASS (64 tests / 104 assertions), plus lint and `git diff --check` PASS. Browser/Excel opening, production timezone, export performance, and human sample reconciliation remain required before marking F25 fully verified.

## 34. Iteration 01H preventive-LKP contract (16 September 2026)

- `Preventif::simpanLkp()` validates checklist rows server-side and uses one transaction for schedule claim, LKP header/details, template additions, schedule completion, and optional auto-LK. `JadwalModel::claimForCompletion()` permits only one conditional claim from `Belum`; retry after a committed completion cannot create a second LKP.
- New LKP rows persist `nama_user_ttd`. Location conformity is preserved as a `Teks` checklist row. `LkpChecklist` preserves `Teks` result and note inside structured `detail_checklist_lkp.keterangan`, while `0` remains a valid measurement value. The result view decodes new text rows and keeps legacy rows readable.
- Auto-LK source lineage is only textual (`Temuan PM [LKP-…]`) because deployed `laporan_kerusakan` has no `source_lkp_id` FK/column. It must not be described as a database-enforced relation. No schema migration was run.
- Direct PHPUnit evidence: `LkpChecklistTest` PASS (3 tests / 6 assertions), `PreventifCompletionModelTest` PASS (1 test / 6 assertions), and full direct PHPUnit PASS (68 tests / 116 assertions). Tests use disposable SQLite for the DB contract; browser/HTTP/CSRF, MySQL concurrency/rollback, forced auto-LK failure, and respondent UAT remain open.

## 35. Iteration 01I corrective handover evidence contract (16 September 2026)

- `SignatureEvidence` now validates new LK close evidence at the server boundary. It accepts only a real decodable `data:image/png;base64,...` payload and rejects wrong prefixes, malformed base64, non-image bytes, and decoded payloads over 1 MiB. It is deliberately a format/evidence validator, not a cryptographic-signature verifier.
- A transition to `Selesai` needs `tindakan` plus normalized PNG evidence. The LK detail view labels it as **bukti serah-terima operasional**, associates it with the pre-existing pelapor/unit fields, and shows server-created close date/time. The UI explicitly says it is not a certified digital signature.
- LKP has no canvas signature flow. Its stored `nama_user_ttd` is presented as `Nama Perwakilan Unit` / `Perwakilan Unit` to avoid claiming a digital signature that the application does not store or authenticate.
- Direct full PHPUnit PASS (**70 tests / 120 assertions**), including `SignatureEvidenceTest`. Browser canvas flow, HTTP tampering, MySQL data validation, independently authenticated pelapor approval, signer/actor metadata, and immutable document-version binding are still open. Do not describe current evidence as formal approval, non-repudiation, or cryptographic digital signature.

## 36. Iteration 01J legacy role/object-access guard (16 September 2026)

- `AuthFilter` revalidates the `pengguna` row for every internal request. A missing, inactive, or unsupported-role account is redirected to login; role/name/unit are refreshed from the authoritative row. Login also normalizes stored role values to lower-case session roles and rejects values outside `admin`, `teknisi`, and `pelapor`.
- Central route gate: Pelapor can use only LK internal routes. Teknisi is denied management/master/report/loan/kanibal modules, can only read asset/stock routes, and can mutate preventive data only through LKP submission. Admin retains current operational access.
- `AccessPolicy` protects LK list/show and LK mutation (detail/status/parts/vendor) by current reporter/technician name; it protects preventive list/LKP/result by scheduled technician name and makes schedule create/direct-complete/delete Admin-only. LK deletion is Admin-only.
- This is a compatibility safeguard, not complete identity authorization: deployed LK and schedule data still uses mutable display names rather than stable user-ID FKs. Full direct PHPUnit PASS (**73 tests / 133 assertions**) including `AccessPolicyTest`; authenticated HTTP allow/deny, non-assigned-tech, inactive-session, and MySQL/schema evidence remain open.

## 37. Iteration 01K inventory movement and LK reversal transaction (16 September 2026)

- `StokModel::catatTransaksi()` now performs a conditional balance mutation first and only then writes its ledger. `Keluar` needs `stok_tersedia >= jumlah` in SQL; shortage produces neither a negative balance nor a new ledger row. Every caller currently wraps it in a database transaction.
- LK Gudang part addition atomically creates the stock debit/ledger and `detail_suku_cadang_lk` row. It refuses duplicate Gudang item submissions for the same LK and additions after `Selesai`. LK hard-delete atomically credits only Gudang detail rows and their reversal ledgers before deleting the LK. `Kanibal` rows are excluded from warehouse credit.
- Direct SQLite evidence: `InventoryTransactionModelTest` PASS (3 tests / 7 assertions); full direct PHPUnit PASS (**76 tests / 140 assertions**), lint and `git diff --check` PASS. MySQL concurrency, DB uniqueness/idempotency keys, forced controller rollback, and an auditable cancellation replacement for hard delete remain open.

## 38. Iteration 01L asset lifecycle and location guard (16 September 2026)

- `AsetLifecycle` centralizes the implemented lifecycle guard. Borrow/edit/relocate require `Tersedia`; return requires `Dipinjam`; formal disposal requires `Rusak Berat`; LK synchronization cannot revive `Rusak Berat` or `Dihapuskan` to `Tersedia`.
- Pinjam, return, formal disposal, and room mutation now use conditional state writes plus database transactions. Room mutation is intentionally narrowed to `Pindah Ruangan` with a real `master_lokasi` record, and history plus `id_lokasi` write together. The previous UI/server paths that wrote non-enum lifecycle strings (`Aktif`, `Di Gudang`, `Kanibal`, `Dibuang`) have been removed from active mutation.
- New kanibal candidate display reads canonical `Rusak Berat` and keeps legacy `Kanibal` only for historical read compatibility. This does not complete F11 donor/recipient/approval integrity.
- Direct `AsetLifecycleTest` PASS (3 tests / 12 assertions); full direct PHPUnit PASS (**79 tests / 152 assertions**), lint and `git diff --check` PASS. Legacy rows, duplicated legacy location text, portal/LK transaction atomicity, and MySQL/HTTP concurrency evidence remain open.

## 39. Iteration 01M kanibal-transfer integrity (16 September 2026)

- `KanibalTransfer` makes kanibal one transaction: history, LK `Kanibal` detail, conditional donor removal, and receiver installation/update. The operation requires Admin, a processing LK linked to the receiver, a `Rusak Berat` donor, a non-terminal receiver, and an available exact donor component.
- LK number and approver are server-derived; the browser cannot set `no_order_lk` or `disetujui_oleh`. Approval is still a compatibility display name, not a user-ID-backed formal approval.
- The LK form shows canonical donor candidates and loads components through `/ipsrs/aset/series/{id}/komponen`; the server remains authoritative against manipulated POST data.
- Direct `KanibalTransferTest` PASS (3 tests / 12 assertions); full direct PHPUnit PASS (**82 tests / 164 assertions**), targeted lint and `git diff --check` PASS. Browser/HTTP UAT, MySQL multi-connection proof, and actor-FK approval remain open.

## 40. Iteration 01N dependency-security baseline (17 September 2026)

- Composer lock now uses CodeIgniter 4.7.4 and PHPSpreadsheet 5.9.0 under the existing composer constraints. The upgrade also updated its normal transitive packages; no schema or application code changed.
- `composer validate` and `composer audit --locked` pass with no security advisory. Route compilation and full direct PHPUnit pass (**82 tests / 164 assertions**).
- Local PHP CLI lacks `ext-zip`; XLSX export cannot be exercised locally even though PHPSpreadsheet is installed. This is a deployment gate: hosted PHP must enable `ext-zip` and pass the deployment checklist before final release claims.

## 41. Iteration 01O request-level access and CSRF proof (17 September 2026)

- `tests/feature/AuthFilterHttpTest.php` uses the real CodeIgniter route/filter stack and disposable SQLite tables. It proves: Teknisi → Kanibal redirects to `/ipsrs`; Pelapor → Aset redirects to `/ipsrs`; a disabled account session redirects to `/login`; a Teknisi cannot view another Teknisi's LK, LKP form, or LKP result by direct ID; POST `/lapor` without CSRF token raises framework `SecurityException` before controller execution; a Pelapor's valid-CSRF LK claim is rejected without changing the ticket status or technician; and a Teknisi's valid-CSRF preventive-schedule deletion is blocked without deleting the schedule.
- Direct focused evidence PASS (9 tests / 20 assertions), and full direct PHPUnit PASS (**91 tests / 184 assertions**). This is request-level negative proof, not full route/record authorization or valid-token browser proof.

## 42. Iteration 01P route/action repair (17 September 2026)

- Static form/fetch versus route inventory found a real missing route: Vendor delete form posted to `/ipsrs/vendor/{id}/delete` but only `Vendor::delete()` existed. `Routes.php` now declares that POST route, compiled with existing `csrf auth` filters.
- PHP lint, `php spark routes`, and full direct PHPUnit PASS (**91 tests / 184 assertions**). Browser confirmation, valid-CSRF submission, and FK failure feedback remain UAT work.

## 43. Iteration 01Q internal asset-route authentication repair (17 September 2026)

- Static review found `AuthFilter` returned early for UUID-shaped `/ipsrs/aset/{id}` and `/ipsrs/aset/{id}/qr`, even though both routes are inside the authenticated group. An anonymous request reached `Aset::show()` before the fix, proving a real authentication bypass rather than a hypothetical concern.
- The legacy early-return block was removed. Public `/ipsrs/aset/scan/{id}` and `/ipsrs/aset/{id}/ping` remain outside the authenticated route group and therefore retain their intended public behavior.
- `AuthFilterHttpTest` now proves anonymous internal asset detail redirects to `/login`. Focused proof PASS (**10 tests / 22 assertions**); route compilation and full direct PHPUnit PASS (**92 tests / 186 assertions**). QR-specific browser behavior and public ping telemetry limitation remain separate work.

## 44. Autonomous work inventory (17 September 2026)

- `AI/AUTOMATABLE-BACKLOG.md` is the current execution inventory. It separates source/test/documentation work that can run locally from browser UAT, hosting, MySQL concurrency, live-data repair, and external integration evidence that cannot be claimed by local automation.
- Recommended next work is request-level LK mutation authorization, then LKP POST authorization and BA multipart/storage proof. Baseline remains **92 tests / 186 assertions PASS** at the time this inventory was written.

## 45. Iteration 01R LK mutation authorization matrix (17 September 2026)

- `AuthFilterHttpTest` now sends valid-CSRF POST requests through the real router/filter/controller stack. A Teknisi cannot mutate another Teknisi's LK through detail, spare-part, or vendor endpoints; a Pelapor cannot delete LK. Post-request assertions confirm the cross-technician LK detail and assignment remain unchanged and the Pelapor target still exists.
- Focused proof PASS (**12 tests / 33 assertions**); direct full PHPUnit PASS (**94 tests / 197 assertions**). This expands F02/F09/F16 evidence but does not replace user-ID ownership migration, MySQL concurrency, or browser UAT.

## 46. Iteration 01S preventive POST object authorization (17 September 2026)

- A valid-CSRF Teknisi POST to another Teknisi's `/ipsrs/preventif/lkp/{jadwal}` now has direct feature-test evidence: it redirects to the preventive index and leaves schedule status `Belum`. The guard executes before checklist/LKP writes.
- Focused proof PASS (**13 tests / 36 assertions**); direct full PHPUnit PASS (**95 tests / 200 assertions**). This expands F02/F13/F14/F16 proof; duplicate successful submission and MySQL race evidence remain separate.

## 47. Iteration 01T BA direct-download and GPS telemetry contract (17 September 2026)

- A direct BA download URL as Teknisi redirects to `/ipsrs`; the request cannot reach document lookup/download logic. This is partial F01 request-level access evidence, not multipart/storage hosting proof.
- `Aset::ping()` now persists canonical `session('user_name')` rather than unused `session('nama')`. `PublicPingHttpTest` proves a valid-CSRF JSON ping writes valid coordinates, server timestamp, and session actor in disposable SQLite.
- Focused feature proof PASS (**15 tests / 43 assertions**); direct full PHPUnit PASS (**97 tests / 207 assertions**). GPS remains non-authoritative client telemetry; public device/browser and rate-limit UAT remain open.

## 48. Iteration 01U terminal LK immutability and GPS negative contracts (17 September 2026)

- Completed LK now rejects detail edits and vendor additions in the controller, matching the existing spare-part terminal guard. Valid-CSRF feature tests also prove spare-part addition and status reopening are denied; status and complaint remain unchanged.
- GPS feature coverage now includes valid persistence, invalid coordinate range without writes, unknown-series 404, and recent-update rate limiting without coordinate overwrite. Rate-limit responses return `updated=false`, and the scan UI stops before presenting a new point as saved.
- Focused affected proof PASS (**19 tests / 66 assertions**); direct full PHPUnit PASS (**101 tests / 230 assertions**). Browser/device GPS and MySQL concurrency remain open.

## 49. Iteration 01V asset loan/return runtime repair (17 September 2026)

- `Aset::pinjam()` and `Aset::kembali()` referenced the nonexistent `\AppModels\AsetSeriesModel`, causing both Admin happy paths to fail before lifecycle checks. Both now resolve the actual `\App\Models\AsetSeriesModel`.
- `AssetLoanHttpTest` runs valid-CSRF Admin requests through real routing/auth/controller/database layers. Borrow changes one available series to `Dipinjam` and creates one active loan; return completes that loan, records the actual return date, and restores the series to `Tersedia`.
- Focused lifecycle proof PASS (**5 tests / 21 assertions**); direct full PHPUnit PASS (**103 tests / 239 assertions**). MySQL concurrent requests and browser interaction remain open.

## 50. Iteration 01W lifecycle rollback and BA storage proof (17 September 2026)

- Borrow now validates series, borrower, unit, and planned-return date server-side. Tests prove missing input and non-available series create no loan and preserve state. A duplicate-active-loan return updates two rows inside a transaction, detects the invariant violation, and rolls the entire change back.
- BA storage proof now exercises an `UploadedFile` test double at the CLI/SAPI boundary: PNG content named `.php` is detected by content, stored under a random `.png` key below `WRITEPATH/uploads`, resolves outside `FCPATH`, and is removed through the storage API. This is not claimed as real web multipart evidence.
- Focused proof PASS (**10 tests / 31 assertions**); direct full PHPUnit PASS (**107 tests / 256 assertions**). Hosting multipart/document-root behavior and MySQL concurrency remain manual gates.

## 51. Iteration 01X automated phase release gate (17 September 2026)

- The cautious automated phase ends here. `AI/REQUIREMENT-EVIDENCE-TRACEABILITY.md` maps each thesis claim to implementation, automated evidence, required manual UAT, and explicit limitation.
- Final local gates PASS: PHP route compilation; Composer manifest validation; Composer advisory audit with no known advisories; direct full PHPUnit **107 tests / 256 assertions**.
- Remaining work is evidence collection rather than open-ended implementation: corrective/preventive/kanibal/lifecycle browser UAT, QR/GPS device checks, hosting multipart and `ext-zip`, HTTPS/cookie parity, backup/restore, and MySQL concurrency where required. Number 8 (migration/import/notification scope work) remains intentionally deferred by user instruction.

## 52. Iteration 01Y production hardening (18 September 2026)

- Removed `public/check.php`, which executed a schema probe using privileged default credentials inside the public document root. A regression test now permits only `public/index.php` as public PHP.
- Login now uses CodeIgniter's throttler at five attempts per account and 20 attempts per IP per 60 seconds. Active login resets both buckets; unknown and inactive accounts retain the same generic credential error.
- Session regeneration destroys the old session record, and the global `secureheaders` filter adds frame/content-type/referrer protections. Database config defaults now fail closed until environment credentials are supplied.
- Public registration routes were removed because registration is outside product scope. The compiled table now has 77 explicit routes.
- Added repeatable production preflight, disposable backup/restore drill, 20,000-row performance smoke, route-security inventory, deployment/recovery runbook, and release-record template.
- Local evidence: preflight 23 PASS/5 FAIL/3 MANUAL; recovery drill PASS; HTTP smoke login 200/internal 302/debug endpoint 404; Chrome headless rendered the login form and CSRF/input contract; full PHPUnit **115 tests / 296 assertions PASS**; Composer/route/advisory gates PASS.
- The benchmark confirms F26 as a realistic scaling issue: current-style load-all + PHP filter took 38.082 ms versus 2.224 ms for a 50-row SQL page on synthetic SQLite. This is evidence for future server-side pagination, not a hosted MySQL SLA.
- Authenticated business-flow browser E2E, hosted configuration, MySQL concurrency/restore, QR/GPS device proof, and user acceptance remain external. Migration/import/notification work remains deferred.

## 53. Iteration 01Z release dependency compatibility (21 September 2026)

- The release lock is explicitly resolved against PHP `8.2.0`/64-bit through Composer platform overrides. This prevents a newer transitive ZipStream release from silently raising the hosting requirement to PHP 8.3.
- The verified lock uses CodeIgniter `4.7.4`, PhpSpreadsheet `5.10.0`, ZipStream `3.1.2`, and PHPUnit `10.5.64`. Composer install, manifest validation, and locked advisory audit pass; no known advisory is reported.
- Full PHPUnit remains **115 tests / 296 assertions PASS** on the installed lock. The runner reports only that local code coverage is unavailable; this is not a test failure.
- `vendor/` is excluded from the release commit. Deployment must run `composer install --no-dev --optimize-autoloader` on the server with PHP 8.2+ 64-bit and `ext-zip` enabled.
- The server `.env` remains deployment-local and untracked. Back it up and update it during deployment, then prove it using hosted preflight and smoke/UAT evidence.
