<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserLog;
use App\Models\UserReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserLogAndReportTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $this->customer = User::factory()->create([
            'role' => User::ROLE_CUSTOMER,
        ]);
    }

    public function test_customer_cannot_view_logs_or_reports(): void
    {
        Sanctum::actingAs($this->customer);

        $this->getJson('/api/v1/log')->assertForbidden();
        $this->getJson('/api/v1/report')->assertForbidden();
    }

    public function test_login_activity_is_logged_on_success(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'login' => $this->customer->username,
            'password' => 'password', // standard user factory password
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('user_logs', [
            'user_id' => $this->customer->id,
            'activity' => 'Login Member',
            'status' => 'success',
        ]);
    }

    public function test_login_activity_is_logged_on_failure(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'login' => 'wronguser',
            'password' => 'wrongpass',
        ]);

        $response->assertUnauthorized();

        $this->assertDatabaseHas('user_logs', [
            'username' => 'wronguser',
            'activity' => 'Gagal Login (Password)',
            'status' => 'failed',
        ]);
    }

    public function test_admin_can_retrieve_logs_with_pagination_and_filters(): void
    {
        Sanctum::actingAs($this->admin);

        // Seed logs
        UserLog::create([
            'user_id' => $this->customer->id,
            'username' => $this->customer->username,
            'activity' => 'Login Member',
            'device' => 'iPhone 15 Pro',
            'ip_address' => '192.168.1.1',
            'status' => 'success',
            'created_at' => '2026-06-06 12:00:00',
        ]);

        UserLog::create([
            'username' => 'unknown_user',
            'activity' => 'Gagal Login (Password)',
            'device' => 'MacBook Air',
            'ip_address' => '103.11.24.12',
            'status' => 'failed',
            'created_at' => '2026-06-05 12:00:00',
        ]);

        // Test list
        $response = $this->getJson('/api/v1/log');
        $response->assertOk()
            ->assertJsonPath('meta.total', 2);

        // Filter status=failed
        $this->getJson('/api/v1/log?status=failed')
            ->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.logs.0.username', 'unknown_user');

        // Filter date=2026-06-06
        $this->getJson('/api/v1/log?date=2026-06-06')
            ->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.logs.0.username', $this->customer->username);
    }

    public function test_user_can_submit_report_and_admin_can_view_it(): void
    {
        // 1. Submit report as user
        Sanctum::actingAs($this->customer);

        $submitResponse = $this->postJson('/api/v1/report', [
            'issue' => 'Aplikasi lag saat upload bukti bayar',
            'category' => 'Aplikasi / Teknis',
        ]);

        $submitResponse->assertCreated()
            ->assertJsonPath('data.report.issue', 'Aplikasi lag saat upload bukti bayar')
            ->assertJsonPath('data.report.status', 'open');

        $this->assertDatabaseHas('user_reports', [
            'user_id' => $this->customer->id,
            'issue' => 'Aplikasi lag saat upload bukti bayar',
            'category' => 'Aplikasi / Teknis',
            'status' => 'open',
        ]);

        // 2. View reports as admin
        Sanctum::actingAs($this->admin);

        $viewResponse = $this->getJson('/api/v1/report');
        $viewResponse->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.reports.0.issue', 'Aplikasi lag saat upload bukti bayar');

        // Filter category
        $this->getJson('/api/v1/report?category=Layanan / GPS')
            ->assertOk()
            ->assertJsonPath('meta.total', 0);
    }
}
