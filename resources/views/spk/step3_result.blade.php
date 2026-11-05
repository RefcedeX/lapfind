@extends('layouts.public', ['title' => 'Hasil Evaluasi Rekomendasi TOPSIS — Langkah 3'])

@section('content')
<div class="space-y-8">

    {{-- ──────────────────────────────────────────────────────────────────
         STEPPER & HEADER
    ────────────────────────────────────────────────────────────────────── --}}
    <div class="border-b border-[#E2E8F0] pb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 text-xs font-mono uppercase tracking-wider text-[#2563EB] font-bold mb-1.5">
                <span>Langkah 3 dari 3: Hasil Akhir Evaluasi TOPSIS</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">
                {{ $title ?? 'Hasil Rekomendasi Laptop Mahasiswa' }}
            </h1>
            <p class="text-sm text-slate-600 mt-1.5 max-w-2xl leading-relaxed">
                Diurutkan berdasarkan kedekatan relatif preferensi (V_i) terhadap solusi ideal positif (A+) dan negatif (A-).
                @if (isset($createdAt))
                    Waktu komputasi: <span class="font-mono font-semibold text-slate-900">{{ \Carbon\Carbon::parse($createdAt)->format('d F Y, H:i') }} WIB</span>
                @endif
            </p>
        </div>

        {{-- Action Buttons & Step Indicator --}}
        <div class="flex flex-wrap items-center gap-2.5">
            @php
                $top3Ids = collect($rankings)->take(3)->pluck('alternative_id')->toArray();
                $compareQuery = http_build_query(['laptop_ids' => $top3Ids]);
            @endphp
            <a href="{{ route('catalog.compare') }}?{{ $compareQuery }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-[#E2E8F0] dark:border-slate-700 text-xs sm:text-sm font-semibold rounded-lg transition shadow-2xs">
                <span>Bandingkan 3 Terbaik</span>
            </a>
            <a href="{{ route('spk.export_pdf', $resultId ? ['spkResult' => $resultId] : []) }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-[#2563EB] dark:text-blue-400 border border-[#2563EB]/30 dark:border-blue-500/30 text-xs sm:text-sm font-bold rounded-lg transition shadow-2xs">
                <span>Unduh PDF</span>
            </a>
            <button type="button"
                    onclick="window.print()"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-[#E2E8F0] dark:border-slate-700 text-xs sm:text-sm font-semibold rounded-lg transition shadow-2xs cursor-pointer">
                <span>Cetak</span>
            </button>
            <a href="{{ route('spk.index') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#2563EB] hover:bg-[#1D4ED8] text-white text-xs sm:text-sm font-bold rounded-lg transition shadow-2xs">
                <span>Mulai Ulang &rarr;</span>
            </a>
        </div>
    </div>

    {{-- ──────────────────────────────────────────────────────────────────
         #1 RECOMMENDATION PODIUM CARD (Versus Style)
    ────────────────────────────────────────────────────────────────────── --}}
    @if (!empty($rankings))
        @php
            $bestAlt = $rankings[0];
            $bestLaptop = $laptops[$bestAlt['alternative_id']] ?? null;
        @endphp
        <div class="bg-white dark:bg-slate-900 border-2 border-[#2563EB] dark:border-blue-500 rounded-xl p-6 sm:p-8 flex flex-col md:flex-row items-center justify-between gap-8 shadow-sm">
            {{-- Studio Image Container --}}
            <div class="w-full md:w-60 aspect-[4/3] bg-[#F8FAFC] dark:bg-slate-800 border border-[#E2E8F0] dark:border-slate-700 rounded-lg flex items-center justify-center p-4 shrink-0 overflow-hidden relative">
                <img src="{{ $bestLaptop?->image_url }}"
                     alt="{{ $bestAlt['name'] }}"
                     class="max-h-full max-w-full object-contain">
                <span class="absolute top-2.5 left-2.5 px-2.5 py-0.5 bg-white dark:bg-slate-900 border border-[#E2E8F0] dark:border-slate-700 text-xs font-mono font-bold uppercase rounded shadow-2xs text-slate-900 dark:text-slate-100">
                    {{ $bestLaptop?->brand }}
                </span>
            </div>

            {{-- Metadata & Specs --}}
            <div class="space-y-2.5 flex-1 min-w-0">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-900 dark:bg-blue-600 text-white rounded-md font-mono text-xs font-bold uppercase tracking-wider">
                        Rekomendasi Terbaik #1
                    </span>
                    <span class="text-xs font-mono font-bold text-slate-500 dark:text-slate-400 uppercase">{{ $bestLaptop?->brand }}</span>
                </div>
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-slate-100 tracking-tight">
                    {{ $bestAlt['name'] }}
                </h2>
                <p class="text-sm text-slate-600 dark:text-slate-400 max-w-xl leading-relaxed">
                    {{ $bestLaptop?->description }}
                </p>
                <div class="pt-1.5 flex items-center gap-4 text-sm font-mono">
                    <span class="text-slate-500 dark:text-slate-400">Harga Resmi:</span>
                    <span class="text-lg font-bold text-slate-900 dark:text-slate-100">{{ $bestLaptop?->formatted_price }}</span>
                </div>
            </div>

            {{-- TOPSIS Preference Score Badge --}}
            <div class="bg-[#F8FAFC] dark:bg-slate-800/80 p-6 border border-[#E2E8F0] dark:border-slate-700 rounded-xl w-full md:w-64 shrink-0 text-right">
                <span class="text-xs font-mono font-bold text-slate-500 dark:text-slate-400 block uppercase tracking-wider">
                    Nilai Preferensi (V_1)
                </span>
                <div class="text-3xl sm:text-4xl font-extrabold font-mono text-[#2563EB] dark:text-blue-400 mt-1 tabular-nums">
                    {{ number_format($bestAlt['preference_score'], 4) }}
                </div>
                <div class="w-full bg-slate-200 dark:bg-slate-700 rounded h-2.5 mt-3 overflow-hidden">
                    <div class="bg-[#2563EB] dark:bg-blue-500 h-full" style="width: {{ $bestAlt['preference_score'] * 100 }}%"></div>
                </div>
                <div class="text-xs text-slate-500 dark:text-slate-400 font-mono mt-2.5">
                    D+ = {{ number_format($bestAlt['d_plus'], 4) }} &bull; D- = {{ number_format($bestAlt['d_minus'], 4) }}
                </div>
            </div>
        </div>
    @endif

    {{-- ──────────────────────────────────────────────────────────────────
         SIMULASI & ANALISIS SENSITIVITAS KESTABILAN BOBOT (SENSITIVITY ANALYSIS)
    ────────────────────────────────────────────────────────────────────── --}}
    @php
        $sensitivityPayload = [
            'weights' => $weights,
            'criteria' => $criteria->map(fn($c) => ['code' => $c->code, 'name' => $c->name, 'type' => $c->type])->values(),
            'normalized' => $steps['topsis']['normalized_matrix'] ?? [],
            'alternatives' => collect($rankings)->map(function ($r) use ($laptops) {
                return [
                    'id' => $r['alternative_id'],
                    'name' => $r['name'],
                    'brand' => $laptops[$r['alternative_id']]->brand ?? '',
                    'score' => (float)$r['preference_score'],
                ];
            })->values(),
        ];
    @endphp

    <div x-data="sensitivitySimulator({{ json_encode($sensitivityPayload) }})"
         class="bg-white dark:bg-slate-900 border border-[#E2E8F0] dark:border-slate-800 rounded-xl overflow-hidden shadow-2xs">
        
        <div class="px-5 py-4 border-b border-[#E2E8F0] dark:border-slate-800 bg-[#F8FAFC] dark:bg-slate-800/60 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <div class="flex items-center gap-2">
                    <span class="p-1 rounded bg-blue-500/10 text-blue-600 dark:text-blue-400">
                        <x-icon name="chart" class="w-3.5 h-3.5" />
                    </span>
                    <h2 class="text-xs font-mono font-bold uppercase tracking-wider text-slate-900 dark:text-slate-100">
                        Uji Analisis Sensitivitas & Kestabilan Keputusan
                    </h2>
                </div>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                    Simulasikan pergeseran bobot kriteria (&plusmn;30%) untuk menguji apakah rekomendasi laptop #1 tetap kokoh (Robust Decision Making).
                </p>
            </div>

            <div class="flex items-center gap-2">
                <span class="text-[10px] font-mono text-slate-500 uppercase">Status Kestabilan:</span>
                <span class="px-2 py-0.5 rounded text-xs font-mono font-bold border"
                      :class="status.includes('STABIL') ? 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-800' : 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-400 dark:border-amber-800'"
                      x-text="status"></span>
            </div>
        </div>

        <div class="p-5 space-y-6">
            {{-- Interactive Controls: Criterion Selector + Delta Slider --}}
            <div class="grid grid-cols-1 md:grid-cols-12 gap-5 items-center">
                <div class="md:col-span-5 space-y-1.5">
                    <label class="block text-xs font-medium text-slate-700 dark:text-slate-300">
                        Pilih Kriteria yang Akan Diuji Sensitivitasnya:
                    </label>
                    <select x-model="activeCrit"
                            @change="recalculate()"
                            class="w-full text-xs rounded-lg border-[#E2E8F0] dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:border-blue-500 focus:ring-blue-500 py-2">
                        <template x-for="c in criteria" :key="c.code">
                            <option :value="c.code" x-text="`${c.code} - ${c.name} (${c.type.toUpperCase()})`"></option>
                        </template>
                    </select>
                </div>

                <div class="md:col-span-7 space-y-2">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-medium text-slate-700 dark:text-slate-300">
                            Perubahan Bobot Kriteria:
                        </span>
                        <div class="font-mono text-xs font-bold text-blue-600 dark:text-blue-400 flex items-center gap-1">
                            <span x-text="(deltaPct > 0 ? '+' : '') + deltaPct + '%'"></span>
                            <span class="text-[10px] text-slate-400 font-normal" x-text="`(Bobot awal: ${((baseWeights[activeCrit] || 0) * 100).toFixed(1)}%)`"></span>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="text-[10px] font-mono text-slate-400">-30%</span>
                        <input type="range"
                               min="-30"
                               max="30"
                               step="5"
                               x-model="deltaPct"
                               @input="recalculate()"
                               class="w-full h-1.5 bg-slate-200 dark:bg-slate-700 rounded-lg appearance-none cursor-pointer accent-blue-600">
                        <span class="text-[10px] font-mono text-slate-400">+30%</span>
                        
                        <button type="button"
                                @click="deltaPct = 0; recalculate()"
                                class="px-2.5 py-1 text-[11px] font-mono text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white border border-[#E2E8F0] dark:border-slate-700 rounded hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                            Reset
                        </button>
                    </div>
                </div>
            </div>

            {{-- Comparison Podium: Top 3 Shift Visualization --}}
            <div class="pt-4 border-t border-[#E2E8F0] dark:border-slate-800">
                <span class="text-xs font-mono uppercase tracking-wider text-slate-500 dark:text-slate-400 block mb-3">
                    Proyeksi 3 Peringkat Tertinggi Pasca Perubahan Bobot:
                </span>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <template x-for="(item, idx) in results.slice(0, 3)" :key="item.id">
                        <div class="p-3.5 rounded-lg border transition"
                             :class="idx === 0 ? 'bg-blue-50/50 dark:bg-blue-950/30 border-blue-200 dark:border-blue-800' : 'bg-[#F8FAFC] dark:bg-slate-800/40 border-[#E2E8F0] dark:border-slate-800'">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-mono font-bold"
                                      :class="idx === 0 ? 'bg-blue-600 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300'"
                                      x-text="`Peringkat #${idx + 1}`"></span>
                                <span class="text-[10px] font-mono"
                                      :class="item.diff >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'"
                                      x-text="(item.diff >= 0 ? '+' : '') + item.diff.toFixed(4)"></span>
                            </div>
                            <div class="font-semibold text-xs text-slate-900 dark:text-slate-100 truncate" x-text="item.name"></div>
                            <div class="flex items-center justify-between mt-2 pt-2 border-t border-slate-200/60 dark:border-slate-700 text-[11px] font-mono">
                                <span class="text-slate-500">Skor Baru:</span>
                                <span class="font-bold text-slate-900 dark:text-slate-100" x-text="item.newScore.toFixed(4)"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    {{-- ──────────────────────────────────────────────────────────────────
         COMPLETE TOPSIS RANKING TABLE
    ────────────────────────────────────────────────────────────────────── --}}
    <div class="bg-white dark:bg-slate-900 border border-[#E2E8F0] dark:border-slate-800 rounded-lg overflow-hidden shadow-2xs">
        <div class="px-5 py-3.5 bg-[#F8FAFC] dark:bg-slate-800/60 border-b border-[#E2E8F0] dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <h2 class="text-xs font-mono font-bold uppercase tracking-wider text-slate-900 dark:text-slate-100">
                Daftar Lengkap Peringkat Alternatif Laptop ({{ count($rankings) }} Unit)
            </h2>
            <span class="text-[11px] font-mono text-[#6B7280] dark:text-slate-400">Algoritma TOPSIS Berbobot Eigen-Vektor AHP</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs divide-y divide-[#E2E8F0]">
                <thead class="bg-[#F8FAFC] text-[#6B7280] font-medium font-mono text-[11px]">
                    <tr>
                        <th class="py-3 px-4 w-14 text-center">Rank</th>
                        <th class="py-3 px-4 min-w-[240px]">Laptop & Vendor</th>
                        <th class="py-3 px-4 text-right">Harga Resmi</th>
                        <th class="py-3 px-4 text-right">Jarak D+</th>
                        <th class="py-3 px-4 text-right">Jarak D-</th>
                        <th class="py-3 px-4 text-right w-28">Skor V_i</th>
                        <th class="py-3 px-4 w-44">Visualisasi Preferensi</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8F0]">
                    @foreach ($rankings as $rank)
                        @php
                            $lap = $laptops[$rank['alternative_id']] ?? null;
                            $pct = $rank['preference_score'] * 100;
                            $isTop = $rank['rank'] === 1;
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/60 transition {{ $isTop ? 'bg-blue-50/20 dark:bg-blue-950/20' : '' }}">
                            <td class="py-3.5 px-4 text-center font-mono">
                                @if ($rank['rank'] === 1)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded bg-slate-900 text-white font-bold text-xs shadow-2xs">1</span>
                                @elseif ($rank['rank'] === 2)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded bg-slate-200 text-slate-800 font-bold text-xs">2</span>
                                @elseif ($rank['rank'] === 3)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded bg-amber-100 text-amber-900 border border-amber-300 font-bold text-xs">3</span>
                                @else
                                    <span class="text-[#6B7280] font-bold">{{ $rank['rank'] }}</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-10 bg-[#F8FAFC] border border-[#E2E8F0] rounded overflow-hidden flex items-center justify-center p-1 shrink-0">
                                        <img src="{{ $lap?->image_url }}" alt="{{ $rank['name'] }}" class="max-h-full max-w-full object-contain">
                                    </div>
                                    <div>
                                        <a href="{{ $lap ? route('catalog.show', $lap) : '#' }}" class="font-bold text-slate-900 hover:text-[#2563EB] transition">
                                            {{ $rank['name'] }}
                                        </a>
                                        <span class="text-[11px] text-[#6B7280] block">{{ $lap?->brand }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-900">
                                {{ $lap?->formatted_price ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono text-[#6B7280]">
                                {{ number_format($rank['d_plus'], 4) }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono text-[#6B7280]">
                                {{ number_format($rank['d_minus'], 4) }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold {{ $isTop ? 'text-[#2563EB]' : 'text-slate-900' }}">
                                {{ number_format($rank['preference_score'], 4) }}
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="w-full bg-slate-100 border border-[#E2E8F0] rounded h-2 overflow-hidden">
                                    <div class="bg-[#2563EB] h-full transition-all duration-300" style="width: {{ $pct }}%"></div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if ($lap)
                                    <a href="{{ route('catalog.show', $lap) }}" class="inline-flex items-center gap-1 text-[#2563EB] hover:text-[#1D4ED8] font-bold text-[11px]">
                                        <span>Spesifikasi &rarr;</span>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ──────────────────────────────────────────────────────────────────
         MATHEMATICAL TRANSPARENCY ACCORDION (TOPSIS)
    ────────────────────────────────────────────────────────────────────── --}}
    <div x-data="{ openSteps: false }" class="bg-white border border-[#E2E8F0] rounded-lg overflow-hidden shadow-2xs">
        <button type="button"
                @click="openSteps = !openSteps"
                class="w-full px-5 py-4 text-left flex items-center justify-between text-xs font-semibold text-slate-900 dark:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60 transition cursor-pointer">
            <div>
                <span class="font-mono font-bold uppercase tracking-wider text-slate-900">
                    Transparansi Laporan SPK (Langkah Antara Perhitungan AHP + TOPSIS)
                </span>
                <p class="text-[11px] text-[#6B7280] font-normal mt-0.5">
                    Menampilkan Matriks Keputusan (X), Normalisasi Vektor (R), Matriks Terbobot (Y), Solusi Ideal A+/A-, dan Jarak Euclidean D+/D-.
                </p>
            </div>
            <span class="font-mono text-xs font-bold text-[#2563EB]" x-text="openSteps ? '▲ Tutup Langkah' : '▼ Lihat Langkah Perhitungan'"></span>
        </button>

        <div x-show="openSteps" class="p-5 border-t border-[#E2E8F0] space-y-7" style="display: none;">
            @php
                $topsisSteps = $steps['topsis'] ?? [];
                $ahpSteps = $steps['ahp'] ?? [];
            @endphp

            {{-- 1. Bobot Kriteria AHP --}}
            <section class="space-y-2">
                <h3 class="text-xs font-mono font-bold uppercase tracking-wider text-slate-900">
                    Langkah 1: Bobot Kriteria dari AHP (w_j)
                </h3>
                <div class="grid grid-cols-2 sm:grid-cols-6 gap-2">
                    @foreach ($criteria as $crit)
                        <div class="p-2.5 bg-[#F8FAFC] border border-[#E2E8F0] rounded text-center">
                            <span class="font-mono text-xs font-bold text-slate-900 block">{{ $crit->code }}</span>
                            <span class="text-[10px] text-[#6B7280] block truncate">{{ $crit->name }}</span>
                            <span class="text-xs font-mono font-bold text-[#2563EB] mt-1 block">
                                {{ isset($weights[$crit->code]) ? number_format($weights[$crit->code], 4) : '-' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- 2. Pembagi Vektor Normalisasi --}}
            @if (!empty($topsisSteps['column_divisors']))
                <section class="space-y-2">
                    <h3 class="text-xs font-mono font-bold uppercase tracking-wider text-slate-900">
                        Langkah 2: Nilai Pembagi Vektor Kolom (&radic;&sum; x_ij&sup2;)
                    </h3>
                    <div class="grid grid-cols-2 sm:grid-cols-6 gap-2 font-mono text-xs">
                        @foreach ($criteria as $crit)
                            <div class="p-2.5 bg-[#F8FAFC] border border-[#E2E8F0] rounded text-center">
                                <span class="text-[11px] font-bold text-slate-900 block">{{ $crit->code }}</span>
                                <span class="text-xs font-mono text-[#6B7280] mt-1 block">
                                    {{ isset($topsisSteps['column_divisors'][$crit->code]) ? number_format($topsisSteps['column_divisors'][$crit->code], 2) : '-' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- 3. Matriks Ternormalisasi (R) --}}
            @if (!empty($topsisSteps['normalized_matrix']))
                <section class="space-y-2">
                    <h3 class="text-xs font-mono font-bold uppercase tracking-wider text-slate-900">
                        Langkah 3: Matriks Ternormalisasi (R = [r_ij])
                    </h3>
                    <div class="overflow-x-auto border border-[#E2E8F0] rounded">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-[#F8FAFC] font-mono text-slate-900 text-[11px]">
                                <tr>
                                    <th class="p-2 border border-[#E2E8F0]">Alternatif</th>
                                    @foreach ($criteria as $crit)
                                        <th class="p-2 border border-[#E2E8F0] text-right">{{ $crit->code }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="font-mono divide-y divide-[#E2E8F0] text-[11px]">
                                @foreach ($topsisSteps['normalized_matrix'] as $altId => $row)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-colors">
                                        <td class="p-2 border border-[#E2E8F0] font-bold text-slate-900 truncate max-w-[150px]">
                                            {{ $laptops[$altId]->name ?? "A#{$altId}" }}
                                        </td>
                                        @foreach ($criteria as $crit)
                                            <td class="p-2 border border-[#E2E8F0] text-right text-[#6B7280]">
                                                {{ isset($row[$crit->code]) ? number_format($row[$crit->code], 4) : '-' }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
            @endif

            {{-- 4. Matriks Ternormalisasi Terbobot (Y) --}}
            @if (!empty($topsisSteps['weighted_matrix']))
                <section class="space-y-2">
                    <h3 class="text-xs font-mono font-bold uppercase tracking-wider text-slate-900">
                        Langkah 4: Matriks Ternormalisasi Terbobot (Y = w_j &times; r_ij)
                    </h3>
                    <div class="overflow-x-auto border border-[#E2E8F0] rounded">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-[#F8FAFC] font-mono text-slate-900 text-[11px]">
                                <tr>
                                    <th class="p-2 border border-[#E2E8F0]">Alternatif</th>
                                    @foreach ($criteria as $crit)
                                        <th class="p-2 border border-[#E2E8F0] text-right">{{ $crit->code }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="font-mono divide-y divide-[#E2E8F0] text-[11px]">
                                @foreach ($topsisSteps['weighted_matrix'] as $altId => $row)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-colors">
                                        <td class="p-2 border border-[#E2E8F0] font-bold text-slate-900 truncate max-w-[150px]">
                                            {{ $laptops[$altId]->name ?? "A#{$altId}" }}
                                        </td>
                                        @foreach ($criteria as $crit)
                                            <td class="p-2 border border-[#E2E8F0] text-right text-[#6B7280]">
                                                {{ isset($row[$crit->code]) ? number_format($row[$crit->code], 4) : '-' }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
            @endif

            {{-- 5. Solusi Ideal Positif (A+) dan Negatif (A-) --}}
            @if (!empty($topsisSteps['ideal_positive']))
                <section class="space-y-2">
                    <h3 class="text-xs font-mono font-bold uppercase tracking-wider text-slate-900">
                        Langkah 5: Solusi Ideal Positif (A+) dan Solusi Ideal Negatif (A-)
                    </h3>
                    <div class="overflow-x-auto border border-[#E2E8F0] rounded">
                        <table class="w-full text-left text-xs font-mono">
                            <thead class="bg-[#F8FAFC] text-slate-900 text-[11px]">
                                <tr>
                                    <th class="p-2 border border-[#E2E8F0]">Solusi Ideal</th>
                                    @foreach ($criteria as $crit)
                                        <th class="p-2 border border-[#E2E8F0] text-right">
                                            {{ $crit->code }} ({{ $crit->type }})
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#E2E8F0] text-[11px]">
                                <tr class="bg-blue-50/50 font-bold text-blue-950">
                                    <td class="p-2 border border-[#E2E8F0]">Ideal Positif (A+)</td>
                                    @foreach ($criteria as $crit)
                                        <td class="p-2 border border-[#E2E8F0] text-right">
                                            {{ isset($topsisSteps['ideal_positive'][$crit->code]) ? number_format($topsisSteps['ideal_positive'][$crit->code], 4) : '-' }}
                                        </td>
                                    @endforeach
                                </tr>
                                <tr class="bg-amber-50/40 font-bold text-amber-950">
                                    <td class="p-2 border border-[#E2E8F0]">Ideal Negatif (A-)</td>
                                    @foreach ($criteria as $crit)
                                        <td class="p-2 border border-[#E2E8F0] text-right">
                                            {{ isset($topsisSteps['ideal_negative'][$crit->code]) ? number_format($topsisSteps['ideal_negative'][$crit->code], 4) : '-' }}
                                        </td>
                                    @endforeach
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>
            @endif
        </div>
    </div>
</div>

<script>
function sensitivitySimulator(data) {
    return {
        criteria: data.criteria || [],
        baseWeights: Object.assign({}, data.weights || {}),
        activeCrit: (data.criteria && data.criteria.length > 0) ? data.criteria[0].code : 'C1',
        deltaPct: 0,
        results: [],
        status: 'KONDISI BASIS (AHP Asli)',
        init() {
            this.recalculate();
        },
        recalculate() {
            const delta = parseFloat(this.deltaPct) / 100;
            const targetCrit = this.activeCrit;

            // 1. Raw weights calculation
            let rawWeights = {};
            let sum = 0;
            for (let i = 0; i < this.criteria.length; i++) {
                let code = this.criteria[i].code;
                let w = Number(this.baseWeights[code] || 0);
                if (code === targetCrit) {
                    w = Math.max(0.001, w * (1 + delta));
                }
                rawWeights[code] = w;
                sum += w;
            }

            // 2. Normalize weights (sum = 1)
            let normWeights = {};
            for (let i = 0; i < this.criteria.length; i++) {
                let code = this.criteria[i].code;
                normWeights[code] = sum > 0 ? (rawWeights[code] / sum) : 0;
            }

            // 3. Normalized Matrix & Ideal Solutions
            let normMatrix = data.normalized || {};
            let idealPos = {};
            let idealNeg = {};

            for (let i = 0; i < this.criteria.length; i++) {
                let c = this.criteria[i];
                let code = c.code;
                let type = c.type;
                let vals = [];
                for (let j = 0; j < data.alternatives.length; j++) {
                    let alt = data.alternatives[j];
                    let r = (normMatrix[alt.id] && normMatrix[alt.id][code] !== undefined) ? Number(normMatrix[alt.id][code]) : 0;
                    vals.push(r * normWeights[code]);
                }
                if (vals.length > 0) {
                    if (type === 'cost') {
                        idealPos[code] = Math.min(...vals);
                        idealNeg[code] = Math.max(...vals);
                    } else {
                        idealPos[code] = Math.max(...vals);
                        idealNeg[code] = Math.min(...vals);
                    }
                } else {
                    idealPos[code] = 0;
                    idealNeg[code] = 0;
                }
            }

            // 4. Distances & Relative Closeness V_i
            let newAlts = data.alternatives.map(function(alt) {
                let sumDPos = 0;
                let sumDNeg = 0;
                for (let i = 0; i < data.criteria.length; i++) {
                    let code = data.criteria[i].code;
                    let r = (normMatrix[alt.id] && normMatrix[alt.id][code] !== undefined) ? Number(normMatrix[alt.id][code]) : 0;
                    let y = r * normWeights[code];
                    sumDPos += Math.pow(y - idealPos[code], 2);
                    sumDNeg += Math.pow(y - idealNeg[code], 2);
                }
                let dPos = Math.sqrt(sumDPos);
                let dNeg = Math.sqrt(sumDNeg);
                let v = (dPos + dNeg) > 0 ? (dNeg / (dPos + dNeg)) : 0;
                return {
                    id: alt.id,
                    name: alt.name,
                    brand: alt.brand,
                    newScore: v,
                    baseScore: alt.score,
                    diff: v - alt.score
                };
            });

            // 5. Sort descending
            newAlts.sort(function(a, b) { return b.newScore - a.newScore; });
            this.results = newAlts;

            // 6. Stability status
            if (data.alternatives.length > 0 && newAlts.length > 0) {
                let baseTop = data.alternatives[0].id;
                let newTop = newAlts[0].id;
                if (baseTop === newTop) {
                    this.status = Math.abs(delta) < 0.001 ? 'KONDISI BASIS (AHP Asli)' : 'SANGAT STABIL (Juara #1 Bertahan)';
                } else {
                    this.status = 'BERGESER KE ' + (newAlts[0].name || '');
                }
            }
        }
    };
}
</script>
@endsection