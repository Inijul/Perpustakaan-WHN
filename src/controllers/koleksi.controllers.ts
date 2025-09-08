import { Request, Response } from "express";
import db from "../config/database";

// GET semua koleksi dengan status berdasarkan aktivitas
export const getKoleksi = async (req: Request, res: Response) => {
  try {
    console.log('=== GET KOLEKSI REQUEST RECEIVED ===');
    
    // Query untuk mendapatkan koleksi dengan status berdasarkan aktivitas
    const [rows] = await db.query(`
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
    console.log('Number of records:', (rows as any[]).length);
    res.json(rows);
  } catch (err) {
    console.error('Error fetching koleksi:', err);
    res.status(500).json({ message: "Error mengambil koleksi", error: err });
  }
};

// GET koleksi by kode dengan status
export const getKoleksiByKode = async (req: Request, res: Response) => {
  const { kode } = req.params;
  try {
    const [rows] = await db.query(`
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
    
    if ((rows as any[]).length > 0) {
      res.json((rows as any[])[0]);
    } else {
      res.status(404).json({ message: "Koleksi tidak ditemukan" });
    }
  } catch (err) {
    res.status(500).json({ message: "Error mengambil koleksi", error: err });
  }
};

// CREATE koleksi
export const createKoleksi = async (req: Request, res: Response) => {
    console.log("Body data:", req.body); // DEBUG
  const { kode, kategori, topik, judul, penulis, penerbit, tahun_terbit, lokasi_rak, deskripsi, sampul } = req.body;
  
  // Jika kategori bukan buku, set topik menjadi "-"
  const topikValue = kategori === 'buku' ? topik : '-';
  
  try {
    await db.query(
      `INSERT INTO koleksi (kode, kategori, topik, judul, penulis, penerbit, tahun_terbit, lokasi_rak, deskripsi, sampul) 
       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
      [kode, kategori, topikValue, judul, penulis, penerbit, tahun_terbit, lokasi_rak, deskripsi, sampul]
    );
    res.status(201).json({ message: "Koleksi ditambahkan" });
  } catch (err) {
    res.status(500).json({ message: "Error menambah koleksi", error: err });
  }
};

// UPDATE koleksi
export const updateKoleksi = async (req: Request, res: Response) => {
  console.log('=== UPDATE KOLEKSI REQUEST RECEIVED ===');
  console.log('Method:', req.method);
  console.log('URL:', req.url);
  console.log('Headers:', req.headers);
  
  const { kode } = req.params;
  const { kategori, topik, judul, penulis, penerbit, tahun_terbit, lokasi_rak, deskripsi, sampul } = req.body;
  
  // Jika kategori bukan buku, set topik menjadi "-"
  const topikValue = kategori === 'buku' ? topik : '-';
  
  console.log('Kode:', kode);
  console.log('Request body:', req.body);
  console.log('Topik value:', topikValue);
  
  try {
    const [result] = await db.query(
      `UPDATE koleksi 
       SET kategori=?, topik=?, judul=?, penulis=?, penerbit=?, tahun_terbit=?, lokasi_rak=?, deskripsi=?, sampul=? 
       WHERE kode=?`,
      [kategori, topikValue, judul, penulis, penerbit, tahun_terbit, lokasi_rak, deskripsi, sampul, kode]
    );
    
    console.log('Update result:', result);
    
    // Verify the update by fetching the updated record
    const [updatedRows] = await db.query("SELECT * FROM koleksi WHERE kode = ?", [kode]);
    console.log('Updated record:', updatedRows);
    
    res.json({ message: "Koleksi diperbarui", updated: (updatedRows as any[])[0] });
  } catch (err) {
    console.error('Error updating koleksi:', err);
    res.status(500).json({ message: "Error update koleksi", error: err });
  }
};

// DELETE koleksi
export const deleteKoleksi = async (req: Request, res: Response) => {
  console.log('=== DELETE KOLEKSI REQUEST RECEIVED ===');
  console.log('Method:', req.method);
  console.log('URL:', req.url);
  console.log('Headers:', req.headers);
  
  const { kode } = req.params;
  console.log('Kode to delete:', kode);
  
  try {
    // First, check if the record exists
    const [existingRows] = await db.query("SELECT * FROM koleksi WHERE kode = ?", [kode]);
    console.log('Existing records found:', (existingRows as any[]).length);
    
    if ((existingRows as any[]).length === 0) {
      console.log('No record found with kode:', kode);
      return res.status(404).json({ message: "Koleksi tidak ditemukan", kode: kode });
    }
    
    // Perform the delete
    const [result] = await db.query("DELETE FROM koleksi WHERE kode = ?", [kode]);
    console.log('Delete result:', result);
    
    // Verify the deletion
    const [remainingRows] = await db.query("SELECT * FROM koleksi WHERE kode = ?", [kode]);
    console.log('Remaining records after delete:', (remainingRows as any[]).length);
    
    if ((remainingRows as any[]).length === 0) {
      console.log('Successfully deleted koleksi with kode:', kode);
      res.json({ message: "Koleksi berhasil dihapus", kode: kode });
    } else {
      console.log('Failed to delete koleksi with kode:', kode);
      res.status(500).json({ message: "Gagal menghapus koleksi", kode: kode });
    }
  } catch (err) {
    console.error('Error deleting koleksi:', err);
    res.status(500).json({ message: "Error hapus koleksi", error: err, kode: kode });
  }
};
