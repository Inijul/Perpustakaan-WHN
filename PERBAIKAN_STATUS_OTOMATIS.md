# Perbaikan Status Otomatis - Aktivitas Peminjaman

## Masalah yang Ditemukan

Ketika menambahkan aktivitas peminjaman baru, status buku di database koleksi tidak otomatis berubah menjadi "Dipinjam". Status buku tetap "Tersedia" meskipun sudah ada aktivitas peminjaman aktif.

## Analisis Masalah

### Sebelum Perbaikan
Di method `createAktivitas` di `src/controllers/aktivitas.controllers.ts`:

```typescript
// TIDAK mengupdate status koleksi saat membuat aktivitas peminjaman
// Status koleksi akan diupdate hanya saat aktivitas dikembalikan
console.log('Aktivitas peminjaman created for kode:', kode);
```

**Masalah:**
- Status koleksi tidak diupdate saat aktivitas peminjaman dibuat
- Buku tetap "Tersedia" meskipun sedang dipinjam
- User harus manual mengklik tombol "Dipinjam" untuk mengubah status

### Setelah Perbaikan
```typescript
// Update status koleksi menjadi 'Dipinjam' saat aktivitas peminjaman dibuat
await db.query(
  "UPDATE koleksi SET status = 'Dipinjam' WHERE kode = ?",
  [kode]
);
console.log('Aktivitas peminjaman created and koleksi status updated to Dipinjam for kode:', kode);
```

**Solusi:**
- Status koleksi otomatis berubah menjadi "Dipinjam" saat aktivitas dibuat
- Konsistensi data terjaga
- User experience lebih baik

## Logika Status yang Diterapkan

### 1. Saat Membuat Aktivitas Peminjaman
```typescript
// Insert aktivitas
await db.query(
  "INSERT INTO aktivitas (id_aktivitas, kode, nrm, tanggal_peminjaman, jatuh_tempo) VALUES (?, ?, ?, ?, ?)",
  [id_aktivitas, kode, nrm, tanggal_peminjaman, jatuh_tempo]
);

// Update status koleksi menjadi 'Dipinjam'
await db.query(
  "UPDATE koleksi SET status = 'Dipinjam' WHERE kode = ?",
  [kode]
);
```

### 2. Saat Mengubah Status Aktivitas
```typescript
if (status === 'dikembalikan') {
  // Update status koleksi menjadi 'Tersedia'
  await db.query(
    "UPDATE koleksi SET status = 'Tersedia' WHERE kode = ?",
    [kode]
  );
} else if (status === 'dipinjam') {
  // Update status koleksi menjadi 'Dipinjam'
  await db.query(
    "UPDATE koleksi SET status = 'Dipinjam' WHERE kode = ?",
    [kode]
  );
}
```

## Flow Status Buku

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
6. User bisa membuat aktivitas peminjaman baru
```

## Testing

### Test Case 1: Buku Tersedia → Peminjaman
- **Input:** Buat aktivitas peminjaman untuk buku BK001
- **Expected:** Status buku berubah dari "Tersedia" → "Dipinjam"
- **Result:** ✅ SUCCESS

### Test Case 2: Buku Sudah Dipinjam → Peminjaman Baru
- **Input:** Buat aktivitas peminjaman baru untuk buku BK001 (yang sudah dipinjam)
- **Expected:** Status buku tetap "Dipinjam"
- **Result:** ✅ SUCCESS (perilaku yang benar)

## Keuntungan Perbaikan

1. **Konsistensi Data:** Status buku selalu sesuai dengan kondisi aktual
2. **User Experience:** Tidak perlu manual mengubah status setelah peminjaman
3. **Integritas Database:** Data aktivitas dan koleksi selalu sinkron
4. **Otomatisasi:** Proses update status berjalan otomatis

## File yang Diubah

- `src/controllers/aktivitas.controllers.ts` - Method `createAktivitas`

## Verifikasi

Perbaikan telah diverifikasi dengan test script yang menunjukkan:
- Status buku otomatis berubah dari "Tersedia" ke "Dipinjam" saat aktivitas dibuat
- Status buku tetap konsisten saat aktivitas peminjaman baru dibuat untuk buku yang sudah dipinjam
