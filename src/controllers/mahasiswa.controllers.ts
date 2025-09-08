import { Request, Response } from "express";
import db from "../config/database";

// GET semua mahasiswa
export const getMahasiswa = async (req: Request, res: Response) => {
  try {
    console.log("Fetching mahasiswa data...");
    
    const [rows] = await db.query(`
      SELECT nrm, nim, namam
      FROM mahasiswa
      ORDER BY namam
    `);
    
    console.log("Mahasiswa data fetched:", {
      count: (rows as any[]).length,
      data: rows
    });
    
    // Pastikan response dikirim dengan header yang benar
    res.setHeader('Content-Type', 'application/json');
    res.setHeader('Access-Control-Allow-Origin', '*');
    res.json(rows);
  } catch (err) {
    console.error("Error in getMahasiswa:", err);
    res.status(500).json({ message: "Error mengambil data mahasiswa", error: err });
  }
};

// GET mahasiswa berdasarkan NRM
export const getMahasiswaByNrm = async (req: Request, res: Response) => {
  try {
    const { nrm } = req.params;
    const [rows] = await db.query(
      `SELECT nrm, nim, namam FROM mahasiswa WHERE nrm = ?`,
      [nrm]
    );

    if ((rows as any[]).length === 0) {
      return res.status(404).json({ message: "Mahasiswa tidak ditemukan" });
    }

    res.json((rows as any[])[0]);
  } catch (err) {
    res.status(500).json({ message: "Error mengambil mahasiswa", error: err });
  }
};

// CREATE mahasiswa
export const createMahasiswa = async (req: Request, res: Response) => {
  try {
    const { nrm, nim, namam } = req.body;

    await db.query(
      "INSERT INTO mahasiswa (nrm, nim, namam) VALUES (?, ?, ?)",
      [nrm, nim, namam]
    );

    res.status(201).json({ message: "Mahasiswa berhasil ditambahkan" });
  } catch (err) {
    res.status(500).json({ message: "Error menambah mahasiswa", error: err });
  }
};
