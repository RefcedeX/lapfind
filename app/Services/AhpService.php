<?php

namespace App\Services;

use InvalidArgumentException;

class AhpService
{
    /**
     * Saaty Random Consistency Index (RI) table for matrix sizes n = 1 .. 15.
     *
     * Reference:
     * Saaty, T. L. (1980). The Analytic Hierarchy Process. McGraw-Hill, New York.
     *
     * @var array<int, float>
     */
    public const array RANDOM_INDEX = [
        1 => 0.00,
        2 => 0.00,
        3 => 0.58,
        4 => 0.90,
        5 => 1.12,
        6 => 1.24,
        7 => 1.32,
        8 => 1.41,
        9 => 1.45,
        10 => 1.49,
        11 => 1.51,
        12 => 1.48,
        13 => 1.56,
        14 => 1.57,
        15 => 1.59,
    ];

    /**
     * Threshold for consistency ratio.
     */
    public const float CONSISTENCY_THRESHOLD = 0.10;

    /**
     * Calculate AHP priority weights and consistency ratio from a pairwise comparison matrix.
     *
     * Steps performed:
     * 1. Validate and complete reciprocal matrix (a_ji = 1 / a_ij, a_ii = 1.0).
     * 2. Calculate column sums.
     * 3. Normalize matrix (divide each cell by its column sum).
     * 4. Calculate priority vector (row averages of normalized matrix).
     * 5. Multiply original matrix with priority vector (weighted sum vector).
     * 6. Calculate consistency vector, lambda max, CI, and CR.
     *
     * @param  array<int, string>  $criteriaKeys  List of unique criteria identifiers (e.g. ['C1', 'C2', 'C3'])
     * @param  array<string, array<string, float|int>>  $rawMatrix  Pairwise comparison matrix or partial pairwise array
     * @return array<string, mixed> All intermediate calculation tables and final consistency metrics
     */
    public function calculate(array $criteriaKeys, array $rawMatrix): array
    {
        $n = count($criteriaKeys);

        if ($n === 0) {
            throw new InvalidArgumentException('Daftar kriteria tidak boleh kosong.');
        }

        // 1. Bangun matriks perbandingan berpasangan lengkap dengan sifat resiprokal
        $matrix = $this->buildFullMatrix($criteriaKeys, $rawMatrix);

        // Kasus trivial n = 1
        if ($n === 1) {
            $singleKey = $criteriaKeys[0];

            return [
                'criteria_keys' => $criteriaKeys,
                'n' => 1,
                'matrix' => $matrix,
                'column_sums' => [$singleKey => 1.0],
                'normalized_matrix' => [$singleKey => [$singleKey => 1.0]],
                'weights' => [$singleKey => 1.0],
                'weighted_sum_vector' => [$singleKey => 1.0],
                'consistency_vector' => [$singleKey => 1.0],
                'lambda_max' => 1.0,
                'ci' => 0.0,
                'ri' => 0.0,
                'cr' => 0.0,
                'is_consistent' => true,
                'consistency_label' => 'Konsisten (CR <= 0.1)',
            ];
        }

        // 2. Jumlah tiap kolom: S_j = sum_i(a_ij)
        $columnSums = [];
        foreach ($criteriaKeys as $col) {
            $sum = 0.0;
            foreach ($criteriaKeys as $row) {
                $sum += (float) $matrix[$row][$col];
            }
            $columnSums[$col] = $sum;
        }

        // 3. Normalisasi matriks: b_ij = a_ij / S_j
        $normalizedMatrix = [];
        foreach ($criteriaKeys as $row) {
            $normalizedMatrix[$row] = [];
            foreach ($criteriaKeys as $col) {
                $colSum = $columnSums[$col];
                $normalizedMatrix[$row][$col] = $colSum > 0 ? (float) $matrix[$row][$col] / $colSum : 0.0;
            }
        }

        // 4. Bobot prioritas (Priority Vector): w_i = (1/n) * sum_j(b_ij)
        $weights = [];
        foreach ($criteriaKeys as $row) {
            $rowSum = array_sum($normalizedMatrix[$row]);
            $weights[$row] = $rowSum / $n;
        }

        // 5. Hitung Weighted Sum Vector: A * w
        $weightedSumVector = [];
        foreach ($criteriaKeys as $row) {
            $sum = 0.0;
            foreach ($criteriaKeys as $col) {
                $sum += ((float) $matrix[$row][$col]) * $weights[$col];
            }
            $weightedSumVector[$row] = $sum;
        }

        // 6. Hitung rasio konsistensi per baris: t_i = (A*w)_i / w_i
        $consistencyVector = [];
        $totalRatio = 0.0;
        foreach ($criteriaKeys as $row) {
            $ratio = $weights[$row] > 0 ? $weightedSumVector[$row] / $weights[$row] : 0.0;
            $consistencyVector[$row] = $ratio;
            $totalRatio += $ratio;
        }

        // 7. Hitung lambda_max, CI, dan CR
        $lambdaMax = $totalRatio / $n;
        $ci = ($lambdaMax - $n) / ($n - 1);
        $ri = self::RANDOM_INDEX[$n] ?? (1.98 * ($n - 2) / $n);
        $cr = $ri > 0 ? $ci / $ri : 0.0;

        // Toleransi floating point negatif kecil (misal -0.0000001 diubah ke 0)
        if ($ci < 0 && $ci > -0.00001) {
            $ci = 0.0;
            $cr = 0.0;
        }

        $isConsistent = $cr <= self::CONSISTENCY_THRESHOLD;

        return [
            'criteria_keys' => $criteriaKeys,
            'n' => $n,
            'matrix' => $matrix,
            'column_sums' => $columnSums,
            'normalized_matrix' => $normalizedMatrix,
            'weights' => $weights,
            'weighted_sum_vector' => $weightedSumVector,
            'consistency_vector' => $consistencyVector,
            'lambda_max' => round($lambdaMax, 6),
            'ci' => round($ci, 6),
            'ri' => $ri,
            'cr' => round($cr, 6),
            'is_consistent' => $isConsistent,
            'consistency_label' => $isConsistent ? 'Konsisten (CR <= 0.1)' : 'Tidak Konsisten (CR > 0.1)',
        ];
    }

