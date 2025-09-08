import "./bootstrap";
import Alpine from "alpinejs";
import axios from "axios";

window.Alpine = Alpine;

// Set API base URL dari .env
const API_URL = import.meta.env.VITE_API_URL;

// Component Alpine untuk Koleksi
Alpine.data("koleksiComponent", () => ({
    koleksi: [],

    async fetchKoleksi() {
        try {
            const res = await axios.get(`${API_URL}/koleksi`);
            this.koleksi = res.data;
        } catch (err) {
            console.error("Gagal ambil koleksi:", err);
        }
    },

    init() {
        this.fetchKoleksi();
    }
}));

// Event listener untuk update data koleksi
document.addEventListener('DOMContentLoaded', function() {
    // Listen untuk update data setelah edit berhasil
    window.addEventListener('koleksi-updated', (event) => {
        console.log('Koleksi updated:', event.detail);
        
        // Dispatch event untuk refresh data di modal detail
        window.dispatchEvent(new CustomEvent('refresh-koleksi-data', {
            detail: { kode: event.detail.kode }
        }));
    });
    
    // Listen untuk refresh data dari server
    window.addEventListener('refresh-koleksi-data', async (event) => {
        if (event.detail && event.detail.kode) {
            try {
                const response = await fetch(`/api/koleksi/${event.detail.kode}`);
                if (response.ok) {
                    const updatedData = await response.json();
                    console.log('Refreshed koleksi data:', updatedData);
                    
                    // Dispatch event untuk update modal detail
                    window.dispatchEvent(new CustomEvent('koleksi-updated', {
                        detail: updatedData
                    }));
                }
            } catch (error) {
                console.error('Error refreshing koleksi data:', error);
            }
        }
    });
});

Alpine.start();
