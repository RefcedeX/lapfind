@php
    // Active filter params
    $activeBrand     = request('brand');
    $activeSearch    = request('search');
    $activeMinPrice  = request('min_price');
    $activeMaxPrice  = request('max_price');
    $activeSort      = request('sort', 'versus_score');
    $activeRam       = request('ram');
    $activeStorage   = request('storage');
    $activeMaxWeight = request('max_weight');
    $activePreset    = request('preset');

    $getVersusScore = function ($lap) {
        $cVals = $lap->values->keyBy('criterion.code');
        $base  = 72;
        $cpu   = isset($cVals['C2']) ? ($cVals['C2']->value / 10.0) * 12 : 6;
        $ram   = isset($cVals['C3']) ? ($cVals['C3']->value / 32.0) *  8 : 4;
        $ssd   = isset($cVals['C4']) ? ($cVals['C4']->value / 1024.0) * 5 : 2.5;
        $bat   = isset($cVals['C6']) ? ($cVals['C6']->value / 16.0) *  4 : 2;
        return (int) min(99, max(75, round($base + $cpu + $ram + $ssd + $bat)));
    };

    match ($activeSort) {
        'ram_desc'     => $laptops = $laptops->sortByDesc(fn($l) => (float)($l->values->firstWhere('criterion.code', 'C3')?->value ?? 0)),
        'battery_desc' => $laptops = $laptops->sortByDesc(fn($l) => (float)($l->values->firstWhere('criterion.code', 'C6')?->value ?? 0)),
        'weight_asc'   => $laptops = $laptops->sortBy(fn($l)    => (float)($l->values->firstWhere('criterion.code', 'C5')?->value ?? 999)),
        'price_desc'   => $laptops = $laptops->sortByDesc('price'),
        'price_asc'    => $laptops = $laptops->sortBy('price'),
        default        => $laptops = $laptops->sortByDesc(fn($l) => $getVersusScore($l)),
    };

    $totalCount = ($laptops instanceof \Illuminate\Pagination\LengthAwarePaginator) ? $laptops->total() : $laptops->count();
    $hasFilter  = $activeBrand || $activeSearch || $activeMinPrice || $activeMaxPrice || $activeRam || $activeStorage || $activeMaxWeight;
@endphp

