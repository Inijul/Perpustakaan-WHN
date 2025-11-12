# 📦 Panduan Lengkap Backup Database & Project

## ⚠️ PENTING: Lakukan Backup SEBELUM Migrasi!

Backup adalah **safety net** Anda. Jika ada yang salah saat migrasi, Anda bisa restore dengan mudah.

---

## 🗃️ BAGIAN 1: BACKUP DATABASE

### **Metode A: Menggunakan mysqldump (Recommended)**

#### **Windows - PowerShell/CMD:**

```powershell
# 1. Buka PowerShell atau Command Prompt
# 2. Pindah ke folder project
cd "D:\Aplikasi\VSCode\Perpustakaan - WHN"

# 3. Buat folder backup (jika belum ada)
mkdir backup

# 4. Backup database
mysqldump -u root -p perpustakaan_whn > backup\backup_database_$(Get-Date -Format 'yyyyMMdd_HHmmss').sql

# Atau versi sederhana:
mysqldump -u root -p perpustakaan_whn > backup\backup_database.sql
```

**Catatan:**
- Ganti `root` dengan username MySQL Anda
- Ganti `perpustakaan_whn` dengan nama database Anda
- Setelah enter, akan minta password MySQL

#### **Jika mysqldump tidak dikenali:**

```powershell
# Cari lokasi MySQL
# Biasanya di: C:\Program Files\MySQL\MySQL Server X.X\bin\

# Gunakan full path:
"C:\Program Files\MySQL\MySQL Server 8.0\bin\mysqldump.exe" -u root -p perpustakaan_whn > backup\backup_database.sql

# Atau tambahkan ke PATH dulu:
$env:Path += ";C:\Program Files\MySQL\MySQL Server 8.0\bin"
mysqldump -u root -p perpustakaan_whn > backup\backup_database.sql
```

---

### **Metode B: Menggunakan phpMyAdmin (GUI)**

#### **Langkah-langkah:**

1. **Buka phpMyAdmin**
   - Buka browser
   - Akses: `http://localhost/phpmyadmin`
   - Atau: `http://localhost:8080` (jika pakai Docker)

2. **Login**
   - Username: `root` (atau sesuai konfigurasi Anda)
   - Password: (password MySQL Anda)

3. **Pilih Database**
   - Klik nama database di panel kiri (misal: `perpustakaan_whn`)

4. **Export Database**
   - Klik tab **"Export"** di menu atas
   - Method: Pilih **"Quick"** (untuk cepat) atau **"Custom"** (untuk detail)
   - Format: **"SQL"**
   - Klik tombol **"Go"**

5. **Save File**
   - File `.sql` akan otomatis terdownload
   - Rename menjadi: `backup_perpustakaan_20250111.sql`
   - Pindahkan ke folder `backup/` di project Anda

---

### **Metode C: Menggunakan MySQL Workbench**

1. **Buka MySQL Workbench**
2. **Connect ke Database** (klik connection Anda)
3. **Menu Server → Data Export**
4. **Pilih Database:**
   - Check database yang mau di-backup
5. **Export Options:**
   - Export to Self-Contained File: Browse → Pilih lokasi save
   - Atau: Export to Dump Project Folder
6. **Include:**
   - ✅ Dump Structure and Data
   - ✅ Include Create Schema
7. **Klik "Start Export"**
8. **Tunggu sampai selesai**

---

### **Metode D: Menggunakan Docker (Jika database di Docker)**

```powershell
# Cek nama container database
docker ps

# Backup database dari container
docker exec [nama_container_mysql] mysqldump -u root -p[password] perpustakaan_whn > backup\backup_database.sql

# Contoh (ganti dengan nama container Anda):
docker exec perpustakaan_mysql mysqldump -u root -ppassword perpustakaan_whn > backup\backup_database.sql

# Atau masuk ke container dulu:
docker exec -it perpustakaan_mysql bash
mysqldump -u root -p perpustakaan_whn > /tmp/backup.sql
exit

# Copy dari container ke host:
docker cp perpustakaan_mysql:/tmp/backup.sql ./backup/backup_database.sql
```

