# Solusi Duplikasi Status Aktivitas

## Masalah yang Ditemukan

**Masalah Utama:** Sistem mengalami duplikasi status ketika ada beberapa aktivitas untuk buku yang sama. Ketika satu aktivitas dikembalikan, status koleksi berubah menjadi "Tersedia", tetapi ketika ada aktivitas baru untuk buku yang sama, status koleksi berubah menjadi "Dipinjam" lagi, yang mempengaruhi aktivitas yang sudah dikembalikan.

**Contoh Skenario Masalah:**
1. Aktivitas A: Buku B001 dipinjam (status koleksi = "Dipinjam")
2. Aktivitas B: Buku B001 dikembalikan (status koleksi = "Tersedia") 
3. Aktivitas C: Buku B001 dipinjam lagi (status koleksi = "Dipinjam")
4. **Masalah**: Aktivitas A dan B seharusnya tetap "Dipinjam" dan "Dikembalikan", tapi status koleksi saling menimpa

## Akar Masalah

1. **Tabel `aktivitas` tidak memiliki field `status_aktivitas`**
2. **Status diambil dari tabel `koleksi` field `status`**
3. **Satu buku (kode) bisa memiliki banyak aktivitas, tapi status koleksi hanya satu**
4. **Sistem mengupdate status koleksi berdasarkan `kode` buku, bukan `id_aktivitas`**

## Solusi yang Diterapkan

### 1. Menambahkan Field `status_aktivitas` ke Tabel `aktivitas`

**Script SQL:** `fix_aktivitas_status_field.sql`
```sql
-- Tambahkan field status_aktivitas
ALTER TABLE aktivitas 
ADD COLUMN status_aktivitas ENUM('Dipinjam', 'Dikembalikan') DEFAULT 'Dipinjam' 
AFTER jatuh_tempo;

-- Update semua aktivitas yang sudah ada
UPDATE aktivitas SET status_aktivitas = 'Dipinjam' WHERE status_aktivitas IS NULL;
```

### 2. Perubahan Kode Controller

#### A. **Query GET Aktivitas**
**Sebelum:**
```sql
SELECT a.id_aktivitas, a.kode, a.nrm, a.tanggal_peminjaman, a.jatuh_tempo,
       m.namam AS nama_mahasiswa, k.judul AS judul_buku, k.status AS status_buku
```

**Sesudah:**
```sql
SELECT a.id_aktivitas, a.kode, a.nrm, a.tanggal_peminjaman, a.jatuh_tempo,
       a.status_aktivitas, m.namam AS nama_mahasiswa, k.judul AS judul_buku, k.status AS status_buku
```

#### B. **Query CREATE Aktivitas**
**Sebelum:**
```sql
INSERT INTO aktivitas (id_aktivitas, kode, nrm, tanggal_peminjaman, jatuh_tempo) VALUES (?, ?, ?, ?, ?)
```

**Sesudah:**
```sql
INSERT INTO aktivitas (id_aktivitas, kode, nrm, tanggal_peminjaman, jatuh_tempo, status_aktivitas) VALUES (?, ?, ?, ?, ?, 'Dipinjam')
```

#### C. **Logika UPDATE Status Aktivitas**

**Logika Baru:**
1. **Update `status_aktivitas`** di tabel `aktivitas` berdasarkan `id_aktivitas`
2. **Logika cerdas untuk status koleksi:**
   - Cek apakah ada aktivitas lain untuk buku yang sama yang masih 'Dipinjam'
   - Jika ada aktivitas lain yang 'Dipinjam', koleksi tetap 'Dipinjam'
   - Jika tidak ada aktivitas lain yang 'Dipinjam', koleksi menjadi 'Tersedia'

