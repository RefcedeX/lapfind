@extends('layouts.admin')

@section('content')
<div class="max-w-3xl space-y-6">
    <div class="flex items-center justify-between border-b border-line pb-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-ink-muted mb-1">
                <a href="{{ route('admin.alternatives.index') }}" class="hover:text-ink">Alternatif Laptop</a>
                <span>/</span>
                <span>Detail Laptop</span>
            </div>
            <h1 class="text-xl font-semibold tracking-tight text-ink">{{ $alternative->name }}</h1>
            <p class="text-xs text-ink-muted mt-1">{{ $alternative->brand }} • {{ $alternative->formatted_price }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.alternatives.edit', $alternative) }}"
               class="px-3 py-1.5 bg-accent hover:bg-accent-hover text-white text-xs font-medium rounded transition">
                Ubah Laptop
            </a>
            <a href="{{ route('admin.alternatives.index') }}"
               class="px-3 py-1.5 bg-surface hover:bg-surface-subtle text-ink border border-line text-xs font-medium rounded transition">
                Kembali
            </a>
        </div>
    </div>

    <!-- Overview Card -->
    <div class="bg-surface border border-line rounded p-6 space-y-6">
        @if ($alternative->image)
            <div>
                <img src="{{ asset('storage/' . $alternative->image) }}" alt="{{ $alternative->name }}" class="max-h-64 rounded border border-line object-contain bg-canvas p-2">
            </div>
        @endif

        <div>
            <h2 class="text-xs font-semibold uppercase tracking-wider text-ink mb-2">Deskripsi Spesifikasi</h2>
            <p class="text-xs text-ink leading-relaxed">{{ $alternative->description ?: 'Tidak ada deskripsi tambahan.' }}</p>
        </div>

        <div>
            <h2 class="text-xs font-semibold uppercase tracking-wider text-ink mb-3 pb-2 border-b border-line">
                Nilai Kriteria Matriks Keputusan (X)
            </h2>
            @php
                $valuesMap = $alternative->values->pluck('value', 'criteria_id')->toArray();
            @endphp
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach ($criteria as $crit)
                    <div class="p-3 bg-surface-subtle border border-line rounded flex items-center justify-between">
                        <div>
                            <span class="font-mono text-xs font-semibold text-ink">{{ $crit->code }}</span>
                            <span class="text-xs text-ink ml-1">{{ $crit->name }}</span>
                            <div class="text-[11px] text-ink-muted">{{ ucfirst($crit->type) }}</div>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-semibold font-mono text-accent">
                                {{ isset($valuesMap[$crit->id]) ? (float) $valuesMap[$crit->id] : '-' }}
                            </span>
                            @if ($crit->unit)
                                <span class="text-[11px] font-mono text-ink-muted ml-0.5">{{ $crit->unit }}</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
