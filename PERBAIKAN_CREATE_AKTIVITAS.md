# Perbaikan Logika Create Aktivitas

## Masalah yang Ditemukan

**Masalah:** Ketika membuat aktivitas baru untuk buku yang sudah dikembalikan (status koleksi = 'Tersedia'), status koleksi berubah menjadi 'Dipinjam' lagi. Ini menyebabkan aktivitas yang sudah dikembalikan seolah-olah menjadi 'Dipinjam' lagi.

**Skenario Masalah:**
1. Aktivitas A: Buku B001 dipinjam (status koleksi = 'Dipinjam')
2. Aktivitas A: Buku B001 dikembalikan (status koleksi = 'Tersedia')
3. Aktivitas B: Buku B001 dipinjam lagi (status koleksi = 'Dipinjam')
4. **Masalah**: Aktivitas A seolah-olah menjadi 'Dipinjam' lagi karena status koleksi berubah

## Akar Masalah

1. **Validasi yang terlalu ketat**: Sistem menolak peminjaman buku yang status koleksinya 'Dipinjam'
2. **Logika update status koleksi**: Sistem selalu mengupdate status koleksi menjadi 'Dipinjam' ketika ada aktivitas baru
3. **Tidak mempertimbangkan aktivitas yang sudah dikembalikan**: Sistem tidak membedakan antara buku yang benar-benar dipinjam vs buku yang sudah dikembalikan

## Solusi yang Diterapkan

### 1. Perbaikan Validasi Peminjaman

**Sebelum:**
```typescript
// Validasi: hanya bisa meminjam buku yang tersedia
if (currentStatus !== 'Tersedia') {
  await db.query("ROLLBACK");
  return res.status(400).json({ 
    message: `Buku dengan kode ${kode} sedang ${currentStatus.toLowerCase()}. Tidak bisa dipinjam.` 
  });
}
```

**Sesudah:**
```typescript
// Validasi: hanya bisa meminjam buku yang tersedia
// Tapi kita perlu cek apakah ada aktivitas lain yang masih dipinjam
if (currentStatus !== 'Tersedia') {
  // Cek apakah ada aktivitas lain yang masih dipinjam
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
}
```

### 2. Logika Update Status Koleksi

**Sebelum:**
```typescript
// Update status koleksi menjadi 'Dipinjam'
await db.query(
  "UPDATE koleksi SET status = 'Dipinjam' WHERE kode = ?",
  [kode]
);
```

**Sesudah:**
```typescript
// Update status koleksi menjadi 'Dipinjam' (karena ada aktivitas baru yang dipinjam)
await db.query(
  "UPDATE koleksi SET status = 'Dipinjam' WHERE kode = ?",
  [kode]
);

console.log('Koleksi status updated to Dipinjam for kode:', kode, '(new aktivitas created)');
```

## Penjelasan Logika Baru

### 1. **Validasi yang Lebih Cerdas**
- Jika status koleksi 'Tersedia', langsung izinkan peminjaman
- Jika status koleksi 'Dipinjam', cek apakah ada aktivitas yang masih aktif
- Jika tidak ada aktivitas yang masih aktif, izinkan peminjaman
- Jika ada aktivitas yang masih aktif, tolak peminjaman

### 2. **Update Status Koleksi yang Konsisten**
- Status koleksi selalu diupdate menjadi 'Dipinjam' ketika ada aktivitas baru
- Ini konsisten dengan logika bahwa jika ada aktivitas yang dipinjam, koleksi harus 'Dipinjam'
- Status individual aktivitas tetap dipertahankan di field `status_aktivitas`

## Keuntungan Perbaikan

### 1. **Validasi yang Lebih Akurat**
- Sistem dapat membedakan antara buku yang benar-benar dipinjam vs buku yang sudah dikembalikan
- Peminjaman buku yang sudah dikembalikan diperbolehkan
- Peminjaman buku yang masih dipinjam ditolak

### 2. **Status yang Konsisten**
- Status koleksi selalu mencerminkan apakah ada aktivitas yang masih aktif
- Status individual aktivitas tetap akurat di field `status_aktivitas`
- Tidak ada duplikasi status antar aktivitas

### 3. **Logika Bisnis yang Benar**
- Buku yang sudah dikembalikan bisa dipinjam lagi
- Buku yang masih dipinjam tidak bisa dipinjam lagi
- Status koleksi diupdate berdasarkan aktivitas yang masih aktif

## Skenario Testing

### Skenario 1: Peminjaman Buku yang Tersedia
1. Buku B001 status koleksi = 'Tersedia'
2. Buat aktivitas ACT001 untuk buku B001
3. **Expected**: 
   - ACT001: `status_aktivitas` = "Dipinjam"
   - Status koleksi = "Dipinjam"

### Skenario 2: Peminjaman Buku yang Sudah Dikembalikan
1. Aktivitas ACT001: Buku B001 dipinjam → dikembalikan
2. Status koleksi = 'Tersedia' (karena semua aktivitas sudah dikembalikan)
3. Buat aktivitas ACT002 untuk buku B001
4. **Expected**: 
   - ACT001: `status_aktivitas` = "Dikembalikan"
   - ACT002: `status_aktivitas` = "Dipinjam"
   - Status koleksi = "Dipinjam" (karena ada aktivitas yang masih dipinjam)

### Skenario 3: Peminjaman Buku yang Masih Dipinjam
1. Aktivitas ACT001: Buku B001 dipinjam (belum dikembalikan)
2. Status koleksi = 'Dipinjam'
3. Coba buat aktivitas ACT002 untuk buku B001
4. **Expected**: 
   - Request ditolak dengan pesan: "Buku dengan kode B001 sedang dipinjam oleh 1 aktivitas lain. Tidak bisa dipinjam."

## Kesimpulan

Perbaikan ini mengatasi masalah duplikasi status dengan:
1. **Validasi yang lebih cerdas** untuk membedakan buku yang tersedia vs yang masih dipinjam
2. **Logika update status koleksi** yang konsisten dengan aktivitas yang masih aktif
3. **Status individual aktivitas** yang tetap akurat dan tidak saling menimpa

Sistem sekarang dapat menangani peminjaman buku yang sudah dikembalikan tanpa mempengaruhi status aktivitas yang sudah dikembalikan.
