<x-guest-layout>
    <div class="mb-5">
        <h2 class="text-base font-semibold text-ink">Konfirmasi Akses Area Aman</h2>
        <p class="text-xs text-ink-muted mt-1 leading-relaxed">
            Ini adalah area sistem yang terlindungi. Silakan masukkan kembali kata sandi akun Anda sebelum melanjutkan.
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
        @csrf

        <!-- Password -->
        <div>
            <label for="password" class="block text-xs font-medium text-ink mb-1">Kata Sandi Akun</label>
            <input id="password"
                   class="w-full text-xs rounded border-line focus:border-accent focus:ring-accent bg-canvas"
                   type="password"
                   name="password"
                   required
                   autocomplete="current-password"
                   placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs text-rose-600" />
        </div>

        <div class="pt-2">
            <button type="submit"
                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-accent hover:bg-accent-hover text-white text-xs font-medium rounded transition shadow-xs cursor-pointer">
                <span>Konfirmasi Akses</span>
                <x-icon name="arrow-right" class="w-3.5 h-3.5" />
            </button>
        </div>
    </form>
</x-guest-layout>
