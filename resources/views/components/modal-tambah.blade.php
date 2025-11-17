<style>
    .kategori-dropdown-option:hover {
        background-color: #f3f4f6;
    }
    
    .kategori-dropdown-option.selected {
        background-color: #e5e7eb;
    }
    /* Error state styling */
    .error-border {
        border-color: #dc2626 !important;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
    }

    .error-text {
        color: #dc2626;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .error-message {
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    /* Shake animation untuk field error */
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-2px); }
        20%, 40%, 60%, 80% { transform: translateX(2px); }
    }

    .shake {
        animation: shake 0.5s ease-in-out;
    }

</style>

<div x-data="{
        open: false,
        selectedKategori: '',
        isLoading: false,
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
        errors: {
            kategori: false,
            kode: false,
            judul: false
        },
        init() {
            window.addEventListener('show-tambah-koleksi', () => {
                this.open = true;
                this.selectedKategori = ''; // Reset kategori saat modal dibuka
                this.resetErrors();
            });
            window.addEventListener('hide-tambah-koleksi', () => this.open = false);
        },
        closeModal() {
            this.open = false;
            this.resetErrors();
        },
        resetErrors() {
            this.errors = {
                kategori: false,
                kode: false,
                judul: false
            };
        },
        validateField(field) {
            if (field === 'kategori') {
                this.errors.kategori = !this.selectedKategori;
            } else if (field === 'kode') {
                this.errors.kode = !this.form.kode.trim();
            } else if (field === 'judul') {
                this.errors.judul = !this.form.judul.trim();
            }
        },
        validateAll() {
            this.validateField('kategori');
            this.validateField('kode');
            this.validateField('judul');
            return !this.errors.kategori && !this.errors.kode && !this.errors.judul;
        },
        focusToFirstError() {
            // Cari field pertama yang error dan fokus ke sana
            if (this.errors.kategori) {
                // Fokus ke dropdown kategori
                const dropdownTrigger = document.getElementById('kategori-dropdown');
                if (dropdownTrigger) {
                    dropdownTrigger.focus();
                    dropdownTrigger.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    // Tambahkan efek shake
                    dropdownTrigger.classList.add('shake');
                    setTimeout(() => {
                        dropdownTrigger.classList.remove('shake');
                    }, 500);
                }
                return;
            }
            
            if (this.errors.kode) {
                // Fokus ke input kode
                const kodeInput = document.getElementById('kode-input');
                if (kodeInput) {
                    kodeInput.focus();
                    kodeInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    // Tambahkan efek shake
                    kodeInput.classList.add('shake');
                    setTimeout(() => {
                        kodeInput.classList.remove('shake');
                    }, 500);
                }
                return;
            }
            
            if (this.errors.judul) {
                // Fokus ke input judul
                const judulInput = document.getElementById('judul-input');
                if (judulInput) {
                    judulInput.focus();
                    judulInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    // Tambahkan efek shake
                    judulInput.classList.add('shake');
                    setTimeout(() => {
                        judulInput.classList.remove('shake');
                    }, 500);
                }
                return;
            }
        },
        handleSubmit(event) {
            if (!this.validateAll()) {
                event.preventDefault();
                // Fokus ke field pertama yang error
                this.$nextTick(() => {
                    this.focusToFirstError();
                });
                return false;
            }
            
            // Set loading state
            this.isLoading = true;
            
            // Biarkan form submit normal
            // Form akan redirect setelah berhasil
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
        <form action="{{ route('koleksi.store') }}" method="POST" enctype="multipart/form-data" @submit="handleSubmit($event)">
            @csrf
            <!-- Kategori -->
            <div class="mb-4">
                <label class="block font-medium mb-1">Kategori <span class="text-red-500">*</span></label>
                <div class="relative" x-data="{ 
                    open: false
                }" x-init="
                    // Set selectedKategori dari old value jika ada
                    if ('{{ old('kategori') }}') {
                        selectedKategori = '{{ old('kategori') }}';
                    }
                ">
                    <div @click="open = !open" 
                         id="kategori-dropdown"
                         class="w-full border rounded-lg px-3 py-2 appearance-none focus:outline-none cursor-pointer flex items-center justify-between transition-all duration-200" 
                         :class="{ 
                             'border-[#024088]': open && !errors.kategori,
                             'border-red-500 error-border': errors.kategori,
                             'border-black': !open && !errors.kategori
                         }">
                        <span x-text="selectedKategori ? (selectedKategori === 'buku' ? 'Buku' : selectedKategori === 'jurnal' ? 'Jurnal' : selectedKategori === 'skripsi' ? 'Skripsi' : '') : 'Pilih Kategori'" class="text-left"></span>
                        <svg class="pointer-events-none transition-transform" :class="{ 'rotate-180': open }" width="18" height="10" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M8.00016 9.6024L0 1.92021L1.99969 0L9 6.72209L16.0003 0L18 1.92021L9.99984 9.6024C9.73464 9.85698 9.375 10 9 10C8.625 10 8.26536 9.85698 8.00016 9.6024Z"
                                fill="black" />
                        </svg>
                    </div>
                    
                    <!-- Hidden input untuk form -->
                    <input type="hidden" name="kategori" x-model="selectedKategori" required>
                    
                    <!-- Dropdown tanpa search -->
                    <div x-show="open" x-transition @click.away="open = false" class="absolute left-0 right-0 mt-2 bg-white rounded-[15px] shadow-lg border border-black overflow-hidden z-50">
                        <!-- Options dengan Scroll -->
                        <div class="max-h-60 overflow-y-auto">
                            <div @click="
                                selectedKategori = '';
                                open = false;
                                validateField('kategori');
                            "
                                 class="px-4 py-2 text-sm text-black cursor-pointer kategori-dropdown-option border-b">
                                Pilih Kategori
                            </div>
                            <div @click="
                                selectedKategori = 'buku';
                                open = false;
                                validateField('kategori');
                            "
                                 class="px-4 py-2 text-sm text-black cursor-pointer kategori-dropdown-option border-b">
                                <div class="font-medium">Buku</div>
                            </div>
                            <div @click="
                                selectedKategori = 'jurnal';
                                open = false;
                                validateField('kategori');
                            "
                                 class="px-4 py-2 text-sm text-black cursor-pointer kategori-dropdown-option border-b">
                                <div class="font-medium">Jurnal</div>
                            </div>
                            <div @click="
                                selectedKategori = 'skripsi';
                                open = false;
                                validateField('kategori');
                            "
                                 class="px-4 py-2 text-sm text-black cursor-pointer kategori-dropdown-option">
                                <div class="font-medium">Skripsi</div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Error message untuk kategori -->
                <div x-show="errors.kategori" class="error-message error-text">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <span>Kategori wajib dipilih</span>
                </div>
            </div>

            <!-- Kode -->
            <div class="mb-4">
                <label class="block font-medium mb-1">Kode <span class="text-red-500">*</span></label>
                <input type="text" 
                       id="kode-input"
                       name="kode" 
                       x-model="form.kode"
                       @input="validateField('kode')"
                       @blur="validateField('kode')"
                       placeholder="Tuliskan Kode"
                       class="w-full border rounded-lg px-3 py-2 focus:outline-none transition-all duration-200"
                       :class="{
                           'border-red-500 error-border': errors.kode,
                           'border-black focus:border-[#024088]': !errors.kode
                       }" />
                <!-- Error message untuk kode -->
                <div x-show="errors.kode" class="error-message error-text">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <span>Kode wajib diisi</span>
                </div>
            </div>

            <!-- Topik -->
            <div class="mb-4">
                <label class="block font-medium mb-1">
                    Topik 
                    <span x-show="selectedKategori !== 'buku' && selectedKategori !== ''" class="text-sm text-gray-500 font-normal">
                        (hanya untuk Kategori Buku)
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
                <label class="block font-medium mb-1">Judul <span class="text-red-500">*</span></label>
                <input type="text" 
                       id="judul-input"
                       name="judul" 
                       x-model="form.judul"
                       @input="validateField('judul')"
                       @blur="validateField('judul')"
                       placeholder="Tuliskan Judul"
                       class="w-full border rounded-lg px-3 py-2 focus:outline-none transition-all duration-200"
                       :class="{
                           'border-red-500 error-border': errors.judul,
                           'border-black focus:border-[#024088]': !errors.judul
                       }" />
                <!-- Error message untuk judul -->
                <div x-show="errors.judul" class="error-message error-text">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <span>Judul wajib diisi</span>
                </div>
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

            <!-- Tautan -->
            <div class="mb-4">
                <label class="block font-medium mb-1">
                    Tautan 
                    <span x-show="selectedKategori !== 'jurnal' && selectedKategori !== 'skripsi' && selectedKategori !== ''" class="text-sm text-gray-500 font-normal">
                        (Hanya untuk Kategori Jurnal dan Skripsi)
                    </span>
                </label>
                <input type="url" name="tautan" placeholder="Tuliskan URL tautan"
                    x-bind:value="selectedKategori === 'jurnal' || selectedKategori === 'skripsi' ? '' : '-'"
                    x-bind:disabled="selectedKategori !== 'jurnal' && selectedKategori !== 'skripsi'"
                    class="w-full border border-black rounded-lg px-3 py-2 focus:outline-none focus:border-[#024088] transition-all duration-300"
                    x-bind:class="selectedKategori !== 'jurnal' && selectedKategori !== 'skripsi' ? 'text-gray-400 cursor-not-allowed bg-gray-100' : 'text-black'" />
                <p class="text-xs text-gray-500 mt-1">
                    <span x-show="selectedKategori === 'jurnal' || selectedKategori === 'skripsi'">Tuliskan URL lengkap (contoh: https://example.com)</span>
                </p>
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
                    class="glass-effect flex-1 border border-black rounded-[30px] py-2 font-medium hover:bg-gray-100 transition-all duration-300">Batalkan</button>
                <button type="submit" id="submit-btn"
                    class="loading-button glass-effect flex-1 bg-[#1976c5] text-white rounded-[30px] py-2 font-medium hover:bg-[#125a96] flex items-center justify-center transition-all duration-300"
                    :disabled="isLoading"
                    :class="{ 'opacity-50 cursor-not-allowed': isLoading }">
                    <span x-show="!isLoading">Tambah</span>
                    <span x-show="isLoading">Menambahkan...</span>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Reset loading state saat modal dibuka
    window.addEventListener('show-tambah-koleksi', function() {
        const modalElement = document.querySelector('[x-data*="isLoading: false"]');
        if (modalElement && modalElement.__x) {
            modalElement.__x.$data.isLoading = false;
        }
    });
    
    // Reset loading state saat modal ditutup
    window.addEventListener('hide-tambah-koleksi', function() {
        const modalElement = document.querySelector('[x-data*="isLoading: false"]');
        if (modalElement && modalElement.__x) {
            modalElement.__x.$data.isLoading = false;
        }
    });
});
</script>

