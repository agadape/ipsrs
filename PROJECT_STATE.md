# PROJECT STATE - IPSRS Application
**Last Updated:** 2026-07-10  
**Status:** Fase 1-8 Completed, Ready for Testing  
**App URL:** http://localhost:8080

---

## Overview
CodeIgniter 4.7.2 application for hospital facility management (IPSRS) running on Laragon with MySQL 8.x. Migrated from Supabase PostgreSQL to local MySQL. All IDs use UUID (`CHAR(36)`), with sequential display numbers for business references.

---

## Tech Stack
- **Framework:** CodeIgniter 4.7.2
- **PHP:** 8.2.12
- **Database:** MySQL 8.x (Laragon, root, no password, `ipsrs` database, port 3306)
- **Frontend:** Tailwind CSS (CDN), Blade-like views with `$this->render()`
- **Auth:** Local email+password (`password_hash`/`password_verify`)

---

## Database Schema (17 Tables)

### Core Tables
| Table | Purpose | Key Columns |
|-------|---------|-------------|
| `aset` | Master assets | `id` (UUID), `nomor_aset` (display), `nama`, `kategori`, `lokasi`, `kondisi`, `status`, `model`, `kapasitas`, `keterangan` |
| `lk` | Laporan Kerusakan (damage reports) | `id`, `no_order` (LK-XXXX), `aset`, `aset_nama`, `kode`, `uraian`, `status` |
| `detail_suku_cadang_lk` | Parts used in LK | `lk`, `barang`, `qty`, `sumber` (Gudang/Kanibal) |
| `vendor_lk` | Vendors for LK | `lk`, `vendor`, `estimasi_biaya`, `estimasi_selesai` |
| `jadwal_preventif` | Preventive maintenance schedules | `id`, `aset` (denormalized text), `kategori_aset`, `jadwal`, `jam`, `status` |
| `lkp` | LKP (Preventive reports) | `id`, `no_order` (LKP-XXXX), `jadwal`, `aset` |
| `detail_lkp` | Checklist items for LKP | `lkp`, `item`, `kondisi` |
| `stok` | Parts inventory | `id`, `barang` (B-XXXX), `nama`, `kategori`, `tersedia`, `minimum` |
| `riwayat_stok` | Stock transactions | `stok`, `jenis` (Masuk/Keluar), `qty`, `referensi`, `tanggal`, `keterangan` |
| `mutasi` | Asset transfers | `id`, `aset`, `lokasi_asal`, `lokasi_tujual`, `tanggal` |
| `riwayat_kanibal` | Parts cannibalization records | `id`, `lk`, `aset_donor`, `aset_penerima`, `tanggal`, `keterangan` |
| `komponen_aset` | Components from kanibalization | `aset`, `nama_komponen`, `kondisi` |

### Master Data Tables
| Table | Purpose |
|-------|---------|
| `pengguna` | Users (email, password, role, unit, initial) |
| `kategori_aset` | Asset categories |
| `kode_kerusakan` | Damage codes (AC, PR, NM, AL, etc.) |
| `vendor` | Vendors/suppliers |
| `template_checklist` | Checklist templates per kategori |

### Session/Log Tables
| Table | Purpose |
|-------|---------|
| `session` | CI4 session storage |

---

## Authentication

### Login Credentials
- **Email:** `admin@rsud-jogja.go.id`
- **Password:** `password`
- **Password Hash:** `$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi`

### Session Keys
- `user_id` - UUID of logged-in user
- `user_email` - Email address
- `user_name` - Display name
- `user_role` - Role (Admin, Teknisi, etc.)
- `user_unit` - Unit assignment
- `user_initial` - User initials

---

## Routes

### Public Routes
```
GET  /                    → Redirect to /ipsrs
GET  /login               → Auth::login
POST /login               → Auth::doLogin
GET  /logout              → Auth::logout
```

