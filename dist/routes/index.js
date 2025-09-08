"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
const express_1 = __importDefault(require("express"));
const koleksi_controllers_1 = require("../controllers/koleksi.controllers");
const aktivitas_controllers_1 = require("../controllers/aktivitas.controllers");
const mahasiswa_controllers_1 = require("../controllers/mahasiswa.controllers");
const test_contollers_1 = require("../controllers/test.contollers");
const router = express_1.default.Router();
// Routes Koleksi
router.get("/koleksi", koleksi_controllers_1.getKoleksi);
router.get("/koleksi/:kode", koleksi_controllers_1.getKoleksiByKode);
router.post("/koleksi", koleksi_controllers_1.createKoleksi);
router.put("/koleksi/:kode", koleksi_controllers_1.updateKoleksi);
router.delete("/koleksi/:kode", koleksi_controllers_1.deleteKoleksi);
router.options("/koleksi/:kode", (req, res) => {
    res.header('Access-Control-Allow-Origin', '*');
    res.header('Access-Control-Allow-Methods', 'PUT, OPTIONS');
    res.header('Access-Control-Allow-Headers', 'Content-Type, Authorization, Accept');
    res.sendStatus(200);
});
// Routes Aktivitas
router.get("/aktivitas", aktivitas_controllers_1.getAktivitas);
router.get("/aktivitas/:id_aktivitas", aktivitas_controllers_1.getAktivitasById);
router.post("/aktivitas", aktivitas_controllers_1.createAktivitas);
router.put("/aktivitas/:id_aktivitas", aktivitas_controllers_1.updateAktivitas);
router.patch("/aktivitas/:id_aktivitas/status", aktivitas_controllers_1.updateStatusAktivitas);
router.delete("/aktivitas/:id_aktivitas", aktivitas_controllers_1.deleteAktivitas);
// Routes Mahasiswa
router.get("/mahasiswa", mahasiswa_controllers_1.getMahasiswa);
router.get("/mahasiswa/:nrm", mahasiswa_controllers_1.getMahasiswaByNrm);
router.post("/mahasiswa", mahasiswa_controllers_1.createMahasiswa);
// Test routes
router.get("/test-db", test_contollers_1.testDbConnection);
router.get("/test-mahasiswa", test_contollers_1.testMahasiswaData);
exports.default = router;
