<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Kelola Koleksi</title>
    
    <!-- Preload untuk performa -->
    <link rel="dns-prefetch" href="//backend:5000">
    <link rel="preconnect" href="//backend:5000">
    
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/api.js', 'resources/js/koleksi.js'])
    <style>
        [x-cloak] {
            display: none !important;
        }
        
        /* Custom styles untuk button delete */
        .delete-btn {
            background: white;
            transition: all 0.2s ease;
        }
        
        .delete-btn:hover {
            transform: scale(1.05);
        }
        
        .delete-btn:active {
            transform: scale(0.95);
        }
        
        /* Custom styles untuk button add */
        .add-btn {
            background: white;
            transition: all 0.2s ease;
        }
        
        .add-btn:hover {
            transform: scale(1.05);
        }
        
        .add-btn:active {
            transform: scale(0.95);
        }
        
        /* Loading indicator */
        .loading-spinner {
            border: 2px solid #f3f3f3;
            border-top: 2px solid #024088;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Page loading overlay */
        .page-loading {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        
        /* Fade in animation */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        /* Filter dropdown styles */
        .filter-dropdown {
            z-index: 50;
        }
        
        .filter-dropdown .filter-option {
            transition: background-color 0.2s ease;
        }
        
        .filter-dropdown .filter-option:hover {
            background-color: #f3f4f6;
        }
        
        /* Responsive filter container */
        @media (max-width: 640px) {
            .filter-container {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .filter-container > div {
                width: 100%;
            }
        }
        
        /* Year dropdown search styles */
        .year-search-input {
            transition: border-color 0.2s ease;
        }
        
        .year-search-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
        }
        
        .year-dropdown-option {
            transition: background-color 0.15s ease;
        }
        
        .year-dropdown-option:hover {
            background-color: #f3f4f6;
        }
        
        .year-dropdown-option:active {
            background-color: #e5e7eb;
        }
        
        /* Scrollbar styling untuk dropdown */
        .year-dropdown-scroll::-webkit-scrollbar {
            width: 8px;
        }
        
        .year-dropdown-scroll::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
            margin: 2px;
        }
        
        .year-dropdown-scroll::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
            border: 1px solid #f1f1f1;
        }
        
        .year-dropdown-scroll::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
        
        .year-dropdown-scroll::-webkit-scrollbar-thumb:active {
            background: #888;
        }
        
        /* Firefox scrollbar */
        .year-dropdown-scroll {
            scrollbar-width: thin;
            scrollbar-color: #c1c1c1 #f1f1f1;
            scroll-behavior: smooth;
            overflow-x: hidden !important;
            overflow-y: auto !important;
        }
        
        /* Smooth scroll untuk semua browser */
        .year-dropdown-scroll {
            -webkit-overflow-scrolling: touch;
        }
        
        /* Focus styles untuk accessibility */
        .year-dropdown-option:focus {
            background-color: #dbeafe !important;
            outline: 2px solid #3b82f6;
            outline-offset: -2px;
        }
        
        /* Hover effect yang lebih smooth */
        .year-dropdown-option {
            transition: background-color 0.15s ease;
        }
        
        .year-dropdown-option:hover {
            background-color: #f3f4f6;
        }
        
        /* Responsive styling untuk dropdown search */
        @media (max-width: 640px) {
            .year-search-input {
                font-size: 16px; /* Mencegah zoom di iOS */
            }
            
            .year-dropdown-scroll {
                max-height: 200px;
                -webkit-overflow-scrolling: touch;
                overflow-x: hidden !important;
                overflow-y: auto !important;
            }
            
            /* Mobile scrollbar */
            .year-dropdown-scroll::-webkit-scrollbar {
                width: 6px;
            }
        }
        
        /* Desktop enhancements */
        @media (min-width: 641px) {
            .year-dropdown-scroll {
                max-height: 240px;
                overflow-x: hidden !important;
                overflow-y: auto !important;
            }
        }
        
        /* Ensure proper scroll behavior */
        .year-dropdown-scroll {
            direction: ltr;
            writing-mode: horizontal-tb;
        }
    </style>
</head>

