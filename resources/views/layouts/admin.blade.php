<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Panel Admin' }} — SPK Pemilihan Laptop</title>

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-950 text-ink antialiased min-h-screen selection:bg-accent/20 selection:text-accent"
      x-data="{ mobileMenuOpen: false }">

    <!-- Mobile Drawer Overlay / Navigation with Push-Back Depth Pattern -->
    <div x-show="mobileMenuOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="mobileMenuOpen = false"
         class="fixed inset-0 z-40 bg-slate-900/40 md:hidden"
         style="display: none;">
    </div>

    <!-- Mobile Drawer -->
    <aside x-show="mobileMenuOpen"
           x-transition:enter="transition ease-out duration-200"
           x-transition:enter-start="-translate-x-full"
           x-transition:enter-end="translate-x-0"
           x-transition:leave="transition ease-in duration-150"
           x-transition:leave-start="translate-x-0"
           x-transition:leave-end="-translate-x-full"
           class="fixed inset-y-0 left-0 z-50 w-72 bg-canvas border-r border-line p-6 flex flex-col justify-between md:hidden no-scrollbar overflow-y-auto"
           style="display: none;">
        <div>
            <div class="flex items-center justify-between pb-6 border-b border-line">
                <div>
                    <span class="text-xs uppercase tracking-widest text-ink-muted font-mono">Panel Kelola</span>
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 mt-1 hover:opacity-85 transition">
                        <img src="{{ asset('images/lapfind-logo.png') }}" alt="LapFind" class="h-5 w-auto object-contain">
                        <span class="text-[10px] bg-slate-100 text-slate-700 px-1 py-0.5 rounded font-mono font-bold">Admin</span>
                    </a>
                </div>
                <button type="button" @click="mobileMenuOpen = false" class="p-1.5 text-ink-muted hover:text-ink text-sm">
                    ✕
                </button>
            </div>
            <nav class="mt-6 space-y-1 text-sm font-medium">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-3 py-2 rounded {{ request()->routeIs('admin.dashboard') ? 'bg-surface-subtle text-accent font-semibold border-l-2 border-accent' : 'text-ink-muted hover:text-ink hover:bg-surface-subtle' }}">
                    <x-icon name="chart" class="w-4 h-4 mr-2.5 text-accent" />
                    <span>Ringkasan Sistem</span>
                </a>
                <a href="{{ route('admin.criteria.index') }}" class="flex items-center px-3 py-2 rounded {{ request()->routeIs('admin.criteria.*') ? 'bg-surface-subtle text-accent font-semibold border-l-2 border-accent' : 'text-ink-muted hover:text-ink hover:bg-surface-subtle' }}">
                    <x-icon name="filter" class="w-4 h-4 mr-2.5 text-accent" />
                    <span>Kriteria Evaluasi</span>
                </a>
                <a href="{{ route('admin.alternatives.index') }}" class="flex items-center px-3 py-2 rounded {{ request()->routeIs('admin.alternatives.*') ? 'bg-surface-subtle text-accent font-semibold border-l-2 border-accent' : 'text-ink-muted hover:text-ink hover:bg-surface-subtle' }}">
                    <x-icon name="laptop" class="w-4 h-4 mr-2.5 text-accent" />
                    <span>Katalog Laptop</span>
                </a>
                <a href="{{ route('admin.values.index') }}" class="flex items-center px-3 py-2 rounded {{ request()->routeIs('admin.values.*') ? 'bg-surface-subtle text-accent font-semibold border-l-2 border-accent' : 'text-ink-muted hover:text-ink hover:bg-surface-subtle' }}">
                    <x-icon name="scale" class="w-4 h-4 mr-2.5 text-accent" />
                    <span>Matriks Keputusan (X)</span>
                </a>
                <a href="{{ route('admin.users.index') }}" class="flex items-center px-3 py-2 rounded {{ request()->routeIs('admin.users.*') ? 'bg-surface-subtle text-accent font-semibold border-l-2 border-accent' : 'text-ink-muted hover:text-ink hover:bg-surface-subtle' }}">
                    <x-icon name="user" class="w-4 h-4 mr-2.5 text-accent" />
                    <span>Manajemen Pengguna</span>
                </a>
                <a href="{{ route('admin.audit-logs.index') }}" class="flex items-center px-3 py-2 rounded {{ request()->routeIs('admin.audit-logs.*') ? 'bg-surface-subtle text-accent font-semibold border-l-2 border-accent' : 'text-ink-muted hover:text-ink hover:bg-surface-subtle' }}">
                    <x-icon name="shield" class="w-4 h-4 mr-2.5 text-accent" />
                    <span>Log Audit Aktivitas</span>
                </a>
            </nav>
        </div>

        <div class="pt-6 border-t border-line text-xs text-ink-muted space-y-2">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-1.5 text-ink hover:text-accent font-medium">
                <x-icon name="arrow-left" class="w-3.5 h-3.5" />
                <span>Portal Publik</span>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-rose-700 hover:underline block w-full text-left">Keluar dari Akun</button>
            </form>
        </div>
    </aside>

    <!-- Main Content Wrapper (With Push-Back Depth scaling when mobile menu opens) -->
    <div :class="mobileMenuOpen ? 'scale-[0.96] translate-y-4 opacity-75 rounded-[24px] pointer-events-none' : 'scale-100 translate-y-0 opacity-100 rounded-none'"
         class="transition-all duration-200 ease-out min-h-screen bg-canvas origin-top flex">

        <!-- Desktop Sidebar -->
        <aside class="hidden md:flex md:w-64 md:flex-col md:fixed md:inset-y-0 bg-canvas border-r border-line z-20">
            <div class="p-6 border-b border-line">
                <span class="text-[10px] uppercase tracking-widest text-ink-muted font-mono block">Administrasi SPK</span>
                <a href="{{ route('admin.dashboard') }}" class="mt-1 text-base font-semibold text-ink tracking-tight hover:text-accent transition flex items-center gap-2">
                    <img src="{{ asset('images/lapfind-logo.png') }}" alt="LapFind" class="h-6 w-auto object-contain">
                    <span class="text-[10px] bg-slate-100 text-slate-700 px-1.5 py-0.5 rounded font-mono font-bold">Admin</span>
                </a>
                <p class="text-xs text-ink-muted mt-1">AHP & TOPSIS Intelligence</p>
            </div>

            <nav class="flex-1 px-4 py-5 space-y-1 text-sm font-medium overflow-y-auto no-scrollbar">
                <div class="px-2 py-1 text-[11px] font-mono uppercase tracking-wider text-ink-muted/80">Utama</div>
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center px-3 py-2 rounded text-xs transition {{ request()->routeIs('admin.dashboard') ? 'bg-surface text-ink font-semibold border border-line shadow-xs' : 'text-ink-muted hover:text-ink hover:bg-surface-subtle' }}">
                    <x-icon name="chart" class="w-4 h-4 mr-2.5 text-accent" />
                    <span>Ringkasan Sistem</span>
                </a>

                <div class="pt-4 px-2 py-1 text-[11px] font-mono uppercase tracking-wider text-ink-muted/80">Data Master SPK</div>
                <a href="{{ route('admin.criteria.index') }}"
                   class="flex items-center px-3 py-2 rounded text-xs transition {{ request()->routeIs('admin.criteria.*') ? 'bg-surface text-ink font-semibold border border-line shadow-xs' : 'text-ink-muted hover:text-ink hover:bg-surface-subtle' }}">
                    <x-icon name="filter" class="w-4 h-4 mr-2.5 text-accent" />
                    <span>Kriteria Evaluasi</span>
                </a>
                <a href="{{ route('admin.alternatives.index') }}"
                   class="flex items-center px-3 py-2 rounded text-xs transition {{ request()->routeIs('admin.alternatives.*') ? 'bg-surface text-ink font-semibold border border-line shadow-xs' : 'text-ink-muted hover:text-ink hover:bg-surface-subtle' }}">
                    <x-icon name="laptop" class="w-4 h-4 mr-2.5 text-accent" />
                    <span>Alternatif Laptop</span>
                </a>
                <a href="{{ route('admin.values.index') }}"
                   class="flex items-center px-3 py-2 rounded text-xs transition {{ request()->routeIs('admin.values.*') ? 'bg-surface text-ink font-semibold border border-line shadow-xs' : 'text-ink-muted hover:text-ink hover:bg-surface-subtle' }}">
                    <x-icon name="scale" class="w-4 h-4 mr-2.5 text-accent" />
                    <span>Matriks Keputusan (X)</span>
                </a>

                <div class="pt-4 px-2 py-1 text-[11px] font-mono uppercase tracking-wider text-ink-muted/80">Akses Pengguna</div>
                <a href="{{ route('admin.users.index') }}"
                   class="flex items-center px-3 py-2 rounded text-xs transition {{ request()->routeIs('admin.users.*') ? 'bg-surface text-ink font-semibold border border-line shadow-xs' : 'text-ink-muted hover:text-ink hover:bg-surface-subtle' }}">
                    <x-icon name="user" class="w-4 h-4 mr-2.5 text-accent" />
                    <span>Manajemen Pengguna</span>
                </a>

                <div class="pt-4 px-2 py-1 text-[11px] font-mono uppercase tracking-wider text-ink-muted/80">Keamanan & Audit</div>
                <a href="{{ route('admin.audit-logs.index') }}"
                   class="flex items-center px-3 py-2 rounded text-xs transition {{ request()->routeIs('admin.audit-logs.*') ? 'bg-surface text-ink font-semibold border border-line shadow-xs' : 'text-ink-muted hover:text-ink hover:bg-surface-subtle' }}">
                    <x-icon name="shield" class="w-4 h-4 mr-2.5 text-accent" />
                    <span>Log Audit Aktivitas</span>
                </a>
            </nav>

            <div class="p-4 border-t border-line text-xs text-ink-muted">
                <div class="flex items-center justify-between mb-3">
                    <span class="truncate font-medium text-ink">{{ auth()->user()->name }}</span>
                    <x-badge type="admin">Admin</x-badge>
                </div>
                <div class="flex items-center justify-between pt-2 border-t border-line-subtle text-[11px]">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-1 text-ink-muted hover:text-accent font-medium transition">
                        <x-icon name="arrow-left" class="w-3 h-3" />
                        <span>Portal Publik</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-rose-700 hover:text-rose-900 transition">
                            Keluar
                        </button>
                    </form>
                </div>
        </aside>

        <!-- Right Side: Header + Main View -->
        <div class="flex-1 flex flex-col md:pl-64 min-w-0">
            <!-- Mobile Top Bar -->
            <header class="md:hidden flex items-center justify-between px-4 py-3 bg-canvas border-b border-line sticky top-0 z-10">
                <button type="button" @click="mobileMenuOpen = true" class="p-1.5 border border-line rounded text-xs font-mono text-ink">
                    ☰ Menu
                </button>
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-1.5 hover:opacity-85 transition">
                    <img src="{{ asset('images/lapfind-logo.png') }}" alt="LapFind" class="h-4 w-auto object-contain">
                    <span class="text-[10px] bg-slate-100 text-slate-700 px-1 py-0.2 rounded font-mono font-bold">Admin</span>
                </a>
                <span class="text-[11px] font-mono text-ink-muted">{{ auth()->user()->role }}</span>
            </header>

            <!-- Page Container (restricted width ~1120px, left-aligned) -->
            <main class="flex-1 w-full max-w-[1120px] px-4 sm:px-8 py-8">
                <!-- Flash Notification Banner -->
                @if (session('success'))
                    <div class="mb-6 p-3 bg-indigo-50 border border-indigo-200 text-indigo-950 text-xs rounded flex items-start justify-between shadow-2xs">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-indigo-700">✓</span>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 p-3 bg-rose-50 border border-rose-200 text-rose-900 text-xs rounded flex items-start justify-between">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-rose-700">✕</span>
                            <span>{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 p-3 bg-rose-50 border border-rose-200 text-rose-900 text-xs rounded">
                        <div class="font-semibold mb-1">Terdapat kesalahan pada input formulir:</div>
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Yielding Header & Content -->
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
