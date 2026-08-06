# FINIAN — Technical Specification

> Spec ini ditulis supaya bisa mengimplementasikan atau melanjutkan FINIAN **hanya berdasarkan dokumen ini** — tanpa perlu bertanya balik ke pemilik asli. Disusun dari kebutuhan produk (`prd.md`) dan diverifikasi terhadap implementasi nyata di repo per Juli 2026.

---

## 1. Problem Statement

Mahasiswa, karyawan muda, dan freelancer sering tidak mencatat pengeluaran secara terstruktur, sehingga:
- Kehabisan uang sebelum akhir bulan
- Gagal menabung secara konsisten
- Tidak punya visibilitas atas kebiasaan belanja mereka sendiri

FINIAN menyelesaikan ini dengan pencatatan transaksi yang ringan, budgeting sederhana, dan ringkasan kebiasaan belanja berbasis AI dalam bahasa natural — tanpa user perlu menganalisis datanya sendiri.

---

## 2. Goals and Non-Goals

### Goals
- User bisa mencatat income/expense harian dengan cepat
- User bisa memantau pengeluaran bulanan terhadap satu angka budget total
- User mendapat insight AI singkat (2–4 kalimat) tentang kebiasaan belanja bulan berjalan
- Sistem berjalan stabil meski layanan AI eksternal gagal (graceful degradation)

### Non-Goals (di luar scope versi ini)
- Payment reminder / notifikasi
- Transaction tags
- Export ke PDF
- Dark mode
- Auto-alokasi budget per kategori (budget tetap satu angka total)
- Halaman Monthly Report terpisah (digabung ke Dashboard)
- Bank integration, OCR receipt scanner, investment tracking
- Multi-wallet, goal saving (masuk roadmap v1.2)
- AI chat assistant interaktif (baru direncanakan di v2.0 — versi ini hanya insight satu arah)

---

## 3. Functional Requirements

| ID | Requirement |
|---|---|
| FR-01–05 | User bisa register, login, logout; password di-hash; semua route fitur dilindungi middleware `auth` |
| FR-06–07 | Sistem menghitung dan menampilkan current balance dan sisa budget |
| FR-08–11 | User bisa tambah, edit, hapus transaksi income/expense; hanya pemilik transaksi yang boleh edit/hapus |
| FR-12 | 8 kategori default ter-seed otomatis saat instalasi (Food, Transportation, Shopping, Entertainment, Education, Bills, Healthcare, Others) |
| FR-13–14 | User set satu angka total budget per bulan; sistem tampilkan used/remaining/persentase |
| FR-15 | Sistem mengelompokkan pengeluaran per kategori untuk bulan berjalan |
| FR-16 | Sistem mengirim data **agregat** (bukan baris transaksi mentah) ke LLM API |
| FR-17 | Insight AI ditampilkan sebagai card di dashboard |
| FR-18 | Insight di-cache — satu baris per user per bulan, tidak generate ulang otomatis (hanya saat user klik "Buat/Perbarui Analisa") |
| FR-19 | API key LLM disimpan di environment variable, tidak pernah hardcode |

---

## 4. Technical Architecture

**Pola:** Laravel MVC klasik — Controller memanggil Eloquent Model langsung, tidak ada Service/Repository layer terpisah. AI insight generation berjalan **synchronous** di dalam request lifecycle (bukan queued job).

```
Browser
   │
   ▼
routes/web.php  (middleware: auth, verified)
   │
   ▼
Controller (Dashboard / Transaction / Budget)
   │
   ├──► Eloquent Model ──► Supabase PostgreSQL (region Singapore)
   │
   └──► [khusus DashboardController@generateInsight]
              │
              ▼
        Http::withoutVerifying()->timeout(10)
              │
              ▼
        Gemini API (gemini-3.5-flash)
              │
        sukses ──► simpan ke ai_insights (updateOrCreate)
        gagal/timeout ──► redirect dengan session('error'), dashboard tetap render
   │
   ▼
Blade View (+ Tailwind CSS, Alpine.js untuk modal interaktif)
```

