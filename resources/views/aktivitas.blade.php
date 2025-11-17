<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Aktivitas</title>
    
    <!-- Preload untuk navigasi cepat -->
    <link rel="dns-prefetch" href="//backend:5000">
    <link rel="prefetch" href="/">
    <link rel="prefetch" href="/koleksi">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] {
            display: none !important;
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
        
        /* Status dropdown styling yang diperbaiki */
        .status-container {
            position: relative;
            display: inline-block;
        }
        
        .status-button {
            min-width: 120px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            padding: 0 12px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
            outline: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
            background: #EBD61A;
            color: #1f2937;
        }
        
        .status-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        
        .status-button:hover::before {
            left: 100%;
        }
        
        .status-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .status-button:active {
            transform: translateY(0);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .status-button:focus {
            outline: none !important;
        }
        
        .status-button.dipinjam {
            background: #EBD61A;
            color: #1f2937;
        }
        
        .status-button.dikembalikan {
            background: #EBD61A;
            color: #1f2937;
        }
        
        /* Status display untuk status dikembalikan (hanya teks) */
        .status-display {
            min-width: 120px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 12px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 14px;
            border: none;
            outline: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
            white-space: nowrap;
        }
        
        .status-display.dikembalikan {
            background: #00C20D;
            color: #000000;
        }
        
        .status-button .chevron {
            transition: transform 0.2s ease;
            flex-shrink: 0;
            filter: drop-shadow(0 1px 2px rgba(0,0,0,0.1));
            transform: rotate(180deg); /* Panah mengarah ke kiri secara default */
        }
        
        .status-button .chevron.rotated {
            transform: rotate(0deg); /* Panah mengarah ke kanan saat terbuka */
        }
        
        /* Dropdown options styling */
        .status-dropdown {
            position: absolute;
            top: 0;
            left: calc(100% + 8px);
            z-index: 9999;
            min-width: 120px;
            background: transparent;
            border-radius: 12px;
            box-shadow: none;
            border: none;
            overflow: visible;
            opacity: 0;
            visibility: hidden;
            transform: translateX(-10px);
            transition: all 0.2s ease;
            pointer-events: none;
            padding: 0;
        }
        
        .status-dropdown.show {
            opacity: 1;
            visibility: visible;
            transform: translateX(0);
            pointer-events: auto;
        }
        
        .status-dropdown::before {
            display: none;
        }
        
        .dropdown-option {
            padding: 0 12px;
            font-size: 14px;
            font-weight: 600;
            color: #000000;
            cursor: pointer;
            transition: all 0.15s ease;
            position: relative;
            overflow: hidden;
            background: #00C20D;
            border-radius: 30px;
            margin: 0;
            width: 120px;
            height: 40px;
            border: none;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            white-space: nowrap;
        }
        
        .dropdown-option::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.3s;
        }
        
        .dropdown-option:hover::before {
            left: 100%;
        }
        
        .dropdown-option:hover {
            background: #00C20D;
            color: #000000;
            transform: translateX(2px);
            opacity: 0.9;
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .dropdown-option:last-child {
            border-bottom: none;
        }
        
        .dropdown-option.dikembalikan {
            color: #000000;
        }
        
        .dropdown-option.dikembalikan:hover {
            background: #00C20D;
            color: #000000;
            opacity: 0.9;
        }
        
        .dropdown-option:focus {
            outline: none;
        }
        
        /* Memastikan tabel tidak terpengaruh oleh dropdown */
        .table-container {
            position: relative;
            overflow: visible;
        }
        
        /* Memastikan dropdown tidak terpotong oleh container */
        .table-container .status-dropdown {
            position: absolute;
            z-index: 9999;
        }
        
        td {
            position: relative;
            overflow: visible !important;
        }
        
        /* Memastikan dropdown tidak terpotong */
        .status-container {
            position: relative;
            display: inline-block;
            z-index: 10;
        }
        

        
        /* Animasi untuk status change */
        .status-change-animation {
            animation: statusPulse 0.6s ease-in-out;
        }
        
        @keyframes statusPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        /* Animasi transisi untuk perubahan status */
        .status-container > div {
            transition: all 0.3s ease;
        }
        
        .status-container > div > * {
            transition: all 0.3s ease;
        }
        
        /* Loading state untuk status update */
        .status-loading {
            opacity: 0.7;
            pointer-events: none;
        }
        
        .status-loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 16px;
            height: 16px;
            margin: -8px 0 0 -8px;
            border: 2px solid transparent;
            border-top: 2px solid currentColor;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Responsive design untuk dropdown */
        @media (max-width: 768px) {
            .status-button {
                min-width: 100px;
                height: 36px;
                font-size: 13px;
                padding: 0 10px;
            }
            
            .status-dropdown {
                min-width: 100px;
                left: calc(100% + 4px);
            }
            
            .dropdown-option {
                padding: 10px 14px;
                font-size: 13px;
            }
        }
        
        /* Focus states untuk aksesibilitas */
        .status-button:focus {
            outline: none !important;
        }
        
        .dropdown-option:focus {
            outline: none !important;
        }
        
        /* Container aktivitas dengan ukuran responsive dan scrolling yang enak dilihat */
        .aktivitas-container {
            width: 100%;
            height: auto;
            max-height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            border-radius: 30px;
            background: white;
            box-shadow: 0px -4px 16px 0 rgba(0,0,0,0.1);
            padding: 16px 24px;
        }
        
        .aktivitas-header {
            flex-shrink: 0;
            padding: 0 0 24px 0;
        }
        
        .aktivitas-content {
            flex: 1;
            overflow-y: auto;
            padding: 0;
            /* Membuat scrollbar masuk ke dalam container */
            margin-right: -8px;
            padding-right: 8px;
            scrollbar-width: thin;
            scrollbar-color: #d1d5db transparent;
            scroll-behavior: smooth;
        }
        
        /* Custom scrollbar yang enak dilihat dan masuk ke dalam container */
        .aktivitas-content::-webkit-scrollbar {
            width: 6px;
        }
        
        .aktivitas-content::-webkit-scrollbar-track {
            background: transparent;
            margin: 8px 0;
        }
        
        .aktivitas-content::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 10px;
            border: 1px solid #f3f4f6;
        }
        
        .aktivitas-content::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }
        
        .aktivitas-content::-webkit-scrollbar-thumb:active {
            background: #6b7280;
        }
        
        /* Memastikan tabel tetap rapi dengan scrollbar */
        .table-container {
            margin-right: 0;
        }
        
        /* Animasi smooth untuk scrolling */
        .aktivitas-content {
            scroll-behavior: smooth;
        }
        
        /* Responsive design untuk container */
        @media (max-width: 1024px) {
            .aktivitas-container {
                height: auto;
            }
        }
        
        @media (max-width: 768px) {
            .aktivitas-container {
                height: auto;
                padding: 12px 16px;
            }
            
            .aktivitas-header {
                padding: 0 0 16px 0;
            }
            
            .aktivitas-content {
                padding: 0;
                padding-right: 8px;
            }
        }
        
        /* Keyboard navigation support */
        .status-dropdown:focus-within {
            opacity: 1;
            visibility: visible;
            transform: translateX(0);
            pointer-events: auto;
        }
        
        /* Fallback untuk dropdown yang akan terpotong di sebelah kanan */
        .status-dropdown.dropdown-left {
            left: auto;
            right: calc(100% + 8px);
            transform: translateX(10px);
        }
        
        .status-dropdown.dropdown-left.show {
            transform: translateX(0);
        }
        
        /* Memastikan tidak ada outline di semua elemen dropdown */
        .status-dropdown *:focus {
            outline: none !important;
        }
        
        .status-container *:focus {
            outline: none !important;
        }
    </style>
