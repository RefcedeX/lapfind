<x-guest-layout>
    <div class="mb-5">
        <h2 class="text-base font-semibold text-ink">Perbarui Kata Sandi Akun</h2>
        <p class="text-xs text-ink-muted mt-0.5 leading-relaxed">
            Buat kata sandi baru yang kuat dan aman untuk akun LapFind Anda.
        </p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-medium text-ink mb-1">Alamat Email</label>
            <input id="email"
                   class="w-full text-xs rounded border-line focus:border-accent focus:ring-accent bg-canvas"
                   type="email"
                   name="email"
                   value="{{ old('email', $request->email) }}"
                   required
                   autofocus
                   autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs text-rose-600" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-xs font-medium text-ink mb-1">Kata Sandi Baru</label>
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
            <label for="password_confirmation" class="block text-xs font-medium text-ink mb-1">Konfirmasi Kata Sandi Baru</label>
            <input id="password_confirmation"
                   class="w-full text-xs rounded border-line focus:border-accent focus:ring-accent bg-canvas"
                   type="password"
                   name="password_confirmation"
                   required
                   autocomplete="new-password"
                   placeholder="Ulangi kata sandi baru" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5 text-xs text-rose-600" />
        </div>

        <div class="pt-2">
            <button type="submit"
                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-accent hover:bg-accent-hover text-white text-xs font-medium rounded transition shadow-xs cursor-pointer">
                <span>Simpan Kata Sandi Baru</span>
                <x-icon name="arrow-right" class="w-3.5 h-3.5" />
            </button>
        </div>
    </form>
</x-guest-layout>
