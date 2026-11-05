@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-line pb-4">
        <div>
            <div class="text-[11px] font-mono uppercase tracking-wider text-accent font-semibold mb-1">
                Data Master AHP
            </div>
            <h1 class="text-xl sm:text-2xl font-semibold tracking-tight text-ink">Kriteria Evaluasi Laptop</h1>
            <p class="text-xs text-ink-muted mt-1">Daftar parameter yang digunakan dalam pembobotan matriks AHP dan perankingan TOPSIS.</p>
        </div>
        <a href="{{ route('admin.criteria.create') }}"
           class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-accent hover:bg-accent-hover text-white text-xs font-medium rounded transition shadow-xs self-start sm:self-auto">
            <x-icon name="plus" class="w-3.5 h-3.5 text-white" />
            <span>Tambah Kriteria Baru</span>
        </a>
    </div>

    <div class="bg-surface border border-line rounded overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-surface-subtle border-b border-line text-ink-muted font-medium">
                    <tr>
                        <th class="py-3 px-4 w-16 font-mono">Kode</th>
                        <th class="py-3 px-4 min-w-[200px]">Nama Kriteria</th>
                        <th class="py-3 px-4">Tipe Kriteria</th>
                        <th class="py-3 px-4 font-mono">Satuan</th>
                        <th class="py-3 px-4">Deskripsi / Penjelasan</th>
                        <th class="py-3 px-4 text-center">Data Alternatif</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
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
                    @forelse ($criteria as $crit)
                        <tr class="hover:bg-surface-subtle/50 transition">
                            <td class="py-3 px-4 font-mono font-semibold text-ink">{{ $crit->code }}</td>
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2">
                                    <span class="p-1 rounded bg-surface-subtle border border-line text-accent shrink-0">
                                        <x-icon :name="$critIcons[$crit->code] ?? 'filter'" class="w-3.5 h-3.5" />
                                    </span>
                                    <span class="font-medium text-ink">{{ $crit->name }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <x-badge :type="$crit->type">
                                    {{ $crit->type === 'cost' ? 'Cost (Biaya)' : 'Benefit (Keuntungan)' }}
                                </x-badge>
                            </td>
                            <td class="py-3 px-4 font-mono text-ink-muted">{{ $crit->unit ?? '-' }}</td>
                            <td class="py-3 px-4 text-ink-muted max-w-xs truncate">{{ $crit->description ?? '-' }}</td>
                            <td class="py-3 px-4 text-center font-mono text-ink-muted">
                                {{ $crit->alternative_values_count }} terisi
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('admin.criteria.edit', $crit) }}"
                                       class="inline-flex items-center gap-1 px-2.5 py-1 bg-surface-subtle hover:bg-line border border-line rounded text-[11px] font-medium text-ink hover:text-accent transition">
                                        <x-icon name="edit" class="w-3 h-3 text-ink-muted" />
                                        <span>Ubah</span>
                                    </a>
                                    <form action="{{ route('admin.criteria.destroy', $crit) }}"
                                          method="POST"
                                          class="inline"
                                          onsubmit="return confirm('Hapus kriteria {{ $crit->code }} ({{ $crit->name }})? Menghapus kriteria ini akan menghapus nilai kriteria terkait pada seluruh laptop.');">
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
                            <td colspan="7" class="py-8 text-center text-ink-muted">
                                Belum ada kriteria yang terdaftar. Silakan tambahkan kriteria evaluasi baru.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
