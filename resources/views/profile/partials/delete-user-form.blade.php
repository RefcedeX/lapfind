<section>
    <header class="flex items-start gap-3.5 pb-5 border-b border-rose-200/60 dark:border-rose-900/50">
        <div class="w-9 h-9 rounded-lg bg-rose-100 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                <line x1="12" y1="9" x2="12" y2="13"/>
                <line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
        </div>
        <div>
            <h2 class="text-base sm:text-lg font-bold text-rose-900 dark:text-rose-200">
                Zona Bahaya — Hapus Akun
            </h2>
            <p class="text-xs sm:text-sm text-rose-700/80 dark:text-rose-300/80 mt-0.5 leading-relaxed">
                Setelah akun Anda dihapus, semua data sesi perbandingan laptop dan riwayat evaluasi AHP-TOPSIS akan dimusnahkan secara permanen. Tindakan ini tidak dapat dibatalkan.
            </p>
        </div>
    </header>

    <div class="mt-6 flex items-center justify-between flex-wrap gap-4">
        <div class="text-xs text-slate-500 dark:text-slate-400 max-w-md">
            Pastikan Anda telah mencatat atau mencetak hasil perankingan SPK yang penting sebelum melanjutkan proses ini.
        </div>
        <x-danger-button
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        >
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="3 6 5 6 21 6"/>
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
            </svg>
            <span>Hapus Akun Permanen</span>
        </x-danger-button>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 sm:p-7">
            @csrf
            @method('delete')

            <div class="flex items-center gap-3 text-rose-600 dark:text-rose-400 mb-4">
                <div class="w-10 h-10 rounded-full bg-rose-100 dark:bg-rose-950/60 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                        <line x1="12" y1="9" x2="12" y2="13"/>
                        <line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white">
                        Konfirmasi Hapus Akun LapFind
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Tindakan permanen dan tidak dapat dipulihkan
                    </p>
                </div>
            </div>

            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed mb-5">
                Apakah Anda benar-benar yakin ingin menghapus akun ini? Seluruh data akun beserta riwayat perankingan SPK dan komparasi laptop akan dihapus secara permanen dari server. Silakan masukkan kata sandi Anda untuk mengonfirmasi.
            </p>

            <div>
                <x-input-label for="password" value="Kata Sandi Konfirmasi" class="font-semibold text-slate-700 dark:text-slate-300" />
                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-full"
                    placeholder="Masukkan kata sandi akun Anda"
                    autofocus
                />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')">
                    Batal
                </x-secondary-button>

                <x-danger-button>
                    Ya, Hapus Akun Secara Permanen
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
