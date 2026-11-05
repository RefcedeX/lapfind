@props([
    'name',
    'class' => 'w-4 h-4',
    'strokeWidth' => '2',
])

@php
    $c = $class;
    $sw = $strokeWidth;
@endphp

@switch($name)
    @case('cpu')
        <svg class="{{ $c }}" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" viewBox="0 0 24 24">
            <rect x="4" y="4" width="16" height="16" rx="2" />
            <rect x="9" y="9" width="6" height="6" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 1v3M15 1v3M9 20v3M15 20v3M20 9h3M20 14h3M1 9h3M1 14h3" />
        </svg>
        @break

    @case('ram')
        <svg class="{{ $c }}" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" viewBox="0 0 24 24">
            <rect x="2" y="6" width="20" height="12" rx="2" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 10v4M10 10v4M14 10v4M18 10v4M6 18v2M10 18v2M14 18v2M18 18v2" />
        </svg>
        @break

    @case('storage')
        <svg class="{{ $c }}" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" viewBox="0 0 24 24">
            <rect x="3" y="4" width="18" height="16" rx="2" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 14h18M7 17h.01M17 17h.01" />
        </svg>
        @break

    @case('weight')
        <svg class="{{ $c }}" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18M3 9l9-6 9 6M6 15l-3 4h6l-3-4zM18 15l-3 4h6l-3-4z" />
        </svg>
        @break

    @case('battery')
        <svg class="{{ $c }}" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" viewBox="0 0 24 24">
            <rect x="2" y="7" width="16" height="10" rx="2" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M22 11v2M7 12h6" />
        </svg>
        @break

    @case('price')
        <svg class="{{ $c }}" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5a2 2 0 011.414.586l7 7a2 2 0 010 2.828l-5 5a2 2 0 01-2.828 0l-7-7A2 2 0 013 9V5a2 2 0 012-2z" />
        </svg>
        @break

    @case('laptop')
        <svg class="{{ $c }}" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" viewBox="0 0 24 24">
            <rect x="3" y="4" width="18" height="12" rx="2" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M2 18h20a1 1 0 011 1v1H1v-1a1 1 0 011-1z" />
        </svg>
        @break

    @case('sparkles')
        <svg class="{{ $c }}" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4M4 19h4M13 3l2.5 5.5L21 11l-5.5 2.5L13 19l-2.5-5.5L5 11l5.5-2.5L13 3z" />
        </svg>
        @break

    @case('check')
        <svg class="{{ $c }}" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
        </svg>
        @break

    @case('check-circle')
        <svg class="{{ $c }}" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        @break

    @case('trophy')
        <svg class="{{ $c }}" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 21h8m-4-4v4M6 4h12a2 2 0 012 2v2a6 6 0 01-6 6h-4a6 6 0 01-6-6V6a2 2 0 012-2zM4 6H2a2 2 0 00-2 2v1a4 4 0 004 4h2M20 6h2a2 2 0 012 2v1a4 4 0 01-4 4h-2" />
        </svg>
        @break

    @case('shield')
        <svg class="{{ $c }}" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
        </svg>
        @break

    @case('code')
        <svg class="{{ $c }}" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
        </svg>
        @break

    @case('palette')
        <svg class="{{ $c }}" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M7 21a4 4 0 01-4-4 4 4 0 014-4h1a2 2 0 002-2V9a6 6 0 1112 0v1a5 5 0 01-5 5h-1a2 2 0 00-2 2v1a3 3 0 01-3 3z" />
        </svg>
        @break

    @case('book')
        <svg class="{{ $c }}" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
        </svg>
        @break

    @case('compare')
        <svg class="{{ $c }}" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
        </svg>
        @break

    @case('chart')
        <svg class="{{ $c }}" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
        </svg>
        @break

    @case('search')
        <svg class="{{ $c }}" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        @break

    @case('filter')
        <svg class="{{ $c }}" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
        </svg>
        @break

    @case('file-text')
        <svg class="{{ $c }}" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        @break

    @case('download')
        <svg class="{{ $c }}" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
        </svg>
        @break

    @case('printer')
        <svg class="{{ $c }}" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
        </svg>
        @break

    @case('trash')
        <svg class="{{ $c }}" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
        </svg>
        @break

    @case('refresh')
        <svg class="{{ $c }}" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
        </svg>
        @break

    @case('arrow-right')
        <svg class="{{ $c }}" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
        </svg>
        @break

    @case('chevron-right')
        <svg class="{{ $c }}" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
        </svg>
        @break

    @case('chevron-down')
        <svg class="{{ $c }}" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
        @break

    @case('user')
        <svg class="{{ $c }}" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
        </svg>
        @break

    @case('clock')
        <svg class="{{ $c }}" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        @break

    @case('arrow-left')
        <svg class="{{ $c }}" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        @break

    @case('plus')
        <svg class="{{ $c }}" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
        </svg>
        @break

    @case('eye')
        <svg class="{{ $c }}" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
        </svg>
        @break

    @case('alert-triangle')
        <svg class="{{ $c }}" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        @break

    @case('edit')
        <svg class="{{ $c }}" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
        </svg>
        @break

    @case('info')
        <svg class="{{ $c }}" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        @break

    @case('scale')
        <svg class="{{ $c }}" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3-1m6 1l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M15 7l3-1M12 3v18m-3 0h6" />
        </svg>
        @break

    @case('calendar')
        <svg class="{{ $c }}" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" viewBox="0 0 24 24">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 2v4M8 2v4M3 10h18" />
        </svg>
        @break

    @case('shield')
        <svg class="{{ $c }}" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
        </svg>
        @break

    @case('activity')
        <svg class="{{ $c }}" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" viewBox="0 0 24 24">
            <polyline stroke-linecap="round" stroke-linejoin="round" points="22 12 18 12 15 21 9 3 6 12 2 12" />
        </svg>
        @break

    @default
        <svg class="{{ $c }}" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01" />
        </svg>
@endswitch
