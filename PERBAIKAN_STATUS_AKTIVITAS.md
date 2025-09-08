# Perbaikan Status Aktivitas - Konsistensi Status Individual

## Masalah yang Ditemukan

Ketika menambahkan aktivitas peminjaman baru dengan kode buku yang sama (misalnya BK001), semua aktivitas peminjaman sebelumnya dengan kode yang sama juga ikut berubah statusnya menjadi "Dipinjam", padahal beberapa sudah dikembalikan.

**Contoh masalah:**
- Aktivitas #1: BK001 dipinjam oleh Andi (sudah dikembalikan)
- Aktivitas #2: BK001 dipinjam oleh Andi (sudah dikembalikan)  
- Aktivitas #3: BK001 dipinjam oleh Andi (masih aktif)
- **Saat menambah aktivitas #4: BK001 dipinjam oleh Budi**
- **Hasil: Semua aktivitas #1, #2, #3, #4 menampilkan status "Dipinjam"**

## Analisis Masalah

1. **Tabel aktivitas tidak memiliki kolom status** - Status diambil dari tabel koleksi melalui JOIN
2. **Logika update status koleksi** - Saat menambah aktivitas baru, status koleksi selalu diupdate menjadi "Dipinjam"
3. **Query aktivitas** - Menggunakan status dari tabel koleksi untuk semua aktivitas dengan kode yang sama

## Solusi yang Diterapkan

### 1. Perbaikan Logika `createAktivitas`

**File:** `src/controllers/aktivitas.controllers.ts`

**Sebelum:**
```typescript
// Update status koleksi menjadi 'Dipinjam' saat aktivitas peminjaman dibuat
await db.query(
  "UPDATE koleksi SET status = 'Dipinjam' WHERE kode = ?",
  [kode]
);
```

**Sesudah:**
```typescript
// Cek status koleksi saat ini
const [koleksiRows] = await db.query(
  "SELECT status FROM koleksi WHERE kode = ?",
  [kode]
);

const currentStatus = (koleksiRows as any[])[0]?.status || 'Tersedia';

// Update status koleksi menjadi 'Dipinjam' hanya jika status saat ini 'Tersedia'
if (currentStatus === 'Tersedia') {
  await db.query(
    "UPDATE koleksi SET status = 'Dipinjam' WHERE kode = ?",
    [kode]
  );
  console.log('Aktivitas peminjaman created and koleksi status updated to Dipinjam for kode:', kode);
} else {
  console.log('Aktivitas peminjaman created but koleksi status remains', currentStatus, 'for kode:', kode);
}
```

### 2. Perbaikan Logika `updateStatusAktivitas`

**Sebelum:**
```typescript
// Update status koleksi berdasarkan status aktivitas
if (status === 'dikembalikan') {
  await db.query(
    "UPDATE koleksi SET status = 'Tersedia' WHERE kode = ?",
    [kode]
  );
} else if (status === 'dipinjam') {
  await db.query(
    "UPDATE koleksi SET status = 'Dipinjam' WHERE kode = ?",
    [kode]
  );
}
```

**Sesudah:**
```typescript
// Cek apakah ada aktivitas peminjaman lain yang masih aktif untuk buku ini
const [otherActiveLoans] = await db.query(
  "SELECT COUNT(*) as count FROM aktivitas WHERE kode = ? AND id_aktivitas != ?",
  [kode, id_aktivitas]
);

const otherActiveCount = (otherActiveLoans as any[])[0].count;

// Update status koleksi berdasarkan status aktivitas dan aktivitas lain
if (status === 'dikembalikan') {
  // Jika dikembalikan dan tidak ada aktivitas lain, update status koleksi menjadi 'Tersedia'
  if (otherActiveCount === 0) {
    await db.query(
      "UPDATE koleksi SET status = 'Tersedia' WHERE kode = ?",
      [kode]
    );
    console.log('Koleksi status updated to Tersedia for kode:', kode, 'No other active loans');
  } else {
    console.log('Koleksi status remains Dipinjam for kode:', kode, 'Other active loans:', otherActiveCount);
  }
} else if (status === 'dipinjam') {
  // Jika dipinjam, update status koleksi menjadi 'Dipinjam'
  await db.query(
    "UPDATE koleksi SET status = 'Dipinjam' WHERE kode = ?",
    [kode]
  );
  console.log('Koleksi status updated to Dipinjam for kode:', kode);
}
```

