import express from "express";
import cors from "cors";
import routes from "./routes";

const app = express();

// Tambahkan ini untuk parsing JSON
app.use(cors({
  origin: '*',
  methods: ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
  allowedHeaders: ['Content-Type', 'Authorization', 'Accept', 'X-CSRF-TOKEN', 'X-HTTP-Method-Override']
}));
app.use(express.json()); // <-- WAJIB
app.use(express.urlencoded({ extended: true })); // kalau butuh form data

app.use("/api", routes);

app.listen(5000, () => {
  console.log("Server running at http://localhost:5000");
});
