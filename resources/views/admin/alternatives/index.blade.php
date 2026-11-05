@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-line pb-4">
        <div>
            <h1 class="text-xl font-semibold tracking-tight text-ink">Alternatif Laptop Mahasiswa</h1>
            <p class="text-xs text-ink-muted mt-1">Daftar alternatif laptop yang dievaluasi dalam perankingan TOPSIS.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.alternatives.export', request()->query()) }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-surface hover:bg-surface-subtle text-ink border border-line text-xs font-medium rounded transition shadow-2xs">
                <x-icon name="download" class="w-3.5 h-3.5 text-ink-muted" />
                <span>Ekspor CSV</span>
            </a>
            <a href="{{ route('admin.values.index') }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-surface hover:bg-surface-subtle text-ink border border-line text-xs font-medium rounded transition shadow-2xs">
                <x-icon name="scale" class="w-3.5 h-3.5 text-accent" />
                <span>Matriks Nilai Lengkap</span>
            </a>
            <a href="{{ route('admin.alternatives.create') }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-accent hover:bg-accent-hover text-white text-xs font-medium rounded transition shadow-xs">
                <x-icon name="plus" class="w-3.5 h-3.5 text-white" />
                <span>Tambah Laptop</span>
            </a>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <form method="GET" action="{{ route('admin.alternatives.index') }}" class="flex flex-wrap items-center gap-3 bg-surface p-3 border border-line rounded shadow-2xs">
        <div class="flex-1 min-w-[200px]">
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Cari berdasarkan model atau merek laptop..."
                   class="w-full text-xs rounded border-line focus:border-accent focus:ring-accent bg-canvas">
        </div>
        <div>
            <select name="brand"
                    class="text-xs rounded border-line focus:border-accent focus:ring-accent bg-canvas"
                    onchange="this.form.submit()">
                <option value="">Semua Merek</option>
                @foreach ($brands as $b)
                    <option value="{{ $b }}" {{ request('brand') === $b ? 'selected' : '' }}>{{ $b }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-surface-subtle hover:bg-line border border-line text-xs font-medium text-ink rounded transition">
            <x-icon name="filter" class="w-3.5 h-3.5 text-ink-muted" />
            <span>Filter</span>
        </button>
        @if (request()->hasAny(['search', 'brand']))
            <a href="{{ route('admin.alternatives.index') }}" class="text-xs text-ink-muted hover:text-ink underline">
                Reset
            </a>
        @endif
    </form>

    <!-- Table of Laptops -->
    <div class="bg-surface border border-line rounded overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-surface-subtle border-b border-line text-ink-muted font-medium">
                    <tr>
                        <th class="py-3 px-4 w-12 font-mono">No</th>
                        <th class="py-3 px-4 min-w-[220px]">Nama & Model Laptop</th>
                        <th class="py-3 px-4">Merek</th>
                        <th class="py-3 px-4 text-right">Harga Resmi</th>
                        @foreach ($criteria as $crit)
                            <th class="py-3 px-3 text-right font-mono text-[11px]" title="{{ $crit->name }}">
                                {{ $crit->code }}
                            </th>
                        @endforeach
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($laptops as $index => $laptop)
                        @php
                            $valuesMap = $laptop->values->pluck('value', 'criteria_id')->toArray();
                        @endphp
                        <tr class="hover:bg-surface-subtle/50 transition">
                            <td class="py-3 px-4 font-mono text-ink-muted">{{ $laptops->firstItem() + $index }}</td>
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-10 bg-surface-subtle border border-line rounded flex items-center justify-center p-1 shrink-0 overflow-hidden">
                                        <img src="{{ $laptop->image_url }}" alt="{{ $laptop->name }}" class="max-h-full max-w-full object-contain">
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.alternatives.show', $laptop) }}" class="font-medium text-ink hover:text-accent">
                                            {{ $laptop->name }}
                                        </a>
                                        @if ($laptop->description)
                                            <p class="text-[11px] text-ink-muted truncate max-w-xs mt-0.5">{{ $laptop->description }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-ink-muted">{{ $laptop->brand }}</td>
                            <td class="py-3 px-4 text-right font-mono font-medium text-ink">{{ $laptop->formatted_price }}</td>
                            @foreach ($criteria as $crit)
                                <td class="py-3 px-3 text-right font-mono text-ink">
                                    {{ isset($valuesMap[$crit->id]) ? (float) $valuesMap[$crit->id] : '-' }}
                                </td>
                            @endforeach
                            <td class="py-3 px-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('admin.alternatives.edit', $laptop) }}"
                                       class="inline-flex items-center gap-1 px-2.5 py-1 bg-surface-subtle hover:bg-line border border-line rounded text-[11px] font-medium text-ink hover:text-accent transition">
                                        <x-icon name="edit" class="w-3 h-3 text-ink-muted" />
                                        <span>Ubah</span>
                                    </a>
                                    <form action="{{ route('admin.alternatives.destroy', $laptop) }}"
                                          method="POST"
                                          class="inline"
                                          onsubmit="return confirm('Hapus laptop {{ $laptop->name }}? Tindakan ini tidak dapat dibatalkan.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded text-[11px] font-medium text-rose-700 hover:text-rose-900 transition">
                                            <x-icon name="trash" class="w-3 h-3 text-rose-600" />
                                            <span>Hapus</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 5 + $criteria->count() }}" class="py-8 text-center text-ink-muted">
                                Tidak ada data laptop yang cocok dengan kriteria pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($laptops->hasPages())
            <div class="px-4 py-3 border-t border-line bg-surface">
                {{ $laptops->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
