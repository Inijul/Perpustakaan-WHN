// API dan Event Management untuk Koleksi

class KoleksiAPI {
    constructor() {
        this.baseURL = '/api/koleksi';
        this.csrfToken = document.querySelector('meta[name=csrf-token]')?.getAttribute('content');
    }

    // Ambil data koleksi berdasarkan kode
    async getKoleksi(kode) {
        try {
            const response = await fetch(`${this.baseURL}/${kode}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                }
            });

            if (response.ok) {
                return await response.json();
            } else {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
        } catch (error) {
            console.error('Error fetching koleksi:', error);
            throw error;
        }
    }

    // Update koleksi
    async updateKoleksi(kode, data) {
        try {
            const formData = new FormData();
            
            // Tambahkan data ke FormData
            Object.keys(data).forEach(key => {
                if (data[key] !== null && data[key] !== undefined) {
                    formData.append(key, data[key]);
                }
            });

            const response = await fetch(`/koleksi/${kode}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                },
                body: formData
            });

            if (response.ok) {
                return await response.json();
            } else {
                const errorText = await response.text();
                throw new Error(errorText);
            }
        } catch (error) {
            console.error('Error updating koleksi:', error);
            throw error;
        }
    }

    // Delete koleksi
    async deleteKoleksi(kode) {
        try {
            const response = await fetch(`/koleksi/${kode}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Content-Type': 'application/json',
                }
            });

            if (response.ok) {
                return await response.json();
            } else {
                const errorText = await response.text();
                throw new Error(errorText);
            }
        } catch (error) {
            console.error('Error deleting koleksi:', error);
            throw error;
        }
    }
}

// Event Manager untuk komunikasi antar komponen
class KoleksiEventManager {
    constructor() {
        this.api = new KoleksiAPI();
        this.initEventListeners();
    }

    initEventListeners() {
        // Listen untuk update data setelah edit berhasil
        window.addEventListener('koleksi-updated', (event) => {
            console.log('Koleksi updated event received:', event.detail);
            this.handleKoleksiUpdated(event.detail);
        });

        // Listen untuk refresh data dari server
        window.addEventListener('refresh-koleksi-data', async (event) => {
            if (event.detail && event.detail.kode) {
                await this.refreshKoleksiData(event.detail.kode);
            }
        });

        // Listen untuk delete koleksi
        window.addEventListener('koleksi-deleted', (event) => {
            console.log('Koleksi deleted event received:', event.detail);
            this.handleKoleksiDeleted(event.detail);
        });
    }

    // Handle koleksi updated
    handleKoleksiUpdated(updatedData) {
        // Dispatch event untuk update modal detail
        window.dispatchEvent(new CustomEvent('refresh-koleksi-data', {
            detail: { kode: updatedData.kode }
        }));

        // Dispatch event untuk update tabel
        window.dispatchEvent(new CustomEvent('update-table-row', {
            detail: updatedData
        }));
    }

    // Handle koleksi deleted
    handleKoleksiDeleted(deletedData) {
        // Dispatch event untuk remove row dari tabel
        window.dispatchEvent(new CustomEvent('remove-table-row', {
            detail: { kode: deletedData.kode }
        }));
    }

    // Refresh data koleksi dari server
    async refreshKoleksiData(kode) {
        try {
            const updatedData = await this.api.getKoleksi(kode);
            console.log('Refreshed koleksi data from server:', updatedData);
            
            // Dispatch event untuk update modal detail
            window.dispatchEvent(new CustomEvent('koleksi-updated', {
                detail: updatedData
            }));
        } catch (error) {
            console.error('Error refreshing koleksi data:', error);
        }
    }

    // Dispatch event untuk show detail
    showDetail(koleksiData) {
        window.dispatchEvent(new CustomEvent('show-detail-koleksi', {
            detail: koleksiData
        }));
    }

    // Dispatch event untuk show edit
    showEdit(koleksiData) {
        window.dispatchEvent(new CustomEvent('show-edit-koleksi', {
            detail: koleksiData
        }));
    }

    // Dispatch event untuk hide modal
    hideModal(modalType) {
        window.dispatchEvent(new CustomEvent(`hide-${modalType}-koleksi`));
    }
}

// Initialize event manager when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.koleksiEventManager = new KoleksiEventManager();
    console.log('Koleksi Event Manager initialized');
});

// Export untuk penggunaan di file lain
window.KoleksiAPI = KoleksiAPI;
window.KoleksiEventManager = KoleksiEventManager;
