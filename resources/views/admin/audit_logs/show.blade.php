@extends('layouts.admin', ['title' => "Detail Log Audit #{$auditLog->id}"])

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">

    {{-- ─── HEADER & BREADCRUMB ───────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-line pb-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-mono text-ink-muted mb-1">
                <a href="{{ route('admin.audit-logs.index') }}" class="hover:text-accent transition">Log Audit</a>
                <span>/</span>
                <span class="text-ink font-semibold">Entri #{{ $auditLog->id }}</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-semibold tracking-tight text-ink">
                Detail Log Audit Aktivitas
            </h1>
        </div>

        <a href="{{ route('admin.audit-logs.index') }}"
           class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-surface hover:bg-surface-subtle text-ink border border-line text-xs font-medium rounded transition shadow-2xs self-start sm:self-auto">
            <x-icon name="arrow-left" class="w-3.5 h-3.5 text-ink-muted" />
            <span>Kembali ke Daftar Log</span>
        </a>
    </div>

    {{-- ─── 2-COLUMN DETAIL WORKBENCH ────────────────────────────────── --}}
    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">

        {{-- LEFT COLUMN: Primary Record Meta --}}
        <div class="md:col-span-7 space-y-6">
            <div class="p-5 sm:p-6 bg-surface border border-line rounded-lg shadow-2xs space-y-5">
                <div class="flex items-start justify-between gap-3 border-b border-line pb-4">
                    <div>
                        <span class="text-[10px] font-mono uppercase tracking-wider text-ink-muted block">Aksi Tercatat</span>
                        <div class="mt-1 flex items-center gap-2">
                            <span class="font-mono font-bold text-base text-ink">{{ $auditLog->action }}</span>
                            <span class="text-xs px-2 py-0.5 rounded font-mono uppercase font-semibold bg-surface-subtle border border-line text-ink-muted">
                                {{ $auditLog->category }}
                            </span>
                        </div>
                    </div>
                    <span class="text-xs font-mono text-ink-muted bg-surface-subtle px-2.5 py-1 rounded border border-line">
                        {{ $auditLog->created_at->format('d/m/Y H:i:s') }} WIB
                    </span>
                </div>

                {{-- Deskripsi --}}
                <div>
                    <span class="text-xs font-medium text-ink-muted block mb-1">Deskripsi Lengkap</span>
                    <p class="text-sm text-ink leading-relaxed font-medium bg-canvas p-3 rounded border border-line">
                        {{ $auditLog->description }}
                    </p>
                </div>

                {{-- Pelaksana --}}
                <div class="pt-3 border-t border-line space-y-3">
                    <span class="text-xs font-medium text-ink-muted block">Identitas Pelaksana (User)</span>
                    <div class="flex items-center gap-3 p-3 bg-canvas border border-line rounded">
                        <div class="w-10 h-10 rounded-full bg-surface-subtle border border-line text-accent font-bold font-mono text-sm flex items-center justify-center shrink-0">
                            {{ strtoupper(substr($auditLog->user_name ?? 'G', 0, 1)) }}
                        </div>
                        <div class="truncate">
                            <div class="font-semibold text-sm text-ink">{{ $auditLog->user_name ?? 'Tamu (Guest)' }}</div>
                            <div class="text-xs font-mono text-ink-muted">{{ $auditLog->user_email ?? 'Tidak terhubung akun terdaftar' }}</div>
                        </div>
                        <div class="ml-auto">
                            <span class="text-[11px] font-mono uppercase px-2 py-0.5 rounded font-semibold bg-surface border border-line text-ink">
                                {{ $auditLog->user_role ?? 'guest' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Network & Request Metadata --}}
                <div class="pt-3 border-t border-line space-y-3">
                    <span class="text-xs font-medium text-ink-muted block">Konteks Jaringan & Permintaan</span>
                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div class="p-3 bg-canvas border border-line rounded">
                            <span class="text-[10px] font-mono text-ink-muted block">IP Address</span>
                            <span class="font-mono font-semibold text-ink mt-0.5 block">{{ $auditLog->ip_address ?? '127.0.0.1' }}</span>
                        </div>
                        <div class="p-3 bg-canvas border border-line rounded">
                            <span class="text-[10px] font-mono text-ink-muted block">HTTP Method</span>
                            <span class="font-mono font-semibold text-ink mt-0.5 block">{{ $auditLog->method ?? 'GET' }}</span>
                        </div>
                    </div>

                    <div class="p-3 bg-canvas border border-line rounded text-xs space-y-1">
                        <span class="text-[10px] font-mono text-ink-muted block">Target URL</span>
                        <span class="font-mono text-ink text-[11px] break-all block">{{ $auditLog->url ?? '-' }}</span>
                    </div>

                    <div class="p-3 bg-canvas border border-line rounded text-xs space-y-1">
                        <span class="text-[10px] font-mono text-ink-muted block">User Agent (Perangkat / Browser)</span>
                        <span class="font-mono text-ink-muted text-[11px] break-all block leading-relaxed">{{ $auditLog->user_agent ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN: JSON Payload / Properties --}}
        <div class="md:col-span-5 space-y-6">
            <div class="p-5 bg-surface border border-line rounded-lg shadow-2xs space-y-3">
                <div class="flex items-center justify-between border-b border-line pb-3">
                    <span class="text-xs font-semibold text-ink">Data Metadata (JSON Payload)</span>
                    <span class="text-[10px] font-mono text-ink-muted">Payload Context</span>
                </div>

                @if (!empty($auditLog->properties))
                    <div class="rounded-lg overflow-hidden border border-slate-700 bg-slate-900 text-slate-100 p-3.5 text-xs font-mono">
                        <pre class="overflow-x-auto no-scrollbar">{{ json_encode($auditLog->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                @else
                    <p class="text-xs text-ink-muted p-4 text-center bg-canvas rounded border border-line">
                        Tidak ada payload tambahan yang tersimpan untuk entri ini.
                    </p>
                @endif
            </div>

            <div class="p-4 bg-surface border border-line rounded-lg text-xs space-y-2 text-ink-muted leading-relaxed">
                <div class="flex items-center gap-1.5 font-semibold text-ink">
                    <x-icon name="shield" class="w-3.5 h-3.5 text-accent" />
                    <span>Integritas Audit Trail</span>
                </div>
                <p>
                    Entri log ini bersifat <em>immutable</em> (hanya-baca) dan tidak dapat dimodifikasi oleh pengguna manapun guna menjaga keabsahan pelaporan audit forensik dan kepatuhan standar keamanan sistem.
                </p>
            </div>
        </div>

    </div>

</div>
@endsection

