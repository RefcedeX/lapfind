<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SPK Pemilihan Laptop') }}</title>

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-canvas text-ink antialiased min-h-screen selection:bg-accent/20 selection:text-accent font-sans">
        <div class="min-h-screen flex flex-col justify-center items-center py-12 px-4 sm:px-6">
            <div class="text-center space-y-2 mb-6">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-2.5 group">
                    <img src="{{ asset('images/lapfind-logo.png') }}" alt="LapFind" class="h-9 w-auto object-contain transition group-hover:scale-105">
                </a>
            </div>

            <div class="w-full sm:max-w-md p-6 sm:p-8 bg-surface border border-line rounded shadow-xs">
                {{ $slot }}
            </div>

            <div class="mt-6 text-center text-xs text-ink-muted">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-1.5 hover:text-accent transition">
                    <x-icon name="arrow-left" class="w-3.5 h-3.5" />
                    <span>Kembali ke Halaman Utama</span>
                </a>
            </div>
        </div>
    </body>
</html>
