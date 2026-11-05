<section>
    <header class="flex items-start gap-3.5 pb-5 border-b border-slate-100 dark:border-slate-800">
        <div class="w-9 h-9 rounded-lg bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
        </div>
        <div>
            <h2 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white">
                Keamanan & Kata Sandi
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                Pastikan akun Anda terlindungi dengan menggunakan kata sandi acak dan unik.
            </p>
        </div>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('put')

        {{-- Kata Sandi Saat Ini --}}
        <div>
            <x-input-label for="update_password_current_password" value="Kata Sandi Saat Ini" />
            <x-text-input id="update_password_current_password"
                          name="current_password"
                          type="password"
                          class="mt-1 block w-full"
                          autocomplete="current-password"
                          placeholder="••••••••" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1.5" />
        </div>

        {{-- Kata Sandi Baru --}}
        <div>
            <x-input-label for="update_password_password" value="Kata Sandi Baru" />
            <x-text-input id="update_password_password"
                          name="password"
                          type="password"
                          class="mt-1 block w-full"
                          autocomplete="new-password"
                          placeholder="Minimal 8 karakter kombinasi" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1.5" />
        </div>

        {{-- Konfirmasi Kata Sandi Baru --}}
        <div>
            <x-input-label for="update_password_password_confirmation" value="Ulangi Kata Sandi Baru" />
            <x-text-input id="update_password_password_confirmation"
                          name="password_confirmation"
                          type="password"
                          class="mt-1 block w-full"
                          autocomplete="new-password"
                          placeholder="Ketik ulang kata sandi baru" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1.5" />
        </div>

        {{-- Security Best Practice Hint --}}
        <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-800/60 border border-slate-200/70 dark:border-slate-700/60 flex items-start gap-2.5">
            <svg class="w-4 h-4 text-slate-400 dark:text-slate-400 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="16" x2="12" y2="12"/>
                <line x1="12" y1="8" x2="12.01" y2="8"/>
            </svg>
            <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                <strong class="font-semibold text-slate-800 dark:text-slate-200">Tips Keamanan:</strong> Gunakan minimal 8 karakter perpaduan huruf besar, huruf kecil, angka, dan simbol untuk menjaga keamanan riwayat evaluasi Anda.
            </p>
        </div>

        {{-- Actions & Feedback --}}
        <div class="pt-2 flex flex-wrap items-center gap-3">
            <x-primary-button>
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                <span>Perbarui Kata Sandi</span>
            </x-primary-button>

            @if (session('status') === 'password-updated')
                <div x-data="{ show: true }"
                     x-show="show"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     x-init="setTimeout(() => show = false, 3500)"
                     class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-xs font-medium">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    <span>Kata sandi berhasil diperbarui.</span>
                </div>
            @endif
        </div>
    </form>
</section>