---

## 💾 BAGIAN 2: BACKUP PROJECT/KODE

### **Metode A: Git Commit (Paling Recommended)**

#### **Menggunakan Git:**

```powershell
# 1. Pindah ke folder project
cd "D:\Aplikasi\VSCode\Perpustakaan - WHN"

# 2. Cek status Git
git status

# 3. Lihat apa saja yang berubah
git diff

# 4. Tambahkan semua perubahan
git add .

# 5. Commit dengan message yang jelas
git commit -m "Backup sebelum migrasi ke Full Laravel MVC - $(Get-Date -Format 'yyyy-MM-dd HH:mm')"

# 6. (Optional) Push ke remote repository
git push origin master

# 7. (Optional) Buat tag untuk checkpoint ini
git tag -a v1.0-before-migration -m "Backup sebelum migrasi ke Full Laravel"
git push origin v1.0-before-migration
```

#### **Jika belum pakai Git:**

```powershell
# Initialize Git repository
cd "D:\Aplikasi\VSCode\Perpustakaan - WHN"
git init

# Tambahkan .gitignore (agar tidak backup file yang tidak perlu)
echo "node_modules/" > .gitignore
echo "vendor/" >> .gitignore
echo ".env" >> .gitignore
echo "*.log" >> .gitignore

# Commit pertama
git add .
git commit -m "Initial commit - Backup sebelum migrasi"
```

**Keuntungan Git:**
- ✅ Bisa rollback kapan saja
- ✅ Bisa compare perubahan
- ✅ History lengkap
- ✅ Tidak memakan banyak space

---

### **Metode B: Copy Folder Manual**

#### **Cara 1: PowerShell (Cepat & Exclude folder besar)**

```powershell
# Pindah ke parent folder
cd "D:\Aplikasi\VSCode\"

# Copy dengan robocopy (exclude folder besar)
robocopy "Perpustakaan - WHN" "Perpustakaan - WHN - BACKUP_20250111" /E /XD node_modules vendor .git

# Atau pakai xcopy:
xcopy "Perpustakaan - WHN" "Perpustakaan - WHN - BACKUP_20250111" /E /I /H /EXCLUDE:exclude_list.txt
```

**Buat file `exclude_list.txt`:**
```
\node_modules\
\vendor\
\.git\
\storage\logs\
\bootstrap\cache\
```

#### **Cara 2: Windows Explorer (Manual)**

1. Buka **File Explorer**
2. Navigate ke: `D:\Aplikasi\VSCode\`
3. **Klik kanan** folder `Perpustakaan - WHN`
4. Pilih **"Copy"** (Ctrl+C)
5. **Klik kanan** di area kosong → **"Paste"** (Ctrl+V)
6. **Rename** folder baru menjadi: `Perpustakaan - WHN - BACKUP_20250111`

**Tips:**
- Proses copy bisa lama jika ada folder `node_modules` dan `vendor`
- Bisa hapus/skip folder tersebut saat copy (tidak mempengaruhi backup kode)

---

### **Metode C: Compress ke ZIP File**

#### **PowerShell:**

```powershell
# Buat folder backup dulu
mkdir "D:\Backup\Perpustakaan" -Force

# Compress ke ZIP
Compress-Archive -Path "D:\Aplikasi\VSCode\Perpustakaan - WHN\*" -DestinationPath "D:\Backup\Perpustakaan\backup_project_$(Get-Date -Format 'yyyyMMdd_HHmmss').zip" -Force

