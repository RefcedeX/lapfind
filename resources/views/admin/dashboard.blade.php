@extends('layouts.admin')

@section('content')
<div class="space-y-8">
    <!-- Header Page -->
    <div class="border-b border-line pb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="text-[11px] font-mono uppercase tracking-wider text-accent font-semibold mb-1">
                Panel Kontrol
            </div>
            <h1 class="text-xl sm:text-2xl font-semibold tracking-tight text-ink">Ringkasan Sistem SPK</h1>
            <p class="text-xs text-ink-muted mt-1">Status basis data laptop mahasiswa, parameter pembobotan AHP, dan pemeringkatan TOPSIS.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-surface border border-line rounded text-xs font-mono text-ink">
                <span class="w-2 h-2 rounded-full bg-accent animate-pulse"></span>
                <span>Sistem Aktif</span>
            </span>
        </div>
    </div>

    <!-- Stat Cards with Icons -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="bg-surface p-3.5 rounded border border-line shadow-2xs">
            <div class="flex items-center justify-between text-ink-muted mb-2">
                <span class="text-[10px] font-mono uppercase tracking-wider">Total Laptop</span>
                <span class="p-1 rounded bg-surface-subtle border border-line text-accent">
                    <x-icon name="laptop" class="w-3.5 h-3.5" />
                </span>
            </div>
            <div class="text-xl sm:text-2xl font-semibold font-mono tracking-tight text-ink">{{ $stats['total_laptops'] }}</div>
            <span class="text-[10px] text-ink-muted mt-1 block">Alternatif aktif</span>
        </div>

        <div class="bg-surface p-3.5 rounded border border-line shadow-2xs">
            <div class="flex items-center justify-between text-ink-muted mb-2">
                <span class="text-[10px] font-mono uppercase tracking-wider">Kriteria AHP</span>
                <span class="p-1 rounded bg-surface-subtle border border-line text-accent">
                    <x-icon name="filter" class="w-3.5 h-3.5" />
                </span>
            </div>
            <div class="text-xl sm:text-2xl font-semibold font-mono tracking-tight text-ink">{{ $stats['total_criteria'] }}</div>
            <span class="text-[10px] text-ink-muted mt-1 block">C1 s.d. C{{ $stats['total_criteria'] }}</span>
        </div>

        <div class="bg-surface p-3.5 rounded border border-line shadow-2xs">
            <div class="flex items-center justify-between text-ink-muted mb-2">
                <span class="text-[10px] font-mono uppercase tracking-wider">Pengguna</span>
                <span class="p-1 rounded bg-surface-subtle border border-line text-accent">
                    <x-icon name="user" class="w-3.5 h-3.5" />
                </span>
            </div>
            <div class="text-xl sm:text-2xl font-semibold font-mono tracking-tight text-ink">{{ $stats['total_users'] }}</div>
            <span class="text-[10px] text-ink-muted mt-1 block">Admin & Mahasiswa</span>
        </div>

        <div class="bg-surface p-3.5 rounded border border-line shadow-2xs">
            <div class="flex items-center justify-between text-ink-muted mb-2">
                <span class="text-[10px] font-mono uppercase tracking-wider">Riwayat SPK</span>
                <span class="p-1 rounded bg-surface-subtle border border-line text-accent">
                    <x-icon name="chart" class="w-3.5 h-3.5" />
                </span>
            </div>
            <div class="text-xl sm:text-2xl font-semibold font-mono tracking-tight text-ink">{{ $stats['total_spk_results'] }}</div>
            <span class="text-[10px] text-ink-muted mt-1 block">Sesi tersimpan</span>
        </div>

        <div class="bg-surface p-3.5 rounded border border-line shadow-2xs">
            <div class="flex items-center justify-between text-ink-muted mb-2">
                <span class="text-[10px] font-mono uppercase tracking-wider">Log Audit</span>
                <span class="p-1 rounded bg-surface-subtle border border-line text-emerald-600 dark:text-emerald-400">
                    <x-icon name="activity" class="w-3.5 h-3.5" />
                </span>
            </div>
            <div class="text-xl sm:text-2xl font-semibold font-mono tracking-tight text-ink">{{ $stats['total_audit_logs'] }}</div>
            <span class="text-[10px] text-ink-muted mt-1 block">Rekaman aktivitas</span>
        </div>

        <div class="bg-surface p-3.5 rounded border border-line shadow-2xs">
            <div class="flex items-center justify-between text-ink-muted mb-2">
                <span class="text-[10px] font-mono uppercase tracking-wider">Keamanan 7h</span>
                <span class="p-1 rounded bg-surface-subtle border border-line {{ $stats['failed_logins_week'] > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                    <x-icon name="shield" class="w-3.5 h-3.5" />
                </span>
            </div>
            <div class="text-xl sm:text-2xl font-semibold font-mono tracking-tight {{ $stats['failed_logins_week'] > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                {{ $stats['failed_logins_week'] > 0 ? $stats['failed_logins_week'] : '0' }}
            </div>
            <span class="text-[10px] text-ink-muted mt-1 block">{{ $stats['failed_logins_week'] > 0 ? 'Percobaan gagal' : 'Status aman' }}</span>
        </div>
    </div>

    <!-- Quick Action Bar -->
    <div class="flex flex-wrap items-center gap-2 pt-1">
        <a href="{{ route('admin.alternatives.create') }}"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-accent hover:bg-accent-hover text-white text-xs font-medium rounded transition shadow-xs">
            <x-icon name="plus" class="w-3.5 h-3.5 text-white" />
            <span>Tambah Laptop</span>
        </a>
        <a href="{{ route('admin.criteria.create') }}"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-surface hover:bg-surface-subtle text-ink border border-line text-xs font-medium rounded transition shadow-2xs">
            <x-icon name="plus" class="w-3.5 h-3.5 text-accent" />
            <span>Tambah Kriteria</span>
        </a>
        <a href="{{ route('admin.values.index') }}"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-surface hover:bg-surface-subtle text-ink border border-line text-xs font-medium rounded transition shadow-2xs">
            <x-icon name="scale" class="w-3.5 h-3.5 text-accent" />
            <span>Kelola Matriks Keputusan</span>
        </a>
        <a href="{{ route('admin.users.index') }}"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-surface hover:bg-surface-subtle text-ink border border-line text-xs font-medium rounded transition shadow-2xs">
            <x-icon name="user" class="w-3.5 h-3.5 text-accent" />
            <span>Kelola Pengguna</span>
        </a>
        <a href="{{ route('admin.audit-logs.index') }}"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-surface hover:bg-surface-subtle text-ink border border-line text-xs font-medium rounded transition shadow-2xs">
            <x-icon name="shield" class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" />
            <span>Log Audit Aktivitas</span>
        </a>
    </div>

    <!-- 2 Column Section: Kriteria Aktif & Laptop Terbaru -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Tabel Kriteria Saat Ini -->
        <div class="bg-surface border border-line rounded shadow-2xs">
            <div class="px-4 py-3 border-b border-line flex items-center justify-between">
                <h2 class="text-xs font-semibold uppercase tracking-wider text-ink">Kriteria Aktif (AHP)</h2>
                <a href="{{ route('admin.criteria.index') }}" class="text-[11px] text-accent hover:underline inline-flex items-center gap-1">
                    <span>Kelola semua</span>
                    <x-icon name="arrow-right" class="w-3 h-3" />
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-surface-subtle border-b border-line text-ink-muted font-medium">
                        <tr>
                            <th class="py-2.5 px-3 w-14 font-mono">Kode</th>
                            <th class="py-2.5 px-3">Nama Kriteria</th>
                            <th class="py-2.5 px-3">Sifat</th>
                            <th class="py-2.5 px-3 font-mono">Satuan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse ($criteria as $crit)
                            <tr class="hover:bg-surface-subtle/50 transition">
                                <td class="py-2 px-3 font-mono font-medium text-ink">{{ $crit->code }}</td>
                                <td class="py-2 px-3 text-ink font-medium">{{ $crit->name }}</td>
                                <td class="py-2 px-3">
                                    <x-badge :type="$crit->type">{{ ucfirst($crit->type) }}</x-badge>
                                </td>
                                <td class="py-2 px-3 font-mono text-ink-muted">{{ $crit->unit ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-ink-muted">Belum ada kriteria tersimpan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tabel Alternatif Terbaru dengan Thumbnail Studio -->
        <div class="bg-surface border border-line rounded shadow-2xs">
            <div class="px-4 py-3 border-b border-line flex items-center justify-between">
                <h2 class="text-xs font-semibold uppercase tracking-wider text-ink">Laptop Terbaru (TOPSIS)</h2>
                <a href="{{ route('admin.alternatives.index') }}" class="text-[11px] text-accent hover:underline inline-flex items-center gap-1">
                    <span>Semua katalog</span>
                    <x-icon name="arrow-right" class="w-3 h-3" />
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-surface-subtle border-b border-line text-ink-muted font-medium">
                        <tr>
                            <th class="py-2.5 px-3">Model Laptop</th>
                            <th class="py-2.5 px-3">Merek</th>
                            <th class="py-2.5 px-3 text-right">Harga</th>
                            <th class="py-2.5 px-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse ($recentLaptops as $laptop)
                            <tr class="hover:bg-surface-subtle/50 transition">
                                <td class="py-2 px-3">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-7 bg-surface-subtle border border-line rounded flex items-center justify-center p-0.5 shrink-0 overflow-hidden">
                                            <img src="{{ $laptop->image_url }}" alt="{{ $laptop->name }}" class="max-h-full max-w-full object-contain">
                                        </div>
                                        <span class="font-medium text-ink truncate max-w-[150px]">{{ $laptop->name }}</span>
                                    </div>
                                </td>
                                <td class="py-2 px-3 text-ink-muted">{{ $laptop->brand }}</td>
                                <td class="py-2 px-3 text-right font-mono text-ink">{{ $laptop->formatted_price }}</td>
                                <td class="py-2 px-3 text-center">
                                    <a href="{{ route('admin.alternatives.edit', $laptop) }}" class="inline-flex items-center gap-1 text-ink-muted hover:text-accent font-medium text-[11px]">
                                        <x-icon name="edit" class="w-3 h-3" />
                                        <span>Ubah</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-ink-muted">Belum ada alternatif laptop.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Audit Trail: Log Aktivitas & Keamanan Terkini -->
    <div class="bg-surface border border-line rounded shadow-2xs">
        <div class="px-4 py-3 border-b border-line flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <div class="flex items-center gap-2">
                    <span class="p-1 rounded bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                        <x-icon name="shield" class="w-3.5 h-3.5" />
                    </span>
                    <h2 class="text-xs font-semibold uppercase tracking-wider text-ink">Audit Log & Aktivitas Keamanan Terkini</h2>
                </div>
                <p class="text-[11px] text-ink-muted mt-0.5">Pemantauan real-time otentikasi akun, mutasi data laptop, kriteria, dan integritas sistem (ISO/IEC 27001).</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.audit-logs.index') }}"
                   class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium text-accent hover:text-accent-hover bg-accent/5 hover:bg-accent/10 border border-accent/20 rounded transition">
                    <span>Lihat Semua Log ({{ $stats['total_audit_logs'] }})</span>
                    <x-icon name="arrow-right" class="w-3 h-3" />
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-surface-subtle border-b border-line text-ink-muted font-medium">
                    <tr>
                        <th class="py-2.5 px-3">Waktu</th>
                        <th class="py-2.5 px-3">Pengguna / Subjek</th>
                        <th class="py-2.5 px-3">Aksi</th>
                        <th class="py-2.5 px-3">Kategori</th>
                        <th class="py-2.5 px-3">Deskripsi Aktivitas</th>
                        <th class="py-2.5 px-3 font-mono">IP Address</th>
                        <th class="py-2.5 px-3 text-right">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($recentAuditLogs as $log)
                        @php
                            $actionBadgeClasses = match($log->severity_color) {
                                'rose' => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/40 dark:text-rose-400 dark:border-rose-900',
                                'amber' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-400 dark:border-amber-900',
                                'emerald' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-900',
                                default => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/40 dark:text-blue-400 dark:border-blue-900',
                            };
                        @endphp
                        <tr class="hover:bg-surface-subtle/50 transition">
                            <td class="py-2.5 px-3 font-mono text-ink-muted whitespace-nowrap">
                                <div class="flex items-center gap-1.5">
                                    <x-icon name="clock" class="w-3.5 h-3.5 text-ink-muted/70 shrink-0" />
                                    <span>{{ $log->created_at->format('d/m/Y H:i:s') }}</span>
                                </div>
                            </td>
                            <td class="py-2.5 px-3 whitespace-nowrap">
                                <div class="flex items-center gap-1.5">
                                    <div class="w-6 h-6 rounded-full bg-surface-subtle border border-line flex items-center justify-center text-[10px] font-mono font-semibold text-ink shrink-0">
                                        {{ strtoupper(substr($log->user_name ?? 'G', 0, 1)) }}
                                    </div>
                                    <div class="leading-tight">
                                        <div class="font-medium text-ink truncate max-w-[130px]">{{ $log->user_name ?? 'Tamu / Sistem' }}</div>
                                        <div class="text-[10px] font-mono text-ink-muted">{{ $log->user_role ?? 'guest' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-2.5 px-3 whitespace-nowrap">
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-mono font-medium border {{ $actionBadgeClasses }}">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="py-2.5 px-3 whitespace-nowrap">
                                <span class="text-ink-muted font-mono uppercase text-[10px]">{{ $log->category }}</span>
                            </td>
                            <td class="py-2.5 px-3 text-ink max-w-xs truncate">
                                {{ $log->description }}
                            </td>
                            <td class="py-2.5 px-3 font-mono text-[11px] text-ink-muted whitespace-nowrap">
                                {{ $log->ip_address ?? '-' }}
                            </td>
                            <td class="py-2.5 px-3 text-right whitespace-nowrap">
                                <a href="{{ route('admin.audit-logs.show', $log) }}"
                                   class="text-accent hover:underline text-[11px] font-medium inline-flex items-center gap-1">
                                    <span>Lihat</span>
                                    <x-icon name="arrow-right" class="w-3 h-3" />
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-ink-muted">
                                Belum ada catatan aktivitas sistem.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Riwayat Perhitungan SPK Terakhir -->
    <div class="bg-surface border border-line rounded shadow-2xs">
        <div class="px-4 py-3 border-b border-line flex items-center justify-between">
            <div>
                <h2 class="text-xs font-semibold uppercase tracking-wider text-ink">Perhitungan SPK Terakhir</h2>
                <p class="text-[11px] text-ink-muted">Hasil evaluasi preferensi yang dijalankan oleh pengguna atau tamu.</p>
            </div>
            <span class="text-[11px] font-mono text-ink-muted">Sesi Terbaru</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-surface-subtle border-b border-line text-ink-muted font-medium">
                    <tr>
                        <th class="py-2.5 px-3">Waktu</th>
                        <th class="py-2.5 px-3">Pengguna</th>
                        <th class="py-2.5 px-3">Profil Evaluasi</th>
                        <th class="py-2.5 px-3">Rekomendasi Utama #1</th>
                        <th class="py-2.5 px-3 text-right">Skor V_i</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($recentResults as $res)
                        @php
                            $topRank = $res->ranking_snapshot[0] ?? null;
                        @endphp
                        <tr class="hover:bg-surface-subtle/50 transition">
                            <td class="py-2.5 px-3 font-mono text-ink-muted">
                                <div class="flex items-center gap-1.5">
                                    <x-icon name="clock" class="w-3.5 h-3.5 text-ink-muted/70 shrink-0" />
                                    <span>{{ $res->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </td>
                            <td class="py-2.5 px-3 text-ink font-medium">
                                <div class="flex items-center gap-1.5">
                                    <x-icon name="user" class="w-3.5 h-3.5 text-ink-muted shrink-0" />
                                    <span>{{ $res->user?->name ?? 'Tamu (Guest)' }}</span>
                                </div>
                            </td>
                            <td class="py-2.5 px-3">
                                <x-badge type="neutral">{{ $res->title ?? 'Kustom' }}</x-badge>
                            </td>
                            <td class="py-2.5 px-3 font-medium text-ink">
                                <div class="flex items-center gap-1.5">
                                    <x-icon name="trophy" class="w-3.5 h-3.5 text-amber-600 shrink-0" />
                                    <span>{{ $topRank['name'] ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="py-2.5 px-3 text-right font-mono text-accent font-medium">
                                {{ isset($topRank['preference_score']) ? number_format($topRank['preference_score'], 4) : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-ink-muted">
                                Belum ada riwayat perhitungan SPK yang tersimpan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
