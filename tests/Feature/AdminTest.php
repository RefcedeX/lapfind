<?php

namespace Tests\Feature;

use App\Models\Alternative;
use App\Models\Criterion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create([
            'email' => 'admin@spk.test',
        ]);

        $this->regularUser = User::factory()->create([
            'email' => 'user@spk.test',
            'role' => 'user',
        ]);
    }

    /**
     * Test guest is redirected to login when trying to access admin pages.
     */
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('login'));
    }

    /**
     * Test regular user cannot access admin pages and gets 403 Forbidden.
     */
    public function test_regular_user_is_forbidden_from_admin_area(): void
    {
        $response = $this->actingAs($this->regularUser)->get(route('admin.dashboard'));
        $response->assertForbidden();
    }

    /**
     * Test admin user can access dashboard.
     */
    public function test_admin_can_access_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertOk();
        $response->assertSee('Ringkasan Sistem SPK');
    }

    /**
     * Test admin can view criteria index and create a criterion.
     */
    public function test_admin_can_create_criterion(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.criteria.store'), [
            'code' => 'C9',
            'name' => 'Kualitas Layar',
            'type' => 'benefit',
            'unit' => 'skor 1-10',
            'description' => 'Akurasi warna dan tingkat kecerahan layar',
        ]);

        $response->assertRedirect(route('admin.criteria.index'));
        $this->assertDatabaseHas('criteria', [
            'code' => 'C9',
            'name' => 'Kualitas Layar',
            'type' => 'benefit',
        ]);
    }

    /**
     * Test validation rejects duplicate criterion code.
     */
    public function test_duplicate_criterion_code_is_rejected(): void
    {
        Criterion::factory()->create(['code' => 'C1']);

        $response = $this->actingAs($this->admin)->post(route('admin.criteria.store'), [
            'code' => 'C1',
            'name' => 'Harga Baru',
            'type' => 'cost',
        ]);

        $response->assertSessionHasErrors('code');
    }

    /**
     * Test admin can update criterion.
     */
    public function test_admin_can_update_criterion(): void
    {
        $criterion = Criterion::factory()->create([
            'code' => 'C2',
            'name' => 'CPU Lama',
            'type' => 'benefit',
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.criteria.update', $criterion), [
            'code' => 'C2',
            'name' => 'CPU Baru Diperbarui',
            'type' => 'benefit',
            'unit' => 'GHz',
            'description' => 'Deskripsi baru',
        ]);

        $response->assertRedirect(route('admin.criteria.index'));
        $this->assertDatabaseHas('criteria', [
            'id' => $criterion->id,
            'name' => 'CPU Baru Diperbarui',
        ]);
    }

    /**
     * Test admin can delete criterion.
     */
    public function test_admin_can_delete_criterion(): void
    {
        $criterion = Criterion::factory()->create(['code' => 'C8']);

        $response = $this->actingAs($this->admin)->delete(route('admin.criteria.destroy', $criterion));

        $response->assertRedirect(route('admin.criteria.index'));
        $this->assertDatabaseMissing('criteria', ['id' => $criterion->id]);
    }

    /**
     * Test admin can create laptop alternative with criteria values.
     */
    public function test_admin_can_create_alternative_with_values(): void
    {
        $c1 = Criterion::factory()->create(['code' => 'C1', 'type' => 'cost']);
        $c2 = Criterion::factory()->create(['code' => 'C2', 'type' => 'benefit']);

        $response = $this->actingAs($this->admin)->post(route('admin.alternatives.store'), [
            'name' => 'Laptop Test Unit 2026',
            'brand' => 'ASUS',
            'price' => 12500000,
            'description' => 'Unit uji coba',
            'values' => [
                $c1->id => 12500000,
                $c2->id => 8.5,
            ],
        ]);

        $response->assertRedirect(route('admin.alternatives.index'));
        $this->assertDatabaseHas('alternatives', [
            'name' => 'Laptop Test Unit 2026',
            'brand' => 'ASUS',
        ]);

        $laptop = Alternative::where('name', 'Laptop Test Unit 2026')->first();
        $this->assertNotNull($laptop);
        $this->assertDatabaseHas('alternative_values', [
            'alternative_id' => $laptop->id,
            'criteria_id' => $c1->id,
            'value' => 12500000,
        ]);
    }

    /**
     * Test admin can update laptop alternative and delete it.
     */
    public function test_admin_can_update_and_delete_alternative(): void
    {
        $laptop = Alternative::factory()->create(['name' => 'Laptop Lama']);

        $updateResponse = $this->actingAs($this->admin)->put(route('admin.alternatives.update', $laptop), [
            'name' => 'Laptop Terupdate',
            'brand' => 'Lenovo',
            'price' => 9999000,
        ]);

        $updateResponse->assertRedirect(route('admin.alternatives.index'));
        $this->assertDatabaseHas('alternatives', ['id' => $laptop->id, 'name' => 'Laptop Terupdate']);

        $deleteResponse = $this->actingAs($this->admin)->delete(route('admin.alternatives.destroy', $laptop));
        $deleteResponse->assertRedirect(route('admin.alternatives.index'));
        $this->assertDatabaseMissing('alternatives', ['id' => $laptop->id]);
    }

    /**
     * Test admin can bulk update decision matrix.
     */
    public function test_admin_can_bulk_update_decision_matrix(): void
    {
        $laptop = Alternative::factory()->create();
        $criterion = Criterion::factory()->create();

        $response = $this->actingAs($this->admin)->put(route('admin.values.update'), [
            'values' => [
                $laptop->id => [
                    $criterion->id => 99.5,
                ],
            ],
        ]);

        $response->assertRedirect(route('admin.values.index'));
        $this->assertDatabaseHas('alternative_values', [
            'alternative_id' => $laptop->id,
            'criteria_id' => $criterion->id,
            'value' => 99.5,
        ]);
    }

    /**
     * Test admin user management: create and prevent self deletion.
     */
    public function test_admin_user_crud_and_self_delete_protection(): void
    {
        // 1. Create new student user
        $createResponse = $this->actingAs($this->admin)->post(route('admin.users.store'), [
            'name' => 'Mahasiswa Baru',
            'email' => 'mhsbaru@spk.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'user',
        ]);

        $createResponse->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', ['email' => 'mhsbaru@spk.test']);

        // 2. Prevent self-deletion
        $selfDeleteResponse = $this->actingAs($this->admin)->delete(route('admin.users.destroy', $this->admin));
        $selfDeleteResponse->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);

        // 3. Can delete another user
        $newUser = User::where('email', 'mhsbaru@spk.test')->first();
        $deleteResponse = $this->actingAs($this->admin)->delete(route('admin.users.destroy', $newUser));
        $deleteResponse->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseMissing('users', ['id' => $newUser->id]);
    }

    /**
     * Test admin can export decision matrix to CSV.
     */
    public function test_admin_can_export_decision_matrix_to_csv(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.values.export'));
        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    /**
     * Test admin can export alternatives to CSV.
     */
    public function test_admin_can_export_alternatives_to_csv(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.alternatives.export'));
        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }
}
