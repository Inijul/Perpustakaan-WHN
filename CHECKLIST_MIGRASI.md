# ✅ CHECKLIST MIGRASI KE FULL LARAVEL MVC

Panduan langkah demi langkah untuk migrasi dari Backend Node.js + Frontend Laravel menjadi Full Laravel MVC.

---

## 📋 PERSIAPAN

- [ ] **Backup Database**
  ```bash
  mysqldump -u root -p perpustakaan_whn > backup_database_$(date +%Y%m%d).sql
  ```

- [ ] **Backup Project**
  ```bash
  # Buat backup folder project
  cp -r "Perpustakaan - WHN" "Perpustakaan - WHN - BACKUP"
  ```

- [ ] **Commit semua perubahan ke Git**
  ```bash
  git add .
  git commit -m "Before migration to full Laravel MVC"
  ```

---

## 🔧 LANGKAH 1: KONFIGURASI DATABASE LARAVEL

- [ ] **Copy file .env Laravel**
  ```bash
  cd frontend
  cp .env.example .env  # Jika belum ada
  ```

- [ ] **Update file `.env` di folder `frontend/`**
  - [ ] Set `DB_CONNECTION=mysql`
  - [ ] Set `DB_HOST=127.0.0.1` (atau sesuai host MySQL Anda)
  - [ ] Set `DB_PORT=3306`
  - [ ] Set `DB_DATABASE=perpustakaan_whn` (sesuaikan dengan database Anda)
  - [ ] Set `DB_USERNAME=root` (sesuaikan)
  - [ ] Set `DB_PASSWORD=your_password` (sesuaikan)
  
  **Lihat file `CONTOH_ENV_LARAVEL.txt` untuk referensi lengkap**

- [ ] **Generate APP_KEY Laravel**
  ```bash
  cd frontend
  php artisan key:generate
  ```

- [ ] **Test koneksi database**
  ```bash
  php artisan tinker
  ```
  Lalu ketik:
  ```php
  DB::connection()->getPdo();
  exit;
  ```
  Jika sukses, akan muncul objek PDO tanpa error.

---

## 🔧 LANGKAH 2: UPDATE MODELS LARAVEL

- [ ] **Update Model Aktivitas**
  - [ ] Copy file `frontend/app/Models/Aktivitas_UPDATED.php`
  - [ ] Rename menjadi `frontend/app/Models/Aktivitas.php` (replace yang lama)
  - [ ] Pastikan ada `public $timestamps = false;`

- [ ] **Update Model Mahasiswa**
  - [ ] Copy file `frontend/app/Models/Mahasiswa_UPDATED.php`
  - [ ] Rename menjadi `frontend/app/Models/Mahasiswa.php` (replace yang lama)
  - [ ] Pastikan ada `public $timestamps = false;`

- [ ] **Update Model Koleksi**
  - [ ] Copy file `frontend/app/Models/Koleksi_UPDATED.php`
  - [ ] Rename menjadi `frontend/app/Models/Koleksi.php` (replace yang lama)
  - [ ] Pastikan ada `public $timestamps = false;`

- [ ] **Test Models dengan Tinker**
  ```bash
  php artisan tinker
  ```
  ```php
  \App\Models\Mahasiswa::count();
  \App\Models\Koleksi::count();
  \App\Models\Aktivitas::count();
  exit;
  ```

---

## 🔧 LANGKAH 3: UPDATE CONTROLLERS LARAVEL

- [ ] **Backup Controller yang lama**
  ```bash
  cd frontend/app/Http/Controllers
  cp AktivitasController.php AktivitasController.OLD.php
  cp KoleksiController.php KoleksiController.OLD.php
  ```

- [ ] **Update AktivitasController**
  - [ ] Copy file `frontend/app/Http/Controllers/AktivitasController_NEW_LARAVEL_ONLY.php`
  - [ ] Rename menjadi `frontend/app/Http/Controllers/AktivitasController.php` (replace yang lama)
  - [ ] Cek tidak ada error syntax