### Protected Routes (requires auth)
```
GET  /ipsrs               → Dashboard::index

// Aset
GET  /ipsrs/aset          → Aset::index
GET  /ipsrs/aset/tambah   → Aset::create
POST /ipsrs/aset/tambah   → Aset::store
GET  /ipsrs/aset/:id      → Aset::show
GET  /ipsrs/aset/:id/edit → Aset::edit
POST /ipsrs/aset/:id/edit → Aset::update
POST /ipsrs/aset/:id/ping → Aset::ping
GET  /ipsrs/aset/:id/qr   → Aset::qr
GET  /ipsrs/aset/mutasi   → Aset::mutasi
POST /ipsrs/aset/mutasi   → Aset::storeMutasi

// LK (Laporan Kerusakan)
GET  /ipsrs/lk            → LK::index
GET  /ipsrs/lk/baru       → LK::create
POST /ipsrs/lk/baru       → LK::store
GET  /ipsrs/lk/:id        → LK::show
POST /ipsrs/lk/:id/status      → LK::updateStatus
POST /ipsrs/lk/:id/suku-cadang → LK::addSukuCadang
POST /ipsrs/lk/:id/vendor      → LK::storeVendor

// Preventif
GET  /ipsrs/preventif           → Preventif::index
POST /ipsrs/preventif/tambah    → Preventif::store
POST /ipsrs/preventif/:id/selesai → Preventif::selesai
POST /ipsrs/preventif/:id/hapus   → Preventif::delete
GET  /ipsrs/preventif/lkp/:id      → Preventif::lkp
POST /ipsrs/preventif/lkp/:id      → Preventif::simpanLkp
GET  /ipsrs/preventif/lkp-hasil/:id → Preventif::lihatLkp

// Stok
GET  /ipsrs/stok          → Stok::index
POST /ipsrs/stok/tambah-barang → Stok::tambahBarang
POST /ipsrs/stok/masuk    → Stok::catatMasuk
POST /ipsrs/stok/keluar   → Stok::catatKeluar
GET  /ipsrs/stok/riwayat  → Stok::riwayat

// Kanibal Alat
GET  /ipsrs/kanibal       → Kanibal::riwayat
POST /ipsrs/kanibal       → Kanibal::store

// Master Data
GET  /ipsrs/vendor             → Vendor::index
POST /ipsrs/vendor/tambah      → Vendor::store
POST /ipsrs/vendor/:id/edit    → Vendor::update

GET  /ipsrs/pengguna           → Pengguna::index
POST /ipsrs/pengguna/tambah    → Pengguna::tambah
POST /ipsrs/pengguna/:id/edit  → Pengguna::edit

GET  /ipsrs/kategori-aset      → KategoriAset::index
POST /ipsrs/kategori-aset/tambah    → KategoriAset::tambah
POST /ipsrs/kategori-aset/:id/edit  → KategoriAset::edit

GET  /ipsrs/kode-kerusakan     → KodeKerusakan::index
POST /ipsrs/kode-kerusakan/tambah    → KodeKerusakan::tambah
POST /ipsrs/kode-kerusakan/:id/edit  → KodeKerusakan::edit

// Laporan
GET  /ipsrs/laporan            → Laporan::index
GET  /ipsrs/laporan/export-csv     → Laporan::exportCsv
GET  /ipsrs/laporan/export-print   → Laporan::exportPrint
```

---

## Models

### BaseModel (Abstract)
- Plain abstract class (NOT extends CI4 Model)
- Uses `$this->qb()` returning `BaseBuilder`
- Methods: `getAll()`, `find()`, `create()`, `createWithRetry()`, `update()`, `delete()`, `throwIfError()`, `nextId()`, `nextNoOrder()`

