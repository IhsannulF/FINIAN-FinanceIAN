# 💸 FINIAN - Intelligent Personal Finance Tracker

FINIAN adalah aplikasi manajemen keuangan pribadi berbasis web yang dirancang untuk membantu mahasiswa dan pekerja muda mencatat pengeluaran, mengatur anggaran bulanan, dan memahami kebiasaan finansial mereka melalui ringkasan otomatis berteknologi AI.

Proyek ini dibangun dalam waktu **24 Jam** untuk memenuhi kualifikasi tahap Hackathon **IndonesiaNEXT Telkomsel** (Kategori Tema: Literasi Finansial).

---

## 🚀 Latar Belakang & Solusi

Banyak anak muda kesulitan mengelola keuangan karena tidak memiliki sistem pencatatan yang terstruktur. Akibatnya, pengeluaran menjadi tidak terkendali dan mereka tidak memahami pola konsumsi mereka sendiri.

**FINIAN** hadir sebagai solusi ringan (*lightweight*) yang tidak hanya mencatat uang masuk dan keluar, tetapi juga memproses data mentah tersebut menggunakan **LLM API (Generative AI)** untuk memberikan *insight* dan saran keuangan dalam bahasa yang mudah dipahami (tanpa perlu analisis manual).

---

## 🛠 Tech Stack

| Kategori | Teknologi |
|---|---|
| Framework | Laravel 13 |
| Frontend | Blade Template, Tailwind CSS |
| Database | Supabase (PostgreSQL) |
| AI Integration | Google Gemini API (`gemini-3.5-flash`) |
| Icons | Phosphor Icons |

---

## ✨ Fitur Utama (MVP)

- **Autentikasi Aman** — Sistem login, registrasi, dan proteksi *route* untuk menjaga privasi data finansial pengguna.
- **Dashboard Interaktif** — Ringkasan sisa saldo, pemasukan, pengeluaran, dan persentase penggunaan *budget* bulan ini secara *real-time*.
- **Manajemen Transaksi (CRUD)** — Tambah, edit, dan hapus data pemasukan atau pengeluaran harian dengan mudah.
- **Monitoring Pengeluaran** — Pelacakan pengeluaran yang dikelompokkan berdasarkan kategori (Makanan, Transportasi, Belanja, dll).
- **FINIAN AI Insight** *(Fitur Unggulan)* — Mengirimkan agregasi data bulanan (bukan data mentah) ke LLM API untuk menghasilkan 1–2 kalimat saran keuangan yang dipersonalisasi. Fitur ini dirancang dengan sistem *caching* (disimpan di database) untuk menghemat pemanggilan API dan mencegah *latency* pada dashboard.

---

## ⚙️ Panduan Instalasi Lokal

Ikuti langkah-langkah di bawah ini untuk menjalankan FINIAN di mesin lokal Anda.

### 1. Clone Repositori

```bash
git clone https://github.com/username-kamu/finian.git
cd finian
```

### 2. Install Dependensi PHP & Node.js

```bash
composer install
npm install
```

### 3. Konfigurasi Environment

Salin file konfigurasi bawaan dan sesuaikan nilainya:

```bash
cp .env.example .env
```

Buka file `.env` dan pastikan Anda mengisi kredensial database (Supabase) dan kunci API Gemini:

```env
DB_CONNECTION=pgsql
DB_HOST=aws-0-region.pooler.supabase.com
DB_PORT=6543
DB_DATABASE=postgres
DB_USERNAME=postgres.project_id
DB_PASSWORD=your_supabase_password

GEMINI_API_KEY=your_gemini_api_key
```

### 4. Generate Application Key & Migrasi Database

```bash
php artisan key:generate
php artisan migrate:fresh --seed
```

> **Catatan:** Flag `--seed` digunakan untuk mengisi kategori default pada tabel `categories`.

### 5. Jalankan Development Server

Buka dua terminal terpisah dan jalankan perintah berikut:

```bash
# Terminal 1 — Menjalankan server Laravel
php artisan serve
```

```bash
# Terminal 2 — Menjalankan Vite asset bundler
npm run dev
```

### 6. Akses Aplikasi

Buka browser dan kunjungi: [http://localhost:8000](http://localhost:8000)

---
## 🌍 Live Demo

Aplikasi FINIAN dapat diakses secara publik melalui tautan berikut:
**https://finian-financeian-production.up.railway.app/**
