
<div x-data="{ 
        open: false,
        koleksiData: null,
        selectedKategori: '',
        isLoading: false,
        hasUserEdited: false,
        confirmOpen: false,
        init() {
            window.addEventListener('show-edit-koleksi', (event) => {
                this.koleksiData = event.detail;
                this.selectedKategori = event.detail?.kategori || '';
                this.isLoading = false;
                this.hasUserEdited = false;
                this.open = true;
            });
            window.addEventListener('hide-edit-koleksi', () => this.open = false);
            
            // Listen untuk update koleksi dari modal lain (jika ada)
            window.addEventListener('koleksi-updated', (event) => {
                const updatedData = event.detail;
                // Update data jika modal edit sedang terbuka dan kode sama
                if (this.open && this.koleksiData && this.koleksiData.kode === updatedData.kode && !this.hasUserEdited) {
                    this.koleksiData = {
                        ...this.koleksiData,
                        ...updatedData
                    };
                    this.selectedKategori = updatedData.kategori || this.koleksiData.kategori;
                    console.log('Modal edit data updated from external source:', updatedData);
                }
            });
        },
        closeModal() {
            this.open = false;
            this.hasUserEdited = false;
        },
        refreshFormData() {
            // Refresh data form dengan data terbaru dari koleksiData
            if (this.koleksiData) {
                this.selectedKategori = this.koleksiData.kategori || '';
                console.log('Form data refreshed:', this.koleksiData);
            }
        },
        async submitForm(event) {
            event.preventDefault();
            
            // Set loading state
            this.isLoading = true;
            
            try {
                const form = event.target;
                const formData = new FormData(form);
                
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData
                });
                
                if (response.ok) {
                    const result = await response.json();
                    
                    // Update data di modal edit dengan data terbaru
                    this.koleksiData = {
                        ...this.koleksiData,
                        kategori: formData.get('kategori'),
                        topik: formData.get('topik'),
                        judul: formData.get('judul'),
                        penulis: formData.get('penulis'),
                        penerbit: formData.get('penerbit'),
                        tahun_terbit: formData.get('tahun_terbit'),
                        lokasi_rak: formData.get('lokasi_rak'),
                        deskripsi: formData.get('deskripsi')
                    };
                    
                    // Update selectedKategori juga
                    this.selectedKategori = formData.get('kategori');
                    
                    // Refresh form data untuk memastikan tampilan terupdate
                    this.refreshFormData();
                    
                    // Dispatch event untuk update data di modal detail
                    window.dispatchEvent(new CustomEvent('koleksi-updated', { 
                        detail: {
                            kode: this.koleksiData.kode,
                            kategori: formData.get('kategori'),
                            topik: formData.get('topik'),
                            judul: formData.get('judul'),
                            penulis: formData.get('penulis'),
                            penerbit: formData.get('penerbit'),
                            tahun_terbit: formData.get('tahun_terbit'),
                            lokasi_rak: formData.get('lokasi_rak'),
                            deskripsi: formData.get('deskripsi'),
                            // Pertahankan field yang tidak diubah
                            status: this.koleksiData.status,
                            sampul: this.koleksiData.sampul,
                            tautan: this.koleksiData.tautan
                        }
                    }));
                    
                    this.closeModal();
                    this.hasUserEdited = false;
                    
                    // Tampilkan pesan sukses dari controller
                    if (typeof showSuccessMessage === 'function' && result.success) {
                        showSuccessMessage(result.message);
                    }
                    
                    // Refresh data tanpa reload halaman
                    window.dispatchEvent(new CustomEvent('refresh-koleksi-data'));
                    
                    // Update tabel koleksi juga
                    window.dispatchEvent(new CustomEvent('update-table-row', {
                        detail: {
                            kode: this.koleksiData.kode,
                            kategori: formData.get('kategori'),
                            topik: formData.get('topik'),
                            judul: formData.get('judul'),
                            penulis: formData.get('penulis'),
                            penerbit: formData.get('penerbit'),
                            tahun_terbit: formData.get('tahun_terbit'),
                            lokasi_rak: formData.get('lokasi_rak'),
                            deskripsi: formData.get('deskripsi'),
                            status: this.koleksiData.status,
                            sampul: this.koleksiData.sampul,
                            tautan: this.koleksiData.tautan
                        }
                    }));
                } else {
                    const errorText = await response.text();
                    if (typeof showErrorMessage === 'function') {
                        showErrorMessage('Gagal memperbarui koleksi: ' + errorText);
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                if (typeof showErrorMessage === 'function') {
                    showErrorMessage('Terjadi kesalahan saat memperbarui koleksi');
                }
            } finally {
                // Reset loading state
                this.isLoading = false;
            }
        }
    }"
    x-show="open"
    x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    <div class="bg-white rounded-[30px] w-full max-w-md mx-auto relative max-h-[95vh] border border-black overflow-hidden" 
         @click.away="closeModal()">
        <div class="p-8 overflow-y-auto scrollbar-custom" style="max-height: calc(95vh - 4rem);">
        <h2 class="text-xl font-bold text-center mb-6">Edit Koleksi</h2>
        <form x-show="koleksiData" x-ref="editForm" :action="`/koleksi/${koleksiData?.kode || ''}`" method="POST" enctype="multipart/form-data" @submit.prevent="return false" @input="hasUserEdited = true">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block font-medium mb-1">Kategori</label>
                <div class="relative">
                    <select name="kategori" x-model="selectedKategori" class="w-full border border-black rounded-lg px-3 py-2 pr-8 appearance-none focus:outline-none focus:border-[#024088]">
                        <option value="">Pilih Kategori</option>
                        <option value="buku" x-bind:selected="koleksiData?.kategori === 'buku'">Buku</option>
                        <option value="jurnal" x-bind:selected="koleksiData?.kategori === 'jurnal'">Jurnal</option>
                        <option value="skripsi" x-bind:selected="koleksiData?.kategori === 'skripsi'">Skripsi</option>
                    </select>
                    <svg class="absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none" width="18" height="10"
                        fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M8.00016 9.6024L0 1.92021L1.99969 0L9 6.72209L16.0003 0L18 1.92021L9.99984 9.6024C9.73464 9.85698 9.375 10 9 10C8.625 10 8.26536 9.85698 8.00016 9.6024Z"
                            fill="black" />
                    </svg>
                </div>
            </div>
            <div class="mb-4">
                <label class="block font-medium mb-1">Kode</label>
                <input type="text" name="kode" placeholder="Tuliskan Kode"
                    x-bind:value="koleksiData?.kode || ''"
                    readonly
                    class="w-full border border-black rounded-lg px-3 py-2 focus:outline-none focus:border-[#024088] bg-gray-100" />
            </div>
            <div class="mb-4">
                <label class="block font-medium mb-1">
                    Topik 
                    <span x-show="selectedKategori !== 'buku' && selectedKategori !== ''" class="text-sm text-gray-500 font-normal">
                        (hanya untuk kategori Buku)
                    </span>
                </label>
                <input type="text" name="topik" placeholder="Tuliskan Topik"
                    x-bind:value="selectedKategori === 'buku' ? (koleksiData?.topik || '') : '-'"
                    x-bind:disabled="selectedKategori !== 'buku'"
                    class="w-full border border-black rounded-lg px-3 py-2 focus:outline-none focus:border-[#024088] transition-all duration-300"
                    x-bind:class="selectedKategori !== 'buku' ? 'text-gray-400 cursor-not-allowed bg-gray-100' : 'text-black'" />
            </div>

            <div class="mb-4">
                <label class="block font-medium mb-1">Judul</label>
                <input type="text" name="judul" placeholder="Tuliskan Judul"
                    x-model="koleksiData.judul"
                    class="w-full border border-black rounded-lg px-3 py-2 focus:outline-none focus:border-[#024088]" />
            </div>
            <div class="mb-4">
                <label class="block font-medium mb-1">Penulis</label>
                <input type="text" name="penulis" placeholder="Tuliskan Penulis"
                    x-model="koleksiData.penulis"
                    class="w-full border border-black rounded-lg px-3 py-2 focus:outline-none focus:border-[#024088]" />
            </div>
            <div class="mb-4">
                <label class="block font-medium mb-1">Penerbit</label>
                <input type="text" name="penerbit" placeholder="Tuliskan Penerbit"
                    x-model="koleksiData.penerbit"
                    class="w-full border border-black rounded-lg px-3 py-2 focus:outline-none focus:border-[#024088]" />
            </div>

            <div class="mb-4">
                <label class="block font-medium mb-1">Tahun Terbit</label>
                <input type="text" name="tahun_terbit" placeholder="Tuliskan Tahun Terbit"
                    x-model="koleksiData.tahun_terbit"
                    class="w-full border border-black rounded-lg px-3 py-2 focus:outline-none focus:border-[#024088]" />
            </div>
            <div class="mb-4">
                <label class="block font-medium mb-1">Lokasi Rak</label>
                <input type="text" name="lokasi_rak" placeholder="Tuliskan Lokasi Rak"
                    x-model="koleksiData.lokasi_rak"
                    class="w-full border border-black rounded-lg px-3 py-2 focus:outline-none focus:border-[#024088]" />
            </div>
            <div class="mb-4">
                <label class="block font-medium mb-1">Deskripsi</label>
                <input type="text" name="deskripsi" placeholder="Tuliskan Deskripsi"
                    x-model="koleksiData.deskripsi"
                    class="w-full border border-black rounded-lg px-3 py-2 focus:outline-none focus:border-[#024088]" />
            </div>
            <!-- Sampul -->
            <div class="mb-4">
                <label class="block font-medium mb-1">Sampul</label>
                <div x-data="{ fileName: '' }" class="space-y-2">
                     <!-- Hidden input untuk mengirim sampul lama -->
                    <input type="hidden" name="sampul_lama" :value="koleksiData?.sampul">

                    <!-- Preview sampul yang sudah ada -->
                    <template x-if="koleksiData?.sampul && koleksiData.sampul !== '' && koleksiData.sampul !== null">
                        <div class="mb-3">
                            <p class="text-sm text-gray-600 mb-2">Sampul saat ini:</p>
                            <img :src="'/storage/sampul/' + koleksiData.sampul" 
                                :alt="koleksiData.judul || 'Cover Buku'"
                                class="w-20 h-28 object-cover rounded border border-gray-300"
                                loading="lazy">
                        </div>
                    </template>
                    
                    <input type="file" 
                           name="sampul"
                           accept="image/*" 
                           @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''"
                           class="hidden" 
                           id="sampul-edit-input" />
                    
                    <div class="flex items-center space-x-3">
                        <button type="button" 
                                @click="document.getElementById('sampul-edit-input').click()"
                                class="flex items-center justify-center px-4 py-2 border-2 border-dashed border-gray-300 rounded-lg hover:border-gray-400 hover:bg-gray-50 transition-colors duration-200">
                            <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-700" x-text="koleksiData?.sampul ? 'Ganti Sampul' : 'Pilih File'"></span>
                        </button>
                        
                        <div x-show="fileName" class="flex-1">
                            <p class="text-sm text-gray-600 truncate" x-text="fileName"></p>
                        </div>
                        
                        <button x-show="fileName" 
                                type="button" 
                                @click="fileName = ''; document.getElementById('sampul-edit-input').value = ''"
                                class="text-red-500 hover:text-red-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <p class="text-xs text-gray-500">Format yang didukung: JPG, PNG (Maksimal 2MB). Kosongkan jika tidak ingin mengubah.</p>
                </div>
            </div>


            <div class="flex justify-between gap-4 mt-8">
                <button type="button" @click="closeModal()"
                    class="glass-effect flex-1 border border-black rounded-[30px] py-2 font-medium hover:bg-gray-100 transition-all duration-300"
                    :disabled="isLoading">Batalkan</button>
                <button type="button" id="submit-btn-edit" onclick="showEditConfirmationModal()"
                    class="loading-button glass-effect flex-1 bg-[#1976c5] text-white rounded-[30px] py-2 font-medium hover:bg-[#125a96] flex items-center justify-center transition-all duration-300"
                    :disabled="isLoading"
                    :class="{ 'opacity-50 cursor-not-allowed': isLoading }">
                    <span x-show="!isLoading">Update Koleksi</span>
                    <span x-show="isLoading">Memperbarui...</span>
                    <svg x-show="isLoading" class="loading-spinner-svg ml-2 text-white"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </div>
        </form>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Edit -->