# Atau versi yang exclude folder besar:
# Buat temporary folder untuk staging
$tempFolder = "D:\Temp\Perpustakaan_Backup"
robocopy "D:\Aplikasi\VSCode\Perpustakaan - WHN" $tempFolder /E /XD node_modules vendor .git
Compress-Archive -Path "$tempFolder\*" -DestinationPath "D:\Backup\Perpustakaan\backup_project.zip" -Force
Remove-Item $tempFolder -Recurse -Force
```

#### **Windows Explorer (Manual):**

1. Klik kanan folder `Perpustakaan - WHN`
2. Pilih **"Send to"** → **"Compressed (zipped) folder"**
3. Rename ZIP file: `backup_perpustakaan_20250111.zip`
4. Pindahkan ke folder backup yang aman

**Keuntungan ZIP:**
- ✅ Menghemat space (compressed)
- ✅ Mudah dipindahkan/di-share
- ✅ Single file untuk restore

---

## 🔄 CARA RESTORE (Jika Migrasi Gagal)

### **Restore Database:**

#### **Metode 1: mysql command line**

```powershell
# Restore dari backup
mysql -u root -p perpustakaan_whn < backup\backup_database.sql

# Atau jika ingin buat database baru dulu:
mysql -u root -p -e "DROP DATABASE IF EXISTS perpustakaan_whn; CREATE DATABASE perpustakaan_whn;"
mysql -u root -p perpustakaan_whn < backup\backup_database.sql
```

#### **Metode 2: phpMyAdmin**

1. Buka phpMyAdmin
2. Pilih database
3. Klik tab **"Import"**
4. Klik **"Choose File"** → pilih file backup `.sql`
5. Klik **"Go"**
6. Tunggu sampai selesai

---

### **Restore Project:**

#### **Jika pakai Git:**

```powershell
# Lihat list commits
git log --oneline

# Rollback ke commit sebelumnya
git reset --hard [commit_hash]

# Atau checkout ke tag backup:
git checkout v1.0-before-migration
```

#### **Jika pakai Copy Folder:**

```powershell
# Hapus folder yang gagal migrasi
Remove-Item "D:\Aplikasi\VSCode\Perpustakaan - WHN" -Recurse -Force

# Copy dari backup
xcopy "D:\Aplikasi\VSCode\Perpustakaan - WHN - BACKUP_20250111" "D:\Aplikasi\VSCode\Perpustakaan - WHN" /E /I /H
```

#### **Jika pakai ZIP:**

1. Extract file ZIP backup
2. Hapus folder project yang gagal
3. Rename extracted folder sesuai nama asli

---

## ✅ CHECKLIST BACKUP LENGKAP

Sebelum migrasi, pastikan sudah backup:

### **Database:**
- [ ] Backup database dengan mysqldump ATAU
- [ ] Export database dari phpMyAdmin ATAU
- [ ] Backup database dari MySQL Workbench
- [ ] Verifikasi file backup `.sql` sudah ada dan tidak corrupt (coba buka dengan text editor)
- [ ] Catat lokasi file backup

### **Project/Kode:**
- [ ] Git commit semua perubahan ATAU
- [ ] Copy folder project ke backup ATAU
- [ ] Compress project ke ZIP
- [ ] Verifikasi backup sudah lengkap
- [ ] Catat lokasi backup

### **File Penting Lainnya:**
- [ ] File `.env` (konfigurasi)
- [ ] Folder `public/sampul/` (jika ada upload file)
- [ ] File dokumentasi custom

---

## 📝 LOKASI BACKUP YANG DISARANKAN

**Good Practice:**

```
D:\Backup\Perpustakaan\
├── database\
│   ├── backup_20250111_140523.sql
│   └── backup_20250111_153012.sql (setelah test)
├── project\
│   ├── perpustakaan_backup_20250111.zip
│   └── atau: Perpustakaan - WHN - BACKUP_20250111\ (folder copy)
└── README_BACKUP.txt (catatan tanggal & versi)
```

**Contoh README_BACKUP.txt:**

```
BACKUP INFORMATION
==================
Tanggal Backup: 11 Januari 2025, 14:05
Versi: Sebelum Migrasi ke Full Laravel MVC
Database: perpustakaan_whn
File Database: backup_20250111_140523.sql (45.2 MB)
File Project: perpustakaan_backup_20250111.zip (156 MB)

