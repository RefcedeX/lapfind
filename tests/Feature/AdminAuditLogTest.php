<?php

namespace Tests\Feature;

use App\Models\Alternative;
use App\Models\AuditLog;
use App\Models\Criterion;
use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuditLogTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create([
            'name' => 'Admin Utama',
            'email' => 'admin@spk.test',
        ]);

        $this->regularUser = User::factory()->create([
            'name' => 'Mahasiswa Biasa',
            'email' => 'user@spk.test',
            'role' => 'user',
        ]);
    }

    /**
     * Test guest is redirected to login when trying to access audit logs.
     */
    public function test_guest_cannot_access_audit_logs(): void
    {
        $response = $this->get(route('admin.audit-logs.index'));
        $response->assertRedirect(route('login'));
    }

    /**
     * Test regular user cannot access audit logs (403 Forbidden).
     */
    public function test_regular_user_is_forbidden_from_audit_logs(): void
    {
        $response = $this->actingAs($this->regularUser)->get(route('admin.audit-logs.index'));
        $response->assertForbidden();
    }

    /**
     * Test admin can view audit logs index and telemetry cards.
     */
    public function test_admin_can_view_audit_logs_index(): void
    {
        AuditLog::create([
            'user_id' => $this->admin->id,
            'user_name' => $this->admin->name,
            'user_email' => $this->admin->email,
            'user_role' => 'admin',
            'action' => 'auth.login',
            'category' => 'auth',
            'description' => 'Admin berhasil masuk ke dalam sesi.',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit Test',
            'method' => 'POST',
            'url' => 'http://localhost/login',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.audit-logs.index'));

        $response->assertOk();
        $response->assertSee('Rekam jejak seluruh aktivitas login');
        $response->assertSee('Admin berhasil masuk ke dalam sesi.');
    }

    /**
     * Test admin can filter audit logs by category.
     */
    public function test_admin_can_filter_audit_logs_by_category(): void
    {
        AuditLog::create([
            'user_name' => 'Tester',
            'action' => 'alternative.create',
            'category' => 'catalog',
            'description' => 'Menambahkan laptop baru ASUS ROG',
            'created_at' => now(),
        ]);

        AuditLog::create([
            'user_name' => 'Tester',
            'action' => 'criterion.create',
            'category' => 'criterion',
            'description' => 'Menambahkan kriteria baru Portabilitas',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.audit-logs.index', ['category' => 'catalog']));

        $response->assertOk();
        $response->assertSee('Menambahkan laptop baru ASUS ROG');
        $response->assertDontSee('Menambahkan kriteria baru Portabilitas');
    }

    /**
     * Test admin can view audit log detail.
     */
    public function test_admin_can_view_audit_log_detail(): void
    {
        $log = AuditLog::create([
            'user_id' => $this->admin->id,
            'user_name' => $this->admin->name,
            'user_email' => $this->admin->email,
            'user_role' => 'admin',
            'action' => 'alternative.delete',
            'category' => 'catalog',
            'description' => 'Menghapus laptop Lenovo Legion 5',
            'ip_address' => '192.168.1.100',
            'properties' => ['laptop_id' => 99, 'deleted_by' => 'Admin Utama'],
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.audit-logs.show', $log));

        $response->assertOk();
        $response->assertSee('Detail Log Audit Aktivitas');
        $response->assertSee('Menghapus laptop Lenovo Legion 5');
        $response->assertSee('192.168.1.100');
    }

    /**
     * Test admin can export audit logs to CSV.
     */
    public function test_admin_can_export_audit_logs_to_csv(): void
    {
        AuditLog::create([
            'user_name' => 'Export Tester',
            'action' => 'export.test',
            'category' => 'security',
            'description' => 'Aktivitas uji ekspor data CSV',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.audit-logs.export'));

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    /**
     * Test creating a laptop automatically generates an audit log record.
     */
    public function test_creating_laptop_generates_audit_log(): void
    {
        $payload = [
            'name' => 'MacBook Pro M3 Max',
            'brand' => 'Apple',
            'price' => 38000000,
            'description' => 'Laptop premium untuk komputasi berat',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.alternatives.store'), $payload);

        $response->assertRedirect(route('admin.alternatives.index'));

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'alternative.create',
            'category' => 'catalog',
            'user_email' => $this->admin->email,
        ]);
    }

    /**
     * Test deleting a laptop automatically generates an audit log record.
     */
    public function test_deleting_laptop_generates_audit_log(): void
    {
        $laptop = Alternative::factory()->create([
            'name' => 'Dell XPS 13 Plus',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.alternatives.destroy', $laptop));

        $response->assertRedirect(route('admin.alternatives.index'));

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'alternative.delete',
            'category' => 'catalog',
            'user_email' => $this->admin->email,
        ]);
    }

    /**
     * Test creating a criterion automatically generates an audit log record.
     */
    public function test_creating_criterion_generates_audit_log(): void
    {
        $payload = [
            'code' => 'C99',
            'name' => 'Daya Tahan Baterai',
            'type' => 'benefit',
            'unit' => 'Jam',
            'description' => 'Kapasitas dan ketahanan baterai harian',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.criteria.store'), $payload);

        $response->assertRedirect(route('admin.criteria.index'));

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'criterion.create',
            'category' => 'criterion',
            'user_email' => $this->admin->email,
        ]);
    }

    /**
     * Test user login event automatically creates an audit log record.
     */
    public function test_user_login_event_creates_audit_log(): void
    {
        event(new Login('web', $this->regularUser, false));

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'auth.login',
            'category' => 'auth',
            'user_email' => $this->regularUser->email,
            'user_role' => 'user',
        ]);
    }

    /**
     * Test user logout event automatically creates an audit log record.
     */
    public function test_user_logout_event_creates_audit_log(): void
    {
        event(new Logout('web', $this->regularUser));

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'auth.logout',
            'category' => 'auth',
            'user_email' => $this->regularUser->email,
        ]);
    }
}