<body class="min-h-screen bg-white" x-data="{ showModal: false }">
    <!-- Loading overlay -->
    <div id="loading-overlay" class="page-loading">
        <div class="loading-spinner"></div>
    </div>
    
    <div class="flex h-screen overflow-hidden fade-in" style="display: none;" id="main-content">
        <x-sidebar></x-sidebar>

        <div class="flex-1 p-4 md:p-8 flex flex-col gap-6">
            <!-- Pesan Sukses dan Error -->
            @if(session('success'))
                <div id="success-message" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-400 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 1 1-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <div class="flex-1">
                            <p class="font-medium">Berhasil!</p>
                            <p class="text-sm">{{ session('success') }}</p>
                        </div>
                        <button type="button" class="ml-3" onclick="this.parentElement.parentElement.style.display='none'">
                            <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <title>Close</title>
                                <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1 1 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div id="error-message" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none'">
                        <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <title>Close</title>
                            <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                        </svg>
                    </button>
                </div>
            @endif

            <!-- Filter dan Pencarian -->
            <div class="flex flex-wrap justify-start items-center gap-4 filter-container" x-data="filterData()" x-init="init()">
                <!-- Search Input -->
                <div class="flex items-center h-[44px] w-full sm:w-[300px] pl-4 pr-2 rounded-[30px] border border-black bg-white">
                    <div class="flex items-center gap-2 md:gap-3">
                        <svg width="20" height="20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="9" cy="9" r="8" stroke="black" stroke-width="2" />
                            <line x1="15.5" y1="15" x2="19" y2="19" stroke="black" stroke-width="2" stroke-linecap="round" />
                        </svg>
                        <svg width="2" height="30" viewBox="0 0 2 30" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-5 md:h-[30px] flex-shrink-0" preserveAspectRatio="none">
                            <line x1="0.550049" x2="0.550049" y2="30" stroke="black" stroke-opacity="0.7"></line>
                        </svg>
                    </div>
                    <input type="text" 
                           placeholder="Pencarian" 
                           class="w-full h-full pl-2 outline-none bg-transparent text-base placeholder:text-gray-500" 
                           x-model="searchTerm"
                           @input.debounce.300ms="applyFilters()" />
                </div>
                
                <!-- Kategori Dropdown -->
                <div class="relative w-full sm:w-[180px]">
                    <div @click="kategoriOpen = !kategoriOpen" class="flex items-center h-[44px] px-4 rounded-[30px] border border-black bg-white cursor-pointer">
                        <span class="text-base text-black" x-text="selectedKategori || 'Kategori:'"></span>
                        <svg width="18" height="10" fill="none" xmlns="http://www.w3.org/2000/svg" class="ml-auto transition-transform" :class="{ 'rotate-180': kategoriOpen }">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M8.00016 9.6024L0 1.92021L1.99969 0L9 6.72209L16.0003 0L18 1.92021L9.99984 9.6024C9.73464 9.85698 9.375 10 9 10C8.625 10 8.26536 9.85698 8.00016 9.6024Z" fill="black" />
                        </svg>
                    </div>
                    <div x-show="kategoriOpen" x-transition @click.away="kategoriOpen = false" class="absolute left-0 right-0 mt-2 bg-white rounded-[15px] shadow-lg border border-black overflow-hidden z-50">
                        <div @click="selectedKategori = null; kategoriOpen = false; applyFilters(); console.log('Semua Kategori clicked')" class="px-4 py-2 text-sm text-black cursor-pointer hover:bg-gray-100 border-b">Semua Kategori</div>
                        <template x-for="option in availableKategoris" :key="option">
                            <div @click="selectedKategori = option; kategoriOpen = false; applyFilters(); console.log('Kategori selected:', option)" class="px-4 py-2 text-sm text-black cursor-pointer hover:bg-gray-100 border-b last:border-none" x-text="option"></div>
                        </template>
                    </div>
                </div>
                
                <!-- Tahun Terbit Dropdown -->
                <div class="relative w-full sm:w-[180px]">
                    <div @click="tahunOpen = !tahunOpen; focusedIndex = -1" class="flex items-center h-[44px] px-4 rounded-[30px] border border-black bg-white cursor-pointer">
                        <span class="text-base text-black" x-text="selectedTahun || 'Tahun Terbit:'"></span>
                        <svg width="18" height="10" fill="none" xmlns="http://www.w3.org/2000/svg" class="ml-auto transition-transform" :class="{ 'rotate-180': tahunOpen }">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M8.00016 9.6024L0 1.92021L1.99969 0L9 6.72209L16.0003 0L18 1.92021L9.99984 9.6024C9.73464 9.85698 9.375 10 9 10C8.625 10 8.26536 9.85698 8.00016 9.6024Z" fill="black" />
                        </svg>
                    </div>
                    <div x-show="tahunOpen" x-transition @click.away="tahunOpen = false; searchYear = ''" class="absolute left-0 right-0 mt-2 bg-white rounded-[15px] shadow-lg border border-black overflow-hidden z-50 max-h-80" style="overflow-x: hidden;">
                        <!-- Search Input untuk Tahun -->
                        <div class="p-2 border-b border-gray-200 bg-white sticky top-0 z-10">
                            <div class="relative">
                                <input type="text" 
                                       placeholder="Ketik tahun" 
                                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 year-search-input"
                                       x-model="searchYear"
                                       @click.stop
                                       @input.debounce.200ms="searchYear = $event.target.value"
                                       @keydown.escape="tahunOpen = false; searchYear = ''"
                                       @keydown.enter="if(availableYears.filter(y => y.toString().includes(searchYear)).length === 1) { selectedTahun = availableYears.filter(y => y.toString().includes(searchYear))[0]; tahunOpen = false; searchYear = ''; applyFilters(); }" />
                                <svg class="absolute right-2 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <!-- Info hasil pencarian -->
                            <div x-show="searchYear !== ''" class="mt-1 text-xs text-gray-500">
                                <span x-text="`${availableYears.filter(y => y.toString().includes(searchYear)).length} tahun ditemukan`"></span>
                                <span x-show="availableYears.filter(y => y.toString().includes(searchYear)).length === 0" class="text-red-500"> - Coba kata kunci lain</span>
                            </div>
                            <div x-show="searchYear === ''" class="mt-1 text-xs text-gray-400">
                                Tekan Esc untuk menutup
                            </div>
                        </div>
                        
                        <!-- Tahun Options dengan Scroll -->
                        <div class="max-h-60 overflow-y-auto year-dropdown-scroll" 
                             style="scrollbar-width: thin; overflow-x: hidden;"
                             @keydown.arrow-down.prevent="focusNext()"
                             @keydown.arrow-up.prevent="focusPrevious()"
                             @keydown.enter.prevent="selectFocused()"
                             @scroll="handleScroll($event)"
                             tabindex="0">
                            <div @click="selectedTahun = null; tahunOpen = false; searchYear = ''; applyFilters()" 
                                 class="px-4 py-2 text-sm text-black cursor-pointer year-dropdown-option border-b font-medium hover:bg-gray-100 focus:bg-blue-50 focus:outline-none"
                                 tabindex="0">Semua Tahun</div>
                            
                            <!-- Tahun yang sering digunakan (jika ada) -->
                            <template x-if="searchYear === '' && availableYears.length > 0">
                                <div class="px-4 py-2 text-xs text-gray-500 bg-gray-50 border-b">
                                    Tahun Terbaru:
                                </div>
                            </template>
                            
                            <template x-for="(year, index) in availableYears.filter(y => y.toString().includes(searchYear))" :key="year">
                                <div @click="selectedTahun = year; tahunOpen = false; searchYear = ''; applyFilters()" 
                                     class="px-4 py-2 text-sm text-black cursor-pointer year-dropdown-option border-b last:border-none hover:bg-gray-100 focus:bg-blue-50 focus:outline-none" 
                                     x-text="year"
                                     :tabindex="index + 1"></div>
                            </template>
                            
                            <!-- No results message -->
                            <div x-show="availableYears.filter(y => y.toString().includes(searchYear)).length === 0 && searchYear !== ''" class="px-4 py-2 text-sm text-gray-500 italic">
                                Tidak ada tahun yang cocok
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Status Dropdown -->
                <div class="relative w-full sm:w-[180px]">
                    <div @click="statusOpen = !statusOpen" class="flex items-center h-[44px] px-4 rounded-[30px] border border-black bg-white cursor-pointer">
                        <span class="text-base text-black" x-text="selectedStatus || 'Status:'"></span>
                        <svg width="18" height="10" fill="none" xmlns="http://www.w3.org/2000/svg" class="ml-auto transition-transform" :class="{ 'rotate-180': statusOpen }">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M8.00016 9.6024L0 1.92021L1.99969 0L9 6.72209L16.0003 0L18 1.92021L9.99984 9.6024C9.73464 9.85698 9.375 10 9 10C8.625 10 8.26536 9.85698 8.00016 9.6024Z" fill="black" />
                        </svg>
                    </div>
                    <div x-show="statusOpen" x-transition @click.away="statusOpen = false" class="absolute left-0 right-0 mt-2 bg-white rounded-[15px] shadow-lg border border-black overflow-hidden z-50">
                        <div @click="selectedStatus = null; statusOpen = false; applyFilters()" class="px-4 py-2 text-sm text-black cursor-pointer hover:bg-gray-100 border-b">Semua Status</div>
                        <template x-for="option in availableStatuses" :key="option">
                            <div @click="selectedStatus = option; statusOpen = false; applyFilters()" class="px-4 py-2 text-sm text-black cursor-pointer hover:bg-gray-100 border-b last:border-none" x-text="option"></div>
                        </template>
                    </div>
                </div>
                
                <!-- Reset Filter Button -->
                <button @click="resetFilters()" 
                        class="h-[44px] px-4 rounded-[30px] border border-black bg-white hover:bg-gray-100 transition-colors duration-200 text-base text-black">
                    Reset Filter
                </button>
            </div>

            <div class="w-full max-w-full overflow-x-auto h-auto p-4 md:p-6 rounded-[30px] bg-white shadow-lg"
                style="box-shadow: 0px -4px 16px 0 rgba(0,0,0,0.1);">
                <div class="flex justify-between items-center mb-6">
                    <div class="flex items-center gap-4">
                        <p class="text-xl font-bold text-black">
                            Kelola Koleksi
                        </p>
                        <span id="result-counter" class="text-sm text-gray-600">
                            Menampilkan {{ count($koleksis ?? []) }} koleksi
                        </span>
                    </div>
                </div>

                <div id="koleksi-table" x-data="{
                        checkedRows: [],
                        isDeleting: false,
                        toggleRow(id) {
                            const index = this.checkedRows.indexOf(id);
                            if (index === -1) this.checkedRows.push(id);
                            else this.checkedRows.splice(index, 1);
                        },
                        clearAll() { this.checkedRows = [] },
                        async deleteSelected() {
                            if (this.checkedRows.length === 0) return;
                            
                            // Set loading state
                            this.isDeleting = true;
                            
                            // Update tombol konfirmasi untuk menunjukkan loading
                            const confirmBtn = document.getElementById('confirm-delete-btn');
                            if (confirmBtn) {
                                confirmBtn.disabled = true;
                                confirmBtn.classList.add('opacity-50', 'cursor-not-allowed');
                                confirmBtn.innerHTML = 'Menghapus...';
                            }
                            
                            try {
                                let successCount = 0;
                                let errorCount = 0;
                                let errorMessages = [];
                                let successMessages = [];
                                
                                for (let kode of this.checkedRows) {
                                    console.log('Attempting to delete kode:', kode);
                                    
                                    const response = await fetch('/koleksi/' + kode, {
                                        method: 'DELETE',
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                                            'Content-Type': 'application/json',
                                        },
                                    });
                                    
                                    console.log('Response for ' + kode + ':', response.status, response.statusText);
                                    
                                    if (response.ok) {
                                        const result = await response.json();
                                        console.log('Success deleting ' + kode + ':', result);
                                        successCount++;
                                        
                                        // Simpan pesan dari controller
                                        if (result.message) {
                                            successMessages.push(result.message);
                                        }
                                        
                                        // Dispatch event untuk remove row dari tabel
                                        window.dispatchEvent(new CustomEvent('remove-table-row', {
                                            detail: { kode: kode }
                                        }));
                                    } else {
                                        const errorResult = await response.json().catch(() => ({ message: 'Unknown error' }));
                                        console.error('Failed to delete ' + kode + ':', errorResult);
                                        errorCount++;
                                        errorMessages.push(kode + ': ' + (errorResult.message || 'Unknown error'));
                                    }
                                }
                                
                                // Tampilkan pesan hasil
                                if (successCount > 0) {
                                    if (typeof showSuccessMessage === 'function') {
                                        // Gunakan pesan dari controller jika ada, fallback ke pesan default
                                        const message = successMessages.length > 0 ? 
                                            successMessages[0] : // Ambil pesan pertama dari controller
                                            'Berhasil menghapus ' + successCount + ' koleksi';
                                        showSuccessMessage(message);
                                    }
                                    if (errorCount > 0 && typeof showErrorMessage === 'function') {
                                        showErrorMessage('Gagal menghapus ' + errorCount + ' item' + (errorCount > 1 ? '' : '') + ':\n' + errorMessages.join('\n'));
                                    }
                                    // Clear checked rows setelah berhasil delete
                                    this.checkedRows = [];
                                    
                                    // Tutup modal konfirmasi setelah berhasil hapus
                                    if (typeof hideDeleteConfirmationModal === 'function') {
                                        hideDeleteConfirmationModal();
                                    }
                                } else {
                                    if (typeof showErrorMessage === 'function') {
                                        showErrorMessage('Gagal menghapus semua item yang dipilih:\n' + errorMessages.join('\n'));
                                    }
                                    // Tutup modal konfirmasi meskipun gagal
                                    if (typeof hideDeleteConfirmationModal === 'function') {
                                        hideDeleteConfirmationModal();
                                    }
                                }
                            } catch (error) {
                                console.error('Error:', error);
                                if (typeof showErrorMessage === 'function') {
                                    showErrorMessage('Terjadi kesalahan saat menghapus item: ' + error.message);
                                }
                                // Tutup modal konfirmasi meskipun terjadi error
                                if (typeof hideDeleteConfirmationModal === 'function') {
                                    hideDeleteConfirmationModal();
                                }
                            } finally {
                                // Reset loading state
                                this.isDeleting = false;
                                
                                // Reset tombol konfirmasi
                                const confirmBtn = document.getElementById('confirm-delete-btn');
                                if (confirmBtn) {
                                    confirmBtn.disabled = false;
                                    confirmBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                                    confirmBtn.innerHTML = 'Ya, Hapus';
                                }
                            }
                        }
                    }" x-init="
                        window.addEventListener('clear-checked-rows', () => {
                            this.clearAll();
                            this.$nextTick(() => {
                                const checkboxes = $el.querySelectorAll('input[type=checkbox]');
                                checkboxes.forEach(cb => { cb.checked = false; });
                            });
                        });
                    ">

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-5 py-3 text-left text-s font-large text-gray-500 uppercase tracking-wider">
                                        Checkbox
                                    </th>
                                    <th class="px-5 py-3 text-left text-s font-large text-gray-500 uppercase tracking-wider">
                                        Kategori
                                    </th>
                                    <th class="px-5 py-3 text-left text-s font-large text-gray-500 uppercase tracking-wider">
                                        Kode
                                    </th>
                                    <th class="px-5 py-3 text-left text-s font-large text-gray-500 uppercase tracking-wider">
                                        Tahun
                                    </th>
                                    <th class="min-w-[150px] px-5 py-3 text-left text-s font-large text-gray-500 uppercase tracking-wider">
                                        Lokasi Rak
                                    </th>
                                    <th class="px-5 py-3 text-left text-s font-large text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <div class="flex gap-2 justify-end items-center">
                                            <!-- Delete Button - Muncul ketika checkbox dipilih -->
                                            <button
                                                x-show="checkedRows.length > 0"
                                                x-transition:enter="transition ease-out duration-200"
                                                x-transition:enter-start="opacity-0 scale-95"
                                                x-transition:enter-end="opacity-100 scale-100"
                                                x-transition:leave="transition ease-in duration-150"
                                                x-transition:leave-start="opacity-100 scale-100"
                                                x-transition:leave-end="opacity-0 scale-95"
                                                @click="window.showDeleteConfirmationModal([...checkedRows])"
                                                :disabled="isDeleting"
                                                :class="isDeleting ? 'opacity-50 cursor-not-allowed' : ''"
                                                class="w-8 h-8 flex items-center justify-center rounded-full border-[3px] border-black hover:border-red-500 transition-all duration-200 group shadow-sm hover:shadow-md delete-btn"
                                                :title="isDeleting ? 'Sedang menghapus...' : 'Hapus item yang dipilih'">
                                                <svg class="w-7 h-7 transition-all duration-200 group-hover:stroke-red-500" 
                                                     fill="none" 
                                                     viewBox="0 0 24 24"
                                                     stroke="black" 
                                                     stroke-width="2">
                                                    <path stroke-linecap="round" 
                                                          stroke-linejoin="round"
                                                          d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                            <!-- Add Button -->
                                            <button
                                                @click="window.dispatchEvent(new CustomEvent('show-tambah-koleksi'))"
                                                class="w-8 h-8 flex items-center justify-center rounded-full border-[3px] border-black hover:border-[#024088] transition-all duration-200 group shadow-sm hover:shadow-md add-btn"
                                                title="Tambah koleksi baru">
                                                <svg class="w-7 h-7 transition-all duration-200 group-hover:stroke-[#024088]" 
                                                     fill="none" 
                                                     viewBox="0 0 24 24"
                                                     stroke="black" 
                                                     stroke-width="2">
                                                    <path stroke-linecap="round" 
                                                          stroke-linejoin="round"
                                                          d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                </svg>
                                            </button>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @if(isset($koleksis) && is_array($koleksis))
                                @foreach($koleksis as $koleksi)
                                <tr data-kode="{{ $koleksi['kode'] ?? '' }}" 
                                    data-kategori="{{ $koleksi['kategori'] ?? '' }}"
                                    data-tahun="{{ $koleksi['tahun_terbit'] ?? '' }}"
                                    data-status="{{ $koleksi['status'] ?? 'Tersedia' }}"
                                    data-judul="{{ $koleksi['judul'] ?? '' }}"
                                    data-kode-search="{{ $koleksi['kode'] ?? '' }}"
                                    class="filterable-row">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox" 
                                               class="w-5 h-5 rounded-[5px] border border-black cursor-pointer checked:bg-[#024088] checked:border-[#024088]"
                                               @click="toggleRow('{{ $koleksi['kode'] ?? '' }}')"
                                               :checked="checkedRows.includes('{{ $koleksi['kode'] ?? '' }}')">
                                    </td>
                                    <td class="px-6 py-4 whitespace text-base font-medium text-black">{{ $koleksi['kategori'] ?? '' }}</td>
                                    <td class="px-6 py-4 whitespace text-base font-medium text-black">{{ $koleksi['kode'] ?? '' }}</td>
                                    <td class="px-6 py-4 whitespace text-base font-medium text-black">{{ $koleksi['tahun_terbit'] ?? '' }}</td>
                                    <td class="px-6 py-4 whitespace text-base font-medium text-black">{{ $koleksi['lokasi_rak'] ?? '' }}</td>
                                    <td class="px-6 py-4 whitespace">
                                        @if(strtolower($koleksi['status'] ?? '') == 'dipinjam')
                                            <div class="inline-flex justify-center items-center px-4 py-1 rounded-[30px] bg-[#ded000]/70 text-base font-medium text-black">
                                                Dipinjam
                                            </div>
                                        @else
                                            <div class="inline-flex justify-center items-center px-4 py-1 rounded-[30px] bg-[#00d836]/70 text-base font-medium text-black">
                                                Tersedia
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex gap-2 justify-end">
                                            <button @click="window.dispatchEvent(new CustomEvent('show-detail-koleksi', { detail: {{ json_encode($koleksi) }} }))" class="w-8 h-8 flex items-center justify-center hover:bg-gray-200 rounded-md">
                                                <svg width="30" height="37" viewBox="0 0 30 37" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-7 h-[35px]">
                                                    <path d="M18.5 23.75L21.125 26.375M8.875 19.375C8.875 20.7674 9.42812 22.1027 10.4127 23.0873C11.3973 24.0719 12.7326 24.625 14.125 24.625C15.5174 24.625 16.8527 24.0719 17.8373 23.0873C18.8219 22.1027 19.375 20.7674 19.375 19.375C19.375 17.9826 18.8219 16.6473 17.8373 15.6627C16.8527 14.6781 15.5174 14.125 14.125 14.125C12.7326 14.125 11.3973 14.6781 10.4127 15.6627C9.42812 16.6473 8.875 17.9826 8.875 19.375Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                                    <path d="M1 34.95V2.05C1 1.77152 1.11062 1.50445 1.30754 1.30754C1.50445 1.11062 1.77152 1 2.05 1H22.441C22.7194 1.00025 22.9863 1.11103 23.183 1.308L28.692 6.817C28.79 6.91482 28.8676 7.03105 28.9204 7.15899C28.9733 7.28694 29.0003 7.42407 29 7.5625V34.95C29 35.0879 28.9729 35.2244 28.9201 35.3518C28.8673 35.4792 28.79 35.595 28.6925 35.6925C28.595 35.79 28.4792 35.8673 28.3518 35.9201C28.2244 35.9728 28.0879 36 27.95 36H2.05C1.91211 36 1.77557 35.9728 1.64818 35.9201C1.52079 35.8673 1.40504 35.79 1.30754 35.6925C1.21004 35.595 1.13269 35.4792 1.07993 35.3518C1.02716 35.2244 1 35.0879 1 34.95Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                                    <path d="M22 1V6.95C22 7.22848 22.1106 7.49555 22.3075 7.69246C22.5045 7.88937 22.7715 8 23.05 8H29" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                                </svg>
                                            </button>
                                            <button @click="window.dispatchEvent(new CustomEvent('show-edit-koleksi', { detail: {{ json_encode($koleksi) }} }))" class="w-8 h-8 flex items-center justify-center hover:bg-gray-200 rounded-md">
                                                <svg width="37" height="37" viewBox="0 0 37 37" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 cursor-pointer">
                                                    <path d="M26.684 5.95734L31.0426 10.3159M29.4861 2.1176L17.6951 13.9086C17.0841 14.5156 16.6683 15.2913 16.501 16.1363L15.4119 21.5881L20.8637 20.4969C21.7078 20.3281 22.4819 19.9142 23.0914 19.3048L34.8824 7.51383C35.2367 7.15951 35.5177 6.73887 35.7095 6.27592C35.9013 5.81298 36 5.3168 36 4.81571C36 4.31463 35.9013 3.81845 35.7095 3.3555C35.5177 2.89256 35.2367 2.47192 34.8824 2.1176C34.528 1.76327 34.1074 1.48221 33.6445 1.29045C33.1815 1.0987 32.6853 1 32.1842 1C31.6832 1 31.187 1.0987 30.724 1.29045C30.2611 1.48221 29.8404 1.76327 29.4861 2.1176Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                                    <path d="M31.8827 25.7058V31.8823C31.8827 32.9744 31.4488 34.0217 30.6766 34.7939C29.9044 35.5662 28.8571 36 27.765 36H5.11769C4.02561 36 2.97826 35.5662 2.20604 34.7939C1.43383 34.0217 1 32.9744 1 31.8823V9.235C1 8.14292 1.43383 7.09557 2.20604 6.32335C2.97826 5.55114 4.02561 5.11731 5.11769 5.11731H11.2942" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                        Tidak ada data koleksi atau data belum tersedia.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    </div>
                </div>
        </div>
        <x-modal-tambah></x-modal-tambah>
        <x-modal-edit></x-modal-edit>
        <x-modal-detail></x-modal-detail>
    </div>