**Layer:**
- **Presentation:** Blade templates, komponen Breeze bawaan untuk auth UI, Alpine.js khusus untuk modal transaksi
- **Application:** Controllers menangani validasi, query, dan orkestrasi pemanggilan AI
- **Data:** Eloquent ORM ke PostgreSQL (Supabase)
- **External:** Gemini API — satu-satunya dependency eksternal, dipanggil langsung dari controller

**Keputusan desain penting:**
- HTTPS dipaksa di production lewat `AppServiceProvider::boot()` → `URL::forceScheme('https')`, dibutuhkan karena Railway berada di belakang reverse proxy
- Region Supabase harus **Singapore**, bukan default (Tokyo menyebabkan latency ~300ms lebih tinggi ke user Indonesia)
- Tidak ada job queue untuk AI insight — dianggap acceptable untuk MVP karena dibatasi 1x generate per klik user, dengan timeout 10 detik

---

## 5. File Structure

```
app/
├── Http/Controllers/
│   ├── Auth/                       # Scaffolding Breeze
│   ├── DashboardController.php     # index() + generateInsight()
│   ├── TransactionController.php   # CRUD, ownership check
│   ├── BudgetController.php        # index() + store() (upsert)
│   └── ProfileController.php       # Scaffolding Breeze
├── Models/
│   ├── User.php / Category.php / Transaction.php / Budget.php / AiInsight.php
├── Providers/AppServiceProvider.php
└── View/Components/ (AppLayout, GuestLayout)

database/
├── migrations/  (users, categories, transactions, budgets, ai_insights)
└── seeders/CategorySeeder.php

resources/
├── views/
│   ├── dashboard.blade.php, transactions.blade.php, budget.blade.php, welcome.blade.php  (flat, bukan subfolder)
│   ├── layouts/, partials/navbar.blade.php, components/ (Breeze), auth/, profile/
├── css/app.css
└── js/app.js, js/alpine/transaction-modal.js

routes/web.php, routes/auth.php
.env.example, prd.md, PROJECT_STRUCTURE.md, spec.md, README.md
```

> Tidak ada `app/Services/` — logic aggregasi finansial dan pemanggilan Gemini API menyatu di `DashboardController`. Kalau engineer lanjutan ingin ekstrak ke Service class, itu refactor opsional, bukan prasyarat fungsi.

---

## 6. Data Models

```
users
  id PK, name, email (unique), password (hashed), timestamps

categories
  id PK, name, icon (nullable), color (nullable), timestamps

transactions
  id PK
  user_id       FK → users.id      (cascade delete)
  category_id   FK → categories.id (cascade delete)
  type          enum('income','expense')
  amount        decimal(15,2)
  description   string, nullable
  transaction_date  date
  timestamps

budgets
  id PK
  user_id  FK → users.id (cascade delete)
  month    unsignedTinyInteger
  year     unsignedSmallInteger
  total_budget  decimal(15,2)
  timestamps
  UNIQUE(user_id, month, year)

ai_insights
  id PK
  user_id  FK → users.id (cascade delete)
  month    unsignedTinyInteger
  year     unsignedSmallInteger
  content  text
  timestamps
  UNIQUE(user_id, month, year)
```

**Relasi Eloquent:**
`User hasMany Transaction, Budget, AiInsight` · `Category hasMany Transaction` · `Transaction belongsTo User, Category`

**Catatan risiko:** `category_id` pakai `onDelete('cascade')`. Karena kategori saat ini hanya di-seed (tidak ada UI hapus kategori untuk user), risiko ini rendah — tapi kalau nanti ditambah fitur kelola kategori, cascade delete akan menghapus seluruh riwayat transaksi terkait tanpa konfirmasi. Sebaiknya diganti `onDelete('restrict')` atau `set null` sebelum fitur itu dibuat.

