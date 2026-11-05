<x-guest-layout>
    <div class="mb-5">
        <h2 class="text-base font-semibold text-ink">Masuk ke Akun</h2>
        <p class="text-xs text-ink-muted mt-0.5">Akses panel administrator atau riwayat evaluasi tersimpan Anda.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-medium text-ink mb-1">Alamat Email</label>
            <input id="email"
                   class="w-full text-xs rounded border-line focus:border-accent focus:ring-accent bg-canvas"
                   type="email"
                   name="email"
                   value="{{ old('email') }}"
                   required
                   autofocus
                   autocomplete="username"
                   placeholder="nama@kampus.ac.id" />
            <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs text-rose-600" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-1">
                <label for="password" class="block text-xs font-medium text-ink">Kata Sandi</label>
                @if (Route::has('password.request'))
                    <a class="text-[11px] text-ink-muted hover:text-accent underline" href="{{ route('password.request') }}">
                        Lupa kata sandi?
                    </a>
                @endif
            </div>

            <input id="password"
                   class="w-full text-xs rounded border-line focus:border-accent focus:ring-accent bg-canvas"
                   type="password"
                   name="password"
                   required
                   autocomplete="current-password"
                   placeholder="••••••••" />

            <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs text-rose-600" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between pt-1">
            <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer">
                <input id="remember_me"
                       type="checkbox"
                       class="rounded border-line text-accent focus:ring-accent bg-canvas"
                       name="remember">
                <span class="text-xs text-ink-muted">Ingat sesi saya</span>
            </label>
        </div>

        <div class="pt-2">
            <button type="submit"
                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-accent hover:bg-accent-hover text-white text-xs font-medium rounded transition shadow-xs">
                <span>Masuk Sekarang</span>
                <x-icon name="arrow-right" class="w-3.5 h-3.5" />
            </button>
        </div>

        <div class="pt-3 border-t border-line text-center text-xs text-ink-muted">
            Belum memiliki akun?
            <a href="{{ route('register') }}" class="text-accent font-medium hover:underline ml-1">
                Daftar Akun Baru
            </a>
        </div>
    </form>

    <!-- Quick Demo Accounts (UTS / Pengujian Pengguna) -->
    <div class="mt-4 p-3 bg-surface-subtle border border-line rounded text-xs space-y-2"
         x-data="{ fill(e, p) { document.getElementById('email').value = e; document.getElementById('password').value = p; } }">
        <div class="flex items-center justify-between text-[11px] font-mono uppercase tracking-wider text-ink-muted">
            <span>Kredensial Pengujian (Demo)</span>
            <span class="text-[10px] text-accent font-medium">Klik untuk isi otomatis</span>
        </div>
        <div class="grid grid-cols-2 gap-2 pt-1">
            <button type="button"
                    @click="fill('admin@spk-laptop.test', 'password')"
                    class="p-2 text-left bg-surface hover:bg-surface-subtle border border-line rounded transition cursor-pointer group">
                <div class="flex items-center justify-between">
                    <span class="font-semibold text-ink group-hover:text-accent text-[11px]">Admin SPK</span>
                    <span class="text-[9px] px-1 py-0.5 rounded bg-blue-500/10 text-blue-600 dark:text-blue-400 font-mono font-bold">admin</span>
                </div>
                <div class="text-[10px] font-mono text-ink-muted truncate mt-0.5">admin@spk-laptop.test</div>
            </button>
            <button type="button"
                    @click="fill('mahasiswa@spk-laptop.test', 'password')"
                    class="p-2 text-left bg-surface hover:bg-surface-subtle border border-line rounded transition cursor-pointer group">
                <div class="flex items-center justify-between">
                    <span class="font-semibold text-ink group-hover:text-accent text-[11px]">Mahasiswa</span>
                    <span class="text-[9px] px-1 py-0.5 rounded bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-mono font-bold">user</span>
                </div>
                <div class="text-[10px] font-mono text-ink-muted truncate mt-0.5">mahasiswa@spk-laptop.test</div>
            </button>
        </div>
    </div>
</x-guest-layout>
