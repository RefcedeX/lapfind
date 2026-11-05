<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Laporan SPK Pemilihan Laptop' }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #1C1B19;
            line-height: 1.4;
            margin: 0;
            padding: 15px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #1F5C4A;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header h1 {
            font-size: 14px;
            font-weight: bold;
            margin: 0 0 4px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #1C1B19;
        }
        .header h2 {
            font-size: 12px;
            font-weight: normal;
            margin: 0;
            color: #1F5C4A;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 15px;
            font-size: 10px;
        }
        .meta-table td {
            padding: 3px 0;
        }
        .section-title {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #E5E2DA;
            padding-bottom: 3px;
            margin-top: 15px;
            margin-bottom: 8px;
            color: #1F5C4A;
        }
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 10px;
        }
        table.data th, table.data td {
            border: 1px solid #D1CFC7;
            padding: 5px 6px;
        }
        table.data th {
            background-color: #F4F2EC;
            font-weight: bold;
            text-align: left;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .font-mono {
            font-family: 'Courier New', Courier, monospace;
        }
        .badge-best {
            background-color: #EBF3F0;
            color: #1F5C4A;
            font-weight: bold;
            padding: 2px 4px;
            border: 1px solid #C7DCD4;
        }
        .summary-box {
            background-color: #FAF9F6;
            border: 1px solid #E5E2DA;
            padding: 8px;
            margin-bottom: 12px;
            font-size: 10px;
        }
        .footer {
            margin-top: 25px;
            border-top: 1px solid #E5E2DA;
            padding-top: 8px;
            font-size: 9px;
            color: #737067;
            text-align: center;
        }
    </style>
</head>
<body>

    <!-- Header Dokumen -->
    <div class="header">
        <h1>Laporan Hasil Sistem Pendukung Keputusan (SPK)</h1>
        <h2>Pemilihan Kriteria Laptop Mahasiswa — Metode AHP & TOPSIS</h2>
    </div>

    <!-- Identitas Sesi Evaluasi -->
    <table class="meta-table">
        <tr>
            <td style="width: 20%;"><strong>Profil Kebutuhan:</strong></td>
            <td style="width: 35%;">{{ $title ?? 'Kustom' }}</td>
            <td style="width: 20%;"><strong>Waktu Cetak:</strong></td>
            <td style="width: 25%; font-family: monospace;">{{ \Carbon\Carbon::parse($createdAt)->format('d/m/Y H:i') }} WIB</td>
        </tr>
        <tr>
            <td><strong>Evaluator / Pengguna:</strong></td>
            <td>{{ $user ?? 'Tamu' }}</td>
            <td><strong>Basis Alternatif:</strong></td>
            <td>{{ count($rankings) }} Unit Laptop</td>
        </tr>
    </table>

    <!-- Bagian 1: Bobot Kriteria AHP -->
    <div class="section-title">1. Pembobotan Kriteria (Metode AHP Saaty)</div>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 40px;">Kode</th>
                <th>Nama Kriteria</th>
                <th style="width: 70px;">Sifat</th>
                <th style="width: 60px;">Satuan</th>
                <th class="text-right" style="width: 90px;">Bobot (w_j)</th>
                <th class="text-right" style="width: 80px;">Persentase</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($criteria as $crit)
                @php
                    $w = $weights[$crit->code] ?? 0;
                @endphp
                <tr>
                    <td class="font-mono text-center"><strong>{{ $crit->code }}</strong></td>
                    <td>{{ $crit->name }}</td>
                    <td class="text-center">{{ ucfirst($crit->type) }}</td>
                    <td class="text-center font-mono">{{ $crit->unit ?? '-' }}</td>
                    <td class="text-right font-mono">{{ number_format($w, 4) }}</td>
                    <td class="text-right font-mono">{{ number_format($w * 100, 2) }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if (!empty($steps['ahp']))
        <div class="summary-box">
            <strong>Uji Rasio Konsistensi (Saaty Consistency Check):</strong><br>
            &lambda; max = <span class="font-mono">{{ number_format($steps['ahp']['lambda_max'] ?? 0, 4) }}</span> |
            CI = <span class="font-mono">{{ number_format($steps['ahp']['ci'] ?? 0, 4) }}</span> |
            RI = <span class="font-mono">{{ number_format($steps['ahp']['ri'] ?? 0, 2) }}</span> |
            <strong>CR = <span class="font-mono">{{ number_format($steps['ahp']['cr'] ?? 0, 4) }}</span></strong>
            (Status: <strong>{{ ($steps['ahp']['is_consistent'] ?? true) ? 'KONSISTEN (CR <= 0.10)' : 'TIDAK KONSISTEN (CR > 0.10)' }}</strong>)
        </div>
    @endif

    <!-- Bagian 2: Hasil Perankingan TOPSIS -->
    <div class="section-title">2. Hasil Perankingan Alternatif Laptop (Metode TOPSIS)</div>

    <table class="data">
        <thead>
            <tr>
                <th class="text-center" style="width: 35px;">Rank</th>
                <th>Model & Merek Laptop</th>
                <th class="text-right" style="width: 95px;">Harga Pasar</th>
                <th class="text-right font-mono" style="width: 65px;">Jarak D+</th>
                <th class="text-right font-mono" style="width: 65px;">Jarak D-</th>
                <th class="text-right font-mono" style="width: 80px;">Skor V_i</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rankings as $item)
                @php
                    $lap = $laptops[$item['alternative_id']] ?? null;
                    $isTop = $item['rank'] === 1;
                @endphp
                <tr style="{{ $isTop ? 'background-color: #F0F7F4; font-weight: bold;' : '' }}">
                    <td class="text-center font-mono">
                        @if ($isTop)
                            <span class="badge-best">#1</span>
                        @else
                            {{ $item['rank'] }}
                        @endif
                    </td>
                    <td>
                        {{ $item['name'] }}
                        <span style="color: #737067; font-size: 9px;">({{ $lap?->brand }})</span>
                    </td>
                    <td class="text-right font-mono">{{ $lap?->formatted_price ?? '-' }}</td>
                    <td class="text-right font-mono">{{ number_format($item['d_plus'], 4) }}</td>
                    <td class="text-right font-mono">{{ number_format($item['d_minus'], 4) }}</td>
                    <td class="text-right font-mono" style="{{ $isTop ? 'color: #1F5C4A; font-size: 11px;' : '' }}">
                        {{ number_format($item['preference_score'], 4) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Solusi Ideal TOPSIS -->
    @if (!empty($steps['topsis']['ideal_positive']))
        <div class="section-title">3. Titik Solusi Ideal TOPSIS</div>
        <table class="data">
            <thead>
                <tr>
                    <th>Solusi</th>
                    @foreach ($criteria as $crit)
                        <th class="text-right font-mono">{{ $crit->code }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Ideal Positif (A+)</strong></td>
                    @foreach ($criteria as $crit)
                        <td class="text-right font-mono">
                            {{ isset($steps['topsis']['ideal_positive'][$crit->code]) ? number_format($steps['topsis']['ideal_positive'][$crit->code], 4) : '-' }}
                        </td>
                    @endforeach
                </tr>
                <tr>
                    <td><strong>Ideal Negatif (A-)</strong></td>
                    @foreach ($criteria as $crit)
                        <td class="text-right font-mono">
                            {{ isset($steps['topsis']['ideal_negative'][$crit->code]) ? number_format($steps['topsis']['ideal_negative'][$crit->code], 4) : '-' }}
                        </td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    @endif

    <div class="footer">
        Dokumen ini dihasilkan secara otomatis oleh Aplikasi SPK Pemilihan Laptop Mahasiswa.<br>
        Fokus Perancangan Sistem (Rekayasa Web) dan Transparansi Algoritma Komputasi (Manajemen Sains).
    </div>

</body>
</html>
