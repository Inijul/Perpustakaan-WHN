"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
exports.createMahasiswa = exports.getMahasiswaByNrm = exports.getMahasiswa = void 0;
const database_1 = __importDefault(require("../config/database"));
// GET semua mahasiswa
const getMahasiswa = async (req, res) => {
    try {
        console.log("Fetching mahasiswa data...");
        const [rows] = await database_1.default.query(`
      SELECT nrm, nim, namam
      FROM mahasiswa
      ORDER BY namam
    `);
        console.log("Mahasiswa data fetched:", {
            count: rows.length,
            data: rows
        });
        // Pastikan response dikirim dengan header yang benar
        res.setHeader('Content-Type', 'application/json');
        res.setHeader('Access-Control-Allow-Origin', '*');
        res.json(rows);
    }
    catch (err) {
        console.error("Error in getMahasiswa:", err);
        res.status(500).json({ message: "Error mengambil data mahasiswa", error: err });
    }
};
exports.getMahasiswa = getMahasiswa;
// GET mahasiswa berdasarkan NRM
const getMahasiswaByNrm = async (req, res) => {
    try {
        const { nrm } = req.params;
        const [rows] = await database_1.default.query(`SELECT nrm, nim, namam FROM mahasiswa WHERE nrm = ?`, [nrm]);
        if (rows.length === 0) {
            return res.status(404).json({ message: "Mahasiswa tidak ditemukan" });
        }
        res.json(rows[0]);
    }
    catch (err) {
        res.status(500).json({ message: "Error mengambil mahasiswa", error: err });
    }
};
exports.getMahasiswaByNrm = getMahasiswaByNrm;
// CREATE mahasiswa
const createMahasiswa = async (req, res) => {
    try {
        const { nrm, nim, namam } = req.body;
        await database_1.default.query("INSERT INTO mahasiswa (nrm, nim, namam) VALUES (?, ?, ?)", [nrm, nim, namam]);
        res.status(201).json({ message: "Mahasiswa berhasil ditambahkan" });
    }
    catch (err) {
        res.status(500).json({ message: "Error menambah mahasiswa", error: err });
    }
};
exports.createMahasiswa = createMahasiswa;
