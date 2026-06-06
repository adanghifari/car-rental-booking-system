<?php

namespace Tests\Feature;

use App\Models\Car;
use App\Models\User;
use App\Enums\CarStatus;
use App\Enums\VehicleType;
use App\Enums\TransmissionType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarValidationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);
    }

    public function test_api_car_store_validation_accepts_valid_types(): void
    {
        foreach (VehicleType::values() as $type) {
            $response = $this->postJson('/api/v1/car', [
                'brand' => 'Toyota',
                'name' => 'Car ' . $type,
                'description' => 'Test description',
                'transmission' => 'manual',
                'seat_count' => 5,
                'year' => 2022,
                'cc' => 1500,
                'vehicle_type' => $type,
                'color' => 'Black',
                'daily_rate' => 300000,
                'license_plate' => 'B ' . rand(1000, 9999) . ' val ' . str_replace('_', '', $type),
                'status' => CarStatus::AVAILABLE,
            ]);

            $response->assertStatus(201);
            $this->assertDatabaseHas('cars', [
                'name' => 'Car ' . $type,
                'vehicle_type' => $type,
            ]);
        }
    }

    public function test_api_car_store_validation_rejects_invalid_types(): void
    {
        $response = $this->postJson('/api/v1/car', [
            'brand' => 'Toyota',
            'name' => 'Invalid Type Car',
            'description' => 'Test description',
            'transmission' => 'manual',
            'seat_count' => 5,
            'year' => 2022,
            'cc' => 1500,
            'vehicle_type' => 'Supercar', // Invalid type
            'color' => 'Black',
            'daily_rate' => 300000,
            'license_plate' => 'B 1234 INVALID',
            'status' => CarStatus::AVAILABLE,
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseMissing('cars', [
            'name' => 'Invalid Type Car',
        ]);
    }

    public function test_backoffice_car_store_validation_accepts_valid_types(): void
    {
        $this->actingAs($this->admin);

        $image = \Illuminate\Http\UploadedFile::fake()->image('car.jpg');

        foreach (VehicleType::values() as $type) {
            $response = $this->post('/dashboard/cars', [
                'brand' => 'Toyota',
                'name' => 'Backoffice Car ' . $type,
                'description' => 'Test description',
                'transmission' => 'manual',
                'seat_count' => 5,
                'year' => 2022,
                'cc' => 1500,
                'vehicle_type' => $type,
                'color' => 'Black',
                'daily_rate' => 300000,
                'license_plate' => 'B ' . rand(1000, 9999) . ' bo ' . str_replace('_', '', $type),
                'image' => $image,
            ]);

            $response->assertRedirect(route('backoffice.cars'));
            $this->assertDatabaseHas('cars', [
                'name' => 'Backoffice Car ' . $type,
                'vehicle_type' => $type,
            ]);
        }
    }

    public function test_backoffice_car_store_validation_rejects_invalid_types(): void
    {
        $this->actingAs($this->admin);

        $image = \Illuminate\Http\UploadedFile::fake()->image('car.jpg');

        $response = $this->post('/dashboard/cars', [
            'brand' => 'Toyota',
            'name' => 'Backoffice Invalid Type Car',
            'description' => 'Test description',
            'transmission' => 'manual',
            'seat_count' => 5,
            'year' => 2022,
            'cc' => 1500,
            'vehicle_type' => 'Helicopter', // Invalid type
            'color' => 'Black',
            'daily_rate' => 300000,
            'license_plate' => 'B 1234 BOINVALID',
            'image' => $image,
        ]);

        $response->assertSessionHasErrors(['vehicle_type']);
        $this->assertDatabaseMissing('cars', [
            'name' => 'Backoffice Invalid Type Car',
        ]);
    }
}