---

## 7. API / Endpoints

Semua route di bawah adalah web routes (bukan REST API JSON) — response berupa redirect + Blade view, bukan JSON.

| Method | Route | Auth? | Deskripsi |
|---|---|---|---|
| GET | `/` | tidak | Landing page |
| GET | `/dashboard` | ya | Ringkasan balance, income, expense, budget, AI insight |
| POST | `/dashboard/generate-insight` | ya | Trigger generate/refresh AI insight bulan berjalan |
| GET | `/transactions` | ya | List transaksi (paginated, 5/halaman), filter `?type=income\|expense` |
| POST | `/transactions` | ya | Tambah transaksi baru |
| PUT | `/transactions/{transaction}` | ya | Update transaksi (403 jika bukan milik user) |
| DELETE | `/transactions/{transaction}` | ya | Hapus transaksi (403 jika bukan milik user) |
| GET | `/budget` | ya | Lihat status budget bulan berjalan |
| POST | `/budget` | ya | Set/update budget bulan berjalan (upsert) |
| GET/PATCH/DELETE | `/profile` | ya | Scaffolding Breeze — kelola profil |

---

## 8. Business Logic

**Dashboard summary (bulan berjalan, difilter by `now()->month/year`):**
- `income` = SUM(amount) WHERE type='income'
- `expense` = SUM(amount) WHERE type='expense'
- `balance` = income − expense
- `budgetRemaining` = total_budget − expense (total_budget = 0 jika belum di-set)
- `budgetPercentage` = min(100, round(expense/total_budget × 100)); 0 jika total_budget = 0
- `expensesByCategory` = transaksi expense di-group by category_id, urut total_amount descending

**Budget (satu angka per user per bulan):**
- Disimpan via `updateOrCreate(['user_id','month','year'], ['total_budget'=>...])` — submit berkali-kali di bulan yang sama akan **menimpa** nilai lama, bukan menambah baris baru.

**AI Insight generation (alur lengkap):**
1. Ambil semua transaksi bulan berjalan milik user
2. Hitung total income, total expense, dan kategori pengeluaran terbesar (group by nama kategori, ambil kunci dengan total tertinggi; fallback `'Belum ada'` jika tidak ada expense)
3. Susun prompt teks (instruksi: 1–2 kalimat, maks. 30 kata, gaya santai-profesional, tanpa poin-poin), sisipkan angka income/expense/budget/topCategory
4. Kirim ke Gemini API (`gemini-3.5-flash`) dengan timeout 10 detik
5. Sukses → simpan/replace via `updateOrCreate(['user_id','month','year'], ['content'=>...])`, redirect dengan flash `success`
6. Gagal (exception atau response non-2xx) → log peringatan, redirect dengan flash `error`, dashboard tetap render dengan pesan fallback

**Prinsip keamanan data:** hanya angka agregat yang dikirim ke LLM API — tidak pernah baris transaksi individual.

---

## 9. Edge Cases

