<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Untuk mengatasi FOUC (Flash of Unstyled Content) jika menggunakan Alpine.js */
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="min-h-screen bg-white flex items-center justify-center relative overflow-hidden">
    <svg width="671" height="296" viewBox="0 0 671 296" fill="none" xmlns="http://www.w3.org/2000/svg"
        class="hidden md:block absolute right-0 top-0 z-0 w-1/3 h-auto max-w-[480px]">
        <path d="M542.643 -0.25016L413.965 -99L413.965 98.5L542.643 -0.25016Z" fill="#207BB5"></path>
        <path d="M413.965 98.4998L285.287 -0.250006L285.287 197.25L413.965 98.4998Z" fill="#207BB5"></path>
        <path d="M542.643 197.25L671.322 98.5L671.322 296L542.643 197.25Z" fill="#135EA2"></path>
        <path d="M285.287 -0.25016L413.965 -99L413.965 98.5L285.287 -0.25016Z" fill="#135EA2"></path>
        <path d="M413.965 98.4998L542.643 -0.250006L542.643 197.25L413.965 98.4998Z" fill="#135EA2"></path>
        <path d="M542.643 -0.250145L671.322 -99L671.322 98.5L542.643 -0.250145Z" fill="#135EA2"></path>
        <path d="M671.322 98.4998L542.643 -0.250006L542.643 197.25L671.322 98.4998Z" fill="#207BB5"></path>
        <path d="M27.9302 -0.25016L156.608 -99L156.608 98.5L27.9302 -0.25016Z" fill="#135EA2"></path>
        <path d="M285.287 -0.25016L156.609 -99L156.609 98.5L285.287 -0.25016Z" fill="#207BB5"></path>
        <path d="M156.608 98.4998L285.287 -0.250006L285.287 197.25L156.608 98.4998Z" fill="#135EA2"></path>
    </svg>
    <svg width="671" height="296" viewBox="0 0 671 296" fill="none" xmlns="http://www.w3.org/2000/svg"
        class="hidden md:block absolute left-0 bottom-0 z-0 rotate-180 w-1/3 h-auto max-w-[480px]">
        <path d="M542.643 -0.25016L413.965 -99L413.965 98.5L542.643 -0.25016Z" fill="#207BB5"></path>
        <path d="M413.965 98.4998L285.287 -0.250006L285.287 197.25L413.965 98.4998Z" fill="#207BB5"></path>
        <path d="M542.643 197.25L671.322 98.5L671.322 296L542.643 197.25Z" fill="#135EA2"></path>
        <path d="M285.287 -0.25016L413.965 -99L413.965 98.5L285.287 -0.25016Z" fill="#135EA2"></path>
        <path d="M413.965 98.4998L542.643 -0.250006L542.643 197.25L413.965 98.4998Z" fill="#135EA2"></path>
        <path d="M542.643 -0.250145L671.322 -99L671.322 98.5L542.643 -0.250145Z" fill="#135EA2"></path>
        <path d="M671.322 98.4998L542.643 -0.250006L542.643 197.25L671.322 98.4998Z" fill="#207BB5"></path>
        <path d="M27.9302 -0.25016L156.608 -99L156.608 98.5L27.9302 -0.25016Z" fill="#135EA2"></path>
        <path d="M285.287 -0.25016L156.609 -99L156.609 98.5L285.287 -0.25016Z" fill="#207BB5"></path>
        <path d="M156.608 98.4998L285.287 -0.250006L285.287 197.25L156.608 98.4998Z" fill="#135EA2"></path>
    </svg>

    <div class="relative flex flex-col items-center justify-center w-full min-h-screen z-10 p-4">
        <div
            class="flex flex-col items-center gap-6 w-full max-w-sm mx-auto px-6 py-10 sm:px-8 sm:py-12 bg-white rounded-lg shadow-xl">
            <div class="flex flex-col items-center gap-4">
                <div class="bg-white rounded-xl shadow-md p-4">
                    <svg width="100" height="100" viewBox="0 0 182 184" fill="none" xmlns="http://www.w3.org/2000/svg"
                        class="w-[80px] h-[80px] sm:w-[100px] sm:h-[100px]" preserveAspectRatio="xMidYMid meet">
                        <g filter="url(#filter0_d_539_2421)">
                            <g filter="url(#filter1_d_539_2421)">
                                <rect x="16" y="20" width="150" height="150" rx="16" fill="#FBFBFB"></rect>
                            </g>
                            <path d="M50.7015 60.2985L71.1306 44.6269L71.1306 75.9702L50.7015 60.2985Z" fill="#207BB5">
                            </path>
                            <path d="M50.7015 91.6417L71.1306 75.9701L71.1306 107.313L50.7015 91.6417Z" fill="#207BB5">
                            </path>
                            <path d="M91.5598 91.6417L111.989 75.9701L111.989 107.313L91.5598 91.6417Z" fill="#207BB5">
                            </path>
                            <path d="M111.989 75.9701L132.418 60.2985L132.418 91.6418L111.989 75.9701Z" fill="#207BB5">
                            </path>
                            <path d="M91.5598 91.6417L71.1307 75.9701L71.1307 107.313L91.5598 91.6417Z" fill="#135EA2">
                            </path>
                            <path d="M132.418 91.6417L111.989 75.9701L111.989 107.313L132.418 91.6417Z" fill="#135EA2">
                            </path>
                            <path d="M132.418 60.2985L111.989 44.6269L111.989 75.9702L132.418 60.2985Z" fill="#135EA2">
                            </path>
                            <path d="M111.989 75.9701L91.5597 60.2985L91.5597 91.6418L111.989 75.9701Z" fill="#135EA2">
                            </path>
                            <path d="M71.1307 75.9701L91.5598 60.2985L91.5598 91.6418L71.1307 75.9701Z" fill="#207BB5">
                            </path>
                            <path d="M71.1307 75.9702L50.7016 91.6418L50.7016 60.2985L71.1307 75.9702Z" fill="#135EA2">
                            </path>
                            <path
                                d="M56.2856 144.254L49.5821 118.507H55.3574L60.4108 140.072H58.8638L64.1922 118.507H69.6925L75.0209 140.072H73.4739L78.5273 118.507H84.3026L77.5991 144.254H71.4113L66.0485 123.518H67.8361L62.4734 144.254H56.2856Z"
                                fill="#135EA2"></path>
                            <path
                                d="M86.6136 144.254V118.507H91.942V129.635H101.911V118.507H107.24V144.254H101.911V134.301H91.942V144.254H86.6136Z"
                                fill="#135EA2"></path>
                            <path
                                d="M111.448 144.254V118.507H115.539L129.29 136.72L127.09 137.238V118.507H132.418V144.254H128.293L114.783 125.903L116.776 125.385V144.254H111.448Z"
                                fill="#135EA2"></path>
                        </g>
                        <defs>
                            <filter id="filter0_d_539_2421" x="0" y="0" width="182" height="182"
                                filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                <feFlood flood-opacity="0" result="BackgroundImageFix"></feFlood>
                                <feColorMatrix in="SourceAlpha" type="matrix"
                                    values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha">
                                </feColorMatrix>
                                <feOffset dy="-4"></feOffset>
                                <feGaussianBlur stdDeviation="8"></feGaussianBlur>
                                <feComposite in2="hardAlpha" operator="out"></feComposite>
                                <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.1 0">
                                </feColorMatrix>
                                <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_539_2421">
                                </feBlend>
                                <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_539_2421"
                                    result="shape">
                                </feBlend>
                            </filter>
                            <filter id="filter1_d_539_2421" x="6" y="14" width="170" height="170"
                                filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                <feFlood flood-opacity="0" result="BackgroundImageFix"></feFlood>
                                <feColorMatrix in="SourceAlpha" type="matrix"
                                    values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha">
                                </feColorMatrix>
                                <feOffset dy="4"></feOffset>
                                <feGaussianBlur stdDeviation="5"></feGaussianBlur>
                                <feComposite in2="hardAlpha" operator="out"></feComposite>
                                <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.05 0">
                                </feColorMatrix>
                                <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_539_2421">
                                </feBlend>
                                <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_539_2421"
                                    result="shape">
                                </feBlend>
                            </filter>
                        </defs>
                    </svg>
                </div>
                <div class="flex flex-col items-center gap-1">
                    <p class="w-full text-xl md:text-2xl font-medium text-center text-black">
                        Politeknik Kesehatan
                    </p>
                    <p class="w-full text-xl md:text-2xl font-medium text-center text-black">
                        Wira Husada Nusantara
                    </p>
                </div>
            </div>

            <form action="{{ route('login') }}" method="POST" class="flex flex-col w-full gap-6 mt-2">
                @csrf
                
                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif



                <div>
                    <label class="block text-base md:text-lg font-semibold text-black mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" 
                        class="w-full border-b border-[#545556] py-2 outline-none bg-transparent @error('email') border-red-500 @enderror"
                        placeholder="Masukkan email" required />
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-base md:text-lg font-semibold text-black mb-1">Password</label>
                    <input type="password" name="password" 
                        class="w-full border-b border-[#545556] py-2 outline-none bg-transparent @error('password') border-red-500 @enderror"
                        placeholder="Masukkan password" required />
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="mt-4 flex justify-center items-center h-[44px] rounded-xl bg-[#135ea2] text-base font-bold text-white hover:bg-[#10487a] transition w-full">
                    Masuk
                </button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            
            // Simpan email ke localStorage saat form di-submit
            form.addEventListener('submit', function(e) {
                const emailInput = document.querySelector('input[name="email"]');
                if (emailInput && emailInput.value) {
                    localStorage.setItem('last_email', emailInput.value);
                }
            });
            
            // Isi form dengan email terakhir yang digunakan
            const lastEmail = localStorage.getItem('last_email');
            if (lastEmail && !document.querySelector('input[name="email"]').value) {
                document.querySelector('input[name="email"]').value = lastEmail;
            }
        });
    </script>
</body>

</html>