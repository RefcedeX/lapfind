@extends('layouts.admin')

@section('content')
<div class="max-w-3xl space-y-6">
    <div class="border-b border-line pb-4">
        <div class="flex items-center gap-2 text-xs text-ink-muted mb-1">
            <a href="{{ route('admin.alternatives.index') }}" class="hover:text-ink">Alternatif Laptop</a>
            <span>/</span>
            <span>Ubah Data</span>
        </div>
        <h1 class="text-xl font-semibold tracking-tight text-ink">Ubah Data: {{ $alternative->name }}</h1>
        <p class="text-xs text-ink-muted mt-1">Perbarui spesifikasi dan matriks nilai kriteria laptop.</p>
    </div>

    <form action="{{ route('admin.alternatives.update', $alternative) }}" method="POST" enctype="multipart/form-data" class="bg-surface border border-line rounded p-6 space-y-6">
        @csrf
        @method('PUT')

        <!-- Informasi Utama -->
        <div>
            <h2 class="text-xs font-semibold uppercase tracking-wider text-ink mb-3 pb-2 border-b border-line">
                Informasi Utama
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label for="name" class="block text-xs font-medium text-ink mb-1">Nama / Model Lengkap Laptop <span class="text-rose-600">*</span></label>
                    <input type="text"
                           name="name"
                           id="name"
                           value="{{ old('name', $alternative->name) }}"
                           required
                           class="w-full text-xs rounded border-line focus:border-accent focus:ring-accent bg-canvas">
                </div>

                <div>
                    <label for="brand" class="block text-xs font-medium text-ink mb-1">Merek Vendor <span class="text-rose-600">*</span></label>
                    <input type="text"
                           name="brand"
                           id="brand"
                           value="{{ old('brand', $alternative->brand) }}"
                           required
                           class="w-full text-xs rounded border-line focus:border-accent focus:ring-accent bg-canvas">
                </div>

                <div>
                    <label for="price" class="block text-xs font-medium text-ink mb-1">Harga Resmi (Rupiah) <span class="text-rose-600">*</span></label>
                    <input type="number"
                           step="1000"
                           name="price"
                           id="price"
                           value="{{ old('price', (int) $alternative->price) }}"
                           required
                           class="w-full text-xs font-mono rounded border-line focus:border-accent focus:ring-accent bg-canvas">
                </div>

                <div class="sm:col-span-2">
                    <label for="image" class="block text-xs font-medium text-ink mb-1">Foto / Gambar Laptop</label>
                    @if ($alternative->image)
                        <div class="mb-2 flex items-center gap-3 p-2 bg-surface-subtle border border-line rounded">
                            <img src="{{ asset('storage/' . $alternative->image) }}" alt="{{ $alternative->name }}" class="w-12 h-12 object-cover rounded border border-line">
                            <span class="text-xs text-ink-muted">Gambar saat ini terpasang. Pilih file baru untuk mengganti.</span>
                        </div>
                    @endif
                    <input type="file"
                           name="image"
                           id="image"
                           accept="image/*"
                           class="w-full text-xs text-ink-muted file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-medium file:bg-surface-subtle file:text-ink hover:file:bg-line">
                </div>

                <div class="sm:col-span-2">
                    <label for="description" class="block text-xs font-medium text-ink mb-1">Deskripsi Singkat & Catatan</label>
                    <textarea name="description"
                              id="description"
                              rows="3"
                              class="w-full text-xs rounded border-line focus:border-accent focus:ring-accent bg-canvas">{{ old('description', $alternative->description) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Nilai Kriteria untuk SPK -->
        <div>
            <h2 class="text-xs font-semibold uppercase tracking-wider text-ink mb-1 pb-2 border-b border-line">
                Nilai Matriks Kriteria (Input TOPSIS)
            </h2>
            <p class="text-[11px] text-ink-muted mb-4">Nilai numerik terukur yang digunakan dalam perhitungan normalisasi TOPSIS.</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach ($criteria as $crit)
                    <div class="p-3 bg-surface-subtle/60 border border-line rounded">
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-mono text-xs font-semibold text-ink">{{ $crit->code }} — {{ $crit->name }}</span>
                            <x-badge :type="$crit->type">{{ ucfirst($crit->type) }}</x-badge>
                        </div>
                        <span class="text-[11px] text-ink-muted block mb-2">{{ $crit->description ?? "Satuan: {$crit->unit}" }}</span>
                        <div class="flex items-center gap-2">
                            <input type="number"
                                   step="any"
                                   name="values[{{ $crit->id }}]"
                                   value="{{ old("values.{$crit->id}", $valuesMap[$crit->id] ?? '') }}"
                                   placeholder="Nilai angka..."
                                   class="w-full text-xs font-mono rounded border-line focus:border-accent focus:ring-accent bg-canvas">
                            @if ($crit->unit)
                                <span class="text-xs font-mono text-ink-muted shrink-0">{{ $crit->unit }}</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="pt-4 border-t border-line flex items-center justify-end gap-3">
            <a href="{{ route('admin.alternatives.index') }}"
               class="px-3 py-1.5 text-xs text-ink-muted hover:text-ink font-medium">
                Batal
            </a>
            <button type="submit"
                    class="px-4 py-1.5 bg-accent hover:bg-accent-hover text-white text-xs font-medium rounded transition">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
