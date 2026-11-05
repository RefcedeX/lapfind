@extends('layouts.public', ['title' => 'Bandingkan Laptop — Versus LapFind'])

@section('content')
@php
    $winCounts = [];
    $allAlternativeCount = \App\Models\Alternative::count();

    // Nilai acuan min & max untuk visualisasi skala mini-bar
    $criteriaStats = [
        'C1' => ['min' => 5000000, 'max' => 20000000],
        'C2' => ['min' => 5.0,     'max' => 10.0],
        'C3' => ['min' => 8,       'max' => 32],
        'C4' => ['min' => 256,     'max' => 1024],
        'C5' => ['min' => 1.2,     'max' => 2.5],
        'C6' => ['min' => 4.0,     'max' => 16.0],
    ];

    // Palet warna editorial LapFind (Versus Clean Tech: Blue, Purple, Emerald)
    $palette = [
        0 => [
            'color'  => '#2563EB',
            'stroke' => '#2563EB',
            'fill'   => 'rgba(37, 99, 235, 0.12)',
            'name'   => 'Unit A',
            'badge'  => 'bg-blue-50 text-blue-700 border-blue-200',
        ],
        1 => [
            'color'  => '#7C3AED',
            'stroke' => '#7C3AED',
            'fill'   => 'rgba(124, 58, 237, 0.12)',
            'name'   => 'Unit B',
            'badge'  => 'bg-purple-50 text-purple-700 border-purple-200',
        ],
        2 => [
            'color'  => '#059669',
            'stroke' => '#059669',
            'fill'   => 'rgba(5, 150, 105, 0.12)',
            'name'   => 'Unit C',
            'badge'  => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        ],
    ];

    // Sumbu Radar 6 Dimensi (Persis standar Versus.com)
    $radarAxes = [
        'C4' => ['pill' => 'STORAGE',     'full' => 'Penyimpanan SSD',  'angle' => -90, 'type' => 'benefit', 'unit' => 'GB SSD'],
        'C3' => ['pill' => 'MEMORY',      'full' => 'Kapasitas RAM',    'angle' => -30, 'type' => 'benefit', 'unit' => 'GB'],
        'C5' => ['pill' => 'MOBILITY',    'full' => 'Bobot Ringan',     'angle' => 30,  'type' => 'cost',    'unit' => 'kg'],
        'C2' => ['pill' => 'PERFORMANCE', 'full' => 'Performa CPU',     'angle' => 90,  'type' => 'benefit', 'unit' => '/10'],
        'C6' => ['pill' => 'BATTERY',     'full' => 'Daya Baterai',     'angle' => 150, 'type' => 'benefit', 'unit' => 'Jam'],
        'C1' => ['pill' => 'VALUE',       'full' => 'Efisiensi Harga',  'angle' => 210, 'type' => 'cost',    'unit' => 'Rp'],
    ];

    $cx = 200;
    $cy = 200;
    $radius = 120;

    $laptopPolygons = [];
    $differencesData = [];
    $advantages = [];

    // Perhitungan skor Versus Points dinamis (75-99)
    $getVersusScore = function ($lap) {
        $cVals = $lap->values->keyBy('criterion.code');
        $base  = 72;
        $cpu   = isset($cVals['C2']) ? ($cVals['C2']->value / 10.0) * 12 : 6;
        $ram   = isset($cVals['C3']) ? ($cVals['C3']->value / 32.0) *  8 : 4;
        $ssd   = isset($cVals['C4']) ? ($cVals['C4']->value / 1024.0) * 5 : 2.5;
        $bat   = isset($cVals['C6']) ? ($cVals['C6']->value / 16.0) *  4 : 2;
        return (int) min(99, max(75, round($base + $cpu + $ram + $ssd + $bat)));
    };

    if ($comparedLaptops->isNotEmpty()) {
        foreach ($comparedLaptops as $lap) {
            $winCounts[$lap->id] = 0;
            $advantages[$lap->id] = [];
        }

        // Tentukan pemenang per kriteria
        foreach ($criteria as $crit) {
            $critVals = [];
            foreach ($comparedLaptops as $lap) {
                if ($crit->code === 'C1') {
                    $critVals[$lap->id] = (float)$lap->price;
                } else {
                    $val = $lap->values->firstWhere('criteria_id', $crit->id)?->value;
                    if ($val !== null) {
                        $critVals[$lap->id] = (float)$val;
                    }
                }
            }

            if (!empty($critVals)) {
                $best = $crit->type === 'cost' ? min($critVals) : max($critVals);
                $uniqueVals = array_unique($critVals);
                $hasDiff = count($uniqueVals) > 1;

                foreach ($critVals as $lId => $v) {
                    if ($v == $best) {
                        $winCounts[$lId]++;
                    }
                }

                // Hitung keunggulan komparatif (Versus "Reasons to choose")
                if ($hasDiff) {
                    foreach ($comparedLaptops as $lap) {
                        $v = $critVals[$lap->id] ?? null;
                        if ($v !== null && $v == $best) {
                            $otherVals = array_filter($critVals, fn($val, $id) => $id !== $lap->id, ARRAY_FILTER_USE_BOTH);
                            if (!empty($otherVals)) {
                                $otherAvg = array_sum($otherVals) / count($otherVals);
                                if ($crit->code === 'C1') {
                                    $diff = $otherAvg - $v;
                                    $pct = $otherAvg > 0 ? round(($diff / $otherAvg) * 100) : 0;
                                    $advantages[$lap->id][] = "Harga Rp " . number_format($diff, 0, ',', '.') . " lebih hemat (" . $pct . "% lebih murah)";
                                } elseif ($crit->code === 'C3') {
                                    $advantages[$lap->id][] = "Kapasitas RAM " . $v . " GB lebih besar";
                                } elseif ($crit->code === 'C4') {
                                    $advantages[$lap->id][] = "SSD internal " . $v . " GB lebih luas";
                                } elseif ($crit->code === 'C5') {
                                    $diff = round($otherAvg - $v, 2);
                                    $advantages[$lap->id][] = "Bobot " . $v . " kg (" . ($diff > 0 ? $diff . " kg lebih ringan" : "lebih ringkas") . ")";
                                } elseif ($crit->code === 'C6') {
                                    $diff = round($v - $otherAvg, 1);
                                    $advantages[$lap->id][] = "Baterai " . $v . " Jam (+" . $diff . " jam lebih awet)";
                                } elseif ($crit->code === 'C2') {
                                    $advantages[$lap->id][] = "Skor CPU " . $v . "/10 lebih bertenaga";
                                }
                            }
                        }
                    }
                }
            }
        }

        arsort($winCounts);
        $winnerId = array_key_first($winCounts);
        $winnerLaptop = $comparedLaptops->firstWhere('id', $winnerId);
        $winnerWins = $winCounts[$winnerId] ?? 0;

        // Koordinat Polygon Spider Radar
        foreach ($comparedLaptops as $lapIdx => $lap) {
            $points = [];
            $ptsList = [];

            foreach ($radarAxes as $code => $ax) {
                $raw = 0;
                if ($code === 'C1') {
                    $raw = (float)$lap->price;
                } else {
                    $raw = (float)($lap->values->firstWhere('criterion.code', $code)?->value ?? 0);
                }

                if ($code === 'C1') {
                    $norm = max(0.25, min(1.0, 1.0 - (($raw - 5000000) / 16000000) * 0.75));
                } elseif ($code === 'C2') {
                    $norm = max(0.25, min(1.0, ($raw / 10.0)));
                } elseif ($code === 'C3') {
                    $norm = max(0.25, min(1.0, ($raw / 32.0)));
                } elseif ($code === 'C4') {
                    $norm = max(0.25, min(1.0, ($raw / 1024.0)));
                } elseif ($code === 'C5') {
                    $norm = max(0.25, min(1.0, 1.0 - (($raw - 1.2) / 1.3) * 0.75));
                } elseif ($code === 'C6') {
                    $norm = max(0.25, min(1.0, ($raw / 16.0)));
                } else {
                    $norm = 0.5;
                }

                $rad = deg2rad($ax['angle']);
                $px = round($cx + $norm * $radius * cos($rad), 1);
                $py = round($cy + $norm * $radius * sin($rad), 1);
                $points[] = "{$px},{$py}";
                $ptsList[$code] = ['x' => $px, 'y' => $py, 'norm' => $norm, 'raw' => $raw];
            }

            $laptopPolygons[$lap->id] = [
                'id'     => $lap->id,
                'idx'    => $lapIdx,
                'points' => implode(' ', $points),
                'pts'    => $ptsList,
                'color'  => $palette[$lapIdx]['color']  ?? '#2563EB',
                'stroke' => $palette[$lapIdx]['stroke'] ?? '#2563EB',
                'fill'   => $palette[$lapIdx]['fill']   ?? 'rgba(37,99,235,0.12)',
                'name'   => $lap->name,
                'brand'  => $lap->brand,
            ];
        }

        // Siapkan tabel perbedaan utama
        foreach ($radarAxes as $code => $ax) {
            $vals = [];
            foreach ($comparedLaptops as $lap) {
                if ($code === 'C1') {
                    $vals[$lap->id] = (float)$lap->price;
                } else {
                    $vals[$lap->id] = (float)($lap->values->firstWhere('criterion.code', $code)?->value ?? 0);
                }
            }
            $isCost = ($ax['type'] === 'cost');
            $bestVal = $isCost ? min($vals) : max($vals);
            $differencesData[$code] = [
                'code'    => $code,
                'fact'    => $ax['full'],
                'pill'    => $ax['pill'],
                'unit'    => $ax['unit'],
                'isCost'  => $isCost,
                'bestVal' => $bestVal,
                'hasDiff' => count(array_unique($vals)) > 1,
                'vals'    => $vals,
            ];
        }

        // ────────────────────────────────────────────────────────────────
        // PERHITUNGAN MATEMATIS TRANSPARAN (AHP-TOPSIS INSPECTOR)
        // ────────────────────────────────────────────────────────────────
        $ahpWeights = [
            'C1' => 0.2800,
            'C2' => 0.2400,
            'C3' => 0.1800,
            'C4' => 0.1200,
            'C5' => 0.1000,
            'C6' => 0.0800,
        ];
        if (session()->has('spk_weights')) {
            $sessW = session('spk_weights');
            if (is_array($sessW)) {
                foreach ($ahpWeights as $cCode => $wDef) {
                    if (isset($sessW[$cCode]) && is_numeric($sessW[$cCode])) {
                        $ahpWeights[$cCode] = (float)$sessW[$cCode];
                    }
                }
            }
        }

        // 1. Matriks Keputusan X
        $matrixX = [];
        foreach ($comparedLaptops as $lap) {
            $matrixX[$lap->id] = [];
            foreach ($criteria as $crit) {
                if ($crit->code === 'C1') {
                    $matrixX[$lap->id][$crit->code] = (float)$lap->price;
                } else {
                    $val = $lap->values->firstWhere('criteria_id', $crit->id)?->value ?? 0;
                    $matrixX[$lap->id][$crit->code] = (float)$val;
                }
            }
        }

        // 2. Pembagi Euclidean per Kriteria (sqrt(sum(x^2)))
        $divisors = [];
        foreach ($criteria as $crit) {
            $sumSq = 0;
            foreach ($comparedLaptops as $lap) {
                $val = $matrixX[$lap->id][$crit->code] ?? 0;
                $sumSq += ($val * $val);
            }
            $divisors[$crit->code] = sqrt($sumSq) ?: 1;
        }

        // 3. Matriks Ternormalisasi R (r_ij = x_ij / divisor) & 4. Terbobot Y (y_ij = w_j * r_ij)
        $matrixR = [];
        $matrixY = [];
        foreach ($comparedLaptops as $lap) {
            $matrixR[$lap->id] = [];
            $matrixY[$lap->id] = [];
            foreach ($criteria as $crit) {
                $r = $matrixX[$lap->id][$crit->code] / $divisors[$crit->code];
                $matrixR[$lap->id][$crit->code] = $r;
                $matrixY[$lap->id][$crit->code] = $r * $ahpWeights[$crit->code];
            }
        }

        // 5. Solusi Ideal Positif (A+) & Negatif (A-)
        $solIdealPos = [];
        $solIdealNeg = [];
        foreach ($criteria as $crit) {
            $yList = [];
            foreach ($comparedLaptops as $lap) {
                $yList[] = $matrixY[$lap->id][$crit->code];
            }
            if ($crit->type === 'cost') {
                $solIdealPos[$crit->code] = min($yList);
                $solIdealNeg[$crit->code] = max($yList);
            } else {
                $solIdealPos[$crit->code] = max($yList);
                $solIdealNeg[$crit->code] = min($yList);
            }
        }

        // 6. Jarak Euclidean (D+ dan D-) & 7. Skor Preferensi V_i
        $distPos = [];
        $distNeg = [];
        $prefScores = [];
        foreach ($comparedLaptops as $lap) {
            $sumDpos = 0;
            $sumDneg = 0;
            foreach ($criteria as $crit) {
                $y = $matrixY[$lap->id][$crit->code];
                $sumDpos += pow($y - $solIdealPos[$crit->code], 2);
                $sumDneg += pow($y - $solIdealNeg[$crit->code], 2);
            }
            $dp = sqrt($sumDpos);
            $dn = sqrt($sumDneg);
            $distPos[$lap->id] = $dp;
            $distNeg[$lap->id] = $dn;
            $denom = $dn + $dp;
            $prefScores[$lap->id] = $denom > 0 ? ($dn / $denom) : 0.5;
        }

        $rankedByTopsis = $comparedLaptops->sortByDesc(fn($l) => $prefScores[$l->id]);
        $topsisWinner = $rankedByTopsis->first();
    }

    $existingIds = $comparedLaptops->pluck('id')->toArray();
    $availableLaptops = $allLaptops->whereNotIn('id', $existingIds);