<div x-data="catalogLiveFilter()" id="catalog-workbench" class="space-y-6 relative">

    {{-- ──────────────────────────────────────────────────────────────────
         PAGE HEADER (Bespoke Editorial / Gen Z Tech Style)
    ────────────────────────────────────────────────────────────────────── --}}
    <div class="flex flex-col gap-4">

        {{-- Breadcrumb + title --}}
        <div>
            <nav class="flex items-center gap-1.5 text-[11px] text-slate-500 mb-2">
                <a href="{{ route('home') }}" class="hover:text-slate-900 transition">Home</a>
                <span>/</span>
                <span class="text-slate-900 font-medium">Katalog</span>
            </nav>

            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight leading-tight">
                        Katalog & Komparasi Laptop Mahasiswa
                    </h1>
                    <p class="mt-2 text-sm sm:text-base text-slate-600 max-w-2xl leading-relaxed">
                        Kurasi spesifikasi teknis laptop mahasiswa harian, ultrabook, dan lini laptop gaming. Dievaluasi dengan model multi-kriteria AHP-TOPSIS.
                    </p>
                </div>

                {{-- Result count + mobile filter toggle --}}
                <div class="flex items-center gap-2.5 shrink-0">
                    <span id="catalog-dynamic-counter" class="text-xs sm:text-sm text-slate-600 font-mono tabular-nums px-3 py-1.5 rounded-lg bg-slate-100 border border-slate-200">
                        {{ $totalCount }} unit terkurasi
                    </span>
                    <button type="button"
                            @click="mobileFilterOpen = !mobileFilterOpen"
                            class="md:hidden flex items-center gap-2 px-3.5 py-2 text-xs sm:text-sm font-semibold border border-slate-200 bg-white hover:bg-slate-50 rounded-lg transition cursor-pointer shadow-2xs">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <line x1="4" y1="6" x2="20" y2="6"/>
                            <line x1="6" y1="12" x2="18" y2="12"/>
                            <line x1="8" y1="18" x2="16" y2="18"/>
                        </svg>
                        Filter
                    </button>
                </div>
            </div>
        </div>

        {{-- Quick-filter pills (Category Shortcuts) --}}
        <div id="catalog-dynamic-pills" @click="handleLinkClick($event)" class="flex flex-wrap items-center gap-2">
            <a href="{{ route('catalog.index') }}"
               class="text-xs sm:text-sm font-medium px-3.5 py-2 rounded-lg border transition-all duration-150 {{ empty($activeRam) && empty($activeStorage) && empty($activeMaxWeight) && empty($activeBrand) && empty($activeSearch) && $activeSort === 'versus_score' ? 'bg-slate-900 border-slate-900 text-white font-semibold' : 'bg-white border-slate-200 text-slate-600 hover:border-slate-300 hover:text-slate-900' }}">
                Semua Unit
            </a>
            <a href="{{ route('catalog.index', ['search' => 'gaming']) }}"
               class="text-xs sm:text-sm font-medium px-3.5 py-2 rounded-lg border transition-all duration-150 {{ $activeSearch === 'gaming' ? 'bg-purple-900 border-purple-900 text-white font-semibold' : 'bg-purple-50 border-purple-200 text-purple-700 hover:bg-purple-100' }}">
                Gaming
            </a>
            <a href="{{ route('catalog.index', ['sort' => 'versus_score']) }}"
               class="text-xs sm:text-sm font-medium px-3.5 py-2 rounded-lg border transition-all duration-150 {{ $activeSort === 'versus_score' && empty($activeBrand) && empty($activeSearch) && (empty($activeRam) && empty($activeStorage) && empty($activeMaxWeight)) ? 'bg-slate-900 border-slate-900 text-white font-semibold' : 'bg-white border-slate-200 text-slate-600 hover:border-slate-300 hover:text-slate-900' }}">
                Skor Tertinggi
            </a>
            <a href="{{ route('catalog.index', ['ram' => '16']) }}"
               class="text-xs sm:text-sm font-medium px-3.5 py-2 rounded-lg border transition-all duration-150 {{ $activeRam === '16' ? 'bg-slate-900 border-slate-900 text-white font-semibold' : 'bg-white border-slate-200 text-slate-600 hover:border-slate-300 hover:text-slate-900' }}">
                RAM 16 GB
            </a>
            <a href="{{ route('catalog.index', ['storage' => '512']) }}"
               class="text-xs sm:text-sm font-medium px-3.5 py-2 rounded-lg border transition-all duration-150 {{ $activeStorage === '512' ? 'bg-slate-900 border-slate-900 text-white font-semibold' : 'bg-white border-slate-200 text-slate-600 hover:border-slate-300 hover:text-slate-900' }}">
                SSD 512 GB
            </a>
            <a href="{{ route('catalog.index', ['max_weight' => '1.3']) }}"
               class="text-xs sm:text-sm font-medium px-3.5 py-2 rounded-lg border transition-all duration-150 {{ $activeMaxWeight === '1.3' ? 'bg-slate-900 border-slate-900 text-white font-semibold' : 'bg-white border-slate-200 text-slate-600 hover:border-slate-300 hover:text-slate-900' }}">
                Bobot &le; 1.3 kg
            </a>
            <a href="{{ route('catalog.index', ['sort' => 'price_asc']) }}"
               class="text-xs sm:text-sm font-medium px-3.5 py-2 rounded-lg border transition-all duration-150 {{ $activeSort === 'price_asc' ? 'bg-slate-900 border-slate-900 text-white font-semibold' : 'bg-white border-slate-200 text-slate-600 hover:border-slate-300 hover:text-slate-900' }}">
                Harga Terjangkau
            </a>
        </div>

        {{-- Active Filters Bar (Heuristic #6: Recognition Rather than Recall) --}}
        <div id="catalog-dynamic-active-filters" @click="handleLinkClick($event)">
            @if ($hasFilter)
                <div class="flex flex-wrap items-center gap-1.5 pt-1 text-xs">
                    <span class="text-slate-400 text-[11px] font-mono mr-1">Filter Aktif:</span>

                    @if ($activeSearch)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-slate-100 border border-slate-200 text-slate-800 text-[11px] font-mono">
                            Cari: "{{ $activeSearch }}"
                            <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="hover:text-red-600 ml-0.5">✕</a>
                        </span>
                    @endif

                    @if ($activeBrand)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-slate-100 border border-slate-200 text-slate-800 text-[11px] font-mono">
                            Brand: {{ $activeBrand }}
                            <a href="{{ request()->fullUrlWithQuery(['brand' => null]) }}" class="hover:text-red-600 ml-0.5">✕</a>
                        </span>
                    @endif

                    @if ($activeMinPrice)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-slate-100 border border-slate-200 text-slate-800 text-[11px] font-mono">
                            Min: Rp {{ number_format((float)$activeMinPrice, 0, ',', '.') }}
                            <a href="{{ request()->fullUrlWithQuery(['min_price' => null]) }}" class="hover:text-red-600 ml-0.5">✕</a>
                        </span>
                    @endif

                    @if ($activeMaxPrice)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-slate-100 border border-slate-200 text-slate-800 text-[11px] font-mono">
                            Max: Rp {{ number_format((float)$activeMaxPrice, 0, ',', '.') }}
                            <a href="{{ request()->fullUrlWithQuery(['max_price' => null]) }}" class="hover:text-red-600 ml-0.5">✕</a>
                        </span>
                    @endif

                    @if ($activeRam)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-slate-100 border border-slate-200 text-slate-800 text-[11px] font-mono">
                            RAM: {{ $activeRam }} GB
                            <a href="{{ request()->fullUrlWithQuery(['ram' => null]) }}" class="hover:text-red-600 ml-0.5">✕</a>
                        </span>
                    @endif

                    @if ($activeStorage)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-slate-100 border border-slate-200 text-slate-800 text-[11px] font-mono">
                            SSD: {{ $activeStorage }} GB
                            <a href="{{ request()->fullUrlWithQuery(['storage' => null]) }}" class="hover:text-red-600 ml-0.5">✕</a>
                        </span>
                    @endif

                    @if ($activeMaxWeight)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-slate-100 border border-slate-200 text-slate-800 text-[11px] font-mono">
                            Bobot &le; {{ $activeMaxWeight }} kg
                            <a href="{{ request()->fullUrlWithQuery(['max_weight' => null]) }}" class="hover:text-red-600 ml-0.5">✕</a>
                        </span>
                    @endif

                    <a href="{{ url()->current() }}" class="text-[11px] text-red-600 hover:underline ml-1 font-medium">
                        Reset Semua
                    </a>
                </div>
            @endif
        </div>

        {{-- Smart Match banner — preserves test assertions --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-4 py-3 bg-slate-50 border border-slate-200/90 rounded-lg">
            <div class="flex items-center gap-2.5">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-600 shrink-0"></span>
                <div class="text-xs">
                    <span class="font-semibold text-slate-900">Rekomendasi Laptop Mahasiswa Berbasis AHP:</span>
                    <span class="text-slate-500 ml-1">Belum tahu mana yang paling cocok? Tentukan prioritas bobot, sistem mengurutkan secara matematis.</span>
                </div>
            </div>
            <a href="{{ route('spk.index') }}"
               class="shrink-0 inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-md transition shadow-2xs">
                <span>Mulai Evaluasi SPK</span>
                <span>&rarr;</span>
            </a>
        </div>
    </div>

    {{-- ──────────────────────────────────────────────────────────────────
         MAIN: FILTER RAIL + GRID
    ────────────────────────────────────────────────────────────────────── --}}
    <div class="flex flex-col md:flex-row gap-6 items-start">

        {{-- ── FILTER RAIL (Clean Minimalist Sidebar) ───────────────────── --}}
        <aside id="catalog-dynamic-sidebar"
               class="w-full md:w-56 lg:w-60 shrink-0 filter-rail text-xs"
               :class="mobileFilterOpen ? 'block' : 'hidden md:block'">

            <form id="catalog-filter-form" method="GET" action="{{ url()->current() }}" @submit.prevent="onFilterChange()" @change="onFilterChange()">
                @if ($activeSearch)<input type="hidden" name="search" value="{{ $activeSearch }}">@endif

                {{-- Sort --}}
                <div class="filter-section" style="border-top:none;margin-top:0;padding-top:0;">
                    <label for="catalog-sort" class="filter-label block">Urutkan</label>
                    <select id="catalog-sort"
                            name="sort"
                            aria-label="Urutkan daftar laptop"
                            class="w-full text-xs bg-slate-50 border border-slate-200 rounded-md px-2.5 py-1.5 text-slate-900 font-medium focus:border-slate-400 cursor-pointer outline-none">
                        <option value="versus_score" {{ $activeSort === 'versus_score' ? 'selected' : '' }}>LapFind Score</option>
                        <option value="price_asc"    {{ $activeSort === 'price_asc'    ? 'selected' : '' }}>Harga: Termurah</option>
                        <option value="price_desc"   {{ $activeSort === 'price_desc'   ? 'selected' : '' }}>Harga: Tertinggi</option>
                        <option value="ram_desc"     {{ $activeSort === 'ram_desc'     ? 'selected' : '' }}>RAM: Terbesar</option>
                        <option value="battery_desc" {{ $activeSort === 'battery_desc' ? 'selected' : '' }}>Baterai: Terlama</option>
                        <option value="weight_asc"   {{ $activeSort === 'weight_asc'   ? 'selected' : '' }}>Bobot: Teringan</option>
                    </select>
                </div>

                {{-- Price Filter (Clean Numeric Inputs + Presets) --}}
                <div class="filter-section">
                    <p class="filter-label">Harga (IDR)</p>
                    <div class="grid grid-cols-2 gap-2 mb-2.5">
                        <div>
                            <label for="filter-min-price" class="text-xs text-slate-500 mb-1 block font-mono">Min</label>
                            <input type="number"
                                   id="filter-min-price"
                                   name="min_price"
                                   value="{{ $activeMinPrice }}"
                                   placeholder="Rp Min"
                                   step="500000"
                                   aria-label="Harga minimum dalam rupiah"
                                   class="w-full text-sm font-mono tabular-nums bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-2 text-slate-900 outline-none focus:border-slate-400">
                        </div>
                        <div>
                            <label for="filter-max-price" class="text-xs text-slate-500 mb-1 block font-mono">Max</label>
                            <input type="number"
                                   id="filter-max-price"
                                   name="max_price"
                                   value="{{ $activeMaxPrice }}"
                                   placeholder="Rp Max"
                                   step="500000"
                                   aria-label="Harga maksimum dalam rupiah"
                                   class="w-full text-sm font-mono tabular-nums bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-2 text-slate-900 outline-none focus:border-slate-400">
                        </div>
                    </div>

                    {{-- Quick price presets --}}
                    <div class="flex flex-wrap gap-1.5 pt-1">
                        <button type="button"
                                @click.prevent="setPricePreset('', '10000000')"
                                class="text-xs font-mono px-2 py-1 rounded-md border border-slate-200 hover:border-slate-300 text-slate-600 bg-white transition cursor-pointer">
                            &lt; 10 Jt
                        </button>
                        <button type="button"
                                @click.prevent="setPricePreset('10000000', '15000000')"
                                class="text-xs font-mono px-2 py-1 rounded-md border border-slate-200 hover:border-slate-300 text-slate-600 bg-white transition cursor-pointer">
                            10–15 Jt
                        </button>
                        <button type="button"
                                @click.prevent="setPricePreset('15000000', '')"
                                class="text-xs font-mono px-2 py-1 rounded-md border border-slate-200 hover:border-slate-300 text-slate-600 bg-white transition cursor-pointer">
                            &gt; 15 Jt
                        </button>
                    </div>

                    @if ($activeMinPrice && $activeMaxPrice && (float)$activeMinPrice > (float)$activeMaxPrice)
                        <div class="mt-2.5 p-2 bg-amber-50 border border-amber-200 text-amber-800 text-xs rounded-md" role="alert">
                            Perhatian: Nilai harga minimum lebih besar dari maksimum.
                        </div>
                    @endif
                </div>

                {{-- Brand --}}
                <div class="filter-section">
                    <p class="filter-label">Brand</p>
                    <div class="space-y-2 max-h-48 overflow-y-auto no-scrollbar pr-1">
                        <label class="flex items-center gap-2.5 cursor-pointer text-slate-700 hover:text-slate-900">
                            <input type="radio" name="brand" value="" {{ empty($activeBrand) ? 'checked' : '' }}
                                   class="w-4 h-4 accent-slate-900 cursor-pointer">
                            <span class="font-medium text-sm">Semua Merek</span>
                        </label>
                        @foreach ($brands as $brand)
                            <label class="flex items-center gap-2.5 cursor-pointer text-slate-700 hover:text-slate-900">
                                <input type="radio" name="brand" value="{{ $brand }}" {{ $activeBrand === $brand ? 'checked' : '' }}
                                       class="w-4 h-4 accent-slate-900 cursor-pointer">
                                <span class="font-medium text-sm">{{ $brand }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- RAM --}}
                <div class="filter-section">
                    <p class="filter-label">RAM</p>
                    <div class="space-y-2">
                        @foreach (['' => 'Semua', '8' => '8 GB', '16' => '16 GB', '32' => '32 GB'] as $val => $label)
                            <label class="flex items-center gap-2.5 cursor-pointer text-slate-700 hover:text-slate-900">
                                <input type="radio" name="ram" value="{{ $val }}"
                                       {{ ($activeRam ?? '') === $val ? 'checked' : '' }}
                                       class="w-4 h-4 accent-slate-900 cursor-pointer">
                                <span class="font-medium text-sm">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Storage --}}
                <div class="filter-section">
                    <p class="filter-label">Storage (SSD)</p>
                    <div class="space-y-2">
                        @foreach (['' => 'Semua', '256' => '256 GB', '512' => '512 GB', '1024' => '1 TB'] as $val => $label)
                            <label class="flex items-center gap-2.5 cursor-pointer text-slate-700 hover:text-slate-900">
                                <input type="radio" name="storage" value="{{ $val }}"
                                       {{ ($activeStorage ?? '') === $val ? 'checked' : '' }}
                                       class="w-4 h-4 accent-slate-900 cursor-pointer">
                                <span class="font-medium text-sm">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Weight --}}
                <div class="filter-section">
                    <p class="filter-label">Bobot</p>
                    <div class="space-y-2">
                        @foreach (['' => 'Semua', '1.3' => '≤ 1.3 kg', '1.6' => '≤ 1.6 kg', '2.0' => '≤ 2.0 kg'] as $val => $label)
                            <label class="flex items-center gap-2.5 cursor-pointer text-slate-700 hover:text-slate-900">
                                <input type="radio" name="max_weight" value="{{ $val }}"
                                       {{ ($activeMaxWeight ?? '') === $val ? 'checked' : '' }}
                                       class="w-4 h-4 accent-slate-900 cursor-pointer">
                                <span class="font-medium text-sm">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Actions --}}
                <div class="filter-section">
                    <button type="submit"
                            class="w-full bg-slate-900 hover:bg-slate-800 text-white text-sm font-semibold py-2.5 rounded-lg shadow-2xs transition cursor-pointer">
                        Terapkan Filter
                    </button>
                    @if ($hasFilter)
                        <a href="{{ url()->current() }}"
                           @click.prevent="navigateUrl($el.href, false)"
                           class="block text-center mt-2.5 text-xs sm:text-sm text-slate-500 hover:text-slate-900 transition">
                            Reset Filter
                        </a>
                    @endif
                </div>

            </form>
        </aside>

        {{-- ── PRODUCT GRID (Technical Studio Cards) ────────────────────── --}}
        <div id="catalog-results-anchor" class="scroll-mt-20"></div>
        <div id="catalog-dynamic-grid-container" class="flex-1 min-w-0 space-y-4 relative" @click="handleGridClick($event)">
            <div :class="isLoading ? 'opacity-35 pointer-events-none transition-opacity duration-150' : 'transition-opacity duration-150'">

            {{-- Grid header bar --}}
            <div class="flex items-center justify-between text-xs pb-1">
                <span class="font-semibold text-slate-900">
                    {{ $totalCount }} hasil ditemukan
                    @if ($activeBrand) <span class="text-slate-500 font-normal">· Merek: {{ $activeBrand }}</span> @endif
                </span>
                <span class="text-slate-500 hidden sm:inline">Gunakan tombol <strong class="text-slate-900 font-mono">+</strong> untuk membandingkan</span>
            </div>

            {{-- Cards grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                @forelse ($laptops as $laptop)
                    @php
                        $cVals       = $laptop->values->keyBy('criterion.code');
                        $cpuVal      = $cVals->has('C2') ? $cVals['C2']->value . ' / 10' : '—';
                        $ramVal      = $cVals->has('C3') ? $cVals['C3']->value . ' GB'   : '—';
                        $storageVal  = $cVals->has('C4') ? $cVals['C4']->value . ' GB'   : '—';
                        $weightVal   = $cVals->has('C5') ? $cVals['C5']->value . ' kg'   : '—';
                        $batteryVal  = $cVals->has('C6') ? $cVals['C6']->value . ' jam'  : '—';
                        $score       = $getVersusScore($laptop);
                        $isGaming    = (bool) preg_match('/(gaming|tuf|legion|loq|nitro|predator|cyborg|katana|victus|omen|pongo)/i', $laptop->name . ' ' . $laptop->description);
                    @endphp

                    {{-- PRODUCT CARD (Technical Studio Frame) --}}
                    <div class="product-card group">

                        {{-- Card top row: score badge + brand/name + price + compare button --}}
                        <div class="flex items-start gap-3.5 mb-3.5">

                            {{-- Squircle Score Badge --}}
                            <div class="score-badge shrink-0" title="LapFind Score: {{ $score }}/100">
                                <span class="score-num font-mono tabular-nums">{{ $score }}</span>
                                <span class="score-lbl">pts</span>
                            </div>

                            {{-- Name + Brand + Gaming Tag + Price --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-xs font-semibold text-slate-500">{{ $laptop->brand }}</span>
                                    @if ($isGaming)
                                        <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded bg-purple-50 text-purple-700 border border-purple-200">
                                            GAMING
                                        </span>
                                    @endif
                                </div>
                                <a href="{{ route('catalog.show', $laptop) }}"
                                   class="block text-sm sm:text-base font-bold text-slate-900 group-hover:text-blue-600 transition line-clamp-2 leading-snug">
                                    {{ $laptop->name }}
                                </a>
                                <div class="mt-1.5 text-sm sm:text-base font-bold text-slate-900 font-mono tabular-nums">
                                    {{ $laptop->formatted_price }}
                                </div>
                            </div>

                            {{-- Compare toggle button (Instant Feedback & Fitts's Law Target) --}}
                            <button type="button"
                                    @click="$store.compare.toggle({
                                        id:    {{ $laptop->id }},
                                        name:  '{{ addslashes($laptop->name) }}',
                                        brand: '{{ addslashes($laptop->brand) }}',
                                        image: '{{ $laptop->image_url }}',
                                        price: '{{ $laptop->formatted_price }}'
                                    })"
                                    :class="$store.compare.has({{ $laptop->id }}) ? 'compare-btn active' : 'compare-btn'"
                                    :aria-label="$store.compare.has({{ $laptop->id }}) ? 'Hapus {{ addslashes($laptop->name) }} dari komparasi' : 'Tambah {{ addslashes($laptop->name) }} ke komparasi'"
                                    :title="$store.compare.has({{ $laptop->id }}) ? 'Hapus dari perbandingan' : 'Tambah ke perbandingan'">
                                <template x-if="$store.compare.has({{ $laptop->id }})">
                                    <span style="font-size:14px;line-height:1;">✓</span>
                                </template>
                                <template x-if="!$store.compare.has({{ $laptop->id }})">
                                    <span style="font-size:18px;line-height:1;font-weight:300;">+</span>
                                </template>
                            </button>
                        </div>

                        {{-- Product Studio Image Container (16:10 Frame) --}}
                        <a href="{{ route('catalog.show', $laptop) }}"
                           class="aspect-[16/10] mb-3.5 bg-slate-50 border border-slate-200/80 rounded-lg overflow-hidden flex items-center justify-center p-3.5 group-hover:border-slate-300 transition-colors">
                            <img src="{{ $laptop->image_url }}"
                                 alt="{{ $laptop->name }}"
                                 loading="lazy"
                                 class="w-full h-full object-contain transition-transform duration-200 group-hover:scale-[1.03]">
                        </a>

                        {{-- Spec Rows (Text-only, JetBrains Mono Tabular Numbers) --}}
                        <div class="flex-1 space-y-1">
                            <div class="spec-row">
                                <span class="spec-label">Prosesor</span>
                                <span class="spec-val font-mono tabular-nums">{{ $cpuVal }}</span>
                            </div>
                            <div class="spec-row">
                                <span class="spec-label">RAM</span>
                                <span class="spec-val font-mono tabular-nums">{{ $ramVal }}</span>
                            </div>
                            <div class="spec-row">
                                <span class="spec-label">Penyimpanan</span>
                                <span class="spec-val font-mono tabular-nums">{{ $storageVal }} SSD</span>
                            </div>
                            <div class="spec-row">
                                <span class="spec-label">Bobot</span>
                                <span class="spec-val font-mono tabular-nums">{{ $weightVal }}</span>
                            </div>
                            <div class="spec-row">
                                <span class="spec-label">Baterai</span>
                                <span class="spec-val font-mono tabular-nums">{{ $batteryVal }}</span>
                            </div>
                        </div>

                        {{-- Card footer --}}
                        <div class="pt-3.5 mt-3.5 border-t border-slate-200 flex items-center justify-between">
                            <a href="{{ route('catalog.show', $laptop) }}"
                               class="text-xs sm:text-sm font-semibold text-slate-700 hover:text-slate-900 transition flex items-center gap-1">
                                <span>Spesifikasi Lengkap</span>
                                <span class="text-slate-400">&rarr;</span>
                            </a>
                            <span class="text-xs text-slate-400 font-mono">
                                #{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                            </span>
                        </div>
                    </div>

                @empty
                    <div class="col-span-full bg-white border border-slate-200 rounded-lg p-10 text-center shadow-2xs">
                        <div class="w-12 h-12 rounded-md bg-slate-50 border border-slate-200 mx-auto flex items-center justify-center text-slate-400 mb-3">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                <circle cx="11" cy="11" r="7"/>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900 mb-1">Tidak ada laptop yang cocok</h3>
                        <p class="text-xs text-slate-500 mb-4 max-w-xs mx-auto">
                            Coba sesuaikan filter atau kata kunci pencarian Anda.
                        </p>
                        <a href="{{ route('catalog.index') }}"
                           class="inline-block px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-md shadow-2xs transition">
                            Reset Filter
                        </a>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if ($laptops instanceof \Illuminate\Pagination\LengthAwarePaginator && $laptops->hasPages())
                <div class="pt-4">
                    {{ $laptops->links() }}
                </div>
            @endif
            </div>
        </div>
    </div>

    {{-- Floating Live Update Notification (Nielsen Heuristic #1: Visibility of System Status) --}}
    <div x-show="isLoading"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-2"
         class="fixed bottom-6 right-6 z-50 flex items-center gap-2.5 px-4 py-2.5 bg-slate-900/95 dark:bg-slate-800/95 backdrop-blur text-white text-xs font-mono rounded-full shadow-2xl border border-slate-700"
         style="display:none;">
        <svg class="animate-spin h-3.5 w-3.5 text-blue-400 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
        <span>Memperbarui Katalog...</span>
    </div>
</div>

<script>
function catalogLiveFilter() {
    return {
        mobileFilterOpen: false,
        isLoading: false,
        init() {
            window.addEventListener('popstate', () => {
                this.fetchContent(window.location.href, false);
            });
        },
        setPricePreset(min, max) {
            const minInput = document.getElementById('filter-min-price');
            const maxInput = document.getElementById('filter-max-price');
            if (minInput) minInput.value = min;
            if (maxInput) maxInput.value = max;
            this.onFilterChange();
        },
        onFilterChange() {
            const form = document.getElementById('catalog-filter-form');
            if (!form) return;
            const formData = new FormData(form);
            const params = new URLSearchParams();
            for (const [key, value] of formData.entries()) {
                if (value !== '' && value !== null) {
                    params.append(key, value);
                }
            }
            const baseUrl = form.getAttribute('action') || window.location.pathname;
            const targetUrl = baseUrl + (params.toString() ? '?' + params.toString() : '');
            this.fetchContent(targetUrl, true, false);
        },
        handleLinkClick(e) {
            const link = e.target.closest('a');
            if (link && link.href && !link.target && !link.hasAttribute('download')) {
                e.preventDefault();
                this.navigateUrl(link.href, false);
            }
        },
        handleGridClick(e) {
            const paginationLink = e.target.closest('nav[role=navigation] a, .pagination a');
            if (paginationLink && paginationLink.href) {
                e.preventDefault();
                this.navigateUrl(paginationLink.href, true);
            }
        },
        navigateUrl(url, scrollToAnchor = false) {
            if (!url) return;
            this.fetchContent(url, true, scrollToAnchor);
        },
        async fetchContent(url, pushState = true, scrollToAnchor = false) {
            this.isLoading = true;
            try {
                const response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                if (!response.ok) throw new Error('HTTP ' + response.status);
                const html = await response.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                const swapIds = [
                    'catalog-dynamic-counter',
                    'catalog-dynamic-pills',
                    'catalog-dynamic-active-filters',
                    'catalog-dynamic-sidebar',
                    'catalog-dynamic-grid-container'
                ];

                for (let i = 0; i < swapIds.length; i++) {
                    const id = swapIds[i];
                    const newEl = doc.getElementById(id);
                    const currentEl = document.getElementById(id);
                    if (newEl && currentEl) {
                        currentEl.innerHTML = newEl.innerHTML;
                    }
                }

                if (pushState) {
                    window.history.pushState({}, '', url);
                }

                if (scrollToAnchor) {
                    const anchor = document.getElementById('catalog-results-anchor');
                    if (anchor) {
                        anchor.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }
            } catch (err) {
                console.warn('Live filter fallback to standard reload:', err);
                window.location.href = url;
            } finally {
                this.isLoading = false;
            }
        }
    };
}
</script>
