# Perbaikan Status Aktivitas - Solusi yang Benar

## Masalah yang Ditemukan

Berdasarkan struktur database yang benar:
- Tabel `aktivitas` TIDAK memiliki field `status_aktivitas`
- Status aktivitas diambil dari tabel `koleksi` field `status`
- Kode backend salah karena mencoba menggunakan field yang tidak ada

## Struktur Database yang Benar

```sql
-- Tabel koleksi
CREATE TABLE koleksi (
    kode varchar(50) primary key not null,
    kategori enum('buku','jurnal','skripsi'),
    topik varchar(100),
    judul varchar(200) not null,
    penulis varchar(100),
    penerbit varchar(100),
    tahun_terbit varchar(4),
    lokasi_rak varchar(50) not null,
    deskripsi varchar(255),
    sampul varchar(255),
    status ENUM('Tersedia', 'Dipinjam') DEFAULT 'Tersedia'  -- Status di sini
);

-- Tabel aktivitas
CREATE TABLE aktivitas (
    id_aktivitas varchar(50) primary key not null,
    kode varchar(50) not null,
    nrm varchar(12) not null,
    tanggal_peminjaman date not null,
    jatuh_tempo date not null,
    foreign key (kode) references koleksi(kode),
    foreign key (nrm) references mahasiswa(nrm)
    -- TIDAK ADA field status_aktivitas
);
```

## Perbaikan yang Telah Dilakukan

### 1. Query GET Aktivitas
**Sebelum (SALAH):**
```sql
COALESCE(a.status_aktivitas, 'Dipinjam') AS status_aktivitas
```

**Sesudah (BENAR):**
```sql
k.status AS status_aktivitas
```

### 2. Query GET Aktivitas by ID
**Sebelum (SALAH):**
```sql
COALESCE(a.status_aktivitas, 'Dipinjam') AS status_aktivitas
```

**Sesudah (BENAR):**
```sql
k.status AS status_aktivitas
```

### 3. Query CREATE Aktivitas
**Sebelum (SALAH):**
```sql
INSERT INTO aktivitas (id_aktivitas, kode, nrm, tanggal_peminjaman, jatuh_tempo, status_aktivitas) VALUES (?, ?, ?, ?, ?, '?')
```

**Sesudah (BENAR):**
```sql
INSERT INTO aktivitas (id_aktivitas, kode, nrm, tanggal_peminjaman, jatuh_tempo) VALUES (?, ?, ?, ?, ?)
```

### 4. Query UPDATE Status Aktivitas
**Sebelum (SALAH):**
```sql
-- Mencoba update field yang tidak ada
UPDATE aktivitas SET status_aktivitas = ? WHERE id_aktivitas = ?
```

**Sesudah (BENAR):**
```sql
-- Hanya update status di tabel koleksi
UPDATE koleksi SET status = ? WHERE kode = ?
```

### 5. Validasi Status
**Sebelum (SALAH):**
```sql
-- Mencoba ambil status dari field yang tidak ada
SELECT kode, COALESCE(status_aktivitas, 'Dipinjam') as current_status FROM aktivitas
```

**Sesudah (BENAR):**
```sql
-- Ambil status dari tabel koleksi
SELECT a.kode, k.status as current_status FROM aktivitas a JOIN koleksi k ON a.kode = k.kode
```

## Cara Kerja Setelah Perbaikan

1. **Peminjaman**: 
   - Data aktivitas disimpan di tabel `aktivitas`
   - Status koleksi diupdate menjadi 'Dipinjam' di tabel `koleksi`

2. **Pengembalian**:
   - Status koleksi diupdate menjadi 'Tersedia' di tabel `koleksi`
   - Data aktivitas tetap ada untuk riwayat

3. **Tampilan Status**:
   - Status aktivitas diambil dari `k.status` (tabel koleksi)
   - Jika status = 'Dipinjam' → tampil "Dipinjam"
   - Jika status = 'Tersedia' → tampil "Dikembalikan"

## Testing

Setelah perbaikan ini:

1. Buka halaman aktivitas
2. Pilih aktivitas dengan status "Dipinjam"
3. Ubah status menjadi "Dikembalikan"
4. Refresh halaman
5. Status seharusnya tetap "Dikembalikan" karena tersimpan di tabel `koleksi`

## File yang Diperbaiki

- `src/controllers/aktivitas.controllers.ts` - Semua query telah diperbaiki

## Catatan Penting

- **TIDAK PERLU** menambahkan field `status_aktivitas` ke tabel `aktivitas`
- Status aktivitas diambil dari tabel `koleksi` field `status`
- Perbaikan ini sesuai dengan struktur database yang sudah ada
- Kode backend sekarang konsisten dengan struktur database