**Kode:**
```typescript
// Update status_aktivitas di tabel aktivitas
await db.query(
  "UPDATE aktivitas SET status_aktivitas = ? WHERE id_aktivitas = ?",
  [status, id_aktivitas]
);

// Cek apakah ada aktivitas lain untuk buku yang sama yang masih 'Dipinjam'
const [otherAktivitas] = await db.query(
  "SELECT COUNT(*) as count FROM aktivitas WHERE kode = ? AND status_aktivitas = 'Dipinjam' AND id_aktivitas != ?",
  [kode, id_aktivitas]
);

// Update status koleksi berdasarkan logika cerdas
let newKoleksiStatus = '';
if (status === 'dikembalikan' && otherDipinjamCount === 0) {
  newKoleksiStatus = 'Tersedia';
} else if (status === 'dipinjam') {
  newKoleksiStatus = 'Dipinjam';
} else {
  newKoleksiStatus = currentKoleksiStatus;
}
```

## Keuntungan Solusi

### 1. **Tidak Ada Duplikasi Status**
- Setiap aktivitas memiliki status sendiri di field `status_aktivitas`
- Status aktivitas tidak saling menimpa antar aktivitas

### 2. **Logika Bisnis yang Benar**
- Status koleksi diupdate berdasarkan aktivitas yang masih aktif
- Jika ada aktivitas yang masih 'Dipinjam', koleksi tetap 'Dipinjam'
- Jika semua aktivitas sudah 'Dikembalikan', koleksi menjadi 'Tersedia'

### 3. **Konsistensi dengan `id_aktivitas`**
- Semua operasi menggunakan `id_aktivitas` sebagai pengidentifikasi utama
- Status diupdate berdasarkan `id_aktivitas`, bukan `kode` buku

### 4. **Response yang Informatif**
- Client mendapat informasi lengkap tentang perubahan status
- Menampilkan status aktivitas dan status koleksi secara terpisah

## Cara Penggunaan

### 1. **Jalankan Script SQL**
```bash
# Jalankan di DBeaver
fix_aktivitas_status_field.sql
```

### 2. **Membuat Aktivitas Baru**
```javascript
POST /api/aktivitas
{
  "id_aktivitas": "ACT001",
  "kode": "B001", 
  "nrm": "12345678",
  "tanggal_peminjaman": "2024-01-15",
  "jatuh_tempo": "2024-01-22"
}
// Response: status_aktivitas = "Dipinjam" (default)
```

### 3. **Update Status Aktivitas**
```javascript
PUT /api/aktivitas/ACT001/status
{
  "status": "dikembalikan"
}
// Response: 
// - status_aktivitas = "dikembalikan"
// - status_koleksi = "Tersedia" (jika tidak ada aktivitas lain yang dipinjam)
```

## Testing Skenario

### Skenario 1: Aktivitas Tunggal
1. Buat aktivitas ACT001 untuk buku B001
2. Update status menjadi "dikembalikan"
3. **Expected**: status_aktivitas = "dikembalikan", status_koleksi = "Tersedia"

### Skenario 2: Aktivitas Ganda
1. Buat aktivitas ACT001 untuk buku B001
2. Buat aktivitas ACT002 untuk buku B001
3. Update ACT001 menjadi "dikembalikan"
4. **Expected**: 
   - ACT001: status_aktivitas = "dikembalikan"
   - ACT002: status_aktivitas = "Dipinjam"
   - status_koleksi = "Dipinjam" (karena ACT002 masih dipinjam)

### Skenario 3: Aktivitas Ganda - Semua Dikembalikan
1. Buat aktivitas ACT001 dan ACT002 untuk buku B001
2. Update ACT001 menjadi "dikembalikan"
3. Update ACT002 menjadi "dikembalikan"
4. **Expected**: 
   - ACT001: status_aktivitas = "dikembalikan"
   - ACT002: status_aktivitas = "dikembalikan"
   - status_koleksi = "Tersedia" (karena semua sudah dikembalikan)

## Kesimpulan

Solusi ini mengatasi masalah duplikasi status dengan:
1. **Menambahkan field `status_aktivitas`** ke tabel `aktivitas`
2. **Menggunakan `id_aktivitas`** sebagai pengidentifikasi utama
3. **Logika cerdas** untuk update status koleksi berdasarkan aktivitas yang masih aktif
4. **Tidak ada duplikasi status** antar aktivitas

Sistem sekarang dapat menangani multiple aktivitas untuk buku yang sama tanpa saling menimpa status.
