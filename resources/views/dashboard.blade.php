@extends('layouts.public', ['title' => 'Dashboard Mahasiswa'])

@section('content')
@php
    $user = $user ?? auth()->user();
    $latestResult = $latestResult ?? $user?->spkResults()->latest()->first();
    $savedCount = $savedCount ?? ($user ? $user->spkResults()->count() : 0);
    $totalAlternatives = $totalAlternatives ?? \App\Models\Alternative::count();
    $criteria = $criteria ?? \App\Models\Criterion::orderBy('code')->get();
    $featuredAlternatives = $featuredAlternatives ?? \App\Models\Alternative::with('values.criterion')->latest()->take(3)->get();
@endphp

<div class="space-y-8 max-w-7xl mx-auto">

    {{-- ─── 1. COCKPIT COMMAND BAR (HERO SURFACE) ────────────────────── --}}
    <div class="border-b border-slate-200/80 dark:border-slate-800 pb-7">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div>
                <div class="flex items-center gap-2.5 mb-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-mono font-medium bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Mesin SPK Siap Komputasi
                    </span>
                    @if ($user->isAdmin())
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-mono font-semibold bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                            Administrator
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-mono font-semibold bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                            Mahasiswa
                        </span>
                    @endif
                </div>

                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                    Halo, {{ $user->name }}
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1 max-w-2xl leading-relaxed">
                    Pusat komputasi multi-kriteria untuk menemukan laptop kuliah paling ideal secara objektif berbasis AHP-TOPSIS.
                </p>
            </div>

            {{-- Primary Action Hub --}}
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('spk.index') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-semibold text-xs sm:text-sm rounded-lg shadow-sm hover:shadow transition cursor-pointer">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                    </svg>
                    <span>Mulai Smart Match</span>
                </a>

                <a href="{{ route('catalog.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 font-medium text-xs sm:text-sm rounded-lg shadow-2xs transition">
                    <svg class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="12" rx="2"/>
                        <path d="M2 20h20"/>
                    </svg>
                    <span>Jelajahi {{ $totalAlternatives }} Laptop</span>
                </a>
            </div>
        </div>
    </div>

    {{-- ─── 2. INTEGRATED TELEMETRY STRIP (BORDERLESS MINIMAL RIBBON) ──── --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 py-2">
        <div class="p-4 rounded-xl bg-slate-50/80 dark:bg-slate-900/60 border border-slate-200/70 dark:border-slate-800">
            <span class="text-xs text-slate-500 dark:text-slate-400 block font-medium">Basis Data Alternatif</span>
            <div class="mt-1 flex items-baseline gap-2">
                <span class="text-xl sm:text-2xl font-bold font-mono text-slate-900 dark:text-white">{{ $totalAlternatives }}</span>
                <span class="text-[11px] text-slate-500 dark:text-slate-400">Unit Terverifikasi</span>
            </div>
            <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">Katalog laptop terverifikasi</p>
        </div>

        <div class="p-4 rounded-xl bg-slate-50/80 dark:bg-slate-900/60 border border-slate-200/70 dark:border-slate-800">
            <span class="text-xs text-slate-500 dark:text-slate-400 block font-medium">Parameter Penilaian</span>
            <div class="mt-1 flex items-baseline gap-2">
                <span class="text-xl sm:text-2xl font-bold font-mono text-blue-600 dark:text-blue-400">{{ $criteria->count() }}</span>
                <span class="text-[11px] text-slate-500 dark:text-slate-400">Kriteria Sains</span>
            </div>
            <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">Cost & Benefit terstandarisasi</p>
        </div>

        <div class="p-4 rounded-xl bg-slate-50/80 dark:bg-slate-900/60 border border-slate-200/70 dark:border-slate-800">
            <span class="text-xs text-slate-500 dark:text-slate-400 block font-medium">Riwayat Sesi Anda</span>
            <div class="mt-1 flex items-baseline gap-2">
                <span class="text-xl sm:text-2xl font-bold font-mono text-slate-900 dark:text-white">{{ $savedCount }}</span>
                <span class="text-[11px] text-slate-500 dark:text-slate-400">Evaluasi Tersimpan</span>
            </div>
            <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">Dapat dicetak & diunduh PDF</p>
        </div>

        <div class="p-4 rounded-xl bg-slate-50/80 dark:bg-slate-900/60 border border-slate-200/70 dark:border-slate-800">
            <span class="text-xs text-slate-500 dark:text-slate-400 block font-medium">Metode Komputasi</span>
            <div class="mt-1 flex items-baseline gap-1.5">
                <span class="text-sm sm:text-base font-bold font-mono text-slate-800 dark:text-slate-200">AHP + TOPSIS</span>
            </div>
            <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">Konsistensi Saaty (CR &le; 0.10)</p>
        </div>
    </div>

    {{-- ─── 3. WORKSPACE CORE (MAIN WORKBENCH & SIDEBAR) ──────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        {{-- MAIN COLUMN (8 cols): Latest Session / Blueprint & Decision Presets --}}
        <div class="lg:col-span-8 space-y-8">

            {{-- 3A. Latest SPK Result OR Empty State Blueprint --}}
            @if ($latestResult)
                @php
                    $topRank = $latestResult->ranking_snapshot[0] ?? null;
                @endphp
                <div class="bg-gradient-to-br from-blue-900/10 via-slate-900/5 to-transparent dark:from-blue-950/30 dark:via-slate-900/40 dark:to-slate-950 border border-blue-200/80 dark:border-blue-900/50 rounded-2xl p-6 sm:p-7 relative overflow-hidden">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-blue-100 dark:border-blue-900/40">
                        <div>
                            <span class="inline-flex items-center gap-1.5 text-xs font-mono font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wider">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <polyline points="12 6 12 12 16 14"/>
                                </svg>
                                <span>Sesi Terakhir: {{ $latestResult->created_at->translatedFormat('d F Y, H:i') }} WIB</span>
                            </span>
                            <h2 class="text-lg font-bold text-slate-900 dark:text-white mt-1">
                                {{ $latestResult->title ?? 'Evaluasi SPK Laptop' }}
                            </h2>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('spk.result', $latestResult) }}"
                               class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold transition">
                                Buka Rekap
                            </a>
                            <a href="{{ route('spk.export_pdf', $latestResult) }}"
                               class="px-3 py-1.5 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-medium transition"
                               title="Unduh Berkas PDF">
                                Unduh PDF
                            </a>
                        </div>
                    </div>

                    @if ($topRank)
                        <div class="mt-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <span class="text-[11px] font-mono text-slate-500 dark:text-slate-400 block mb-0.5">Rekomendasi Utama Solusi Ideal (#1)</span>
                                <h3 class="text-xl font-extrabold text-slate-900 dark:text-white">
                                    {{ $topRank['name'] ?? '-' }}
                                </h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                    {{ $topRank['brand'] ?? '' }} &bull; Nilai Preferensi Relatif (V_1)
                                </p>
                            </div>
                            <div class="text-right sm:text-right">
                                <span class="text-2xl sm:text-3xl font-mono font-extrabold text-blue-600 dark:text-blue-400">
                                    {{ isset($topRank['preference_score']) ? number_format($topRank['preference_score'], 4) : '-' }}
                                </span>
                                <span class="text-[10px] text-slate-400 dark:text-slate-500 block font-mono">Skor Kedekatan Positif</span>
                            </div>
                        </div>
                    @endif
                </div>
            @else
                {{-- Guided Blueprint for First-time Users (Emptiness as moment for direction) --}}
                <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-6 sm:p-8">
                    <div class="max-w-2xl">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-mono font-semibold bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800 mb-3">
                            Panduan Keputusan Cepat
                        </span>
                        <h2 class="text-xl font-bold text-slate-900 dark:text-white">
                            Awali Pencarian Laptop Ideal untuk Kebutuhan Kuliah Anda
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
                            LapFind menggunakan kombinasi pembobotan hierarki analitik (AHP) untuk mengukur tingkat kepentingan kriteria Anda, lalu menggunakan TOPSIS untuk memeringkat 25 laptop berdasarkan kedekatan geometris terhadap solusi ideal.
                        </p>
                    </div>

                    {{-- 3-Step Process Indicator (Nielsen Heuristic: Visibility & Mental Model) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-6 pt-6 border-t border-slate-100 dark:border-slate-800">
                        <div class="space-y-1">
                            <div class="text-xs font-mono font-bold text-blue-600 dark:text-blue-400">Langkah 1</div>
                            <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Tentukan Prioritas</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">Pilih preset kebutuhan kuliah atau atur perbandingan berpasangan AHP.</p>
                        </div>
                        <div class="space-y-1">
                            <div class="text-xs font-mono font-bold text-blue-600 dark:text-blue-400">Langkah 2</div>
                            <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Uji Konsistensi</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">Sistem memverifikasi rasio konsistensi Saaty (CR &le; 0.10) agar hasil logis.</p>
                        </div>
                        <div class="space-y-1">
                            <div class="text-xs font-mono font-bold text-blue-600 dark:text-blue-400">Langkah 3</div>
                            <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Dapatkan Ranking</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">Lihat urutan laptop terbaik dari peringkat 1 hingga alternatif lainnya.</p>
                        </div>
                    </div>

                    <div class="mt-6 pt-4 flex items-center justify-between flex-wrap gap-4">
                        <a href="{{ route('spk.index') }}"
                           class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs sm:text-sm rounded-lg shadow-sm transition">
                            <span>Mulai Evaluasi Pertama Anda</span>
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="5" y1="12" x2="19" y2="12"/>
                                <polyline points="12 5 19 12 12 19"/>
                            </svg>
                        </a>
                        <span class="text-xs font-mono text-slate-400 dark:text-slate-500">Estimasi waktu: ~2 menit</span>
                    </div>
                </div>
            @endif

            {{-- 3B. Quick Decision Presets for Students (Action-oriented, Non-card format) --}}
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">
                            Pintasan Profil Kebutuhan Mahasiswa
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Pilih profil siap pakai sesuai orientasi studi atau aktivitas utama Anda di kampus.
                        </p>
                    </div>
                    <a href="{{ route('spk.index') }}" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline">
                        Kustomisasi Sendiri
                    </a>
                </div>

                {{-- Interactive Preset List (Clean Integrated Surface) --}}
                <div class="divide-y divide-slate-100 dark:divide-slate-800 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl overflow-hidden shadow-2xs">

                    {{-- Preset 1: Umum / Kuliah Harian --}}
                    <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-slate-50 dark:hover:bg-slate-800/60 transition group">
                        <div class="flex items-start gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                                    Mahasiswa Umum & Tugas Kuliah
                                </h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 leading-relaxed">
                                    Pengetikan dokumen, presentasi, browsing riset, dan rapat virtual harian.
                                </p>
                                <div class="flex items-center gap-2 mt-2">
                                    <span class="text-[11px] font-mono px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">Fokus: Harga Terjangkau (C1)</span>
                                    <span class="text-[11px] font-mono px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">Baterai Awet (C6)</span>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('spk.index', ['profil' => 'umum']) }}"
                           class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-semibold text-slate-700 dark:text-slate-200 bg-slate-100 dark:bg-slate-800 hover:bg-blue-600 hover:text-white dark:hover:bg-blue-600 dark:hover:text-white transition shrink-0">
                            <span>Gunakan Profil</span>
                        </a>
                    </div>

                    {{-- Preset 2: Komputasi / Coding --}}
                    <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-slate-50 dark:hover:bg-slate-800/60 transition group">
                        <div class="flex items-start gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="16 18 22 12 16 6"/>
                                    <polyline points="8 6 2 12 8 18"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                                    Informatika, Coding & Sains Data
                                </h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 leading-relaxed">
                                    Kompilasi kode program, container Docker, emulator, dan analisis data berkapasitas besar.
                                </p>
                                <div class="flex items-center gap-2 mt-2">
                                    <span class="text-[11px] font-mono px-2 py-0.5 rounded bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300">Fokus: Prosesor Kencang (C2)</span>
                                    <span class="text-[11px] font-mono px-2 py-0.5 rounded bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300">RAM 16 GB (C3)</span>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('spk.index', ['profil' => 'komputasi']) }}"
                           class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-semibold text-slate-700 dark:text-slate-200 bg-slate-100 dark:bg-slate-800 hover:bg-indigo-600 hover:text-white dark:hover:bg-indigo-600 dark:hover:text-white transition shrink-0">
                            <span>Gunakan Profil</span>
                        </a>
                    </div>

                    {{-- Preset 3: Mobilitas & Portabel --}}
                    <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-slate-50 dark:hover:bg-slate-800/60 transition group">
                        <div class="flex items-start gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-cyan-50 dark:bg-cyan-950/50 text-cyan-600 dark:text-cyan-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 16v1a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v1"/>
                                    <path d="M18 8h4a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2h-4"/>
                                    <circle cx="8" cy="12" r="2"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                                    Mobilitas Tinggi & Praktikum Lapangan
                                </h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 leading-relaxed">
                                    Mudah dibawa di ransel, ringan saat jalan kaki antargedung, baterai tahan seharian.
                                </p>
                                <div class="flex items-center gap-2 mt-2">
                                    <span class="text-[11px] font-mono px-2 py-0.5 rounded bg-cyan-50 dark:bg-cyan-950/40 text-cyan-700 dark:text-cyan-300">Fokus: Bobot &le; 1.3 kg (C5)</span>
                                    <span class="text-[11px] font-mono px-2 py-0.5 rounded bg-cyan-50 dark:bg-cyan-950/40 text-cyan-700 dark:text-cyan-300">Baterai Awet (C6)</span>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('spk.index', ['profil' => 'portabel']) }}"
                           class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-semibold text-slate-700 dark:text-slate-200 bg-slate-100 dark:bg-slate-800 hover:bg-cyan-600 hover:text-white dark:hover:bg-cyan-600 dark:hover:text-white transition shrink-0">
                            <span>Gunakan Profil</span>
                        </a>
                    </div>

                    {{-- Preset 4: Multimedia, Desain & Gaming --}}
                    <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-slate-50 dark:hover:bg-slate-800/60 transition group">
                        <div class="flex items-start gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-950/50 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <polygon points="5 3 19 12 5 21 5 3"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                                    Desain Grafis, Video Editing & Gaming
                                </h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 leading-relaxed">
                                    Render grafis tinggi, timeline video 4K, dan performa visual maksimal tanpa kompromi.
                                </p>
                                <div class="flex items-center gap-2 mt-2">
                                    <span class="text-[11px] font-mono px-2 py-0.5 rounded bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300">Fokus: CPU/GPU Monster (C2)</span>
                                    <span class="text-[11px] font-mono px-2 py-0.5 rounded bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300">SSD Cepat (C4)</span>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('spk.index', ['profil' => 'gaming']) }}"
                           class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-semibold text-slate-700 dark:text-slate-200 bg-slate-100 dark:bg-slate-800 hover:bg-purple-600 hover:text-white dark:hover:bg-purple-600 dark:hover:text-white transition shrink-0">
                            <span>Gunakan Profil</span>
                        </a>
                    </div>

                </div>
            </div>

        </div>

        {{-- SIDEBAR COLUMN (4 cols): Active Criteria, Catalog Snippet & Shortcuts --}}
        <div class="lg:col-span-4 space-y-6">

            {{-- 3C. 6 Active Evaluation Criteria (Recognition Over Recall) --}}
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-6 shadow-2xs">
                <div class="flex items-center justify-between pb-3.5 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white">
                            6 Kriteria Penilaian
                        </h2>
                        <span class="text-[11px] text-slate-500 dark:text-slate-400">Parameter dalam matriks AHP & TOPSIS</span>
                    </div>
                    <span class="text-xs font-mono font-bold text-blue-600 dark:text-blue-400">v2.0</span>
                </div>

                <div class="divide-y divide-slate-100 dark:divide-slate-800/80 mt-2">
                    @foreach ($criteria as $crit)
                        <div class="py-2.5 flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2.5">
                                <span class="w-6 h-6 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-mono font-bold flex items-center justify-center text-[10px]">
                                    {{ $crit->code }}
                                </span>
                                <div>
                                    <span class="font-semibold text-slate-800 dark:text-slate-200 block">{{ $crit->name }}</span>
                                    <span class="text-[10px] text-slate-400 dark:text-slate-500 font-mono">{{ $crit->unit ?? '-' }}</span>
                                </div>
                            </div>
                            <div>
                                @if ($crit->type === 'cost')
                                    <span class="text-[10px] font-mono font-semibold px-2 py-0.5 rounded bg-rose-50 dark:bg-rose-950/50 text-rose-700 dark:text-rose-300 border border-rose-200/60 dark:border-rose-900/40" title="Cost: Nilai semakin rendah semakin menguntungkan">
                                        Cost &darr;
                                    </span>
                                @else
                                    <span class="text-[10px] font-mono font-semibold px-2 py-0.5 rounded bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-900/40" title="Benefit: Nilai semakin tinggi semakin menguntungkan">
                                        Benefit &uarr;
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- 3D. Popular Laptops In Catalog (Direct Quick Comparison) --}}
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-6 shadow-2xs">
                <div class="flex items-center justify-between pb-3.5 border-b border-slate-100 dark:border-slate-800">
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">
                        Lini Laptop Unggulan
                    </h2>
                    <a href="{{ route('catalog.index') }}" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline">
                        Lihat Semua
                    </a>
                </div>

                <div class="space-y-3 mt-3">
                    @forelse ($featuredAlternatives as $laptop)
                        <div class="flex items-center justify-between gap-3 p-2.5 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/60 transition group">
                            <div class="flex items-center gap-3 truncate">
                                <div class="w-10 h-10 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center shrink-0 overflow-hidden border border-slate-200/60 dark:border-slate-700/60">
                                    @if ($laptop->image)
                                        <img src="{{ asset('storage/' . $laptop->image) }}" alt="{{ $laptop->name }}" class="w-full h-full object-cover">
                                    @else
                                        <svg class="w-5 h-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <rect x="3" y="4" width="18" height="12" rx="2"/>
                                            <path d="M2 20h20"/>
                                        </svg>
                                    @endif
                                </div>
                                <div class="truncate">
                                    <a href="{{ route('catalog.show', $laptop) }}" class="text-xs font-semibold text-slate-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition truncate block">
                                        {{ $laptop->name }}
                                    </a>
                                    <span class="text-[11px] font-mono text-slate-500 dark:text-slate-400">
                                        Rp {{ number_format($laptop->price, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                            <button type="button"
                                    @click="$store.compare.toggle({{ $laptop->id }}, '{{ addslashes($laptop->name) }}', '{{ $laptop->image ? asset('storage/' . $laptop->image) : '' }}', '{{ number_format($laptop->price, 0, ',', '.') }}')"
                                    class="shrink-0 text-[11px] font-mono px-2 py-1 rounded border border-slate-200 dark:border-slate-700 hover:border-blue-500 text-slate-600 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 transition cursor-pointer"
                                    :class="$store.compare.items.includes({{ $laptop->id }}) ? 'bg-blue-50 dark:bg-blue-950/50 text-blue-600 border-blue-400' : ''">
                                <span x-text="$store.compare.items.includes({{ $laptop->id }}) ? '✓ VS' : '+ VS'">+ VS</span>
                            </button>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-3 text-center">Belum ada alternatif laptop.</p>
                    @endforelse
                </div>
            </div>

            {{-- 3E. Account & Security Direct Shortcuts --}}
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/80 dark:border-slate-800 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-600 dark:text-slate-300 shrink-0">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="3"/>
                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-slate-800 dark:text-slate-200 block">Pengaturan Akun</span>
                        <span class="text-[11px] text-slate-400 dark:text-slate-500 font-mono">{{ $user->email }}</span>
                    </div>
                </div>
                <a href="{{ route('profile.edit') }}" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline">
                    Buka &rarr;
                </a>
            </div>

        </div>

    </div>

</div>
@endsection
