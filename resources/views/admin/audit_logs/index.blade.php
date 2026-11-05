@extends('layouts.admin', ['title' => 'Log Audit & Aktivitas Akun'])

@section('content')
<div class="space-y-6">

    {{-- ─── HEADER & ACTIONS ─────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-line pb-4">
        <div>
            <div class="text-[11px] font-mono uppercase tracking-wider text-accent font-semibold mb-1">
                Sistem Keamanan & Pengawasan Website
            </div>
            <h1 class="text-xl sm:text-2xl font-semibold tracking-tight text-ink">
                Log Audit & Aktivitas Akun
            </h1>
            <p class="text-xs text-ink-muted mt-1 max-w-2xl leading-relaxed">
                Rekam jejak seluruh aktivitas login, modifikasi katalog laptop, penambahan kriteria, dan interaksi pengguna sesuai standar keamanan sistem.
            </p>
        </div>

        <div class="flex items-center gap-2 self-start sm:self-auto">
            <a href="{{ route('admin.audit-logs.export', request()->query()) }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-surface hover:bg-surface-subtle text-ink border border-line text-xs font-medium rounded transition shadow-2xs">
                <x-icon name="download" class="w-3.5 h-3.5 text-ink-muted" />
                <span>Ekspor CSV</span>
            </a>
        </div>
    </div>

    {{-- ─── 4 TELEMETRY CARDS ────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Stat 1: Total Logs --}}
        <div class="p-4 bg-surface border border-line rounded shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs text-ink-muted">Total Aktivitas</span>
                <span class="p-1 rounded bg-surface-subtle text-accent">
                    <x-icon name="activity" class="w-3.5 h-3.5" />
                </span>
            </div>
            <div class="mt-2 text-2xl font-bold font-mono text-ink">
                {{ number_format($stats['total']) }}
            </div>
            <span class="text-[11px] text-ink-muted">Seluruh entri tercatat</span>
        </div>

        {{-- Stat 2: Today Logs --}}
        <div class="p-4 bg-surface border border-line rounded shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs text-ink-muted">Aktivitas Hari Ini</span>
                <span class="p-1 rounded bg-emerald-50 text-emerald-700">
                    <x-icon name="clock" class="w-3.5 h-3.5" />
                </span>
            </div>
            <div class="mt-2 text-2xl font-bold font-mono text-emerald-600">
                {{ number_format($stats['today']) }}
            </div>
            <span class="text-[11px] text-ink-muted">Sesi aktif per hari ini</span>
        </div>

        {{-- Stat 3: Failed Logins (Security Alert) --}}
        <div class="p-4 bg-surface border border-line rounded shadow-2xs {{ $stats['failed_logins'] > 0 ? 'border-rose-300 bg-rose-50/20' : '' }}">
            <div class="flex items-center justify-between">
                <span class="text-xs text-ink-muted">Login Gagal (7 Hari)</span>
                <span class="p-1 rounded {{ $stats['failed_logins'] > 0 ? 'bg-rose-100 text-rose-700' : 'bg-surface-subtle text-ink-muted' }}">
                    <x-icon name="shield" class="w-3.5 h-3.5" />
                </span>
            </div>
            <div class="mt-2 text-2xl font-bold font-mono {{ $stats['failed_logins'] > 0 ? 'text-rose-600' : 'text-ink' }}">
                {{ number_format($stats['failed_logins']) }}
            </div>
            <span class="text-[11px] text-ink-muted">Peringatan potensi anomali</span>
        </div>

        {{-- Stat 4: Data Mutations --}}
        <div class="p-4 bg-surface border border-line rounded shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs text-ink-muted">Mutasi Data Master</span>
                <span class="p-1 rounded bg-surface-subtle text-accent">
                    <x-icon name="edit" class="w-3.5 h-3.5" />
                </span>
            </div>
            <div class="mt-2 text-2xl font-bold font-mono text-ink">
                {{ number_format($stats['mutations']) }}
            </div>
            <span class="text-[11px] text-ink-muted">Laptop, kriteria & user</span>
        </div>
    </div>

    {{-- ─── FILTER & SEARCH BAR ──────────────────────────────────────── --}}
    <div class="space-y-3">
        {{-- Category Pills --}}
        <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar pb-1 text-xs">
            @foreach ($categories as $catKey => $catData)
                @php
                    $isActive = request('category', 'all') === $catKey;
                @endphp
                <a href="{{ route('admin.audit-logs.index', array_merge(request()->query(), ['category' => $catKey, 'page' => 1])) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full font-medium transition shrink-0 {{ $isActive ? 'bg-accent text-white font-semibold shadow-xs' : 'bg-surface hover:bg-surface-subtle text-ink-muted border border-line' }}">
                    <span>{{ $catData['label'] }}</span>
                    <span class="text-[10px] font-mono px-1.5 py-0.2 rounded {{ $isActive ? 'bg-white/20 text-white' : 'bg-surface-subtle text-ink-muted' }}">
                        {{ $catData['count'] }}
                    </span>
                </a>
            @endforeach
        </div>

        {{-- Search & Date Filters --}}
        <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="p-4 bg-surface border border-line rounded shadow-xs">
            <input type="hidden" name="category" value="{{ request('category', 'all') }}">

            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-5">
                    <label for="search" class="block text-xs font-medium text-ink mb-1">Cari Kata Kunci</label>
                    <div class="relative">
                        <input type="text"
                               id="search"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Nama pengguna, email, IP, atau deskripsi..."
                               class="w-full text-xs rounded border-line focus:border-accent focus:ring-accent bg-canvas pl-8">
                        <div class="absolute left-2.5 top-2.5 text-ink-muted pointer-events-none">
                            <x-icon name="search" class="w-3.5 h-3.5" />
                        </div>
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label for="date_from" class="block text-xs font-medium text-ink mb-1">Dari Tanggal</label>
                    <input type="date"
                           id="date_from"
                           name="date_from"
                           value="{{ request('date_from') }}"
                           class="w-full text-xs rounded border-line focus:border-accent focus:ring-accent bg-canvas font-mono">
                </div>

                <div class="sm:col-span-3">
                    <label for="date_to" class="block text-xs font-medium text-ink mb-1">Sampai Tanggal</label>
                    <input type="date"
                           id="date_to"
                           name="date_to"
                           value="{{ request('date_to') }}"
                           class="w-full text-xs rounded border-line focus:border-accent focus:ring-accent bg-canvas font-mono">
                </div>

                <div class="sm:col-span-1 flex gap-1">
                    <button type="submit"
                            class="w-full py-2 bg-accent hover:bg-accent-hover text-white text-xs font-medium rounded transition text-center shadow-xs cursor-pointer"
                            title="Terapkan Filter">
                        Filter
                    </button>
                    @if (request()->hasAny(['search', 'date_from', 'date_to', 'category']))
                        <a href="{{ route('admin.audit-logs.index') }}"
                           class="p-2 bg-surface hover:bg-surface-subtle border border-line text-ink-muted text-xs rounded transition"
                           title="Reset Filter">
                            ✕
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- ─── AUDIT LOG TABLE ──────────────────────────────────────────── --}}
    <div class="bg-surface border border-line rounded overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-surface-subtle border-b border-line text-ink-muted font-medium">
                    <tr>
                        <th class="py-3 px-4 w-36 font-mono">Waktu</th>
                        <th class="py-3 px-4 min-w-[160px]">Pengguna</th>
                        <th class="py-3 px-4 w-32">Aksi</th>
                        <th class="py-3 px-4">Deskripsi Aktivitas</th>
                        <th class="py-3 px-4 w-32 font-mono">IP & Method</th>
                        <th class="py-3 px-4 text-right w-20">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-surface-subtle/50 transition">
                            {{-- Waktu --}}
                            <td class="py-3 px-4 whitespace-nowrap font-mono text-ink-muted">
                                <div>{{ $log->created_at->format('d/m/Y H:i:s') }}</div>
                                <div class="text-[10px] text-ink-muted/80">{{ $log->created_at->diffForHumans() }}</div>
                            </td>

                            {{-- Pengguna --}}
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-full bg-surface-subtle border border-line text-accent font-bold font-mono text-[11px] flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($log->user_name ?? 'G', 0, 1)) }}
                                    </div>
                                    <div class="truncate max-w-[150px]">
                                        <div class="font-medium text-ink truncate">{{ $log->user_name }}</div>
                                        <div class="text-[10px] font-mono text-ink-muted truncate">{{ $log->user_email ?? 'Tidak ada email' }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- Aksi & Kategori --}}
                            <td class="py-3 px-4 whitespace-nowrap">
                                @php
                                    $badgeStyles = [
                                        'rose' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        'amber' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'emerald' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'blue' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    ][$log->severity_color] ?? 'bg-surface-subtle text-ink-muted border-line';
                                @endphp
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-mono font-semibold border {{ $badgeStyles }}">
                                    <span>{{ $log->action }}</span>
                                </span>
                            </td>

                            {{-- Deskripsi --}}
                            <td class="py-3 px-4 text-ink leading-relaxed">
                                {{ $log->description }}
                            </td>

                            {{-- IP Address & Method --}}
                            <td class="py-3 px-4 whitespace-nowrap font-mono text-ink-muted text-[11px]">
                                <div>{{ $log->ip_address ?? '127.0.0.1' }}</div>
                                <span class="text-[10px] px-1 py-0.2 rounded bg-surface-subtle border border-line text-ink-muted">
                                    {{ $log->method ?? 'GET' }}
                                </span>
                            </td>

                            {{-- Aksi / Detail --}}
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <a href="{{ route('admin.audit-logs.show', $log) }}"
                                   class="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-surface hover:bg-surface-subtle border border-line text-[11px] font-medium text-ink transition">
                                    <x-icon name="eye" class="w-3 h-3 text-ink-muted" />
                                    <span>Lihat</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-ink-muted">
                                <div class="w-12 h-12 rounded-full bg-surface-subtle border border-line flex items-center justify-center mx-auto mb-3 text-ink-muted">
                                    <x-icon name="shield" class="w-5 h-5" />
                                </div>
                                <p class="text-sm font-medium text-ink">Tidak ada log aktivitas ditemukan</p>
                                <p class="text-xs text-ink-muted mt-1">Coba ubah kata kunci pencarian atau bersihkan filter tanggal.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($logs->hasPages())
            <div class="p-4 border-t border-line bg-surface">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