| # | Kasus | Perilaku yang Diharapkan |
|---|---|---|
| 1 | User belum set budget bulan ini | `totalBudget=0`, `budgetPercentage=0`; `budgetRemaining` jadi negatif (= -expense) — UI sebaiknya tampilkan "Belum ada budget" alih-alih angka minus mentah |
| 2 | Belum ada transaksi bulan ini | income/expense/balance = 0, `expensesByCategory` kosong, dashboard tetap render tanpa error |
| 3 | Gemini API timeout/gagal | Fallback message tampil, dashboard tetap normal (lihat section 8 & patch resilience) |
| 4 | Submit budget dua kali di bulan sama | Nilai lama tertimpa (expected — bukan bug) |
| 5 | User A coba edit/hapus transaksi milik User B | 403 Forbidden via `abort_unless($transaction->user_id === Auth::id())` |
| 6 | Amount transaksi sangat besar (>13 digit) | Melebihi kapasitas `decimal(15,2)` — perlu validasi `max:` eksplisit di request (belum ada saat ini) |
| 7 | `transaction_date` di masa depan | Tidak ada validasi penolakan saat ini — bebas diinput; perlu diputuskan apakah ini valid use case (mis. pencatatan terjadwal) atau harus ditolak |
| 8 | Kategori dihapus (kalau fitur ini dibuat nanti) | Cascade delete akan menghapus semua transaksi terkait — lihat catatan risiko di section 6 |
| 9 | `topCategory` kosong (belum ada expense) | Fallback string `'Belum ada'`, tetap dikirim ke prompt AI |
| 10 | Akses `?page=999` di daftar transaksi | Laravel pagination mengembalikan collection kosong secara graceful, tidak error |
| 11 | Dua request submit budget/generate-insight bersamaan (race condition) | `updateOrCreate` + unique constraint DB meminimalisir duplikasi, tapi race murni di level aplikasi (dua request lolos cek sebelum insert) secara teori masih mungkin — risiko rendah untuk skala trafik MVP |

---

## 10. Validation Rules

**Transaction (`store`, `update`):**
```
type              required, in:income,expense
category_id       required, exists:categories,id
amount            required, numeric, min:1
description       nullable, string, max:255
transaction_date  required, date
```

**Budget (`store`):**
```
budget_amount     required, numeric, min:0
```

**Auth (Breeze default):** email required + format email + unique (register); password required + confirmed + aturan default Laravel (min 8 karakter).

