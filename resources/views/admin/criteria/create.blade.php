@extends('layouts.admin')

@section('content')
<div class="max-w-2xl space-y-6">
    <div class="border-b border-line pb-4">
        <div class="flex items-center gap-2 text-xs text-ink-muted mb-1">
            <a href="{{ route('admin.criteria.index') }}" class="hover:text-ink">Kriteria Evaluasi</a>
            <span>/</span>
            <span>Tambah Baru</span>
        </div>
        <h1 class="text-xl font-semibold tracking-tight text-ink">Tambah Kriteria Baru</h1>
        <p class="text-xs text-ink-muted mt-1">Definisikan parameter kriteria baru untuk matriks evaluasi SPK.</p>
    </div>

    <form action="{{ route('admin.criteria.store') }}" method="POST" class="bg-surface border border-line rounded p-6 space-y-5">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="code" class="block text-xs font-medium text-ink mb-1">Kode Kriteria <span class="text-rose-600">*</span></label>
                <input type="text"
                       name="code"
                       id="code"
                       value="{{ old('code') }}"
                       placeholder="misal: C7"
                       required
                       class="w-full text-xs font-mono rounded border-line focus:border-accent focus:ring-accent bg-canvas">
                <span class="text-[11px] text-ink-muted mt-0.5 block">Format standar: C1, C2, C3, dst.</span>
            </div>

            <div>
                <label for="type" class="block text-xs font-medium text-ink mb-1">Sifat / Tipe Kriteria <span class="text-rose-600">*</span></label>
                <select name="type"
                        id="type"
                        required
                        class="w-full text-xs rounded border-line focus:border-accent focus:ring-accent bg-canvas">
                    <option value="">Pilih sifat kriteria...</option>
                    <option value="benefit" {{ old('type') === 'benefit' ? 'selected' : '' }}>Benefit (Semakin tinggi semakin baik)</option>
                    <option value="cost" {{ old('type') === 'cost' ? 'selected' : '' }}>Cost (Semakin rendah semakin hemat/baik)</option>
                </select>
            </div>
        </div>

        <div>
            <label for="name" class="block text-xs font-medium text-ink mb-1">Nama Kriteria <span class="text-rose-600">*</span></label>
            <input type="text"
                   name="name"
                   id="name"
                   value="{{ old('name') }}"
                   placeholder="misal: Resolusi Layar atau Kualitas Audio"
                   required
                   class="w-full text-xs rounded border-line focus:border-accent focus:ring-accent bg-canvas">
        </div>

        <div>
            <label for="unit" class="block text-xs font-medium text-ink mb-1">Satuan Nilai</label>
            <input type="text"
                   name="unit"
                   id="unit"
                   value="{{ old('unit') }}"
                   placeholder="misal: Rp, GB, kg, jam, skor 1-10"
                   class="w-full text-xs font-mono rounded border-line focus:border-accent focus:ring-accent bg-canvas">
        </div>

        <div>
            <label for="description" class="block text-xs font-medium text-ink mb-1">Deskripsi / Penjelasan Kriteria</label>
            <textarea name="description"
                      id="description"
                      rows="3"
                      placeholder="Jelaskan bagaimana kriteria ini dinilai dan relevansinya bagi pemilihan laptop mahasiswa..."
                      class="w-full text-xs rounded border-line focus:border-accent focus:ring-accent bg-canvas">{{ old('description') }}</textarea>
        </div>

        <div class="pt-4 border-t border-line flex items-center justify-end gap-3">
            <a href="{{ route('admin.criteria.index') }}"
               class="px-3 py-1.5 text-xs text-ink-muted hover:text-ink font-medium">
                Batal
            </a>
            <button type="submit"
                    class="px-4 py-1.5 bg-accent hover:bg-accent-hover text-white text-xs font-medium rounded transition">
                Simpan Kriteria
            </button>
        </div>
    </form>
</div>
@endsection
