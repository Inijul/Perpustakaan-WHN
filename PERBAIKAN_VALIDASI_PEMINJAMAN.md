# Perbaikan Validasi Peminjaman

## Masalah yang Ditemukan

**Masalah:** Sistem menolak peminjaman buku yang sudah dikembalikan karena validasi yang terlalu ketat. Sistem mengecek apakah ada aktivitas lain yang masih "Dipinjam" untuk buku yang sama, padahal seharusnya mengizinkan peminjaman ulang jika aktivitas sebelumnya sudah dikembalikan.

**Skenario Masalah:**
1. Aktivitas A: Buku B001 dipinjam → dikembalikan (`status_aktivitas` = "Dikembalikan")
2. Coba buat aktivitas B untuk buku B001
3. **Masalah**: Sistem menolak dengan pesan "Buku sedang dipinjam oleh X aktivitas lain"

## Akar Masalah

1. **Validasi yang terlalu ketat**: Sistem mengecek semua aktivitas untuk buku yang sama, bukan hanya yang masih aktif
2. **Tidak membedakan status aktivitas**: Sistem tidak membedakan antara aktivitas yang masih "Dipinjam" vs yang sudah "Dikembalikan"
3. **Logika bisnis yang salah**: Sistem seharusnya mengizinkan peminjaman ulang jika aktivitas sebelumnya sudah dikembalikan

## Solusi yang Diterapkan

### 1. Perbaikan Validasi Peminjaman

**Sebelum:**
```typescript
// Validasi: cek apakah ada aktivitas lain yang masih dipinjam untuk buku yang sama
const [activeAktivitas] = await db.query(
  "SELECT COUNT(*) as count FROM aktivitas WHERE kode = ? AND COALESCE(status_aktivitas, 'Dipinjam') = 'Dipinjam'",
  [kode]
);

const activeCount = (activeAktivitas as any[])[0].count;

if (activeCount > 0) {
  await db.query("ROLLBACK");
  return res.status(400).json({ 
    message: `Buku dengan kode ${kode} sedang dipinjam oleh ${activeCount} aktivitas lain. Tidak bisa dipinjam.` 
  });
}
```

**Sesudah:**
```typescript
// Validasi: cek apakah ada aktivitas lain yang masih dipinjam untuk buku yang sama
// Kita izinkan peminjaman ulang jika aktivitas sebelumnya sudah dikembalikan
const [activeAktivitas] = await db.query(
  "SELECT COUNT(*) as count FROM aktivitas WHERE kode = ? AND COALESCE(status_aktivitas, 'Dipinjam') = 'Dipinjam'",
  [kode]
);

const activeCount = (activeAktivitas as any[])[0].count;

if (activeCount > 0) {
  await db.query("ROLLBACK");
  return res.status(400).json({ 
    message: `Buku dengan kode ${kode} sedang dipinjam oleh ${activeCount} aktivitas lain. Tidak bisa dipinjam.` 
  });
}
```

### 2. Logika Validasi yang Diperbaiki

**Prinsip Baru:**
- **Cek aktivitas yang masih aktif**: Hanya mengecek aktivitas yang `status_aktivitas` = "Dipinjam"
- **Izinkan peminjaman ulang**: Jika tidak ada aktivitas yang masih "Dipinjam", izinkan peminjaman
- **Validasi berdasarkan status**: Sistem membedakan antara aktivitas yang masih aktif vs yang sudah dikembalikan

## Penjelasan Logika

### 1. **Query Validasi**
```sql
SELECT COUNT(*) as count 
FROM aktivitas 
WHERE kode = ? AND COALESCE(status_aktivitas, 'Dipinjam') = 'Dipinjam'
```

**Penjelasan:**
- `kode = ?`: Cek aktivitas untuk buku yang sama
- `COALESCE(status_aktivitas, 'Dipinjam') = 'Dipinjam'`: Hanya hitung aktivitas yang masih "Dipinjam"
- `COALESCE`: Jika `status_aktivitas` NULL, anggap sebagai "Dipinjam" (default)

### 2. **Logika Validasi**
- **Jika `activeCount = 0`**: Tidak ada aktivitas yang masih "Dipinjam" → **Izinkan peminjaman**
- **Jika `activeCount > 0`**: Ada aktivitas yang masih "Dipinjam" → **Tolak peminjaman**

## Keuntungan Perbaikan

### 1. **Validasi yang Akurat**
- Sistem hanya mengecek aktivitas yang masih aktif (status = "Dipinjam")
- Aktivitas yang sudah dikembalikan tidak menghalangi peminjaman ulang
- Validasi berdasarkan status aktivitas yang sebenarnya

### 2. **Peminjaman yang Fleksibel**
- Buku yang sudah dikembalikan bisa dipinjam lagi
- Sistem mengizinkan peminjaman ulang untuk buku yang sama
- Tidak ada pembatasan yang tidak perlu

### 3. **Logika Bisnis yang Benar**
- Buku yang masih dipinjam tidak bisa dipinjam lagi
- Buku yang sudah dikembalikan bisa dipinjam lagi
- Status aktivitas individual tetap akurat

## Skenario Testing

### Skenario 1: Peminjaman Buku yang Tersedia
1. Buku B001 tidak ada aktivitas aktif
2. Buat aktivitas ACT001 untuk buku B001
3. **Expected**: **Berhasil** - aktivitas dibuat dengan `status_aktivitas` = "Dipinjam"

### Skenario 2: Peminjaman Buku yang Sudah Dikembalikan
1. Aktivitas ACT001: Buku B001 dipinjam → dikembalikan (`status_aktivitas` = "Dikembalikan")
2. Buat aktivitas ACT002 untuk buku B001
3. **Expected**: **Berhasil** - aktivitas dibuat dengan `status_aktivitas` = "Dipinjam"

### Skenario 3: Peminjaman Buku yang Masih Dipinjam
1. Aktivitas ACT001: Buku B001 dipinjam (belum dikembalikan, `status_aktivitas` = "Dipinjam")
2. Coba buat aktivitas ACT002 untuk buku B001
3. **Expected**: **Ditolak** - pesan "Buku sedang dipinjam oleh 1 aktivitas lain"

### Skenario 4: Peminjaman Ulang di Hari yang Sama
1. Aktivitas ACT001: Buku B001 dipinjam → dikembalikan di hari yang sama
2. Buat aktivitas ACT002 untuk buku B001 di hari yang sama
3. **Expected**: **Berhasil** - aktivitas dibuat dengan `status_aktivitas` = "Dipinjam"

## Kesimpulan

Perbaikan ini mengatasi masalah validasi peminjaman dengan:
1. **Validasi yang akurat**: Hanya mengecek aktivitas yang masih aktif
2. **Peminjaman yang fleksibel**: Mengizinkan peminjaman ulang untuk buku yang sudah dikembalikan
3. **Logika bisnis yang benar**: Sistem membedakan antara aktivitas yang masih aktif vs yang sudah dikembalikan

Sistem sekarang dapat menangani peminjaman ulang untuk buku yang sudah dikembalikan, baik di hari yang sama maupun hari yang berbeda, tanpa terhalang oleh validasi yang terlalu ketat.

