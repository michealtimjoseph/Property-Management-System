<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'DreamHome') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            #page-wipe {
                position: fixed;
                inset: 0;
                background: #853953;
                z-index: 9999;
                transform: translateX(0%);
                pointer-events: none;
            }
            #page-wipe.wipe-out {
                transform: translateX(100%);
                transition: transform 0.45s cubic-bezier(0.77, 0, 0.175, 1);
            }
        </style>
    </head>
    <body class="font-sans antialiased">

        {{-- Maroon wipe overlay — sweeps out on page load --}}
        <div id="page-wipe"></div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                requestAnimationFrame(function () {
                    setTimeout(function () {
                        document.getElementById('page-wipe').classList.add('wipe-out');
                    }, 30);
                });
            });
        </script>
        <div class="min-h-screen flex">

            {{-- ===== LEFT PANEL — AUTO SLIDESHOW ===== --}}
            <div
                x-data="{
                    current: 0,
                    slides: [
                        'https://images.unsplash.com/photo-1570129477492-45c003edd2be?auto=format&fit=crop&w=1200&q=80',
                        'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?auto=format&fit=crop&w=1200&q=80',
                        'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1200&q=80',
                        'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=80'
                    ],
                    init() {
                        setInterval(() => {
                            this.current = (this.current + 1) % this.slides.length
                        }, 4000)
                    }
                }"
                class="hidden lg:flex lg:w-1/2 relative flex-col justify-between overflow-hidden"
            >
                {{-- Slides --}}
                <template x-for="(slide, index) in slides" :key="index">
                    <div
                        x-show="current === index"
                        x-transition:enter="transition ease-in-out duration-700"
                        x-transition:enter-start="opacity-0 scale-105"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in-out duration-700"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="absolute inset-0 bg-cover bg-center"
                        :style="`background-image: url('${slide}')`"
                    ></div>
                </template>

                {{-- Dark gradient overlay --}}
                <div class="absolute inset-0 bg-gradient-to-t from-[#1a0510]/95 via-[#3a0d1e]/70 to-[#853953]/20 z-10"></div>

                {{-- Top: Logo --}}
                <div class="relative z-20 p-10">
                    @php $logoPath = public_path('images/dreamhome-logo-white.png'); @endphp
                    @if(file_exists($logoPath))
                        <img src="{{ asset('images/dreamhome-logo-white.png') }}" alt="DreamHome" class="h-10 w-auto object-contain">
                    @else
                        <div class="flex items-center gap-2">
                            <div class="w-9 h-9 bg-white/20 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                            </div>
                            <span class="font-black text-xl tracking-tight text-white">Dream<span class="text-pink-200">Home</span></span>
                        </div>
                    @endif
                </div>

                {{-- Bottom: Headline + dots --}}
                <div class="relative z-20 px-10 pb-10">
                    <p class="text-[10px] font-black uppercase tracking-[0.25em] text-pink-200 mb-3">Cagayan de Oro</p>
                    <h1 class="text-4xl font-black text-white leading-tight tracking-tight mb-4">
                        Find Your<br><span class="text-pink-200">Dream Home</span><br>Today.
                    </h1>
                    <p class="text-sm text-white/70 font-medium leading-relaxed max-w-xs mb-8">
                        Browse available houses, flats, and bungalows across CDO. Book a viewing and move in faster.
                    </p>

                    {{-- Stats --}}
                    <div class="flex items-center gap-6 mb-10">
                        <div>
                            <p class="text-2xl font-black text-white">50+</p>
                            <p class="text-[10px] font-bold text-pink-200 uppercase tracking-wider">Properties</p>
                        </div>
                        <div class="w-px h-8 bg-white/20"></div>
                        <div>
                            <p class="text-2xl font-black text-white">3</p>
                            <p class="text-[10px] font-bold text-pink-200 uppercase tracking-wider">Branches</p>
                        </div>
                        <div class="w-px h-8 bg-white/20"></div>
                        <div>
                            <p class="text-2xl font-black text-white">CDO</p>
                            <p class="text-[10px] font-bold text-pink-200 uppercase tracking-wider">City</p>
                        </div>
                    </div>

                    {{-- Slide indicator dots --}}
                    <div class="flex items-center gap-2">
                        <template x-for="(slide, index) in slides" :key="index">
                            <div
                                :class="current === index
                                    ? 'w-6 h-2 bg-white rounded-full'
                                    : 'w-2 h-2 bg-white/30 rounded-full'"
                                class="transition-all duration-500 rounded-full">
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Decorative circles --}}
                <div class="absolute -bottom-32 -right-32 w-80 h-80 rounded-full bg-white/5 border border-white/10 z-10"></div>
                <div class="absolute -top-20 -left-20 w-60 h-60 rounded-full bg-white/5 border border-white/10 z-10"></div>

            </div>

            {{-- ===== RIGHT FORM PANEL ===== --}}
            <div class="w-full lg:w-1/2 flex items-center justify-center bg-[#F3F4F6] px-6 py-12">
                <div class="w-full max-w-md">

                    {{-- Mobile logo --}}
                    <div class="flex justify-center mb-8 lg:hidden">
                        @php $logoPath = public_path('images/dreamhome-logo-colored.png'); @endphp
                        @if(file_exists($logoPath))
                            <img src="{{ asset('images/dreamhome-logo-colored.png') }}" alt="DreamHome" class="h-12 w-auto object-contain">
                        @else
                            <div class="flex items-center gap-2">
                                <div class="w-9 h-9 bg-[#853953] rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                                </div>
                                <span class="font-black text-xl tracking-tight"><span class="text-[#853953]">Dream</span><span class="text-gray-800">Home</span></span>
                            </div>
                        @endif
                    </div>

                    {{-- Form Card --}}
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 px-8 py-10">
                        {{ $slot }}
                    </div>

                </div>
            </div>

        </div>
    </body>
</html>