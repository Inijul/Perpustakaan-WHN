@php
use Carbon\Carbon;
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Library Dashboard</title>
    
    <!-- Preload untuk navigasi cepat -->
    <link rel="dns-prefetch" href="//backend:5000">
    <link rel="prefetch" href="/koleksi">
    <link rel="prefetch" href="/aktivitas">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-white">
    <div class="flex h-screen overflow-hidden">
        <x-sidebar>

        </x-sidebar>

        {{-- Main Content Area --}}
        <div class="flex-grow p-4 md:p-[50px] space-y-6 md:space-y-8 overflow-y-auto">
            {{-- Header section (Tanggal, Search, Notifikasi) --}}
            <div class="flex flex-col sm:flex-row justify-between items-center w-full gap-4 sm:gap-0"> {{-- Flex-col
                untuk mobile, flex-row untuk desktop --}}
                <div class="flex items-center gap-2">
                    <p class="text-sm md:text-base font-bold text-black"> {{-- Sesuaikan ukuran font --}}
                        {{-- Menggunakan Carbon untuk menampilkan tanggal hari ini dalam format "Hari, DD Bulan YYYY"
                        --}}
                        {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, DD MMMM YYYY') }}
                    </p>
                </div>

                <div class="flex items-center gap-2 md:gap-4 flex-wrap justify-center sm:justify-end"> {{-- Flex-wrap
                    untuk mobile, justify-end untuk desktop --}}
                    {{-- Search bar --}}
                    <div
                        class="flex items-center h-[44px] w-full sm:w-[250px] pl-4 pr-2 rounded-[30px] border border-black bg-white">
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
                    {{-- Notification bell --}}
                </div>
            </div>

            {{-- Welcome message --}}
            <div class="flex flex-col items-start gap-1.5 md:gap-2.5"> {{-- Sesuaikan gap --}}
                <p class="text-base md:text-lg font-bold text-black"> {{-- Sesuaikan ukuran font --}}
                    Selamat Datang, {{ session('admin_nip') ?? 'Admin' }}
                </p>
                <p class="text-xs md:text-sm font-medium text-black"> {{-- Sesuaikan ukuran font --}}
                    Admin, Wira Husada Nusantara
                </p>
            </div>

            {{-- Cards Section --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-8"> {{-- Sesuaikan gap --}}
                {{-- Card: Buku --}}
                <div
                    class="flex flex-col justify-center items-start p-4 md:px-5 md:py-3 rounded-lg md:rounded-[20px] bg-white shadow-md">
                    {{-- Sesuaikan padding dan radius --}}
                    <div class="flex justify-start items-center gap-4 md:gap-[25px]"> {{-- Sesuaikan gap --}}
                        <svg width="41" height="50" viewBox="0 0 41 50" fill="none" xmlns="http://www.w3.org/2000/svg"
                            class="w-8 h-8 md:w-10 md:h-[49.14px]" preserveAspectRatio="xMidYMid meet"> {{-- Perkecil
                            ikon --}}
                            <path
                                d="M9.64285 13.5714C9.64285 13.1167 9.82346 12.6807 10.145 12.3592C10.4664 12.0377 10.9025 11.8571 11.3571 11.8571H29.6429C30.0975 11.8571 30.5335 12.0377 30.855 12.3592C31.1765 12.6807 31.3571 13.1167 31.3571 13.5714C31.3571 14.0261 31.1765 14.4621 30.855 14.7836C30.5335 15.1051 30.0975 15.2857 29.6429 15.2857H11.3571C10.9025 15.2857 10.4664 15.1051 10.145 14.7836C9.82346 14.4621 9.64285 14.0261 9.64285 13.5714ZM11.3571 19.8571C10.9025 19.8571 10.4664 20.0377 10.145 20.3592C9.82346 20.6807 9.64285 21.1167 9.64285 21.5714C9.64285 22.0261 9.82346 22.4621 10.145 22.7836C10.4664 23.1051 10.9025 23.2857 11.3571 23.2857H22.7857C23.2404 23.2857 23.6764 23.1051 23.9979 22.7836C24.3194 22.4621 24.5 22.0261 24.5 21.5714C24.5 21.1167 24.3194 20.6807 23.9979 20.3592C23.6764 20.0377 23.2404 19.8571 22.7857 19.8571H11.3571Z"
                                fill="black"></path>
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M15.8029 0.428574C12.6783 0.428574 10.1571 0.428574 8.17543 0.696002C6.11829 0.970288 4.38571 1.56457 3.00971 2.93829C1.63371 4.31429 1.044 6.04686 0.767429 8.104C0.5 10.0857 0.5 12.6069 0.5 15.7314V34.2686C0.5 37.3931 0.5 39.9143 0.767429 41.896C1.04171 43.9531 1.636 45.6857 3.00971 47.0617C4.38571 48.4377 6.11829 49.0274 8.17543 49.3063C10.1571 49.5714 12.6783 49.5714 15.8029 49.5714H25.1971C28.3217 49.5714 30.8429 49.5714 32.8246 49.3063C34.8817 49.0274 36.6143 48.4377 37.9903 47.0617C39.3663 48.4377 39.956 49.0274 40.2349 41.896C40.5 39.9143 40.5 37.3931 40.5 34.2686V15.7314C40.5 12.6069 40.5 10.0857 40.2349 8.104C39.956 6.04686 39.3663 4.31429 37.9903 2.93829C36.6143 1.56229 34.8817 0.972574 32.8246 0.696002C30.8429 0.428574 28.3217 0.428574 25.1971 0.428574H15.8029ZM5.43714 5.36343C6.07029 4.73029 6.95714 4.31886 8.63714 4.09257C10.3606 3.86172 12.6509 3.85714 15.9309 3.85714H25.0737C28.3537 3.85714 30.6417 3.86172 32.3697 4.09257C34.0474 4.31886 34.9343 4.73257 35.5674 5.36343C36.2006 5.99657 36.612 6.88343 36.8383 8.56343C37.0691 10.2869 37.0737 12.5771 37.0737 15.8571V32.4286H10.8589C8.98914 32.4286 7.71371 32.4286 6.61886 32.7211C5.65688 32.9812 4.74684 33.405 3.92857 33.9737V15.8571C3.92857 12.5771 3.93314 10.2869 4.164 8.56114C4.39029 6.88343 4.80629 5.99657 5.43714 5.36343ZM3.99029 39.2834C4.02457 40.0926 4.07943 40.8034 4.164 41.4389C4.39029 43.1166 4.804 44.0034 5.43486 44.6366C6.068 45.2697 6.95486 45.6811 8.63486 45.9074C10.3583 46.1383 12.6486 46.1429 15.9286 46.1429H25.0714C28.3514 46.1429 30.6394 46.1383 32.3674 45.9074C34.0451 45.6811 34.932 45.2674 35.5651 44.6366C36.1983 44.0034 36.6097 43.1166 36.836 41.4366C37.0257 40.0309 37.0623 38.248 37.0691 35.8571H11.124C8.88857 35.8571 8.10686 35.8709 7.50571 36.0331C6.7001 36.2492 5.95914 36.658 5.3467 37.2243C4.73427 37.7905 4.26875 38.4972 3.99029 39.2834Z"
                                fill="black"></path>
                        </svg>
                        <div class="flex flex-col justify-center items-start gap-0.5 md:gap-[5px]"> {{-- Sesuaikan gap
                            --}}
                            <p class="text-sm md:text-base font-medium text-left text-black"> {{-- Sesuaikan font --}}
                                Buku
                            </p>
                            <p class="text-base md:text-lg font-bold text-left text-black"> {{-- Sesuaikan font --}}
                                {{ $totalBuku ?? 0 }}
                            </p>
                        </div>
                    </div>
                </div>
                {{-- Card: Jurnal --}}
                <div
                    class="flex flex-col justify-center items-start p-4 md:px-5 md:py-3 rounded-lg md:rounded-[20px] bg-white shadow-md">
                    {{-- Sesuaikan padding dan radius --}}
                    <div class="flex justify-start items-center gap-4 md:gap-[25px]"> {{-- Sesuaikan gap --}}
                        <svg width="41" height="28" viewBox="0 0 41 28" fill="none" xmlns="http://www.w3.org/2000/svg"
                            class="w-8 h-8 md:w-[41px] md:h-[28px] flex-shrink-0" preserveAspectRatio="xMidYMid meet">
                            {{-- Perkecil ikon --}}
                            <path
                                d="M2.72223 27.5C2.0926 27.5 1.56519 27.284 1.14001 26.852C0.714823 26.42 0.501489 25.886 0.500008 25.25C0.498526 24.614 0.71186 24.08 1.14001 23.648C1.56816 23.216 2.09556 23 2.72223 23H24.9444C25.5741 23 26.1022 23.216 26.5289 23.648C26.9556 24.08 27.1681 24.614 27.1667 25.25C27.1652 25.886 26.9519 26.4207 26.5267 26.8542C26.1015 27.2877 25.5741 27.503 24.9444 27.5H2.72223ZM2.72223 16.25C2.0926 16.25 1.56519 16.034 1.14001 15.602C0.714823 15.17 0.501489 14.636 0.500008 14C0.498526 13.364 0.71186 12.83 1.14001 12.398C1.56816 11.966 2.09556 11.75 2.72223 11.75H38.2778C38.9074 11.75 39.4355 11.966 39.8622 12.398C40.2889 12.83 40.5015 13.364 40.5 14C40.4985 14.636 40.2852 15.1707 39.86 15.6042C39.4348 16.0377 38.9074 16.253 38.2778 16.25H2.72223ZM2.72223 5C2.0926 5 1.56519 4.784 1.14001 4.352C0.714823 3.92 0.501489 3.386 0.500008 2.75C0.498526 2.114 0.71186 1.58 1.14001 1.148C1.56816 0.716 2.09556 0.5 2.72223 0.5H38.2778C38.9074 0.5 39.4355 0.716 39.8622 1.148C40.2889 1.58 40.5015 2.114 40.5 2.75C40.4985 3.386 40.2852 3.92075 39.86 4.35425C39.4348 4.78774 38.9074 5.003 38.2778 5H2.72223Z"
                                fill="black"></path>
                        </svg>
                        <div class="flex flex-col justify-center items-start gap-0.5 md:gap-[5px]">
                            <p class="text-sm md:text-base font-medium text-left text-black">
                                Jurnal
                            </p>
                            <p class="text-base md:text-lg font-bold text-left text-black">
                                {{ $totalJurnal ?? 0 }}
                            </p>
                        </div>
                    </div>
                </div>
                {{-- Card: Dokumen Skripsi --}}
                <div
                    class="flex flex-col justify-center items-start p-4 md:px-5 md:py-3 rounded-lg md:rounded-[20px] bg-white shadow-md">
                    {{-- Sesuaikan padding dan radius --}}
                    <div class="flex justify-start items-center gap-4 md:gap-[25px]"> {{-- Sesuaikan gap --}}
                        <svg width="41" height="52" viewBox="0 0 41 52" fill="none" xmlns="http://www.w3.org/2000/svg"
                            class="w-8 h-8 md:w-[41px] md:h-[52px] flex-shrink-0" preserveAspectRatio="xMidYMid meet">
                            {{-- Perkecil ikon --}}
                            <path
                                d="M5.11714 51.5C3.80095 51.5 2.70286 51.0637 1.82286 50.191C0.942857 49.3183 0.501905 48.2294 0.5 46.9242V5.07583C0.5 3.7725 0.940952 2.6845 1.82286 1.81183C2.70476 0.939167 3.80286 0.501889 5.11714 0.5H27.6429L40.5 13.25V46.9242C40.5 48.2275 40.06 49.3164 39.18 50.191C38.3 51.0656 37.201 51.5019 35.8829 51.5H5.11714ZM26.2143 14.6667V3.33333H5.11714C4.67714 3.33333 4.27333 3.51467 3.90571 3.87733C3.53809 4.24 3.35524 4.6395 3.35714 5.07583V46.9242C3.35714 47.3586 3.54 47.7581 3.90571 48.1227C4.27143 48.4872 4.67428 48.6685 5.11428 48.6667H35.8857C36.3238 48.6667 36.7267 48.4853 37.0943 48.1227C37.4619 47.76 37.6448 47.3596 37.6429 46.9213V14.6667H26.2143Z"
                                fill="black"></path>
                        </svg>
                        <div class="flex flex-col justify-start items-start gap-0.5 md:gap-[5px]">
                            <p class="text-sm md:text-base font-medium text-left text-black">
                                Dokumen Skripsi
                            </p>
                            <p class="text-base md:text-lg font-bold text-left text-black">
                                {{ $totalSkripsi ?? 0 }}
                            </p>
                        </div>
                    </div>
                </div>
                {{-- Card: Status Keluar --}}
                <div
                    class="flex flex-col justify-start items-start p-4 md:px-5 md:py-3 rounded-lg md:rounded-[20px] bg-white shadow-md">
                    {{-- Sesuaikan padding dan radius --}}
                    <div class="flex justify-start items-center gap-4 md:gap-[25px]"> {{-- Sesuaikan gap --}}
                        <svg width="43" height="46" viewBox="0 0 43 46" fill="none" xmlns="http://www.w3.org/2000/svg"
                            class="w-8 h-8 md:w-10 md:h-[42.86px]" preserveAspectRatio="xMidYMid meet"> {{-- Perkecil
                            ikon --}}
                            <path
                                d="M20.0714 44.4286H5.78571C4.64907 44.4286 3.55898 43.977 2.75526 43.1733C1.95153 42.3696 1.5 41.2795 1.5 40.1429M1.5 40.1429V7.28571C1.5 5.77019 2.10204 4.31674 3.17368 3.2451C4.24531 2.17347 5.69876 1.57143 7.21429 1.57143H34.3571C34.736 1.57143 35.0994 1.72194 35.3673 1.98985C35.6352 2.25775 35.7857 2.62112 35.7857 3V15.8571M1.5 40.1429C1.5 39.0062 1.95153 37.9161 2.75526 37.1124C3.55898 36.3087 4.64907 35.8571 5.78571 35.8571H12.9286"
                                stroke="black" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            </path>
                            <path
                                d="M41.4999 44.4286L34.2847 37.2133M17.2142 30.1428C17.2142 32.7955 18.268 35.3395 20.1437 37.2153C22.0194 39.091 24.5635 40.1447 27.2161 40.1447C29.8688 40.1447 32.4128 39.091 34.2886 37.2153C36.1643 35.3395 37.218 32.7955 37.218 30.1428C37.218 27.4902 36.1643 24.9461 34.2886 23.0704C32.4128 21.1947 29.8688 21.1947 27.2161 21.1947C24.5635 21.1947 22.0194 22.1947 20.1437 23.0704C18.268 24.9461 17.2142 27.4902 17.2142 30.1428Z"
                                stroke="black" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            </path>
                        </svg>
                        <div class="flex flex-col justify-start items-start gap-0.5 md:gap-[5px]">
                            <p class="text-sm md:text-base font-medium text-left text-black">
                                Status Keluar
                            </p>
                            <p class="text-base md:text-lg font-bold text-left text-black">
                                {{ $totalDipinjam ?? 0 }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Aktivitas Section --}}
            <div class="p-4 md:p-8 rounded-lg md:rounded-[30px] bg-white shadow-md"> {{-- Sesuaikan padding dan radius
                --}}
                <p class="text-lg md:text-xl font-bold text-black mb-3 md:mb-4">Daftar Pengajuan Peminjaman</p> {{--
                Sesuaikan ukuran font
                dan margin --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-black/70">
                                <th
                                    class="px-2 py-3 text-left text-s font-large text-gray-500 uppercase tracking-wider">
                                    Tanggal</th>
                                {{-- Sesuaikan ukuran font dan padding --}}
                                <th
                                    class="px-2 py-3 text-left text-s font-large text-gray-500 uppercase tracking-wider">
                                    Peminjam</th>
                                <th
                                    class="px-2 py-3 text-left text-s font-large text-gray-500 uppercase tracking-wider">
                                    Kategori</th>
                                <th
                                    class="px-2 py-3 text-left text-s font-large text-gray-500 uppercase tracking-wider">
                                    Kode</th>
                                <th
                                    class="px-2 py-3 text-left text-s font-large text-gray-500 uppercase tracking-wider">
                                    Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="px-2 py-4 whitespace text-base font-medium text-black">Rabu, 09 Juli
                                    2025</td> {{-- Sesuaikan ukuran font dan padding --}}
                                <td class="px-2 py-4 whitespace text-base font-medium text-black">Muhammad
                                    Zulfikar</td>
                                <td class="px-2 py-4 whitespace text-base font-medium text-black">Jurnal</td>
                                <td class="px-2 py-4 whitespace text-base font-medium text-black">JRNL01</td>
                                <td class="px-2 py-4 text-base font-medium text-black">
                                    <span
                                        class="inline-flex justify-center items-center px-2 py-1 md:px-[17px] md:py-[5px] rounded-full bg-[#00d836]/70 text-xs md:text-base">Dipinjam</span>
                                    {{-- Sesuaikan padding, radius, dan ukuran font --}}
                                </td>
                            </tr>
                            {{-- Add more rows as needed --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


</body>

</html>