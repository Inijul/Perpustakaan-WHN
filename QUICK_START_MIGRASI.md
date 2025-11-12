# ⚡ Quick Start Migrasi - Full Laravel MVC

## 🎯 Untuk Anda yang:
- ✅ Data tidak kritis (development/testing)
- ✅ Ingin migrasi cepat dan praktis
- ✅ Siap edit langsung file yang ada

---

## 📋 LANGKAH MIGRASI (35-45 MENIT)

### **STEP 0: Backup Minimal (2 menit)** - OPSIONAL

```powershell
cd "D:\Aplikasi\VSCode\Perpustakaan - WHN"
git add .
git commit -m "Before migration to Full Laravel MVC"
```

**Jika belum pakai Git, skip aja** (data untuk development)

---

### **STEP 1: Update .env Laravel (5 menit)** ✅

1. **Buka file:** `frontend/.env`

2. **Cari bagian database:**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1  # atau mysql jika pakai Docker
   DB_PORT=3306
   DB_DATABASE=perpustakaan_whn  # sesuaikan dengan nama database Anda
   DB_USERNAME=root  # sesuaikan
   DB_PASSWORD=your_password  # sesuaikan
   ```

3. **Sesuaikan dengan database MySQL Anda**
   - Lihat konfigurasi di backend (`src/config/database.ts`) untuk tahu DB yang benar
   - Atau cek di `docker-compose.yml` bagian environment mysql

4. **Save file**

5. **Test koneksi database:**
   ```bash
   cd frontend
   php artisan tinker
   ```
   
   Di tinker, ketik:
   ```php
   DB::connection()->getPdo();
   exit;
   ```
   
   **✅ Jika muncul object PDO** → Koneksi berhasil! Lanjut!
   **❌ Jika error** → Cek lagi `.env`, ada yang salah

---

### **STEP 2: Update Models (10 menit)**

Edit 3 file Model, tambahkan `public $timestamps = false;`

#### **A. File: `frontend/app/Models/Aktivitas.php`**

**Tambahkan baris ini setelah class declaration:**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aktivitas extends Model
{
    protected $table = 'aktivitas';
    protected $primaryKey = 'id_aktivitas';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false; // ← TAMBAHKAN BARIS INI

    // ... sisanya tetap sama
}
```

#### **B. File: `frontend/app/Models/Koleksi.php`**

**Tambahkan baris ini:**

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
    public $timestamps = false; // ← TAMBAHKAN BARIS INI

    // ... sisanya tetap sama
}
```

#### **C. File: `frontend/app/Models/Mahasiswa.php`**

**Tambahkan baris ini:**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    protected $table = 'mahasiswa';
    protected $primaryKey = 'nrm';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false; // ← TAMBAHKAN BARIS INI

    // ... sisanya tetap sama
}
```

---

### **STEP 3: Update Controllers (15 menit)**

Ini step yang paling penting. Ganti HTTP calls ke backend dengan query langsung ke database.

#### **A. File: `frontend/app/Http/Controllers/AktivitasController.php`**

**Cari bagian method `index()` di baris ~14-23:**

```php
// HAPUS ini (HTTP call ke backend):
$response = Http::timeout(30)->get('http://backend:5000/api/aktivitas');
$aktivitas = $response->json();
```

**GANTI dengan:**

```php
// Query langsung ke database
$aktivitasCollection = \App\Models\Aktivitas::with(['mahasiswa', 'koleksi'])
    ->select('aktivitas.*')
    ->orderBy('tanggal_peminjaman', 'desc')
    ->get();

// Transform data
$aktivitas = $aktivitasCollection->map(function($item) {
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
        'topik' => $item->koleksi->topik ?? '-',
    ];
})->toArray();
```

**Untuk method `store()` di baris ~217-286:**

Cari bagian:
```php
// HAPUS HTTP call ini:
$response = Http::timeout(30)->post('http://backend:5000/api/aktivitas', $data);
```

**GANTI dengan:**

```php
try {
    DB::beginTransaction();
    
    // Cek apakah buku masih dipinjam
    $existingAktivitas = \App\Models\Aktivitas::where('kode', $request->kode)
        ->where('status', 'dipinjam')
        ->first();
    
    if ($existingAktivitas) {
        DB::rollBack();
        return redirect()->back()->withErrors([
            'error' => 'Buku masih dipinjam dan belum dikembalikan.'
        ]);
    }
    
    // Insert aktivitas
    $aktivitas = \App\Models\Aktivitas::create([
        'id_aktivitas' => $id_aktivitas,
        'kode' => $request->kode,
        'nrm' => $request->nrm,
        'tanggal_peminjaman' => $request->tanggal_peminjaman,
        'jatuh_tempo' => $request->jatuh_tempo,
        'status' => 'dipinjam'
    ]);
    
    DB::commit();
    
    Log::info('Aktivitas berhasil ditambahkan', ['id_aktivitas' => $id_aktivitas]);
    
    return redirect()->route('aktivitas.index')->with('success', 'Aktivitas berhasil ditambahkan');
    
} catch (\Exception $e) {
    DB::rollBack();
    Log::error('Exception saat menambahkan aktivitas', ['error' => $e->getMessage()]);
    return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
}
```

