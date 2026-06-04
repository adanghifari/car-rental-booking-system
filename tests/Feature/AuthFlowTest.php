<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_returns_login_redirect_and_does_not_set_auth_cookie(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Customer One',
            'username' => 'customerone',
            'email' => 'customer@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.redirect_to', route('login'))
            ->assertCookieMissing('access_token');
    }

    public function test_customer_login_returns_frontliner_redirect_and_auth_cookie(): void
    {
        $user = User::factory()->create([
            'username' => 'customerone',
            'email' => 'customer@example.com',
            'password' => 'Password123!',
            'role' => User::ROLE_CUSTOMER,
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'login' => $user->username,
            'password' => 'Password123!',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.redirect_to', route('frontliner'))
            ->assertCookie('access_token');
    }

    public function test_customer_login_uses_requested_redirect_when_present(): void
    {
        $user = User::factory()->create([
            'username' => 'customerredirect',
            'email' => 'redirect@example.com',
            'password' => 'Password123!',
            'role' => User::ROLE_CUSTOMER,
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'login' => $user->username,
            'password' => 'Password123!',
            'redirect' => '/frontliner',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.redirect_to', url('/frontliner'))
            ->assertCookie('access_token');
    }

    public function test_admin_login_returns_dashboard_redirect_and_auth_cookie(): void
    {
        $user = User::factory()->create([
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => 'Administrator123!',
            'role' => User::ROLE_ADMIN,
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'login' => $user->username,
            'password' => 'Administrator123!',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.redirect_to', route('dashboard'))
            ->assertCookie('access_token');
    }

    public function test_admin_can_open_dashboard_after_login(): void
    {
        $user = User::factory()->create([
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => 'Administrator123!',
            'role' => User::ROLE_ADMIN,
        ]);

        $loginResponse = $this->postJson('/api/v1/auth/login', [
            'login' => $user->username,
            'password' => 'Administrator123!',
        ]);

        $loginResponse->assertOk();

        $response = $this->get('/dashboard');

        $response->assertOk();
    }

    public function test_admin_opening_login_after_login_redirects_to_dashboard(): void
    {
        $user = User::factory()->create([
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => 'Administrator123!',
            'role' => User::ROLE_ADMIN,
        ]);

        $loginResponse = $this->postJson('/api/v1/auth/login', [
            'login' => $user->username,
            'password' => 'Administrator123!',
        ]);

        $loginResponse->assertOk();

        $response = $this->get('/login');

        $response->assertRedirect(route('dashboard'));
    }
}
