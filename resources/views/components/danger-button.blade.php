<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center gap-2 px-4.5 py-2.5 bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white font-semibold text-xs sm:text-sm rounded-lg shadow-2xs transition cursor-pointer disabled:opacity-50 outline-none focus:ring-2 focus:ring-rose-500/30']) }}>
    {{ $slot }}
</button>