- [ ] **Update KoleksiController**
  - [ ] Copy file `frontend/app/Http/Controllers/KoleksiController_NEW_LARAVEL_ONLY.php`
  - [ ] Rename menjadi `frontend/app/Http/Controllers/KoleksiController.php` (replace yang lama)
  - [ ] Cek tidak ada error syntax

- [ ] **Buat MahasiswaController (jika diperlukan)**
  - [ ] Copy file `frontend/app/Http/Controllers/MahasiswaController_NEW_LARAVEL_ONLY.php`
  - [ ] Rename menjadi `frontend/app/Http/Controllers/MahasiswaController.php`

---

## 🔧 LANGKAH 4: UPDATE ROUTES

- [ ] **Cek file `frontend/routes/web.php`**
  - [ ] Pastikan semua route sudah mengarah ke Controller Laravel
  - [ ] Tidak ada route yang memanggil backend Node.js

- [ ] **Hapus route API ke backend (jika ada)**
  - [ ] Cek file `frontend/routes/api.php`
  - [ ] Hapus route yang mengarah ke `http://backend:5000`

---

## 🔧 LANGKAH 5: TESTING APLIKASI LARAVEL

- [ ] **Clear cache Laravel**
  ```bash
  cd frontend
  php artisan cache:clear
  php artisan config:clear
  php artisan route:clear
  php artisan view:clear
  ```

- [ ] **Jalankan Laravel Server**
  ```bash
  php artisan serve
  ```
  Atau:
  ```bash
  php artisan serve --host=0.0.0.0 --port=8000
  ```

- [ ] **Test di Browser**
  - [ ] Buka `http://localhost:8000`
  - [ ] Test halaman Koleksi - tampil data
  - [ ] Test halaman Aktivitas - tampil data

- [ ] **Test CRUD Koleksi**
  - [ ] Test Tambah Koleksi
  - [ ] Test Edit Koleksi
  - [ ] Test Hapus Koleksi
  - [ ] Test Lihat Detail Koleksi

- [ ] **Test CRUD Aktivitas**
  - [ ] Test Tambah Aktivitas (Peminjaman)
  - [ ] Test Update Status (Dikembalikan)
  - [ ] Test Lihat Detail Aktivitas

- [ ] **Cek Log Laravel**
  ```bash
  tail -f frontend/storage/logs/laravel.log
  ```
  Pastikan tidak ada error.

---

## 🗑️ LANGKAH 6: HAPUS BACKEND NODE.JS (SETELAH YAKIN LARAVEL BERJALAN)

⚠️ **PENTING: Lakukan step ini HANYA setelah yakin Laravel berfungsi dengan baik!**

- [ ] **Stop Backend Node.js (jika sedang running)**
  ```bash
  # Jika menggunakan Docker
  docker-compose down
  
  # Atau jika manual
  # Cari proses Node.js dan stop
  ```

- [ ] **Hapus file/folder backend Node.js**
  - [ ] Hapus folder `src/`
  - [ ] Hapus folder `dist/`
  - [ ] Hapus file `tsconfig.json` (di root)
  - [ ] Hapus file `package.json` (di root, bukan yang di frontend!)
  - [ ] Hapus file `package-lock.json` (di root)
  - [ ] Hapus folder `node_modules/` (di root)

- [ ] **Hapus/Arsipkan file SQL yang tidak diperlukan**
  - [ ] Pindahkan ke folder `archive/` atau hapus file:
    - `add_status_field_if_missing.sql`
    - `check_status_field.sql`
    - `debug_status.sql`
    - `fix_aktivitas_status_field.sql`
    - `fix_status_aktivitas.sql`

- [ ] **Hapus/Arsipkan file dokumentasi lama**
  - [ ] Pindahkan ke folder `archive/` file-file:
    - `PERBAIKAN_*.md`
    - `SOLUSI_*.md`
    - `PERUBAHAN_*.md`
    - `INTEGRASI_DASHBOARD.md`

---

## 🐳 LANGKAH 7: UPDATE DOCKER COMPOSE (Jika Menggunakan Docker)