    /**
     * Build full square reciprocal comparison matrix from input pairs.
     * Ensures diagonal is 1.0 and a_ji = 1 / a_ij.
     *
     * @param  array<int, string>  $keys
     * @param  array<string, array<string, float|int>>  $raw
     * @return array<string, array<string, float>>
     */
    public function buildFullMatrix(array $keys, array $raw): array
    {
        $matrix = [];

        foreach ($keys as $i) {
            $matrix[$i] = [];
            foreach ($keys as $j) {
                if ($i === $j) {
                    $matrix[$i][$j] = 1.0;
                } elseif (isset($raw[$i][$j]) && (float) $raw[$i][$j] > 0) {
                    $val = (float) $raw[$i][$j];
                    $matrix[$i][$j] = $val;
                    $matrix[$j][$i] = 1.0 / $val;
                } elseif (isset($raw[$j][$i]) && (float) $raw[$j][$i] > 0) {
                    $val = (float) $raw[$j][$i];
                    $matrix[$j][$i] = $val;
                    $matrix[$i][$j] = 1.0 / $val;
                } else {
                    // Default bila belum diisi adalah sama penting (1.0)
                    $matrix[$i][$j] = 1.0;
                    $matrix[$j][$i] = 1.0;
                }
            }
        }

        return $matrix;
    }

