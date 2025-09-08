# Perubahan Status Koleksi - Integrasi dengan Aktivitas

## Ringkasan Perubahan

Berdasarkan permintaan user untuk menggunakan hanya 1 kolom status di tabel `koleksi`, telah dilakukan perubahan pada struktur database dan kode program untuk menghapus kolom status dari tabel `aktivitas` dan mengintegrasikan status dengan tabel `koleksi`.

## Perubahan Database

### 1. Tabel Koleksi
```sql
-- Kolom status sudah ada di tabel koleksi
ALTER TABLE koleksi ADD COLUMN status ENUM('Tersedia', 'Dipinjam') DEFAULT 'Tersedia' AFTER sampul;
```

### 2. Tabel Aktivitas
```sql
-- Kolom status dihapus dari tabel aktivitas (sudah dilakukan manual oleh user)
-- Struktur tabel aktivitas sekarang:
CREATE TABLE aktivitas (
    id_aktivitas VARCHAR(50) PRIMARY KEY NOT NULL,
    kode VARCHAR(50) NOT NULL,
    nrm VARCHAR(12) NOT NULL,
    tanggal_peminjaman DATE NOT NULL,
    jatuh_tempo DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (kode) REFERENCES koleksi(kode) ON DELETE CASCADE,
    FOREIGN KEY (nrm) REFERENCES mahasiswa(nrm) ON DELETE CASCADE
);
```

## Perubahan Backend (Node.js)

### 1. `src/controllers/aktivitas.controllers.ts`

#### GET Aktivitas
- **Sebelum**: Mengambil `a.status` dari tabel aktivitas
- **Sesudah**: Mengambil `k.status AS status_buku` dari tabel koleksi

```typescript
// Sebelum
SELECT a.id_aktivitas, a.kode, a.nrm, a.status, a.tanggal_peminjaman, a.jatuh_tempo,
       m.namam AS nama_mahasiswa, k.judul AS judul_buku

// Sesudah
SELECT a.id_aktivitas, a.kode, a.nrm, a.tanggal_peminjaman, a.jatuh_tempo,
       m.namam AS nama_mahasiswa, k.judul AS judul_buku, k.status AS status_buku
```

#### CREATE Aktivitas
- **Sebelum**: Insert dengan status ke tabel aktivitas
- **Sesudah**: Insert tanpa status ke aktivitas + update status koleksi

```typescript
// Sebelum
await db.query(
  "INSERT INTO aktivitas (id_aktivitas, kode, nrm, status, tanggal_peminjaman, jatuh_tempo) VALUES (?, ?, ?, ?, ?, ?)",
  [id_aktivitas, kode, nrm, status, tanggal_peminjaman, jatuh_tempo]
);

// Sesudah
await db.query("START TRANSACTION");
try {
  await db.query(
    "INSERT INTO aktivitas (id_aktivitas, kode, nrm, tanggal_peminjaman, jatuh_tempo) VALUES (?, ?, ?, ?, ?)",
    [id_aktivitas, kode, nrm, tanggal_peminjaman, jatuh_tempo]
  );
  await db.query(
    "UPDATE koleksi SET status = 'Dipinjam' WHERE kode = ?",
    [kode]
  );
  await db.query("COMMIT");
} catch (err) {
  await db.query("ROLLBACK");
  throw err;
}
```

#### UPDATE Status Aktivitas
- **Sebelum**: Update status di tabel aktivitas
- **Sesudah**: Update status di tabel koleksi

```typescript
// Sebelum
await db.query("UPDATE aktivitas SET status = ? WHERE id_aktivitas = ?", [status, id_aktivitas]);

// Sesudah
const kode = (aktivitasRows as any[])[0].kode;
const koleksiStatus = status === 'dipinjam' ? 'Dipinjam' : 'Tersedia';
await db.query("UPDATE koleksi SET status = ? WHERE kode = ?", [koleksiStatus, kode]);
```

## Perubahan Frontend (Laravel)

### 1. `frontend/app/Http/Controllers/AktivitasController.php`

