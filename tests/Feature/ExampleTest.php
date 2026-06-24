<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_home_page_queries_and_filters_cars(): void
    {
        // 1. Create cars
        $this->createCar([
            'name' => 'Avanza Murah',
            'daily_rate' => 300000,
            'status' => \App\Enums\CarStatus::AVAILABLE,
        ]);

        $this->createCar([
            'name' => 'Fortuner Mahal',
            'daily_rate' => 1000000,
            'status' => \App\Enums\CarStatus::AVAILABLE,
        ]);

        $this->createCar([
            'name' => 'Brio Tidak Tersedia',
            'daily_rate' => 250000,
            'status' => \App\Enums\CarStatus::UNAVAILABLE,
        ]);

        // 2. Visit home page without filters
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Avanza Murah');
        $response->assertSee('Fortuner Mahal');
        $response->assertDontSee('Brio Tidak Tersedia');

        // 3. Visit home page with max_price filter
        $response = $this->get('/?max_price=400000');
        $response->assertStatus(200);
        $response->assertSee('Avanza Murah');
        $response->assertDontSee('Fortuner Mahal');
    }

    public function test_search_results_page_queries_and_filters_cars(): void
    {
        // 1. Create cars
        $this->createCar([
            'name' => 'Avanza Murah',
            'daily_rate' => 300000,
            'status' => \App\Enums\CarStatus::AVAILABLE,
        ]);

        $this->createCar([
            'name' => 'Fortuner Mahal',
            'daily_rate' => 1000000,
            'status' => \App\Enums\CarStatus::AVAILABLE,
        ]);

        // 2. Visit search results page without filters
        $response = $this->get('/search-result');
        $response->assertStatus(200);
        $response->assertSee('Avanza Murah');
        $response->assertSee('Fortuner Mahal');

        // 3. Visit search results page with max_price filter
        $response = $this->get('/search-result?max_price=400000');
        $response->assertStatus(200);
        $response->assertSee('Avanza Murah');
        $response->assertDontSee('Fortuner Mahal');

        // 4. Test types filter
        $this->createCar([
            'name' => 'Brio Hatchback',
            'vehicle_type' => \App\Enums\VehicleType::HATCHBACK,
            'daily_rate' => 250000,
            'status' => \App\Enums\CarStatus::AVAILABLE,
        ]);

        $response = $this->get('/search-result?types[]=hatchback');
        $response->assertStatus(200);
        $response->assertSee('Brio Hatchback');
        $response->assertDontSee('Avanza Murah'); // Avanza is MPV

        // 5. Test capacity filter
        $this->createCar([
            'name' => 'Ayla 2 Kursi',
            'seat_count' => 2,
            'daily_rate' => 200000,
            'status' => \App\Enums\CarStatus::AVAILABLE,
        ]);

        $response = $this->get('/search-result?capacity=2');
        $response->assertStatus(200);
        $response->assertSee('Ayla 2 Kursi');
        $response->assertDontSee('Brio Hatchback'); // Brio has 7 (or 5) seats

        // 6. Test service types filter (excluder for Pariwisata)
        $this->createCar([
            'name' => 'Elf Pariwisata',
            'vehicle_type' => \App\Enums\VehicleType::PARIWISATA,
            'daily_rate' => 1200000,
            'status' => \App\Enums\CarStatus::AVAILABLE,
        ]);

        // When requesting self_drive, Elf Pariwisata should be excluded
        $response = $this->get('/search-result?service_types[]=self_drive');
        $response->assertStatus(200);
        $response->assertDontSee('Elf Pariwisata');
    }

    public function test_armada_page_displays_all_cars(): void
    {
        // 1. Create cars
        $this->createCar([
            'name' => 'Avanza Murah',
            'daily_rate' => 300000,
            'status' => \App\Enums\CarStatus::AVAILABLE,
        ]);

        $this->createCar([
            'name' => 'Fortuner Mahal',
            'daily_rate' => 1000000,
            'status' => \App\Enums\CarStatus::AVAILABLE,
        ]);

        // 2. Visit armada page
        $response = $this->get('/armada');
        $response->assertStatus(200);
        $response->assertSee('Avanza Murah');
        $response->assertSee('Fortuner Mahal');
        $response->assertSee('MD CAR RENTAL');
    }

    public function test_booking_detail_page_is_accessible_to_owner(): void
    {
        $user = \App\Models\User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
            'role' => \App\Models\User::ROLE_CUSTOMER,
        ]);

        $car = $this->createCar();

        $rental = \App\Models\Rental::create([
            'user_id' => $user->id,
            'car_id' => $car->id,
            'start_date' => now(),
            'end_date' => now()->addDays(3),
            'total_price' => 1000000,
            'status' => \App\Enums\RentalStatus::PREPAID,
            'type' => \App\Enums\RentalType::SELF_DRIVE,
            'ktp_path' => 'ktp/mock_ktp.png',
            'selfie_path' => 'selfie/mock_selfie.png',
        ]);

        $response = $this->actingAs($user)->get("/booking/detail/{$rental->id}");
        $response->assertStatus(200);
        $response->assertSee('Detail Kendaraan');
        $response->assertSee('Rincian Biaya');
        $response->assertSee('Bayar Sekarang via Midtrans');
    }

    public function test_pesanan_saya_page_is_accessible_and_filterable(): void
    {
        $user = \App\Models\User::create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => bcrypt('password'),
            'role' => \App\Models\User::ROLE_CUSTOMER,
        ]);

        $car = $this->createCar();

        $rental = \App\Models\Rental::create([
            'user_id' => $user->id,
            'car_id' => $car->id,
            'start_date' => now(),
            'end_date' => now()->addDays(3),
            'total_price' => 1000000,
            'status' => \App\Enums\RentalStatus::PREPAID,
            'type' => \App\Enums\RentalType::SELF_DRIVE,
            'ktp_path' => 'ktp/mock_ktp.png',
            'selfie_path' => 'selfie/mock_selfie.png',
        ]);

        $response = $this->actingAs($user)->get('/pesanan-saya');
        $response->assertStatus(200);
        $response->assertSee('Semua Riwayat Pemesanan');
        $response->assertSee('RESI:');
        $response->assertSee('Pending');
    }

    private function createCar(array $attributes = []): \App\Models\Car
    {
        return \App\Models\Car::create(array_merge([
            'name' => 'Toyota Avanza',
            'brand' => 'Toyota',
            'description' => 'Mobil keluarga serbaguna',
            'transmission' => \App\Enums\TransmissionType::MANUAL,
            'seat_count' => 7,
            'year' => 2020,
            'cc' => 1300,
            'vehicle_type' => \App\Enums\VehicleType::MPV,
            'color' => 'Silver',
            'daily_rate' => 350000,
            'license_plate' => 'B ' . rand(1000, 9999) . ' XYZ',
            'status' => \App\Enums\CarStatus::AVAILABLE,
        ], $attributes));
    }
}