</body>

<script>
// Filter functionality
function filterData() {
    return {
        searchTerm: '',
        selectedKategori: null,
        selectedTahun: null,
        selectedStatus: null,
        availableYears: [],
        kategoriOpen: false,
        statusOpen: false,
        tahunOpen: false,
        searchYear: '',
        focusedIndex: -1,
        availableKategoris: [],
        availableStatuses: [],
        
        init() {
            this.extractAvailableYears();
            this.extractAvailableKategoris();
            this.extractAvailableStatuses();
            this.updateResultCount(); // Initial count
        },
        
        extractAvailableYears() {
            const rows = document.querySelectorAll('.filterable-row');
            const years = new Set();
            
            rows.forEach(row => {
                const tahun = row.getAttribute('data-tahun');
                if (tahun && tahun.trim() !== '') {
                    years.add(tahun);
                }
            });
            
            this.availableYears = Array.from(years).sort((a, b) => b - a); // Sort descending
        },
        
        extractAvailableKategoris() {
            const rows = document.querySelectorAll('.filterable-row');
            const kategoris = new Set();
            
            rows.forEach(row => {
                const kategori = row.getAttribute('data-kategori');
                if (kategori && kategori.trim() !== '') {
                    kategoris.add(kategori);
                }
            });
            
            this.availableKategoris = Array.from(kategoris).sort();
            console.log('Available categories:', this.availableKategoris);
        },
        
        extractAvailableStatuses() {
            const rows = document.querySelectorAll('.filterable-row');
            const statuses = new Set();
            
            rows.forEach(row => {
                const status = row.getAttribute('data-status');
                if (status && status.trim() !== '') {
                    statuses.add(status);
                }
            });
            
            this.availableStatuses = Array.from(statuses).sort();
        },
        
        applyFilters() {
            const rows = document.querySelectorAll('.filterable-row');
            
            rows.forEach(row => {
                let show = true;
                
                // Search filter
                if (this.searchTerm) {
                    const searchLower = this.searchTerm.toLowerCase();
                    const judul = row.getAttribute('data-judul') || '';
                    const kode = row.getAttribute('data-kode-search') || '';
                    
                    if (!judul.toLowerCase().includes(searchLower) && 
                        !kode.toLowerCase().includes(searchLower)) {
                        show = false;
                    }
                }
                
                // Kategori filter
                if (this.selectedKategori && show) {
                    const kategori = row.getAttribute('data-kategori') || '';
                    console.log('Checking kategori:', {
                        selected: this.selectedKategori,
                        rowKategori: kategori,
                        match: kategori === this.selectedKategori
                    });
                    if (kategori !== this.selectedKategori) {
                        show = false;
                    }
                }
                
                // Tahun filter
                if (this.selectedTahun && show) {
                    const tahun = row.getAttribute('data-tahun') || '';
                    if (tahun !== this.selectedTahun) {
                        show = false;
                    }
                }
                
                // Status filter
                if (this.selectedStatus && show) {
                    const status = row.getAttribute('data-status') || '';
                    if (status !== this.selectedStatus) {
                        show = false;
                    }
                }
                
                // Show/hide row
                if (show) {
                    row.style.display = '';
                    row.classList.remove('hidden');
                } else {
                    row.style.display = 'none';
                    row.classList.add('hidden');
                }
            });
            
            this.updateResultCount();
        },
        
        resetFilters() {
            this.searchTerm = '';
            this.selectedKategori = null;
            this.selectedTahun = null;
            this.selectedStatus = null;
            this.kategoriOpen = false;
            this.statusOpen = false;
            this.tahunOpen = false;
            this.searchYear = '';
            this.focusedIndex = -1;
            
            const rows = document.querySelectorAll('.filterable-row');
            rows.forEach(row => {
                row.style.display = '';
                row.classList.remove('hidden');
            });
            
            this.updateResultCount();
        },
        
        updateResultCount() {
            const visibleRows = document.querySelectorAll('.filterable-row:not(.hidden)');
            const totalRows = document.querySelectorAll('.filterable-row');
            
            // Update counter if exists
            const counter = document.getElementById('result-counter');
            if (counter) {
                counter.textContent = `Menampilkan ${visibleRows.length} dari ${totalRows.length} koleksi`;
            }
        },
        
        // Keyboard navigation functions for year dropdown
        focusNext() {
            const options = this.$el.querySelectorAll('.year-dropdown-option[tabindex]');
            if (options.length > 0) {
                this.focusedIndex = Math.min(this.focusedIndex + 1, options.length - 1);
                options[this.focusedIndex]?.focus();
            }
        },
        
        focusPrevious() {
            const options = this.$el.querySelectorAll('.year-dropdown-option[tabindex]');
            if (options.length > 0) {
                this.focusedIndex = Math.max(this.focusedIndex - 1, 0);
                options[this.focusedIndex]?.focus();
            }
        },
        
        selectFocused() {
            const options = this.$el.querySelectorAll('.year-dropdown-option[tabindex]');
            if (this.focusedIndex >= 0 && options[this.focusedIndex]) {
                options[this.focusedIndex].click();
            }
        },
        
        handleScroll(event) {
            // Smooth scroll handling
            const scrollContainer = event.target;
            const scrollTop = scrollContainer.scrollTop;
            const scrollHeight = scrollContainer.scrollHeight;
            const clientHeight = scrollContainer.clientHeight;
            
            // Auto-scroll to focused item if needed
            if (this.focusedIndex >= 0) {
                const options = this.$el.querySelectorAll('.year-dropdown-option[tabindex]');
                const focusedOption = options[this.focusedIndex];
                if (focusedOption) {
                    const optionTop = focusedOption.offsetTop;
                    const optionBottom = optionTop + focusedOption.offsetHeight;
                    
                    if (optionTop < scrollTop) {
                        scrollContainer.scrollTo({
                            top: optionTop - 10,
                            behavior: 'smooth'
                        });
                    } else if (optionBottom > scrollTop + clientHeight) {
                        scrollContainer.scrollTo({
                            top: optionBottom - clientHeight + 10,
                            behavior: 'smooth'
                        });
                    }
                }
            }
        }
    }
}