**Belum divalidasi (rekomendasi tambahan):** batas maksimum `amount` (selaraskan dengan kapasitas `decimal(15,2)`), dan kebijakan eksplisit soal `transaction_date` di masa depan (lihat edge case #7).

---

## 11. Error Handling

| Situasi | Penanganan Saat Ini |
|---|---|
| Validasi gagal (form transaksi/budget) | Laravel default: redirect back dengan `$errors` bag, ditampilkan di Blade via `@error` |
| Transaksi bukan milik user | `abort_unless(..., 403)` → halaman 403 default Laravel |
| Transaksi tidak ditemukan | Route model binding otomatis lempar 404 |
| Gemini API gagal (non-2xx) | Log warning via `Log::warning()`, redirect dengan `session('error')`, dashboard render normal (patched) |
| Gemini API timeout/connection error | `try/catch ConnectionException`, redirect dengan `session('error')` (patched) |
| Exception tak terduga lainnya | Laravel default exception handler — pastikan `APP_DEBUG=false` di production (Railway env var) supaya stack trace tidak bocor ke publik |

---

## 12. Testing Strategy

**Kondisi saat ini:** folder `tests/` hanya berisi scaffolding default Breeze (`AuthenticationTest`, `RegistrationTest`, `PasswordResetTest`, `ProfileTest`, `ExampleTest`) — **belum ada test untuk fitur inti** (Transaction CRUD, Budget, AI Insight).

**Rencana minimal yang direkomendasikan untuk engineer lanjutan:**

| Test | Jenis | Prioritas |
|---|---|---|
| Transaction CRUD (store/update/destroy) sebagai pemilik | Feature | Tinggi |
| Transaction update/destroy oleh bukan pemilik → 403 | Feature | Tinggi |
| Budget upsert — submit dua kali menimpa nilai lama | Feature | Sedang |
| Dashboard summary calculation (balance, budgetPercentage) dengan data seed | Unit/Feature | Tinggi |
| AI insight generation — mock Gemini API response sukses | Feature (dengan `Http::fake()`) | Tinggi |
| AI insight generation — mock Gemini API gagal/timeout → fallback message muncul, bukan crash | Feature (dengan `Http::fake()`) | **Tinggi** (langsung menguji fix resilience yang baru dipatch) |
| Category seeding menghasilkan 8 kategori | Unit | Rendah |

**Tools:** PHPUnit (sudah tersedia via `phpunit.xml`), `Illuminate\Support\Facades\Http::fake()` untuk mock Gemini API tanpa panggilan network sungguhan saat testing.

---

## 13. Acceptance Criteria

Sistem dianggap selesai dan sesuai spec jika:

- [ ] User bisa register, login, logout dengan aman
- [ ] User bisa tambah/edit/hapus transaksi income & expense, hanya untuk transaksi miliknya sendiri
- [ ] Dashboard menampilkan balance, income, expense, dan status budget bulan berjalan secara akurat dan real-time
- [ ] User bisa set satu budget total per bulan, dan submit berulang menimpa nilai lama (bukan duplikat)
- [ ] Expense breakdown per kategori tampil terurut dari terbesar
- [ ] Klik "Buat/Perbarui Analisa" menghasilkan insight AI 1–2 kalimat berbahasa Indonesia dari Gemini API, berdasarkan data agregat (bukan raw transaction)
- [ ] Insight ter-cache — hanya berubah saat user klik generate ulang, tidak auto-regenerate setiap load dashboard
- [ ] Jika Gemini API gagal/timeout, dashboard tetap render normal dengan pesan fallback — **tidak ada** `dd()` atau crash
- [ ] Tidak ada API key atau secret lain yang hardcode di kode — semua lewat environment variable
- [ ] Aplikasi ter-deploy dan bisa diakses publik lewat HTTPS

---

## 14. Step-by-Step Implementation Plan

1. **Setup project & database**
   Install Laravel 13 + Breeze, buat project Supabase (region **Singapore**), hubungkan `.env`, jalankan migrasi awal.

2. **Migrations & seeder**
   Buat migrasi `categories`, `transactions`, `budgets`, `ai_insights` sesuai skema section 6. Buat `CategorySeeder` untuk 8 kategori default.

3. **Models & relasi**
   Buat `Category`, `Transaction`, `Budget`, `AiInsight` dengan relasi Eloquent sesuai section 6.

4. **Auth**
   Gunakan scaffolding Breeze bawaan (login/register/logout/password reset) — tidak perlu dibangun dari nol.

5. **Transaction CRUD**
   `TransactionController` — implementasikan `index` (dengan pagination + filter type), `store`, `update`, `destroy` sesuai validasi (section 10) dan ownership check (section 9 edge case #5).

6. **Budget**
   `BudgetController` — `index` untuk lihat status, `store` dengan `updateOrCreate` (upsert) sesuai section 8.

7. **Dashboard summary**
   `DashboardController@index` — hitung balance, budget remaining/percentage, expense by category sesuai formula di section 8.

8. **AI Insight**
   `DashboardController@generateInsight` — implementasikan alur lengkap di section 8, termasuk:
   - Timeout eksplisit pada HTTP client
   - try/catch untuk connection error
   - Fallback message via session flash saat gagal (**bukan** `dd()` atau exception yang tidak tertangani)
   - Caching via `updateOrCreate`

9. **Views**
   Bangun `dashboard.blade.php`, `transactions.blade.php`, `budget.blade.php` dengan Tailwind CSS. Pastikan ada block untuk menampilkan `session('error')` dan `session('success')`.

10. **Deployment**
    Deploy ke Railway. Set `URL::forceScheme('https')` di production (`AppServiceProvider`). Set semua environment variable (DB, Gemini API key) lewat Railway dashboard, bukan file `.env` yang di-commit.

11. **Testing** *(belum dilakukan di versi MVP ini — lihat section 12 untuk rencana)*
    Tambahkan test untuk Transaction CRUD, ownership check, budget upsert, dan AI insight (sukses & gagal) sebelum melanjutkan ke fitur v1.1.

12. **Dokumentasi**
    Pastikan `README.md`, `prd.md`, `PROJECT_STRUCTURE.md`, dan `spec.md` ini konsisten satu sama lain dan mencerminkan kode yang sebenarnya (bukan rencana yang belum diimplementasikan).
