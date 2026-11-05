<?php

namespace Tests\Unit;

use App\Services\TopsisService;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class TopsisServiceTest extends TestCase
{
    private TopsisService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TopsisService;
    }

    /**
     * Test TOPSIS algorithm with a small 3-alternative, 3-criteria dataset
     * calculated dynamically in the test to compare with service output.
     */
    public function test_topsis_calculation_matches_independent_mathematical_model(): void
    {
        // 3 Alternatif laptop dummy sederhana
        $matrix = [
            'A1' => ['C1' => 10.0, 'C2' => 8.0, 'C3' => 4.0],
            'A2' => ['C1' => 12.0, 'C2' => 9.0, 'C3' => 8.0],
            'A3' => ['C1' => 8.0,  'C2' => 6.0, 'C3' => 16.0],
        ];

        // C1 = Harga (Cost), C2 = Performa (Benefit), C3 = RAM (Benefit)
        $criteriaTypes = [
            'C1' => 'cost',
            'C2' => 'benefit',
            'C3' => 'benefit',
        ];

        $weights = [
            'C1' => 0.50,
            'C2' => 0.30,
            'C3' => 0.20,
        ];

        $names = [
            'A1' => 'Laptop Hemat',
            'A2' => 'Laptop Performa',
            'A3' => 'Laptop RAM Jumbo',
        ];

        $result = $this->service->calculate($matrix, $criteriaTypes, $weights, $names);

        // 1. Hitung independen pembagi vektor (divisors): sqrt(sum(x^2))
        $expectedDivC1 = sqrt(pow(10.0, 2) + pow(12.0, 2) + pow(8.0, 2));
        $expectedDivC2 = sqrt(pow(8.0, 2) + pow(9.0, 2) + pow(6.0, 2));
        $expectedDivC3 = sqrt(pow(4.0, 2) + pow(8.0, 2) + pow(16.0, 2));

        $this->assertEqualsWithDelta($expectedDivC1, $result['column_divisors']['C1'], 0.0001);
        $this->assertEqualsWithDelta($expectedDivC2, $result['column_divisors']['C2'], 0.0001);
        $this->assertEqualsWithDelta($expectedDivC3, $result['column_divisors']['C3'], 0.0001);

        // 2. Hitung independen matriks ternormalisasi terbobot (Y)
        $expectedY = [];
        foreach (['A1', 'A2', 'A3'] as $alt) {
            $expectedY[$alt]['C1'] = (float) $weights['C1'] * ($matrix[$alt]['C1'] / $expectedDivC1);
            $expectedY[$alt]['C2'] = (float) $weights['C2'] * ($matrix[$alt]['C2'] / $expectedDivC2);
            $expectedY[$alt]['C3'] = (float) $weights['C3'] * ($matrix[$alt]['C3'] / $expectedDivC3);

            $this->assertEqualsWithDelta($expectedY[$alt]['C1'], $result['weighted_matrix'][$alt]['C1'], 0.0001);
            $this->assertEqualsWithDelta($expectedY[$alt]['C2'], $result['weighted_matrix'][$alt]['C2'], 0.0001);
            $this->assertEqualsWithDelta($expectedY[$alt]['C3'], $result['weighted_matrix'][$alt]['C3'], 0.0001);
        }

        // 3. Hitung independen solusi ideal positif (A+) dan negatif (A-)
        // C1 (cost) => A+ = min(Y_C1), A- = max(Y_C1)
        // C2 (benefit) => A+ = max(Y_C2), A- = min(Y_C2)
        // C3 (benefit) => A+ = max(Y_C3), A- = min(Y_C3)
        $expectedIdealPos = [
            'C1' => min($expectedY['A1']['C1'], $expectedY['A2']['C1'], $expectedY['A3']['C1']),
            'C2' => max($expectedY['A1']['C2'], $expectedY['A2']['C2'], $expectedY['A3']['C2']),
            'C3' => max($expectedY['A1']['C3'], $expectedY['A2']['C3'], $expectedY['A3']['C3']),
        ];

        $expectedIdealNeg = [
            'C1' => max($expectedY['A1']['C1'], $expectedY['A2']['C1'], $expectedY['A3']['C1']),
            'C2' => min($expectedY['A1']['C2'], $expectedY['A2']['C2'], $expectedY['A3']['C2']),
            'C3' => min($expectedY['A1']['C3'], $expectedY['A2']['C3'], $expectedY['A3']['C3']),
        ];

        $this->assertEqualsWithDelta($expectedIdealPos['C1'], $result['ideal_positive']['C1'], 0.0001);
        $this->assertEqualsWithDelta($expectedIdealPos['C2'], $result['ideal_positive']['C2'], 0.0001);
        $this->assertEqualsWithDelta($expectedIdealPos['C3'], $result['ideal_positive']['C3'], 0.0001);

        $this->assertEqualsWithDelta($expectedIdealNeg['C1'], $result['ideal_negative']['C1'], 0.0001);
        $this->assertEqualsWithDelta($expectedIdealNeg['C2'], $result['ideal_negative']['C2'], 0.0001);
        $this->assertEqualsWithDelta($expectedIdealNeg['C3'], $result['ideal_negative']['C3'], 0.0001);

        // 4. Hitung independen jarak Euclidean dan preferensi V_i
        $expectedV = [];
        foreach (['A1', 'A2', 'A3'] as $alt) {
            $dPos = sqrt(
                pow($expectedY[$alt]['C1'] - $expectedIdealPos['C1'], 2) +
                pow($expectedY[$alt]['C2'] - $expectedIdealPos['C2'], 2) +
                pow($expectedY[$alt]['C3'] - $expectedIdealPos['C3'], 2)
            );
            $dNeg = sqrt(
                pow($expectedY[$alt]['C1'] - $expectedIdealNeg['C1'], 2) +
                pow($expectedY[$alt]['C2'] - $expectedIdealNeg['C2'], 2) +
                pow($expectedY[$alt]['C3'] - $expectedIdealNeg['C3'], 2)
            );

            $this->assertEqualsWithDelta($dPos, $result['distance_positive'][$alt], 0.0001);
            $this->assertEqualsWithDelta($dNeg, $result['distance_negative'][$alt], 0.0001);

            $v = $dNeg / ($dPos + $dNeg);
            $expectedV[$alt] = $v;
            $this->assertEqualsWithDelta($v, $result['preference_values'][$alt], 0.0001);
        }

        // 5. Verifikasi perankingan terurut menurun
        $this->assertCount(3, $result['rankings']);
        $this->assertEquals(1, $result['rankings'][0]['rank']);
        $this->assertEquals(2, $result['rankings'][1]['rank']);
        $this->assertEquals(3, $result['rankings'][2]['rank']);

        // Skor rank 1 harus lebih besar atau sama dengan rank 2, dst
        $this->assertGreaterThanOrEqual(
            $result['rankings'][1]['preference_score'],
            $result['rankings'][0]['preference_score']
        );
        $this->assertGreaterThanOrEqual(
            $result['rankings'][2]['preference_score'],
            $result['rankings'][1]['preference_score']
        );
    }

    /**
     * Test preference values are always strictly bounded within [0, 1].
     */
    public function test_preference_values_are_between_zero_and_one(): void
    {
        $matrix = [
            'A1' => ['C1' => 5000000, 'C2' => 5, 'C3' => 8],
            'A2' => ['C1' => 8000000, 'C2' => 8, 'C3' => 16],
            'A3' => ['C1' => 12000000, 'C2' => 9, 'C3' => 16],
            'A4' => ['C1' => 15000000, 'C2' => 10, 'C3' => 32],
        ];

        $types = ['C1' => 'cost', 'C2' => 'benefit', 'C3' => 'benefit'];
        $weights = ['C1' => 0.4, 'C2' => 0.3, 'C3' => 0.3];

        $result = $this->service->calculate($matrix, $types, $weights);

        foreach ($result['preference_values'] as $v) {
            $this->assertGreaterThanOrEqual(0.0, $v);
            $this->assertLessThanOrEqual(1.0, $v);
        }
    }

    /**
     * Test empty matrix throws exception.
     */
    public function test_empty_decision_matrix_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->service->calculate([], ['C1' => 'benefit'], ['C1' => 1.0]);
    }
}
