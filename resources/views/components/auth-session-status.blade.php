@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'font-medium text-sm text-accent-indigo']) }}>
        {{ $status }}
    </div>
@endif