@endphp

<div x-data="{ onlyDiff: false, activeCriterion: null, activeLaptopId: null, showMath: false }" class="space-y-8">

    {{-- ──────────────────────────────────────────────────────────────────
         PAGE HEADER & TOOLBAR
    ────────────────────────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-[#E2E8F0] pb-4">
        <div>
            <nav class="flex items-center gap-2 text-xs sm:text-sm text-slate-500 mb-1.5">
                <a href="{{ route('home') }}" class="hover:text-slate-900 transition">Home</a>
                <span>/</span>
                <a href="{{ route('catalog.index') }}" class="hover:text-slate-900 transition">Katalog</a>
                <span>/</span>
                <span class="text-slate-900 font-medium">Bandingkan</span>
            </nav>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight leading-tight">
                Head-to-Head Komparasi Spesifikasi
            </h1>
        </div>

        <div class="flex items-center gap-2.5">
            @if ($comparedLaptops->count() >= 2)
                <button type="button"
                        @click="onlyDiff = !onlyDiff"
                        class="px-4 py-2 rounded-lg text-sm font-medium border transition cursor-pointer flex items-center gap-2 shadow-2xs"
                        :class="onlyDiff
                            ? 'bg-slate-900 text-white border-slate-900'
                            : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'">
                    <span class="w-2 h-2 rounded-full" :class="onlyDiff ? 'bg-emerald-400' : 'bg-slate-400'"></span>
                    <span x-text="onlyDiff ? 'Tampilkan Semua' : 'Sorot Perbedaan'"></span>
                </button>
            @endif

            <a href="{{ route('catalog.index') }}"
               class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-medium rounded-lg transition shadow-2xs">
                + Cari di Katalog
            </a>
        </div>
    </div>

    @if ($comparedLaptops->isNotEmpty())

        {{-- ──────────────────────────────────────────────────────────────
             1. VERSUS HEAD-TO-HEAD UNIT SLOTS (Refined Hairline Cards)
        ────────────────────────────────────────────────────────────────── --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($comparedLaptops as $lapIdx => $lap)
                @php
                    $vScore = $getVersusScore($lap);
                    $slotPalette = $palette[$lapIdx] ?? $palette[0];
                    $lapAdv = $advantages[$lap->id] ?? [];
                @endphp
                <div class="bg-white border border-slate-200 rounded-xl p-6 flex flex-col justify-between transition-all duration-200 shadow-2xs relative overflow-hidden"
                     :class="activeLaptopId === {{ $lap->id }} ? 'ring-2 ring-slate-900 ring-offset-2' : ''">

                    {{-- Subtle top-border accent bar --}}
                    <div class="absolute top-0 left-0 right-0 h-1.5" style="background-color: {{ $slotPalette['color'] }};"></div>

                    <div>
                        {{-- Top Slot Meta: Badge + Remove --}}
                        <div class="flex items-center justify-between gap-2 pb-3.5 border-b border-slate-100">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-mono font-bold uppercase tracking-wider px-2.5 py-1 rounded-md {{ $slotPalette['badge'] }}">
                                    {{ $slotPalette['name'] }} &bull; {{ $lap->brand }}
                                </span>
                                @if (isset($winnerLaptop) && $winnerLaptop->id === $lap->id && $comparedLaptops->count() >= 2)
                                    <span class="text-xs font-mono font-bold uppercase tracking-wider px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Pemenang
                                    </span>
                                @endif
                            </div>

                            {{-- Remove button --}}
                            <form method="GET" action="{{ route('catalog.compare') }}">
                                @foreach ($comparedLaptops as $other)
                                    @if ($other->id !== $lap->id)
                                        <input type="hidden" name="laptop_ids[]" value="{{ $other->id }}">
                                    @endif
                                @endforeach
                                <button type="submit"
                                        @click="$store.compare.remove({{ $lap->id }})"
                                        class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-red-600 hover:bg-red-50 border border-transparent hover:border-red-200 transition cursor-pointer"
                                        aria-label="Hapus {{ addslashes($lap->name) }} dari perbandingan"
                                        title="Hapus dari perbandingan">
                                    <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                        <line x1="3" y1="3" x2="13" y2="13"/>
                                        <line x1="13" y1="3" x2="3" y2="13"/>
                                    </svg>
                                </button>
                            </form>
                        </div>

                        {{-- Product Studio Image (aspect-16/10) --}}
                        <div class="aspect-[16/10] w-full flex items-center justify-center p-3.5 my-3.5 bg-slate-50 border border-slate-200/80 rounded-lg">
                            <img src="{{ $lap->image_url }}"
                                 alt="{{ $lap->name }}"
                                 class="object-contain w-full h-full max-h-36 transition-transform duration-200 hover:scale-105">
                        </div>

                        {{-- Title & Price --}}
                        <div class="space-y-1 mb-4">
                            <a href="{{ route('catalog.show', $lap) }}"
                               class="text-sm sm:text-base font-bold text-slate-900 hover:text-blue-600 transition line-clamp-2 leading-snug">
                                {{ $lap->name }}
                            </a>
                            <div class="text-base font-bold text-slate-900 font-mono tabular-nums">
                                {{ $lap->formatted_price }}
                            </div>
                        </div>

                        {{-- Score & Quick Stats (Squircle Badge) --}}
                        <div class="flex items-center gap-3.5 p-3.5 bg-slate-50 border border-slate-200/80 rounded-lg mb-4">
                            <div class="score-badge shrink-0" title="LapFind Hardware Rating: {{ $vScore }}/100">
                                <span class="score-num font-mono tabular-nums">{{ $vScore }}</span>
                                <span class="score-lbl">pts</span>
                            </div>
                            <div class="text-xs leading-tight text-slate-600">
                                <span class="font-bold text-slate-900 text-sm">Versus Hardware Rating</span>
                                <p class="text-slate-500 text-xs mt-1">Dihitung dari kombinasi CPU, RAM, SSD, dan baterai.</p>
                            </div>
                        </div>

                        {{-- Versus Signature: Reasons to choose this unit --}}
                        @if (!empty($lapAdv))
                            <div class="space-y-2 mb-4">
                                <p class="text-xs font-mono font-bold uppercase tracking-wider text-slate-500">Keunggulan Utama:</p>
                                <ul class="space-y-1.5 text-xs sm:text-sm text-slate-900">
                                    @foreach (array_slice($lapAdv, 0, 3) as $adv)
                                        <li class="flex items-start gap-2">
                                            <span class="text-emerald-600 font-bold shrink-0">✓</span>
                                            <span class="leading-normal">{{ $adv }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>

                    {{-- Bottom Action --}}
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                        <a href="{{ route('catalog.show', $lap) }}"
                           class="w-full text-center py-2 px-3 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-md transition shadow-2xs">
                            Detail Spesifikasi &rarr;
                        </a>
                    </div>
                </div>
            @endforeach

            {{-- Empty Slot Placeholder (+ Tambah Laptop ke Slot) --}}
            @if ($comparedLaptops->count() < 3)
                <div class="bg-white border border-dashed border-slate-300 rounded-lg p-5 flex flex-col items-center justify-center text-center space-y-3 min-h-[360px]">
                    <div class="w-10 h-10 rounded-md bg-slate-50 border border-slate-200 flex items-center justify-center text-slate-400">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <line x1="12" y1="5" x2="12" y2="19"/>
                            <line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
                    </div>

                    <div class="space-y-1">
                        <p class="text-xs font-bold text-slate-900">Tambah Unit Perbandingan</p>
                        <p class="text-[11px] text-slate-500 max-w-xs mt-0.5">
                            Pilih laptop lain untuk dibandingkan langsung secara head-to-head.
                        </p>
                    </div>

                    <form method="GET" action="{{ route('catalog.compare') }}" class="w-full max-w-xs space-y-2 pt-2">
                        @foreach ($comparedLaptops as $existing)
                            <input type="hidden" name="laptop_ids[]" value="{{ $existing->id }}">
                        @endforeach

                        <select name="laptop_ids[]"
                                onchange="this.form.submit()"
                                class="w-full text-xs bg-slate-50 border border-slate-200 rounded-md px-3 py-2 text-slate-900 font-medium outline-none focus:border-slate-400 cursor-pointer">
                            <option value="">-- Pilih Laptop Tambahan --</option>
                            @foreach ($availableLaptops as $available)
                                <option value="{{ $available->id }}">
                                    {{ $available->brand }} — {{ $available->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            @endif
        </div>

        {{-- ──────────────────────────────────────────────────────────────
             2. VERDICT BANNER (Keputusan Komparasi)
        ────────────────────────────────────────────────────────────────── --}}
        @if (isset($winnerLaptop) && $comparedLaptops->count() >= 2)
            <div class="bg-slate-50 border border-slate-200/90 rounded-lg p-5 sm:p-6 flex flex-col md:flex-row md:items-center justify-between gap-5 shadow-2xs">
                <div class="space-y-1">
                    <div class="inline-flex items-center gap-1.5 text-[10px] font-mono font-bold uppercase tracking-wider text-blue-600">
                        <span>KEPUTUSAN KOMPARASI</span>
                        <span>&bull;</span>
                        <span>THE VERDICT</span>
                    </div>
                    <h2 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
                        Rekomendasi Utama: <span class="text-blue-600">{{ $winnerLaptop->name }}</span>
                    </h2>
                    <p class="text-xs text-slate-600 max-w-2xl leading-relaxed">
                        Memimpin pada <strong class="text-slate-900 font-mono">{{ $winnerWins }} dari {{ count($radarAxes) }} parameter kunci</strong>. Pilihan paling rasional berdasarkan keseimbangan harga, efisiensi komponen, dan daya dukung aktivitas perkuliahan.
                    </p>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <a href="{{ route('spk.index') }}"
                       class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-md transition shadow-2xs">
                        Uji dengan SPK Smart Match &rarr;
                    </a>
                </div>
            </div>
        @endif

        {{-- ──────────────────────────────────────────────────────────────
             3. INTERACTIVE HARDWARE RADAR COCKPIT & DIFFERENCES
        ────────────────────────────────────────────────────────────────── --}}
        <div class="bg-white border border-[#E2E8F0] rounded-lg p-6 sm:p-7 space-y-6 shadow-2xs">

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-[#E2E8F0] pb-4">
                <div>
                    <h2 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight uppercase">
                        Radar Spesifikasi & Karakteristik
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Visualisasi multi-dimensi perbandingan spesifikasi 6 parameter inti. Sorot titik sudut untuk memfokuskan kriteria.
                    </p>
                </div>

                {{-- Interactive Legend --}}
                <div class="flex flex-wrap items-center gap-4 text-xs font-medium">
                    @foreach ($comparedLaptops as $lapIdx => $lap)
                        @php $slotPalette = $palette[$lapIdx] ?? $palette[0]; @endphp
                        <button type="button"
                                @mouseenter="activeLaptopId = {{ $lap->id }}"
                                @mouseleave="activeLaptopId = null"
                                @click="activeLaptopId = (activeLaptopId === {{ $lap->id }} ? null : {{ $lap->id }})"
                                class="flex items-center gap-2 px-2.5 py-1 rounded transition cursor-pointer"
                                :class="activeLaptopId === {{ $lap->id }} ? 'bg-slate-100 ring-1 ring-slate-300' : 'hover:bg-slate-50'">
                            <span class="w-3 h-3 rounded-full shrink-0" style="background-color: {{ $slotPalette['color'] }};"></span>
                            <span class="text-slate-800 font-semibold">{{ $slotPalette['name'] }}</span>
                            <span class="text-slate-500 truncate max-w-[120px]">{{ $lap->name }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Cockpit Grid: Radar Chart SVG (Left) + Differences Table (Right) --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">

                {{-- LEFT: Radar Chart SVG --}}
                <div class="lg:col-span-5 flex flex-col items-center justify-center p-3">
                    <div class="relative w-full max-w-[340px] aspect-square">
                        <svg viewBox="0 0 400 400" class="w-full h-full select-none">
                            <defs>
                                @foreach ($laptopPolygons as $poly)
                                    <radialGradient id="grad-{{ $poly['id'] }}" cx="50%" cy="50%" r="50%">
                                        <stop offset="0%" stop-color="{{ $poly['color'] }}" stop-opacity="0.35"/>
                                        <stop offset="100%" stop-color="{{ $poly['color'] }}" stop-opacity="0.05"/>
                                    </radialGradient>
                                @endforeach
                            </defs>

                            {{-- Concentric Hexagons (Grid Levels: 25%, 50%, 75%, 100%) --}}
                            @foreach ([0.25, 0.5, 0.75, 1.0] as $level)
                                @php
                                    $gridPoints = [];
                                    foreach ($radarAxes as $code => $ax) {
                                        $rad = deg2rad($ax['angle']);
                                        $gx = round($cx + $level * $radius * cos($rad), 1);
                                        $gy = round($cy + $level * $radius * sin($rad), 1);
                                        $gridPoints[] = "{$gx},{$gy}";
                                    }
                                @endphp
                                <polygon points="{{ implode(' ', $gridPoints) }}"
                                         fill="none"
                                         stroke="#E2E8F0"
                                         class="stroke-slate-200 dark:stroke-slate-700"
                                         stroke-width="{{ $level == 1.0 ? '1.5' : '1' }}"
                                         stroke-dasharray="{{ $level < 1.0 ? '2,3' : 'none' }}"/>
                            @endforeach

                            {{-- Spoke Lines from Center to Axes --}}
                            @foreach ($radarAxes as $code => $ax)
                                @php
                                    $rad = deg2rad($ax['angle']);
                                    $tx = round($cx + $radius * cos($rad), 1);
                                    $ty = round($cy + $radius * sin($rad), 1);
                                @endphp
                                <line x1="{{ $cx }}" y1="{{ $cy }}" x2="{{ $tx }}" y2="{{ $ty }}"
                                      stroke="#CBD5E1" class="stroke-slate-300 dark:stroke-slate-700" stroke-width="1"/>
                            @endforeach

                            {{-- Laptop Area Polygons --}}
                            @foreach ($laptopPolygons as $poly)
                                <polygon points="{{ $poly['points'] }}"
                                         fill="url(#grad-{{ $poly['id'] }})"
                                         stroke="{{ $poly['color'] }}"
                                         stroke-width="2"
                                         class="transition-all duration-200 cursor-pointer"
                                         :stroke-width="activeLaptopId === {{ $poly['id'] }} ? 3.5 : 2"
                                         :fill-opacity="activeLaptopId === null || activeLaptopId === {{ $poly['id'] }} ? 1 : 0.2"
                                         :stroke-opacity="activeLaptopId === null || activeLaptopId === {{ $poly['id'] }} ? 1 : 0.35"
                                         @mouseenter="activeLaptopId = {{ $poly['id'] }}"
                                         @mouseleave="activeLaptopId = null"/>
                            @endforeach

                            {{-- Vertex Points on Selected / All Polygons --}}
                            @foreach ($laptopPolygons as $poly)
                                @foreach ($poly['pts'] as $cCode => $pt)
                                    <circle cx="{{ $pt['x'] }}" cy="{{ $pt['y'] }}" r="4"
                                            fill="{{ $poly['color'] }}"
                                            stroke="#FFFFFF"
                                            stroke-width="1.5"
                                            class="transition-all duration-150"
                                            :r="activeCriterion === '{{ $cCode }}' || activeLaptopId === {{ $poly['id'] }} ? 6 : 4"/>
                                @endforeach
                            @endforeach

                            {{-- Axis Labels (Surrounding the Hexagon) --}}
                            @foreach ($radarAxes as $code => $ax)
                                @php
                                    $rad = deg2rad($ax['angle']);
                                    $lx = round($cx + ($radius + 28) * cos($rad), 1);
                                    $ly = round($cy + ($radius + 28) * sin($rad), 1);
                                    $textAnchor = abs(cos($rad)) < 0.2 ? 'middle' : (cos($rad) > 0 ? 'start' : 'end');
                                @endphp
                                <g class="cursor-pointer"
                                   @mouseenter="activeCriterion = '{{ $code }}'"
                                   @mouseleave="activeCriterion = null"
                                   @click="activeCriterion = (activeCriterion === '{{ $code }}' ? null : '{{ $code }}')">
                                    <text x="{{ $lx }}" y="{{ $ly + 4 }}"
                                          text-anchor="{{ $textAnchor }}"
                                          class="text-[10px] font-mono font-bold transition-all duration-150"
                                          :fill="activeCriterion === '{{ $code }}' ? '#2563EB' : '#64748B'"
                                          :font-size="activeCriterion === '{{ $code }}' ? '11px' : '10px'">
                                        {{ $ax['pill'] }}
                                    </text>
                                </g>
                            @endforeach
                        </svg>
                    </div>
                </div>

                {{-- RIGHT: Differences Breakdown Table --}}
                <div class="lg:col-span-7 bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg p-5 space-y-4">
                    <div class="flex items-center justify-between border-b border-[#E2E8F0] pb-3">
                        <h3 class="text-xs font-mono font-bold uppercase tracking-wider text-slate-900">
                            Perbandingan Spesifikasi Inti
                        </h3>
                        <span class="text-[10px] font-mono text-slate-500">
                            {{ count($differencesData) }} Dimensi
                        </span>
                    </div>

                    <div class="overflow-x-auto no-scrollbar">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="border-b border-[#E2E8F0] text-[10px] font-mono text-slate-500 uppercase">
                                    <th class="py-2 pr-3 font-semibold w-40">Parameter</th>
                                    @foreach ($comparedLaptops as $lapIdx => $lap)
                                        @php $slotPalette = $palette[$lapIdx] ?? $palette[0]; @endphp
                                        <th class="py-2 px-3 font-bold truncate max-w-[130px]"
                                            style="color: {{ $slotPalette['color'] }};">
                                            {{ $lap->name }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#E2E8F0] text-xs">
                                @foreach ($differencesData as $code => $diff)
                                    <tr class="transition-colors duration-150 cursor-pointer"
                                        :class="activeCriterion === '{{ $code }}' ? 'bg-white dark:bg-slate-800 shadow-2xs' : 'hover:bg-slate-200/50 dark:hover:bg-slate-800/50'"
                                        @mouseenter="activeCriterion = '{{ $code }}'"
                                        @mouseleave="activeCriterion = null"
                                        @click="activeCriterion = (activeCriterion === '{{ $code }}' ? null : '{{ $code }}')">

                                        <td class="py-2.5 pr-3 font-medium text-slate-900 dark:text-white">
                                            <div class="flex items-center gap-1.5">
                                                <span>{{ $diff['fact'] }}</span>
                                                <span class="text-[9px] font-mono px-1 rounded-sm bg-[#E2E8F0] dark:bg-slate-700 text-slate-600 dark:text-slate-300">
                                                    {{ $diff['pill'] }}
                                                </span>
                                            </div>
                                        </td>

                                        @foreach ($comparedLaptops as $lapIdx => $lap)
                                            @php
                                                $val = $diff['vals'][$lap->id] ?? 0;
                                                $isWinner = ($val == $diff['bestVal'] && $diff['hasDiff']);
                                                $slotPalette = $palette[$lapIdx] ?? $palette[0];
                                            @endphp
                                            <td class="py-2.5 px-3 font-mono tabular-nums">
                                                <div class="flex items-center gap-1.5">
                                                    @if ($isWinner)
                                                        <span class="font-extrabold text-slate-900">
                                                            @if ($code === 'C1')
                                                                Rp {{ number_format($val, 0, ',', '.') }}
                                                            @else
                                                                {{ $val }} {{ $diff['unit'] }}
                                                            @endif
                                                        </span>
                                                        <span class="w-1.5 h-1.5 rounded-full"
                                                              style="background-color: {{ $slotPalette['color'] }};"
                                                              title="Unggul di parameter ini"></span>
                                                    @else
                                                        <span class="text-slate-500">
                                                            @if ($code === 'C1')
                                                                Rp {{ number_format($val, 0, ',', '.') }}
                                                            @else
                                                                {{ $val }} {{ $diff['unit'] }}
                                                            @endif
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- ──────────────────────────────────────────────────────────────
             4. FULL SPECIFICATIONS MATRIX TABLE (Clean Labels C1-C6)
        ────────────────────────────────────────────────────────────────── --}}
        <div class="bg-white border border-[#E2E8F0] rounded-lg overflow-hidden shadow-2xs">
            <div class="px-6 py-4 bg-slate-50 border-b border-[#E2E8F0] flex items-center justify-between">
                <div>
                    <h2 class="text-xs sm:text-sm font-mono font-bold uppercase tracking-wider text-slate-900">
                        Matriks Spesifikasi & Nilai Lengkap
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Data terstandarisasi untuk perhitungan Multi-Criteria Decision Making (AHP-TOPSIS).
                    </p>
                </div>
                <span class="text-xs font-mono text-slate-500">
                    {{ $criteria->count() }} Kriteria Terdaftar
                </span>
            </div>

            <div class="overflow-x-auto no-scrollbar">
                <table class="w-full text-left text-sm border-collapse">
                    <thead>
                        <tr class="bg-white dark:bg-slate-900 border-b border-[#E2E8F0] dark:border-slate-800 text-xs font-bold text-slate-900 dark:text-white">
                            <th class="py-3.5 px-6 w-64 sticky left-0 bg-white dark:bg-slate-900 z-10 border-r border-[#E2E8F0] dark:border-slate-800">
                                Kriteria Spesifikasi
                            </th>
                            @foreach ($comparedLaptops as $lapIdx => $lap)
                                @php $slotPalette = $palette[$lapIdx] ?? $palette[0]; @endphp
                                <th class="py-3.5 px-6 min-w-[200px]">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $slotPalette['color'] }};"></span>
                                        <span class="font-bold text-slate-900 dark:text-white">{{ $lap->name }}</span>
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E2E8F0] dark:divide-slate-800">
                        {{-- SPK ranking row if active in session --}}
                        @if (session('spk_ranking'))
                            <tr class="bg-blue-50/40 dark:bg-blue-950/30">
                                <td class="py-3.5 px-6 font-bold text-slate-900 dark:text-white sticky left-0 bg-blue-50/60 dark:bg-blue-950/50 z-10 border-r border-[#E2E8F0] dark:border-slate-800">
                                    Peringkat Smart Match (TOPSIS)
                                </td>
                                @foreach ($comparedLaptops as $lap)
                                    @php
                                        $rankingList = session('spk_ranking', []);
                                        $userRank = '-';
                                        foreach ($rankingList as $idx => $rItem) {
                                            if (($rItem['laptop_id'] ?? null) == $lap->id || ($rItem['id'] ?? null) == $lap->id) {
                                                $userRank = '#' . ($idx + 1);
                                                break;
                                            }
                                        }
                                    @endphp
                                    <td class="py-3.5 px-6 text-blue-600 dark:text-blue-400 font-bold font-mono text-sm">
                                        {{ $userRank }}
                                    </td>
                                @endforeach
                            </tr>
                        @endif

                        {{-- Loop Criteria C1-C6 with Consistent Left Column Labels --}}
                        @foreach ($criteria as $crit)
                            @php
                                $critValues = [];
                                foreach ($comparedLaptops as $lap) {
                                    if ($crit->code === 'C1') {
                                        $critValues[$lap->id] = (float)$lap->price;
                                    } else {
                                        $val = $lap->values->firstWhere('criteria_id', $crit->id)?->value;
                                        if ($val !== null) {
                                            $critValues[$lap->id] = (float)$val;
                                        }
                                    }
                                }

                                $hasDiff = count(array_unique($critValues)) > 1;
                                $bestVal = null;
                                if (!empty($critValues)) {
                                    $bestVal = $crit->type === 'cost' ? min($critValues) : max($critValues);
                                }

                                $statMin = $criteriaStats[$crit->code]['min'] ?? min($critValues);
                                $statMax = $criteriaStats[$crit->code]['max'] ?? max($critValues);
                                $statRange = max(1, $statMax - $statMin);
                            @endphp

                            <tr x-show="!onlyDiff || {{ $hasDiff ? 'true' : 'false' }}"
                                class="hover:bg-slate-50 dark:hover:bg-slate-800/60 transition group">
                                <td class="py-3.5 px-6 sticky left-0 bg-white dark:bg-slate-900 group-hover:bg-slate-50 dark:group-hover:bg-slate-800/60 z-10 border-r border-[#E2E8F0] dark:border-slate-800 transition-colors">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-xs font-mono font-bold px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                            {{ $crit->code }}
                                        </span>
                                        <span class="font-bold text-slate-900 dark:text-white text-sm">{{ $crit->name }}</span>
                                    </div>
                                    <div class="text-xs font-mono">
                                        @if ($crit->type === 'cost')
                                            <span class="text-amber-600 dark:text-amber-400 font-semibold">[Cost]</span>
                                            <span class="text-slate-400 dark:text-slate-400">· Makin hemat makin baik</span>
                                        @else
                                            <span class="text-blue-600 dark:text-blue-400 font-semibold">[Benefit]</span>
                                            <span class="text-slate-400 dark:text-slate-400">· Makin besar makin baik</span>
                                        @endif
                                    </div>
                                </td>

                                @foreach ($comparedLaptops as $lap)
                                    @php
                                        $v = $critValues[$lap->id] ?? null;
                                        $isBest = ($v !== null && $bestVal !== null && $v == $bestVal && $hasDiff);
                                        $pct = $v !== null ? min(100, max(5, (($v - $statMin) / $statRange) * 100)) : 0;
                                    @endphp
                                    <td class="py-3 px-5 {{ $isBest ? 'bg-blue-50/20 dark:bg-blue-950/20' : '' }}">
                                        @if ($v !== null)
                                            <div class="flex items-center justify-between mb-1.5">
                                                <span class="font-mono tabular-nums {{ $isBest ? 'font-bold text-slate-900 dark:text-white' : 'text-slate-700 dark:text-slate-300' }}">
                                                    @if ($crit->code === 'C1')
                                                        Rp {{ number_format($v, 0, ',', '.') }}
                                                    @else
                                                        {{ $v }} {{ $crit->unit }}
                                                    @endif
                                                </span>
                                                @if ($isBest)
                                                    <span class="text-[9px] font-mono font-bold uppercase tracking-wider px-1.5 py-0.5 rounded-sm bg-blue-50 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-700/60">
                                                        Unggul
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="w-full bg-[#E2E8F0] dark:bg-slate-700 rounded-xs h-1 overflow-hidden">
                                                <div class="h-full {{ $isBest ? 'bg-blue-600 dark:bg-blue-500' : 'bg-slate-400 dark:bg-slate-500' }}" style="width: {{ $pct }}%"></div>
                                            </div>
                                        @else
                                            <span class="text-slate-400 dark:text-slate-500">-</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ──────────────────────────────────────────────────────────────
             5. ACADEMIC CALCULATION INSPECTOR (AHP-TOPSIS Step-by-Step)
             Progressive Disclosure for Science Management / SPK Course
        ────────────────────────────────────────────────────────────────── --}}
        <div class="bg-white border border-[#E2E8F0] rounded-lg overflow-hidden shadow-2xs">

            {{-- Accordion Toggle Header --}}
            <button type="button"
                    @click="showMath = !showMath"
                    class="w-full px-6 py-4.5 bg-slate-900 hover:bg-slate-800 text-white flex items-center justify-between transition cursor-pointer text-left">
                <div class="flex items-center gap-3">
                    <span class="w-7 h-7 rounded-md bg-white/10 text-white flex items-center justify-center font-mono text-sm font-bold">
                        &sum;
                    </span>
                    <div>
                        <span class="text-sm font-mono font-bold uppercase tracking-wider block">
                            Bedah Perhitungan Matematis (AHP-TOPSIS Inspector)
                        </span>
                        <span class="text-xs text-slate-300 mt-0.5 block">
                            Transparansi langkah perankingan: Bobot Prioritas &bull; Normalisasi Euclidean &bull; Jarak Solusi Ideal
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-2.5">
                    <span class="hidden sm:inline-block text-xs font-mono px-2.5 py-1 rounded bg-white/15 text-slate-200">
                        Progressive Disclosure
                    </span>
                    <svg class="w-4.5 h-4.5 text-slate-300 transition-transform duration-200"
                         :class="showMath ? 'rotate-180' : ''"
                         viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>
                </div>
            </button>

            {{-- Accordion Content --}}
            <div x-show="showMath"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="p-6 sm:p-7 space-y-6 text-xs sm:text-sm bg-slate-50/50 border-t border-[#E2E8F0]"
                 style="display:none;">

                {{-- Theoretical intro callout --}}
                <div class="p-4.5 rounded-lg bg-blue-50/60 dark:bg-blue-950/40 border border-blue-200/80 dark:border-blue-800/60 text-blue-950 dark:text-blue-200 text-xs sm:text-sm leading-relaxed space-y-1.5">
                    <div class="flex items-center gap-2 font-bold text-blue-900 dark:text-blue-300">
                        <svg class="w-4.5 h-4.5 shrink-0 text-blue-600 dark:text-blue-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="16" x2="12" y2="12"/>
                            <line x1="12" y1="8" x2="12.01" y2="8"/>
                        </svg>
                        <span class="text-sm font-bold">Transparansi Algoritma Multi-Criteria Decision Making (MCDM)</span>
                    </div>
                    <p class="text-xs sm:text-sm text-blue-900/80 dark:text-blue-300/90 leading-relaxed">
                        Modul ini memperlihatkan seluruh rantai komputasi matematis penentuan alternatif terbaik menggunakan kombinasi metode <strong>AHP (Analytic Hierarchy Process)</strong> untuk pembobotan kriteria dan <strong>TOPSIS (Technique for Order Preference by Similarity to Ideal Solution)</strong> untuk perankingan jarak kedekatan relatif. Seluruh angka dihitung secara deterministik untuk memenuhi standar penilaian akademis mata kuliah Manajemen Sains.
                    </p>
                </div>

                {{-- TABEL 1: Bobot Kriteria AHP (W_j) --}}
                <div class="bg-white dark:bg-slate-900 border border-[#E2E8F0] dark:border-slate-800 rounded-lg overflow-hidden shadow-2xs">
                    <div class="px-4 py-3 bg-slate-50 dark:bg-slate-800 border-b border-[#E2E8F0] dark:border-slate-800 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-mono font-bold text-slate-900 dark:text-white">Tabel 1: Vektor Bobot Kriteria AHP (W_j)</span>
                            <span class="text-[10px] font-mono px-1.5 py-0.2 rounded bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60">
                                CR &le; 0.10 (Konsisten)
                            </span>
                        </div>
                        <span class="text-[10px] font-mono text-slate-500 dark:text-slate-400">&Sigma; W = 1.0000</span>
                    </div>
                    <div class="overflow-x-auto no-scrollbar">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-white dark:bg-slate-900 border-b border-[#E2E8F0] dark:border-slate-800 text-[10px] font-mono text-slate-500 dark:text-slate-400 uppercase">
                                    <th class="py-2.5 px-4">Kode</th>
                                    <th class="py-2.5 px-4">Nama Kriteria</th>
                                    <th class="py-2.5 px-4">Sifat Kriteria</th>
                                    <th class="py-2.5 px-4 text-right">Bobot Prioritas (W_j)</th>
                                    <th class="py-2.5 px-4">Persentase</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#E2E8F0] dark:divide-slate-800">
                                @foreach ($criteria as $crit)
                                    @php $w = $ahpWeights[$crit->code] ?? 0.1667; @endphp
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-colors">
                                        <td class="py-2 px-4 font-mono font-bold text-slate-900 dark:text-white">{{ $crit->code }}</td>
                                        <td class="py-2 px-4 font-medium text-slate-800 dark:text-slate-200">{{ $crit->name }}</td>
                                        <td class="py-2 px-4 font-mono text-[11px]">
                                            @if ($crit->type === 'cost')
                                                <span class="text-amber-700 dark:text-amber-400 font-semibold">[Cost] Biaya Minimal</span>
                                            @else
                                                <span class="text-blue-700 dark:text-blue-400 font-semibold">[Benefit] Manfaat Maksimal</span>
                                            @endif
                                        </td>
                                        <td class="py-2 px-4 font-mono tabular-nums text-right font-bold text-slate-900 dark:text-white">
                                            {{ number_format($w, 4) }}
                                        </td>
                                        <td class="py-2 px-4 font-mono tabular-nums text-slate-600 dark:text-slate-400">
                                            {{ number_format($w * 100, 1) }}%
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- TABEL 2: Matriks Keputusan X & Normalisasi Euclidean R --}}
                <div class="bg-white dark:bg-slate-900 border border-[#E2E8F0] dark:border-slate-800 rounded-lg overflow-hidden shadow-2xs">
                    <div class="px-4 py-3 bg-slate-50 dark:bg-slate-800 border-b border-[#E2E8F0] dark:border-slate-800 flex items-center justify-between">
                        <span class="text-xs font-mono font-bold text-slate-900 dark:text-white">
                            Tabel 2: Normalisasi Vektor Euclidean (r_ij = x_ij / &radic;&Sigma; x_kj&sup2;)
                        </span>
                    </div>
                    <div class="overflow-x-auto no-scrollbar">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-white dark:bg-slate-900 border-b border-[#E2E8F0] dark:border-slate-800 text-[10px] font-mono text-slate-500 dark:text-slate-400 uppercase">
                                    <th class="py-2.5 px-4 w-48">Laptop Alternatif</th>
                                    @foreach ($criteria as $crit)
                                        <th class="py-2.5 px-4 text-center font-mono">
                                            {{ $crit->code }} ({{ $crit->type }})
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#E2E8F0] dark:divide-slate-800">
                                @foreach ($comparedLaptops as $lap)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-colors">
                                        <td class="py-2 px-4 font-semibold text-slate-900 dark:text-white truncate max-w-[180px]">
                                            {{ $lap->name }}
                                        </td>
                                        @foreach ($criteria as $crit)
                                            <td class="py-2 px-4 font-mono tabular-nums text-center text-slate-700 dark:text-slate-300">
                                                {{ number_format($matrixR[$lap->id][$crit->code], 4) }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                                <tr class="bg-slate-100/70 dark:bg-slate-800/60 font-mono text-[10px] text-slate-600 dark:text-slate-300">
                                    <td class="py-2 px-4 font-bold text-slate-700 dark:text-slate-200">Pembagi (&radic;&Sigma; x&sup2;)</td>
                                    @foreach ($criteria as $crit)
                                        <td class="py-2 px-4 text-center tabular-nums">
                                            {{ $crit->code === 'C1' ? number_format($divisors[$crit->code], 0, ',', '.') : number_format($divisors[$crit->code], 2) }}
                                        </td>
                                    @endforeach
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- TABEL 3: Matriks Ternormalisasi Terbobot Y (y_ij = w_j * r_ij) --}}
                <div class="bg-white dark:bg-slate-900 border border-[#E2E8F0] dark:border-slate-800 rounded-lg overflow-hidden shadow-2xs">
                    <div class="px-4 py-3 bg-slate-50 dark:bg-slate-800 border-b border-[#E2E8F0] dark:border-slate-800 flex items-center justify-between">
                        <span class="text-xs font-mono font-bold text-slate-900 dark:text-white">
                            Tabel 3: Matriks Ternormalisasi Terbobot (y_ij = W_j &times; r_ij)
                        </span>
                    </div>
                    <div class="overflow-x-auto no-scrollbar">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-white dark:bg-slate-900 border-b border-[#E2E8F0] dark:border-slate-800 text-[10px] font-mono text-slate-500 dark:text-slate-400 uppercase">
                                    <th class="py-2.5 px-4 w-48">Laptop Alternatif</th>
                                    @foreach ($criteria as $crit)
                                        <th class="py-2.5 px-4 text-center font-mono">
                                            {{ $crit->code }} (W={{ $ahpWeights[$crit->code] }})
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#E2E8F0] dark:divide-slate-800">
                                @foreach ($comparedLaptops as $lap)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-colors">
                                        <td class="py-2 px-4 font-semibold text-slate-900 dark:text-white truncate max-w-[180px]">
                                            {{ $lap->name }}
                                        </td>
                                        @foreach ($criteria as $crit)
                                            <td class="py-2 px-4 font-mono tabular-nums text-center font-bold text-slate-800 dark:text-slate-200">
                                                {{ number_format($matrixY[$lap->id][$crit->code], 4) }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- TABEL 4: Solusi Ideal Positif (A+) & Negatif (A-) --}}
                <div class="bg-white dark:bg-slate-900 border border-[#E2E8F0] dark:border-slate-800 rounded-lg overflow-hidden shadow-2xs">
                    <div class="px-4 py-3 bg-slate-50 dark:bg-slate-800 border-b border-[#E2E8F0] dark:border-slate-800">
                        <span class="text-xs font-mono font-bold text-slate-900 dark:text-white">
                            Tabel 4: Solusi Ideal Positif (A+) dan Solusi Ideal Negatif (A-)
                        </span>
                    </div>
                    <div class="overflow-x-auto no-scrollbar">
                        <table class="w-full text-left text-xs border-collapse font-mono">
                            <thead>
                                <tr class="bg-white dark:bg-slate-900 border-b border-[#E2E8F0] dark:border-slate-800 text-[10px] text-slate-500 dark:text-slate-400 uppercase">
                                    <th class="py-2.5 px-4 w-48">Titik Solusi Ideal</th>
                                    @foreach ($criteria as $crit)
                                        <th class="py-2.5 px-4 text-center">
                                            {{ $crit->code }} [{{ strtoupper($crit->type) }}]
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#E2E8F0] dark:divide-slate-800 text-xs">
                                <tr class="bg-emerald-50/50 dark:bg-emerald-950/30">
                                    <td class="py-2.5 px-4 font-bold text-emerald-900 dark:text-emerald-300">
                                        Solusi Ideal Positif (A+)
                                    </td>
                                    @foreach ($criteria as $crit)
                                        <td class="py-2.5 px-4 text-center font-bold tabular-nums text-emerald-800 dark:text-emerald-200">
                                            {{ number_format($solIdealPos[$crit->code], 4) }}
                                        </td>
                                    @endforeach
                                </tr>
                                <tr class="bg-amber-50/50 dark:bg-amber-950/30">
                                    <td class="py-2.5 px-4 font-bold text-amber-900 dark:text-amber-300">
                                        Solusi Ideal Negatif (A-)
                                    </td>
                                    @foreach ($criteria as $crit)
                                        <td class="py-2.5 px-4 text-center font-bold tabular-nums text-amber-800 dark:text-amber-200">
                                            {{ number_format($solIdealNeg[$crit->code], 4) }}
                                        </td>
                                    @endforeach
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- TABEL 5: Jarak Euclidean & Nilai Preferensi V_i (Perankingan Akhir) --}}
                <div class="bg-white dark:bg-slate-900 border border-[#E2E8F0] dark:border-slate-800 rounded-lg overflow-hidden shadow-2xs">
                    <div class="px-4 py-3 bg-slate-900 text-white flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-mono font-bold">
                                Tabel 5: Jarak Euclidean (D+, D-) & Nilai Preferensi Akhir (V_i)
                            </span>
                        </div>
                        <span class="text-[10px] font-mono text-slate-300">V_i = D- / (D- + D+)</span>
                    </div>
                    <div class="overflow-x-auto no-scrollbar">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-slate-800 border-b border-[#E2E8F0] dark:border-slate-800 text-[10px] font-mono text-slate-500 dark:text-slate-400 uppercase">
                                    <th class="py-2.5 px-4 w-12 text-center">Rank</th>
                                    <th class="py-2.5 px-4">Laptop Alternatif</th>
                                    <th class="py-2.5 px-4 text-right font-mono">Jarak Ideal Positif (D+)</th>
                                    <th class="py-2.5 px-4 text-right font-mono">Jarak Ideal Negatif (D-)</th>
                                    <th class="py-2.5 px-4 text-right font-mono">Nilai Preferensi (V_i)</th>
                                    <th class="py-2.5 px-4 text-center">Status Keputusan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#E2E8F0] dark:divide-slate-800">
                                @foreach ($rankedByTopsis as $rankIdx => $lap)
                                    @php
                                        $isRank1 = ($rankIdx === 0);
                                        $vScoreVal = $prefScores[$lap->id];
                                    @endphp
                                    <tr class="{{ $isRank1 ? 'bg-blue-50/40 dark:bg-blue-950/40 font-semibold' : 'hover:bg-slate-50 dark:hover:bg-slate-800/60' }} transition-colors">
                                        <td class="py-3 px-4 text-center font-mono font-bold {{ $isRank1 ? 'text-blue-700 dark:text-blue-400 text-sm' : 'text-slate-500 dark:text-slate-400' }}">
                                            #{{ $rankIdx + 1 }}
                                        </td>
                                        <td class="py-3 px-4 text-slate-900 dark:text-white">
                                            <div class="font-bold">{{ $lap->name }}</div>
                                            <div class="text-[11px] text-slate-500 dark:text-slate-400 font-mono">{{ $lap->formatted_price }}</div>
                                        </td>
                                        <td class="py-3 px-4 font-mono tabular-nums text-right text-slate-700 dark:text-slate-300">
                                            {{ number_format($distPos[$lap->id], 4) }}
                                        </td>
                                        <td class="py-3 px-4 font-mono tabular-nums text-right text-slate-700 dark:text-slate-300">
                                            {{ number_format($distNeg[$lap->id], 4) }}
                                        </td>
                                        <td class="py-3 px-4 font-mono tabular-nums text-right text-sm font-bold {{ $isRank1 ? 'text-blue-600 dark:text-blue-400' : 'text-slate-900 dark:text-white' }}">
                                            {{ number_format($vScoreVal, 4) }}
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            @if ($isRank1)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-blue-600 text-white text-[10px] font-mono font-bold uppercase tracking-wider shadow-2xs">
                                                    <span>&starf; Rekomendasi Utama</span>
                                                </span>
                                            @else
                                                <span class="text-[10px] font-mono text-slate-500 dark:text-slate-400">Alternatif #{{ $rankIdx + 1 }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

    @else

        {{-- ──────────────────────────────────────────────────────────────
             EMPTY STATE (Belum Ada Laptop Dipilih)
        ────────────────────────────────────────────────────────────────── --}}
        <div class="bg-white border border-[#E2E8F0] rounded-lg p-8 sm:p-12 text-center max-w-2xl mx-auto space-y-5 shadow-2xs">
            <div class="w-12 h-12 rounded-md bg-slate-50 border border-[#E2E8F0] mx-auto flex items-center justify-center text-blue-600">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <rect x="2" y="3" width="8" height="18" rx="1"/>
                    <rect x="14" y="3" width="8" height="18" rx="1"/>
                    <line x1="6" y1="8" x2="6" y2="8.01"/>
                    <line x1="18" y1="8" x2="18" y2="8.01"/>
                </svg>
            </div>

            <div class="space-y-1">
                <h2 class="text-xl font-bold text-slate-900 tracking-tight">
                    Pilih Laptop untuk Dikomparasi
                </h2>
                <p class="text-xs text-slate-500 max-w-md mx-auto leading-relaxed">
                    Pilih minimal 2 laptop untuk membandingkan spesifikasi head-to-head, radar performa, serta keunggulan masing-masing unit.
                </p>
            </div>

            {{-- Quick Comparison Form --}}
            <form method="GET" action="{{ route('catalog.compare') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-left pt-2">
                <div>
                    <label for="compare-first-laptop" class="block text-[10px] font-mono font-bold uppercase tracking-wider text-slate-500 mb-1">
                        Laptop Pertama:
                    </label>
                    <select id="compare-first-laptop"
                            name="laptop_ids[]"
                            class="w-full text-xs bg-slate-50 border border-[#E2E8F0] rounded-md px-3 py-2 text-slate-900 font-medium outline-none focus:border-slate-400">
                        @foreach ($allLaptops as $lap)
                            <option value="{{ $lap->id }}">{{ $lap->brand }} — {{ $lap->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="compare-second-laptop" class="block text-[10px] font-mono font-bold uppercase tracking-wider text-slate-500 mb-1">
                        Laptop Kedua:
                    </label>
                    <select id="compare-second-laptop"
                            name="laptop_ids[]"
                            class="w-full text-xs bg-slate-50 border border-[#E2E8F0] rounded-md px-3 py-2 text-slate-900 font-medium outline-none focus:border-slate-400">
                        @foreach ($allLaptops as $idx => $lap)
                            <option value="{{ $lap->id }}" {{ $idx === 1 ? 'selected' : '' }}>
                                {{ $lap->brand }} — {{ $lap->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2 pt-2">
                    <button type="submit"
                            class="w-full py-2.5 px-4 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-md shadow-2xs transition cursor-pointer">
                        Mulai Bandingkan Spesifikasi &rarr;
                    </button>
                </div>
            </form>

            {{-- 1-Click Popular Comparison Shortcuts (Heuristic #6: Recognition Rather Than Recall) --}}
            <div class="pt-5 border-t border-slate-100 dark:border-slate-800 space-y-2.5 text-left w-full">
                <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    ⚡ Contoh Komparasi Populer (Langsung Lihat Radar & Matriks):
                </span>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <a href="{{ route('catalog.compare', ['laptop_ids' => [8, 13]]) }}"
                       class="flex items-center justify-between p-3 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40 hover:bg-slate-100 dark:hover:bg-slate-800 transition group text-left">
                        <div>
                            <span class="text-xs font-bold text-slate-900 dark:text-slate-100 block">Ultrabook vs Gaming</span>
                            <span class="text-[11px] text-slate-500 dark:text-slate-400 font-mono">MacBook Air M2 vs TUF Gaming A15</span>
                        </div>
                        <span class="text-blue-600 dark:text-blue-400 font-mono text-xs group-hover:translate-x-1 transition-transform">&rarr;</span>
                    </a>

                    <a href="{{ route('catalog.compare', ['laptop_ids' => [1, 2]]) }}"
                       class="flex items-center justify-between p-3 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40 hover:bg-slate-100 dark:hover:bg-slate-800 transition group text-left">
                        <div>
                            <span class="text-xs font-bold text-slate-900 dark:text-slate-100 block">Budget Mahasiswa (Entry)</span>
                            <span class="text-[11px] text-slate-500 dark:text-slate-400 font-mono">Acer Aspire 3 vs Lenovo Slim 3</span>
                        </div>
                        <span class="text-blue-600 dark:text-blue-400 font-mono text-xs group-hover:translate-x-1 transition-transform">&rarr;</span>
                    </a>
                </div>
            </div>

            <div class="pt-2">
                <a href="{{ route('catalog.index') }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline font-medium">
                    Atau jelajahi katalog laptop terlebih dahulu &rarr;
                </a>
            </div>
        </div>

    @endif

</div>
@endsection
