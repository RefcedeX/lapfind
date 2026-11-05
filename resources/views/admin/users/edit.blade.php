@extends('layouts.admin')

@section('content')
<div class="max-w-2xl space-y-6">
    <div class="border-b border-line pb-4">
        <div class="flex items-center gap-2 text-xs text-ink-muted mb-1">
            <a href="{{ route('admin.users.index') }}" class="hover:text-ink">Manajemen Pengguna</a>
            <span>/</span>
            <span>Ubah Pengguna</span>
        </div>
        <h1 class="text-xl font-semibold tracking-tight text-ink">Ubah Data: {{ $user->name }}</h1>
        <p class="text-xs text-ink-muted mt-1">Perbarui profil atau peran pengguna {{ $user->email }}.</p>
    </div>

    <form action="{{ route('admin.users.update', $user) }}" method="POST" class="bg-surface border border-line rounded p-6 space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label for="name" class="block text-xs font-medium text-ink mb-1">Nama Lengkap <span class="text-rose-600">*</span></label>
            <input type="text"
                   name="name"
                   id="name"
                   value="{{ old('name', $user->name) }}"
                   required
                   class="w-full text-xs rounded border-line focus:border-accent focus:ring-accent bg-canvas">
        </div>

        <div>
            <label for="email" class="block text-xs font-medium text-ink mb-1">Alamat Email <span class="text-rose-600">*</span></label>
            <input type="email"
                   name="email"
                   id="email"
                   value="{{ old('email', $user->email) }}"
                   required
                   class="w-full text-xs font-mono rounded border-line focus:border-accent focus:ring-accent bg-canvas">
        </div>

        <div>
            <label for="role" class="block text-xs font-medium text-ink mb-1">Peran Akun (Role) <span class="text-rose-600">*</span></label>
            <select name="role"
                    id="role"
                    required
                    class="w-full text-xs rounded border-line focus:border-accent focus:ring-accent bg-canvas">
                <option value="user" {{ old('role', $user->role) === 'user' ? 'selected' : '' }}>Mahasiswa (User Biasa)</option>
                <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Administrator (Akses Penuh Kelola SPK)</option>
            </select>
            @if (auth()->id() === $user->id)
                <span class="text-[11px] text-amber-700 mt-1 block">Perhatian: Mengubah peran akun Anda sendiri dapat mencabut akses ke panel admin.</span>
            @endif
        </div>

        <div class="pt-2 border-t border-line">
            <span class="text-xs font-semibold text-ink block mb-1">Ubah Kata Sandi (Opsional)</span>
            <span class="text-[11px] text-ink-muted block mb-3">Kosongkan bagian ini jika tidak ingin mengubah kata sandi pengguna.</span>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-xs font-medium text-ink mb-1">Kata Sandi Baru</label>
                    <input type="password"
                           name="password"
                           id="password"
                           placeholder="Minimal 8 karakter"
                           class="w-full text-xs font-mono rounded border-line focus:border-accent focus:ring-accent bg-canvas">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-medium text-ink mb-1">Ulangi Kata Sandi Baru</label>
                    <input type="password"
                           name="password_confirmation"
                           id="password_confirmation"
                           placeholder="Konfirmasi kata sandi"
                           class="w-full text-xs font-mono rounded border-line focus:border-accent focus:ring-accent bg-canvas">
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-line flex items-center justify-end gap-3">
            <a href="{{ route('admin.users.index') }}"
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