### Child Models
| Model | Table | Key Methods |
|-------|-------|-------------|
| `PenggunaModel` | `pengguna` | `getAll()`, `getActive()`, `getByRole()`, `findByEmail()` |
| `AsetModel` | `aset` | `getAll()`, `getRiwayatLokasi()`, `insertRiwayatLokasi()`, `nextId()` |
| `LKModel` | `lk` | `getAll()`, `getByAset()`, `getSukuCadang()`, `addSukuCadang()`, `getVendor()`, `addVendor()`, `nextNoOrder()` |
| `LkpModel` | `lkp` | `getByJadwal()`, `addDetail()` (insertBatch), `getDetail()`, `nextNoOrder()` |
| `JadwalModel` | `jadwal_preventif` | `getAll()`, `markSelesai()`, `getByAset()` |
| `StokModel` | `stok` | `nextBarangId()`, `catatTransaksi()` (optimistic locking), `getRiwayat()` |
| `MutasiModel` | `mutasi` | `getAll()`, `getByAset()`, `create()` |
| `KategoriAsetModel` | `kategori_aset` | `getAll()` |
| `KodeKerusakanModel` | `kode_kerusakan` | `getAll()` |
| `VendorModel` | `vendor` | `getAll()` |
| `TemplateChecklistModel` | `template_checklist` | `getAll()`, `getByKategori()`, `kategoriList()` |
| `RiwayatKanibalModel` | `riwayat_kanibal` | `getAll()`, `getByAset()`, `getByLk()`, `getByNoOrder()` |
| `KomponenAsetModel` | `komponen_aset` | `getAll()`, `getByAset()`, `deleteByAset()` |

---

## Controllers

### Auth
- `login()` - Show login form
- `doLogin()` - Process login (local MySQL auth)
- `logout()` - Destroy session

### Dashboard
- `index()` - KPI, status rail, pipeline, top items

### Aset
- `index()` - List all assets
- `create()` - Show add form
- `store()` - Create new asset
- `show($id)` - View asset details + komponen + riwayat kanibal
- `edit($id)` - Show edit form
- `update($id)` - Update asset
- `ping($id)` - Check connectivity
- `qr($id)` - Generate QR code
- `mutasi()` - List mutations
- `storeMutasi()` - Create mutation

### LK (Laporan Kerusakan)
- `index()` - List all LK
- `create()` - Show add form
- `store()` - Create new LK
- `show($id)` - View LK details
- `updateStatus($id)` - Change status
- `addSukuCadang($id)` - Add parts (Gudang or Kanibal)
- `storeVendor($id)` - Add vendor

### Preventif
- `index()` - List schedules
- `store()` - Add schedule
- `selesai($id)` - Mark complete + generate LKP
- `delete($id)` - Delete schedule
- `lkp($id)` - Show LKP form
- `simpanLkp($id)` - Save LKP
- `lihatLkp($id)` - View completed LKP

### Stok
- `index()` - List inventory
- `tambahBarang()` - Add new item
- `catatMasuk()` - Record incoming
- `catatKeluar()` - Record outgoing (with optimistic locking)
- `riwayat()` - View transaction history

### Kanibal
- `riwayat()` - List kanibal history
- `store()` - Process kanibalization (create riwayat + update komponen + update donor keterangan)

### Master Data Controllers
- `Pengguna`, `KategoriAset`, `KodeKerusakan`, `Vendor` - Standard CRUD

### Laporan
- `index()` - Period-based reports
- `exportCsv()` - CSV download
- `exportPrint()` - Print view

---

## Views

### Layout
- `main.php` - Master layout
- `sidebar.php` - Navigation (includes Kanibal Alat)
- `topbar.php` - Header (uses `user_*` session keys)

### Pages
- `auth/login.php` - Login form
- `dashboard/index.php` - Dashboard (hero + KPI + status + pipeline)
- `aset/index.php`, `form.php`, `show.php` - Asset CRUD
- `lk/index.php`, `form.php`, `show.php` - LK CRUD + kanibal toggle
- `preventif/index.php`, `lkp.php`, `lkp_lihat.php` - Preventive maintenance
- `stok/index.php`, `riwayat.php` - Inventory
- `kanibal/riwayat.php` - Kanibal history
- `pengguna/index.php` - User management
- `kategori_aset/index.php` - Asset categories
- `kode_kerusakan/index.php` - Damage codes
- `vendor/index.php` - Vendors
- `laporan/index.php`, `cetak.php` - Reports

