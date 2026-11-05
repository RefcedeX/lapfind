<x-guest-layout>
    <div class="mb-5">
        <h2 class="text-base font-semibold text-ink">Pemulihan Kata Sandi</h2>
        <p class="text-xs text-ink-muted mt-0.5 leading-relaxed">
            Lupa kata sandi akun Anda? Masukkan alamat email terdaftar dan kami akan mengirimkan tautan untuk mengatur ulang kata sandi baru.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4 text-xs font-medium text-emerald-600 dark:text-emerald-400" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-medium text-ink mb-1">Alamat Email Terdaftar</label>
            <input id="email"
                   class="w-full text-xs rounded border-line focus:border-accent focus:ring-accent bg-canvas"
                   type="email"
                   name="email"
                   value="{{ old('email') }}"
                   required
                   autofocus
                   placeholder="nama@kampus.ac.id" />
            <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs text-rose-600" />
        </div>

        <div class="pt-2">
            <button type="submit"
                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-accent hover:bg-accent-hover text-white text-xs font-medium rounded transition shadow-xs cursor-pointer">
                <span>Kirim Tautan Reset Kata Sandi</span>
                <x-icon name="arrow-right" class="w-3.5 h-3.5" />
            </button>
        </div>

        <div class="pt-3 border-t border-line text-center text-xs text-ink-muted">
            Sudah ingat kata sandi Anda?
            <a href="{{ route('login') }}" class="text-accent font-medium hover:underline ml-1">
                Kembali ke Halaman Masuk
            </a>
        </div>
    </form>
</x-guest-layout>
