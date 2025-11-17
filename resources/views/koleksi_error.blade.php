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
    </style>
</head>

<body class="min-h-screen bg-white" x-data="{ showModal: false }">
    <!-- Loading overlay -->
    <div id="loading-overlay" class="page-loading">
        <div class="loading-spinner"></div>
    </div>
    
    <div class="flex h-screen overflow-hidden fade-in" style="display: none;" id="main-content">
        <x-sidebar>

        </x-sidebar>

        <div class="flex-1 p-4 md:p-8 flex flex-col gap-6">
            <!-- Flash Messages -->
            @if(session('success'))
                <div id="success-message" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div id="error-message" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="flex flex-wrap justify-start items-center gap-4">
                <div
                    class="flex items-center h-[44px] w-full sm:w-[300px] pl-4 pr-2 rounded-[30px] border border-black bg-white">
                    <div class="flex items-center gap-2 md:gap-3">
                        <svg width="20" height="20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="9" cy="9" r="8" stroke="black" stroke-width="2" />
                            <line x1="15.5" y1="15" x2="19" y2="19" stroke="black" stroke-width="2"
                                stroke-linecap="round" />
                        </svg>
                        <svg width="2" height="30" viewBox="0 0 2 30" fill="none" xmlns="http://www.w3.org/2000/svg"
                            class="h-5 md:h-[30px] flex-shrink-0" preserveAspectRatio="none"> {{-- Sesuaikan tinggi
                            --}}
                            <line x1="0.550049" x2="0.550049" y2="30" stroke="black" stroke-opacity="0.7">
                            </line>
                        </svg>
                    </div>
                    <input type="text" placeholder="Pencarian"
                        class="w-full h-full pl-2 outline-none bg-transparent text-base placeholder:text-gray-500" />
                </div>
                <div x-data="{ open: false, selected: 'Kategori:' }" class="relative w-full sm:w-[180px]">
                    <div @click="open = !open"
                        class="flex items-center h-[44px] px-4 rounded-[30px] border border-black bg-white cursor-pointer">
                        <span class="text-base text-black" x-text="selected"></span>
                        <svg width="18" height="10" fill="none" xmlns="http://www.w3.org/2000/svg"
                            class="ml-auto transition-transform" :class="{ 'rotate-180': open }">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M8.00016 9.6024L0 1.92021L1.99969 0L9 6.72209L16.0003 0L18 1.92021L9.99984 9.6024C9.73464 9.85698 9.375 10 9 10C8.625 10 8.26536 9.85698 8.00016 9.6024Z"
                                fill="black" />
                        </svg>
                    </div>
                    <div x-show="open" x-transition @click.away="open = false"
                        class="absolute left-0 right-0 mt-2 bg-white rounded-[15px] shadow-lg border border-black overflow-hidden z-50">
                        <template x-for="option in ['Jurnal', 'Buku', 'Skripsi']" :key="option">
                            <div @click="selected = option; open = false"
                                class="px-4 py-2 text-sm text-black cursor-pointer hover:bg-gray-100 border-b last:border-none"
                                x-text="option"></div>
                        </template>
                    </div>
                </div>
                <div
                    class="flex items-center h-[44px] w-full sm:w-[180px] px-4 rounded-[30px] border border-black bg-white relative">
                    <span class="text-base text-black">Tahun Terbit:</span>
                    <svg width="10" height="19" fill="none" xmlns="http://www.w3.org/2000/svg" class="ml-auto">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M9.6024 10.4998L1.92021 18.5L0 16.5003L6.72209 9.5L0 2.49969L1.92021 0.5L9.6024 8.50016C9.85698 8.76536 10 9.125 10 9.5C10 9.875 9.85698 10.2346 9.6024 10.4998Z"
                            fill="black" />
                    </svg>
                </div>
                <div x-data="{ open: false, selected: 'Status:' }" class="relative w-full sm:w-[180px]">
                    <div @click="open = !open"
                        class="flex items-center h-[44px] px-4 rounded-[30px] border border-black bg-white cursor-pointer">
                        <span class="text-base text-black" x-text="selected"></span>
                        <svg width="18" height="10" fill="none" xmlns="http://www.w3.org/2000/svg"
                            class="ml-auto transition-transform" :class="{ 'rotate-180': open }">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M8.00016 9.6024L0 1.92021L1.99969 0L9 6.72209L16.0003 0L18 1.92021L9.99984 9.6024C9.73464 9.85698 9.375 10 9 10C8.625 10 8.26536 9.85698 8.00016 9.6024Z"
                                fill="black" />
                        </svg>
                    </div>
                    <div x-show="open" x-transition @click.away="open = false"
                        class="absolute left-0 right-0 mt-2 bg-white rounded-[15px] shadow-lg border border-black overflow-hidden z-50">
                        <template x-for="option in ['Dipinjam', 'Tersedia']" :key="option">
                            <div @click="selected = option; open = false"
                                class="px-4 py-2 text-sm text-black cursor-pointer hover:bg-gray-100 border-b last:border-none"
                                x-text="option"></div>
                        </template>
                    </div>
                </div>
            </div>

            <div class="w-full max-w-full overflow-x-auto h-auto p-4 md:p-6 rounded-[30px] bg-white shadow-lg"
                style="box-shadow: 0px -4px 16px 0 rgba(0,0,0,0.1);">
                <div class="flex justify-between items-center mb-6">
                    <div class="flex items-center gap-4">
                        <p class="text-xl font-bold text-black">
                            Kelola Koleksi
                        </p>
                        <!-- Indikator item yang dipilih -->
                        <div x-show="checkedRows.length > 0" 
                             x-transition
                             class="flex items-center gap-2 px-3 py-1 bg-red-100 border border-red-300 rounded-full">
                            <span class="text-sm font-medium text-red-700">
                                <span x-text="checkedRows.length"></span> item dipilih
                            </span>
                            <button @click="clearAll()" 
                                    class="text-red-500 hover:text-red-700 text-sm font-bold">
                                ×
                            </button>
                        </div>
                    </div>
                </div>

                <div x-data="{
                        checkedRows: [],
                        toggleRow(id) {
                            const index = this.checkedRows.indexOf(id);
                            if (index === -1) this.checkedRows.push(id);
                            else this.checkedRows.splice(index, 1);
                        },
                        clearAll() { this.checkedRows = [] },
                        async deleteSelected() {
                            if (this.checkedRows.length === 0) return;
                            
                            if (!confirm('Apakah Anda yakin ingin menghapus ' + this.checkedRows.length + ' item yang dipilih?')) {
                                return;
                            }
                            
                            try {
                                let successCount = 0;
                                let errorCount = 0;
                                let errorMessages = [];
                                
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
                                    alert('Berhasil menghapus ' + successCount + ' item' + (successCount > 1 ? 's' : ''));
                                    if (errorCount > 0) {
                                        alert('Gagal menghapus ' + errorCount + ' item' + (errorCount > 1 ? 's' : '') + ':\n' + errorMessages.join('\n'));
                                    }
                                    // Clear checked rows setelah berhasil delete
                                    this.checkedRows = [];
                                } else {
                                    alert('Gagal menghapus semua item yang dipilih:\n' + errorMessages.join('\n'));
                                }
                            } catch (error) {
                                console.error('Error:', error);
                                alert('Terjadi kesalahan saat menghapus item: ' + error.message);
                            }
                        }
                    }">

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th
                                        class="px-5 py-3 text-left text-s font-large text-gray-500 uppercase tracking-wider">
                                        Checkbox
                                    </th>
                                    <th
                                        class="px-5 py-3 text-left text-s font-large text-gray-500 uppercase tracking-wider">
                                        Kategori
                                    </th>
                                    <th
                                        class="px-5 py-3 text-left text-s font-large text-gray-500 uppercase tracking-wider">
                                        Kode
                                    </th>
                                    <th
                                        class="px-5 py-3 text-left text-s font-large text-gray-500 uppercase tracking-wider">
                                        Tahun
                                    </th>
                                    <th
                                        class="min-w-[150px] px-5 py-3 text-left text-s font-large text-gray-500 uppercase tracking-wider">
                                        Lokasi Rak
                                    </th>
                                    <th
                                        class="px-5 py-3 text-left text-s font-large text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>

                                    <th
                                        class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
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
                                                @click="deleteSelected()"
                                                class="w-8 h-8 flex items-center justify-center rounded-full border-[3px] border-black hover:border-red-500 transition-all duration-200 group shadow-sm hover:shadow-md delete-btn"
                                                title="Hapus item yang dipilih">
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
                                @foreach($koleksis as $koleksi)
                                    <tr data-kode="{{ $koleksi['kode'] ?? '' }}">
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
                                            @if(($koleksi['status'] ?? '') == 'Dipinjam')
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
                                                <button @click="window.koleksiEventManager.showDetail({{ json_encode($koleksi) }})" class="w-8 h-8 flex items-center justify-center hover:bg-gray-200 rounded-md">
                                                    <svg width="30" height="37" viewBox="0 0 30 37" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-7 h-[35px]" preserveAspectRatio="xMidYMid meet">
                                                        <path d="M18.5 23.75L21.125 26.375M8.875 19.375C8.875 20.7674 9.42812 22.1027 10.4127 23.0873C11.3973 24.0719 12.7326 24.625 14.125 24.625C15.5174 24.625 16.8527 24.0719 17.8373 23.0873C18.8219 22.1027 19.375 20.7674 19.375 19.375C19.375 17.9826 18.8219 16.6473 17.8373 15.6627C16.8527 14.6781 15.5174 14.125 14.125 14.125C12.7326 14.125 11.3973 14.6781 10.4127 15.6627C9.42812 16.6473 8.875 17.9826 8.875 19.375Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                                        <path d="M1 34.95V2.05C1 1.77152 1.11062 1.50445 1.30754 1.30754C1.50445 1.11062 1.77152 1 2.05 1H22.441C22.7194 1.00025 22.9863 1.11103 23.183 1.308L28.692 6.817C28.79 6.91482 28.8676 7.03105 28.9204 7.15899C28.9733 7.28694 29.0003 7.42407 29 7.5625V34.95C29 35.0879 28.9729 35.2244 28.9201 35.3518C28.8673 35.4792 28.79 35.595 28.6925 35.6925C28.595 35.79 28.4792 35.8673 28.3518 35.9201C28.2244 35.9728 28.0879 36 27.95 36H2.05C1.91211 36 1.77557 35.9728 1.64818 35.9201C1.52079 35.8673 1.40504 35.79 1.30754 35.6925C1.21004 35.595 1.13269 35.4792 1.07993 35.3518C1.02716 35.2244 1 35.0879 1 34.95Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                                        <path d="M22 1V6.95C22 7.22848 22.1106 7.49555 22.3075 7.69246C22.5045 7.88937 22.7715 8 23.05 8H29" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                                    </svg>
                                                </button>
                                                <button @click="window.koleksiEventManager.showEdit({{ json_encode($koleksi) }})" class="w-8 h-8 flex items-center justify-center hover:bg-gray-200 rounded-md">
                                                    <svg width="37" height="37" viewBox="0 0 37 37" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 cursor-pointer" preserveAspectRatio="xMidYMid meet">
                                                        <path d="M26.684 5.95734L31.0426 10.3159M29.4861 2.1176L17.6951 13.9086C17.0841 14.5156 16.6683 15.2913 16.501 16.1363L15.4119 21.5881L20.8637 20.4969C21.7078 20.3281 22.4819 19.9142 23.0914 19.3048L34.8824 7.51383C35.2367 7.15951 35.5177 6.73887 35.7095 6.27592C35.9013 5.81298 36 5.3168 36 4.81571C36 4.31463 35.9013 3.81845 35.7095 3.3555C35.5177 2.89256 35.2367 2.47192 34.8824 2.1176C34.528 1.76327 34.1074 1.48221 33.6445 1.29045C33.1815 1.0987 32.6853 1 32.1842 1C31.6832 1 31.187 1.0987 30.724 1.29045C30.2611 1.48221 29.8404 1.76327 29.4861 2.1176Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                                        <path d="M31.8827 25.7058V31.8823C31.8827 32.9744 31.4488 34.0217 30.6766 34.7939C29.9044 35.5662 28.8571 36 27.765 36H5.11769C4.02561 36 2.97826 35.5662 2.20604 34.7939C1.43383 34.0217 1 32.9744 1 31.8823V9.235C1 8.14292 1.43383 7.09557 2.20604 6.32335C2.97826 5.55114 4.02561 5.11731 5.11769 5.11731H11.2942" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <x-modal-tambah></x-modal-tambah>
        <x-modal-edit></x-modal-edit>
        <x-modal-detail></x-modal-detail>
    </div>
