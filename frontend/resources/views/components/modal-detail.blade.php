<div x-data="{ 
        open: false, 
        detail: {}, 
        init() {
            window.addEventListener('show-detail-koleksi', (event) => {
                this.detail = event.detail || {};
                this.open = true;
            });
            window.addEventListener('hide-detail-koleksi', () => this.open = false);
        },
        closeModal() {
            this.open = false;
        },
        getKategoriDisplay(kategori) {
            if (!kategori) return 'Buku';
            return kategori.charAt(0).toUpperCase() + kategori.slice(1);
        },
        getStatusClass(status) {
            return status === 'Dipinjam' ? 'bg-[#ded000]/70' : 'bg-[#00d836]/70';
        }
    }" 
    x-show="open" 
    x-cloak 
    class="fixed inset-0 z-70 flex items-center justify-center bg-gray-800/60"
    x-transition:enter="transition ease-out duration-300" 
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100" 
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100" 
    x-transition:leave-end="opacity-0">

    <div class="bg-white rounded-[30px] w-full max-w-2xl mx-auto relative max-h-[95vh] border border-gray-200 overflow-hidden shadow-2xl" 
         @click.away="closeModal()" 
         style="min-width: 600px;">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <button @click="closeModal()" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-gray-100 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" class="text-gray-600">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <h2 class="text-xl font-bold text-gray-800">Detail Koleksi</h2>
            <div class="w-10"></div>
        </div>

        <!-- Content -->
        <div class="p-6">
            <div class="flex gap-6 flex-wrap lg:flex-nowrap">
                
                <!-- Book Cover -->
                <div class="flex-shrink-0">
                    <div class="w-32 h-44 rounded-lg shadow-lg relative overflow-hidden border-2 border-gray-300 bg-gray-100">
                        
                        <!-- Jika ada gambar sampul -->
                        <template x-if="detail.sampul">
                            <img :src="`{{ asset('storage/sampul') }}/${detail.sampul}`" 
                                 alt="Sampul Buku" 
                                 class="w-full h-full object-cover">
                        </template>

                        <!-- Jika tidak ada gambar sampul -->
                        <template x-if="!detail.sampul">
                            <div class="w-full h-full bg-yellow-400 flex-col justify-between p-3 relative border-2 border-yellow-500">
                                <!-- Book Title -->
                                <div class="text-center mt-2">
                                    <h3 class="text-black font-bold text-xs leading-tight" x-text="detail.judul || 'Judul Buku'"></h3>
                                </div>
                               
                                <!-- Icons -->
                                <div class="flex justify-center items-center gap-1 mb-2">
                                    <div class="w-3 h-3 bg-orange-600 rounded-sm flex items-center justify-center">
                                        <span class="text-white text-xs font-bold">☕</span>
                                    </div>
                                    <div class="w-3 h-3 bg-yellow-600 rounded-sm flex items-center justify-center">
                                        <span class="text-white text-xs font-bold" x-text="getKategoriDisplay(detail.kategori).charAt(0)"></span>
                                    </div>
                                </div>
                                
                                <!-- Publisher -->
                                <div class="text-center mb-2">
                                    <p class="text-black text-xs font-medium" x-text="detail.penerbit || 'Penerbit'"></p>
                                </div>
                                
                                <!-- Decorative elements -->
                                <div class="absolute top-0 left-0 w-full h-1 bg-black opacity-30"></div>
                                <div class="absolute bottom-0 left-0 w-full h-1 bg-black opacity-30"></div>
                                <div class="absolute top-2 left-2 w-1 h-1 bg-black opacity-20 rounded-full"></div>
                                <div class="absolute top-2 right-2 w-1 h-1 bg-black opacity-20 rounded-full"></div>
                            </div>
                        </template>

                    </div>
                </div>

                <!-- Book Details -->
                <div class="flex-1 space-y-3">
                    <div class="space-y-2">
                        <p class="text-sm"><span class="font-semibold text-gray-700">Kode:</span> <span class="text-gray-900" x-text="detail.kode || '-'"></span></p>
                        <p class="text-sm"><span class="font-semibold text-gray-700">Judul:</span> <span class="text-gray-900" x-text="detail.judul || '-'"></span></p>
                        <p class="text-sm"><span class="font-semibold text-gray-700">Penulis:</span> <span class="text-gray-900" x-text="detail.penulis || '-'"></span></p>
                        <p class="text-sm"><span class="font-semibold text-gray-700">Tahun Terbit:</span> <span class="text-gray-900" x-text="detail.tahun_terbit || '-'"></span></p>
                        <p class="text-sm"><span class="font-semibold text-gray-700">Lokasi Rak:</span> <span class="text-gray-900" x-text="detail.lokasi_rak || '-'"></span></p>
                        <p class="text-sm" x-show="detail.topik && detail.topik !== '-'"><span class="font-semibold text-gray-700">Topik:</span> <span class="text-gray-900" x-text="detail.topik || '-'"></span></p>
                    </div>

                    <!-- Status Buttons -->
                    <div class="flex gap-3 pt-2">
                        <span class="w-[100px] h-[35px] flex items-center justify-center rounded-full text-black text-sm font-medium shadow-sm"
                              :class="getStatusClass(detail.status)"
                              x-text="detail.status || 'Tersedia'"></span>
                        <span class="w-[100px] h-[35px] flex items-center justify-center border-2 border-black rounded-full text-sm font-medium text-black bg-white shadow-sm"
                              x-text="getKategoriDisplay(detail.kategori)"></span>
                    </div>
                </div>
            </div>

            <!-- Details Section -->
            <div class="mt-6 pt-4 border-t border-gray-200">
                <h4 class="font-semibold text-gray-700 mb-2">Rincian:</h4>
                <p class="text-gray-600 text-sm leading-relaxed" x-text="detail.deskripsi || 'Tidak ada deskripsi tersedia.'"></p>
            </div>
        </div>
    </div>
</div>
