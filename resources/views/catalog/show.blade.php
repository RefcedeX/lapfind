@extends('layouts.public', ['title' => $alternative->name . ' — Spesifikasi'])

@section('content')
@php
    $allLaptops = \App\Models\Alternative::with('values.criterion')->get();

    // Versus Rating Points calculation (consistent across app)
    $getVersusScore = function ($lap) {
        $cVals = $lap->values->keyBy('criterion.code');
        $base  = 72;
        $cpu   = isset($cVals['C2']) ? ($cVals['C2']->value / 10.0) * 12 : 6;
        $ram   = isset($cVals['C3']) ? ($cVals['C3']->value / 32.0) *  8 : 4;
        $ssd   = isset($cVals['C4']) ? ($cVals['C4']->value / 1024.0) * 5 : 2.5;
        $bat   = isset($cVals['C6']) ? ($cVals['C6']->value / 16.0) *  4 : 2;
        return (int) min(99, max(75, round($base + $cpu + $ram + $ssd + $bat)));
    };
    $versusScore = $getVersusScore($alternative);
    $isGaming = (bool) preg_match('/(gaming|tuf|legion|loq|nitro|predator|cyborg|katana|victus|omen|pongo)/i', $alternative->name . ' ' . $alternative->description);
@endphp

<div class="space-y-8">

    {{-- ──────────────────────────────────────────────────────────────────
         BREADCRUMB & PRIMARY ACTIONS
    ────────────────────────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <nav class="flex items-center gap-2 text-xs sm:text-sm text-slate-500">
            <a href="{{ route('home') }}" class="hover:text-slate-900 transition">Home</a>
            <span>/</span>
            <a href="{{ route('catalog.index') }}" class="hover:text-slate-900 transition">Katalog</a>
            <span>/</span>
            <span class="text-slate-500">{{ $alternative->brand }}</span>
            <span>/</span>
            <span class="text-slate-900 font-semibold truncate">{{ $alternative->name }}</span>
        </nav>

        <div class="flex items-center gap-2.5">
            {{-- Compare toggle button --}}
            <button type="button"
                    @click="$store.compare.toggle({
                        id: {{ $alternative->id }},
                        name: '{{ addslashes($alternative->name) }}',
                        brand: '{{ addslashes($alternative->brand) }}',
                        image: '{{ $alternative->image_url }}',
                        price: '{{ $alternative->formatted_price }}'
                    })"
                    class="px-4 py-2 rounded-lg text-sm font-semibold border transition cursor-pointer flex items-center gap-2 shadow-2xs"
                    :class="$store.compare.has({{ $alternative->id }})
                        ? 'bg-blue-600 text-white border-blue-600'
                        : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'">
                <span x-text="$store.compare.has({{ $alternative->id }}) ? '✓ Dalam Perbandingan' : '+ Bandingkan Unit'"></span>
            </button>

            <a href="{{ route('spk.index') }}"
               class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-sm font-semibold rounded-lg transition shadow-2xs">
                Smart Match SPK &rarr;
            </a>
        </div>
    </div>

    {{-- ──────────────────────────────────────────────────────────────────
         MAIN PRODUCT HERO (Technical Studio Showcase)
    ────────────────────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        {{-- LEFT: Studio Image & Score Badge --}}
        <div class="lg:col-span-5 bg-white border border-slate-200 rounded-xl p-6 sm:p-7 space-y-4 shadow-2xs">
            <div class="flex items-center justify-between pb-3.5 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-mono font-bold uppercase tracking-wider text-slate-500">
                        {{ $alternative->brand }} &bull; ID #{{ $alternative->id }}
                    </span>
                    @if ($isGaming)
                        <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded bg-purple-50 text-purple-700 border border-purple-200">
                            GAMING
                        </span>
                    @endif
                </div>

                {{-- Rating Badge --}}
                <div class="flex items-center gap-2.5">
                    <span class="text-xs font-mono text-slate-500">Rating</span>
                    <div class="score-badge shrink-0" title="Versus Hardware Rating: {{ $versusScore }}/100">
                        <span class="score-num font-mono tabular-nums">{{ $versusScore }}</span>
                        <span class="score-lbl">pts</span>
                    </div>
                </div>
            </div>

            <div class="aspect-[16/10] flex items-center justify-center p-4 bg-slate-50 border border-slate-200/80 rounded-lg">
                <img src="{{ $alternative->image_url }}"
                     alt="{{ $alternative->name }}"
                     class="object-contain w-full h-full max-h-56 transition-transform duration-300 hover:scale-105">
            </div>

            <div class="pt-2.5 text-xs text-slate-500 flex items-center justify-between border-t border-slate-100">
                <span>Brand: <strong class="text-slate-900">{{ $alternative->brand }}</strong></span>
                <span class="font-mono text-xs">Garansi Resmi Indonesia</span>
            </div>
        </div>

        {{-- RIGHT: Overview & Parameter Nilai Evaluasi (Matriks Spesifikasi) --}}
        <div class="lg:col-span-7 space-y-6">
            <div>
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="text-xs font-mono font-bold uppercase tracking-wider text-blue-600">
                        {{ $alternative->brand }}
                    </span>
                    @if ($isGaming)
                        <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded bg-purple-50 text-purple-700 border border-purple-200">
                            Gaming
                        </span>
                    @endif
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight leading-tight">
                    {{ $alternative->name }}
                </h1>
                <div class="text-2xl sm:text-3xl font-bold text-slate-900 font-mono tabular-nums mt-2.5">
                    {{ $alternative->formatted_price }}
                </div>
                <p class="text-sm sm:text-base text-slate-600 mt-3.5 leading-relaxed">
                    {{ $alternative->description }}
                </p>
            </div>

            {{-- Full Specs Matrix — Preserves 'Parameter Nilai Evaluasi' for testing --}}
            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-2xs">
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
                    <h2 class="text-xs sm:text-sm font-mono font-bold uppercase tracking-wider text-slate-900">
                        Parameter Nilai Evaluasi (Matriks Spesifikasi)
                    </h2>
                </div>

                <table class="w-full text-sm divide-y divide-slate-200">
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach ($criteria as $crit)
                            @php
                                $val = $alternative->values->firstWhere('criteria_id', $crit->id)?->value;
                                if ($crit->code === 'C1') {
                                    $val = (float)$alternative->price;
                                }
                            @endphp
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-colors">
                                <td class="py-3.5 px-6 font-bold text-slate-900 dark:text-white w-48">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-mono font-bold px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                            {{ $crit->code }}
                                        </span>
                                        <span>{{ $crit->name }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-6 text-slate-500 dark:text-slate-400 text-xs w-48 font-mono">
                                    {{ $crit->type === 'cost' ? 'Cost [Makin hemat makin baik]' : 'Benefit [Makin besar makin baik]' }}
                                </td>
                                <td class="py-3.5 px-6 font-bold text-slate-900 dark:text-white font-mono tabular-nums text-right">
                                    @if ($crit->code === 'C1')
                                        Rp {{ number_format($val, 0, ',', '.') }}
                                    @else
                                        {{ $val }} {{ $crit->unit }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ──────────────────────────────────────────────────────────────────
         DISTRIBUTION BENCHMARK AMONG ALL ALTERNATIVES
    ────────────────────────────────────────────────────────────────────── --}}
    <div class="bg-white border border-slate-200 rounded-xl p-6 sm:p-8 space-y-6 shadow-2xs">
        <div class="border-b border-slate-200 pb-4">
            <h2 class="text-lg font-bold text-slate-900 tracking-tight">
                Posisi Distribusi di Antara Seluruh Alternatif
            </h2>
            <p class="text-sm text-slate-500 mt-1">
                Titik biru menunjukkan posisi {{ $alternative->name }} relatif terhadap seluruh alternatif laptop di katalog.
            </p>
        </div>

        <div class="space-y-6">
            @foreach ($criteria as $crit)
                @php
                    $allValues = [];
                    foreach ($allLaptops as $lap) {
                        if ($crit->code === 'C1') {
                            $allValues[] = (float)$lap->price;
                        } else {
                            $v = $lap->values->firstWhere('criteria_id', $crit->id)?->value;
                            if ($v !== null) $allValues[] = (float)$v;
                        }
                    }

                    $minVal = !empty($allValues) ? min($allValues) : 0;
                    $maxVal = !empty($allValues) ? max($allValues) : 1;
                    $range = max(1, $maxVal - $minVal);

                    $currentVal = ($crit->code === 'C1')
                        ? (float)$alternative->price
                        : (float)($alternative->values->firstWhere('criteria_id', $crit->id)?->value ?? 0);

                    $currentPct = min(100, max(0, (($currentVal - $minVal) / $range) * 100));
                @endphp

                <div class="space-y-2">
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-slate-900">{{ $crit->name }}</span>
                            <span class="text-xs text-slate-500 font-mono">
                                ({{ $crit->type === 'cost' ? 'Min Lebih Baik' : 'Maks Lebih Baik' }})
                            </span>
                        </div>
                        <div class="font-bold text-blue-600 font-mono tabular-nums">
                            @if ($crit->code === 'C1')
                                Rp {{ number_format($currentVal, 0, ',', '.') }}
                            @else
                                {{ $currentVal }} {{ $crit->unit }}
                            @endif
                        </div>
                    </div>

                    {{-- Distribution line --}}
                    <div class="relative w-full h-3 bg-slate-100 border border-slate-200 rounded-full flex items-center">
                        @foreach ($allValues as $valItem)
                            @php
                                $dotPct = min(100, max(0, (($valItem - $minVal) / $range) * 100));
                            @endphp
                            <div class="absolute w-1.5 h-1.5 rounded-full bg-slate-300"
                                 style="left: {{ $dotPct }}%; transform: translateX(-50%);"></div>
                        @endforeach

                        {{-- Active Laptop Highlight Dot --}}
                        <div class="absolute w-3.5 h-3.5 rounded-full bg-blue-600 border-2 border-white shadow-xs z-10"
                             style="left: {{ $currentPct }}%; transform: translateX(-50%);"
                             title="{{ $alternative->name }}: {{ $currentVal }}"></div>
                    </div>

                    <div class="flex items-center justify-between text-xs text-slate-500 font-mono tabular-nums">
                        <span>Min: {{ $crit->code === 'C1' ? 'Rp '.number_format($minVal, 0, ',', '.') : $minVal.' '.$crit->unit }}</span>
                        <span>Maks: {{ $crit->code === 'C1' ? 'Rp '.number_format($maxVal, 0, ',', '.') : $maxVal.' '.$crit->unit }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ──────────────────────────────────────────────────────────────────
         SIMILAR LAPTOPS (RENTANG HARGA SEJENIS)
    ────────────────────────────────────────────────────────────────────── --}}
    @if ($similarLaptops->isNotEmpty())
        <div class="space-y-4">
            <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                <h2 class="text-lg font-bold text-slate-900 tracking-tight">
                    Alternatif Lain dalam Rentang Harga Serupa (&plusmn;25%)
                </h2>
                <a href="{{ route('catalog.index') }}" class="text-xs sm:text-sm text-blue-600 hover:underline font-semibold">
                    Lihat Semua &rarr;
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ($similarLaptops as $sim)
                    @php
                        $simScore = $getVersusScore($sim);
                    @endphp
                    <div class="bg-white border border-slate-200 hover:border-slate-300 rounded-xl p-5 transition-all duration-200 flex flex-col justify-between shadow-2xs">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-mono font-bold uppercase text-slate-500">{{ $sim->brand }}</span>
                                <div class="score-badge shrink-0" style="min-width:40px;height:40px;padding:0 4px;" title="Rating: {{ $simScore }}/100">
                                    <span class="score-num font-mono tabular-nums text-sm">{{ $simScore }}</span>
                                </div>
                            </div>

                            <div class="aspect-[16/10] w-full flex items-center justify-center p-3 bg-slate-50 border border-slate-200/80 rounded-lg mb-3">
                                <img src="{{ $sim->image_url }}" alt="{{ $sim->name }}" class="object-contain w-full h-full max-h-28">
                            </div>

                            <a href="{{ route('catalog.show', $sim) }}" class="text-sm font-bold text-slate-900 hover:text-blue-600 line-clamp-1">
                                {{ $sim->name }}
                            </a>
                            <div class="text-sm font-mono font-bold text-slate-900 mt-1 tabular-nums">
                                {{ $sim->formatted_price }}
                            </div>
                        </div>

                        <div class="pt-3 mt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                            <a href="{{ route('catalog.compare', ['laptop_ids' => [$alternative->id, $sim->id]]) }}"
                                class="text-xs sm:text-sm font-semibold text-blue-600 hover:text-blue-700 transition">
                                Bandingkan vs {{ $sim->brand }} &rarr;
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection
