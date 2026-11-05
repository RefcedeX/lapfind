@extends('layouts.public', ['title' => 'Bobot Kriteria & Uji Konsistensi AHP — Langkah 2'])

@section('content')
<div class="space-y-8">

    {{-- ──────────────────────────────────────────────────────────────────
         STEPPER & HEADER
    ────────────────────────────────────────────────────────────────────── --}}
    <div class="border-b border-[#E2E8F0] pb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 text-xs font-mono uppercase tracking-wider text-[#2563EB] font-bold mb-1.5">
                <span>Langkah 2 dari 3: Hasil Pembobotan AHP & Uji Konsistensi</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">
                Bobot Prioritas Kriteria (Priority Vector)
            </h1>
            <p class="text-sm text-slate-600 mt-1.5 max-w-2xl leading-relaxed">
                Hasil sintesis eigen-vektor ternormalisasi bobot kriteria dan pengujian validitas rasio konsistensi Saaty (CR &le; 0.10).
            </p>
        </div>

        {{-- Linear Step Indicator --}}
        <div class="flex items-center gap-2 font-mono text-xs shrink-0">
            <span class="inline-flex items-center gap-1.5 px-3 py-2 bg-white border border-[#E2E8F0] text-slate-700 font-medium rounded-lg">
                <span class="text-emerald-600 font-bold">✓</span>
                <span>01. AHP</span>
            </span>
            <span class="text-[#CBD5E1]">/</span>
            <span class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-[#2563EB] text-white font-bold rounded-lg shadow-2xs">
                <span>02. Konsistensi</span>
            </span>
            <span class="text-[#CBD5E1]">/</span>
            <span class="inline-flex items-center px-3 py-2 bg-white border border-[#E2E8F0] text-slate-500 font-medium rounded-lg">
                <span>03. TOPSIS</span>
            </span>
        </div>
    </div>

    {{-- ──────────────────────────────────────────────────────────────────
         CONSISTENCY RATIO (CR) STATUS BANNER
    ────────────────────────────────────────────────────────────────────── --}}
    @if ($ahpResult['is_consistent'])
        <div class="p-5 sm:p-6 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/80 rounded-xl text-sm space-y-1.5 shadow-2xs">
            <div class="flex items-center gap-2.5 text-emerald-900 dark:text-emerald-200 font-bold text-sm sm:text-base">
                <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs">✓</span>
                <span>Matriks Perbandingan Dinyatakan KONSISTEN (CR = {{ number_format($ahpResult['cr'], 4) }} &le; 0.10)</span>
            </div>
            <p class="text-emerald-800 dark:text-emerald-300/90 text-xs sm:text-sm leading-relaxed pl-7.5">
                Rasio konsistensi memenuhi ambang batas ilmiah Saaty (&le; 10%). Bobot prioritas yang dihasilkan valid secara logis dan siap digunakan sebagai pembobot kriteria dalam metode TOPSIS.
            </p>
        </div>
    @else
        <div class="p-5 sm:p-6 bg-amber-50 dark:bg-amber-950/40 border border-amber-300 dark:border-amber-800/80 rounded-xl text-sm space-y-1.5 shadow-2xs">
            <div class="flex items-center gap-2.5 text-amber-900 dark:text-amber-200 font-bold text-sm sm:text-base">
                <span class="w-5 h-5 rounded-full bg-amber-600 text-white flex items-center justify-center text-xs">!</span>
                <span>PERINGATAN: Matriks Perbandingan TIDAK KONSISTEN (CR = {{ number_format($ahpResult['cr'], 4) }} &gt; 0.10)</span>
            </div>
            <p class="text-amber-800 dark:text-amber-300/90 text-xs sm:text-sm leading-relaxed pl-7.5">
                Terdapat kontradiksi logis pada penilaian perbandingan berpasangan. Anda tetap dapat melanjutkan, namun disarankan kembali ke Langkah 1 untuk meninjau skala perbandingan atau gunakan preset profil rekomendasi.
            </p>
        </div>
    @endif

    {{-- ──────────────────────────────────────────────────────────────────
         4 METRICS SUMMARY CARDS
    ────────────────────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-900 p-5 border border-[#E2E8F0] dark:border-slate-800 rounded-xl shadow-2xs">
            <span class="text-xs font-mono font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 block">
                Eigen Maksimum (&lambda; max)
            </span>
            <div class="mt-1.5 text-2xl font-bold font-mono text-slate-900 dark:text-slate-100">
                {{ number_format($ahpResult['lambda_max'], 4) }}
            </div>
            <span class="text-xs text-slate-500 dark:text-slate-400 font-mono mt-1 block">Nilai ideal &approx; n ({{ $ahpResult['n'] }})</span>
        </div>

        <div class="bg-white dark:bg-slate-900 p-5 border border-[#E2E8F0] dark:border-slate-800 rounded-xl shadow-2xs">
            <span class="text-xs font-mono font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 block">
                Indeks Konsistensi (CI)
            </span>
            <div class="mt-1.5 text-2xl font-bold font-mono text-slate-900 dark:text-slate-100">
                {{ number_format($ahpResult['ci'], 4) }}
            </div>
            <span class="text-xs text-slate-500 dark:text-slate-400 font-mono mt-1 block">(&lambda;max - n) / (n - 1)</span>
        </div>

        <div class="bg-white dark:bg-slate-900 p-5 border border-[#E2E8F0] dark:border-slate-800 rounded-xl shadow-2xs">
            <span class="text-xs font-mono font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 block">
                Indeks Acak (RI Saaty)
            </span>
            <div class="mt-1.5 text-2xl font-bold font-mono text-slate-900 dark:text-slate-100">
                {{ number_format($ahpResult['ri'], 2) }}
            </div>
            <span class="text-xs text-slate-500 dark:text-slate-400 font-mono mt-1 block">Matriks ordo n = {{ $ahpResult['n'] }}</span>
        </div>

        <div class="bg-white dark:bg-slate-900 p-5 border rounded-xl shadow-2xs {{ $ahpResult['is_consistent'] ? 'border-emerald-300 dark:border-emerald-800 bg-emerald-50/20 dark:bg-emerald-950/20' : 'border-amber-300 dark:border-amber-800 bg-amber-50/20 dark:bg-amber-950/20' }}">
            <span class="text-xs font-mono font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 block">
                Rasio Konsistensi (CR)
            </span>
            <div class="mt-1.5 text-2xl font-bold font-mono {{ $ahpResult['is_consistent'] ? 'text-emerald-700 dark:text-emerald-400' : 'text-amber-800 dark:text-amber-400' }}">
                {{ number_format($ahpResult['cr'], 4) }}
            </div>
            <span class="text-xs font-mono mt-1 block {{ $ahpResult['is_consistent'] ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-700 dark:text-amber-400' }}">
                Syarat Uji: CR &le; 0.10
            </span>
        </div>
    </div>

    {{-- ──────────────────────────────────────────────────────────────────
         WEIGHT VECTOR TABLE (w_j)
    ────────────────────────────────────────────────────────────────────── --}}
    <div class="bg-white border border-[#E2E8F0] rounded-lg overflow-hidden shadow-2xs">
        <div class="px-5 py-3.5 bg-[#F8FAFC] border-b border-[#E2E8F0] flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <h2 class="text-xs font-mono font-bold uppercase tracking-wider text-slate-900">
                Vektor Bobot Kriteria (w_j)
            </h2>
            <span class="text-[11px] font-mono text-[#6B7280]">Total Bobot: 1.0000 (100.00%)</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs divide-y divide-[#E2E8F0]">
                <thead class="bg-[#F8FAFC] text-[#6B7280] font-medium font-mono text-[11px]">
                    <tr>
                        <th class="py-3 px-5 w-14">Kode</th>
                        <th class="py-3 px-5">Nama Kriteria</th>
                        <th class="py-3 px-5">Sifat</th>
                        <th class="py-3 px-5">Satuan</th>
                        <th class="py-3 px-5 text-right">Bobot Desimal</th>
                        <th class="py-3 px-5 text-right">Persentase</th>
                        <th class="py-3 px-5 w-52">Distribusi Bobot</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8F0]">
                    @foreach ($ahpResult['criteria_keys'] as $code)
                        @php
                            $w = $ahpResult['weights'][$code] ?? 0;
                            $pct = $w * 100;
                            $critInfo = $criteria[$code] ?? null;
                            $isCost = ($critInfo['type'] ?? '') === 'cost';
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/60 transition">
                            <td class="py-3.5 px-5 font-mono font-bold text-slate-900">{{ $code }}</td>
                            <td class="py-3.5 px-5">
                                <span class="font-semibold text-slate-900">{{ $critInfo['name'] ?? $code }}</span>
                            </td>
                            <td class="py-3.5 px-5">
                                <span class="text-[10px] font-mono font-semibold px-2 py-0.5 rounded border {{ $isCost ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200' }}">
                                    {{ ucfirst($critInfo['type'] ?? '-') }}
                                </span>
                            </td>
                            <td class="py-3.5 px-5 font-mono text-[#6B7280]">{{ $critInfo['unit'] ?? '-' }}</td>
                            <td class="py-3.5 px-5 text-right font-mono font-bold text-slate-900">
                                {{ number_format($w, 4) }}
                            </td>
                            <td class="py-3.5 px-5 text-right font-mono font-bold text-[#2563EB]">
                                {{ number_format($pct, 2) }}%
                            </td>
                            <td class="py-3.5 px-5">
                                <div class="w-full bg-slate-100 border border-[#E2E8F0] rounded h-2 overflow-hidden">
                                    <div class="bg-[#2563EB] h-full transition-all duration-300" style="width: {{ $pct }}%"></div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ──────────────────────────────────────────────────────────────────
         MATHEMATICAL TRANSPARENCY ACCORDION
    ────────────────────────────────────────────────────────────────────── --}}
    <div x-data="{ openSteps: false }" class="bg-white border border-[#E2E8F0] rounded-lg overflow-hidden shadow-2xs">
        <button type="button"
                @click="openSteps = !openSteps"
                class="w-full px-5 py-4 text-left flex items-center justify-between text-xs font-semibold text-slate-900 dark:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60 transition cursor-pointer">
            <div>
                <span class="font-mono font-bold uppercase tracking-wider text-slate-900">
                    Transparansi Perhitungan AHP (Matriks Awal, Jumlah Kolom, dan Normalisasi)
                </span>
                <p class="text-[11px] text-[#6B7280] font-normal mt-0.5">
                    Buka untuk memverifikasi langkah matematis pembobotan dan eigen-vektor.
                </p>
            </div>
            <span class="font-mono text-xs font-bold text-[#2563EB]" x-text="openSteps ? '▲ Sembunyikan' : '▼ Tampilkan Detail'"></span>
        </button>

        <div x-show="openSteps" class="p-5 border-t border-[#E2E8F0] space-y-6" style="display: none;">
            {{-- 1. Matriks A --}}
            <div>
                <h3 class="text-xs font-mono font-bold uppercase tracking-wider text-slate-900 mb-2.5">
                    1. Matriks Perbandingan Berpasangan (A)
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border border-[#E2E8F0]">
                        <thead class="bg-[#F8FAFC] font-mono text-slate-900 text-[11px]">
                            <tr>
                                <th class="p-2.5 border border-[#E2E8F0]">Kriteria</th>
                                @foreach ($ahpResult['criteria_keys'] as $c)
                                    <th class="p-2.5 border border-[#E2E8F0] text-right">{{ $c }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="font-mono divide-y divide-[#E2E8F0]">
                            @foreach ($ahpResult['criteria_keys'] as $row)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-colors">
                                    <td class="p-2.5 border border-[#E2E8F0] font-bold text-slate-900">{{ $row }}</td>
                                    @foreach ($ahpResult['criteria_keys'] as $col)
                                        <td class="p-2.5 border border-[#E2E8F0] text-right text-slate-700">
                                            {{ number_format((float) $ahpResult['matrix'][$row][$col], 4) }}
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                            <tr class="bg-[#F1F5F9] font-bold">
                                <td class="p-2.5 border border-[#E2E8F0] text-slate-900">Jumlah Kolom (S_j)</td>
                                @foreach ($ahpResult['criteria_keys'] as $col)
                                    <td class="p-2.5 border border-[#E2E8F0] text-right text-[#2563EB]">
                                        {{ number_format($ahpResult['column_sums'][$col], 4) }}
                                    </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 2. Matriks Ternormalisasi --}}
            <div>
                <h3 class="text-xs font-mono font-bold uppercase tracking-wider text-slate-900 mb-2.5">
                    2. Matriks Ternormalisasi dan Rata-rata Baris (Bobot w_i)
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border border-[#E2E8F0]">
                        <thead class="bg-[#F8FAFC] font-mono text-slate-900 text-[11px]">
                            <tr>
                                <th class="p-2.5 border border-[#E2E8F0]">Kriteria</th>
                                @foreach ($ahpResult['criteria_keys'] as $c)
                                    <th class="p-2.5 border border-[#E2E8F0] text-right">{{ $c }}</th>
                                @endforeach
                                <th class="p-2.5 border border-[#E2E8F0] text-right bg-blue-50 text-[#2563EB] font-bold">Bobot (w_i)</th>
                            </tr>
                        </thead>
                        <tbody class="font-mono divide-y divide-[#E2E8F0]">
                            @foreach ($ahpResult['criteria_keys'] as $row)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-colors">
                                    <td class="p-2.5 border border-[#E2E8F0] font-bold text-slate-900">{{ $row }}</td>
                                    @foreach ($ahpResult['criteria_keys'] as $col)
                                        <td class="p-2.5 border border-[#E2E8F0] text-right text-[#6B7280]">
                                            {{ number_format($ahpResult['normalized_matrix'][$row][$col], 4) }}
                                        </td>
                                    @endforeach
                                    <td class="p-2.5 border border-[#E2E8F0] text-right bg-blue-50/60 font-bold text-[#2563EB]">
                                        {{ number_format($ahpResult['weights'][$row], 4) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ──────────────────────────────────────────────────────────────────
         WORKFLOW NAVIGATION ACTIONS
    ────────────────────────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-4 border-t border-[#E2E8F0]">
        <a href="{{ route('spk.index') }}"
           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-700 border border-[#E2E8F0] text-xs font-semibold rounded-md transition shadow-2xs">
            &larr; Sesuaikan Ulang Perbandingan (Langkah 1)
        </a>

        <form action="{{ route('spk.process_topsis') }}" method="POST">
            @csrf
            <button type="submit"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-[#2563EB] hover:bg-[#1D4ED8] text-white text-xs font-bold rounded-md transition shadow-2xs cursor-pointer">
                <span>Lanjut ke Perankingan TOPSIS (Langkah 3)</span>
                <span>&rarr;</span>
            </button>
        </form>
    </div>

</div>
@endsection