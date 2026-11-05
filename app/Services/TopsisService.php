<?php

namespace App\Services;

use InvalidArgumentException;

class TopsisService
{
    /**
     * Execute TOPSIS method on given decision matrix, criteria metadata, and weights.
     *
     * @param  array<string|int, array<string, float|int>>  $decisionMatrix  Rows are alternative identifiers, columns are criteria codes: [alt_id => [crit_code => value]]
     * @param  array<string, string>  $criteriaTypes  Mapping of criteria codes to type: [crit_code => 'benefit'|'cost']
     * @param  array<string, float>  $weights  Mapping of criteria codes to weights: [crit_code => weight] (sum approx 1.0)
     * @param  array<string|int, string>  $alternativeNames  Optional mapping of alternative identifiers to human-readable names
     * @return array<string, mixed> All intermediate calculation tables, ideal solutions, Euclidean distances, and sorted rankings
     */
    public function calculate(
        array $decisionMatrix,
        array $criteriaTypes,
        array $weights,
        array $alternativeNames = []
    ): array {
        if (empty($decisionMatrix)) {
            throw new InvalidArgumentException('Matriks keputusan alternatif tidak boleh kosong.');
        }

        if (empty($criteriaTypes)) {
            throw new InvalidArgumentException('Tipe kriteria (benefit/cost) tidak boleh kosong.');
        }

        $alternativeIds = array_keys($decisionMatrix);
        $criteriaKeys = array_keys($criteriaTypes);

        // Pastikan bobot terisi untuk semua kriteria
        foreach ($criteriaKeys as $crit) {
            if (! isset($weights[$crit])) {
                throw new InvalidArgumentException("Bobot untuk kriteria [{$crit}] tidak ditemukan.");
            }
        }

        // 1. Matriks Keputusan (X)
        // Disusun ulang untuk menjamin konsistensi urutan kolom dan float casting
        $matrixX = [];
        foreach ($alternativeIds as $altId) {
            $matrixX[$altId] = [];
            foreach ($criteriaKeys as $crit) {
                $val = $decisionMatrix[$altId][$crit] ?? 0;
                $matrixX[$altId][$crit] = (float) $val;
            }
        }

        // 2. Pembagi Normalisasi Vektor: divisor_j = sqrt(sum_i(x_ij^2))
        $divisors = [];
        foreach ($criteriaKeys as $crit) {
            $sumSquares = 0.0;
            foreach ($alternativeIds as $altId) {
                $sumSquares += pow($matrixX[$altId][$crit], 2);
            }
            $divisors[$crit] = sqrt($sumSquares);
        }

        // Matriks Ternormalisasi (R): r_ij = x_ij / divisor_j
        $normalizedMatrix = [];
        foreach ($alternativeIds as $altId) {
            $normalizedMatrix[$altId] = [];
            foreach ($criteriaKeys as $crit) {
                $div = $divisors[$crit];
                $normalizedMatrix[$altId][$crit] = $div > 0 ? $matrixX[$altId][$crit] / $div : 0.0;
            }
        }

        // 3. Matriks Ternormalisasi Terbobot (Y): y_ij = w_j * r_ij
        $weightedMatrix = [];
        foreach ($alternativeIds as $altId) {
            $weightedMatrix[$altId] = [];
            foreach ($criteriaKeys as $crit) {
                $weightedMatrix[$altId][$crit] = $normalizedMatrix[$altId][$crit] * (float) $weights[$crit];
            }
        }

        // 4. Solusi Ideal Positif (A+) dan Solusi Ideal Negatif (A-)
        $idealPositive = [];
        $idealNegative = [];

        foreach ($criteriaKeys as $crit) {
            $colValues = [];
            foreach ($alternativeIds as $altId) {
                $colValues[] = $weightedMatrix[$altId][$crit];
            }

            $maxVal = max($colValues);
            $minVal = min($colValues);

            $type = strtolower($criteriaTypes[$crit]);
            if ($type === 'cost') {
                // Untuk cost: ideal positif adalah nilai minimum, negatif adalah maksimum
                $idealPositive[$crit] = $minVal;
                $idealNegative[$crit] = $maxVal;
            } else {
                // Untuk benefit: ideal positif adalah nilai maksimum, negatif adalah minimum
                $idealPositive[$crit] = $maxVal;
                $idealNegative[$crit] = $minVal;
            }
        }

        // 5. Jarak Euclidean ke Solusi Ideal Positif (D+) dan Negatif (D-)
        $distancePositive = [];
        $distanceNegative = [];

        foreach ($alternativeIds as $altId) {
            $sumDistPos = 0.0;
            $sumDistNeg = 0.0;

            foreach ($criteriaKeys as $crit) {
                $val = $weightedMatrix[$altId][$crit];
                $sumDistPos += pow($val - $idealPositive[$crit], 2);
                $sumDistNeg += pow($val - $idealNegative[$crit], 2);
            }

            $distancePositive[$altId] = sqrt($sumDistPos);
            $distanceNegative[$altId] = sqrt($sumDistNeg);
        }

        // 6. Nilai Preferensi (V_i) = D-_i / (D+_i + D-_i)
        $preferenceValues = [];
        $rankings = [];

        foreach ($alternativeIds as $altId) {
            $dPlus = $distancePositive[$altId];
            $dMinus = $distanceNegative[$altId];
            $totalDist = $dPlus + $dMinus;

            $score = $totalDist > 0 ? $dMinus / $totalDist : 0.0;
            $preferenceValues[$altId] = $score;

            $rankings[] = [
                'alternative_id' => $altId,
                'name' => $alternativeNames[$altId] ?? "Alternatif #{$altId}",
                'd_plus' => $dPlus,
                'd_minus' => $dMinus,
                'preference_score' => $score,
            ];
        }

        // Urutkan menurun berdasarkan preference_score
        usort($rankings, function (array $a, array $b): int {
            if ($b['preference_score'] === $a['preference_score']) {
                return 0;
            }

            return ($b['preference_score'] > $a['preference_score']) ? 1 : -1;
        });

        // Tetapkan peringkat (rank 1, 2, 3..)
        foreach ($rankings as $index => &$item) {
            $item['rank'] = $index + 1;
        }
        unset($item);

        return [
            'criteria_keys' => $criteriaKeys,
            'criteria_types' => $criteriaTypes,
            'weights' => $weights,
            'decision_matrix' => $matrixX,
            'column_divisors' => $divisors,
            'normalized_matrix' => $normalizedMatrix,
            'weighted_matrix' => $weightedMatrix,
            'ideal_positive' => $idealPositive,
            'ideal_negative' => $idealNegative,
            'distance_positive' => $distancePositive,
            'distance_negative' => $distanceNegative,
            'preference_values' => $preferenceValues,
            'rankings' => $rankings,
        ];
    }
}