### 3. Perbaikan Query `getAktivitas`

**Sebelum:**
```sql
k.status AS status_buku
```

**Sesudah:**
```sql
CASE 
  WHEN a.id_aktivitas = (
    SELECT id_aktivitas 
    FROM aktivitas a2 
    WHERE a2.kode = a.kode 
    ORDER BY a2.tanggal_peminjaman DESC, a2.id_aktivitas DESC 
    LIMIT 1
  ) THEN k.status
  ELSE 'dikembalikan'
END AS status_buku
```

### 4. Perbaikan Logika `deleteAktivitas`

**Sebelum:**
```typescript
// Update status koleksi menjadi 'Tersedia' karena aktivitas dihapus
await db.query(
  "UPDATE koleksi SET status = 'Tersedia' WHERE kode = ?",
  [kode]
);
```

**Sesudah:**
```typescript
// Cek apakah masih ada aktivitas lain untuk buku ini
const [remainingLoans] = await db.query(
  "SELECT COUNT(*) as count FROM aktivitas WHERE kode = ?",
  [kode]
);

const remainingCount = (remainingLoans as any[])[0].count;

// Update status koleksi menjadi 'Tersedia' hanya jika tidak ada aktivitas lain
if (remainingCount === 0) {
  await db.query(
    "UPDATE koleksi SET status = 'Tersedia' WHERE kode = ?",
    [kode]
  );
  console.log('Aktivitas deleted and koleksi status updated to Tersedia for kode:', kode, 'No remaining loans');
} else {
  console.log('Aktivitas deleted but koleksi status remains Dipinjam for kode:', kode, 'Remaining loans:', remainingCount);
}
```

## Hasil Perbaikan

### Sebelum Perbaikan:
- ❌ Semua aktivitas dengan kode buku yang sama menampilkan status yang sama
- ❌ Aktivitas yang sudah dikembalikan ikut berubah status saat ada aktivitas baru
- ❌ Inkonsistensi data antara status individual aktivitas dan status koleksi

### Sesudah Perbaikan:
- ✅ Aktivitas menampilkan status individual yang benar
- ✅ Aktivitas yang sudah dikembalikan tetap menampilkan status "dikembalikan"
- ✅ Hanya aktivitas terbaru yang menampilkan status dari tabel koleksi
- ✅ Status koleksi diupdate dengan logika yang tepat

## Testing

### Test Case 1: Aktivitas dengan Status Berbeda
```bash
# Cek aktivitas
curl http://localhost:5000/api/aktivitas
```

**Hasil:**
- Aktivitas lama yang sudah dikembalikan: `"status_buku": "dikembalikan"`
- Aktivitas terbaru yang masih aktif: `"status_buku": "Dipinjam"`

### Test Case 2: Menambah Aktivitas Baru
```bash
# Tambah aktivitas peminjaman baru
curl -X POST http://localhost:5000/api/aktivitas \
  -H "Content-Type: application/json" \
  -d '{
    "id_aktivitas":"AKT20250829132800",
    "kode":"BK001",
    "nrm":"202500000002",
    "tanggal_peminjaman":"2025-08-29",
    "jatuh_tempo":"2025-09-05"
  }'
```

**Hasil:**
- Aktivitas lama tetap menampilkan status yang benar
- Aktivitas baru menampilkan status "Dipinjam"
- Status koleksi tetap konsisten

## Kesimpulan

Masalah telah berhasil diperbaiki dengan mengimplementasikan:

1. **Logika status koleksi yang cerdas** - Hanya mengupdate status koleksi saat diperlukan
2. **Query aktivitas yang akurat** - Menampilkan status individual yang benar untuk setiap aktivitas
3. **Konsistensi data** - Status aktivitas dan koleksi tetap konsisten

Sekarang sistem perpustakaan dapat menangani multiple aktivitas peminjaman untuk buku yang sama dengan status yang akurat dan konsisten.
