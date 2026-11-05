<x-guest-layout>
    <div class="mb-5">
        <h2 class="text-base font-semibold text-ink">Pendaftaran Akun Baru</h2>
        <p class="text-xs text-ink-muted mt-0.5">Buat akun untuk menyimpan riwayat evaluasi preferensi laptop Anda.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block text-xs font-medium text-ink mb-1">Nama Lengkap</label>
            <input id="name"
                   class="w-full text-xs rounded border-line focus:border-accent focus:ring-accent bg-canvas"
                   type="text"
                   name="name"
                   value="{{ old('name') }}"
                   required
                   autofocus
                   autocomplete="name"
                   placeholder="Rijal Muhammad" />
            <x-input-error :messages="$errors->get('name')" class="mt-1.5 text-xs text-rose-600" />
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-medium text-ink mb-1">Alamat Email</label>
            <input id="email"
                   class="w-full text-xs rounded border-line focus:border-accent focus:ring-accent bg-canvas"
                   type="email"
                   name="email"
                   value="{{ old('email') }}"
                   required
                   autocomplete="username"
                   placeholder="nama@kampus.ac.id" />
            <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs text-rose-600" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-xs font-medium text-ink mb-1">Kata Sandi</label>
            <input id="password"
                   class="w-full text-xs rounded border-line focus:border-accent focus:ring-accent bg-canvas"
                   type="password"
                   name="password"
                   required
                   autocomplete="new-password"
                   placeholder="Minimal 8 karakter" />
            <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs text-rose-600" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-xs font-medium text-ink mb-1">Konfirmasi Kata Sandi</label>
            <input id="password_confirmation"
                   class="w-full text-xs rounded border-line focus:border-accent focus:ring-accent bg-canvas"
                   type="password"
                   name="password_confirmation"
                   required
                   autocomplete="new-password"
                   placeholder="Ulangi kata sandi" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5 text-xs text-rose-600" />
        </div>

        <div class="pt-2">
            <button type="submit"
                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-accent hover:bg-accent-hover text-white text-xs font-medium rounded transition shadow-xs">
                <span>Daftar Akun Mahasiswa</span>
                <x-icon name="arrow-right" class="w-3.5 h-3.5" />
            </button>
        </div>

        <div class="pt-3 border-t border-line text-center text-xs text-ink-muted">
            Sudah memiliki akun?
            <a href="{{ route('login') }}" class="text-accent font-medium hover:underline ml-1">
                Masuk di Sini
            </a>
        </div>
    </form>
</x-guest-layout>
