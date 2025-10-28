# 🗄️ Website Gudang Pusat Polban (Sistem Informasi Manajemen Gudang)

Selamat datang di repositori Website Gudang Pusat Polban. Ini adalah aplikasi full-stack yang dibangun menggunakan Laravel & Vue untuk mengelola inventaris, aset, dan alur peminjaman di lingkungan kampus.
Mohon baca dan patuhi panduan ini untuk menjaga alur kerja tim tetap rapi dan efisien.
---
# 🏗️ Struktur Folder

Struktur ini adalah monolith Laravel + Vue, di mana folder resources/js adalah akar dari aplikasi Vue kita.
```
├── app/                # Logic Backend Laravel (Models, Controllers, Services)
├── bootstrap/
├── config/
├── database/           # Migrasi, Seeder, Factory
├── public/             # Aset hasil build (CSS, JS)
├── resources/
│   ├── css/
│   ├── views/          # Halaman Blade (hanya 1 file: app.blade.php)
│   └── js/             # --- Folder Utama VUE.JS ---
│       ├── modules/    # (Disarankan) Fitur dipisah per modul
│       │   ├── auth/
│       │   │   └── views/
│       │   │       └── LoginPage.vue
│       │   ├── master_data/
│       │   │   └── views/
│       │   │       ├── MasterBarang.vue
│       │   │       └── MasterKategori.vue
│       │   └── ... (fitur lainnya: transaksi, opname, dll)
│       │
│       ├── components/     # Komponen global (Button, Modal)
│       ├── layouts/        # Layout utama (DashboardLayout.vue)
│       ├── router/         # router.js (Rute Vue)
│       └── app.js          # Titik masuk utama Vue
│
├── routes/             # Definisi API (api.php) dan Web (web.php)
├── storage/
├── tests/              # Unit & Feature Test
├── vendor/             # Dependensi PHP (Composer)
└── node_modules/       # Dependensi JS (NPM)
```
---
# 🌿 Struktur Branch

## Branch Utama
- `main` → Versi rilis (produksi) stabil. (DILARANG PUSH LANGSUNG)
- `develop` → Gabungan dari semua fitur yang sudah selesai dan siap untuk diuji coba. Ini adalah target PR utama.
## Branch Fitur
Gunakan format: 
```
<tipe>/<nama-fitur-singkat>
```
Contoh:

`fitur/crud-master-barang`

`fitur/alur-pengajuan-user`

`bug/fix-stok-opname`

---
# 🚀 Project Setup (Lokal)

Pastikan Anda memiliki Composer (PHP) dan NPM (Node.js) terinstal.
## 1. Clone Repositori
```
git clone https://github.com/zainandhinf/Proyek-3-Gudang-Pusat-Polban.git
```
```
cd Proyek-3-Gudang-Pusat-Polban
```
## 2. Setup Backend (Laravel)
Salin file .env dan install dependensi PHP:
```
cp .env.example .env
```
```
composer install
```
Generate application key:
```
php artisan key:generate
```
## 3. Konfigurasi Database
Buka file .env dan atur koneksi database (DB_DATABASE, DB_USERNAME, DB_PASSWORD). Setelah itu, jalankan migrasi:
```
php artisan migrate
```
## 4. Setup Frontend (Vue)
Install dependensi JavaScript:
```
npm install
```
---
# 💻 Menjalankan di Mode Development

Anda harus menjalankan 2 server secara bersamaan di 2 terminal terpisah.
- Terminal 1 (Menjalankan Server Backend Laravel)
```
php artisan serve
```
- Terminal 2 (Menjalankan Server Frontend Vue - Vite)
```
npm run dev
```
---
# 📦 Build untuk Production

```
npm run build
```
---
# 🎨 Code Style Check

Frontend (Vue/JS) - Pastikan prettier terinstal (npm install -D prettier)
```
npm run format
```
Backend (Laravel/PHP) - Otomatis ada di Laravel
```
vendor/bin/pint
```
---
# 🔁 Alur Kerja GitHub

## 1. Selalu mulai dari develop
```
git checkout develop
```
```
git pull origin develop
```
## 2. Buat branch fitur baru
```
git checkout -b fitur/crud-master-barang
```
## 3. Kerjakan fitur (Coding...)
## 4. Commit perubahan Anda
```
git add .
```
```
git commit -m "feat: implementasi halaman CRUD master barang"
```
## 5. Push branch Anda ke GitHub
```
git push origin fitur/crud-master-barang
```
## 6. Buat Pull Request (PR)
- Buka repositori di GitHub.
- Buat Pull Request dari fitur/crud-master-barang ke develop.
- Minta 1-2 rekan setim untuk me-review kode Anda.
---
# ✅ Format Commit Message

Gunakan format konvensional: 
```
<tipe>: <deskripsi singkat>
```
Tipe umum:
- feat: fitur baru
- fix: perbaikan bug
- docs: dokumentasi
- style: perubahan visual tanpa logic
- refactor: perbaikan kode internal
- test: pengujian
- chore: pembaruan kecil (CI, deps, dll)
Contoh:
```
feat: tambahkan validasi di form pengajuan
fix: perbaiki perhitungan stok saat barang dikembalikan
refactor: sederhanakan logic di LaporanController
```
---
# 🧼 Tips Tambahan

- Pastikan halaman tetap berfungsi setelah perubahan.
- Jalankan vendor/bin/pint dan npm run format sebelum push.
- Uji fungsionalitas di backend (API) dan frontend (UI).
- Jika ragu, tanyakan ke rekan setim sebelum di-merge.
---
Semangat berkontribusi! 💪
