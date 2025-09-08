# PERUBAHAN STRUKTUR DATABASE DAN KODE

## Overview
Dokumen ini menjelaskan perubahan yang telah dilakukan untuk menyesuaikan kode program dengan struktur database baru dimana:
- Tabel `aktivitas` TIDAK memiliki kolom `status`
- Status diambil dari tabel `koleksi` kolom `status`
- **Menggunakan DBeaver untuk manajemen database (TIDAK ada migration Laravel)**

## Struktur Database Baru

### Tabel Koleksi
```sql
CREATE TABLE koleksi (
    kode VARCHAR(50) PRIMARY KEY NOT NULL,
    kategori ENUM('buku','jurnal','skripsi'),
    topik VARCHAR(100),
    judul VARCHAR(200) NOT NULL,
    penulis VARCHAR(100),
    penerbit VARCHAR(100),
    tahun_terbit VARCHAR(4),
    lokasi_rak VARCHAR(50) NOT NULL,
    deskripsi VARCHAR(255),
    sampul VARCHAR(255),
    status ENUM('Tersedia', 'Dipinjam') DEFAULT 'Tersedia'
);
```

### Tabel Aktivitas
```sql
CREATE TABLE aktivitas (
    id_aktivitas VARCHAR(50) PRIMARY KEY NOT NULL,
    kode VARCHAR(50) NOT NULL,
    nrm VARCHAR(12) NOT NULL,
    tanggal_peminjaman DATE NOT NULL,
    jatuh_tempo DATE NOT NULL,
    FOREIGN KEY (kode) REFERENCES koleksi(kode),
    FOREIGN KEY (nrm) REFERENCES mahasiswa(nrm)
);
```

## Perubahan Kode yang Telah Dilakukan

### 1. Migration Files
- ✅ **SEMUA DIHAPUS**: Karena menggunakan DBeaver untuk manajemen database
- ✅ **Dihapus**: `2025_01_15_000000_create_aktivitas_table.php`
- ✅ **Dihapus**: `2025_08_01_072052_create_koleksis_table.php`
- ✅ **Dihapus**: `2025_01_20_000000_update_mahasiswa_table.sql`
- ✅ **Dihapus**: `0001_01_01_000000_create_users_table.php`
- ✅ **Dihapus**: `0001_01_01_000001_create_cache_table.php`
- ✅ **Dihapus**: `0001_01_01_000002_create_jobs_table.php`
- ✅ **Dihapus**: `2025_07_23_144258_create_personal_access_tokens_table.php`

### 2. Backend Controllers

#### Aktivitas Controller
- ✅ **Query getAktivitas**: Status diambil langsung dari `k.status` bukan CASE WHEN
- ✅ **Query getAktivitasById**: Status diambil dari tabel koleksi
- ✅ **Method updateStatusAktivitas**: Update status koleksi berdasarkan status aktivitas
- ✅ **Method createAktivitas**: Tidak mengupdate status koleksi saat peminjaman

#### Koleksi Controller
- ✅ **Method createKoleksi**: Tambah status default 'Tersedia' saat insert

### 3. Frontend Controllers

#### Aktivitas Controller
- ✅ **Method updateStatus**: Gunakan API backend, hapus DB facade
- ✅ **Import**: Hapus `use Illuminate\Support\Facades\DB`

#### Koleksi Controller
- ✅ **Method store**: Hapus field status dari data yang dikirim ke backend

### 4. Routes
- ✅ **Backend**: Tambah route `PATCH /aktivitas/:id_aktivitas/status` untuk update status

### 5. File SQL yang Dihapus
- ✅ **Dihapus**: `create_aktivitas_table.sql`
- ✅ **Dihapus**: `remove_status_column.sql`
- ✅ **Dihapus**: `check_database.sql`

## Logika Status

### Saat Peminjaman
1. User membuat aktivitas peminjaman
2. Status koleksi TIDAK diupdate (tetap 'Tersedia')
3. Status koleksi akan diupdate menjadi 'Dipinjam' saat user mengklik tombol "Dipinjam"

### Saat Pengembalian
1. User mengklik tombol "Dikembalikan"
2. Status koleksi diupdate menjadi 'Tersedia'

### Saat Melihat Aktivitas
1. Status yang ditampilkan adalah status dari tabel koleksi
2. Bukan status dari tabel aktivitas (karena tidak ada)

## Cara Kerja

### Flow Update Status
```
Frontend (updateStatus) 
    ↓
Backend API (PATCH /aktivitas/:id/status)
    ↓
Backend Controller (updateStatusAktivitas)
    ↓
Update tabel koleksi.status
    ↓
Return response dengan data terbaru
```

### Flow Create Aktivitas
```
Frontend (store)
    ↓
Backend API (POST /aktivitas)
    ↓
Backend Controller (createAktivitas)
    ↓
Insert ke tabel aktivitas (tanpa update status koleksi)
    ↓
Return success message
```

## Testing

### Test Update Status
1. Buat aktivitas peminjaman baru
2. Klik tombol "Dipinjam" - status koleksi harus berubah ke 'Dipinjam'
3. Klik tombol "Dikembalikan" - status koleksi harus berubah ke 'Tersedia'

### Test Create Aktivitas
1. Buat aktivitas peminjaman baru
2. Status koleksi tidak boleh berubah otomatis
3. Status koleksi hanya berubah saat user mengklik tombol status

## Catatan Penting

1. **Status diambil dari tabel koleksi**, bukan dari tabel aktivitas
2. **Tabel aktivitas tidak memiliki kolom status**
3. **TIDAK ADA migration Laravel** - semua tabel dibuat manual di DBeaver
4. **Semua operasi database** dilakukan melalui backend API
5. **Frontend tidak mengakses database langsung** (tidak ada DB facade)
6. **Struktur database dikelola sepenuhnya melalui DBeaver**

## Troubleshooting

### Jika Status Tidak Berubah
1. Cek apakah route `/aktivitas/:id/status` tersedia
2. Cek apakah backend berjalan di port 5000
3. Cek log backend untuk error
4. Cek apakah tabel koleksi memiliki kolom status

### Jika Error "Status tidak valid"
1. Pastikan status yang dikirim adalah 'dipinjam' atau 'dikembalikan'
2. Cek validasi di backend controller

### Jika Error "Aktivitas tidak ditemukan"
1. Cek apakah ID aktivitas valid
2. Cek apakah data ada di database
3. Cek foreign key constraints

### Jika Error "Table doesn't exist"
1. Pastikan tabel sudah dibuat di DBeaver
2. Cek nama tabel dan kolom sesuai dengan yang ada di kode
3. Pastikan backend bisa terhubung ke database