// Modal Konfirmasi Hapus (mengikuti pola sidebar/logout & return-confirmation)
function initDeleteConfirmationModal() {
    // Buat struktur modal sekali jika belum ada
    if (document.getElementById('delete-confirmation-modal')) return;
    const modalHtml = `
    <div id="delete-confirmation-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm" style="display:none;">
        <div id="delete-confirmation-modal-content" class="bg-white rounded-[30px] w-full max-w-sm mx-auto relative border border-gray-200 shadow-2xl transform transition-all duration-300 ease-out scale-95 opacity-0">
            <div class="flex items-center justify-center p-6">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-2">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M12 5a7 7 0 100 14 7 7 0 000-14z" />
                    </svg>
                </div>
            </div>
            <div class="px-6 pb-2 text-center">
                <h3 class="text-xl font-bold text-gray-900">Konfirmasi Hapus</h3>
                <p id="delete-summary" class="text-gray-600 mt-2">Apakah Anda yakin ingin menghapus item terpilih?</p>
            </div>
            <div class="flex justify-between gap-4 px-6 pb-6">
                <button type="button" onclick="hideDeleteConfirmationModal()" class="glass-effect flex-1 border border-black rounded-[30px] py-2 font-medium hover:bg-gray-100 transition-all duration-300">Batalkan</button>
                <button type="button" id="confirm-delete-btn" class="glass-effect flex-1 bg-red-600 text-white rounded-[30px] py-2 font-medium hover:bg-red-700 transition-all duration-300">Ya, Hapus</button>
            </div>
        </div>
    </div>`;
    document.body.insertAdjacentHTML('beforeend', modalHtml);

    // Event backdrop click
    const modal = document.getElementById('delete-confirmation-modal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) hideDeleteConfirmationModal();
        });
    }

    // Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const m = document.getElementById('delete-confirmation-modal');
            if (m && m.style.display === 'flex') hideDeleteConfirmationModal();
        }
    });
}

