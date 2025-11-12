# 🚀 Panduan Migrasi ke Full Laravel MVC

## 📌 Ringkasan
Mengubah arsitektur dari **Backend Node.js + Frontend Laravel** menjadi **Full Laravel MVC**.

---

## 1️⃣ KONFIGURASI DATABASE LARAVEL

### A. Update File `.env` Laravel
Pastikan konfigurasi database di `frontend/.env` mengarah ke database MySQL yang sama dengan yang digunakan Node.js:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1  # atau sesuai dengan host MySQL Anda
DB_PORT=3306
DB_DATABASE=nama_database_anda  # Sama dengan DB_NAME di Node.js
DB_USERNAME=root  # Sama dengan DB_USER di Node.js
DB_PASSWORD=password_anda  # Sama dengan DB_PASSWORD di Node.js
```

**Catatan:** Lihat file `src/config/database.ts` untuk mendapatkan nilai yang sama.

### B. Verifikasi Koneksi Database
Jalankan perintah ini di terminal Laravel:

```bash
cd frontend
php artisan tinker
```

Lalu coba:
```php
DB::connection()->getPdo();
// Jika sukses, akan menampilkan objek PDO
```

---

## 2️⃣ UPDATE CONTROLLERS LARAVEL

### A. AktivitasController.php

**SEBELUM (Memanggil Backend Node.js):**
```php
$response = Http::timeout(30)->get('http://backend:5000/api/aktivitas');
$aktivitas = $response->json();
```

**SESUDAH (Langsung ke Database via Eloquent):**
```php
$aktivitas = Aktivitas::with(['mahasiswa', 'koleksi'])
    ->select('aktivitas.*')
    ->orderBy('tanggal_peminjaman', 'desc')
    ->get()
    ->map(function($item) {
        return [
            'id_aktivitas' => $item->id_aktivitas,
            'kode' => $item->kode,
            'nrm' => $item->nrm,
            'tanggal_peminjaman' => $item->tanggal_peminjaman,
            'jatuh_tempo' => $item->jatuh_tempo,
            'status_aktivitas' => $item->status,
            'nama_mahasiswa' => $item->mahasiswa->namam ?? '-',
            'judul_buku' => $item->koleksi->judul ?? '-',
            'kategori' => $item->koleksi->kategori ?? '-',
        ];
    })
    ->toArray();
```

### B. KoleksiController.php

**SEBELUM:**
```php
$response = Http::timeout(30)->get('http://backend:5000/api/koleksi');
$koleksis = $response->json();
```

**SESUDAH:**
```php
use Illuminate\Support\Facades\DB;

$koleksis = DB::table('koleksi as k')
    ->select(
        'k.*',
        DB::raw("CASE 
            WHEN EXISTS (
                SELECT 1 FROM aktivitas a 
                WHERE a.kode = k.kode 
                AND a.status = 'dipinjam'
            ) THEN 'Dipinjam'
            ELSE 'Tersedia'
        END as status")
    )
    ->orderBy('k.kode')
    ->get()
    ->toArray();
```

### C. MahasiswaController.php (Jika Ada)

**SESUDAH:**
```php
$mahasiswas = Mahasiswa::select('nrm', 'nim', 'namam')
    ->orderBy('namam')
    ->get()
    ->toArray();
```

---

## 3️⃣ UPDATE MODELS LARAVEL

### A. Model Koleksi (Buat/Update)

File: `frontend/app/Models/Koleksi.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Koleksi extends Model
{
    protected $table = 'koleksi';
    protected $primaryKey = 'kode';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'kode',
        'kategori',
        'topik',
        'judul',
        'penulis',
        'penerbit',
        'tahun_terbit',
        'lokasi_rak',
        'deskripsi',
        'tautan',
        'sampul'
    ];

    // Relationship dengan aktivitas
    public function aktivitas()
    {
        return $this->hasMany(Aktivitas::class, 'kode', 'kode');
    }

    // Helper untuk mendapatkan status
    public function getStatusAttribute()
    {
        $dipinjam = $this->aktivitas()
            ->where('status', 'dipinjam')
            ->exists();
        
        return $dipinjam ? 'Dipinjam' : 'Tersedia';
    }
}
```

### B. Update Model Aktivitas

Pastikan `public $timestamps = false;` jika tabel tidak punya `created_at` dan `updated_at`:

```php
class Aktivitas extends Model
{
    public $timestamps = false;
    
    // ... kode lainnya
}
```

### C. Update Model Mahasiswa

```php
class Mahasiswa extends Model
{
    public $timestamps = false;
    
    // ... kode lainnya
}
```

---

## 4️⃣ HAPUS/DISABLE BACKEND NODE.JS

### A. File/Folder yang Bisa Dihapus:
- ❌ `src/` (folder backend TypeScript)
- ❌ `dist/` (folder compiled JavaScript)
- ❌ `tsconfig.json`
- ❌ `package.json` dan `package-lock.json` (di root project)
- ❌ `node_modules/` (di root project)
- ❌ File SQL yang tidak diperlukan

### B. Update Docker Compose (Jika Menggunakan Docker)

**SEBELUM (ada service backend):**
```yaml
services:
  frontend:
    # ...
  backend:
    # ... hapus service ini
  mysql:
    # ...