</body>

<script>
// Page loading optimization
document.addEventListener('DOMContentLoaded', function() {
    // Hide loading overlay and show content
    const loadingOverlay = document.getElementById('loading-overlay');
    const mainContent = document.getElementById('main-content');
    
    setTimeout(() => {
        if (loadingOverlay) loadingOverlay.style.display = 'none';
        if (mainContent) {
            mainContent.style.display = 'flex';
        }
    }, 500);
    
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
    
    // Optimized: Hanya refresh jika benar-benar diperlukan
    if (window.location.search.includes('force-refresh=true')) {
        // Clear URL parameter dan reload dengan throttling
        const url = new URL(window.location);
        url.searchParams.delete('force-refresh');
        window.history.replaceState(null, '', url);
        
        // Delay untuk menghindari multiple reload
        setTimeout(() => {
            window.location.reload();
        }, 100);
    }
    
    // Listen untuk update data setelah edit berhasil
    window.addEventListener('koleksi-updated', (event) => {
        // Update data di tabel
        if (window.updateTableRow) {
            window.updateTableRow(event.detail);
        }
    });
    
    // Listen untuk remove row dari tabel
    window.addEventListener('remove-table-row', (event) => {
        if (window.removeTableRow) {
            window.removeTableRow(event.detail.kode);
        }
    });
});
</script>


</html>