**Untuk method `updateStatus()` di baris ~305-396:**

Cari:
```php
// HAPUS HTTP call ini:
$response = Http::timeout(30)->patch("http://backend:5000/api/aktivitas/{$id_aktivitas}/status", [
    'status' => $status
]);
```

**GANTI dengan:**

```php
try {
    DB::beginTransaction();
    
    // Cari aktivitas
    $aktivitas = \App\Models\Aktivitas::where('id_aktivitas', $id_aktivitas)->first();
    
    if (!$aktivitas) {
        DB::rollBack();
        return response()->json(['error' => 'Aktivitas tidak ditemukan'], 404);
    }
    
    // Update status
    $aktivitas->status = $status;
    $aktivitas->save();
    
    DB::commit();
    
    // Ambil data dengan relasi
    $aktivitas = \App\Models\Aktivitas::with(['mahasiswa', 'koleksi'])
        ->where('id_aktivitas', $id_aktivitas)
        ->first();
    
    $responseData = [
        'id_aktivitas' => $aktivitas->id_aktivitas,
        'kode' => $aktivitas->kode,
        'nrm' => $aktivitas->nrm,
        'status_aktivitas' => $aktivitas->status,
        'nama_mahasiswa' => $aktivitas->mahasiswa->namam ?? '-',
        'judul_buku' => $aktivitas->koleksi->judul ?? '-',
    ];
    
    Log::info('Status berhasil diupdate', ['id_aktivitas' => $id_aktivitas, 'status' => $status]);
    
    if (request()->expectsJson()) {
        return response()->json(['success' => true, 'data' => $responseData]);
    }
    
    return redirect()->route('aktivitas.index')->with('success', 'Status berhasil diupdate');
    
} catch (\Exception $e) {
    DB::rollBack();
    Log::error('Exception saat update status', ['error' => $e->getMessage()]);
    
    if (request()->expectsJson()) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
    
    return redirect()->back()->withErrors(['error' => $e->getMessage()]);
}
```

**Tambahkan use statements di bagian atas file (setelah namespace):**

```php
use Illuminate\Support\Facades\DB;
use App\Models\Aktivitas;
use App\Models\Koleksi;
use App\Models\Mahasiswa;
```

---

#### **B. File: `frontend/app/Http/Controllers/KoleksiController.php`**

**Cari bagian method `index()` di baris ~12-35:**

```php
// HAPUS ini:
$response = Http::timeout(30)->get('http://backend:5000/api/koleksi');
$koleksis = $response->json();
```

**GANTI dengan:**

```php
// Query dengan status dinamis
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

**Untuk method `store()` - ganti HTTP call dengan:**

```php
try {
    // Insert langsung ke database
    $koleksi = \App\Models\Koleksi::create($data);
    
    Log::info('Koleksi berhasil ditambahkan', ['kode' => $data['kode']]);
    
    return redirect()->route('koleksi.index')->with('success', 'Koleksi berhasil ditambahkan');
    
} catch (\Exception $e) {
    Log::error('Exception saat menambahkan koleksi', ['error' => $e->getMessage()]);
    return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
}
```

**Untuk method `update()` - ganti HTTP call dengan:**

```php
try {
    $koleksi = \App\Models\Koleksi::where('kode', $kode)->first();
    
    if (!$koleksi) {
        return response()->json(['success' => false, 'message' => 'Koleksi tidak ditemukan'], 404);
    }
    
    $koleksi->update($data);
    
    Log::info('Koleksi berhasil diperbarui', ['kode' => $kode]);
    
    return response()->json(['success' => true, 'message' => 'Koleksi berhasil diperbarui']);
    
} catch (\Exception $e) {
    Log::error('Exception saat update koleksi', ['error' => $e->getMessage()]);
    return response()->json(['success' => false, 'message' => 'Terjadi kesalahan'], 500);
}
```

**Untuk method `destroy()` - ganti HTTP call dengan:**

```php
try {
    $koleksi = \App\Models\Koleksi::where('kode', $kode)->first();
    
    if (!$koleksi) {
        return response()->json(['success' => false, 'message' => 'Koleksi tidak ditemukan'], 404);
    }
    
    // Cek apakah masih ada aktivitas dipinjam
    $aktivitasDipinjam = \App\Models\Aktivitas::where('kode', $kode)
        ->where('status', 'dipinjam')
        ->exists();
    
    if ($aktivitasDipinjam) {
        return response()->json([
            'success' => false, 
            'message' => 'Tidak dapat menghapus koleksi karena masih dipinjam'
        ], 400);
    }
    
    // Hapus aktivitas terkait (yang sudah dikembalikan)
    \App\Models\Aktivitas::where('kode', $kode)->delete();
    
    // Hapus koleksi
    $koleksi->delete();
    
    Log::info('Koleksi berhasil dihapus', ['kode' => $kode]);
    
    return response()->json(['success' => true, 'message' => 'Koleksi berhasil dihapus']);
    
} catch (\Exception $e) {
    Log::error('Exception saat hapus koleksi', ['error' => $e->getMessage()]);
    return response()->json(['success' => false, 'message' => 'Terjadi kesalahan'], 500);
}
```

**Tambahkan use statements:**

```php
use Illuminate\Support\Facades\DB;
use App\Models\Koleksi;
use App\Models\Aktivitas;
```

---

### **STEP 4: Clear Cache Laravel (2 menit)**

```bash
cd frontend

