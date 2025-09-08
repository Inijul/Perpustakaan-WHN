@props(['koleksis' => [], 'mahasiswas' => []])

<style>
    .book-dropdown-option:hover,
    .mahasiswa-dropdown-option:hover {
        background-color: #f3f4f6;
    }
    
    .book-dropdown-option.selected,
    .mahasiswa-dropdown-option.selected {
        background-color: #e5e7eb;
    }
    
    .book-search-input:focus,
    .mahasiswa-search-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
</style>

    <div id="modal-aktivitas" 
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
     style="display: none;">
>
    <div class="bg-white rounded-[30px] w-full max-w-md mx-auto relative max-h-[95vh] border border-black overflow-hidden" 
         onclick="event.stopPropagation()">
        <div class="p-8 overflow-y-auto scrollbar-custom" style="max-height: calc(95vh - 4rem);">
            <!-- Close button -->
            <button onclick="closeModal()" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

        <h2 class="text-xl font-bold text-center mb-6">Tambah Aktivitas</h2>

        <form action="{{ route('aktivitas.store') }}" method="POST" id="aktivitas-form">
            @csrf
            <!-- Tanggal -->
            <div class="mb-4">
                <label class="block font-medium mb-1">Tanggal Peminjaman</label>
                <input type="date" name="tanggal_peminjaman" required
                    class="w-full border border-black rounded-lg px-3 py-2 focus:outline-none focus:border-[#024088] @error('tanggal_peminjaman') border-red-500 @enderror"
                    placeholder="Masukan Tanggal"
                    value="{{ old('tanggal_peminjaman', date('Y-m-d')) }}" />
                @error('tanggal_peminjaman')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nama -->
            <div class="mb-4">
                <label class="block font-medium mb-1">Nama Peminjam</label>
                <div class="relative" x-data="{ 
                    open: false, 
                    search: '', 
                    selectedNrm: '{{ old('nrm') }}', 
                    selectedNama: ''
                }" x-init="
                    // Set selectedNama jika ada old value
                    if (selectedNrm) {
                        selectedNama = '{{ old('nrm') ? (collect($mahasiswas)->firstWhere('nrm', old('nrm'))['namam'] ?? '') : '' }}';
                    }
                ">
                    <div @click="open = !open" class="w-full border border-black rounded-lg px-3 py-2 appearance-none focus:outline-none focus:border-[#024088] cursor-pointer flex items-center justify-between @error('nrm') border-red-500 @enderror" :class="{ 'border-[#024088]': open }">
                        <span x-text="selectedNrm ? selectedNama + ' (' + selectedNrm + ')' : 'Pilih Mahasiswa'" class="text-left"></span>
                        <svg class="pointer-events-none transition-transform" :class="{ 'rotate-180': open }" width="18" height="10" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M8.00016 9.6024L0 1.92021L1.99969 0L9 6.72209L16.0003 0L18 1.92021L9.99984 9.6024C9.73464 9.85698 9.375 10 9 10C8.625 10 8.26536 9.85698 8.00016 9.6024Z"
                                fill="black" />
                        </svg>
                    </div>
                    
                    <!-- Hidden input untuk form -->
                    <input type="hidden" name="nrm" x-model="selectedNrm" required>
                    
                    <!-- Dropdown dengan search -->
                    <div x-show="open" x-transition @click.away="open = false" class="absolute left-0 right-0 mt-2 bg-white rounded-[15px] shadow-lg border border-black overflow-hidden z-50 max-h-80">
                        <!-- Search Input -->
                        <div class="p-2 border-b border-gray-200 bg-white sticky top-0 z-10">
                            <div class="relative">
                                <input type="text" 
                                       placeholder="Cari mahasiswa..." 
                                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg mahasiswa-search-input"
                                       x-model="search"
                                       @click.stop
                                       @keydown.escape="open = false; search = ''"
                                       @keydown.enter="if($refs.filteredMahasiswaOptions.children.length === 1) { $refs.filteredMahasiswaOptions.children[0].click(); }" />
                                <svg class="absolute right-2 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                        
                        <!-- Options dengan Scroll -->
                        <div class="max-h-60 overflow-y-auto" x-ref="filteredMahasiswaOptions">
                            @if(isset($mahasiswas) && is_array($mahasiswas) && count($mahasiswas) > 0)
                                @foreach($mahasiswas as $mahasiswa)
                                    <div @click="
                                        selectedNrm = '{{ $mahasiswa['nrm'] }}';
                                        selectedNama = '{{ $mahasiswa['namam'] ?? '' }}';
                                        open = false;
                                        search = '';
                                    "
                                         class="px-4 py-2 text-sm text-black cursor-pointer mahasiswa-dropdown-option border-b last:border-none"
                                         x-show="search === '' || '{{ $mahasiswa['namam'] ?? '' }} {{ $mahasiswa['nrm'] ?? '' }}'.toLowerCase().includes(search.toLowerCase())">
                                        <div class="font-medium">{{ $mahasiswa['namam'] ?? '' }}</div>
                                        <div class="text-gray-600 text-xs">NRM: {{ $mahasiswa['nrm'] ?? '' }}</div>
                                    </div>
                                @endforeach
                            @else
                                <div class="px-4 py-2 text-sm text-gray-500 italic">Tidak ada data mahasiswa tersedia</div>
                            @endif
                        </div>
                    </div>
                </div>
                @error('nrm')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>



            <!-- Kode -->
            <div class="mb-4">
                <label class="block font-medium mb-1">Kode Buku</label>
                <div class="relative" x-data="{ 
                    open: false, 
                    search: '', 
                    selectedKode: '{{ old('kode') }}', 
                    selectedJudul: ''
                }" x-init="
                    // Set selectedJudul jika ada old value
                    if (selectedKode) {
                        selectedJudul = '{{ old('kode') ? (collect($koleksis)->firstWhere('kode', old('kode'))['judul'] ?? '') : '' }}';
                    }
                ">
                    <div @click="open = !open" class="w-full border border-black rounded-lg px-3 py-2 appearance-none focus:outline-none focus:border-[#024088] cursor-pointer flex items-center justify-between @error('kode') border-red-500 @enderror" :class="{ 'border-[#024088]': open }">
                        <span x-text="selectedKode ? selectedKode + ' - ' + selectedJudul : 'Pilih Buku'" class="text-left"></span>
                        <svg class="pointer-events-none transition-transform" :class="{ 'rotate-180': open }" width="18" height="10" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M8.00016 9.6024L0 1.92021L1.99969 0L9 6.72209L16.0003 0L18 1.92021L9.99984 9.6024C9.73464 9.85698 9.375 10 9 10C8.625 10 8.26536 9.85698 8.00016 9.6024Z"
                                fill="black" />
                        </svg>
                    </div>
                    
                    <!-- Hidden input untuk form -->
                    <input type="hidden" name="kode" x-model="selectedKode" required>
                    
                    <!-- Dropdown dengan search -->
                    <div x-show="open" x-transition @click.away="open = false" class="absolute left-0 right-0 mt-2 bg-white rounded-[15px] shadow-lg border border-black overflow-hidden z-50 max-h-80">
                        <!-- Search Input -->
                        <div class="p-2 border-b border-gray-200 bg-white sticky top-0 z-10">
                            <div class="relative">
                                <input type="text" 
                                       placeholder="Cari buku..." 
                                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg book-search-input"
                                       x-model="search"
                                       @click.stop
                                       @keydown.escape="open = false; search = ''"
                                       @keydown.enter="if($refs.filteredOptions.children.length === 1) { $refs.filteredOptions.children[0].click(); }" />
                                <svg class="absolute right-2 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                        
                        <!-- Options dengan Scroll -->
                        <div class="max-h-60 overflow-y-auto" x-ref="filteredOptions">
                            @if(isset($koleksis) && is_array($koleksis) && count($koleksis) > 0)
                                @foreach($koleksis as $koleksi)
                                    <div @click="
                                        selectedKode = '{{ $koleksi['kode'] }}';
                                        selectedJudul = '{{ $koleksi['judul'] ?? '' }}';
                                        open = false;
                                        search = '';
                                        
                                        // Langsung update topik dan judul display
                                        const topikDisplay = document.getElementById('topik-display');
                                        const judulDisplay = document.getElementById('judul-display');
                                        if (topikDisplay) topikDisplay.value = '{{ $koleksi['topik'] ?? '' }}';
                                        if (judulDisplay) judulDisplay.value = '{{ $koleksi['judul'] ?? '' }}';
                                        
                                        // Trigger change event untuk kompatibilitas
                                        setTimeout(() => {
                                            const event = new Event('change', { bubbles: true });
                                            const kodeInput = document.querySelector('input[name=kode]');
                                            if (kodeInput) kodeInput.dispatchEvent(event);
                                        }, 10);
                                    "
                                         class="px-4 py-2 text-sm text-black cursor-pointer book-dropdown-option border-b last:border-none"
                                         x-show="search === '' || '{{ $koleksi['kode'] }} - {{ $koleksi['judul'] }}'.toLowerCase().includes(search.toLowerCase())"
                                         data-topik="{{ $koleksi['topik'] ?? '' }}"
                                         data-judul="{{ $koleksi['judul'] ?? '' }}">
                                        <div class="font-medium">{{ $koleksi['kode'] }}</div>
                                        <div class="text-gray-600 text-xs">{{ $koleksi['judul'] ?? '' }}</div>
                                        <div class="text-gray-500 text-xs">{{ $koleksi['topik'] ?? '' }}</div>
                                    </div>
                                @endforeach
                            @else
                                <div class="px-4 py-2 text-sm text-gray-500 italic">Tidak ada data buku tersedia</div>
                            @endif
                        </div>
                    </div>
                </div>
                <p class="text-sm text-gray-600 mt-1">Hanya buku yang tersedia yang dapat dipinjam</p>
                @error('kode')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Jatuh Tempo -->
            <div class="mb-4">
                <label class="block font-medium mb-1">Jatuh Tempo</label>
                <input type="date" name="jatuh_tempo" required
                    class="w-full border border-black rounded-lg px-3 py-2 focus:outline-none focus:border-[#024088] @error('jatuh_tempo') border-red-500 @enderror"
                    placeholder="Masukan Tanggal Jatuh Tempo"
                    value="{{ old('jatuh_tempo', date('Y-m-d', strtotime('+7 days'))) }}" />
                @error('jatuh_tempo')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Topik -->
            <div class="mb-4">
                <label class="block font-medium mb-1">Topik</label>
                <input type="text" name="topik" readonly
                    class="w-full border border-black rounded-lg px-3 py-2 bg-gray-100 text-gray-600"
                    placeholder="Topik akan terisi otomatis"
                    id="topik-display"
                    value="{{ old('kode') ? (collect($koleksis)->firstWhere('kode', old('kode'))['topik'] ?? '') : '' }}" />
                <p class="text-sm text-gray-600 mt-1">Topik otomatis terisi berdasarkan buku yang dipilih</p>
            </div>

            <!-- Judul -->
            <div class="mb-4">
                <label class="block font-medium mb-1">Judul</label>
                <input type="text" name="judul" readonly
                    class="w-full border border-black rounded-lg px-3 py-2 bg-gray-100 text-gray-600"
                    placeholder="Judul akan terisi otomatis"
                    id="judul-display"
                    value="{{ old('kode') ? (collect($koleksis)->firstWhere('kode', old('kode'))['judul'] ?? '') : '' }}" />
            </div>

            <!-- Tombol -->
            <div class="flex justify-between gap-4 mt-8">
                <button type="button" onclick="closeModal()"
                    class="flex-1 border border-black rounded-[30px] py-2 font-medium hover:bg-gray-100">Batalkan</button>
                <button type="submit" id="submit-btn"
                    class="flex-1 bg-[#1976c5] text-white rounded-[30px] py-2 font-medium hover:bg-[#125a96] flex items-center justify-center">
                    <span class="submit-text">Tambah</span>
                    <svg class="submit-loading hidden animate-spin ml-2 h-4 w-4 text-white"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </div>
        </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('aktivitas-form');
    const submitBtn = document.getElementById('submit-btn');
    const submitText = submitBtn.querySelector('.submit-text');
    const submitLoading = submitBtn.querySelector('.submit-loading');
    const topikDisplay = document.getElementById('topik-display');
    const judulDisplay = document.getElementById('judul-display');
    
    // Function untuk update topik dan judul berdasarkan kode
    function updateTopikJudul(kode) {
        if (!kode) {
            // Clear fields jika kode kosong
            if (topikDisplay) topikDisplay.value = '';
            if (judulDisplay) judulDisplay.value = '';
            return;
        }
        
        const koleksiData = JSON.parse('{!! json_encode($koleksis ?? []) !!}');
        const selectedKoleksi = koleksiData.find(k => k.kode === kode);
        
        if (selectedKoleksi) {
            const topik = selectedKoleksi.topik || '';
            const judul = selectedKoleksi.judul || '';
            
            // Force update dengan setTimeout untuk memastikan DOM sudah siap
            setTimeout(() => {
                if (topikDisplay) {
                    topikDisplay.value = topik;
                    topikDisplay.dispatchEvent(new Event('input', { bubbles: true }));
                }
                if (judulDisplay) {
                    judulDisplay.value = judul;
                    judulDisplay.dispatchEvent(new Event('input', { bubbles: true }));
                }
            }, 0);
        } else {
            // Clear fields jika kode tidak ditemukan
            if (topikDisplay) topikDisplay.value = '';
            if (judulDisplay) judulDisplay.value = '';
        }
    }
    
    // Update topik dan judul saat modal dibuka dengan data yang sudah ada
    const initialKode = document.querySelector('input[name="kode"]').value;
    if (initialKode) {
        updateTopikJudul(initialKode);
    }

    // Event listener untuk mengisi topik dan judul berdasarkan kode yang dipilih
    const kodeInput = document.querySelector('input[name="kode"]');
    if (kodeInput) {
        // Multiple event listeners untuk memastikan update terjadi
        kodeInput.addEventListener('change', function() {
            updateTopikJudul(this.value);
        });
        
        kodeInput.addEventListener('input', function() {
            updateTopikJudul(this.value);
        });
        
        // Observer untuk memantau perubahan value
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
                    updateTopikJudul(kodeInput.value);
                }
            });
        });
        
        observer.observe(kodeInput, {
            attributes: true,
            attributeFilter: ['value']
        });
    }
    
    // Event listener untuk dropdown option yang diklik
    document.addEventListener('click', function(e) {
        if (e.target.closest('.book-dropdown-option')) {
            const option = e.target.closest('.book-dropdown-option');
            const kode = option.querySelector('.font-medium').textContent.trim();
            updateTopikJudul(kode);
        }
    });
    
    // Event listener untuk memantau perubahan Alpine.js data
    document.addEventListener('alpine:init', function() {
        // Monitor perubahan pada Alpine.js component
        const dropdownContainer = document.querySelector('[x-data*="selectedKode"]');
        if (dropdownContainer && dropdownContainer.__x) {
            const originalSetSelectedKode = dropdownContainer.__x.$data.selectedKode;
            
            // Override setter untuk selectedKode
            Object.defineProperty(dropdownContainer.__x.$data, 'selectedKode', {
                get: function() {
                    return originalSetSelectedKode;
                },
                set: function(value) {
                    originalSetSelectedKode = value;
                    updateTopikJudul(value);
                }
            });
        }
    });
    
    // Event listener untuk memantau perubahan pada Alpine.js dengan interval
    setInterval(function() {
        const kodeInput = document.querySelector('input[name="kode"]');
        if (kodeInput && kodeInput.value !== kodeInput.getAttribute('data-last-value')) {
            kodeInput.setAttribute('data-last-value', kodeInput.value);
            updateTopikJudul(kodeInput.value);
        }
    }, 100);
    


    if (form) {
        form.addEventListener('submit', function(e) {
            // Prevent default hanya jika form tidak valid
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('border-red-500');
                } else {
                    field.classList.remove('border-red-500');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Mohon lengkapi semua field yang wajib diisi');
                return;
            }
            
            // Cek apakah ada buku yang dipilih
            const kodeInput = form.querySelector('input[name="kode"]');
            if (!kodeInput || !kodeInput.value.trim()) {
                e.preventDefault();
                alert('Mohon pilih buku yang akan dipinjam');
                return;
            }
            
            submitBtn.disabled = true;
            submitText.textContent = 'Menambahkan...';
            submitLoading.classList.remove('hidden');
            
            // Setelah form berhasil di-submit, trigger event
            setTimeout(() => {
                const kode = form.querySelector('input[name="kode"]').value;
                if (kode) {
                    // Trigger event untuk update status koleksi
                    window.dispatchEvent(new CustomEvent('koleksi-status-updated', {
                        detail: {
                            kode: kode,
                            newStatus: 'Dipinjam'
                        }
                    }));
                    console.log('Aktivitas created event dispatched for kode:', kode);
                }
            }, 500);
        });
    }
});
</script>