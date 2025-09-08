# Solusi Status Berdasarkan ID Aktivitas

## Masalah yang Ditemukan

**Masalah Utama:** Sistem masih menggunakan status dari tabel `koleksi` (berdasarkan `kode` buku) untuk menampilkan status aktivitas. Ini menyebabkan semua aktivitas untuk buku yang sama menampilkan status yang sama, bahkan yang sudah dikembalikan.

**Skenario Masalah:**
1. Aktivitas A: Buku B001 dipinjam (`status_aktivitas` = "Dipinjam")
2. Aktivitas A: Buku B001 dikembalikan (`status_aktivitas` = "Dikembalikan")
3. Aktivitas B: Buku B001 dipinjam lagi (`status_aktivitas` = "Dipinjam")
4. **Masalah**: Aktivitas A seolah-olah menjadi 'Dipinjam' lagi karena status koleksi berubah

## Akar Masalah

1. **Status aktivitas diambil dari tabel `koleksi`**: Semua aktivitas untuk buku yang sama menampilkan status yang sama
2. **Status koleksi saling menimpa**: Ketika ada aktivitas baru, status koleksi berubah dan mempengaruhi semua aktivitas
3. **Tidak ada status individual**: Setiap aktivitas tidak memiliki status yang independen

## Solusi yang Diterapkan

### 1. Status Berdasarkan ID Aktivitas

**Prinsip Baru:**
- **Status aktivitas** diambil dari field `status_aktivitas` di tabel `aktivitas` (berdasarkan `id_aktivitas`)
- **Status koleksi** hanya untuk informasi saja, tidak mempengaruhi status aktivitas individual
- **Setiap aktivitas** memiliki status yang independen berdasarkan `id_aktivitas`

### 2. Query yang Diperbaiki

**GET Aktivitas:**
```sql
SELECT 
  a.id_aktivitas, 
  a.kode, 
  a.nrm, 
  a.tanggal_peminjaman, 
  a.jatuh_tempo,
  COALESCE(a.status_aktivitas, 'Dipinjam') AS status_aktivitas,
  m.namam AS nama_mahasiswa, 
  k.judul AS judul_buku,
  k.status AS status_buku
FROM aktivitas a
JOIN mahasiswa m ON a.nrm = m.nrm
JOIN koleksi k ON a.kode = k.kode
ORDER BY a.tanggal_peminjaman DESC
```

**Penjelasan:**
- `COALESCE(a.status_aktivitas, 'Dipinjam') AS status_aktivitas`: Status individual aktivitas
- `k.status AS status_buku`: Status buku saat ini (hanya untuk informasi)

### 3. Logika Validasi yang Diperbaiki

**Create Aktivitas:**
```typescript
// Validasi: cek apakah ada aktivitas lain yang masih dipinjam untuk buku yang sama
const [activeAktivitas] = await db.query(
  "SELECT COUNT(*) as count FROM aktivitas WHERE kode = ? AND COALESCE(status_aktivitas, 'Dipinjam') = 'Dipinjam'",
  [kode]
);

const activeCount = (activeAktivitas as any[])[0].count;

if (activeCount > 0) {
  return res.status(400).json({ 
    message: `Buku sedang dipinjam oleh ${activeCount} aktivitas lain. Tidak bisa dipinjam.` 
  });
}
```

**Update Status Aktivitas:**
```typescript
// Update status koleksi berdasarkan aktivitas yang masih aktif
const [otherAktivitas] = await db.query(
  "SELECT COUNT(*) as count FROM aktivitas WHERE kode = ? AND COALESCE(status_aktivitas, 'Dipinjam') = 'Dipinjam'",
  [kode]
);

const activeDipinjamCount = (otherAktivitas as any[])[0].count;

// Update status koleksi berdasarkan logika:
let newKoleksiStatus = '';
if (activeDipinjamCount > 0) {
  newKoleksiStatus = 'Dipinjam';
} else {
  newKoleksiStatus = 'Tersedia';
}
```

## Keuntungan Solusi

### 1. **Status Individual yang Akurat**
- Setiap aktivitas memiliki status sendiri berdasarkan `id_aktivitas`
- Status aktivitas tidak saling menimpa antar aktivitas
- Aktivitas yang sudah dikembalikan tetap memiliki status "Dikembalikan"

### 2. **Status Koleksi yang Informatif**
- Status koleksi hanya untuk informasi apakah buku tersedia atau dipinjam
- Status koleksi diupdate berdasarkan aktivitas yang masih aktif
- Status koleksi tidak mempengaruhi status aktivitas individual

### 3. **Logika Bisnis yang Benar**
- Buku yang sudah dikembalikan bisa dipinjam lagi
- Buku yang masih dipinjam tidak bisa dipinjam lagi
- Status aktivitas individual tetap akurat

## Skenario Testing

### Skenario 1: Aktivitas Tunggal
1. Buat aktivitas ACT001 untuk buku B001
2. **Expected**: 
   - ACT001: `status_aktivitas` = "Dipinjam"
   - `status_buku` = "Dipinjam"
3. Update status menjadi "dikembalikan"
4. **Expected**: 
   - ACT001: `status_aktivitas` = "Dikembalikan"
   - `status_buku` = "Tersedia"

### Skenario 2: Aktivitas Ganda
1. Buat aktivitas ACT001 untuk buku B001
2. Buat aktivitas ACT002 untuk buku B001
3. **Expected**: 
   - ACT001: `status_aktivitas` = "Dipinjam"
   - ACT002: `status_aktivitas` = "Dipinjam"
   - `status_buku` = "Dipinjam"
4. Update ACT001 menjadi "dikembalikan"
5. **Expected**: 
   - ACT001: `status_aktivitas` = "Dikembalikan"
   - ACT002: `status_aktivitas` = "Dipinjam"
   - `status_buku` = "Dipinjam" (karena ACT002 masih dipinjam)

### Skenario 3: Peminjaman Ulang
1. Aktivitas ACT001: Buku B001 dipinjam → dikembalikan
2. **Expected**: 
   - ACT001: `status_aktivitas` = "Dikembalikan"
   - `status_buku` = "Tersedia"
3. Buat aktivitas ACT002 untuk buku B001
4. **Expected**: 
   - ACT001: `status_aktivitas` = "Dikembalikan" (tetap)
   - ACT002: `status_aktivitas` = "Dipinjam"
   - `status_buku` = "Dipinjam"

## Response Format

```json
{
  "id_aktivitas": "ACT001",
  "kode": "B001",
  "status_aktivitas": "Dikembalikan",  // Status individual aktivitas
  "status_buku": "Tersedia",           // Status buku saat ini
  "nama_mahasiswa": "John Doe",
  "judul_buku": "Programming 101",
  // ... field lainnya
}
```

## Kesimpulan

Solusi ini mengatasi masalah duplikasi status dengan:
1. **Status berdasarkan `id_aktivitas`**: Setiap aktivitas memiliki status yang independen
2. **Status koleksi untuk informasi**: Status koleksi hanya untuk informasi, tidak mempengaruhi status aktivitas
3. **Logika bisnis yang benar**: Buku yang sudah dikembalikan bisa dipinjam lagi tanpa mempengaruhi status aktivitas sebelumnya

Sistem sekarang dapat menangani multiple aktivitas untuk buku yang sama tanpa saling menimpa status, dengan setiap aktivitas memiliki status yang akurat berdasarkan `id_aktivitas`.
