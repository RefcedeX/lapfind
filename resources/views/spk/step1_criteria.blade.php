@extends('layouts.public', ['title' => 'Pembobotan Kriteria AHP — Langkah 1'])

@section('content')
<div class="space-y-8 max-w-7xl mx-auto" x-data="{
    profile: '{{ $activeProfile }}',
    presets: {
        umum: {
            'C1-C2': { winner: 'C1', val: 3 },
            'C1-C3': { winner: 'C1', val: 3 },
            'C1-C4': { winner: 'C1', val: 4 },
            'C1-C5': { winner: 'C1', val: 2 },
            'C1-C6': { winner: 'C1', val: 2 },
            'C2-C3': { winner: 'C2', val: 1 },
            'C2-C4': { winner: 'C2', val: 2 },
            'C2-C5': { winner: 'C5', val: 2 },
            'C2-C6': { winner: 'C6', val: 3 },
            'C3-C4': { winner: 'C3', val: 2 },
            'C3-C5': { winner: 'C5', val: 2 },
            'C3-C6': { winner: 'C6', val: 2 },
            'C4-C5': { winner: 'C5', val: 2 },
            'C4-C6': { winner: 'C6', val: 3 },
            'C5-C6': { winner: 'C6', val: 2 }
        },
        coding: {
            'C1-C2': { winner: 'C2', val: 2 },
            'C1-C3': { winner: 'C3', val: 3 },
            'C1-C4': { winner: 'C1', val: 1 },
            'C1-C5': { winner: 'C1', val: 2 },
            'C1-C6': { winner: 'C1', val: 1 },
            'C2-C3': { winner: 'C3', val: 2 },
            'C2-C4': { winner: 'C2', val: 2 },
            'C2-C5': { winner: 'C2', val: 3 },
            'C2-C6': { winner: 'C2', val: 2 },
            'C3-C4': { winner: 'C3', val: 3 },
            'C3-C5': { winner: 'C3', val: 4 },
            'C3-C6': { winner: 'C3', val: 3 },
            'C4-C5': { winner: 'C4', val: 2 },
            'C4-C6': { winner: 'C4', val: 1 },
            'C5-C6': { winner: 'C6', val: 2 }
        },
        desain: {
            'C1-C2': { winner: 'C2', val: 3 },
            'C1-C3': { winner: 'C3', val: 2 },
            'C1-C4': { winner: 'C1', val: 1 },
            'C1-C5': { winner: 'C1', val: 2 },
            'C1-C6': { winner: 'C1', val: 1 },
            'C2-C3': { winner: 'C2', val: 2 },
            'C2-C4': { winner: 'C2', val: 3 },
            'C2-C5': { winner: 'C2', val: 4 },
            'C2-C6': { winner: 'C2', val: 3 },
            'C3-C4': { winner: 'C3', val: 2 },
            'C3-C5': { winner: 'C3', val: 3 },
            'C3-C6': { winner: 'C3', val: 2 },
            'C4-C5': { winner: 'C4', val: 2 },
            'C4-C6': { winner: 'C4', val: 1 },
            'C5-C6': { winner: 'C6', val: 2 }
        },
        gaming: {
            'C1-C2': { winner: 'C2', val: 3 },
            'C1-C3': { winner: 'C3', val: 2 },
            'C1-C4': { winner: 'C4', val: 2 },
            'C1-C5': { winner: 'C1', val: 2 },
            'C1-C6': { winner: 'C1', val: 2 },
            'C2-C3': { winner: 'C2', val: 2 },
            'C2-C4': { winner: 'C2', val: 3 },
            'C2-C5': { winner: 'C2', val: 5 },
            'C2-C6': { winner: 'C2', val: 5 },
            'C3-C4': { winner: 'C3', val: 2 },
            'C3-C5': { winner: 'C3', val: 4 },
            'C3-C6': { winner: 'C3', val: 4 },
            'C4-C5': { winner: 'C4', val: 3 },
            'C4-C6': { winner: 'C4', val: 3 },
            'C5-C6': { winner: 'C6', val: 1 }
        }
    },
    applyPreset(name) {
        this.profile = name;
        if (this.presets[name]) {
            const p = this.presets[name];
            for (const key in p) {
                const elWinner = document.querySelector(`select[name='winner_${key}']`);
                const elVal = document.querySelector(`select[name='value_${key}']`);
                const elHidden = document.getElementById(`hidden_comp_${key}`);
                if (elWinner && elVal) {
                    elWinner.value = p[key].winner;
                    elVal.value = p[key].val;
                    const parts = key.split('-');
                    const critA = parts[0];
                    if (elHidden) {
                        elHidden.value = (p[key].winner === critA) ? Number(p[key].val) : (1.0 / Number(p[key].val));
                    }
                }
            }
        }
    }
}">

    {{-- ──────────────────────────────────────────────────────────────────
         STEPPER & HEADER
    ────────────────────────────────────────────────────────────────────── --}}
    <div class="border-b border-slate-200/80 dark:border-slate-800 pb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 text-xs font-mono uppercase tracking-wider text-blue-600 dark:text-blue-400 font-bold mb-1.5">
                <span>Langkah 1 dari 3: Pembobotan Kriteria (AHP)</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                Tentukan Prioritas Kriteria Laptop
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1 max-w-2xl leading-relaxed">
                Pilih profil preferensi perkuliahan atau tentukan secara langsung tingkat kepentingan berpasangan berdasarkan Skala Saaty (1–9).
            </p>
        </div>

        {{-- Linear Step Indicator --}}
        <div class="flex items-center gap-2 font-mono text-xs shrink-0">
            <span class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-blue-600 text-white font-bold rounded-lg shadow-2xs">
                <span>01. AHP</span>
            </span>
            <span class="text-slate-300 dark:text-slate-700">/</span>
            <span class="inline-flex items-center px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 font-medium rounded-lg">
                <span>02. Konsistensi</span>
            </span>
            <span class="text-slate-300 dark:text-slate-700">/</span>
            <span class="inline-flex items-center px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 font-medium rounded-lg">
                <span>03. TOPSIS</span>
            </span>
        </div>
    </div>

    {{-- ──────────────────────────────────────────────────────────────────
         PRESET PROFILE SELECTORS
    ────────────────────────────────────────────────────────────────────── --}}
    <div class="space-y-3.5">
        <div class="flex items-center justify-between">
            <label class="text-xs font-mono font-bold uppercase tracking-wider text-slate-900 dark:text-white">
                Profil Preset Kebutuhan Mahasiswa:
            </label>
            <span class="text-xs text-slate-500 dark:text-slate-400">Pilih untuk mengisi 15 nilai perbandingan secara otomatis</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Preset 1: Umum --}}
            <button type="button"
                    @click="applyPreset('umum')"
                    :class="profile === 'umum'
                        ? 'border-blue-600 bg-blue-50/50 dark:bg-blue-950/40 ring-1 ring-blue-600 text-slate-900 dark:text-white'
                        : 'border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 hover:border-slate-300 dark:hover:border-slate-700'"
                    class="p-5 border rounded-2xl text-left transition flex flex-col justify-between shadow-2xs cursor-pointer group">
                <div class="space-y-2.5">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-mono font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                            Preset #01
                        </span>
                        <span class="w-2.5 h-2.5 rounded-full transition"
                              :class="profile === 'umum' ? 'bg-blue-600' : 'bg-slate-300 dark:bg-slate-700'"></span>
                    </div>
                    <div class="text-sm sm:text-base font-bold text-slate-900 dark:text-white">
                        1. Kuliah Umum & Riset
                    </div>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                        Fokus pada efisiensi anggaran kuliah, mobilitas ringan, serta daya tahan baterai seharian.
                    </p>
                    <div class="flex flex-wrap gap-1.5 pt-1">
                        <span class="px-2 py-0.5 rounded text-xs font-mono bg-slate-100 dark:bg-slate-800 border border-slate-200/60 dark:border-slate-700 text-slate-700 dark:text-slate-300">C1: Hemat</span>
                        <span class="px-2 py-0.5 rounded text-xs font-mono bg-slate-100 dark:bg-slate-800 border border-slate-200/60 dark:border-slate-700 text-slate-700 dark:text-slate-300">C5: Ringan</span>
                        <span class="px-2 py-0.5 rounded text-xs font-mono bg-slate-100 dark:bg-slate-800 border border-slate-200/60 dark:border-slate-700 text-slate-700 dark:text-slate-300">C6: Baterai</span>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs font-mono font-semibold">
                    <span :class="profile === 'umum' ? 'text-blue-600 dark:text-blue-400' : 'text-slate-400 dark:text-slate-500'">
                        <span x-text="profile === 'umum' ? '● Aktif Digunakan' : 'Gunakan Preset ini'"></span>
                    </span>
                    <span class="text-blue-600 dark:text-blue-400 group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                </div>
            </button>

            {{-- Preset 2: Coding --}}
            <button type="button"
                    @click="applyPreset('coding')"
                    :class="profile === 'coding'
                        ? 'border-indigo-600 bg-indigo-50/50 dark:bg-indigo-950/40 ring-1 ring-indigo-600 text-slate-900 dark:text-white'
                        : 'border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 hover:border-slate-300 dark:hover:border-slate-700'"
                    class="p-5 border rounded-2xl text-left transition flex flex-col justify-between shadow-2xs cursor-pointer group">
                <div class="space-y-2.5">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-mono font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                            Preset #02
                        </span>
                        <span class="w-2.5 h-2.5 rounded-full transition"
                              :class="profile === 'coding' ? 'bg-indigo-600' : 'bg-slate-300 dark:bg-slate-700'"></span>
                    </div>
                    <div class="text-sm sm:text-base font-bold text-slate-900 dark:text-white">
                        2. Coding & Informatika
                    </div>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                        Fokus pada kapasitas RAM luas untuk multitasking / Docker dan performa prosesor komputasi tinggi.
                    </p>
                    <div class="flex flex-wrap gap-1.5 pt-1">
                        <span class="px-2 py-0.5 rounded text-xs font-mono bg-slate-100 dark:bg-slate-800 border border-slate-200/60 dark:border-slate-700 text-slate-700 dark:text-slate-300">C3: RAM Maksimal</span>
                        <span class="px-2 py-0.5 rounded text-xs font-mono bg-slate-100 dark:bg-slate-800 border border-slate-200/60 dark:border-slate-700 text-slate-700 dark:text-slate-300">C2: CPU Tinggi</span>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs font-mono font-semibold">
                    <span :class="profile === 'coding' ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 dark:text-slate-500'">
                        <span x-text="profile === 'coding' ? '● Aktif Digunakan' : 'Gunakan Preset ini'"></span>
                    </span>
                    <span class="text-indigo-600 dark:text-indigo-400 group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                </div>
            </button>

            {{-- Preset 3: Desain --}}
            <button type="button"
                    @click="applyPreset('desain')"
                    :class="profile === 'desain'
                        ? 'border-cyan-600 bg-cyan-50/50 dark:bg-cyan-950/40 ring-1 ring-cyan-600 text-slate-900 dark:text-white'
                        : 'border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 hover:border-slate-300 dark:hover:border-slate-700'"
                    class="p-5 border rounded-2xl text-left transition flex flex-col justify-between shadow-2xs cursor-pointer group">
                <div class="space-y-2.5">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-mono font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                            Preset #03
                        </span>
                        <span class="w-2.5 h-2.5 rounded-full transition"
                              :class="profile === 'desain' ? 'bg-cyan-600' : 'bg-slate-300 dark:bg-slate-700'"></span>
                    </div>
                    <div class="text-sm sm:text-base font-bold text-slate-900 dark:text-white">
                        3. Desain & Multimedia
                    </div>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                        Fokus pada performa grafis CPU/GPU, kapasitas RAM render, serta kapasitas SSD untuk aset visual.
                    </p>
                    <div class="flex flex-wrap gap-1.5 pt-1">
                        <span class="px-2 py-0.5 rounded text-xs font-mono bg-slate-100 dark:bg-slate-800 border border-slate-200/60 dark:border-slate-700 text-slate-700 dark:text-slate-300">C2: CPU/GPU</span>
                        <span class="px-2 py-0.5 rounded text-xs font-mono bg-slate-100 dark:bg-slate-800 border border-slate-200/60 dark:border-slate-700 text-slate-700 dark:text-slate-300">C4: SSD Luas</span>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs font-mono font-semibold">
                    <span :class="profile === 'desain' ? 'text-cyan-600 dark:text-cyan-400' : 'text-slate-400 dark:text-slate-500'">
                        <span x-text="profile === 'desain' ? '● Aktif Digunakan' : 'Gunakan Preset ini'"></span>
                    </span>
                    <span class="text-cyan-600 dark:text-cyan-400 group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                </div>
            </button>

            {{-- Preset 4: Gaming --}}
            <button type="button"
                    @click="applyPreset('gaming')"
                    :class="profile === 'gaming'
                        ? 'border-purple-600 bg-purple-50/50 dark:bg-purple-950/40 ring-1 ring-purple-600 text-slate-900 dark:text-white'
                        : 'border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 hover:border-slate-300 dark:hover:border-slate-700'"
                    class="p-5 border rounded-2xl text-left transition flex flex-col justify-between shadow-2xs cursor-pointer group">
                <div class="space-y-2.5">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-mono font-bold uppercase tracking-wider text-purple-600 dark:text-purple-400">
                            Preset #04
                        </span>
                        <span class="w-2.5 h-2.5 rounded-full transition"
                              :class="profile === 'gaming' ? 'bg-purple-600' : 'bg-slate-300 dark:bg-slate-700'"></span>
                    </div>
                    <div class="text-sm sm:text-base font-bold text-slate-900 dark:text-white">
                        4. Gaming & Grafis Berat
                    </div>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                        Fokus pada performa mentah CPU/GPU monster dan RAM; toleransi tinggi pada bobot berat dan charger.
                    </p>
                    <div class="flex flex-wrap gap-1.5 pt-1">
                        <span class="px-2 py-0.5 rounded text-xs font-mono bg-purple-50 dark:bg-purple-950/40 border border-purple-200/60 dark:border-purple-800 text-purple-700 dark:text-purple-300">C2: Monster GPU</span>
                        <span class="px-2 py-0.5 rounded text-xs font-mono bg-slate-100 dark:bg-slate-800 border border-slate-200/60 dark:border-slate-700 text-slate-700 dark:text-slate-300">C3: RAM 16-32GB</span>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs font-mono font-semibold">
                    <span :class="profile === 'gaming' ? 'text-purple-600 dark:text-purple-400' : 'text-slate-400 dark:text-slate-500'">
                        <span x-text="profile === 'gaming' ? '● Aktif Digunakan' : 'Gunakan Preset ini'"></span>
                    </span>
                    <span class="text-purple-600 dark:text-purple-400 group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                </div>
            </button>
        </div>
    </div>

    {{-- ──────────────────────────────────────────────────────────────────
         PAIRWISE COMPARISON FORM (15 PAIRS) WITH INTEGRATED SAATY GUIDE
    ────────────────────────────────────────────────────────────────────── --}}
    <form action="{{ route('spk.process_ahp') }}" method="POST" class="space-y-6">
        @csrf
        <input type="hidden" name="profile" :value="profile">

        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl overflow-hidden shadow-2xs">

            {{-- 1. Integrated Saaty Scale Guide Strip (Header Top) --}}
            <div class="px-5 py-3.5 bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200/80 dark:border-slate-800">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-600 dark:bg-blue-400"></span>
                        <span class="font-mono font-bold text-slate-800 dark:text-slate-200 text-xs">
                            Panduan Skala Saaty (1–9):
                        </span>
                    </div>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1.5 text-xs text-slate-600 dark:text-slate-400 font-mono">
                        <div><strong class="text-slate-900 dark:text-white">1:</strong> Sama penting</div>
                        <div><strong class="text-slate-900 dark:text-white">3:</strong> Sedikit lebih penting</div>
                        <div><strong class="text-slate-900 dark:text-white">5:</strong> Lebih penting</div>
                        <div><strong class="text-slate-900 dark:text-white">7:</strong> Sangat penting</div>
                        <div><strong class="text-slate-900 dark:text-white">9:</strong> Mutlak lebih penting</div>
                    </div>
                </div>
            </div>

            {{-- 2. Section Title Bar (Header Sub) --}}
            <div class="px-5 py-3.5 bg-white dark:bg-slate-900 border-b border-slate-200/80 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                    <span class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white">
                        Matriks Perbandingan Berpasangan (15 Pasang Kriteria)
                    </span>
                    <span class="text-[11px] font-mono px-2 py-0.5 rounded bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300">
                        15 Pasang
                    </span>
                </div>
                <span class="text-xs font-mono text-slate-500 dark:text-slate-400">
                    Nilai Timbal Balik: a_ji = 1 / a_ij
                </span>
            </div>

            {{-- 3. 15 Comparison Rows --}}
            <div class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                @php
                    $count = 0;
                @endphp
                @for ($i = 0; $i < count($criteria); $i++)
                    @for ($j = $i + 1; $j < count($criteria); $j++)
                        @php
                            $critA = $criteria[$i];
                            $critB = $criteria[$j];
                            $key = "{$critA->code}-{$critB->code}";
                            $count++;

                            // Ambil nilai bawaan dari profil default
                            $rawVal = $defaultMatrix[$critA->code][$critB->code] ?? 1.0;
                            if ($rawVal >= 1.0) {
                                $defaultWinner = $critA->code;
                                $defaultScore = round($rawVal);
                            } else {
                                $defaultWinner = $critB->code;
                                $defaultScore = round(1.0 / $rawVal);
                            }
                        @endphp

                        <div class="p-4 sm:px-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 hover:bg-slate-50 dark:hover:bg-slate-800/60 transition">
                            {{-- Criterion Pair Labels --}}
                            <div class="flex items-center gap-3 sm:w-1/2">
                                <span class="font-mono text-xs text-slate-400 dark:text-slate-500 w-7 shrink-0">
                                    #{{ str_pad($count, 2, '0', STR_PAD_LEFT) }}
                                </span>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-mono font-bold text-slate-900 dark:text-white bg-slate-100 dark:bg-slate-800 border border-slate-200/60 dark:border-slate-700 px-2 py-0.5 rounded text-xs">
                                        {{ $critA->code }}
                                    </span>
                                    <span class="font-bold text-slate-800 dark:text-slate-200 text-sm">{{ $critA->name }}</span>
                                    <span class="text-xs text-slate-400 dark:text-slate-500 font-mono px-1 font-bold">VS</span>
                                    <span class="font-mono font-bold text-slate-900 dark:text-white bg-slate-100 dark:bg-slate-800 border border-slate-200/60 dark:border-slate-700 px-2 py-0.5 rounded text-xs">
                                        {{ $critB->code }}
                                    </span>
                                    <span class="font-bold text-slate-800 dark:text-slate-200 text-sm">{{ $critB->name }}</span>
                                </div>
                            </div>

                            {{-- Select Controls --}}
                            <div class="flex items-center gap-2.5 sm:w-1/2 sm:justify-end flex-wrap sm:flex-nowrap">
                                {{-- Winner Select --}}
                                <select name="winner_{{ $key }}"
                                        id="winner_{{ $key }}"
                                        aria-label="Kriteria prioritas {{ $critA->name }} vs {{ $critB->name }}"
                                        class="text-xs sm:text-sm font-medium text-slate-900 dark:text-slate-100 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 py-2 px-3 focus:border-blue-600 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 outline-none cursor-pointer"
                                        onchange="document.getElementById('hidden_comp_{{ $key }}').value = (this.value === '{{ $critA->code }}' ? document.getElementById('value_{{ $key }}').value : 1 / document.getElementById('value_{{ $key }}').value);">
                                    <option value="{{ $critA->code }}" {{ $defaultWinner === $critA->code ? 'selected' : '' }}>
                                        {{ $critA->name }} ({{ $critA->code }}) lebih penting
                                    </option>
                                    <option value="{{ $critB->code }}" {{ $defaultWinner === $critB->code ? 'selected' : '' }}>
                                        {{ $critB->name }} ({{ $critB->code }}) lebih penting
                                    </option>
                                </select>

                                {{-- Saaty Scale Value Select --}}
                                <select name="value_{{ $key }}"
                                        id="value_{{ $key }}"
                                        aria-label="Tingkat kepentingan Saaty {{ $critA->name }} vs {{ $critB->name }}"
                                        class="text-xs sm:text-sm font-mono font-bold text-slate-900 dark:text-slate-100 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 py-2 px-3 focus:border-blue-600 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 outline-none cursor-pointer"
                                        onchange="document.getElementById('hidden_comp_{{ $key }}').value = (document.getElementById('winner_{{ $key }}').value === '{{ $critA->code }}' ? this.value : 1 / this.value);">
                                    <option value="1" {{ $defaultScore == 1 ? 'selected' : '' }}>1 (Sama Penting)</option>
                                    <option value="2" {{ $defaultScore == 2 ? 'selected' : '' }}>2 (Mendekati Sedikit Lebih Penting)</option>
                                    <option value="3" {{ $defaultScore == 3 ? 'selected' : '' }}>3 (Sedikit Lebih Penting)</option>
                                    <option value="4" {{ $defaultScore == 4 ? 'selected' : '' }}>4 (Mendekati Lebih Penting)</option>
                                    <option value="5" {{ $defaultScore == 5 ? 'selected' : '' }}>5 (Lebih Penting)</option>
                                    <option value="6" {{ $defaultScore == 6 ? 'selected' : '' }}>6 (Mendekati Sangat Penting)</option>
                                    <option value="7" {{ $defaultScore == 7 ? 'selected' : '' }}>7 (Sangat Penting)</option>
                                    <option value="8" {{ $defaultScore == 8 ? 'selected' : '' }}>8 (Mendekati Mutlak)</option>
                                    <option value="9" {{ $defaultScore == 9 ? 'selected' : '' }}>9 (Mutlak Lebih Penting)</option>
                                </select>

                                {{-- Hidden calculation float --}}
                                <input type="hidden"
                                       name="comparisons[{{ $critA->code }}][{{ $critB->code }}]"
                                       id="hidden_comp_{{ $key }}"
                                       value="{{ $defaultWinner === $critA->code ? $defaultScore : (1.0 / $defaultScore) }}">
                            </div>
                        </div>
                    @endfor
                @endfor
            </div>

            {{-- 4. Footer Action --}}
            <div class="p-4 sm:p-5 bg-slate-50 dark:bg-slate-800/90 border-t border-slate-200/80 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <span class="text-xs text-slate-500 dark:text-slate-400">
                    Langkah berikutnya akan menghitung vektor bobot AHP dan menguji rasio konsistensi Saaty (CR).
                </span>
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-semibold text-xs sm:text-sm rounded-lg shadow-2xs transition cursor-pointer">
                    <span>Hitung Bobot AHP & Lanjut ke Langkah 2</span>
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="5" y1="12" x2="19" y2="12"/>
                        <polyline points="12 5 19 12 12 19"/>
                    </svg>
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
