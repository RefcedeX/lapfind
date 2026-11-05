<?php

namespace Tests\Unit;

use App\Services\AhpService;
use PHPUnit\Framework\TestCase;

class AhpServiceTest extends TestCase
{
    private AhpService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AhpService;
    }

    /**
     * Test reciprocity and diagonal property of the comparison matrix.
     */
    public function test_matrix_maintains_diagonal_and_reciprocal_properties(): void
    {
        $keys = ['C1', 'C2', 'C3'];
        $raw = [
            'C1' => ['C2' => 3.0, 'C3' => 5.0],
            'C2' => ['C3' => 2.0],
        ];

        $matrix = $this->service->buildFullMatrix($keys, $raw);

        // Diagonal must be 1.0
        foreach ($keys as $k) {
            $this->assertEquals(1.0, $matrix[$k][$k]);
        }

        // a_ji must be reciprocal of a_ij (a_ji * a_ij = 1.0)
        $this->assertEqualsWithDelta(1.0, $matrix['C1']['C2'] * $matrix['C2']['C1'], 0.0001);
        $this->assertEqualsWithDelta(1.0, $matrix['C1']['C3'] * $matrix['C3']['C1'], 0.0001);
        $this->assertEqualsWithDelta(1.0, $matrix['C2']['C3'] * $matrix['C3']['C2'], 0.0001);
    }

    /**
     * Test complete AHP calculation with small 3x3 dataset that matches manual Excel calculation.
     */
    public function test_ahp_calculation_matches_independent_mathematical_model(): void
    {
        $keys = ['C1', 'C2', 'C3'];
        $raw = [
            'C1' => ['C2' => 3.0, 'C3' => 5.0],
            'C2' => ['C3' => 2.0],
        ];

        $result = $this->service->calculate($keys, $raw);

        // 1. Verifikasi jumlah kolom dihitung manual
        // Matrix:
        // C1: 1.0,    3.0,  5.0
        // C2: 1/3,    1.0,  2.0
        // C3: 1/5,    1/2,  1.0
        $expectedColSumC1 = 1.0 + (1.0 / 3.0) + (1.0 / 5.0);
        $expectedColSumC2 = 3.0 + 1.0 + 0.5;
        $expectedColSumC3 = 5.0 + 2.0 + 1.0;

        $this->assertEqualsWithDelta($expectedColSumC1, $result['column_sums']['C1'], 0.0001);
        $this->assertEqualsWithDelta($expectedColSumC2, $result['column_sums']['C2'], 0.0001);
        $this->assertEqualsWithDelta($expectedColSumC3, $result['column_sums']['C3'], 0.0001);

        // 2. Verifikasi normalisasi dan bobot
        $normRowC1 = (1.0 / $expectedColSumC1) + (3.0 / $expectedColSumC2) + (5.0 / $expectedColSumC3);
        $normRowC2 = ((1.0 / 3.0) / $expectedColSumC1) + (1.0 / $expectedColSumC2) + (2.0 / $expectedColSumC3);
        $normRowC3 = ((1.0 / 5.0) / $expectedColSumC1) + (0.5 / $expectedColSumC2) + (1.0 / $expectedColSumC3);

        $expectedWeightC1 = $normRowC1 / 3.0;
        $expectedWeightC2 = $normRowC2 / 3.0;
        $expectedWeightC3 = $normRowC3 / 3.0;

        $this->assertEqualsWithDelta($expectedWeightC1, $result['weights']['C1'], 0.001);
        $this->assertEqualsWithDelta($expectedWeightC2, $result['weights']['C2'], 0.001);
        $this->assertEqualsWithDelta($expectedWeightC3, $result['weights']['C3'], 0.001);

        // Total bobot harus persis 1.0 (100%)
        $totalWeight = array_sum($result['weights']);
        $this->assertEqualsWithDelta(1.0, $totalWeight, 0.0001);

        // 3. Verifikasi konsistensi lambda_max, CI, dan CR
        $w1 = $expectedWeightC1;
        $w2 = $expectedWeightC2;
        $w3 = $expectedWeightC3;

        $aw1 = (1.0 * $w1) + (3.0 * $w2) + (5.0 * $w3);
        $aw2 = ((1.0 / 3.0) * $w1) + (1.0 * $w2) + (2.0 * $w3);
        $aw3 = ((1.0 / 5.0) * $w1) + (0.5 * $w2) + (1.0 * $w3);

        $expectedLambda = (($aw1 / $w1) + ($aw2 / $w2) + ($aw3 / $w3)) / 3.0;
        $expectedCi = ($expectedLambda - 3.0) / 2.0;
        $expectedCr = $expectedCi / 0.58;

        $this->assertEqualsWithDelta($expectedLambda, $result['lambda_max'], 0.01);
        $this->assertEqualsWithDelta($expectedCi, $result['ci'], 0.01);
        $this->assertEqualsWithDelta($expectedCr, $result['cr'], 0.01);

        // Matriks ini relatif konsisten (CR < 0.1)
        $this->assertTrue($result['is_consistent']);
        $this->assertLessThanOrEqual(0.10, $result['cr']);
    }

    /**
     * Test perfectly consistent matrix produces CR ~ 0.
     */
    public function test_perfectly_consistent_matrix_produces_zero_cr(): void
    {
        $keys = ['C1', 'C2', 'C3'];
        // a_12 = 2, a_23 = 2, sehingga a_13 = 4 (sempurna transitif)
        $raw = [
            'C1' => ['C2' => 2.0, 'C3' => 4.0],
            'C2' => ['C3' => 2.0],
        ];

        $result = $this->service->calculate($keys, $raw);

        $this->assertTrue($result['is_consistent']);
        $this->assertEqualsWithDelta(3.0, $result['lambda_max'], 0.001);
        $this->assertEqualsWithDelta(0.0, $result['ci'], 0.001);
        $this->assertEqualsWithDelta(0.0, $result['cr'], 0.001);
    }

    /**
     * Test highly inconsistent matrix is properly flagged as inconsistent.
     */
    public function test_inconsistent_matrix_is_flagged(): void
    {
        $keys = ['C1', 'C2', 'C3'];
        // Sangat kontradiktif: C1 jauh lebih penting dari C2 (9), C2 jauh lebih penting dari C3 (9),
        // tapi C3 dinyatakan jauh lebih penting dari C1 (9)
        $raw = [
            'C1' => ['C2' => 9.0, 'C3' => 1.0 / 9.0],
            'C2' => ['C3' => 9.0],
        ];

        $result = $this->service->calculate($keys, $raw);

        $this->assertFalse($result['is_consistent']);
        $this->assertGreaterThan(0.10, $result['cr']);
    }

    /**
     * Test presets for student profiles return valid consistent matrices.
     */
    public function test_student_profile_presets_are_consistent(): void
    {
        $keys = ['C1', 'C2', 'C3', 'C4', 'C5', 'C6'];

        foreach (['umum', 'coding', 'desain'] as $profile) {
            $matrix = $this->service->getProfileDefaultComparisons($profile, $keys);
            $result = $this->service->calculate($keys, $matrix);

            $this->assertCount(6, $result['weights']);
            $this->assertEqualsWithDelta(1.0, array_sum($result['weights']), 0.0001);
            $this->assertTrue($result['is_consistent'], "Profil {$profile} harus konsisten (CR <= 0.1). CR didapat: {$result['cr']}");
        }
    }
}
