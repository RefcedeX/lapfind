@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-line pb-4">
        <div>
            <div class="text-[11px] font-mono uppercase tracking-wider text-accent font-semibold mb-1">
                Data Master TOPSIS
            </div>
            <h1 class="text-xl sm:text-2xl font-semibold tracking-tight text-ink">Matriks Keputusan (X)</h1>
            <p class="text-xs text-ink-muted mt-1">
                Tabel nilai seluruh alternatif laptop terhadap kriteria evaluasi yang menjadi input dasar normalisasi TOPSIS.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.values.export') }}"
               class="inline-flex items-center gap-1.5 px-3 py-1 bg-surface hover:bg-surface-subtle text-ink border border-line rounded text-xs font-medium transition shadow-2xs">
                <x-icon name="download" class="w-3.5 h-3.5 text-ink-muted" />
                <span>Ekspor CSV</span>
            </a>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-surface border border-line rounded text-xs text-ink-muted font-mono shadow-2xs">
                <x-icon name="scale" class="w-3.5 h-3.5 text-accent" />
                <span>{{ $alternatives->count() }} Alternatif &times; {{ $criteria->count() }} Kriteria</span>
            </span>
        </div>
    </div>

    <form action="{{ route('admin.values.update') }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div class="bg-surface border border-line rounded overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-surface-subtle border-b border-line text-ink font-medium">
                        <tr>
                            <th class="py-3 px-3 w-10 text-center font-mono text-ink-muted">No</th>
                            <th class="py-3 px-4 min-w-[220px]">Alternatif Laptop</th>
                            @php
                                $critIcons = [
                                    'C1' => 'price',
                                    'C2' => 'cpu',
                                    'C3' => 'ram',
                                    'C4' => 'storage',
                                    'C5' => 'weight',
                                    'C6' => 'battery',
                                ];
                            @endphp
                            @foreach ($criteria as $crit)
                                <th class="py-3 px-3 min-w-[130px] text-right">
                                    <div class="flex items-center justify-end gap-1.5 font-mono font-semibold">
                                        <x-icon :name="$critIcons[$crit->code] ?? 'filter'" class="w-3.5 h-3.5 text-accent" />
                                        <span>{{ $crit->code }}</span>
                                    </div>
                                    <div class="text-[11px] text-ink-muted font-normal truncate max-w-[120px]">{{ $crit->name }}</div>
                                    <div class="text-[10px] uppercase tracking-wider text-ink-muted font-mono">
                                        {{ $crit->type }} @if ($crit->unit)• {{ $crit->unit }}@endif
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @foreach ($alternatives as $index => $alt)
                            <tr class="hover:bg-surface-subtle/50 transition">
                                <td class="py-2.5 px-3 text-center font-mono text-ink-muted">{{ $index + 1 }}</td>
                                <td class="py-2.5 px-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-9 h-7 bg-surface-subtle border border-line rounded flex items-center justify-center p-0.5 shrink-0 overflow-hidden">
                                            <img src="{{ $alt->image_url }}" alt="{{ $alt->name }}" class="max-h-full max-w-full object-contain">
                                        </div>
                                        <div>
                                            <span class="font-medium text-ink block leading-snug">{{ $alt->name }}</span>
                                            <span class="text-[11px] text-ink-muted block">{{ $alt->brand }} • {{ $alt->formatted_price }}</span>
                                        </div>
                                    </div>
                                </td>
                                @foreach ($criteria as $crit)
                                    @php
                                        $val = $matrix[$alt->id][$crit->id] ?? '';
                                    @endphp
                                    <td class="py-2 px-3 text-right">
                                        <input type="number"
                                               step="any"
                                               name="values[{{ $alt->id }}][{{ $crit->id }}]"
                                               value="{{ old("values.{$alt->id}.{$crit->id}", $val) }}"
                                               required
                                               class="w-full text-right text-xs font-mono py-1 px-2 rounded border-line focus:border-accent focus:ring-accent bg-canvas">
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 bg-surface-subtle border-t border-line flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <span class="text-xs text-ink-muted">
                    Pastikan seluruh nilai numerik telah sesuai dengan spesifikasi sebelum memperbarui.
                </span>
                <button type="submit"
                        class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-accent hover:bg-accent-hover text-white text-xs font-medium rounded transition shadow-xs">
                    <x-icon name="check" class="w-3.5 h-3.5 text-white" />
                    <span>Simpan Seluruh Perubahan Matriks</span>
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