window.showDeleteConfirmationModal = function(kodeList) {
    initDeleteConfirmationModal();
    window.__pendingDeleteKodeList = Array.isArray(kodeList) ? kodeList : [];
    const count = window.__pendingDeleteKodeList.length;
    const summary = document.getElementById('delete-summary');
    if (summary) {
        summary.textContent = count > 1 ? `Apakah Anda yakin ingin menghapus ${count} koleksi terpilih?` : 'Apakah Anda yakin ingin menghapus 1 koleksi terpilih?';
    }
    const modal = document.getElementById('delete-confirmation-modal');
    const content = document.getElementById('delete-confirmation-modal-content');
    if (!modal || !content) return;
    modal.style.display = 'flex';
    setTimeout(() => {
        content.classList.remove('scale-95', 'opacity-0');
        content.classList.add('scale-100', 'opacity-100');
    }, 10);

    // Pasang handler konfirmasi yang memanggil Alpine.js method
    const confirmBtn = document.getElementById('confirm-delete-btn');
    if (confirmBtn && !confirmBtn.__bound) {
        confirmBtn.__bound = true;
        confirmBtn.addEventListener('click', () => {
            // Panggil method Alpine.js dari komponen tabel
            const tableComponent = document.querySelector('[x-data*="checkedRows"]');
            if (tableComponent && tableComponent._x_dataStack) {
                const alpineData = tableComponent._x_dataStack[0];
                if (alpineData && typeof alpineData.deleteSelected === 'function') {
                    alpineData.deleteSelected();
                }
            }
        });
    }
};

