-- Script untuk memperbaiki masalah status aktivitas
-- Jalankan script ini di DBeaver

-- 1. Tambahkan kolom status_aktivitas ke tabel aktivitas
ALTER TABLE aktivitas 
ADD COLUMN status_aktivitas ENUM('Dipinjam', 'Dikembalikan') DEFAULT 'Dipinjam' 
AFTER jatuh_tempo;

-- 2. Update semua aktivitas yang sudah ada dengan status 'Dipinjam' sebagai default
UPDATE aktivitas SET status_aktivitas = 'Dipinjam' WHERE status_aktivitas IS NULL;

-- 3. Verifikasi perubahan
SELECT id_aktivitas, kode, status_aktivitas, tanggal_peminjaman, jatuh_tempo 
FROM aktivitas 
ORDER BY tanggal_peminjaman DESC 
LIMIT 10;
