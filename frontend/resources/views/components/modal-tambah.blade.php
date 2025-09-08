<div x-data="{
        open: false,
        selectedKategori: '',
        form: {
            kode: '',
            kategori: '',
            topik: '',
            judul: '',
            penulis: '',
            penerbit: '',
            tahun_terbit: '',
            lokasi_rak: '',
            deskripsi: '',
            sampul: ''
        },
        init() {
            window.addEventListener('show-tambah-koleksi', () => {
                this.open = true;
                this.selectedKategori = ''; // Reset kategori saat modal dibuka
            });
            window.addEventListener('hide-tambah-koleksi', () => this.open = false);
        },
        closeModal() {
            this.open = false;
        }
    }"
    x-show="open"
    x-cloak
    @keydown.escape="closeModal()"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
>
    <div class="bg-white rounded-[30px] w-full max-w-md mx-auto relative max-h-[95vh] border border-black overflow-hidden" 
         @click.away="closeModal()"
         @keydown.escape="closeModal()">
        <div class="p-8 overflow-y-auto scrollbar-custom" style="max-height: calc(95vh - 4rem);">

        <h2 class="text-xl font-bold text-center mb-6">Tambah Koleksi</h2>

        <!-- Form Tambah Koleksi -->
        <form action="{{ route('koleksi.store') }}" method="POST" enctype="multipart/form-data" onsubmit="setTimeout(() => window.location.reload(), 1000)">
            @csrf
            <!-- Kategori -->
            <div class="mb-4">
                <label class="block font-medium mb-1">Kategori</label>
                <div class="relative">
                    <select name="kategori" x-model="selectedKategori"
                        class="w-full border border-black rounded-lg px-3 py-2 pr-8 appearance-none focus:outline-none focus:border-[#024088]">
                        <option value="">Pilih Kategori</option>
                        <option value="buku">Buku</option>
                        <option value="jurnal">Jurnal</option>
                        <option value="skripsi">Skripsi</option>
                    </select>
                    <svg class="absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none" width="18" height="10"
                        fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M8.00016 9.6024L0 1.92021L1.99969 0L9 6.72209L16.0003 0L18 1.92021L9.99984 9.6024C9.73464 9.85698 9.375 10 9 10C8.625 10 8.26536 9.85698 8.00016 9.6024Z"
                            fill="black" />
                    </svg>
                </div>
            </div>

            <!-- Kode -->
            <div class="mb-4">
                <label class="block font-medium mb-1">Kode</label>
                <input type="text" name="kode" placeholder="Tuliskan Kode"
                    class="w-full border border-black rounded-lg px-3 py-2 focus:outline-none focus:border-[#024088]" />
            </div>

            <!-- Topik -->
            <div class="mb-4">
                <label class="block font-medium mb-1">
                    Topik 
                    <span x-show="selectedKategori !== 'buku' && selectedKategori !== ''" class="text-sm text-gray-500 font-normal">
                        (hanya untuk kategori Buku)
                    </span>
                </label>
                <input type="text" name="topik" placeholder="Tuliskan Topik"
                    x-bind:value="selectedKategori === 'buku' ? '' : '-'"
                    x-bind:disabled="selectedKategori !== 'buku'"
                    class="w-full border border-black rounded-lg px-3 py-2 focus:outline-none focus:border-[#024088] transition-all duration-300"
                    x-bind:class="selectedKategori !== 'buku' ? 'text-gray-400 cursor-not-allowed bg-gray-100' : 'text-black'" />
            </div>

            <!-- Judul -->
            <div class="mb-4">
                <label class="block font-medium mb-1">Judul</label>
                <input type="text" name="judul" placeholder="Tuliskan Judul"
                    class="w-full border border-black rounded-lg px-3 py-2 focus:outline-none focus:border-[#024088]" />
            </div>

            <!-- Penulis -->
            <div class="mb-4">
                <label class="block font-medium mb-1">Penulis</label>
                <input type="text" name="penulis" placeholder="Tuliskan Penulis"
                    class="w-full border border-black rounded-lg px-3 py-2 focus:outline-none focus:border-[#024088]" />
            </div>

            <!-- Penerbit -->
            <div class="mb-4">
                <label class="block font-medium mb-1">Penerbit</label>
                <input type="text" name="penerbit" placeholder="Tuliskan Penerbit"
                    class="w-full border border-black rounded-lg px-3 py-2 focus:outline-none focus:border-[#024088]" />
            </div>

            <!-- Tahun Terbit -->
            <div class="mb-4">
                <label class="block font-medium mb-1">Tahun Terbit</label>
                <input type="text" name="tahun_terbit" placeholder="Tuliskan Tahun Terbit"
                    class="w-full border border-black rounded-lg px-3 py-2 focus:outline-none focus:border-[#024088]" />
            </div>

            <!-- Lokasi Rak -->
            <div class="mb-4">
                <label class="block font-medium mb-1">Lokasi Rak</label>
                <input type="text" name="lokasi_rak" placeholder="Tuliskan Lokasi Rak"
                    class="w-full border border-black rounded-lg px-3 py-2 focus:outline-none focus:border-[#024088]" />
            </div>

            <!-- Deskripsi -->
            <div class="mb-4">
                <label class="block font-medium mb-1">Deskripsi</label>
                <input type="text" name="deskripsi" placeholder="Tuliskan Deskripsi"
                    class="w-full border border-black rounded-lg px-3 py-2 focus:outline-none focus:border-[#024088]" />
            </div>

            <!-- Sampul -->
            <div class="mb-4">
                <label class="block font-medium mb-1">Sampul</label>
                <div x-data="{ fileName: '' }" class="space-y-2">
                    <input type="file" 
                           name="sampul"
                           accept="image/*" 
                           @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''"
                           class="hidden" 
                           id="sampul-input" />
                    
                    <div class="flex items-center space-x-3">
                        <button type="button" 
                                @click="document.getElementById('sampul-input').click()"
                                class="flex items-center justify-center px-4 py-2 border-2 border-dashed border-gray-300 rounded-lg hover:border-gray-400 hover:bg-gray-50 transition-colors duration-200">
                            <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-700">Pilih File</span>
                        </button>
                        
                        <div x-show="fileName" class="flex-1">
                            <p class="text-sm text-gray-600 truncate" x-text="fileName"></p>
                        </div>
                        
                        <button x-show="fileName" 
                                type="button" 
                                @click="fileName = ''; document.getElementById('sampul-input').value = ''"
                                class="text-red-500 hover:text-red-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <p class="text-xs text-gray-500">Format yang didukung: JPG, PNG (Maksimal 2MB)</p>
                </div>
            </div>



            <!-- Tombol -->
            <div class="flex justify-between gap-4 mt-8">
                <button type="button" @click="closeModal()"
                    class="flex-1 border border-black rounded-[30px] py-2 font-medium hover:bg-gray-100">Batalkan</button>
                <button type="submit" onclick="setTimeout(() => window.location.reload(), 2000)"
                    class="flex-1 bg-[#1976c5] text-white rounded-[30px] py-2 font-medium hover:bg-[#125a96]">Tambah</button>
            </div>
        </form>
        </div>
    </div>
</div>

