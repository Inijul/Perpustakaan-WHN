# Solusi Hybrid untuk Status Aktivitas

## Pendekatan yang Digunakan

Kami menggunakan pendekatan **hybrid** yang menggabungkan:
1. **Field `status_aktivitas`** di tabel `aktivitas` untuk melacak status individual setiap aktivitas
2. **Status dari tabel `koleksi`** dengan JOIN untuk menampilkan status buku
3. **Logika cerdas** untuk mengelola status koleksi berdasarkan aktivitas yang masih aktif

## Mengapa Pendekatan Hybrid?

### Masalah dengan Pendekatan Murni JOIN:
- Tidak bisa melacak status individual setiap aktivitas
- Semua aktivitas untuk buku yang sama menampilkan status yang sama
- Tidak ada cara membedakan aktivitas yang sudah dikembalikan vs yang masih dipinjam

### Solusi Hybrid:
- **Field `status_aktivitas`**: Melacak status individual setiap aktivitas
- **JOIN dengan tabel `koleksi`**: Menampilkan status buku saat ini
- **Logika cerdas**: Update status koleksi berdasarkan aktivitas yang masih aktif

## Struktur Database

### Tabel Aktivitas
```sql
CREATE TABLE aktivitas (
    id_aktivitas VARCHAR(50) PRIMARY KEY NOT NULL,
    kode VARCHAR(50) NOT NULL,
    nrm VARCHAR(12) NOT NULL,
    tanggal_peminjaman DATE NOT NULL,
    jatuh_tempo DATE NOT NULL,
    status_aktivitas ENUM('Dipinjam', 'Dikembalikan') DEFAULT 'Dipinjam',
    FOREIGN KEY (kode) REFERENCES koleksi(kode),
    FOREIGN KEY (nrm) REFERENCES mahasiswa(nrm)
);
```

### Tabel Koleksi
```sql
CREATE TABLE koleksi (
    kode VARCHAR(50) PRIMARY KEY NOT NULL,
    -- ... field lainnya ...
    status ENUM('Tersedia', 'Dipinjam') DEFAULT 'Tersedia'
);
```

## Query yang Digunakan

### GET Aktivitas
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
- `COALESCE(a.status_aktivitas, 'Dipinjam')`: Jika field `status_aktivitas` NULL, gunakan 'Dipinjam' sebagai default
- `k.status AS status_buku`: Status buku saat ini dari tabel koleksi
- `a.status_aktivitas`: Status individual aktivitas

## Logika Update Status

### 1. Update Status Aktivitas
```typescript
// Update status_aktivitas di tabel aktivitas
await db.query(
  "UPDATE aktivitas SET status_aktivitas = ? WHERE id_aktivitas = ?",
  [status, id_aktivitas]
);
```

### 2. Logika Cerdas untuk Status Koleksi
```typescript
// Cek apakah ada aktivitas lain untuk buku yang sama yang masih 'Dipinjam'
const [otherAktivitas] = await db.query(
  "SELECT COUNT(*) as count FROM aktivitas WHERE kode = ? AND COALESCE(status_aktivitas, 'Dipinjam') = 'Dipinjam' AND id_aktivitas != ?",
  [kode, id_aktivitas]
);

const otherDipinjamCount = (otherAktivitas as any[])[0].count;

// Update status koleksi berdasarkan logika:
let newKoleksiStatus = '';
if (status === 'dikembalikan' && otherDipinjamCount === 0) {
  newKoleksiStatus = 'Tersedia';  // Semua aktivitas sudah dikembalikan
} else if (status === 'dipinjam') {
  newKoleksiStatus = 'Dipinjam';  // Ada aktivitas yang dipinjam
} else {
  newKoleksiStatus = currentKoleksiStatus;  // Tetap sama
}
```

## Keuntungan Solusi Hybrid

### 1. **Status Individual yang Akurat**
- Setiap aktivitas memiliki status sendiri di field `status_aktivitas`
- Tidak ada duplikasi status antar aktivitas

### 2. **Status Buku yang Real-time**
- Status buku diambil dari tabel `koleksi` dengan JOIN
- Menampilkan status buku saat ini (Tersedia/Dipinjam)

### 3. **Logika Bisnis yang Benar**
- Status koleksi diupdate berdasarkan aktivitas yang masih aktif
- Jika ada aktivitas yang masih 'Dipinjam', koleksi tetap 'Dipinjam'
- Jika semua aktivitas sudah 'Dikembalikan', koleksi menjadi 'Tersedia'

### 4. **Response yang Informatif**
```json
{
  "message": "Status aktivitas berhasil diupdate menjadi dikembalikan",
  "id_aktivitas": "ACT001",
  "kode_buku": "B001",
  "status_aktivitas_sebelumnya": "Dipinjam",
  "status_aktivitas_sekarang": "dikembalikan",
  "status_koleksi_sebelumnya": "Dipinjam",
  "status_koleksi_sekarang": "Tersedia",
  "data": {
    "id_aktivitas": "ACT001",
    "kode": "B001",
    "status_aktivitas": "dikembalikan",
    "status_buku": "Tersedia",
    // ... field lainnya
  }
}
```

## Skenario Testing

### Skenario 1: Aktivitas Tunggal
1. Buat aktivitas ACT001 untuk buku B001
2. **Expected**: 
   - `status_aktivitas` = "Dipinjam"
   - `status_buku` = "Dipinjam"
3. Update status menjadi "dikembalikan"
4. **Expected**: 
   - `status_aktivitas` = "dikembalikan"
   - `status_buku` = "Tersedia"

### Skenario 2: Aktivitas Ganda
1. Buat aktivitas ACT001 dan ACT002 untuk buku B001
2. **Expected**: 
   - ACT001: `status_aktivitas` = "Dipinjam"
   - ACT002: `status_aktivitas` = "Dipinjam"
   - `status_buku` = "Dipinjam"
3. Update ACT001 menjadi "dikembalikan"
4. **Expected**: 
   - ACT001: `status_aktivitas` = "dikembalikan"
   - ACT002: `status_aktivitas` = "Dipinjam"
   - `status_buku` = "Dipinjam" (karena ACT002 masih dipinjam)

### Skenario 3: Semua Dikembalikan
1. Update ACT002 menjadi "dikembalikan"
2. **Expected**: 
   - ACT001: `status_aktivitas` = "dikembalikan"
   - ACT002: `status_aktivitas` = "dikembalikan"
   - `status_buku` = "Tersedia" (karena semua sudah dikembalikan)

## Kesimpulan

Solusi hybrid ini memberikan:
1. **Status individual** yang akurat untuk setiap aktivitas
2. **Status buku** yang real-time dari tabel koleksi
3. **Logika bisnis** yang benar untuk mengelola status koleksi
4. **Tidak ada duplikasi status** antar aktivitas
5. **Response yang informatif** dengan detail lengkap

Sistem sekarang dapat menangani multiple aktivitas untuk buku yang sama tanpa saling menimpa status, sambil tetap menampilkan status buku yang akurat.
