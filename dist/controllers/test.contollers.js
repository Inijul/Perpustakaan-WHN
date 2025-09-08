"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
exports.testMahasiswaData = exports.testDbConnection = void 0;
const database_1 = __importDefault(require("../config/database"));
const testDbConnection = async (req, res) => {
    try {
        const [result] = await database_1.default.query("SELECT 1 as test");
        res.json({ message: "Database connection successful", result });
    }
    catch (err) {
        res.status(500).json({ message: "Database connection failed", error: err });
    }
};
exports.testDbConnection = testDbConnection;
// Test endpoint untuk mengecek data mahasiswa
const testMahasiswaData = async (req, res) => {
    try {
        console.log("Testing mahasiswa data...");
        // Cek apakah tabel mahasiswa ada
        const [tableCheck] = await database_1.default.query(`
      SELECT COUNT(*) as count 
      FROM information_schema.tables 
      WHERE table_schema = DATABASE() 
      AND table_name = 'mahasiswa'
    `);
        // Hitung jumlah data mahasiswa
        const [countResult] = await database_1.default.query("SELECT COUNT(*) as total FROM mahasiswa");
        // Ambil semua data mahasiswa
        const [mahasiswaData] = await database_1.default.query("SELECT * FROM mahasiswa LIMIT 5");
        res.json({
            tableExists: tableCheck[0].count > 0,
            totalMahasiswa: countResult[0].total,
            sampleData: mahasiswaData
        });
    }
    catch (err) {
        console.error("Error testing mahasiswa data:", err);
        res.status(500).json({ message: "Error testing mahasiswa data", error: err });
    }
};
exports.testMahasiswaData = testMahasiswaData;
