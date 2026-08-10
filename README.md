# 📁 FMS — File Management System

![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Vue](https://img.shields.io/badge/Vue.js-3.5-4FC08D?style=for-the-badge&logo=vue.js&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.4-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-16-4169E1?style=for-the-badge&logo=postgresql&logoColor=white)
![Sanctum](https://img.shields.io/badge/Sanctum-4-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)

> A full-stack **File Management System** built with **Laravel 11** and **Vue 3** for managing corporate documents — supporting **unlimited hierarchical folders**, role-based access control, and a clean, modern responsive UI.

---

## 📖 Deskripsi Project

**FMS (File Management System)** adalah aplikasi manajemen dokumen perusahaan berbasis web yang dibangun dengan arsitektur **Client–Server terpisah**: REST API murni di sisi backend (Laravel 11) dan SPA (Single Page Application) di sisi frontend (Vue 3 + Tailwind CSS).

Aplikasi ini dirancang untuk mengelola dokumen secara terstruktur dengan dua kemampuan utama:

- **📂 Hierarchical Folder tanpa batas level** — Struktur folder parent–child yang mendukung *unlimited nesting*. Setiap folder dapat memiliki sub-folder sedalam apa pun, dengan **Root folder** ditandai `parent_id = NULL`. Navigasi diperkuat oleh **breadcrumb dinamis** yang memudahkan penjelajahan.
- **🔐 Role-Based Access Control (RBAC)** — Dua peran: **Administrator** (kelola folder, file, dan department) dan **Viewer** (melihat, mengunduh, dan mencari). Otorisasi diterapkan end-to-end melalui **Laravel Policies** di backend dan penyembunyian elemen UI (`v-if`) di frontend.

Modul yang tersedia: **Authentication** (Sanctum), **Department Management**, **Folder Management** (hierarchical + breadcrumb), **File Management** (upload drag & drop, preview, download), **Dashboard** (statistik & 10 file terbaru), serta **Search & Filter**.

---

## ✨ Fitur Utama

- **Authentication** — Login & Logout via Laravel Sanctum (token-based), session dipertahankan di sisi frontend.
- **Department Management** — CRUD lengkap (Create, Update, Delete) dengan soft delete & restore.
- **Folder Management** — Create, Rename, Delete; struktur hierarkis tanpa batas level; navigasi breadcrumb.
- **File Management** — Upload, Edit metadata, Delete; pengunduhan aman via endpoint terproteksi; scoped ke folder aktif.
- **File Detail** — Folder, Nama File, Title, Department, Uploaded By, Upload Date + tombol download.
- **Search & Filter** — Pencarian Nama File / Title dan filter Department (diakses Viewer & Admin).
- **Dashboard** — 3 kartu statistik (Total Folder / File / Department) + tabel 10 File Terbaru.
- **RBAC** — Policies Laravel (`viewAny`, `view`, `create`, `update`, `delete`, `download`, `restore`); mutasi data hanya untuk Administrator.

---

## 🎁 Bonus Features Implemented

- [x] **Breadcrumb Folder** — Navigasi breadcrumb dinamis (`Root / Sub Folder / ...`) dengan navigasi klik.
- [x] **Drag & Drop Upload** — Area unggah bergaris putus-putus yang merespons `@dragover`/`@drop`, plus klik-untuk-pilih.
- [x] **Preview PDF/Image** — Pratinjau langsung menggunakan `<img>` (gambar) dan `<iframe>` (PDF) di modal detail file.
- [x] **Soft Delete** — Semua data yang dihapus dapat dilihat (toggle "Tampilkan Data Terhapus") dan **di-restore**; tidak hilang permanen.
- [x] **Responsive UI** — Layout adaptif (sidebar desktop, top-bar mobile; grid & tabel dengan `overflow-x-auto`).
- [x] **Clean Architecture (Service/Repository Pattern)** — Controller bebas dari logika query & storage; seluruh logika bisnis di `Service` dan akses data di `Repository` (terikat via Interface + Dependency Injection).
- [x] **Search & Filter** — Pencarian `file_name`/`title` + filter department, ter-debounce 300ms.

---

## 🛠️ Technology Stack & Requirement

| Layer | Teknologi | Versi Minimal |
|---|---|---|
| Backend | Laravel + Sanctum | **Laravel 11**, PHP **8.2+** |
| Frontend | Vue 3 (Composition API, `<script setup>`) | **Vue 3.5+**, Node.js **22+** |
| Styling | Tailwind CSS | **3.4** |
| State & Routing | Pinia + Vue Router | Pinia 4, Vue Router 5 |
| HTTP Client | Axios | 1.x |
| Database | PostgreSQL | **16** (atau 12+) |

**Requirement:**
- **PHP ≥ 8.2** dengan ekstensi yang diperlukan Laravel 11 (OpenSSL, PDO, Mbstring, Tokenizer, XML, Ctype, JSON, BCMath).
- **Composer** versi terbaru.
- **Node.js ≥ 22** dan **npm ≥ 10**.
- **PostgreSQL** server (mis. 16) yang aktif.
- (Opsional) MySQL/MariaDB/SQLite juga didukung oleh Laravel, namun konfigurasi resmi di bawah menggunakan PostgreSQL.

---

## 📁 Struktur Repository

```
FMS_Lion_air/
├── api/        # Backend — Laravel 11 REST API (folder: "api")
└── client/     # Frontend — Vue 3 SPA + Tailwind (folder: "client")
```

> Struktur pemisahan **backend** dan **frontend** menjadi dua aplikasi independen yang berkomunikasi melalui REST API di `http://localhost:8000/api`.

---

## 🚀 Cara Instalasi & Konfigurasi Environment

### A. Backend (folder `api/`)

```bash
cd api

# 1. Install dependensi PHP
composer install

# 2. Salin file environment
cp .env.example .env

# 3. Generate application key
php artisan key:generate
```

#### Konfigurasi Database PostgreSQL

Buka file `.env` dan sesuaikan blok berikut:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=fms_db
DB_USERNAME=postgres
DB_PASSWORD=password_anda
```

Buat database PostgreSQL-nya terlebih dahulu:

```bash
psql -U postgres -c "CREATE DATABASE fms_db;"
```

#### (Opsional) Konfigurasi CORS

Pastikan `config/cors.php` mengizinkan origin frontend agar request lintas origin berjalan lancar:

```php
'allowed_origins' => ['http://localhost:5173'],
```

### B. Frontend (folder `client/`)

```bash
cd client

# 1. Install dependensi Node
npm install
```

Jika backend berjalan di URL berbeda dari `http://localhost:8000`, sesuaikan `baseURL` pada `client/src/services/api.ts`:

```ts
baseURL: 'http://localhost:8000/api',
```

---

## 🗄️ Menjalankan Migration & Seeder

Dari folder backend (`api/`):

```bash
cd api
php artisan migrate:fresh --seed
```

Perintah di atas akan:
- Membuat seluruh tabel (roles, users, departments, folders, files, personal_access_tokens) beserta foreign key.
- Menjalankan seeder untuk **roles** (`Administrator`, `Viewer`), **default users**, dan data awal **departments**.

> `migrate:fresh` menghapus seluruh tabel lalu membuat ulang. Gunakan `php artisan migrate --seed` bila ingin mempertahankan data yang ada.

---

## ▶️ Cara Menjalankan Project

### 1. Nyalakan Backend (port default 8000)

```bash
cd api
php artisan serve
```

Buat symlink storage agar file (preview gambar/PDF) dapat diakses publik:

```bash
cd api
php artisan storage:link
```

### 2. Nyalakan Frontend (port default 5173)

```bash
cd client
npm run dev
```

Buka aplikasi di browser: **http://localhost:5173**

---

## 🔑 Akun Login

| Role | Email | Password |
|---|---|---|
| **Administrator** | `admin@example.com` | `password` |
| **Viewer** | `viewer@example.com` | `password` |

- **Administrator** memiliki akses penuh: kelola folder, upload/edit/hapus file, kelola department, dashboard, dan mengelola data yang dihapus (restore).
- **Viewer** hanya dapat **melihat** folder, **melihat detail** file, **mengunduh**, dan **mencari/filter** — tanpa kemampuan mengubah data.

---

## 📡 Ringkasan Endpoint API (prefix `/api`)

| Method | Endpoint | Deskripsi |
|---|---|---|
| POST | `/login` | Login (mengembalikan `access_token`) |
| POST | `/logout` | Logout (mencabut token) |
| GET | `/user` | Data user yang sedang login |
| GET | `/dashboard` | Statistik + 10 file terbaru |
| GET/POST | `/departments` | List / buat department |
| GET/PUT/DELETE | `/departments/{id}` | Detail / ubah / hapus department |
| POST | `/departments/{id}/restore` | Restore department yang dihapus |
| GET/POST | `/folders?parent_id=&trashed=` | List sub-folder / buat folder |
| PUT/DELETE | `/folders/{id}` | Rename / hapus folder |
| POST | `/folders/{id}/restore` | Restore folder yang dihapus |
| GET/POST | `/files?folder_id=&search=&department_id=` | List / unggah file |
| GET/PUT/DELETE | `/files/{id}` | Detail / edit / hapus file |
| GET | `/files/{id}/download` | Unduh file (terproteksi) |
| POST | `/files/{id}/restore` | Restore file yang dihapus |

> Semua endpoint kecuali `/login` dilindungi middleware `auth:sanctum`. Mutasi data (create/update/delete/restore) hanya diizinkan untuk Administrator.

---

## 👨‍💻 Author

**Muhammad Arvind Alaric** — Full-Stack Developer

---

## 📄 Lisensi

Proyek ini dibuat sebagai submission technical test dan bersifat edukasional.

---

**Terima kasih telah meninjau proyek ini!** Jika ada pertanyaan, jangan ragu untuk membuka *issue* atau menghubungi author.
