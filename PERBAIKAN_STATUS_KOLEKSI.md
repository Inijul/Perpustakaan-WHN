# Perbaikan Status Koleksi - Update Otomatis Saat Aktivitas Peminjaman

## Masalah yang Ditemukan

Berdasarkan laporan user, ketika menambahkan aktivitas peminjaman baru, status buku di halaman "Kelola Koleksi" tidak berubah dari "Tersedia" menjadi "Dipinjam". Meskipun aktivitas peminjaman sudah tercatat dengan benar di halaman "Aktivitas", status koleksi tetap menunjukkan "Tersedia".

## Analisis Masalah

Setelah memeriksa kode, ditemukan bahwa:

1. **Backend Controller Aktivitas** (`src/controllers/aktivitas.controllers.ts`):
   - Fungsi `createAktivitas` tidak mengupdate status koleksi saat aktivitas peminjaman dibuat
   - Ada komentar yang menyatakan "TIDAK mengupdate status koleksi saat membuat aktivitas peminjaman"
   - Status koleksi hanya diupdate saat aktivitas dikembalikan

2. **Query Aktivitas** menggunakan logika kompleks untuk menentukan status berdasarkan aktivitas terbaru, bukan menggunakan status langsung dari tabel koleksi

## Solusi yang Diterapkan

### 1. Perbaikan Fungsi `createAktivitas`

**File:** `src/controllers/aktivitas.controllers.ts`

**Sebelum:**
```typescript
// Insert aktivitas (tanpa status)
await db.query(
  "INSERT INTO aktivitas (id_aktivitas, kode, nrm, tanggal_peminjaman, jatuh_tempo) VALUES (?, ?, ?, ?, ?)",
  [id_aktivitas, kode, nrm, tanggal_peminjaman, jatuh_tempo]
);

// TIDAK mengupdate status koleksi saat membuat aktivitas peminjaman
// Status koleksi akan diupdate hanya saat aktivitas dikembalikan
console.log('Aktivitas peminjaman created for kode:', kode);
```

**Sesudah:**
```typescript
// Insert aktivitas (tanpa status)
await db.query(
  "INSERT INTO aktivitas (id_aktivitas, kode, nrm, tanggal_peminjaman, jatuh_tempo) VALUES (?, ?, ?, ?, ?)",
  [id_aktivitas, kode, nrm, tanggal_peminjaman, jatuh_tempo]
);

// Update status koleksi menjadi 'Dipinjam' saat aktivitas peminjaman dibuat
await db.query(
  "UPDATE koleksi SET status = 'Dipinjam' WHERE kode = ?",
  [kode]
);
console.log('Aktivitas peminjaman created and koleksi status updated to Dipinjam for kode:', kode);
```

### 2. Perbaikan Query `getAktivitas`

**Sebelum:**
```sql
CASE 
  WHEN a.id_aktivitas = (
    SELECT id_aktivitas 
    FROM aktivitas 
    WHERE kode = a.kode 
    ORDER BY tanggal_peminjaman DESC, id_aktivitas DESC 
    LIMIT 1
  ) THEN 'Dipinjam'
  ELSE 'Tersedia'
END AS status_buku
```

**Sesudah:**
```sql
k.status AS status_buku
```

### 3. Perbaikan Fungsi `deleteAktivitas`

Ditambahkan logika untuk mengupdate status koleksi menjadi "Tersedia" saat aktivitas dihapus:

```typescript
// Ambil data aktivitas untuk mendapatkan kode buku sebelum dihapus
const [aktivitasRows] = await db.query(
  "SELECT kode FROM aktivitas WHERE id_aktivitas = ?",
  [id_aktivitas]
);

const kode = (aktivitasRows as any[])[0].kode;

// Hapus aktivitas
await db.query("DELETE FROM aktivitas WHERE id_aktivitas = ?", [id_aktivitas]);

// Update status koleksi menjadi 'Tersedia' karena aktivitas dihapus
await db.query(
  "UPDATE koleksi SET status = 'Tersedia' WHERE kode = ?",
  [kode]
);
```

## Hasil Perbaikan

### Sebelum Perbaikan:
- ✅ Aktivitas peminjaman tercatat dengan benar
- ❌ Status koleksi tetap "Tersedia"
- ❌ Inkonsistensi data antara aktivitas dan koleksi

### Sesudah Perbaikan:
- ✅ Aktivitas peminjaman tercatat dengan benar
- ✅ Status koleksi otomatis berubah menjadi "Dipinjam"
- ✅ Konsistensi data antara aktivitas dan koleksi
- ✅ Status koleksi kembali menjadi "Tersedia" saat aktivitas dihapus

## Testing

### Test Case 1: Membuat Aktivitas Peminjaman Baru
```bash
curl -X POST http://localhost:5000/api/aktivitas \
  -H "Content-Type: application/json" \
  -d '{
    "id_aktivitas":"AKT20250829131234",
    "kode":"BK001",
    "nrm":"202500000001",
    "tanggal_peminjaman":"2025-08-29",
    "jatuh_tempo":"2025-09-05"
  }'
```

**Hasil:**
- Aktivitas berhasil dibuat
- Status koleksi BK001 berubah dari "Tersedia" menjadi "Dipinjam"

### Test Case 2: Verifikasi Status di API
```bash
# Cek status koleksi
curl http://localhost:5000/api/koleksi

# Cek aktivitas
curl http://localhost:5000/api/aktivitas
```

**Hasil:**
- Koleksi BK001: `"status": "Dipinjam"`
- Aktivitas: `"status_buku": "Dipinjam"`

## Kesimpulan

Masalah telah berhasil diperbaiki dengan mengimplementasikan:

1. **Update otomatis status koleksi** saat aktivitas peminjaman dibuat
2. **Penggunaan status langsung dari tabel koleksi** di query aktivitas
3. **Rollback status koleksi** saat aktivitas dihapus

Sekarang sistem perpustakaan memiliki konsistensi data yang baik antara aktivitas peminjaman dan status koleksi buku.
