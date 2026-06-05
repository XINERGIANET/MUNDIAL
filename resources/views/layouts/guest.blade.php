<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Polla Mundialista 2026') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        @if (request()->routeIs('home', 'tournaments.*'))
            {{ $slot }}
        @else
            <div class="min-h-screen flex flex-col sm:justify-center items-center px-4 py-8 bg-[#f6f8fb]">
                <a href="/" class="mb-6 flex items-center gap-3">
                    <span class="grid h-12 w-12 place-items-center rounded-lg wc-shell text-sm font-black text-white">26</span>
                    <span class="text-lg font-black uppercase tracking-wide text-gray-950">Polla Mundialista</span>
                </a>

                <div class="w-full sm:max-w-md px-6 py-6 wc-card overflow-hidden rounded-lg">
                    {{ $slot }}
                </div>
            </div>
        @endif
    </body>
</html>
