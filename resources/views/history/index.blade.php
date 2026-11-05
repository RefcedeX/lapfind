@extends('layouts.public', ['title' => 'Riwayat Evaluasi SPK'])

@section('content')
<div class="space-y-6">

    {{-- ──────────────────────────────────────────────────────────────────
         HEADER
    ────────────────────────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-[#E2E8F0] pb-5">
        <div>
            <div class="inline-flex items-center gap-2 text-[11px] font-mono uppercase tracking-wider text-[#2563EB] font-bold mb-1">
                <span>Rekap Sesi Penilaian</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">
                Riwayat Evaluasi SPK Anda
            </h1>
            <p class="text-xs text-[#6B7280] mt-1 max-w-xl leading-relaxed">
                Daftar sesi komputasi pembobotan AHP dan perankingan TOPSIS yang tersimpan di akun Anda.
            </p>
        </div>
        <a href="{{ route('spk.index') }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#2563EB] hover:bg-[#1D4ED8] text-white text-xs font-bold rounded-md transition shadow-2xs self-start sm:self-auto cursor-pointer">
            <span>+ Mulai Evaluasi Baru</span>
        </a>
    </div>

    {{-- ──────────────────────────────────────────────────────────────────
         SAVED RESULTS TABLE
    ────────────────────────────────────────────────────────────────────── --}}
    <div class="bg-white border border-[#E2E8F0] rounded-lg overflow-hidden shadow-2xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs divide-y divide-[#E2E8F0]">
                <thead class="bg-[#F8FAFC] text-[#6B7280] font-medium font-mono text-[11px]">
                    <tr>
                        <th class="py-3 px-5 w-12">No</th>
                        <th class="py-3 px-5 font-mono">Waktu Komputasi</th>
                        <th class="py-3 px-5">Nama Sesi / Profil</th>
                        <th class="py-3 px-5">Rekomendasi Utama (#1)</th>
                        <th class="py-3 px-5 text-right font-mono">Skor V_1</th>
                        <th class="py-3 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8F0]">
                    @forelse ($results as $index => $res)
                        @php
                            $topRank = $res->ranking_snapshot[0] ?? null;
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/60 transition">
                            <td class="py-3.5 px-5 font-mono text-[#6B7280]">
                                {{ $results->firstItem() + $index }}
                            </td>
                            <td class="py-3.5 px-5 font-mono text-[#6B7280]">
                                {{ $res->created_at->format('d/m/Y H:i') }} WIB
                            </td>
                            <td class="py-3.5 px-5 font-bold text-slate-900">
                                {{ $res->title ?? 'Evaluasi SPK' }}
                            </td>
                            <td class="py-3.5 px-5">
                                <span class="font-bold text-slate-900">{{ $topRank['name'] ?? '-' }}</span>
                            </td>
                            <td class="py-3.5 px-5 text-right font-mono font-bold text-[#2563EB]">
                                {{ isset($topRank['preference_score']) ? number_format($topRank['preference_score'], 4) : '-' }}
                            </td>
                            <td class="py-3.5 px-5 text-right">
                                <div class="inline-flex items-center gap-1.5 justify-end">
                                    <a href="{{ route('spk.result', $res) }}"
                                       class="px-2.5 py-1 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 border border-[#E2E8F0] dark:border-slate-700 rounded text-[11px] font-semibold text-slate-700 dark:text-slate-200 hover:text-slate-900 dark:hover:text-white transition">
                                        Lihat
                                    </a>
                                    <a href="{{ route('spk.export_pdf', $res) }}"
                                       class="px-2.5 py-1 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 border border-[#2563EB]/30 dark:border-blue-500/30 rounded text-[11px] font-bold text-[#2563EB] dark:text-blue-400 hover:text-[#1D4ED8] dark:hover:text-blue-300 transition">
                                        PDF
                                    </a>
                                    <form action="{{ route('history.destroy', $res) }}"
                                          method="POST"
                                          class="inline"
                                          onsubmit="return confirm('Hapus riwayat evaluasi ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="px-2.5 py-1 bg-red-50 hover:bg-red-100 border border-red-200 rounded text-[11px] font-semibold text-red-600 hover:text-red-800 transition cursor-pointer"
                                                title="Hapus Riwayat">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-[#6B7280]">
                                <div class="max-w-sm mx-auto space-y-3">
                                    <div class="w-12 h-12 mx-auto rounded-md bg-[#F8FAFC] border border-[#E2E8F0] flex items-center justify-center text-slate-400">
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                            <circle cx="12" cy="12" r="10"/>
                                            <polyline points="12 6 12 12 16 14"/>
                                        </svg>
                                    </div>
                                    <div class="text-sm font-bold text-slate-900">Belum Ada Riwayat Evaluasi</div>
                                    <p class="text-xs text-[#6B7280]">
                                        Anda belum menjalankan sesi perhitungan SPK. Tentukan bobot kriteria AHP sekarang untuk mendapatkan rekomendasi laptop terbaik.
                                    </p>
                                    <div class="pt-2">
                                        <a href="{{ route('spk.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#2563EB] hover:bg-[#1D4ED8] text-white text-xs font-bold rounded-md transition shadow-2xs">
                                            <span>Mulai Evaluasi SPK Pertama &rarr;</span>
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($results->hasPages())
            <div class="px-5 py-3.5 border-t border-[#E2E8F0] bg-[#F8FAFC]">
                {{ $results->links() }}
            </div>
        @endif
    </div>

</div>
@endsection