#### Validasi Input
- **Sebelum**: Memvalidasi field `status`
- **Sesudah**: Menghapus validasi status karena tidak ada lagi

```php
// Sebelum
$request->validate([
    'nrm' => 'required|string|max:12',
    'kode' => 'required|string|max:50',
    'tanggal_peminjaman' => 'required|date',
    'jatuh_tempo' => 'required|date|after_or_equal:tanggal_peminjaman',
    'status' => 'required|in:dipinjam,dikembalikan',
]);

// Sesudah
$request->validate([
    'nrm' => 'required|string|max:12',
    'kode' => 'required|string|max:50',
    'tanggal_peminjaman' => 'required|date',
    'jatuh_tempo' => 'required|date|after_or_equal:tanggal_peminjaman',
]);
```

#### Filter Status
- **Sebelum**: Filter berdasarkan `$item['status']`
- **Sesudah**: Filter berdasarkan `$item['status_buku']`

```php
// Sebelum
$aktivitas = array_filter($aktivitas, function($item) use ($status) {
    return $item['status'] === $status;
});

// Sesudah
$aktivitas = array_filter($aktivitas, function($item) use ($status) {
    return $item['status_buku'] === $status;
});
```

#### Update Status Method
- **Sebelum**: Update status di tabel aktivitas
- **Sesudah**: Update status di tabel koleksi

```php
// Sebelum
$aktivitas = DB::table('aktivitas')
    ->where('id_aktivitas', $id_aktivitas)
    ->update(['status' => $status]);

// Sesudah
$koleksiStatus = ($status === 'dipinjam') ? 'Dipinjam' : 'Tersedia';
$koleksiUpdate = DB::table('koleksis')
    ->where('kode', $aktivitasData->kode)
    ->update(['status' => $koleksiStatus]);
```

### 2. `frontend/resources/views/aktivitas.blade.php`

#### Data Attributes
- **Sebelum**: `data-status="{{ $item['status'] ?? '' }}"`
- **Sesudah**: `data-status="{{ $item['status_buku'] ?? '' }}"`

#### Alpine.js Data
- **Sebelum**: `selected: '{{ $item['status'] }}'`
- **Sesudah**: `selected: '{{ $item['status_buku'] ?? 'dipinjam' }}'`

### 3. `frontend/resources/views/components/modal-aktivitas.blade.php`

#### Field Status
- **Sebelum**: Ada field dropdown untuk memilih status
- **Sesudah**: Field status dihapus karena status otomatis 'dipinjam' saat aktivitas dibuat

## Logika Status

### 1. Saat Membuat Aktivitas Baru
- Status koleksi otomatis berubah menjadi `'Dipinjam'`
- Tidak ada field status di form aktivitas

### 2. Saat Mengubah Status Aktivitas
- Status `'dipinjam'` → Status koleksi menjadi `'Dipinjam'`
- Status `'dikembalikan'` → Status koleksi menjadi `'Tersedia'`

### 3. Tampilan Status
- Status ditampilkan dari tabel koleksi (`status_buku`)
- Dropdown status hanya muncul untuk aktivitas dengan status `'dipinjam'`

## Keuntungan Perubahan

1. **Konsistensi Data**: Status buku hanya ada di satu tempat (tabel koleksi)
2. **Integritas Data**: Tidak ada kemungkinan status aktivitas dan koleksi berbeda
3. **Simplifikasi**: Tidak perlu sinkronisasi status antar tabel
4. **Performansi**: Query lebih sederhana karena tidak perlu join untuk status

## Testing

Untuk memastikan perubahan berfungsi dengan baik:

1. **Test Membuat Aktivitas Baru**:
   - Buat aktivitas peminjaman baru
   - Verifikasi status buku di halaman koleksi berubah menjadi 'Dipinjam'

2. **Test Mengubah Status**:
   - Ubah status aktivitas dari 'dipinjam' ke 'dikembalikan'
   - Verifikasi status buku di halaman koleksi berubah menjadi 'Tersedia'

3. **Test Filter Status**:
   - Filter aktivitas berdasarkan status
   - Verifikasi hasil filter sesuai dengan status buku di koleksi
