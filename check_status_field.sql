-- Script untuk memeriksa field status_aktivitas
-- Jalankan script ini di DBeaver

-- 1. Cek apakah field status_aktivitas sudah ada
SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_NAME = 'aktivitas' 
AND COLUMN_NAME = 'status_aktivitas';

-- 2. Cek data aktivitas terbaru
SELECT id_aktivitas, kode, status_aktivitas, tanggal_peminjaman, jatuh_tempo 
FROM aktivitas 
ORDER BY tanggal_peminjaman DESC 
LIMIT 5;

-- 3. Test update manual satu aktivitas
-- Ganti ID_AKTIVITAS dengan ID yang ingin Anda test
-- UPDATE aktivitas SET status_aktivitas = 'Dikembalikan' WHERE id_aktivitas = 'ID_AKTIVITAS_YANG_INGIN_TEST';

-- 4. Cek setelah update
-- SELECT id_aktivitas, kode, status_aktivitas FROM aktivitas WHERE id_aktivitas = 'ID_AKTIVITAS_YANG_INGIN_TEST';
