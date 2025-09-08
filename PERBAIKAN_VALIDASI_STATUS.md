# Perbaikan Validasi Status - Mencegah Peminjaman Ulang

## Masalah yang Ditemukan

Ketika status aktivitas sudah diubah menjadi "dikembalikan", status buku tiba-tiba berubah kembali menjadi "dipinjam" setelah refresh atau beberapa saat. Ini menyebabkan inkonsistensi data dan user experience yang buruk.

## Analisis Masalah

### 1. Masalah di `createAktivitas`
**Sebelum perbaikan:**
```typescript
// Update status koleksi menjadi 'Dipinjam' saat aktivitas peminjaman dibuat
await db.query(
  "UPDATE koleksi SET status = 'Dipinjam' WHERE kode = ?",
  [kode]
);
```

**Masalah:**
- Setiap aktivitas peminjaman baru selalu mengubah status koleksi menjadi "Dipinjam"
- Tidak mempertimbangkan status koleksi saat ini
- Buku yang sudah dikembalikan bisa otomatis berubah menjadi "Dipinjam" lagi

### 2. Masalah di `updateStatusAktivitas`
**Sebelum perbaikan:**
```typescript
// Ambil data aktivitas untuk mendapatkan kode buku
const [aktivitasRows] = await db.query(
  "SELECT kode FROM aktivitas WHERE id_aktivitas = ?",
  [id_aktivitas]
);
```

**Masalah:**
- Tidak ada validasi untuk mencegah perubahan status dari "dikembalikan" ke "dipinjam"
- User bisa mengubah status bolak-balik tanpa batasan

## Solusi yang Diterapkan

### 1. Perbaikan Method `createAktivitas`
```typescript
// Cek status koleksi saat ini sebelum mengupdate
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

**Keuntungan:**
- Status koleksi hanya diupdate jika buku "Tersedia"
- Buku yang sudah "Dipinjam" tidak akan diupdate lagi
- Konsistensi data terjaga

### 2. Perbaikan Method `updateStatusAktivitas`
```typescript
// Ambil data aktivitas untuk mendapatkan kode buku dan status koleksi saat ini
const [aktivitasRows] = await db.query(
  "SELECT a.kode, k.status as koleksi_status FROM aktivitas a JOIN koleksi k ON a.kode = k.kode WHERE a.id_aktivitas = ?",
  [id_aktivitas]
);

const kode = (aktivitasRows as any[])[0].kode;
const currentKoleksiStatus = (aktivitasRows as any[])[0].koleksi_status;

// Validasi: Jika status koleksi saat ini 'Tersedia' (sudah dikembalikan), 
// tidak boleh diubah menjadi 'dipinjam' lagi
if (currentKoleksiStatus === 'Tersedia' && status === 'dipinjam') {
  await db.query("ROLLBACK");
  console.log('Cannot change status to dipinjam for already returned book:', kode);
  return res.status(400).json({ 
    message: "Buku sudah dikembalikan dan tidak boleh dipinjam lagi. Buat aktivitas peminjaman baru jika diperlukan." 
  });
}
```

**Keuntungan:**
- Mencegah perubahan status dari "dikembalikan" ke "dipinjam"
- User mendapat pesan error yang jelas
- Integritas data terjaga

## Flow Status yang Benar

```
1. Buku Tersedia (status: 'Tersedia')
   ↓
2. User membuat aktivitas peminjaman
   ↓
3. Status buku otomatis berubah menjadi 'Dipinjam'
   ↓
4. User mengklik "Dikembalikan"
   ↓
5. Status buku berubah menjadi 'Tersedia'
   ↓
6. User TIDAK BISA mengubah status kembali ke 'dipinjam'
   ↓
7. User harus membuat aktivitas peminjaman baru jika ingin meminjam lagi
```

## Testing

### Test Case 1: Validasi Status Otomatis
- **Input:** Buat aktivitas peminjaman untuk buku yang sudah "Dipinjam"
- **Expected:** Status buku tetap "Dipinjam" (tidak berubah)
- **Result:** ✅ SUCCESS

### Test Case 2: Validasi Pencegahan Peminjaman Ulang
- **Input:** Coba ubah status dari "dikembalikan" ke "dipinjam"
- **Expected:** Error 400 dengan pesan yang jelas
- **Result:** ✅ SUCCESS

### Test Case 3: Konsistensi Status
- **Input:** Refresh halaman setelah status "dikembalikan"
- **Expected:** Status tetap "Tersedia" (tidak berubah)
- **Result:** ✅ SUCCESS

## Keuntungan Perbaikan

1. **Konsistensi Data:** Status buku tidak berubah secara tidak terduga
2. **User Experience:** User mendapat feedback yang jelas tentang status buku
3. **Integritas Database:** Mencegah perubahan status yang tidak valid
4. **Logika Bisnis:** Sesuai dengan aturan perpustakaan (buku dikembalikan = selesai)
5. **Audit Trail:** Setiap perubahan status memiliki alasan yang jelas

## File yang Diubah

- `src/controllers/aktivitas.controllers.ts` - Method `createAktivitas` dan `updateStatusAktivitas`

## Verifikasi

Perbaikan telah diverifikasi dengan test script yang menunjukkan:
- Status buku tidak berubah otomatis jika sudah "Dipinjam"
- Tidak bisa mengubah status dari "dikembalikan" ke "dipinjam"
- Pesan error yang jelas dan informatif
- Konsistensi status setelah refresh
