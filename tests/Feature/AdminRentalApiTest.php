<?php

namespace Tests\Feature;

use App\Enums\CarStatus;
use App\Enums\RentalStatus;
use App\Enums\RentalType;
use App\Models\Car;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminRentalApiTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $customer;
    private Car $car1;
    private Car $car2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $this->customer = User::factory()->create([
            'role' => User::ROLE_CUSTOMER,
        ]);

        $this->car1 = Car::create([
            'brand' => 'Mercedes-Benz',
            'name' => 'S-Class',
            'description' => 'Luxury sedan',
            'transmission' => 'automatic',
            'seat_count' => 5,
            'year' => 2023,
            'cc' => 3000,
            'vehicle_type' => 'sedan',
            'color' => 'Black',
            'daily_rate' => 4160000,
            'license_plate' => 'B 1234 LUX',
            'status' => CarStatus::AVAILABLE,
            'image' => 'merc.jpg',
            'rating' => 4.9,
        ]);

        $this->car2 = Car::create([
            'brand' => 'Toyota',
            'name' => 'Alphard HEV',
            'description' => 'Luxury MPV',
            'transmission' => 'automatic',
            'seat_count' => 7,
            'year' => 2023,
            'cc' => 2500,
            'vehicle_type' => 'mpv',
            'color' => 'White',
            'daily_rate' => 4500000,
            'license_plate' => 'B 2024 LUX',
            'status' => CarStatus::AVAILABLE,
            'image' => 'alphard.jpg',
            'rating' => 4.8,
        ]);
    }

    public function test_customer_cannot_access_admin_rental_apis(): void
    {
        Sanctum::actingAs($this->customer);

        $this->getJson('/api/v1/rentals')->assertForbidden();
        $this->getJson('/api/v1/rentals/count')->assertForbidden();
    }

    public function test_admin_can_list_rentals_with_pagination(): void
    {
        Sanctum::actingAs($this->admin);

        Rental::create([
            'user_id' => $this->customer->id,
            'car_id' => $this->car1->id,
            'start_date' => '2023-10-12',
            'end_date' => '2023-10-15',
            'total_price' => 12500000,
            'status' => RentalStatus::PREPAID,
            'type' => RentalType::SELF_DRIVE,
            'ktp_path' => 'ktp/test.jpg',
            'selfie_path' => 'selfie/test.jpg',
            'verification_passed' => true,
            'verified_at' => now(),
        ]);

        $response = $this->getJson('/api/v1/rentals?per_page=5');

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'status_code',
                'message',
                'data' => [
                    'rentals' => [
                        '*' => [
                            'id',
                            'user_id',
                            'car_id',
                            'start_date',
                            'end_date',
                            'total_price',
                            'status',
                            'type',
                            'user',
                            'car',
                        ]
                    ]
                ],
                'meta' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total',
                ]
            ])
            ->assertJsonPath('meta.total', 1);
    }

    public function test_admin_can_filter_rentals_by_status_car_type_and_start_date(): void
    {
        Sanctum::actingAs($this->admin);

        // Rental 1: Prepaid, Sedan, 2023-10-12
        $r1 = Rental::create([
            'user_id' => $this->customer->id,
            'car_id' => $this->car1->id,
            'start_date' => '2023-10-12',
            'end_date' => '2023-10-15',
            'total_price' => 12500000,
            'status' => RentalStatus::PREPAID,
            'type' => RentalType::SELF_DRIVE,
            'ktp_path' => 'ktp/test.jpg',
            'selfie_path' => 'selfie/test.jpg',
            'verification_passed' => true,
            'verified_at' => now(),
        ]);

        // Rental 2: Ongoing, MPV, 2023-10-16
        $r2 = Rental::create([
            'user_id' => $this->customer->id,
            'car_id' => $this->car2->id,
            'start_date' => '2023-10-16',
            'end_date' => '2023-10-17',
            'total_price' => 4500000,
            'status' => RentalStatus::ONGOING,
            'type' => RentalType::WITH_DRIVER,
            'ktp_path' => 'ktp/test.jpg',
            'selfie_path' => 'selfie/test.jpg',
            'verification_passed' => true,
            'verified_at' => now(),
        ]);

        // Filter status=prepaid
        $this->getJson('/api/v1/rentals?status=prepaid')
            ->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.rentals.0.id', $r1->id);

        // Filter tipe_mobil=MPV
        $this->getJson('/api/v1/rentals?tipe_mobil=MPV')
            ->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.rentals.0.id', $r2->id);

        // Filter tanggal_mulai=2023-10-12
        $this->getJson('/api/v1/rentals?tanggal_mulai=2023-10-12')
            ->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.rentals.0.id', $r1->id);
    }

    public function test_admin_can_retrieve_rental_counts(): void
    {
        Sanctum::actingAs($this->admin);

        // Create 1 prepaid, 2 ongoing, 1 returned
        Rental::create([
            'user_id' => $this->customer->id,
            'car_id' => $this->car1->id,
            'start_date' => '2023-10-12',
            'end_date' => '2023-10-15',
            'total_price' => 12000000,
            'status' => RentalStatus::PREPAID,
            'type' => RentalType::SELF_DRIVE,
            'ktp_path' => 'ktp/test.jpg',
            'selfie_path' => 'selfie/test.jpg',
        ]);

        Rental::create([
            'user_id' => $this->customer->id,
            'car_id' => $this->car1->id,
            'start_date' => '2023-10-16',
            'end_date' => '2023-10-18',
            'total_price' => 8000000,
            'status' => RentalStatus::ONGOING,
            'type' => RentalType::SELF_DRIVE,
            'ktp_path' => 'ktp/test.jpg',
            'selfie_path' => 'selfie/test.jpg',
        ]);

        Rental::create([
            'user_id' => $this->customer->id,
            'car_id' => $this->car2->id,
            'start_date' => '2023-10-19',
            'end_date' => '2023-10-21',
            'total_price' => 9000000,
            'status' => RentalStatus::ONGOING,
            'type' => RentalType::SELF_DRIVE,
            'ktp_path' => 'ktp/test.jpg',
            'selfie_path' => 'selfie/test.jpg',
        ]);

        Rental::create([
            'user_id' => $this->customer->id,
            'car_id' => $this->car2->id,
            'start_date' => '2023-10-22',
            'end_date' => '2023-10-23',
            'total_price' => 4500000,
            'status' => RentalStatus::RETURNED,
            'type' => RentalType::SELF_DRIVE,
            'ktp_path' => 'ktp/test.jpg',
            'selfie_path' => 'selfie/test.jpg',
        ]);

        $response = $this->getJson('/api/v1/rentals/count');

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'status_code' => 200,
                'data' => [
                    'total' => 4,
                    'prepaid' => 1,
                    'ongoing' => 2,
                    'returned' => 1,
                ]
            ]);
    }

    public function test_admin_can_retrieve_single_rental_details(): void
    {
        Sanctum::actingAs($this->admin);

        $rental = Rental::create([
            'user_id' => $this->customer->id,
            'car_id' => $this->car1->id,
            'start_date' => '2023-10-12',
            'end_date' => '2023-10-15',
            'total_price' => 12500000,
            'status' => RentalStatus::PREPAID,
            'type' => RentalType::SELF_DRIVE,
            'ktp_path' => 'ktp/test.jpg',
            'selfie_path' => 'selfie/test.jpg',
        ]);

        $this->getJson("/api/v1/rentals/{$rental->id}")
            ->assertOk()
            ->assertJsonPath('data.rental.id', $rental->id)
            ->assertJsonPath('data.rental.user.id', $this->customer->id)
            ->assertJsonPath('data.rental.car.id', $this->car1->id);
    }

    public function test_admin_can_update_rental_and_sync_car_status(): void
    {
        Sanctum::actingAs($this->admin);

        $rental = Rental::create([
            'user_id' => $this->customer->id,
            'car_id' => $this->car1->id,
            'start_date' => '2023-10-12',
            'end_date' => '2023-10-15',
            'total_price' => 12500000,
            'status' => RentalStatus::ONGOING,
            'type' => RentalType::SELF_DRIVE,
            'ktp_path' => 'ktp/test.jpg',
            'selfie_path' => 'selfie/test.jpg',
        ]);

        $this->car1->status = CarStatus::UNAVAILABLE;
        $this->car1->save();

        // Update status to RETURNED
        $response = $this->putJson("/api/v1/rentals/{$rental->id}", [
            'status' => 'returned',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.rental.status', 'returned');

        $this->assertEquals(RentalStatus::RETURNED, $rental->fresh()->status);
        // The car status should automatically be updated to AVAILABLE
        $this->assertEquals(CarStatus::AVAILABLE, $this->car1->fresh()->status);
    }

    public function test_admin_can_delete_rental_and_restore_car_status(): void
    {
        Sanctum::actingAs($this->admin);

        $rental = Rental::create([
            'user_id' => $this->customer->id,
            'car_id' => $this->car1->id,
            'start_date' => '2023-10-12',
            'end_date' => '2023-10-15',
            'total_price' => 12500000,
            'status' => RentalStatus::ONGOING,
            'type' => RentalType::SELF_DRIVE,
            'ktp_path' => 'ktp/test.jpg',
            'selfie_path' => 'selfie/test.jpg',
        ]);

        $this->car1->status = CarStatus::UNAVAILABLE;
        $this->car1->save();

        $this->deleteJson("/api/v1/rentals/{$rental->id}")
            ->assertOk();

        $this->assertDatabaseMissing('rentals', ['id' => $rental->id]);
        // The car status should automatically revert to AVAILABLE
        $this->assertEquals(CarStatus::AVAILABLE, $this->car1->fresh()->status);
    }
}
