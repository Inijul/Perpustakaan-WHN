"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
exports.deleteKoleksi = exports.updateKoleksi = exports.createKoleksi = exports.getKoleksiByKode = exports.getKoleksi = void 0;
const database_1 = __importDefault(require("../config/database"));
// GET semua koleksi dengan status berdasarkan aktivitas
const getKoleksi = async (req, res) => {
    try {
        console.log('=== GET KOLEKSI REQUEST RECEIVED ===');
        // Query untuk mendapatkan koleksi dengan status berdasarkan aktivitas
        const [rows] = await database_1.default.query(`
      SELECT 
        k.*,
        CASE 
          WHEN EXISTS (
            SELECT 1 FROM aktivitas a 
            WHERE a.kode = k.kode 
            AND a.status = 'dipinjam'
          ) THEN 'Dipinjam'
          ELSE 'Tersedia'
        END as status
      FROM koleksi k
      ORDER BY k.kode
    `);
        console.log('Koleksi data fetched with status:', rows);
        console.log('Number of records:', rows.length);
        res.json(rows);
    }
    catch (err) {
        console.error('Error fetching koleksi:', err);
        res.status(500).json({ message: "Error mengambil koleksi", error: err });
    }
};
exports.getKoleksi = getKoleksi;
// GET koleksi by kode dengan status
const getKoleksiByKode = async (req, res) => {
    const { kode } = req.params;
    try {
        const [rows] = await database_1.default.query(`
      SELECT 
        k.*,
        CASE 
          WHEN EXISTS (
            SELECT 1 FROM aktivitas a 
            WHERE a.kode = k.kode 
            AND a.status = 'dipinjam'
          ) THEN 'Dipinjam'
          ELSE 'Tersedia'
        END as status
      FROM koleksi k
      WHERE k.kode = ?
    `, [kode]);
        if (rows.length > 0) {
            res.json(rows[0]);
        }
        else {
            res.status(404).json({ message: "Koleksi tidak ditemukan" });
        }
    }
    catch (err) {
        res.status(500).json({ message: "Error mengambil koleksi", error: err });
    }
};
exports.getKoleksiByKode = getKoleksiByKode;
// CREATE koleksi
const createKoleksi = async (req, res) => {
    console.log("Body data:", req.body); // DEBUG
    const { kode, kategori, topik, judul, penulis, penerbit, tahun_terbit, lokasi_rak, deskripsi, tautan, sampul } = req.body;
    // Jika kategori bukan buku, set topik menjadi "-"
    const topikValue = kategori === 'buku' ? topik : '-';
    try {
        await database_1.default.query(`INSERT INTO koleksi (kode, kategori, topik, judul, penulis, penerbit, tahun_terbit, lokasi_rak, deskripsi, tautan, sampul) 
       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`, [kode, kategori, topikValue, judul, penulis, penerbit, tahun_terbit, lokasi_rak, deskripsi, tautan, sampul]);
        res.status(201).json({ message: "Koleksi ditambahkan" });
    }
    catch (err) {
        res.status(500).json({ message: "Error menambah koleksi", error: err });
    }
};
exports.createKoleksi = createKoleksi;
// UPDATE koleksi
const updateKoleksi = async (req, res) => {
    console.log('=== UPDATE KOLEKSI REQUEST RECEIVED ===');
    console.log('Method:', req.method);
    console.log('URL:', req.url);
    console.log('Headers:', req.headers);
    const { kode } = req.params;
    const { kategori, topik, judul, penulis, penerbit, tahun_terbit, lokasi_rak, deskripsi, tautan, sampul } = req.body;
    // Jika kategori bukan buku, set topik menjadi "-"
    const topikValue = kategori === 'buku' ? topik : '-';
    console.log('Kode:', kode);
    console.log('Request body:', req.body);
    console.log('Topik value:', topikValue);
    try {
        const [result] = await database_1.default.query(`UPDATE koleksi 
       SET kategori=?, topik=?, judul=?, penulis=?, penerbit=?, tahun_terbit=?, lokasi_rak=?, deskripsi=?, tautan=?, sampul=? 
       WHERE kode=?`, [kategori, topikValue, judul, penulis, penerbit, tahun_terbit, lokasi_rak, deskripsi, tautan, sampul, kode]);
        console.log('Update result:', result);
        // Verify the update by fetching the updated record
        const [updatedRows] = await database_1.default.query("SELECT * FROM koleksi WHERE kode = ?", [kode]);
        console.log('Updated record:', updatedRows);
        res.json({ message: "Koleksi diperbarui", updated: updatedRows[0] });
    }
    catch (err) {
        console.error('Error updating koleksi:', err);
        res.status(500).json({ message: "Error update koleksi", error: err });
    }
};
exports.updateKoleksi = updateKoleksi;
// DELETE koleksi
const deleteKoleksi = async (req, res) => {
    console.log('=== DELETE KOLEKSI REQUEST RECEIVED ===');
    console.log('Method:', req.method);
    console.log('URL:', req.url);
    console.log('Headers:', req.headers);
    const { kode } = req.params;
    console.log('Kode to delete:', kode);
    try {
        // First, check if the record exists
        const [existingRows] = await database_1.default.query("SELECT * FROM koleksi WHERE kode = ?", [kode]);
        console.log('Existing records found:', existingRows.length);
        if (existingRows.length === 0) {
            console.log('No record found with kode:', kode);
            return res.status(404).json({ message: "Koleksi tidak ditemukan", kode: kode });
        }
        // Perform the delete
        const [result] = await database_1.default.query("DELETE FROM koleksi WHERE kode = ?", [kode]);
        console.log('Delete result:', result);
        // Verify the deletion
        const [remainingRows] = await database_1.default.query("SELECT * FROM koleksi WHERE kode = ?", [kode]);
        console.log('Remaining records after delete:', remainingRows.length);
        if (remainingRows.length === 0) {
            console.log('Successfully deleted koleksi with kode:', kode);
            res.json({ message: "Koleksi berhasil dihapus", kode: kode });
        }
        else {
            console.log('Failed to delete koleksi with kode:', kode);
            res.status(500).json({ message: "Gagal menghapus koleksi", kode: kode });
        }
    }
    catch (err) {
        console.error('Error deleting koleksi:', err);
        res.status(500).json({ message: "Error hapus koleksi", error: err, kode: kode });
    }
};
exports.deleteKoleksi = deleteKoleksi;