Status:
- Backend Node.js masih aktif
- Frontend Laravel berfungsi normal
- Database: 150 koleksi, 45 aktivitas, 230 mahasiswa

Alasan Backup:
- Migrasi dari Backend Node.js + Laravel → Full Laravel MVC
- Referensi: CHECKLIST_MIGRASI.md
```

---

## 🎯 REKOMENDASI BACKUP TERBAIK

**Untuk keamanan maksimal, lakukan SEMUA ini:**

1. **Git Commit** (untuk tracking perubahan)
   ```bash
   git add .
   git commit -m "Backup before migration"
   ```

2. **Database Backup** (untuk safety data)
   ```bash
   mysqldump -u root -p perpustakaan_whn > backup/backup_database.sql
   ```

3. **Copy Folder** (untuk backup fisik)
   ```bash
   robocopy "Perpustakaan - WHN" "Perpustakaan - WHN - BACKUP" /E /XD node_modules vendor
   ```

**Total waktu:** ~5-10 menit (tergantung ukuran project)

---

## 🆘 TROUBLESHOOTING BACKUP

### **Error: mysqldump not found**

**Solusi 1:** Tambahkan MySQL ke PATH

```powershell
# Cari lokasi MySQL
$mysqlPath = "C:\Program Files\MySQL\MySQL Server 8.0\bin"

# Tambahkan ke PATH sementara
$env:Path += ";$mysqlPath"

# Coba lagi
mysqldump -u root -p perpustakaan_whn > backup_database.sql
```

**Solusi 2:** Gunakan full path

```powershell
& "C:\Program Files\MySQL\MySQL Server 8.0\bin\mysqldump.exe" -u root -p perpustakaan_whn > backup_database.sql
```

---

### **Error: Access denied for user**

Cek username dan password MySQL:

```powershell
# Test koneksi dulu
mysql -u root -p

# Jika gagal, coba user lain:
mysql -u laravel_user -p
```

---

### **Backup file terlalu besar**

Compress dengan gzip:

```powershell
# Backup dan compress sekaligus
mysqldump -u root -p perpustakaan_whn | gzip > backup_database.sql.gz
```

---

### **Lupa password MySQL**

**Reset password MySQL** (jika benar-benar lupa):

1. Stop MySQL service
2. Start MySQL dengan skip-grant-tables
3. Reset password
4. Restart MySQL normal

**Atau:** Gunakan phpMyAdmin/MySQL Workbench yang sudah tersimpan credentials

---

## ✨ TIPS PRO

1. **Automasi Backup** (Buat script PowerShell):

```powershell
# backup.ps1
$timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
$backupDir = "D:\Backup\Perpustakaan\$timestamp"
New-Item -ItemType Directory -Path $backupDir -Force

# Backup database
mysqldump -u root -pYOUR_PASSWORD perpustakaan_whn > "$backupDir\database.sql"

# Backup project
Compress-Archive -Path "D:\Aplikasi\VSCode\Perpustakaan - WHN\*" -DestinationPath "$backupDir\project.zip"

Write-Host "Backup selesai di: $backupDir"
```

Jalankan: `.\backup.ps1`

2. **Schedule Backup** (Windows Task Scheduler):
   - Buat task yang menjalankan script backup setiap hari/minggu

3. **Cloud Backup**:
   - Upload backup ke Google Drive, Dropbox, OneDrive
   - Atau push Git repository ke GitHub/GitLab

---

## 🎊 SELESAI

Dengan panduan ini, Anda bisa:
- ✅ Backup database dengan aman
- ✅ Backup project/kode dengan lengkap
- ✅ Restore jika ada masalah
- ✅ Migrasi dengan tenang (ada safety net)

**Sekarang Anda siap untuk migrasi! Lanjut ke CHECKLIST_MIGRASI.md** 🚀