window.hideDeleteConfirmationModal = function() {
    const modal = document.getElementById('delete-confirmation-modal');
    const content = document.getElementById('delete-confirmation-modal-content');
    if (!modal || !content) return;
    content.classList.remove('scale-100', 'opacity-100');
    content.classList.add('scale-95', 'opacity-0');
    setTimeout(() => { modal.style.display = 'none'; }, 300);
};

// Function ini sudah tidak digunakan karena logic sudah dipindah ke Alpine.js x-data
// Semua logic hapus sekarang menggunakan Alpine.js di x-data untuk konsistensi

// Page loading optimization
document.addEventListener('DOMContentLoaded', function() {
    // Hide loading overlay and show content
    const loadingOverlay = document.getElementById('loading-overlay');
    const mainContent = document.getElementById('main-content');
    
    setTimeout(() => {
        // Jika sebelumnya kita minta skip overlay untuk reload halus, sembunyikan overlay segera
        let skip = false;
        try { skip = sessionStorage.getItem('skipOverlayOnce') === '1'; } catch (_) {}
        if (skip) {
            if (loadingOverlay) loadingOverlay.style.display = 'none';
            try { sessionStorage.removeItem('skipOverlayOnce'); } catch (_) {}
        } else {
            if (loadingOverlay) loadingOverlay.style.display = 'none';
        }
        if (mainContent) {
            mainContent.style.display = 'flex';
        }
    }, 200);
    
    // Auto-hide flash messages after 5 seconds
    const successMessage = document.getElementById('success-message');
    const errorMessage = document.getElementById('error-message');
    
    if (successMessage) {
        setTimeout(() => {
            successMessage.style.display = 'none';
        }, 5000);
    }
    
    if (errorMessage) {
        setTimeout(() => {
            errorMessage.style.display = 'none';
        }, 5000);
    }
    
    // Listen untuk remove row dari tabel
    window.addEventListener('remove-table-row', (event) => {
        const kode = event.detail.kode;
        const row = document.querySelector(`tr[data-kode="${kode}"]`);
        if (row) {
            row.remove();
            // Refresh filter setelah row dihapus
            setTimeout(() => {
                const filterComponent = document.querySelector('[x-data*="filterData"]');
                if (filterComponent && filterComponent.__x) {
                    filterComponent.__x.$data.updateResultCount();
                }
            }, 100);
        }
    });
    
    // Listen untuk add new row
    window.addEventListener('add-table-row', (event) => {
        const koleksiData = event.detail;
        if (window.addTableRow) {
            window.addTableRow(koleksiData);
            // Refresh filter setelah row ditambah
            setTimeout(() => {
                if (window.refreshFilter) {
                    window.refreshFilter();
                }
            }, 100);
        }
    });
    
    // Listen untuk update row
    window.addEventListener('update-table-row', (event) => {
        const updatedData = event.detail;
        if (window.updateTableRow) {
            window.updateTableRow(updatedData);
            // Refresh filter setelah row diupdate
            setTimeout(() => {
                if (window.refreshFilter) {
                    window.refreshFilter();
                }
            }, 100);
        }
    });
    
    // Listen untuk update status koleksi dari aktivitas
    window.addEventListener('koleksi-status-updated', (event) => {
        const { kode, newStatus } = event.detail;
        console.log('Koleksi status updated event received:', { kode, newStatus });
        
        // Update status di tabel koleksi
        const statusCell = document.querySelector(`tr[data-kode="${kode}"] td:nth-child(6)`);
        if (statusCell) {
            if (newStatus.toLowerCase() === 'dipinjam') {
                statusCell.innerHTML = `
                    <div class="inline-flex justify-center items-center px-4 py-1 rounded-[30px] bg-[#ded000]/70 text-base font-medium text-black">
                        Dipinjam
                    </div>
                `;
                // Update data-status attribute
                const row = statusCell.closest('tr');
                if (row) {
                    row.setAttribute('data-status', 'Dipinjam');
                }
            } else if (newStatus.toLowerCase() === 'tersedia' || newStatus.toLowerCase() === 'dikembalikan') {
                statusCell.innerHTML = `
                    <div class="inline-flex justify-center items-center px-4 py-1 rounded-[30px] bg-[#00d836]/70 text-base font-medium text-black">
                        Tersedia
                    </div>
                `;
                // Update data-status attribute
                const row = statusCell.closest('tr');
                if (row) {
                    row.setAttribute('data-status', 'Tersedia');
                }
            }
            console.log('Status updated in table for kode:', kode);
        } else {
            console.warn('Status cell not found for kode:', kode);
        }
    });
    


});

