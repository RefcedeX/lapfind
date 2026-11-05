<?php

namespace Tests\Feature;

use App\Models\Alternative;
use App\Models\SpkResult;
use App\Models\User;
use Database\Seeders\AlternativeSeeder;
use Database\Seeders\CriterionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicAndSpkFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed default criteria C1-C6 and 12 laptops for realistic testing
        $this->seed(CriterionSeeder::class);
        $this->seed(AlternativeSeeder::class);
    }

    /**
     * Test home page loads and displays direct functional components.
     */
    public function test_home_page_loads_successfully(): void
    {
        $response = $this->get(route('home'));
        $response->assertOk();
        $response->assertSee('Rekomendasi Laptop Mahasiswa Berbasis AHP');
        $response->assertSee('Mulai Evaluasi SPK');
    }

    /**
     * Test catalog page and filter by brand and price.
     */
    public function test_catalog_page_filters_by_brand_and_price(): void
    {
        $response = $this->get(route('catalog.index', [
            'brand' => 'ASUS',
            'min_price' => 7000000,
            'max_price' => 20000000,
        ]));

        $response->assertOk();
        $response->assertSee('ASUS');
    }

    /**
     * Test single laptop detail page.
     */
    public function test_laptop_detail_page_loads(): void
    {
        $laptop = Alternative::first();
        $this->assertNotNull($laptop);

        $response = $this->get(route('catalog.show', $laptop));
        $response->assertOk();
        $response->assertSee($laptop->name);
        $response->assertSee('Parameter Nilai Evaluasi');
    }

    /**
     * Test user dashboard loads with telemetry, presets, and criteria.
     */
    public function test_user_dashboard_loads_with_telemetry_and_presets(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Halo, '.$user->name);
        $response->assertSee('Mesin SPK Siap Komputasi');
        $response->assertSee('Pintasan Profil Kebutuhan Mahasiswa');
        $response->assertSee('6 Kriteria Penilaian');
    }

    /**
     * Test laptop side-by-side comparison page.
     */
    public function test_laptop_comparison_page(): void
    {
        $laptops = Alternative::take(2)->get();
        $ids = $laptops->pluck('id')->toArray();

        $response = $this->get(route('catalog.compare', ['laptop_ids' => $ids]));
        $response->assertOk();
        $response->assertSee($laptops[0]->name);
        $response->assertSee($laptops[1]->name);
    }

    /**
     * Test complete 3-step SPK workflow as guest.
     */
    public function test_spk_three_step_workflow_as_guest(): void
    {
        // 1. Buka Halaman Langkah 1
        $step1 = $this->get(route('spk.index', ['profil' => 'coding']));
        $step1->assertOk();
        $step1->assertSee('Langkah 1 dari 3');

        // 2. Submit Perbandingan AHP (Langkah 1 -> Langkah 2)
        $ahpSubmit = $this->post(route('spk.process_ahp'), [
            'profile' => 'coding',
            'comparisons' => [
                'C3' => ['C1' => 3, 'C2' => 2, 'C4' => 3, 'C5' => 4, 'C6' => 3],
                'C2' => ['C1' => 2, 'C4' => 2, 'C5' => 3, 'C6' => 2],
                'C4' => ['C1' => 1, 'C5' => 2, 'C6' => 1],
                'C1' => ['C5' => 2, 'C6' => 1],
                'C6' => ['C5' => 2],
            ],
        ]);
        $ahpSubmit->assertRedirect(route('spk.weights'));

        // 3. Buka Halaman Tinjauan Bobot (Langkah 2)
        $step2 = $this->get(route('spk.weights'));
        $step2->assertOk();
        $step2->assertSee('Bobot Prioritas Kriteria');
        $step2->assertSee('Vektor Bobot Kriteria (w_j)');

        // 4. Submit TOPSIS (Langkah 2 -> Langkah 3)
        $topsisSubmit = $this->post(route('spk.process_topsis'));
        $topsisSubmit->assertRedirect(route('spk.result'));

        // 5. Buka Hasil Akhir TOPSIS (Langkah 3)
        $step3 = $this->get(route('spk.result'));
        $step3->assertOk();
        $step3->assertSee('Langkah 3 dari 3');
        $step3->assertSee('Rekomendasi Terbaik #1');
        $step3->assertSee('Transparansi Laporan SPK');
    }

    /**
     * Test SPK execution as authenticated user saves result to database and history.
     */
    public function test_spk_saves_to_database_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        // 1. Submit AHP sebagai authenticated user
        $this->actingAs($user)->post(route('spk.process_ahp'), [
            'profile' => 'umum',
            'comparisons' => [
                'C1' => ['C2' => 3, 'C3' => 3, 'C4' => 4, 'C5' => 2, 'C6' => 2],
                'C6' => ['C2' => 3, 'C3' => 2, 'C4' => 3, 'C5' => 2],
                'C5' => ['C2' => 2, 'C3' => 2, 'C4' => 2],
                'C3' => ['C4' => 2],
                'C2' => ['C4' => 2],
            ],
        ])->assertRedirect(route('spk.weights'));

        // 2. Submit TOPSIS
        $this->actingAs($user)->post(route('spk.process_topsis'))
            ->assertRedirect();

        // 3. Verifikasi tersimpan di database spk_results
        $this->assertDatabaseHas('spk_results', [
            'user_id' => $user->id,
        ]);

        $savedResult = SpkResult::where('user_id', $user->id)->first();
        $this->assertNotNull($savedResult);

        // 4. Verifikasi riwayat user menampilkan evaluasi tersebut
        $historyResponse = $this->actingAs($user)->get(route('history.index'));
        $historyResponse->assertOk();
        $historyResponse->assertSee($savedResult->title);

        // 5. User dapat menghapus riwayatnya
        $deleteResponse = $this->actingAs($user)->delete(route('history.destroy', $savedResult));
        $deleteResponse->assertRedirect(route('history.index'));
        $this->assertDatabaseMissing('spk_results', ['id' => $savedResult->id]);
    }

    /**
     * Test export PDF from saved database result.
     */
    public function test_user_can_export_saved_spk_result_as_pdf(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('spk.process_ahp'), [
            'profile' => 'coding',
            'comparisons' => [
                'C3' => ['C1' => 3, 'C2' => 2, 'C4' => 3, 'C5' => 4, 'C6' => 3],
                'C2' => ['C1' => 2, 'C4' => 2, 'C5' => 3, 'C6' => 2],
                'C4' => ['C1' => 1, 'C5' => 2, 'C6' => 1],
                'C1' => ['C5' => 2, 'C6' => 1],
                'C6' => ['C5' => 2],
            ],
        ]);
        $this->actingAs($user)->post(route('spk.process_topsis'));

        $savedResult = SpkResult::where('user_id', $user->id)->first();
        $this->assertNotNull($savedResult);

        $response = $this->actingAs($user)->get(route('spk.export_pdf', $savedResult));
        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString('Laporan_SPK_Laptop_', $response->headers->get('content-disposition'));
    }

    /**
     * Test export PDF from active session (guest mode).
     */
    public function test_guest_can_export_session_spk_result_as_pdf(): void
    {
        $this->post(route('spk.process_ahp'), [
            'profile' => 'umum',
            'comparisons' => [
                'C1' => ['C2' => 3, 'C3' => 3, 'C4' => 4, 'C5' => 2, 'C6' => 2],
                'C6' => ['C2' => 3, 'C3' => 2, 'C4' => 3, 'C5' => 2],
                'C5' => ['C2' => 2, 'C3' => 2, 'C4' => 2],
                'C3' => ['C4' => 2],
                'C2' => ['C4' => 2],
            ],
        ]);
        $this->post(route('spk.process_topsis'));

        $response = $this->get(route('spk.export_pdf'));
        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }
}
