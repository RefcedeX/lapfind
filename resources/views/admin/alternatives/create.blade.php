@extends('layouts.admin')

@section('content')
<div class="max-w-3xl space-y-6">
    <div class="border-b border-line pb-4">
        <div class="flex items-center gap-2 text-xs text-ink-muted mb-1">
            <a href="{{ route('admin.alternatives.index') }}" class="hover:text-ink">Alternatif Laptop</a>
            <span>/</span>
            <span>Tambah Laptop</span>
        </div>
        <h1 class="text-xl font-semibold tracking-tight text-ink">Tambah Laptop Baru</h1>
        <p class="text-xs text-ink-muted mt-1">Masukkan spesifikasi dan nilai atribut kriteria laptop untuk matriks SPK.</p>
    </div>

    <form action="{{ route('admin.alternatives.store') }}" method="POST" enctype="multipart/form-data" class="bg-surface border border-line rounded p-6 space-y-6">
        @csrf

        <!-- Informasi Dasar Laptop -->
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
                           value="{{ old('name') }}"
                           placeholder="misal: ASUS Zenbook 14 OLED UX3405"
                           required
                           class="w-full text-xs rounded border-line focus:border-accent focus:ring-accent bg-canvas">
                </div>

                <div>
                    <label for="brand" class="block text-xs font-medium text-ink mb-1">Merek Vendor <span class="text-rose-600">*</span></label>
                    <input type="text"
                           name="brand"
                           id="brand"
                           value="{{ old('brand') }}"
                           placeholder="misal: ASUS, Lenovo, Apple, HP, Acer"
                           required
                           class="w-full text-xs rounded border-line focus:border-accent focus:ring-accent bg-canvas">
                </div>

                <div>
                    <label for="price" class="block text-xs font-medium text-ink mb-1">Harga Resmi (Rupiah) <span class="text-rose-600">*</span></label>
                    <input type="number"
                           step="1000"
                           name="price"
                           id="price"
                           value="{{ old('price') }}"
                           placeholder="misal: 11999000"
                           required
                           class="w-full text-xs font-mono rounded border-line focus:border-accent focus:ring-accent bg-canvas">
                </div>

                <div class="sm:col-span-2">
                    <label for="image" class="block text-xs font-medium text-ink mb-1">Foto / Gambar Laptop (Opsional)</label>
                    <input type="file"
                           name="image"
                           id="image"
                           accept="image/*"
                           class="w-full text-xs text-ink-muted file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-medium file:bg-surface-subtle file:text-ink hover:file:bg-line">
                    <span class="text-[11px] text-ink-muted mt-0.5 block">Format: JPG, PNG, WEBP. Maksimal 2MB.</span>
                </div>

                <div class="sm:col-span-2">
                    <label for="description" class="block text-xs font-medium text-ink mb-1">Deskripsi Singkat & Catatan Khusus</label>
                    <textarea name="description"
                              id="description"
                              rows="3"
                              placeholder="Kelebihan utama, target pengguna mahasiswa, spesifikasi layar atau port..."
                              class="w-full text-xs rounded border-line focus:border-accent focus:ring-accent bg-canvas">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Nilai Kriteria untuk SPK -->
        <div>
            <h2 class="text-xs font-semibold uppercase tracking-wider text-ink mb-1 pb-2 border-b border-line">
                Nilai Matriks Kriteria (Input TOPSIS)
            </h2>
            <p class="text-[11px] text-ink-muted mb-4">Isi nilai numerik untuk masing-masing kriteria evaluasi sistem.</p>

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
                                   value="{{ old("values.{$crit->id}", ($crit->code === 'C1' ? old('price') : '')) }}"
                                   placeholder="Nilai angka numerik..."
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
                Simpan Alternatif Laptop
            </button>
        </div>
    </form>
</div>
@endsection