---

## Business Rules

### LK (Laporan Kerusakan)
1. Each LK has unique `no_order` (LK-XXXX format)
2. Status flow: Laporan Masuk → Didisposisi → Survei → Dalam Perbaikan → Selesai
3. Parts can come from Gudang or Kanibal (aset lain)
4. Vendor can be added with estimasi biaya/selesai

### Kanibal Alat
1. Donor aset must be "Tidak Aktif" or "Usulan Penghapusan"
2. Penerima aset cannot be the same as donor
3. Free-text component name (not from existing list)
4. Auto-update donor `keterangan` with kanibalization record
5. Creates `riwayat_kanibal` + `komponen_aset` records

### Stok
1. Optimistic locking prevents race conditions
2. Auto-generate `barang` ID (B-XXXX format)
3. Status: Aman (>minimum), Menipis (≤minimum), Habis (≤0)

### Preventif
1. Denormalized `aset` text column (no FK)
2. Status includes 'Terlewat' for overdue schedules
3. LKP auto-generated when schedule marked complete

---

## Known Issues

### PHP String Literal Bug
In `Aset::store()` and `Aset::update()`:
```php
// Current (WRONG):
$keterangan .= '\n' . $catatan;

// Should be:
$keterangan .= "\n" . $catatan;
```
This uses literal backslash-n instead of newline character.

### RiwayatKanibalModel::getByAset()
Uses `orWhere()` which may produce incorrect SQL without proper grouping:
```php
// May need parentheses for correct grouping
->groupStart()
    ->where('aset_donor', $asetId)
    ->orWhere('aset_penerima', $asetId)
->groupEnd()
```

### Untested Controllers
The following controllers have NOT been tested with MySQL:
- Dashboard
- Laporan
- Pengguna
- KategoriAset
- KodeKerusakan
- Vendor
- Preventif
- Stok
- Mutasi

---

## File Locations

### Config
- `app\Config\Database.php` - MySQL config
- `app\Config\IPSRS.php` - SLA, prefixes, status arrays
- `app\Config\Routes.php` - All routes
- `app\Config\Filters.php` - Auth filter on `ipsrs` group

### Models
- `app\Models\BaseModel.php` - Abstract base
- `app\Models\*.php` - All 12 child models

### Controllers
- `app\Controllers\*.php` - All controllers

### Views
- `app\Views\layout\main.php` - Master layout
- `app\Views\layout\sidebar.php` - Navigation
- `app\Views\layout\topbar.php` - Header
- `app\Views\pages\*\*.php` - All page views

### Database
- `app\Database\Migrations\2026-07-01-100000_full_mysql_schema.sql` - Full schema + seeds

### Tests
- `tests\unit\BaseModelTest.php` - Base model tests

### Helpers
- `app\Helpers\ui_helper.php` - Badge and format helpers

### Libraries
- `app\Libraries\Metrics.php` - Business logic (statusStok, selisihMenit, nextSequential)

---

## Testing Checklist

- [ ] Login with admin credentials
- [ ] Dashboard loads without errors
- [ ] Aset CRUD (create, read, update, list)
- [ ] Aset QR code generation
- [ ] Aset mutation
- [ ] LK CRUD
- [ ] LK status changes
- [ ] LK suku cadang (Gudang)
- [ ] LK suku cadang (Kanibal)
- [ ] LK vendor assignment
- [ ] Preventif schedule creation
- [ ] Preventif completion + LKP generation
- [ ] LKP form submission
- [ ] Stok inventory list
- [ ] Stok masuk/keluar transactions
- [ ] Stok riwayat
- [ ] Kanibal alat flow
- [ ] Kanibal riwayat page
- [ ] Vendor management
- [ ] Pengguna management
- [ ] Kategori aset management
- [ ] Kode kerusakan management
- [ ] Laporan period filter
- [ ] Laporan CSV export
- [ ] Laporan print view