- [ ] **Backup `docker-compose.yml`**
  ```bash
  cp docker-compose.yml docker-compose.yml.OLD
  ```

- [ ] **Edit `docker-compose.yml`**
  - [ ] Hapus service `backend`
  - [ ] Pastikan service `frontend` langsung connect ke `mysql`
  - [ ] Update environment variables di service `frontend`

- [ ] **Contoh docker-compose.yml yang baru:**
  ```yaml
  services:
    frontend:
      build: ./frontend
      ports:
        - "8000:80"
      depends_on:
        - mysql
      environment:
        - DB_HOST=mysql
        - DB_PORT=3306
        - DB_DATABASE=perpustakaan
        - DB_USERNAME=root
        - DB_PASSWORD=password
    
    mysql:
      image: mysql:8.0
      ports:
        - "3306:3306"
      environment:
        MYSQL_ROOT_PASSWORD: password
        MYSQL_DATABASE: perpustakaan
      volumes:
        - mysql_data:/var/lib/mysql

  volumes:
    mysql_data:
  ```

- [ ] **Test Docker Compose**
  ```bash
  docker-compose up -d
  docker-compose logs -f frontend
  ```

---

## 📝 LANGKAH 8: UPDATE DOKUMENTASI

- [ ] **Update README.md**
  - [ ] Hapus instruksi tentang backend Node.js
  - [ ] Update dengan instruksi Laravel saja
  - [ ] Update cara setup dan run aplikasi

- [ ] **Buat dokumentasi baru**
  - [ ] Cara instalasi
  - [ ] Cara konfigurasi database
  - [ ] Cara menjalankan aplikasi
  - [ ] Struktur project Laravel

---

## ✅ LANGKAH 9: FINAL CHECK

- [ ] **Test keseluruhan aplikasi**
  - [ ] Login/Logout berfungsi
  - [ ] CRUD Koleksi berfungsi
  - [ ] CRUD Aktivitas berfungsi
  - [ ] Status koleksi update otomatis
  - [ ] Filter dan search berfungsi

- [ ] **Test performance**
  - [ ] Halaman loading lebih cepat (tidak ada HTTP ke backend)
  - [ ] Tidak ada timeout
  - [ ] Tidak ada error di console browser

- [ ] **Commit ke Git**
  ```bash
  git add .
  git commit -m "Migrasi selesai: Full Laravel MVC tanpa backend Node.js"
  git tag -a v2.0.0 -m "Version 2.0.0 - Full Laravel MVC"
  ```

---

## 🎉 SELESAI!

Aplikasi Anda sekarang sudah menggunakan **Full Laravel MVC** tanpa backend Node.js!

**Keuntungan yang didapat:**
- ✅ Lebih sederhana - hanya 1 aplikasi
- ✅ Lebih cepat - tidak ada HTTP overhead
- ✅ Lebih mudah maintenance
- ✅ Bisa pakai fitur Laravel secara penuh
- ✅ Tidak perlu manage 2 server berbeda

---

## 🆘 TROUBLESHOOTING

### Jika ada error "Class not found"
```bash
composer dump-autoload
```

### Jika ada error "Connection refused"
- Cek konfigurasi database di `.env`
- Pastikan MySQL service running
- Test koneksi dengan `php artisan tinker`

### Jika data tidak muncul
- Cek apakah Models sudah benar
- Cek apakah ada data di database dengan `php artisan tinker`
- Cek log di `storage/logs/laravel.log`

### Jika halaman blank/white screen
- Cek log error di `storage/logs/laravel.log`
- Aktifkan debug mode: `APP_DEBUG=true` di `.env`
- Clear cache: `php artisan cache:clear`

---

## 📞 BANTUAN

Jika ada masalah, cek:
1. File `PANDUAN_MIGRASI_FULL_LARAVEL.md` untuk penjelasan detail
2. Log Laravel di `storage/logs/laravel.log`
3. Error di browser console (F12)

Semoga sukses! 🚀

