-- Script untuk menambahkan field status_aktivitas jika belum ada
-- Jalankan script ini di DBeaver

-- 1. Cek apakah field sudah ada
SELECT COUNT(*) as field_exists
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_NAME = 'aktivitas' 
AND COLUMN_NAME = 'status_aktivitas';

-- 2. Jika field belum ada (hasil = 0), jalankan script di bawah ini:
-- ALTER TABLE aktivitas 
-- ADD COLUMN status_aktivitas ENUM('Dipinjam', 'Dikembalikan') DEFAULT 'Dipinjam' 
-- AFTER jatuh_tempo;

-- 3. Update semua aktivitas yang sudah ada
-- UPDATE aktivitas SET status_aktivitas = 'Dipinjam' WHERE status_aktivitas IS NULL;

-- 4. Verifikasi
-- SELECT id_aktivitas, kode, status_aktivitas, tanggal_peminjaman 
-- FROM aktivitas 
-- ORDER BY tanggal_peminjaman DESC 
-- LIMIT 10;
