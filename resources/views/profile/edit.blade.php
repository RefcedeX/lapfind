@extends('layouts.public', ['title' => 'Pengaturan Akun & Profil'])

@section('content')
<div class="space-y-8 max-w-7xl mx-auto">

    {{-- ─── BREADCRUMB & HEADER ───────────────────────────────────────── --}}
    <div class="border-b border-slate-200/80 dark:border-slate-800 pb-6">
        <nav class="flex items-center gap-2 text-xs font-mono text-slate-500 dark:text-slate-400 mb-3" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition flex items-center gap-1">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
                <span>Beranda</span>
            </a>
            <span>/</span>
            <a href="{{ route('dashboard') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition">Dashboard</a>
            <span>/</span>
            <span class="text-slate-900 dark:text-white font-semibold">Pengaturan Akun</span>
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 text-[11px] font-mono uppercase tracking-wider text-blue-600 dark:text-blue-400 font-bold mb-1">
                    <span class="w-2 h-2 rounded-full bg-blue-600 dark:bg-blue-400"></span>
                    <span>Preferensi & Keamanan Akun</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                    Pengaturan Akun
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1 max-w-2xl leading-relaxed">
                    Kelola identitas personal, alamat email, dan kredensial keamanan akun LapFind Anda.
                </p>
            </div>

            <div class="flex items-center gap-2.5 self-start sm:self-auto">
                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 transition shadow-2xs">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="19" y1="12" x2="5" y2="12"/>
                        <polyline points="12 19 5 12 12 5"/>
                    </svg>
                    <span>Kembali ke Dashboard</span>
                </a>
            </div>
        </div>
    </div>

    {{-- ─── 2-COLUMN PROFILE WORKSPACE ───────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        {{-- LEFT COLUMN: User Summary & ID Badge Card --}}
        <div class="lg:col-span-4 lg:sticky lg:top-24 space-y-6">
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-6 shadow-2xs overflow-hidden relative">
                
                {{-- Decorative gradient bar --}}
                <div class="absolute top-0 inset-x-0 h-2 bg-gradient-to-r from-blue-600 via-indigo-600 to-cyan-500"></div>

                <div class="flex flex-col items-center text-center pt-2">
                    {{-- Avatar Monogram --}}
                    <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white font-mono font-bold text-3xl flex items-center justify-center shadow-lg shadow-blue-500/20 ring-4 ring-blue-500/10 mb-4">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>

                    <h2 class="text-lg font-bold text-slate-900 dark:text-white truncate max-w-full">
                        {{ $user->name }}
                    </h2>
                    <p class="text-xs font-mono text-slate-500 dark:text-slate-400 mt-0.5 truncate max-w-full">
                        {{ $user->email }}
                    </p>

                    <div class="mt-3">
                        @if ($user->isAdmin())
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800/70">
                                <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>
                                Administrator
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800/70">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                Mahasiswa
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Metadata Grid --}}
                <div class="mt-6 pt-5 border-t border-slate-100 dark:border-slate-800 space-y-3">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500 dark:text-slate-400">Status Akun</span>
                        <span class="inline-flex items-center gap-1.5 font-semibold text-emerald-600 dark:text-emerald-400 font-mono">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Aktif
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500 dark:text-slate-400">Terdaftar Sejak</span>
                        <span class="font-mono text-slate-700 dark:text-slate-300">
                            {{ $user->created_at ? $user->created_at->translatedFormat('d M Y') : '-' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500 dark:text-slate-400">Evaluasi SPK</span>
                        <span class="font-mono text-slate-700 dark:text-slate-300 font-semibold">
                            {{ $user->spkResults()->count() }} sesi tersimpan
                        </span>
                    </div>
                </div>

                {{-- Fast Shortcut Links --}}
                <div class="mt-6 pt-5 border-t border-slate-100 dark:border-slate-800 space-y-2">
                    <p class="text-[11px] font-mono font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-2">
                        Akses Cepat
                    </p>
                    <a href="{{ route('history.index') }}"
                       class="flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800/80 hover:text-blue-600 dark:hover:text-blue-400 transition">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                            <span>Riwayat Evaluasi SPK</span>
                        </span>
                        <span class="text-slate-400">&rarr;</span>
                    </a>
                    <a href="{{ route('spk.index') }}"
                       class="flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800/80 hover:text-blue-600 dark:hover:text-blue-400 transition">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                            </svg>
                            <span>Mulai Rekomendasi Baru</span>
                        </span>
                        <span class="text-slate-400">&rarr;</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN: Forms --}}
        <div class="lg:col-span-8 space-y-6">

            {{-- 1. Profile Information --}}
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-6 sm:p-8 shadow-2xs">
                @include('profile.partials.update-profile-information-form')
            </div>

            {{-- 2. Update Password --}}
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-6 sm:p-8 shadow-2xs">
                @include('profile.partials.update-password-form')
            </div>

            {{-- 3. Delete Account (Danger Zone) --}}
            <div class="bg-rose-50/20 dark:bg-rose-950/10 border border-rose-200/70 dark:border-rose-900/40 rounded-2xl p-6 sm:p-8 shadow-2xs">
                @include('profile.partials.delete-user-form')
            </div>

        </div>

    </div>

</div>
@endsection
