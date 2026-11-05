@extends('layouts.admin')

@section('content')
<div class="max-w-2xl space-y-6">
    <div class="border-b border-line pb-4">
        <div class="flex items-center gap-2 text-xs text-ink-muted mb-1">
            <a href="{{ route('admin.users.index') }}" class="hover:text-ink">Manajemen Pengguna</a>
            <span>/</span>
            <span>Tambah Pengguna</span>
        </div>
        <h1 class="text-xl font-semibold tracking-tight text-ink">Tambah Pengguna Baru</h1>
        <p class="text-xs text-ink-muted mt-1">Daftarkan akun administrator atau mahasiswa baru ke dalam sistem.</p>
    </div>

    <form action="{{ route('admin.users.store') }}" method="POST" class="bg-surface border border-line rounded p-6 space-y-5">
        @csrf

        <div>
            <label for="name" class="block text-xs font-medium text-ink mb-1">Nama Lengkap <span class="text-rose-600">*</span></label>
            <input type="text"
                   name="name"
                   id="name"
                   value="{{ old('name') }}"
                   placeholder="misal: Siti Nurhaliza"
                   required
                   class="w-full text-xs rounded border-line focus:border-accent focus:ring-accent bg-canvas">
        </div>

        <div>
            <label for="email" class="block text-xs font-medium text-ink mb-1">Alamat Email <span class="text-rose-600">*</span></label>
            <input type="email"
                   name="email"
                   id="email"
                   value="{{ old('email') }}"
                   placeholder="misal: sitinur@kampus.ac.id"
                   required
                   class="w-full text-xs font-mono rounded border-line focus:border-accent focus:ring-accent bg-canvas">
        </div>

        <div>
            <label for="role" class="block text-xs font-medium text-ink mb-1">Peran Akun (Role) <span class="text-rose-600">*</span></label>
            <select name="role"
                    id="role"
                    required
                    class="w-full text-xs rounded border-line focus:border-accent focus:ring-accent bg-canvas">
                <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>Mahasiswa (User Biasa)</option>
                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrator (Akses Penuh Kelola SPK)</option>
            </select>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
            <div>
                <label for="password" class="block text-xs font-medium text-ink mb-1">Kata Sandi <span class="text-rose-600">*</span></label>
                <input type="password"
                       name="password"
                       id="password"
                       required
                       placeholder="Minimal 8 karakter"
                       class="w-full text-xs font-mono rounded border-line focus:border-accent focus:ring-accent bg-canvas">
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-medium text-ink mb-1">Ulangi Kata Sandi <span class="text-rose-600">*</span></label>
                <input type="password"
                       name="password_confirmation"
                       id="password_confirmation"
                       required
                       placeholder="Konfirmasi sandi"
                       class="w-full text-xs font-mono rounded border-line focus:border-accent focus:ring-accent bg-canvas">
            </div>
        </div>

        <div class="pt-4 border-t border-line flex items-center justify-end gap-3">
            <a href="{{ route('admin.users.index') }}"
               class="px-3 py-1.5 text-xs text-ink-muted hover:text-ink font-medium">
                Batal
            </a>
            <button type="submit"
                    class="px-4 py-1.5 bg-accent hover:bg-accent-hover text-white text-xs font-medium rounded transition">
                Simpan Pengguna
            </button>
        </div>
    </form>
</div>
@endsection
