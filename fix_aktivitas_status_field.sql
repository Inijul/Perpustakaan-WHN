-- Script untuk memperbaiki masalah status aktivitas
-- Jalankan script ini di DBeaver untuk menambahkan field status_aktivitas

-- 1. Cek apakah field status_aktivitas sudah ada
SELECT COUNT(*) as field_exists
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_NAME = 'aktivitas' 
AND COLUMN_NAME = 'status_aktivitas';

-- 2. Jika field belum ada (hasil = 0), jalankan script di bawah ini:
ALTER TABLE aktivitas 
ADD COLUMN status_aktivitas ENUM('Dipinjam', 'Dikembalikan') DEFAULT 'Dipinjam' 
AFTER jatuh_tempo;

-- 3. Update semua aktivitas yang sudah ada dengan status 'Dipinjam' sebagai default
UPDATE aktivitas SET status_aktivitas = 'Dipinjam' WHERE status_aktivitas IS NULL;

-- 4. Verifikasi perubahan
SELECT id_aktivitas, kode, status_aktivitas, tanggal_peminjaman, jatuh_tempo 
FROM aktivitas 
ORDER BY tanggal_peminjaman DESC 
LIMIT 10;

-- 5. Cek struktur tabel setelah perubahan
DESCRIBE aktivitas;
