<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Dreamhome') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('images/dreamhome-logo-white.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <style>
        #page-wipe {
            position: fixed;
            inset: 0;
            background: #853953;
            z-index: 9999;
            transform: translateX(-100%);
            transition: transform 0.45s cubic-bezier(0.77, 0, 0.175, 1);
            pointer-events: none;
        }
        #page-wipe.wipe-in  { transform: translateX(0%); }
        #page-wipe.wipe-out { transform: translateX(100%); transition: transform 0.45s cubic-bezier(0.77, 0, 0.175, 1); }
    </style>

    <body class="font-sans antialiased">

        {{-- Maroon wipe overlay --}}
        <div id="page-wipe"></div>

        <div class="min-h-screen bg-[#F3F4F6]">
            @if(Auth::guard('staff')->check())
                @include('layouts.staff-navigation')
            @else
                @include('layouts.navigation')
            @endif

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const wipe = document.getElementById('page-wipe');

                // Intercept login & register link clicks
                document.querySelectorAll('a[href*="/login"], a[href*="/register"]').forEach(function (link) {
                    link.addEventListener('click', function (e) {
                        e.preventDefault();
                        const target = link.href;
                        wipe.style.pointerEvents = 'all';
                        wipe.classList.add('wipe-in');
                        setTimeout(function () {
                            window.location.href = target;
                        }, 460);
                    });
                });
            });
        </script>

    </body>
</html>