// Fungsi notifikasi global yang sama dengan aktivitas
function showSuccessMessage(message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = 'bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4';
    alertDiv.innerHTML = `
        <div class="flex items-center">
            <svg class="w-5 h-5 text-green-400 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <div class="flex-1">
                <p class="font-medium">Berhasil!</p>
                <p class="text-sm">${message}</p>
            </div>
        </div>
    `;
    
    // Masukkan alert di awal container
    const container = document.querySelector('.flex-1.p-4.md\\:p-8');
    if (container) {
        container.insertBefore(alertDiv, container.firstChild);
        
        // Auto hide setelah 3 detik
        setTimeout(() => {
            alertDiv.remove();
        }, 3000);
    }
}

function showErrorMessage(message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4';
    alertDiv.innerHTML = `
        <div class="flex items-center">
            <svg class="w-5 h-5 text-red-400 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            </svg>
            <div class="flex-1">
                <p class="font-medium">Terjadi kesalahan!</p>
                <p class="text-sm">${message}</p>
            </div>
        </div>
    `;
    
    // Masukkan alert di awal container
    const container = document.querySelector('.flex-1.p-4.md\\:p-8');
    if (container) {
        container.insertBefore(alertDiv, container.firstChild);
        
        // Auto hide setelah 5 detik
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
}
</script>

</html>