php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

### **STEP 5: Test Laravel (10 menit)** ⚠️ PENTING!

```bash
cd frontend
php artisan serve
```

**Buka browser:** `http://localhost:8000`

**Test checklist:**
- □ Login berhasil?
- □ Halaman Koleksi tampil data?
- □ Tambah koleksi berfungsi?
- □ Edit koleksi berfungsi?
- □ Hapus koleksi berfungsi?
- □ Halaman Aktivitas tampil data?
- □ Tambah aktivitas (peminjaman) berfungsi?
- □ Update status ke "dikembalikan" berfungsi?
- □ Tidak ada error di console browser (F12)?

**Cek log jika ada error:**
```bash
tail -f storage/logs/laravel.log
```

**✅ SEMUA OK?** → Lanjut Step 6!
**❌ ADA ERROR?** → Screenshot error-nya, tanya ke saya

---

### **STEP 6: Hapus Backend Node.js (5 menit)**

⚠️ **HANYA LAKUKAN SETELAH STEP 5 BERHASIL!**

```powershell
cd "D:\Aplikasi\VSCode\Perpustakaan - WHN"

# Hapus folder backend
Remove-Item src -Recurse -Force
Remove-Item dist -Recurse -Force
Remove-Item node_modules -Recurse -Force

# Hapus file konfigurasi Node.js
Remove-Item tsconfig.json -Force
Remove-Item package.json -Force
Remove-Item package-lock.json -Force
```

**Atau lewat File Explorer:** Hapus manual folder/file tersebut

---

### **STEP 7: Final Test & Commit (5 menit)**

```bash
# Test sekali lagi
cd frontend
php artisan serve

# Test di browser - pastikan masih OK
```

**Jika semua OK:**

```powershell
cd ..
git add .
git commit -m "Migrasi selesai: Full Laravel MVC tanpa backend Node.js"
```

---

## ✅ CHECKLIST SINGKAT

```
□ STEP 0: Git commit (backup)
□ STEP 1: Update .env → test koneksi DB
□ STEP 2: Edit 3 Models → tambah timestamps = false
□ STEP 3: Edit 2 Controllers → ganti HTTP calls
□ STEP 4: Clear cache
□ STEP 5: TEST! (paling penting)
□ STEP 6: Hapus backend (setelah yakin OK)
□ STEP 7: Final test & commit
□ DONE! ✅
```

---

## ⏱️ Total Waktu: 35-45 menit

---

## 🆘 Troubleshooting

### **Error: Connection refused**
→ Cek `.env`, pastikan DB_HOST, DB_PASSWORD benar
→ Test: `mysql -u root -p`

### **Error: Class not found**
→ Jalankan: `composer dump-autoload`

### **Error: timestamps**
→ Pastikan semua Model ada `public $timestamps = false;`

### **Data tidak muncul**
```bash
php artisan tinker
>>> \App\Models\Koleksi::count();
>>> \App\Models\Aktivitas::first();
```

---

## 🎯 HASIL AKHIR

**Sebelum:** Laravel + Node.js (2 server, lebih lambat)
**Sesudah:** Laravel only (1 server, lebih cepat!)

---

## 💡 TIPS

1. **Jangan hapus backend dulu** sampai 100% yakin Laravel OK
2. **Test tiap step** - jangan skip testing
3. **Cek log Laravel** jika ada error
4. **Browser console (F12)** bisa kasih info error

---

Selamat migrasi! 🚀 Jika ada masalah, tanya ke Cursor AI.
