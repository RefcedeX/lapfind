<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 text-xs sm:text-sm font-medium rounded-lg shadow-2xs transition cursor-pointer disabled:opacity-50 outline-none focus:ring-2 focus:ring-slate-400/20']) }}>
    {{ $slot }}
</button>
