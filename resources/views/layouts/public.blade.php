<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="LapFind — Temukan, bandingkan, dan evaluasi laptop mahasiswa terbaik berdasarkan spesifikasi multi-kriteria berbasis AHP-TOPSIS.">

    <title>{{ $title ?? 'Temukan & Bandingkan Laptop' }} — LapFind</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Google Fonts: Plus Jakarta Sans + JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Anti-flicker Theme Script (Instant Execution before render) -->
    <script>
        (function() {
            try {
                var theme = localStorage.getItem('theme');
                var supportDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (theme === 'dark' || (!theme && supportDark)) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            } catch (e) {}
        })();
    </script>

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-950 text-slate-800 antialiased min-h-screen"
      x-data="{ sideDrawerOpen: false }"
      @keydown.escape.window="sideDrawerOpen = false"
      @keydown.window.prevent.ctrl.k="document.getElementById('header-search-input')?.focus()"
      @keydown.window.prevent.meta.k="document.getElementById('header-search-input')?.focus()"
      @keydown.window.prevent.alt.t="$store.theme.toggle()"
      style="font-family:'Plus Jakarta Sans',sans-serif;">

    {{-- ─── ACCESSIBILITY: SKIP TO MAIN CONTENT (WCAG 2.4.1) ───────── --}}
    <a href="#main-content"
       class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-50 focus:px-4 focus:py-2 focus:bg-[#2563EB] focus:text-white focus:text-xs focus:font-bold focus:rounded-md focus:shadow-md focus:outline-none">
        Lewati ke konten utama
    </a>

    {{-- ─── BACKDROP ─────────────────────────────────────────────── --}}
    <div x-show="sideDrawerOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sideDrawerOpen = false"
         class="fixed inset-0 z-50 bg-slate-900/50 cursor-pointer"
         aria-hidden="true"
         style="display:none;"></div>

    {{-- ─── SIDE DRAWER (Linear / 3-Section Dense Navigation) ────── --}}
    <aside x-show="sideDrawerOpen"
           role="dialog"
           aria-modal="true"
           aria-label="Menu Navigasi Utama"
           x-transition:enter="transition ease-[cubic-bezier(0.22,1,0.36,1)] duration-280"
           x-transition:enter-start="-translate-x-full"
           x-transition:enter-end="translate-x-0"
           x-transition:leave="transition ease-in duration-200"
           x-transition:leave-start="translate-x-0"
           x-transition:leave-end="-translate-x-full"
           class="fixed inset-y-0 left-0 z-50 w-72 bg-white dark:bg-slate-900 border-r border-[#E2E8F0] dark:border-slate-800 flex flex-col no-scrollbar overflow-y-auto shadow-2xl"
           style="display:none;">

        {{-- 1. Drawer Header --}}
        <div class="flex items-center justify-between px-4 py-3.5 border-b border-[#E2E8F0] dark:border-slate-800">
            <div class="flex items-center gap-2">
                <a href="{{ Auth::check() ? route('dashboard') : route('home') }}" @click="sideDrawerOpen = false" class="flex items-center" aria-label="LapFind">
                    <img src="{{ asset('images/lapfind-logo.png') }}" alt="LapFind" class="h-6 w-auto object-contain">
                </a>
                <span class="text-[10px] font-mono font-medium px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200/80 dark:border-slate-700">
                    SPK v2.0
                </span>
            </div>
            <button type="button"
                    @click="sideDrawerOpen = false"
                    class="w-7 h-7 rounded-md flex items-center justify-center text-slate-400 hover:text-slate-900 hover:bg-slate-100 dark:hover:text-slate-200 dark:hover:bg-slate-800 transition cursor-pointer"
                    aria-label="Tutup menu navigasi"
                    title="Tutup">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>

        {{-- 2. Drawer Body (Main Navigation & Category Shortcuts) --}}
        <div class="flex-1 px-3 py-5 space-y-6 overflow-y-auto no-scrollbar">

            {{-- Main Menu --}}
            <div>
                <p class="px-3 pb-2.5 text-xs font-bold uppercase tracking-[0.08em] text-slate-400 dark:text-slate-500">Navigasi Utama</p>
                <nav class="space-y-1.5">
                    {{-- 1. Katalog Laptop --}}
                    <a href="{{ route('catalog.index') }}"
                       @click="sideDrawerOpen = false"
                       class="group flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('catalog.index') || request()->routeIs('home') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white font-semibold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-md flex items-center justify-center shrink-0 transition-colors {{ request()->routeIs('catalog.index') || request()->routeIs('home') ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/40' : 'text-slate-400 dark:text-slate-500 group-hover:text-slate-700 dark:group-hover:text-slate-300' }}">
                                <svg width="18" height="18" class="w-4.5 h-4.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="12" rx="2"/>
                                    <path d="M2 20h20"/>
                                </svg>
                            </div>
                            <span>Katalog Laptop</span>
                        </div>
                        <span class="text-xs font-mono text-slate-400 dark:text-slate-500">{{ \App\Models\Alternative::count() }} unit</span>
                    </a>

                    {{-- 2. Bandingkan --}}
                    <a href="{{ route('catalog.compare') }}"
                       @click="sideDrawerOpen = false"
                       class="group flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('catalog.compare') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white font-semibold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-md flex items-center justify-center shrink-0 transition-colors {{ request()->routeIs('catalog.compare') ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/40' : 'text-slate-400 dark:text-slate-500 group-hover:text-slate-700 dark:group-hover:text-slate-300' }}">
                                <svg width="18" height="18" class="w-4.5 h-4.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="3" width="7" height="18" rx="1.5"/>
                                    <rect x="14" y="3" width="7" height="18" rx="1.5"/>
                                </svg>
                            </div>
                            <span>Bandingkan</span>
                        </div>
                        <span class="text-xs font-mono px-2 py-0.5 rounded bg-blue-50 dark:bg-blue-950/50 text-blue-700 dark:text-blue-300 font-semibold"
                              x-text="'VS ' + $store.compare.count">VS 0</span>
                    </a>

                    {{-- 3. Smart Match --}}
                    <a href="{{ route('spk.index') }}"
                       @click="sideDrawerOpen = false"
                       class="group flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('spk.*') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white font-semibold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-md flex items-center justify-center shrink-0 transition-colors {{ request()->routeIs('spk.*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/40' : 'text-slate-400 dark:text-slate-500 group-hover:text-slate-700 dark:group-hover:text-slate-300' }}">
                                <svg width="18" height="18" class="w-4.5 h-4.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                </svg>
                            </div>
                            <span>Smart Match</span>
                        </div>
                        <span class="text-[11px] font-mono font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-blue-50 dark:bg-blue-950/50 text-blue-700 dark:text-blue-300">AHP</span>
                    </a>

                    {{-- 4. Riwayat Evaluasi --}}
                    <a href="{{ route('history.index') }}"
                       @click="sideDrawerOpen = false"
                       class="group flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('history.*') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white font-semibold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-md flex items-center justify-center shrink-0 transition-colors {{ request()->routeIs('history.*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/40' : 'text-slate-400 dark:text-slate-500 group-hover:text-slate-700 dark:group-hover:text-slate-300' }}">
                                <svg width="18" height="18" class="w-4.5 h-4.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="9"/>
                                    <polyline points="12 7 12 12 15 15"/>
                                </svg>
                            </div>
                            <span>Riwayat Evaluasi</span>
                        </div>
                    </a>

                    @auth
                        @if (auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}"
                               @click="sideDrawerOpen = false"
                               class="group flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.*') ? 'bg-purple-50 dark:bg-purple-950/50 text-purple-900 dark:text-purple-200 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white' }}">
                                <div class="flex items-center gap-3">
                                    <div class="w-7 h-7 rounded-md flex items-center justify-center shrink-0 text-purple-500 dark:text-purple-400">
                                        <svg width="18" height="18" class="w-4.5 h-4.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                        </svg>
                                    </div>
                                    <span>Panel Admin</span>
                                </div>
                                <span class="text-[11px] font-mono font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-purple-100 dark:bg-purple-900/50 text-purple-700 dark:text-purple-300">ADM</span>
                            </a>
                        @endif
                    @endauth
                </nav>
            </div>

            {{-- Shortcut Kategori --}}
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800">
                <p class="px-3 pb-2.5 text-xs font-bold uppercase tracking-[0.08em] text-slate-400 dark:text-slate-500">Filter Cepat</p>
                <div class="space-y-1">
                    <a href="{{ route('catalog.index', ['sort' => 'price_asc']) }}"
                       @click="sideDrawerOpen = false"
                       class="flex items-center justify-between px-3 py-2 rounded-lg text-sm text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white transition">
                        <span>Laptop Budget (Termurah)</span>
                        <span class="text-slate-400 dark:text-slate-500 text-xs">&rarr;</span>
                    </a>
                    <a href="{{ route('catalog.index', ['ram' => '16']) }}"
                       @click="sideDrawerOpen = false"
                       class="flex items-center justify-between px-3 py-2 rounded-lg text-sm text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white transition">
                        <span>Multitasking RAM 16 GB</span>
                        <span class="text-slate-400 dark:text-slate-500 text-xs">&rarr;</span>
                    </a>
                    <a href="{{ route('catalog.index', ['max_weight' => '1.3']) }}"
                       @click="sideDrawerOpen = false"
                       class="flex items-center justify-between px-3 py-2 rounded-lg text-sm text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white transition">
                        <span>Portabel (Bobot &le; 1.3 kg)</span>
                        <span class="text-slate-400 dark:text-slate-500 text-xs">&rarr;</span>
                    </a>
                    <a href="{{ route('catalog.index', ['sort' => 'versus_score']) }}"
                       @click="sideDrawerOpen = false"
                       class="flex items-center justify-between px-3 py-2 rounded-lg text-sm text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white transition">
                        <span>Skor Tertinggi (Top Tier)</span>
                        <span class="text-slate-400 dark:text-slate-500 text-xs">&rarr;</span>
                    </a>
                </div>
            </div>

            {{-- Theme Toggle (Mobile Drawer) --}}
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800">
                <div class="flex items-center justify-between px-3 py-2.5 rounded-lg bg-slate-50 dark:bg-slate-800/60 border border-slate-200/70 dark:border-slate-700/60">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-md flex items-center justify-center text-amber-500 dark:text-indigo-400 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700 shrink-0">
                            <svg x-show="!$store.theme.isDark" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                <circle cx="12" cy="12" r="5"/>
                                <path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42" stroke="currentColor" stroke-width="2"/>
                            </svg>
                            <svg x-show="$store.theme.isDark" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor" style="display:none;">
                                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                            </svg>
                        </div>
                        <div>
                            <span class="text-xs font-semibold text-slate-800 dark:text-slate-200 block">Tema Tampilan</span>
                            <span class="text-[11px] text-slate-500 dark:text-slate-400 font-mono" x-text="$store.theme.isDark ? 'Mode Gelap (Dark)' : 'Mode Terang (Light)'"></span>
                        </div>
                    </div>
                    <button type="button"
                            @click="$store.theme.toggle()"
                            role="switch"
                            :aria-checked="$store.theme.isDark ? 'true' : 'false'"
                            aria-label="Alihkan tema gelap atau terang"
                            class="relative inline-flex items-center h-7 w-12 p-0.5 rounded-full border transition-colors duration-200 cursor-pointer outline-none shadow-2xs"
                            :class="$store.theme.isDark ? 'bg-slate-950 border-slate-700' : 'bg-slate-200 border-slate-300'">
                        <span class="inline-block w-5 h-5 rounded-full bg-white dark:bg-slate-800 shadow transform transition-transform duration-300 ease-[cubic-bezier(0.34,1.56,0.64,1)] border border-slate-200/50 dark:border-slate-600"
                              :class="$store.theme.isDark ? 'translate-x-5' : 'translate-x-0'"></span>
                    </button>
                </div>
            </div>

        </div>

        {{-- 3. Docked Footer (User Session / Clean Auth) --}}
        <div class="p-4 border-t border-[#E2E8F0] dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50">
            @auth
                <div class="flex items-center gap-3 px-2 py-1.5 mb-3">
                    <div class="w-8 h-8 rounded-lg bg-slate-900 dark:bg-blue-600 text-white flex items-center justify-center text-xs font-mono font-bold shrink-0">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="truncate">
                        <div class="text-sm font-semibold text-slate-900 dark:text-slate-100 truncate">{{ auth()->user()->name }}</div>
                        <div class="text-xs text-slate-500 dark:text-slate-400 font-mono truncate">{{ auth()->user()->email }}</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full py-2 text-xs font-semibold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/40 border border-transparent hover:border-red-200 dark:hover:border-red-800 rounded-lg transition cursor-pointer text-center">
                        Keluar dari Akun
                    </button>
                </form>
            @else
                <div class="grid grid-cols-2 gap-2.5">
                    <a href="{{ route('login') }}"
                       class="text-center py-2.5 text-sm font-medium border border-[#E2E8F0] dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-lg transition shadow-2xs">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}"
                       class="text-center py-2.5 text-sm font-semibold bg-slate-900 hover:bg-slate-800 text-white rounded-lg transition shadow-2xs">
                        Daftar
                    </a>
                </div>
            @endauth
        </div>
    </aside>

    {{-- ─── MAIN WRAPPER (Push-Back Depth Pattern) ─────────────────── --}}
    <div class="min-h-screen bg-[#F8FAFC] dark:bg-[#0B0F17] text-slate-800 dark:text-slate-100 flex flex-col transition-all duration-300 ease-out origin-top shadow-2xl"
         :class="sideDrawerOpen ? 'scale-[0.96] translate-y-4 opacity-50 rounded-[24px] overflow-hidden pointer-events-none ring-1 ring-white/10' : 'scale-100 translate-y-0 opacity-100 rounded-none'">

        {{-- ─── HEADER ──────────────────────────────────────────────── --}}
        <header class="sticky top-0 z-40 bg-white/95 dark:bg-slate-900/95 backdrop-blur-xs border-b border-[#E2E8F0] dark:border-slate-800">
            <div class="max-w-[1360px] mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">

                {{-- Left: Hamburger + Logo --}}
                <div class="flex items-center gap-3.5">
                    <button type="button"
                            @click="sideDrawerOpen = true"
                            class="w-9 h-9 flex items-center justify-center text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition cursor-pointer"
                            aria-label="Buka menu navigasi"
                            :aria-expanded="sideDrawerOpen ? 'true' : 'false'">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                            <line x1="3" y1="6" x2="21" y2="6"/>
                            <line x1="3" y1="12" x2="21" y2="12"/>
                            <line x1="3" y1="18" x2="21" y2="18"/>
                        </svg>
                    </button>
                    <a href="{{ Auth::check() ? route('dashboard') : route('home') }}" class="flex items-center select-none" aria-label="Beranda LapFind">
                        <img src="{{ asset('images/lapfind-logo.png') }}"
                             alt="LapFind"
                             class="h-7 sm:h-8 w-auto object-contain">
                    </a>
                </div>

                {{-- Center: Searchbar + Interactive Nav Pills --}}
                <div class="hidden md:flex items-center gap-5">
                    {{-- Sleek Searchbar with Ctrl+K badge & Clear Button --}}
                    <form method="GET" action="{{ route('catalog.index') }}" role="search" class="relative"
                          x-data="{ query: '{{ addslashes(request('search', '')) }}' }">
                        <div class="relative flex items-center">
                            <svg class="absolute left-3 w-4 h-4 text-slate-400 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true">
                                <circle cx="11" cy="11" r="7"/>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                            <input type="text"
                                   id="header-search-input"
                                   name="search"
                                   x-model="query"
                                   placeholder="Cari laptop, prosesor, brand…"
                                   aria-label="Cari laptop, prosesor, atau brand"
                                   class="w-60 lg:w-72 text-sm bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700 focus:bg-white dark:focus:bg-slate-900 focus:border-slate-400 dark:focus:border-slate-500 rounded-lg pl-9 pr-14 py-2 text-slate-900 dark:text-slate-100 placeholder:text-slate-400 dark:placeholder:text-slate-500 transition outline-none">

                            {{-- Clear Search Button (when text exists) --}}
                            <button type="button"
                                    x-show="query && query.length > 0"
                                    @click="query = ''; $nextTick(() => document.getElementById('header-search-input')?.focus())"
                                    class="absolute right-3 w-4.5 h-4.5 flex items-center justify-center rounded-full bg-slate-200 dark:bg-slate-700 text-slate-500 hover:text-slate-900 dark:hover:text-white transition cursor-pointer text-[10px]"
                                    aria-label="Kosongkan pencarian"
                                    style="display:none;">
                                ✕
                            </button>

                            {{-- Ctrl+K badge (when input is empty) --}}
                            <div x-show="!query || query.length === 0" class="absolute right-2.5 flex items-center pointer-events-none">
                                <kbd class="text-[11px] font-mono px-1.5 py-0.5 rounded border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-400 dark:text-slate-400 shadow-2xs">Ctrl K</kbd>
                            </div>
                        </div>
                    </form>

                    {{-- Interactive Nav Pills --}}
                    <nav class="flex items-center gap-1.5">
                        <a href="{{ route('catalog.index') }}"
                           class="text-sm font-medium px-3.5 py-2 rounded-lg transition-all duration-150 {{ request()->routeIs('catalog.index') || request()->routeIs('home') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white font-semibold' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            Katalog
                        </a>
                        <a href="{{ route('catalog.compare') }}"
                           class="text-sm font-medium px-3.5 py-2 rounded-lg transition-all duration-150 {{ request()->routeIs('catalog.compare') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white font-semibold' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            Bandingkan
                        </a>
                        <a href="{{ route('spk.index') }}"
                           class="text-sm font-medium px-3.5 py-2 rounded-lg transition-all duration-150 {{ request()->routeIs('spk.*') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white font-semibold' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            Smart Match
                        </a>
                        <a href="{{ route('history.index') }}"
                           class="text-sm font-medium px-3.5 py-2 rounded-lg transition-all duration-150 {{ request()->routeIs('history.*') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white font-semibold' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            Riwayat
                        </a>
                    </nav>
                </div>

                {{-- Right: Animated Theme Switch + VS counter + User menu --}}
                <div class="flex items-center gap-2.5">
                    {{-- ─── ANIMATED THEME SWITCH TOGGLE (WCAG 2.1 Accessible Switch) ─── --}}
                    <button type="button"
                            @click="$store.theme.toggle()"
                            role="switch"
                            :aria-checked="$store.theme.isDark ? 'true' : 'false'"
                            :aria-label="$store.theme.isDark ? 'Alihkan ke Mode Terang (Alt+T)' : 'Alihkan ke Mode Gelap (Alt+T)'"
                            :title="$store.theme.isDark ? 'Mode Gelap aktif (Klik untuk Terang, Alt+T)' : 'Mode Terang aktif (Klik untuk Gelap, Alt+T)'"
                            class="relative inline-flex items-center h-8 w-[58px] p-0.5 rounded-full border transition-all duration-300 ease-in-out cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-900 select-none group shadow-2xs"
                            :class="$store.theme.isDark 
                                ? 'bg-slate-950/90 border-slate-700/80 hover:border-slate-600' 
                                : 'bg-slate-100 hover:bg-slate-200/80 border-slate-200/90 hover:border-slate-300'">

                        {{-- Track Background Icons --}}
                        <span class="absolute inset-0 flex items-center justify-between px-1.5 pointer-events-none">
                            {{-- Mini Sun (Left) --}}
                            <svg class="w-3.5 h-3.5 text-amber-500 transition-opacity duration-200"
                                 :class="$store.theme.isDark ? 'opacity-30' : 'opacity-90'"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="12" cy="12" r="4"/>
                                <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/>
                            </svg>

                            {{-- Mini Moon (Right) --}}
                            <svg class="w-3.5 h-3.5 text-indigo-400 transition-opacity duration-200"
                                 :class="$store.theme.isDark ? 'opacity-90' : 'opacity-30'"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                            </svg>
                        </span>

                        {{-- Sliding Thumb / Knob with morphing & rotating icons --}}
                        <span class="relative z-10 inline-flex items-center justify-center w-6 h-6 rounded-full shadow-sm transform transition-all duration-300 ease-[cubic-bezier(0.34,1.56,0.64,1)] border"
                              :class="$store.theme.isDark 
                                  ? 'translate-x-[26px] bg-slate-900 text-indigo-300 border-indigo-500/30 shadow-indigo-950/50' 
                                  : 'translate-x-0 bg-white text-amber-500 border-amber-200/80 shadow-slate-300/40'">

                            {{-- Sun Icon --}}
                            <svg class="w-3.5 h-3.5 absolute transform transition-all duration-300"
                                 :class="$store.theme.isDark ? 'rotate-90 scale-0 opacity-0' : 'rotate-0 scale-100 opacity-100'"
                                 viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="5" fill="currentColor"/>
                                <line x1="12" y1="1" x2="12" y2="3" stroke="currentColor" stroke-width="2"/>
                                <line x1="12" y1="21" x2="12" y2="23" stroke="currentColor" stroke-width="2"/>
                                <line x1="4.22" y1="4.22" x2="5.64" y2="5.64" stroke="currentColor" stroke-width="2"/>
                                <line x1="18.36" y1="18.36" x2="19.78" y2="19.78" stroke="currentColor" stroke-width="2"/>
                                <line x1="1" y1="12" x2="3" y2="12" stroke="currentColor" stroke-width="2"/>
                                <line x1="21" y1="12" x2="23" y2="12" stroke="currentColor" stroke-width="2"/>
                                <line x1="4.22" y1="19.78" x2="5.64" y2="18.36" stroke="currentColor" stroke-width="2"/>
                                <line x1="18.36" y1="5.64" x2="19.78" y2="4.22" stroke="currentColor" stroke-width="2"/>
                            </svg>

                            {{-- Moon Icon --}}
                            <svg class="w-3.5 h-3.5 absolute transform transition-all duration-300"
                                 :class="$store.theme.isDark ? 'rotate-0 scale-100 opacity-100' : '-rotate-90 scale-0 opacity-0'"
                                 viewBox="0 0 24 24" fill="currentColor" stroke="none">
                                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                            </svg>
                        </span>
                    </button>

                    {{-- Dynamic VS compare counter badge --}}
                    <a href="{{ route('catalog.compare') }}"
                       class="inline-flex items-center gap-2 px-3 py-2 bg-slate-50 dark:bg-slate-800/80 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 text-xs font-semibold rounded-lg transition font-mono shadow-2xs"
                       title="Buka Halaman Perbandingan">
                        <span class="font-bold text-xs">VS</span>
                        <span x-text="$store.compare.count"
                              class="min-w-[18px] text-center bg-blue-600 text-white px-1.5 py-0.5 rounded text-[11px] font-bold">0</span>
                    </a>

                    @auth
                        @if (auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}"
                               class="hidden sm:inline-flex items-center px-3 py-2 text-xs font-semibold text-purple-700 bg-purple-50 border border-purple-200 hover:bg-purple-100 rounded-lg transition font-mono">
                                ADM
                            </a>
                        @endif

                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                    type="button"
                                    class="flex items-center gap-2 text-sm font-medium text-slate-700 dark:text-slate-200 hover:text-slate-900 dark:hover:text-white px-2.5 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer transition">
                                <span class="hidden sm:block truncate max-w-[120px]">{{ auth()->user()->name }}</span>
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="2 4 6 8 10 4"/>
                                </svg>
                            </button>
                            <div x-show="open"
                                 @click.away="open = false"
                                 x-transition
                                 class="absolute right-0 mt-2 w-52 bg-white dark:bg-slate-900 border border-[#E2E8F0] dark:border-slate-800 rounded-lg shadow-lg py-1.5 z-50 text-sm"
                                 style="display:none;">
                                <div class="px-4 py-2.5 text-xs text-slate-500 dark:text-slate-400 font-mono border-b border-[#E2E8F0] dark:border-slate-800 truncate">
                                    {{ auth()->user()->email }}
                                </div>
                                <a href="{{ route('history.index') }}" class="block px-4 py-2 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white">Riwayat Evaluasi</a>
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white">Pengaturan Akun</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/40 border-t border-[#E2E8F0] dark:border-slate-800 cursor-pointer">
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white px-2.5 py-2 transition">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}"
                           class="hidden sm:inline-flex items-center px-3.5 py-2 bg-slate-900 hover:bg-slate-800 text-white text-sm font-semibold rounded-lg shadow-2xs transition">
                            Daftar
                        </a>
                    @endauth
                </div>
            </div>
        </header>

        {{-- ─── MAIN CONTENT ─────────────────────────────────────────── --}}
        <main id="main-content" tabindex="-1" class="flex-1 w-full max-w-[1360px] mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10 outline-none">
            @if (session('success'))
                <div role="alert" class="mb-6 px-4.5 py-3.5 bg-green-50 border border-green-200 text-green-800 text-sm rounded-lg flex items-center justify-between">
                    <span>{{ session('success') }}</span>
                    <button type="button" onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800 ml-3 p-1 cursor-pointer" aria-label="Tutup notifikasi">✕</button>
                </div>
            @endif

            @if (session('error'))
                <div role="alert" class="mb-6 px-4.5 py-3.5 bg-red-50 border border-red-200 text-red-800 text-sm rounded-lg flex items-center justify-between">
                    <span>{{ session('error') }}</span>
                    <button type="button" onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800 ml-3 p-1 cursor-pointer" aria-label="Tutup notifikasi">✕</button>
                </div>
            @endif

            @yield('content')
        </main>

        {{-- ─── STRUCTURED ACADEMIC-TECH FOOTER (4 COLUMNS) ─────────── --}}
        <footer class="border-t border-[#E2E8F0] dark:border-slate-800 bg-white dark:bg-[#0B0F17] text-sm">
            <div class="max-w-[1360px] mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">

                    {{-- Col 1: Brand & Status --}}
                    <div class="space-y-4">
                        <div class="flex items-center gap-2">
                            <a href="{{ Auth::check() ? route('dashboard') : route('home') }}" aria-label="LapFind">
                                <img src="{{ asset('images/lapfind-logo.png') }}" alt="LapFind" class="h-7 w-auto object-contain">
                            </a>
                        </div>
                        <p class="text-slate-600 text-xs sm:text-sm leading-relaxed">
                            Sistem Pendukung Keputusan (SPK) multi-kriteria untuk pemilihan laptop mahasiswa berbasis integrasi metode <strong>AHP</strong> dan <strong>TOPSIS</strong>.
                        </p>
                        <div>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md border border-emerald-200 bg-emerald-50 text-emerald-700 font-mono text-xs font-medium">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                Sistem Operasional
                            </span>
                        </div>
                    </div>

                    {{-- Col 2: Navigation Modules --}}
                    <div>
                        <h3 class="text-xs font-mono font-bold uppercase tracking-wider text-slate-900 dark:text-slate-100 mb-4">
                            Modul Platform
                        </h3>
                        <ul class="space-y-2.5 text-xs sm:text-sm text-slate-600 dark:text-slate-400">
                            <li>
                                <a href="{{ route('catalog.index') }}" class="hover:text-slate-900 dark:hover:text-slate-100 transition">Katalog & Spesifikasi Laptop</a>
                            </li>
                            <li>
                                <a href="{{ route('catalog.compare') }}" class="hover:text-slate-900 dark:hover:text-slate-100 transition">Komparasi Head-to-Head (Radar)</a>
                            </li>
                            <li>
                                <a href="{{ route('spk.index') }}" class="hover:text-slate-900 dark:hover:text-slate-100 transition">Smart Match Evaluasi AHP-TOPSIS</a>
                            </li>
                            <li>
                                <a href="{{ route('history.index') }}" class="hover:text-slate-900 dark:hover:text-slate-100 transition">Riwayat Rekomendasi Pengguna</a>
                            </li>
                        </ul>
                    </div>

                    {{-- Col 3: SPK Science Parameters --}}
                    <div>
                        <h3 class="text-xs font-mono font-bold uppercase tracking-wider text-slate-900 dark:text-slate-100 mb-4">
                            Parameter Model Sains
                        </h3>
                        <ul class="space-y-2 text-xs font-mono text-slate-600 dark:text-slate-400">
                            <li class="flex items-center justify-between">
                                <span>C1: Harga Beli</span>
                                <span class="text-amber-600 dark:text-amber-400 font-semibold">[Cost]</span>
                            </li>
                            <li class="flex items-center justify-between">
                                <span>C2: Performa Prosesor</span>
                                <span class="text-blue-600 dark:text-blue-400 font-semibold">[Benefit]</span>
                            </li>
                            <li class="flex items-center justify-between">
                                <span>C3: Kapasitas RAM</span>
                                <span class="text-blue-600 dark:text-blue-400 font-semibold">[Benefit]</span>
                            </li>
                            <li class="flex items-center justify-between">
                                <span>C4: Kapasitas SSD</span>
                                <span class="text-blue-600 dark:text-blue-400 font-semibold">[Benefit]</span>
                            </li>
                            <li class="flex items-center justify-between">
                                <span>C5: Bobot Fisik</span>
                                <span class="text-amber-600 dark:text-amber-400 font-semibold">[Cost]</span>
                            </li>
                            <li class="flex items-center justify-between">
                                <span>C6: Daya Baterai</span>
                                <span class="text-blue-600 dark:text-blue-400 font-semibold">[Benefit]</span>
                            </li>
                        </ul>
                    </div>

                    {{-- Col 4: Technical & IMK Compliance --}}
                    <div>
                        <h3 class="text-xs font-mono font-bold uppercase tracking-wider text-slate-900 dark:text-slate-100 mb-4">
                            Standar & Kepatuhan
                        </h3>
                        <ul class="space-y-2.5 text-xs text-slate-500 dark:text-slate-400">
                            <li class="flex items-start gap-2">
                                <span class="text-slate-400 dark:text-slate-500 font-mono font-bold">v2.0</span>
                                <span>AHP Saaty (1980) & TOPSIS Hwang-Yoon (1981)</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-slate-400 dark:text-slate-500 font-mono font-bold">IMK</span>
                                <span>10 Heuristik Usabilitas Nielsen & WCAG 2.1 AA</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-slate-400 dark:text-slate-500 font-mono font-bold">DEV</span>
                                <span>Laravel 11 &bull; Tailwind CSS v3 &bull; Alpine.js</span>
                            </li>
                        </ul>
                    </div>

                </div>

                {{-- Bottom Bar --}}
                <div class="mt-10 pt-6 border-t border-[#E2E8F0] dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500 dark:text-slate-400">
                    <p>&copy; {{ date('Y') }} <span class="font-semibold text-slate-700 dark:text-slate-300">LapFind</span>. Dirancang untuk pengambilan keputusan akademis yang transparan.</p>
                    <div class="flex items-center gap-4 font-mono text-xs text-slate-400 dark:text-slate-500">
                        <span>CR &le; 0.10</span>
                        <span>&bull;</span>
                        <span>Euclidean TOPSIS</span>
                        <span>&bull;</span>
                        <span>Build 2026.11</span>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    {{-- ─── COMPARE TRAY (floating) ─────────────────────────────────── --}}
    <div x-data="{ trayCollapsed: false }"
         x-show="$store.compare.count > 0 && !window.location.pathname.startsWith('/bandingkan')"
         class="fixed bottom-4 right-4 z-50">

        {{-- Expanded tray --}}
        <div x-show="!trayCollapsed"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="scale-95 translate-y-2 opacity-0"
             x-transition:enter-end="scale-100 translate-y-0 opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="scale-100 translate-y-0 opacity-100"
             x-transition:leave-end="scale-95 translate-y-2 opacity-0"
             class="w-80 bg-white dark:bg-slate-900 border border-[#CBD5E1] dark:border-slate-700 rounded-lg shadow-xl overflow-hidden">

            {{-- Header --}}
            <div class="flex items-center justify-between px-4 py-3 bg-slate-900 text-white cursor-pointer"
                 @click="trayCollapsed = true">
                <div class="flex items-center gap-2.5">
                    <span class="text-sm font-mono font-bold">VS Compare</span>
                    <span class="text-xs font-mono bg-white/20 px-2 py-0.5 rounded font-bold" x-text="$store.compare.count + '/3'"></span>
                </div>
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="2 4 6 8 10 4"/>
                </svg>
            </div>

            {{-- Items --}}
            <div class="p-3.5 space-y-2 max-h-64 overflow-y-auto no-scrollbar">
                <template x-for="item in $store.compare.items" :key="item.id">
                    <div class="flex items-center justify-between gap-2.5 py-2.5 border-b border-[#E2E8F0] dark:border-slate-800 last:border-0">
                        <div class="flex items-center gap-2.5 overflow-hidden">
                            <img :src="item.image" :alt="item.name"
                                 class="w-11 h-9 object-contain rounded bg-[#F8FAFC] dark:bg-slate-800 border border-[#E2E8F0] dark:border-slate-700 p-0.5 shrink-0">
                            <div class="truncate">
                                <div class="text-xs font-semibold text-slate-800 dark:text-slate-100 truncate" x-text="item.name"></div>
                                <div class="text-xs text-slate-500 dark:text-slate-400 font-mono" x-text="item.price"></div>
                            </div>
                        </div>
                        <button type="button"
                                @click="$store.compare.remove(item.id)"
                                class="text-slate-400 hover:text-red-600 p-1.5 transition cursor-pointer text-xs">✕</button>
                    </div>
                </template>

                <template x-if="$store.compare.count < 3">
                    <div class="text-center py-2.5 text-xs text-slate-400 dark:text-slate-500 border border-dashed border-[#E2E8F0] dark:border-slate-800 rounded-md font-mono">
                        + Tambah hingga 3 laptop
                    </div>
                </template>
            </div>

            {{-- Footer --}}
            <div class="px-3.5 pb-3.5 flex gap-2">
                <template x-if="$store.compare.count >= 2">
                    <a :href="$store.compare.compareUrl"
                       class="flex-1 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold py-2.5 rounded-lg text-center transition shadow-2xs cursor-pointer">
                        Bandingkan &rarr;
                    </a>
                </template>
                <template x-if="$store.compare.count < 2">
                    <button type="button" disabled
                            class="flex-1 bg-slate-100 dark:bg-slate-800 text-slate-400 text-xs font-medium py-2.5 rounded-lg text-center cursor-not-allowed">
                        Min. 2 laptop
                    </button>
                </template>
                <button type="button"
                        @click="$store.compare.clear()"
                        class="w-9 h-9 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center text-xs transition cursor-pointer"
                        title="Kosongkan daftar">
                    ✕
                </button>
            </div>
        </div>

        {{-- Collapsed pill --}}
        <button type="button"
                x-show="trayCollapsed"
                @click="trayCollapsed = false"
                x-transition
                aria-label="Buka kembali tray perbandingan laptop"
                class="bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-md px-3 py-1.5 text-xs font-mono shadow-md flex items-center gap-2 transition hover:border-slate-900 dark:hover:border-slate-500 cursor-pointer">
            <span class="font-bold text-slate-900 dark:text-slate-100"><span x-text="$store.compare.count"></span> Dipilih</span>
            <span class="text-slate-500 text-[10px]">▲</span>
        </button>
    </div>

    {{-- ─── ACCESSIBILITY / TOAST FEEDBACK (WCAG & HEURISTIC #1) ─────── --}}
    <div x-data
         x-show="$store.toast.visible"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-3"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-3"
         role="status"
         aria-live="polite"
         class="fixed bottom-5 left-1/2 -translate-x-1/2 z-50 px-4 py-2.5 rounded-md shadow-lg border text-xs font-medium flex items-center gap-2.5 pointer-events-auto"
         :class="{
             'bg-slate-900 text-white border-slate-800': $store.toast.type === 'info',
             'bg-emerald-950 text-emerald-100 border-emerald-800': $store.toast.type === 'success',
             'bg-amber-950 text-amber-100 border-amber-800': $store.toast.type === 'warning'
         }"
         style="display:none;">
        <span x-text="$store.toast.message"></span>
        <button type="button"
                @click="$store.toast.visible = false"
                class="ml-2 text-white/70 hover:text-white p-1 cursor-pointer"
                aria-label="Tutup notifikasi">✕</button>
    </div>

</body>
</html>