<div id="edit-confirmation-modal" class="fixed inset-0 z-60 flex items-center justify-center bg-black/60 backdrop-blur-sm" style="display: none;">
    <div id="edit-confirmation-modal-content" class="bg-white rounded-[30px] w-full max-w-sm mx-auto relative border border-gray-200 shadow-2xl transform transition-all duration-300 ease-out scale-95 opacity-0">
        <!-- Modal Header -->
        <div class="flex items-center justify-center p-6">
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-2">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 20.5a8.5 8.5 0 100-17 8.5 8.5 0 000 17z" />
                </svg>
            </div>
        </div>
        
        <!-- Modal Body -->
        <div class="px-6 pb-2 text-center">
            <h3 class="text-xl font-bold text-gray-900">Konfirmasi Perubahan</h3>
            <p class="text-gray-600 mt-2">Apakah Anda yakin ingin memperbarui data koleksi ini?</p>
        </div>

        <!-- Modal Footer -->
        <div class="flex justify-between gap-4 px-6 pb-6">
            <button type="button" onclick="hideEditConfirmationModal()"
                    class="glass-effect flex-1 border border-black rounded-[30px] py-2 font-medium hover:bg-gray-100 transition-all duration-300">
                Batalkan
            </button>
            <button type="button" id="confirm-edit-btn"
                    class="glass-effect flex-1 bg-[#1976c5] text-white rounded-[30px] py-2 font-medium hover:bg-[#125a96] transition-all duration-300">
                Ya, Perbarui
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Edit Confirmation Modal Controls (mengikuti pola sidebar logout)
    window.showEditConfirmationModal = function() {
        const modal = document.getElementById('edit-confirmation-modal');
        const modalContent = document.getElementById('edit-confirmation-modal-content');
        if (!modal || !modalContent) return;
        modal.style.display = 'flex';
        setTimeout(() => {
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
        }, 10);
    };

    window.hideEditConfirmationModal = function() {
        const modal = document.getElementById('edit-confirmation-modal');
        const modalContent = document.getElementById('edit-confirmation-modal-content');
        if (!modal || !modalContent) return;
        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.style.display = 'none';
        }, 300);
    };

    // Klik di luar untuk menutup
    const editModalBackdrop = document.getElementById('edit-confirmation-modal');
    if (editModalBackdrop) {
        editModalBackdrop.addEventListener('click', function(e) {
            if (e.target === this) hideEditConfirmationModal();
        });
    }

    // Escape untuk menutup
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('edit-confirmation-modal');
            if (modal && modal.style.display === 'flex') hideEditConfirmationModal();
        }
    });

    // Tombol konfirmasi edit: jalankan submit dan loading state di sini
    const confirmEditBtn = document.getElementById('confirm-edit-btn');
    if (confirmEditBtn) {
        confirmEditBtn.addEventListener('click', async function() {
            const btn = this;
            // set loading di tombol konfirmasi
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');
            btn.innerHTML = 'Memproses...';

            try {
                // Ambil form referensi
                const form = document.querySelector('[x-ref="editForm"]');
                if (!form) return;

                // Kumpulkan form data & submit via fetch sesuai logic Alpine (mirror)
                const formData = new FormData(form);
                const action = form.getAttribute('action');
                const response = await fetch(action, { method: 'POST', body: formData });
                if (response.ok) {
                    const result = await response.json();
                    // Dispatch event agar konsisten dengan yang ada
                    const updatedData = Object.fromEntries(formData.entries());
                    updatedData.kode = updatedData.kode || (window?.currentEditingKode || '');
                    window.dispatchEvent(new CustomEvent('koleksi-updated', { detail: updatedData }));
                    hideEditConfirmationModal();
                    // Tutup modal edit utama
                    window.dispatchEvent(new CustomEvent('hide-edit-koleksi'));
                    if (typeof showSuccessMessage === 'function' && result.success) {
                        showSuccessMessage(result.message);
                    }
                    window.dispatchEvent(new CustomEvent('refresh-koleksi-data'));
                } else {
                    const errorText = await response.text();
                    if (typeof showErrorMessage === 'function') {
                        showErrorMessage('Gagal memperbarui koleksi: ' + errorText);
                    }
                }
            } catch (err) {
                console.error(err);
                if (typeof showErrorMessage === 'function') {
                    showErrorMessage('Terjadi kesalahan saat memperbarui koleksi');
                }
            } finally {
                btn.disabled = false;
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
                btn.innerHTML = 'Ya, Perbarui';
            }
        });
    }
    // Reset loading state saat modal dibuka
    window.addEventListener('show-edit-koleksi', function() {
        const modalElement = document.querySelector('[x-data*="isLoading: false"]');
        if (modalElement && modalElement.__x) {
            modalElement.__x.$data.isLoading = false;
        }
    });
    
    // Reset loading state saat modal ditutup
    window.addEventListener('hide-edit-koleksi', function() {
        const modalElement = document.querySelector('[x-data*="isLoading: false"]');
        if (modalElement && modalElement.__x) {
            modalElement.__x.$data.isLoading = false;
        }
    });
});
</script>