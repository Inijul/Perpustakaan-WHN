-- Script untuk debug status aktivitas
-- Jalankan script ini di DBeaver untuk memeriksa status

-- 1. Cek struktur tabel aktivitas
DESCRIBE aktivitas;

-- 2. Cek data aktivitas dengan status_aktivitas
SELECT id_aktivitas, kode, status_aktivitas, tanggal_peminjaman, jatuh_tempo 
FROM aktivitas 
ORDER BY tanggal_peminjaman DESC 
LIMIT 10;

-- 3. Cek apakah ada aktivitas dengan status_aktivitas NULL
SELECT COUNT(*) as total_null_status 
FROM aktivitas 
WHERE status_aktivitas IS NULL;

-- 4. Cek aktivitas yang baru saja diupdate (dalam 1 jam terakhir)
SELECT id_aktivitas, kode, status_aktivitas, tanggal_peminjaman, jatuh_tempo 
FROM aktivitas 
WHERE tanggal_peminjaman >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
ORDER BY tanggal_peminjaman DESC;
