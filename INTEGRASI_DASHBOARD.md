# Integrasi Dashboard - Perpustakaan WHN

## Deskripsi
Integrasi dashboard untuk menampilkan total buku, jurnal, dokumen skripsi, dan buku yang dipinjam secara dinamis.

## Perubahan yang Dilakukan

### 1. Membuat DashboardController
- **File**: `frontend/app/Http/Controllers/DashboardController.php`
- **Fungsi**: 
  - Menghitung total buku berdasarkan kategori
  - Menghitung total jurnal berdasarkan kategori
  - Menghitung total dokumen skripsi berdasarkan kategori
  - Menghitung total buku yang dipinjam berdasarkan status aktivitas

### 2. Mengupdate Routes
- **File**: `frontend/routes/web.php`
- **Perubahan**: Mengubah route `/` untuk menggunakan `DashboardController@index`

### 3. Mengupdate View Dashboard
- **File**: `frontend/resources/views/dashboard.blade.php`
- **Perubahan**:
  - Menambahkan import Carbon untuk format tanggal
  - Mengganti angka statis dengan variabel dinamis:
    - Total Buku: `{{ $totalBuku ?? 0 }}`
    - Total Jurnal: `{{ $totalJurnal ?? 0 }}`
    - Total Dokumen Skripsi: `{{ $totalSkripsi ?? 0 }}`
    - Total Status Keluar: `{{ $totalDipinjam ?? 0 }}`
     - Tabel daftar pengajuan peminjaman tetap menggunakan data statis (untuk pengajuan yang akan diajukan mahasiswa)

## Cara Kerja

### Perhitungan Total
1. **Total Buku**: Menghitung koleksi dengan kategori "buku"
2. **Total Jurnal**: Menghitung koleksi dengan kategori "jurnal"
3. **Total Dokumen Skripsi**: Menghitung koleksi dengan kategori "skripsi" atau "dokumen skripsi"
4. **Total Status Keluar**: Menghitung aktivitas dengan status "dipinjam"



## API yang Digunakan
- `GET /api/koleksi` - Untuk data koleksi
- `GET /api/aktivitas` - Untuk data aktivitas peminjaman

## Error Handling
- Jika API tidak tersedia, akan menampilkan nilai 0
- Jika terjadi error, akan log error dan set nilai default
- Menggunakan null coalescing operator (`??`) untuk fallback

## Testing
1. Pastikan backend berjalan di port 5000
2. Pastikan frontend berjalan di port 8000
3. Akses dashboard di `http://localhost:8000`
4. Periksa apakah card menampilkan total yang benar
5. Periksa apakah tabel daftar pengajuan peminjaman menampilkan data statis

## Catatan
- Data koleksi diambil secara real-time dari backend API
- Cache diterapkan untuk performa (60 detik)
- Tabel daftar pengajuan peminjaman menggunakan data statis untuk menampilkan contoh pengajuan yang akan diajukan mahasiswa
- Status aktivitas ditampilkan dengan warna yang berbeda (hijau untuk dipinjam, merah untuk dikembalikan)