</head>

<body class="min-h-screen bg-white" x-data>
    <div class="flex h-screen overflow-hidden">
        <x-sidebar>
        </x-sidebar>
        {{-- Ubah struktur dan styling main content agar sesuai gambar --}}
        {{-- Ganti div utama konten dengan padding dan lebar penuh --}}
        <div class="flex-1 p-4 md:p-8 flex flex-col gap-6">
            {{-- Alert Messages --}}
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-400 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <div class="flex-1">
                            <p class="font-medium">Berhasil!</p>
                            <p class="text-sm">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-red-400 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        <div class="flex-1">
                            <p class="font-medium">Terjadi kesalahan!</p>
                            <ul class="list-disc list-inside text-sm">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif





            {{-- Filter dan Pencarian --}}
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
                
                <!-- Topik Dropdown -->
                <div class="relative w-full sm:w-[180px]">
                    <div @click="topikOpen = !topikOpen" class="flex items-center h-[44px] px-4 rounded-[30px] border border-black bg-white cursor-pointer">
                        <span class="text-base text-black" x-text="selectedTopik || 'Topik:'"></span>
                        <svg width="18" height="10" fill="none" xmlns="http://www.w3.org/2000/svg" class="ml-auto transition-transform" :class="{ 'rotate-180': topikOpen }">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M8.00016 9.6024L0 1.92021L1.99969 0L9 6.72209L16.0003 0L18 1.92021L9.99984 9.6024C9.73464 9.85698 9.375 10 9 10C8.625 10 8.26536 9.85698 8.00016 9.6024Z" fill="black" />
                        </svg>
                    </div>
                    <div x-show="topikOpen" x-transition @click.away="topikOpen = false" class="absolute left-0 right-0 mt-2 bg-white rounded-[15px] shadow-lg border border-black overflow-hidden z-50">
                        <div @click="selectedTopik = null; topikOpen = false; applyFilters()" class="px-4 py-2 text-sm text-black cursor-pointer hover:bg-gray-100 border-b">Semua Topik</div>
                        <template x-for="option in availableTopiks" :key="option">
                            <div @click="selectedTopik = option; topikOpen = false; applyFilters()" class="px-4 py-2 text-sm text-black cursor-pointer hover:bg-gray-100 border-b last:border-none" x-text="option"></div>
                        </template>
                    </div>
                </div>
                
                <!-- Kode Buku Dropdown -->
                <div class="relative w-full sm:w-[180px]">
                    <div @click="kodeOpen = !kodeOpen" class="flex items-center h-[44px] px-4 rounded-[30px] border border-black bg-white cursor-pointer">
                        <span class="text-base text-black" x-text="selectedKode || 'Kode Buku:'"></span>
                        <svg width="18" height="10" fill="none" xmlns="http://www.w3.org/2000/svg" class="ml-auto transition-transform" :class="{ 'rotate-180': kodeOpen }">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M8.00016 9.6024L0 1.92021L1.99969 0L9 6.72209L16.0003 0L18 1.92021L9.99984 9.6024C9.73464 9.85698 9.375 10 9 10C8.625 10 8.26536 9.85698 8.00016 9.6024Z" fill="black" />
                        </svg>
                    </div>
                    <div x-show="kodeOpen" x-transition @click.away="kodeOpen = false" class="absolute left-0 right-0 mt-2 bg-white rounded-[15px] shadow-lg border border-black overflow-hidden z-50">
                        <div @click="selectedKode = null; kodeOpen = false; applyFilters()" class="px-4 py-2 text-sm text-black cursor-pointer hover:bg-gray-100 border-b">Semua Kode</div>
                        <template x-for="option in availableKodes" :key="option">
                            <div @click="selectedKode = option; kodeOpen = false; applyFilters()" class="px-4 py-2 text-sm text-black cursor-pointer hover:bg-gray-100 border-b last:border-none" x-text="option"></div>
                        </template>
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
            {{-- Card Content --}}
            <div class="aktivitas-container">
                <div class="aktivitas-header">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-4">
                            <p class="text-xl font-bold text-black">Aktivitas</p>
                            <span id="result-counter" class="text-sm text-gray-600">
                                Menampilkan {{ count($aktivitas) }} dari {{ count($aktivitas) }} aktivitas
                            </span>
                        </div>
                    </div>
                </div>
                <div class="aktivitas-content">
                    <div class="overflow-x-auto table-container">
                        <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-black/70">
                                <th
                                    class="px-2 py-3 text-left text-s font-large text-gray-500 uppercase tracking-wider">
                                    Tanggal</th>
                                <th
                                    class="px-2 py-3 text-left text-s font-large text-gray-500 uppercase tracking-wider">
                                    Jatuh Tempo</th>
                                <th
                                    class="px-2 py-3 text-left text-s font-large text-gray-500 uppercase tracking-wider">
                                    Nama</th>
                                <th
                                    class="px-2 py-3 text-left text-s font-large text-gray-500 uppercase tracking-wider">
                                    Topik</th>
                                <th
                                    class="px-2 py-3 text-left text-s font-large text-gray-500 uppercase tracking-wider">
                                    Kode</th>
                                <th
                                    class="px-2 py-3 text-left text-s font-large text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <button
                                        id="add-aktivitas-btn"
                                        onclick="openModal()"
                                        class="w-8 h-8 flex items-center justify-center rounded-full border-[3px] border-black hover:border-[#024088] transition-all duration-200 group shadow-sm hover:shadow-md add-btn"
                                        title="Tambah aktivitas baru">
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
                                </th>
                            </tr>
                        </thead>
                        <tbody id="aktivitas-table-body">
                            @forelse($aktivitas as $item)
                            @php
                                // Debug: tampilkan data item
                                if (app()->environment('local')) {
                                    error_log('Aktivitas item: ' . json_encode($item));
                                }
                            @endphp
                            <tr data-topik="{{ $item['topik'] ?? '' }}"
                                data-kode="{{ $item['kode'] ?? '' }}"
                                data-aktivitas-id="{{ $item['id_aktivitas'] }}"
                                data-status="{{ $item['status_aktivitas'] ?? 'dipinjam' }}"
                                data-nama="{{ $item['nama_mahasiswa'] ?? '' }}"
                                class="filterable-row">
                                <td class="px-2 py-4 whitespace text-base font-medium text-black">
                                    <div>
                                        <div class="font-semibold">{{ \Carbon\Carbon::parse($item['tanggal_peminjaman'])->format('d F Y') }}</div>
                                    </div>
                                </td>
                                <td class="px-2 py-4 whitespace text-base font-medium text-black">
                                    <div>
                                        <div class="font-semibold">{{ \Carbon\Carbon::parse($item['jatuh_tempo'])->format('d F Y') }}</div>
                                    </div>
                                </td>
                                <td class="px-2 py-4 whitespace text-base font-medium text-black">
                                    <div>
                                        <div class="font-semibold">{{ $item['nama_mahasiswa'] }}</div>
                                    </div>
                                </td>
                                <td class="px-2 py-4 whitespace text-base font-medium text-black">
                                    <div>
                                        <div class="font-semibold">{{ $item['topik'] ?? '-' }}</div>
                                    </div>
                                </td>
                                <td class="px-2 py-4 whitespace text-base font-medium text-black">
                                    {{ $item['kode'] }}
                                </td>
                                <td class="px-2 py-4 whitespace text-base font-medium text-black">
                                    <div class="status-container">
                                        <div x-data="{ open: false, selected: '{{ $item['status_aktivitas'] ?? 'dipinjam' }}', loading: false }" class="relative">
                                            <!-- Status untuk dipinjam (dengan dropdown) -->
                                            <template x-if="selected === 'dipinjam'">
                                                <button @click="open = !open; $event.stopPropagation()" 
                                                        :class="[
                                                            'status-button',
                                                            selected,
                                                            { 'status-loading': loading }
                                                        ]"
                                                        :disabled="loading"
                                                        class="status-button dipinjam"
                                                        data-aktivitas-id="{{ $item['id_aktivitas'] }}">
                                                    <span>Dipinjam</span>
                                                    <svg width="10" height="19" fill="none" xmlns="http://www.w3.org/2000/svg"
                                                         class="chevron"
                                                         :class="{ 'rotated': open }">
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M0.3976 8.5002L8.07979 0.5L10 2.4997L3.27791 9.5L10 16.5003L8.07979 18.5L0.3976 9.99984C0.14302 9.73464 0 9.375 0 9C0 8.625 0.14302 8.26536 0.3976 8.5002Z"
                                                              fill="currentColor" />
                                                    </svg>
                                                </button>
                                            </template>
                                            
                                            <!-- Status untuk dikembalikan (hanya teks) -->
                                            <template x-if="selected === 'dikembalikan'">
                                                <div class="status-display dikembalikan">
                                                    Dikembalikan
                                                </div>
                                            </template>
                                            
                                            <!-- Dropdown options hanya untuk status dipinjam -->
                                            <div x-show="open && selected === 'dipinjam'" 
                                                 x-transition:enter="transition ease-out duration-200"
                                                 x-transition:enter-start="opacity-0 transform scale-95"
                                                 x-transition:enter-end="opacity-100 transform scale-100"
                                                 x-transition:leave="transition ease-in duration-150"
                                                 x-transition:leave-start="opacity-100 transform scale-100"
                                                 x-transition:leave-end="opacity-0 transform scale-95"
                                                 @click.away="open = false"
                                                 class="status-dropdown"
                                                 :class="{ 'show': open }">
                                                
                                                <div @click="
                                                    // Tutup dropdown terlebih dahulu
                                                    open = false;
                                                    // Panggil updateStatus yang akan menampilkan modal konfirmasi
                                                    updateStatus('{{ $item['id_aktivitas'] }}', 'dikembalikan');
                                                "
                                                     class="dropdown-option dikembalikan">
                                                    Dikembalikan
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-2 py-8 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <p class="text-lg font-medium text-gray-500 mb-2">Tidak ada data aktivitas</p>
                                        <p class="text-sm text-gray-400">Mulai dengan menambahkan aktivitas peminjaman baru</p>
                                        <button id="add-aktivitas-btn-2"
                                            onclick="openModal()"
                                            class="glass-effect mt-4 px-6 py-3 bg-[#024088] text-white rounded-[30px] hover:bg-[#1a5ba8] font-medium shadow-sm hover:shadow-md flex items-center gap-2">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
                                            Tambah Aktivitas
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                </div>
            </div>
        </div>
    </div>
    

    
    <x-modal-aktivitas :koleksis="$koleksis" :mahasiswas="$mahasiswas"></x-modal-aktivitas>
    
    <!-- Data untuk JavaScript -->
    <script>
        window.availableTopicsData = JSON.parse('{!! json_encode($availableTopics ?? []) !!}');
    </script>
    
    @if($errors->any())
    <script>
        // Buka modal jika ada error validasi
        window.addEventListener('DOMContentLoaded', function() {
            openModal();
        });
    </script>
    @endif

    {{-- Return Confirmation Modal --}}
    <div id="return-confirmation-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm" style="display: none;">
        <div class="bg-white rounded-[30px] w-full max-w-sm mx-auto relative border border-gray-200 shadow-2xl transform transition-all duration-300 ease-out scale-95 opacity-0" id="return-confirmation-modal-content">
            <!-- Modal Header -->
            <div class="flex items-center justify-center p-6">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="px-6 pb-4 text-center">
                <h3 class="text-xl font-bold text-gray-900">Konfirmasi Pengembalian</h3>
                <p class="text-gray-600 mt-2">Apakah Anda yakin ingin mengembalikan buku ini?</p>
            </div>

            <!-- Modal Footer -->
            <div class="flex justify-between gap-4 px-6 pb-6">
                <button type="button" onclick="hideReturnConfirmationModal()" 
                        class="glass-effect flex-1 border border-black rounded-[30px] py-2 font-medium hover:bg-gray-100 transition-all duration-300">
                    Batalkan
                </button>
                <button type="button" id="confirm-return-btn"
                        class="glass-effect flex-1 bg-blue-600 text-white rounded-[30px] py-2 font-medium hover:bg-blue-700 transition-all duration-300">
                    Ya, Kembalikan
                </button>
            </div>
        </div>
    </div>

    <script>
        // Fungsi untuk menampilkan modal konfirmasi pengembalian
        function showReturnConfirmationModal() {
            const modal = document.getElementById('return-confirmation-modal');
            const modalContent = document.getElementById('return-confirmation-modal-content');
            
            modal.style.display = 'flex';
            
            // Trigger animation
            setTimeout(() => {
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        // Fungsi untuk menyembunyikan modal konfirmasi pengembalian
        function hideReturnConfirmationModal() {
            const modal = document.getElementById('return-confirmation-modal');
            const modalContent = document.getElementById('return-confirmation-modal-content');
            
            modalContent.classList.remove('scale-100', 'opacity-100');
            modalContent.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                modal.style.display = 'none';
            }, 300);
        }

        // Fungsi untuk mengupdate status aktivitas
        async function updateStatus(id, status) {
            // Cari kode buku dari tabel untuk disimpan
            const row = document.querySelector(`tr[data-aktivitas-id="${id}"]`);
            const kode = row ? row.getAttribute('data-kode') : '';
            
            // Simpan data untuk konfirmasi
            window.pendingStatusUpdate = { id: id, status: status, kode: kode };
            
            // Tampilkan modal konfirmasi
            showReturnConfirmationModal();
        }

        // Fungsi untuk memproses konfirmasi pengembalian
        async function processStatusUpdate() {
            if (!window.pendingStatusUpdate) return;
            
            const { id, status } = window.pendingStatusUpdate;
            
            // Tampilkan loading state pada tombol konfirmasi
            const confirmBtn = document.getElementById('confirm-return-btn');
            const originalText = confirmBtn.textContent;
            confirmBtn.disabled = true;
            confirmBtn.textContent = 'Memproses...';
            confirmBtn.classList.add('opacity-50', 'cursor-not-allowed');
            
            // Refresh halaman langsung saat tombol diklik
            setTimeout(() => {
                window.location.reload();
            }, 500);
            
            try {
                const requestBody = { status: status };
                
                const csrfToken = document.querySelector('meta[name=csrf-token]').getAttribute('content');
                    
                const requestOptions = {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(requestBody)
                };
                    
                const response = await fetch('/aktivitas/' + id + '/status', requestOptions);
                
                if (response.ok) {
                    const responseData = await response.json();
                    
                    // Kirim event untuk update status koleksi
                    window.dispatchEvent(new CustomEvent('koleksi-status-updated', {
                        detail: {
                            kode: window.pendingStatusUpdate.kode || '',
                            newStatus: 'Tersedia'
                        }
                    }));
                    
                    // Clear pending update
                    window.pendingStatusUpdate = null;
                    
                    return true;
                } else {
                    let errorMessage = 'Gagal mengupdate status';
                    try {
                        const errorData = await response.json();
                        errorMessage = errorData.message || errorData.error || errorMessage;
                    } catch (e) {
                        errorMessage = `HTTP ${response.status}: ${response.statusText}`;
                    }
                    
                    throw new Error(errorMessage);
                }
            } catch (error) {
                let errorMessage = 'Terjadi kesalahan saat mengupdate status';
                if (error.message) {
                    errorMessage = error.message;
                }
                
                // Tampilkan error message sebelum refresh
                showErrorMessage(errorMessage);
                
                // Refresh halaman untuk menampilkan error
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
                
                throw error;
            }
        }


        // Fungsi untuk menampilkan pesan sukses
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
            container.insertBefore(alertDiv, container.firstChild);
            
            // Auto hide setelah 3 detik
            setTimeout(() => {
                alertDiv.remove();
            }, 3000);
        }

        // Fungsi untuk menampilkan pesan error
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
            container.insertBefore(alertDiv, container.firstChild);
            
            // Auto hide setelah 5 detik
            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }

        // Event listener untuk menutup dropdown ketika klik di luar
        document.addEventListener('click', function(event) {
            const dropdowns = document.querySelectorAll('.status-dropdown');
            dropdowns.forEach(dropdown => {
                const container = dropdown.closest('.status-container');
                if (container && !container.contains(event.target)) {
                    // Trigger Alpine.js untuk menutup dropdown
                    const alpineComponent = container.querySelector('[x-data]');
                    if (alpineComponent && alpineComponent._x_dataStack) {
                        const alpineData = alpineComponent._x_dataStack[0];
                        if (alpineData.open !== undefined) {
                            alpineData.open = false;
                        }
                    }
                }
            });
        });
        
        // Fungsi untuk mengecek posisi dropdown dan menyesuaikan jika diperlukan
        function adjustDropdownPosition(dropdown) {
            const rect = dropdown.getBoundingClientRect();
            const viewportWidth = window.innerWidth;
            
            // Jika dropdown akan terpotong di sebelah kanan
            if (rect.right > viewportWidth - 20) {
                dropdown.classList.add('dropdown-left');
            } else {
                dropdown.classList.remove('dropdown-left');
            }
        }

        // Mencegah event bubbling pada dropdown
        document.addEventListener('DOMContentLoaded', function() {
            const dropdownOptions = document.querySelectorAll('.dropdown-option');
            dropdownOptions.forEach(option => {
                option.addEventListener('click', function(event) {
                    event.stopPropagation();
                });
            });
        });

        // Memastikan dropdown tidak mempengaruhi scroll dan menyesuaikan posisi
        document.addEventListener('DOMContentLoaded', function() {
            const statusButtons = document.querySelectorAll('.status-button');
            statusButtons.forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    event.stopPropagation();
                    
                    // Tunggu sebentar agar dropdown sudah muncul, lalu sesuaikan posisinya
                    setTimeout(() => {
                        const dropdown = button.closest('.status-container').querySelector('.status-dropdown');
                        if (dropdown) {
                            adjustDropdownPosition(dropdown);
                        }
                    }, 10);
                });
            });
        });

        // Auto refresh data setiap 30 detik
        setInterval(function() {
            fetch(window.location.href)
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newTableBody = doc.getElementById('aktivitas-table-body');
                    const currentTableBody = document.getElementById('aktivitas-table-body');
                    
                    if (newTableBody && currentTableBody) {
                        currentTableBody.innerHTML = newTableBody.innerHTML;
                    }
                    
                    // Update counter
                    const newCounter = doc.getElementById('result-counter');
                    const currentCounter = document.getElementById('result-counter');
                    if (newCounter && currentCounter) {
                        currentCounter.innerHTML = newCounter.innerHTML;
                    }
                })
                .catch(error => console.log('Error refreshing data:', error));
        }, 30000);

        // Update counter when filters are applied
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form[action*="aktivitas"]');
            if (form) {
                form.addEventListener('submit', function() {
                    // Update counter after form submission
                    setTimeout(function() {
                        updateVisibleCounter();
                    }, 100);
                });
            }

            // Auto hide alert messages after 5 seconds
            setTimeout(function() {
                const alerts = document.querySelectorAll('[role="alert"]');
                alerts.forEach(function(alert) {
                    alert.style.display = 'none';
                });
            }, 5000);
        });

        // Function to update counter
        function updateCounter(count) {
            const counter = document.getElementById('result-counter');
            if (counter) {
                counter.textContent = `Menampilkan ${count} dari ${count} aktivitas`;
            }
        }

        // Function to count visible rows and update counter
        function updateVisibleCounter() {
            const visibleRows = document.querySelectorAll('.filterable-row:not([style*="display: none"])');
            const totalRows = document.querySelectorAll('.filterable-row');
            const counter = document.getElementById('result-counter');
            if (counter) {
                counter.textContent = `Menampilkan ${visibleRows.length} dari ${totalRows.length} aktivitas`;
            }
        }

        // Update counter when page loads
        document.addEventListener('DOMContentLoaded', function() {
            updateVisibleCounter();
        });
        
        // Menyesuaikan posisi dropdown ketika window di-resize
        window.addEventListener('resize', function() {
            const openDropdowns = document.querySelectorAll('.status-dropdown.show');
            openDropdowns.forEach(dropdown => {
                adjustDropdownPosition(dropdown);
            });
        });
        

        
        // Filter functionality
        function filterData() {
            return {
                searchTerm: '',
                selectedTopik: null,
                selectedKode: null,
                selectedStatus: null,
                topikOpen: false,
                kodeOpen: false,
                statusOpen: false,
                availableTopiks: [],
                availableKodes: [],
                availableStatuses: [],
                
                init() {
                    this.extractAvailableTopiks();
                    this.extractAvailableKodes();
                    this.extractAvailableStatuses();
                    this.updateResultCount();
                },
                
                extractAvailableTopiks() {
                    // Gunakan data topik dari backend yang sudah difilter
                    const topiks = new Set();
                    
                    // Ambil dari data yang tersedia di tabel
                    const rows = document.querySelectorAll('.filterable-row');
                    rows.forEach(row => {
                        const topik = row.getAttribute('data-topik');
                        if (topik && topik.trim() !== '' && topik !== '-') {
                            topiks.add(topik);
                        }
                    });
                    
                    // Jika tidak ada data dari tabel, gunakan data dari backend
                    if (topiks.size === 0) {
                        if (window.availableTopicsData && window.availableTopicsData.length > 0) {
                            this.availableTopiks = window.availableTopicsData;
                        } else {
                            this.availableTopiks = [];
                        }
                    } else {
                        this.availableTopiks = Array.from(topiks).sort();
                    }
                },
                
                extractAvailableKodes() {
                    const rows = document.querySelectorAll('.filterable-row');
                    const kodes = new Set();
                    
                    rows.forEach(row => {
                        const kode = row.getAttribute('data-kode');
                        if (kode && kode.trim() !== '') {
                            kodes.add(kode);
                        }
                    });
                    
                    this.availableKodes = Array.from(kodes).sort();
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
                            const nama = row.getAttribute('data-nama') || '';
                            const kode = row.getAttribute('data-kode') || '';
                            
                            if (!nama.toLowerCase().includes(searchLower) && 
                                !kode.toLowerCase().includes(searchLower)) {
                                show = false;
                            }
                        }
                        
                        // Topik filter
                        if (this.selectedTopik && show) {
                            const topik = row.getAttribute('data-topik') || '';
                            if (topik !== this.selectedTopik) {
                                show = false;
                            }
                        }
                        
                        // Kode filter
                        if (this.selectedKode && show) {
                            const kode = row.getAttribute('data-kode') || '';
                            if (kode !== this.selectedKode) {
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
                    this.selectedTopik = null;
                    this.selectedKode = null;
                    this.selectedStatus = null;
                    this.topikOpen = false;
                    this.kodeOpen = false;
                    this.statusOpen = false;
                    
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
                    
                    const counter = document.getElementById('result-counter');
                    if (counter) {
                        counter.textContent = `Menampilkan ${visibleRows.length} dari ${totalRows.length} aktivitas`;
                    }
                                }
            }
        }
        
        // Fungsi untuk membuka modal
        function openModal() {
            console.log('openModal function called');
            const modal = document.getElementById('modal-aktivitas');
            if (modal) {
                modal.style.display = 'flex';
                // Reset form
                const form = document.getElementById('aktivitas-form');
                if (form) {
                    form.reset();
                }
                
                // Reset Alpine.js dropdown data
                setTimeout(() => {
                    // Reset mahasiswa dropdown
                    const mahasiswaDropdown = document.querySelector('[x-data*="selectedNrm"]');
                    if (mahasiswaDropdown && mahasiswaDropdown.__x) {
                        mahasiswaDropdown.__x.$data.selectedNrm = '';
                        mahasiswaDropdown.__x.$data.selectedNama = '';
                        mahasiswaDropdown.__x.$data.search = '';
                        mahasiswaDropdown.__x.$data.open = false;
                    }
                    
                    // Reset buku dropdown
                    const bukuDropdown = document.querySelector('[x-data*="selectedKode"]');
                    if (bukuDropdown && bukuDropdown.__x) {
                        bukuDropdown.__x.$data.selectedKode = '';
                        bukuDropdown.__x.$data.selectedJudul = '';
                        bukuDropdown.__x.$data.search = '';
                        bukuDropdown.__x.$data.open = false;
                    }
                    
                    // Reset status dropdown
                    const statusDropdown = document.querySelector('[x-data*="selectedStatus"]');
                    if (statusDropdown && statusDropdown.__x) {
                        statusDropdown.__x.$data.selectedStatus = '';
                        statusDropdown.__x.$data.open = false;
                    }
                    
                    // Reset topik dan judul display
                    const topikDisplay = document.getElementById('topik-display');
                    const judulDisplay = document.getElementById('judul-display');
                    if (topikDisplay) topikDisplay.value = '';
                    if (judulDisplay) judulDisplay.value = '';
                }, 100);
                
                console.log('Modal opened successfully');
            } else {
                console.log('Modal element not found');
            }
        }
        
        // Fungsi untuk menutup modal
        function closeModal() {
            console.log('closeModal function called');
            const modal = document.getElementById('modal-aktivitas');
            if (modal) {
                modal.style.display = 'none';
                console.log('Modal closed successfully');
            }
        }
        
        // Event listener untuk menutup modal saat klik di luar modal
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('modal-aktivitas');
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        closeModal();
                    }
                });
                
                // Event listener untuk escape key
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && modal.style.display === 'flex') {
                        closeModal();
                    }
                });
            }
            
            // Listen untuk aktivitas yang berhasil dibuat
            window.addEventListener('aktivitas-created', (event) => {
                const { kode, status } = event.detail;
                console.log('Aktivitas created event received:', { kode, status });
                
                // Kirim event ke halaman koleksi untuk update status
                if (window.opener && !window.opener.closed) {
                    // Jika dibuka di window baru
                    window.opener.postMessage({
                        type: 'koleksi-status-updated',
                        kode: kode,
                        newStatus: status
                    }, '*');
                } else {
                    // Jika di tab yang sama, dispatch event
                    window.dispatchEvent(new CustomEvent('koleksi-status-updated', {
                        detail: {
                            kode: kode,
                            newStatus: status
                        }
                    }));
                }
                
                // Redirect ke halaman koleksi untuk melihat perubahan
                setTimeout(() => {
                    window.location.href = '/koleksi';
                }, 1000);
            });
        });

        // Event listeners untuk modal konfirmasi pengembalian
        document.addEventListener('DOMContentLoaded', function() {
            // Event listener untuk tombol konfirmasi
            const confirmBtn = document.getElementById('confirm-return-btn');
            if (confirmBtn) {
                confirmBtn.addEventListener('click', function() {
                    processStatusUpdate();
                });
            }

            // Event listener untuk menutup modal saat klik di luar
            const returnModal = document.getElementById('return-confirmation-modal');
            if (returnModal) {
                returnModal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        hideReturnConfirmationModal();
                    }
                });
            }

            // Event listener untuk escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    const returnModal = document.getElementById('return-confirmation-modal');
                    if (returnModal && returnModal.style.display === 'flex') {
                        hideReturnConfirmationModal();
                    }
                }
            });
        });
        
    </script>
</body>

</html>