```

**SESUDAH:**
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

---

## 5️⃣ UPDATE ROUTES

### A. Hapus Route API ke Backend

File: `frontend/routes/web.php`

**Pastikan semua route mengarah ke Controller Laravel:**

```php
use App\Http\Controllers\AktivitasController;
use App\Http\Controllers\KoleksiController;

Route::middleware(['auth'])->group(function () {
    // Koleksi Routes
    Route::get('/koleksi', [KoleksiController::class, 'index'])->name('koleksi.index');
    Route::post('/koleksi', [KoleksiController::class, 'store'])->name('koleksi.store');
    Route::get('/koleksi/{kode}', [KoleksiController::class, 'show'])->name('koleksi.show');
    Route::put('/koleksi/{kode}', [KoleksiController::class, 'update'])->name('koleksi.update');
    Route::delete('/koleksi/{kode}', [KoleksiController::class, 'destroy'])->name('koleksi.destroy');

    // Aktivitas Routes
    Route::get('/aktivitas', [AktivitasController::class, 'index'])->name('aktivitas.index');
    Route::post('/aktivitas', [AktivitasController::class, 'store'])->name('aktivitas.store');
    Route::get('/aktivitas/{id_aktivitas}', [AktivitasController::class, 'show'])->name('aktivitas.show');
    Route::patch('/aktivitas/{id_aktivitas}/status', [AktivitasController::class, 'updateStatus'])->name('aktivitas.updateStatus');
});
```

---

## 6️⃣ TESTING

### A. Test Koneksi Database
```bash
php artisan tinker
>>> \App\Models\Mahasiswa::count();
>>> \App\Models\Koleksi::count();
>>> \App\Models\Aktivitas::count();
```

### B. Test Controller
```bash
# Test mengambil data koleksi
>>> app(\App\Http\Controllers\KoleksiController::class)->index();
```

### C. Test di Browser
1. Jalankan Laravel: `php artisan serve`
2. Akses: `http://localhost:8000/koleksi`
3. Coba tambah, edit, hapus data

---

## 7️⃣ PERBANDINGAN SEBELUM DAN SESUDAH

### **SEBELUM (Backend Node.js + Laravel):**
```
Request → Laravel Controller → HTTP Call → Node.js API → MySQL → Response
         ↓                                                           ↓
         View ← Laravel Controller ← HTTP Response ← Node.js ← MySQL
```

### **SESUDAH (Full Laravel MVC):**
```
Request → Laravel Controller → Eloquent/Query Builder → MySQL → Response
         ↓                                                       ↓
         View ← Laravel Controller ← Eloquent/Query Builder ← MySQL
```

**Keuntungan:**
- ✅ Lebih sederhana (hanya 1 aplikasi)
- ✅ Lebih cepat (tidak ada HTTP overhead)
- ✅ Lebih mudah maintenance
- ✅ Bisa pakai fitur Laravel secara penuh (Eloquent, validation, etc)
- ✅ Tidak perlu manage 2 server berbeda

---

## 8️⃣ CHECKLIST MIGRASI

- [ ] Backup database
- [ ] Update `.env` Laravel dengan konfigurasi database
- [ ] Test koneksi database Laravel
- [ ] Update Model Koleksi (tambah timestamps = false jika perlu)
- [ ] Update AktivitasController (hapus HTTP calls)
- [ ] Update KoleksiController (hapus HTTP calls)
- [ ] Test semua fungsi CRUD
- [ ] Hapus folder `src/` dan `dist/`
- [ ] Update `docker-compose.yml` (hapus service backend)
- [ ] Test di browser
- [ ] Update dokumentasi

---

## 🆘 TROUBLESHOOTING

### Error: SQLSTATE[HY000] [1045] Access denied
- **Solusi:** Cek username/password di `.env`

### Error: Class 'App\Models\Koleksi' not found
- **Solusi:** Buat file `app/Models/Koleksi.php`

### Error: timestamps field not found
- **Solusi:** Tambah `public $timestamps = false;` di Model

### Data tidak muncul
- **Solusi:** Cek dengan `php artisan tinker` apakah data ada di database

---

## 📝 CATATAN PENTING

1. **Jangan hapus backend Node.js dulu** sampai yakin Laravel berjalan dengan baik
2. **Backup database** sebelum melakukan perubahan
3. **Test secara bertahap** - satu controller dulu, baru lanjut ke yang lain
4. **Gunakan Git** untuk tracking perubahan

---

## ✅ SELESAI

Setelah semua langkah di atas, aplikasi Anda sudah menggunakan Full Laravel MVC tanpa perlu backend Node.js lagi!

