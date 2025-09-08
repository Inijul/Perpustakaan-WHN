{{-- Sidebar --}}
<div
    class="w-[300] flex-shrink-0 bg-[#135ea2] p-8 md:p-[30px] flex flex-col items-center justify-start overflow-y-auto">
    {{-- Tambah p-8 untuk mobile, overflow-y-auto --}}
    <div class="flex flex-col items-center gap-10 md:gap-20"> {{-- Kurangi gap untuk mobile --}}
        {{-- Logo SVG --}}
        <svg width="121" height="120" viewBox="0 0 121 120" fill="none" xmlns="http://www.w3.org/2000/svg"
            class="w-[80px] h-[80px] md:w-[100px] md:h-[100px]" preserveAspectRatio="xMidYMid meet">
            {{--Perkecil logo untuk mobile --}}
            <g filter="url(#filter0_d_227_2160)">
                <rect x="10.5" y="6" width="100" height="100" rx="16" fill="#FBFBFB"></rect>
            </g>
            <path d="M33.6343 32.8657L47.2537 22.4179L47.2537 43.3134L33.6343 32.8657Z" fill="#207BB5"></path>
            <path d="M33.6343 53.7612L47.2537 43.3134L47.2537 64.209L33.6343 53.7612Z" fill="#207BB5"></path>
            <path d="M60.8731 53.7612L74.4925 43.3134L74.4925 64.209L60.8731 53.7612Z" fill="#207BB5"></path>
            <path d="M74.4925 43.3134L88.1119 32.8657L88.1119 53.7612L74.4925 43.3134Z" fill="#135EA2"></path>
            <path d="M60.8731 53.7612L47.2537 43.3134L47.2537 64.209L60.8731 53.7612Z" fill="#135EA2"></path>
            <path d="M88.1119 53.7612L74.4925 43.3134L74.4925 64.209L88.1119 53.7612Z" fill="#135EA2"></path>
            <path d="M88.1119 32.8657L74.4925 22.4179L74.4925 43.3134L88.1119 32.8657Z" fill="#135EA2"></path>
            <path d="M74.4925 43.3134L60.8731 32.8657L60.8731 53.7612L74.4925 43.3134Z" fill="#135EA2"></path>
            <path d="M47.2537 43.3134L60.8731 32.8657L60.8731 53.7612L47.2537 43.3134Z" fill="#207BB5"></path>
            <path d="M47.2537 43.3134L33.6343 53.7612L33.6343 32.8657L47.2537 43.3134Z" fill="#135EA2"></path>
            <path
                d="M37.357 88.8358L32.8881 71.6716H36.7383L40.1072 86.0481H39.0759L42.6281 71.6716H46.295L49.8472 86.0481H48.8159L52.1849 71.6716H56.0351L51.5661 88.8358H47.4409L43.8657 75.0123H45.0574L41.4822 88.8358H37.357Z"
                fill="#135EA2"></path>
            <path
                d="M57.5757 88.8358V71.6716H61.128V79.0903H67.7742V71.6716H71.3264V88.8358H67.7742V82.2005H61.128V88.8358H57.5757Z"
                fill="#135EA2"></path>
            <path
                d="M74.1321 88.8358V71.6716H76.8593L86.0264 83.8133L84.5597 84.1589V71.6716H88.1119V88.8358H85.3618L76.3551 76.602L77.6843 76.2564V88.8358H74.1321Z"
                fill="#135EA2"></path>
            <defs>
                <filter id="filter0_d_227_2160" x="0.5" y="0" width="120" height="120" filterUnits="userSpaceOnUse"
                    color-interpolation-filters="sRGB">
                    <feFlood flood-opacity="0" result="BackgroundImageFix"></feFlood>
                    <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"
                        result="hardAlpha"></feColorMatrix>
                    <feOffset dy="4"></feOffset>
                    <feGaussianBlur stdDeviation="5"></feGaussianBlur>
                    <feComposite in2="hardAlpha" operator="out"></feComposite>
                    <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.05 0">
                    </feColorMatrix>
                    <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_227_2160">
                    </feBlend>
                    <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_227_2160" result="shape">
                    </feBlend>
                </filter>
            </defs>
        </svg>
        <div class="flex flex-col items-start gap-8 md:gap-[50px]"> {{-- Kurangi gap untuk mobile --}}
            <div class="flex flex-col items-start gap-8 md:gap-10"> {{-- Kurangi gap untuk mobile --}}
                {{-- Dashboard Link --}}
                <a href="/dashboard"
                    class="flex justify-start items-center h-[44px] w-full sm:w-[200px] pl-4 pr-2 border border-transparent gap-4 md:gap-5 @if(request()->is('/dashboard') || request()->is('/')) bg-[#10487a] rounded-2xl @endif">
                    {{-- Kurangi gap --}}
                    <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg"
                        class="w-6 h-6 md:w-[30px] md:h-[30px] flex-shrink-0" preserveAspectRatio="xMidYMid meet"> {{--
                        Perkecil ikon untuk mobile --}}
                        <path
                            d="M16.6667 8.33333V1.66667C16.6667 1.19444 16.8267 0.798889 17.1467 0.48C17.4667 0.161111 17.8622 0.00111111 18.3333 0H28.3333C28.8055 0 29.2017 0.16 29.5217 0.48C29.8417 0.8 30.0011 1.19556 30 1.66667V8.33333C30 8.80555 29.84 9.20166 29.52 9.52166C29.2 9.84167 28.8044 10.0011 28.3333 10H18.3333C17.8611 10 17.4656 9.84 17.1467 9.52C16.8278 9.2 16.6678 8.80444 16.6667 8.33333ZM0 15V1.66667C0 1.19444 0.16 0.798889 0.48 0.48C0.8 0.161111 1.19556 0.00111111 1.66667 0H11.6667C12.1389 0 12.535 0.16 12.855 0.48C13.175 0.8 13.3344 1.19556 13.3333 1.66667V15C13.3333 15.4722 13.1733 15.8683 12.8533 16.1883C12.5333 16.5083 12.1378 16.6678 11.6667 16.6667H1.66667C1.19444 16.6667 0.798889 16.5067 0.48 16.1867C0.161111 15.8667 0.00111111 15.4711 0 15ZM16.6667 28.3333V15C16.6667 14.5278 16.8267 14.1322 17.1467 13.8133C17.4667 13.4944 17.8622 13.3344 18.3333 13.3333H28.3333C28.8055 13.3333 29.2017 13.4933 29.5217 13.8133C29.8417 14.1333 30.0011 14.5289 30 15V28.3333C30 28.8055 29.84 29.2017 29.52 29.5217C29.2 29.8417 28.8044 30.0011 28.3333 30H18.3333C17.8611 30 17.4656 29.84 17.1467 29.52C16.8278 29.2 16.6678 28.8044 16.6667 28.3333ZM0 28.3333V21.6667C0 21.1944 0.16 20.7989 0.48 20.48C0.8 20.1611 1.19556 20.0011 1.66667 20H11.6667C12.1389 20 12.535 20.16 12.855 20.48C13.175 20.8 13.3344 21.1956 13.3333 21.6667V28.3333C13.3333 28.8055 13.1733 29.2017 12.8533 29.5217C12.5333 29.8417 12.1378 30.0011 11.6667 30H1.66667C1.19444 30 0.798889 29.84 0.48 29.52C0.161111 29.2 0.00111111 28.8044 0 28.3333ZM3.33333 13.3333H10V3.33333H3.33333V13.3333ZM20 26.6667H26.6667V16.6667H20V26.6667ZM20 6.66667H26.6667V3.33333H20V6.66667ZM3.33333 26.6667H10V23.3333H3.33333V26.6667Z"
                            fill="#F3F3F3"></path>
                    </svg>
                    <p class="text-base md:text-lg font-bold text-center text-[#f3f3f3]"> {{-- Perkecil font
                        untuk mobile --}}
                        Dashboard
                    </p>
                </a>
                {{-- Kelola Koleksi Link --}}
                <a href="koleksi"
                    class="flex justify-start items-center h-[44px] w-full sm:w-[200px] pl-4 pr-2 border border-transparent gap-4 md:gap-5 @if(request()->is('koleksi')) bg-[#10487a] rounded-2xl @endif">
                    <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg"
                        class="w-6 h-6 md:w-[30px] md:h-[30px] flex-shrink-0" preserveAspectRatio="xMidYMid meet">
                        <path
                            d="M3 6H0V27C0 28.65 1.35 30 3 30H24V27H3V6ZM27 0H9C7.35 0 6 1.35 6 3V21C6 22.65 7.35 24 9 24H27C28.65 24 30 22.65 30 21V3C30 1.35 28.65 0 27 0ZM27 21H9V3H27V21ZM12 10.5H24V13.5H12V10.5ZM12 15H18V18H12V15ZM12 6H24V9H12V6Z"
                            fill="#F3F3F3"></path>
                    </svg>
                    <p class="text-base md:text-lg font-bold text-center text-[#f3f3f3]">
                        Kelola Koleksi
                    </p>
                </a>
                {{-- Aktivitas Link --}}
                <a href="aktivitas"
                    class="flex justify-start items-center h-[44px] w-full sm:w-[200px] pl-4 pr-2 border border-transparent gap-4 md:gap-5 @if(request()->is('aktivitas')) bg-[#10487a] rounded-2xl @endif">
                    <svg width="30" height="35" viewBox="0 0 30 35" fill="none" xmlns="http://www.w3.org/2000/svg"
                        class="w-6 h-6 md:w-[30px] md:h-[30px] flex-shrink-0" preserveAspectRatio="xMidYMid meet">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M3.75 3.75V31.25H26.25V3.75H3.75ZM2.5 0C1.83696 0 1.20107 0.263392 0.732233 0.732233C0.263392 1.20107 0 1.83696 0 2.5V32.5C0 33.163 0.263392 33.7989 0.732233 34.2678C1.20107 34.7366 1.83696 35 2.5 35H27.5C28.163 35 28.7989 34.7366 29.2678 34.2678C29.7366 33.7989 30 33.163 30 32.5V2.5C30 1.83696 29.7366 1.20107 29.2678 0.732233C28.7989 0.263392 28.163 0 27.5 0H2.5ZM15 25C15 24.5027 15.1975 24.0258 15.5492 23.6742C15.9008 23.3225 16.3777 23.125 16.875 23.125H21.25C21.7473 23.125 22.2242 23.3225 22.5758 23.6742C22.9275 24.0258 23.125 24.5027 23.125 25C23.125 25.4973 22.9275 25.9742 22.5758 26.3258C22.2242 26.6775 21.7473 26.875 21.25 26.875H16.875C16.3777 26.875 15.9008 26.6775 15.5492 26.3258C15.1975 25.9742 15 25.4973 15 25ZM10 27.5C10.663 27.5 11.2989 27.2366 11.7678 26.7678C12.2366 26.2989 12.5 25.663 12.5 25C12.5 24.337 12.2366 23.7011 11.7678 23.2322C11.2989 22.7634 10.663 22.5 10 22.5C9.33696 22.5 8.70107 22.7634 8.23223 23.2322C7.76339 23.7011 7.5 24.337 7.5 25C7.5 25.663 7.76339 26.2989 8.23223 26.7678C8.70107 27.2366 9.33696 27.5 10 27.5ZM15 17.5C15 17.0027 15.1975 16.5258 15.5492 16.1742C15.9008 15.8225 16.3777 15.625 16.875 15.625H21.25C21.7473 15.625 22.2242 15.8225 22.5758 16.1742C22.9275 16.5258 23.125 17.0027 23.125 17.5C23.125 17.9973 22.9275 18.4742 22.5758 18.8258C22.2242 19.1775 21.7473 19.375 21.25 19.375H16.875C16.3777 19.375 15.9008 19.1775 15.5492 18.8258C15.1975 18.4742 15 17.9973 15 17.5ZM10 20C10.663 20 11.2989 19.7366 11.7678 19.2678C12.2366 18.7989 12.5 18.163 12.5 17.5C12.5 16.837 12.2366 16.2011 11.7678 15.7322C11.2989 15.2634 10.663 15 10 15C9.33696 15 8.70107 15.2634 8.23223 15.7322C7.76339 16.2011 7.5 16.837 7.5 17.5C7.5 18.163 7.76339 18.7989 8.23223 19.2678C8.70107 19.7366 9.33696 20 10 20ZM15 10C15 9.50272 15.1975 9.02581 15.5492 8.67417C15.9008 8.32254 16.3777 8.125 16.875 8.125H21.25C21.7473 8.125 22.2242 8.32254 22.5758 8.67417C22.9275 9.02581 23.125 9.50272 23.125 10C23.125 10.4973 22.9275 10.9742 22.5758 11.3258C22.2242 11.6775 21.7473 11.875 21.25 11.875H16.875C16.3777 11.875 15.9008 11.6775 15.5492 11.3258C15.1975 10.9742 15 10.4973 15 10ZM10 12.5C10.663 12.5 11.2989 12.2366 11.7678 11.7678C12.2366 11.2989 12.5 10.663 12.5 10C12.5 9.33696 12.2366 8.70107 11.7678 8.23223C11.2989 7.76339 10.663 7.5 10 7.5C9.33696 7.5 8.70107 7.76339 8.23223 8.23223C7.76339 8.70107 7.5 9.33696 7.5 10C7.5 10.663 7.76339 11.2989 8.23223 11.7678C8.70107 12.2366 9.33696 12.5 10 12.5Z"
                            fill="#F3F3F3"></path>
                    </svg>
                    <p class="text-base md:text-lg font-bold text-center text-[#f3f3f3]">
                        Aktivitas
                    </p>
                </a>
            </div>
            <svg width="239" height="1" viewBox="0 0 239 1" fill="none" xmlns="http://www.w3.org/2000/svg"
                class="self-stretch flex-shrink-0" preserveAspectRatio="none">
                <line y1="0.5" x2="239" y2="0.5" stroke="#F3F3F3" stroke-opacity="0.6"></line>
            </svg>
            {{-- Logout Form --}}
            <form action="{{ route('logout') }}" method="POST" class="w-full">
                @csrf
                <button type="submit" class="flex justify-start items-center h-[44px] w-full sm:w-[200px] pl-4 pr-2 border border-transparent gap-4 md:gap-5 hover:bg-[#10487a] rounded-2xl transition-colors">
                    <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg"
                        class="w-6 h-6 md:w-[30px] md:h-[30px] flex-shrink-0" preserveAspectRatio="xMidYMid meet">
                        <path
                            d="M23.3333 8.33333L20.9833 10.6833L23.6167 13.3333H10V16.6667H23.6167L20.9833 19.3L23.3333 21.6667L30 15L23.3333 8.33333ZM3.33333 3.33333H15V0H3.33333C1.5 0 0 1.5 0 3.33333V26.6667C0 28.5 1.5 30 3.33333 30H15V26.6667H3.33333V3.33333Z"
                            fill="#F3F3F3"></path>
                    </svg>
                    <p class="text-base md:text-lg font-bold text-center text-[#f3f3f3]">
                        Logout
                    </p>
                </button>
            </form>
        </div>
    </div>
</div>