    /**
     * Provide predefined criteria pairwise comparisons based on student profiles.
     *
     * Profile 1: Kuliah Umum (fokus harga terjangkau, bobot ringan, baterai awet)
     * Profile 2: Coding & IT (fokus RAM besar, performa prosesor tinggi, penyimpanan)
     * Profile 3: Desain Grafis & Multimedia (fokus prosesor/GPU, layar, RAM)
     *
     * @param  array<int, string>  $keys
     * @return array<string, array<string, float>>
     */
    public function getProfileDefaultComparisons(string $profileName, array $keys): array
    {
        // Default netral (semua 1.0)
        $matrix = [];
        foreach ($keys as $i) {
            foreach ($keys as $j) {
                $matrix[$i][$j] = 1.0;
            }
        }

        switch (strtolower($profileName)) {
            case 'coding':
            case 'pemrograman':
                // C1 Harga, C2 Performa CPU, C3 RAM, C4 Storage, C5 Bobot, C6 Baterai
                // Mahasiswa coding butuh RAM (C3) dan Performa (C2) paling utama
                $matrix['C3']['C1'] = 3.0; // RAM lebih penting dari Harga
                $matrix['C3']['C2'] = 2.0; // RAM sedikit lebih penting dari Performa
                $matrix['C3']['C4'] = 3.0; // RAM lebih penting dari Storage
                $matrix['C3']['C5'] = 4.0; // RAM jauh lebih penting dari Bobot
                $matrix['C3']['C6'] = 3.0; // RAM lebih penting dari Baterai

                $matrix['C2']['C1'] = 2.0; // CPU lebih penting dari Harga
                $matrix['C2']['C4'] = 2.0; // CPU lebih penting dari Storage
                $matrix['C2']['C5'] = 3.0; // CPU lebih penting dari Bobot
                $matrix['C2']['C6'] = 2.0; // CPU lebih penting dari Baterai

                $matrix['C4']['C1'] = 1.0;
                $matrix['C4']['C5'] = 2.0; // Storage lebih penting dari Bobot
                $matrix['C4']['C6'] = 1.0;

                $matrix['C1']['C5'] = 2.0; // Harga lebih penting dari Bobot
                $matrix['C1']['C6'] = 1.0;

                $matrix['C6']['C5'] = 2.0; // Baterai lebih penting dari Bobot
                break;

            case 'desain':
            case 'multimedia':
                // Desain butuh Performa CPU/GPU (C2), RAM (C3), dan Storage (C4)
                $matrix['C2']['C1'] = 3.0;
                $matrix['C2']['C3'] = 2.0;
                $matrix['C2']['C4'] = 3.0;
                $matrix['C2']['C5'] = 4.0;
                $matrix['C2']['C6'] = 3.0;

                $matrix['C3']['C1'] = 2.0;
                $matrix['C3']['C4'] = 2.0;
                $matrix['C3']['C5'] = 3.0;
                $matrix['C3']['C6'] = 2.0;

                $matrix['C4']['C1'] = 1.0;
                $matrix['C4']['C5'] = 2.0;
                $matrix['C4']['C6'] = 1.0;

                $matrix['C1']['C5'] = 2.0;
                $matrix['C6']['C5'] = 2.0;
                break;

            case 'gaming':
            case 'game':
                // Gaming butuh Performa CPU/GPU monster (C2) dan RAM (C3) paling utama, toleransi bobot berat
                $matrix['C2']['C1'] = 3.0;
                $matrix['C2']['C3'] = 2.0;
                $matrix['C2']['C4'] = 3.0;
                $matrix['C2']['C5'] = 5.0;
                $matrix['C2']['C6'] = 5.0;

                $matrix['C3']['C1'] = 2.0;
                $matrix['C3']['C4'] = 2.0;
                $matrix['C3']['C5'] = 4.0;
                $matrix['C3']['C6'] = 4.0;

                $matrix['C4']['C1'] = 2.0;
                $matrix['C4']['C5'] = 3.0;
                $matrix['C4']['C6'] = 3.0;

                $matrix['C1']['C5'] = 2.0;
                $matrix['C1']['C6'] = 2.0;

                $matrix['C6']['C5'] = 1.0;
                break;

            case 'umum':
            case 'kuliah':
            default:
                // Mahasiswa umum: Harga (C1), Baterai (C6), Bobot (C5) paling prioritas
                $matrix['C1']['C2'] = 3.0; // Harga lebih penting dari Performa
                $matrix['C1']['C3'] = 3.0; // Harga lebih penting dari RAM
                $matrix['C1']['C4'] = 4.0; // Harga lebih penting dari Storage
                $matrix['C1']['C5'] = 2.0; // Harga lebih penting dari Bobot
                $matrix['C1']['C6'] = 2.0; // Harga lebih penting dari Baterai

                $matrix['C6']['C2'] = 3.0; // Baterai lebih penting dari CPU
                $matrix['C6']['C3'] = 2.0;
                $matrix['C6']['C4'] = 3.0;
                $matrix['C6']['C5'] = 2.0; // Baterai lebih penting dari Bobot

                $matrix['C5']['C2'] = 2.0; // Bobot ringan lebih penting dari CPU monster
                $matrix['C5']['C3'] = 2.0;
                $matrix['C5']['C4'] = 2.0;

                $matrix['C3']['C4'] = 2.0;
                $matrix['C2']['C4'] = 2.0;
                break;
        }

        return $this->buildFullMatrix($keys, $matrix);
    }
}
