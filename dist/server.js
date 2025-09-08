"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
const express_1 = __importDefault(require("express"));
const cors_1 = __importDefault(require("cors"));
const routes_1 = __importDefault(require("./routes"));
const app = (0, express_1.default)();
// Tambahkan ini untuk parsing JSON
app.use((0, cors_1.default)({
    origin: '*',
    methods: ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
    allowedHeaders: ['Content-Type', 'Authorization', 'Accept', 'X-CSRF-TOKEN', 'X-HTTP-Method-Override']
}));
app.use(express_1.default.json()); // <-- WAJIB
app.use(express_1.default.urlencoded({ extended: true })); // kalau butuh form data
app.use("/api", routes_1.default);
app.listen(5000, () => {
    console.log("Server running at http://localhost:5000");
});
