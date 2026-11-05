@props(['type' => 'neutral'])

@php
$classes = match($type) {
    'benefit' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
    'admin' => 'bg-purple-50 text-purple-700 border-purple-200',
    'success' => 'bg-blue-50 text-blue-700 border-blue-200',
    'cost', 'warning' => 'bg-amber-50 text-amber-800 border-amber-200',
    'danger', 'error' => 'bg-red-50 text-red-700 border-red-200',
    'info' => 'bg-sky-50 text-sky-700 border-sky-200',
    default => 'bg-slate-100 text-slate-700 border-slate-200',
};
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2 py-0.5 text-[11px] font-mono font-medium rounded border {$classes} tracking-wide"]) }}>
    {{ $slot }}
</span>