<?php

namespace Tests\Feature;

use App\Enums\CarStatus;
use App\Enums\RentalStatus;
use App\Enums\RentalType;
use App\Enums\VerificationStatus;
use App\Models\Car;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BookingIdentityVerificationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $customer;
    private Car $car;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        $this->admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $this->customer = User::factory()->create([
            'role' => User::ROLE_CUSTOMER,
        ]);

        $this->car = Car::create([
            'brand' => 'Honda',
            'name' => 'Civic RS',
            'description' => 'Sporty sedan',
            'transmission' => 'automatic',
            'seat_count' => 5,
            'year' => 2023,
            'cc' => 1500,
            'vehicle_type' => 'sedan',
            'color' => 'Red',
            'daily_rate' => 1500000,
            'license_plate' => 'B 777 RS',
            'status' => CarStatus::AVAILABLE,
            'image' => 'civic.jpg',
            'rating' => 4.9,
        ]);
    }

    public function test_double_booking_prevention_on_identity_step(): void
    {
        $this->actingAs($this->customer);

        // Create an active rental for the same car & overlapping dates
        Rental::create([
            'user_id' => User::factory()->create()->id,
            'car_id' => $this->car->id,
            'start_date' => '2026-07-01',
            'end_date' => '2026-07-05',
            'total_price' => 7500000,
            'status' => RentalStatus::ONGOING,
            'type' => RentalType::SELF_DRIVE,
            'ktp_path' => 'ktp/test.jpg',
            'selfie_path' => 'selfie/test.jpg',
        ]);

        // Attempting to visit identity step for the same car and overlapping dates should fail double booking check
        $response = $this->post('/booking/identity', [
            'car_id' => $this->car->id,
            'start_date' => '2026-07-03',
            'end_date' => '2026-07-07',
            'service_type' => 'self_drive',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Maaf, mobil ini sudah tidak tersedia untuk tanggal yang dipilih. Silakan pilih kendaraan atau tanggal lain.');
    }

    public function test_submit_booking_flow_auto_approve(): void
    {
        $this->actingAs($this->customer);

        $ktp = UploadedFile::fake()->image('ktp.jpg')->size(100);
        $selfie = UploadedFile::fake()->image('selfie.jpg')->size(100);

        $response = $this->post('/booking/submit', [
            'car_id' => $this->car->id,
            'start_date' => '2026-07-10',
            'end_date' => '2026-07-12',
            'service_type' => 'self_drive',
            'ktp' => $ktp,
            'selfie' => $selfie,
        ]);

        $response->assertRedirect();
        $rental = Rental::first();
        $this->assertNotNull($rental);

        if ($rental->verification_status === VerificationStatus::VERIFIED) {
            $this->assertEquals(RentalStatus::PREPAID, $rental->status);
            $this->assertNotNull($rental->verified_at);
            $this->assertTrue($rental->verification_passed);
        } else {
            $this->assertEquals(RentalStatus::PENDING_VERIFICATION, $rental->status);
            $this->assertEquals(VerificationStatus::NEEDS_REVIEW, $rental->verification_status);
            $this->assertNull($rental->verified_at);
            $this->assertFalse($rental->verification_passed);
        }
        
        $this->assertEquals(CarStatus::UNAVAILABLE, $this->car->fresh()->status);
    }

    public function test_admin_manual_approval_starts_4_hour_countdown(): void
    {
        $this->actingAs($this->admin);

        // Create a rental that needs review
        $rental = Rental::create([
            'user_id' => $this->customer->id,
            'car_id' => $this->car->id,
            'start_date' => '2026-07-10',
            'end_date' => '2026-07-12',
            'total_price' => 3000000,
            'status' => RentalStatus::PENDING_VERIFICATION,
            'type' => RentalType::SELF_DRIVE,
            'verification_status' => VerificationStatus::NEEDS_REVIEW,
            'ktp_path' => 'ktp/test.jpg',
            'selfie_path' => 'selfie/test.jpg',
        ]);

        $response = $this->post("/dashboard/reservations/{$rental->id}/verify", [
            'action' => 'approve',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $rental->refresh();
        $this->assertEquals(VerificationStatus::VERIFIED, $rental->verification_status);
        $this->assertTrue($rental->verification_passed);
        $this->assertNotNull($rental->verified_at);
        $this->assertEquals(RentalStatus::PENDING_VERIFICATION, $rental->status);
    }

    public function test_admin_manual_rejection_frees_car_and_cancels_rental(): void
    {
        // Place files in fake storage
        Storage::disk('local')->put('ktp/test.jpg', 'content');
        Storage::disk('local')->put('selfie/test.jpg', 'content');

        $this->actingAs($this->admin);

        // Create a rental that needs review
        $rental = Rental::create([
            'user_id' => $this->customer->id,
            'car_id' => $this->car->id,
            'start_date' => '2026-07-10',
            'end_date' => '2026-07-12',
            'total_price' => 3000000,
            'status' => RentalStatus::PENDING_VERIFICATION,
            'type' => RentalType::SELF_DRIVE,
            'verification_status' => VerificationStatus::NEEDS_REVIEW,
            'ktp_path' => 'ktp/test.jpg',
            'selfie_path' => 'selfie/test.jpg',
        ]);

        // Set car to UNAVAILABLE
        $this->car->status = CarStatus::UNAVAILABLE;
        $this->car->save();

        $response = $this->post("/dashboard/reservations/{$rental->id}/verify", [
            'action' => 'reject',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $rental->refresh();
        $this->assertEquals(VerificationStatus::REJECTED, $rental->verification_status);
        $this->assertEquals(RentalStatus::CANCELLED, $rental->status);
        $this->assertEquals(CarStatus::AVAILABLE, $this->car->fresh()->status);

        // Check file deletion
        Storage::disk('local')->assertMissing('ktp/test.jpg');
        Storage::disk('local')->assertMissing('selfie/test.jpg');
    }

    public function test_customer_cancellation_on_pending_verification(): void
    {
        Storage::disk('local')->put('ktp/test.jpg', 'content');
        Storage::disk('local')->put('selfie/test.jpg', 'content');

        $this->actingAs($this->customer);

        // Create a rental that needs review
        $rental = Rental::create([
            'user_id' => $this->customer->id,
            'car_id' => $this->car->id,
            'start_date' => '2026-07-10',
            'end_date' => '2026-07-12',
            'total_price' => 3000000,
            'status' => RentalStatus::PENDING_VERIFICATION,
            'type' => RentalType::SELF_DRIVE,
            'verification_status' => VerificationStatus::NEEDS_REVIEW,
            'ktp_path' => 'ktp/test.jpg',
            'selfie_path' => 'selfie/test.jpg',
        ]);

        $response = $this->post("/booking/detail/{$rental->id}/cancel");

        $response->assertRedirect();
        
        $rental->refresh();
        $this->assertEquals(RentalStatus::CANCELLED, $rental->status);
        $this->assertEquals(CarStatus::AVAILABLE, $this->car->fresh()->status);

        // Check file deletion
        Storage::disk('local')->assertMissing('ktp/test.jpg');
        Storage::disk('local')->assertMissing('selfie/test.jpg');
    }

    public function test_scheduler_cleans_up_expired_prepaid_and_verified_rentals(): void
    {
        Storage::disk('local')->put('ktp/test_prepaid.jpg', 'content');
        Storage::disk('local')->put('selfie/test_prepaid.jpg', 'content');
        Storage::disk('local')->put('ktp/test_verified.jpg', 'content');
        Storage::disk('local')->put('selfie/test_verified.jpg', 'content');

        // Rental 1: Prepaid and past prepaid_expires_at
        $rentalPrepaid = Rental::create([
            'user_id' => $this->customer->id,
            'car_id' => $this->car->id,
            'start_date' => '2026-07-10',
            'end_date' => '2026-07-12',
            'total_price' => 3000000,
            'status' => RentalStatus::PREPAID,
            'type' => RentalType::SELF_DRIVE,
            'verification_status' => VerificationStatus::VERIFIED,
            'prepaid_expires_at' => now()->subMinutes(1),
            'ktp_path' => 'ktp/test_prepaid.jpg',
            'selfie_path' => 'selfie/test_prepaid.jpg',
        ]);

        // Rental 2: Verified but never paid, past 4 hours from verified_at
        $car2 = Car::create([
            'brand' => 'Toyota',
            'name' => 'Avanza',
            'description' => 'Standard MPV',
            'transmission' => 'manual',
            'seat_count' => 7,
            'year' => 2022,
            'cc' => 1300,
            'vehicle_type' => 'mpv',
            'color' => 'Silver',
            'daily_rate' => 500000,
            'license_plate' => 'B 888 AV',
            'status' => CarStatus::AVAILABLE,
            'rating' => 4.5,
        ]);

        $rentalVerified = Rental::create([
            'user_id' => $this->customer->id,
            'car_id' => $car2->id,
            'start_date' => '2026-07-10',
            'end_date' => '2026-07-12',
            'total_price' => 1000000,
            'status' => RentalStatus::PENDING_VERIFICATION,
            'type' => RentalType::SELF_DRIVE,
            'verification_status' => VerificationStatus::VERIFIED,
            'verified_at' => now()->subHours(4)->subMinutes(1),
            'ktp_path' => 'ktp/test_verified.jpg',
            'selfie_path' => 'selfie/test_verified.jpg',
        ]);

        // Run scheduler command
        $this->artisan('rentals:expire-prepaid')
            ->expectsOutput('Expired rentals cleaned up successfully.')
            ->assertExitCode(0);

        $rentalPrepaid->refresh();
        $rentalVerified->refresh();

        $this->assertEquals(RentalStatus::EXPIRED, $rentalPrepaid->status);
        $this->assertEquals(RentalStatus::EXPIRED, $rentalVerified->status);

        $this->assertEquals(CarStatus::AVAILABLE, $this->car->fresh()->status);
        $this->assertEquals(CarStatus::AVAILABLE, $car2->fresh()->status);

        // Uploads must be deleted
        Storage::disk('local')->assertMissing('ktp/test_prepaid.jpg');
        Storage::disk('local')->assertMissing('selfie/test_prepaid.jpg');
        Storage::disk('local')->assertMissing('ktp/test_verified.jpg');
        Storage::disk('local')->assertMissing('selfie/test_verified.jpg');
    }

    public function test_admin_can_mark_ongoing_rental_as_returned_and_release_car(): void
    {
        $this->actingAs($this->admin);

        // Create an ongoing rental
        $rental = Rental::create([
            'user_id' => $this->customer->id,
            'car_id' => $this->car->id,
            'start_date' => '2026-07-10',
            'end_date' => '2026-07-12',
            'total_price' => 3000000,
            'status' => RentalStatus::ONGOING,
            'type' => RentalType::SELF_DRIVE,
            'verification_status' => VerificationStatus::VERIFIED,
            'ktp_path' => 'ktp/test.jpg',
            'selfie_path' => 'selfie/test.jpg',
        ]);

        // Car is currently unavailable
        $this->car->status = CarStatus::UNAVAILABLE;
        $this->car->save();

        $response = $this->post("/dashboard/reservations/{$rental->id}/return");

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $rental->refresh();
        $this->assertEquals(RentalStatus::RETURNED, $rental->status);
        $this->assertNotNull($rental->returned_at);
        $this->assertEquals(CarStatus::AVAILABLE, $this->car->fresh()->status);
    }

    public function test_customer_cancellation_on_identity_step_restores_car_to_available(): void
    {
        $this->actingAs($this->customer);

        $ktp = UploadedFile::fake()->image('ktp.jpg')->size(100);
        $selfie = UploadedFile::fake()->image('selfie.jpg')->size(100);

        // Submit the booking to create the rental and reserve the car
        $response = $this->post('/booking/submit', [
            'car_id' => $this->car->id,
            'start_date' => '2026-07-10',
            'end_date' => '2026-07-12',
            'service_type' => 'self_drive',
            'ktp' => $ktp,
            'selfie' => $selfie,
        ]);

        $response->assertRedirect();

        // Car status becomes UNAVAILABLE when it is booked
        $this->assertEquals(CarStatus::UNAVAILABLE, $this->car->fresh()->status);

        $rental = Rental::first();
        $this->assertNotNull($rental);
        $this->assertEquals(RentalStatus::PENDING_VERIFICATION, $rental->status);

        // Cancel the booking
        $cancelResponse = $this->post("/booking/detail/{$rental->id}/cancel");
        $cancelResponse->assertRedirect();

        // Rental should be CANCELLED and car status restored to AVAILABLE
        $rental->refresh();
        $this->assertEquals(RentalStatus::CANCELLED, $rental->status);
        $this->assertEquals(CarStatus::AVAILABLE, $this->car->fresh()->status);
    }

    public function test_admin_can_cancel_pending_verification_rental(): void
    {
        $this->actingAs($this->admin);

        $rental = Rental::create([
            'user_id' => $this->customer->id,
            'car_id' => $this->car->id,
            'start_date' => '2026-07-10',
            'end_date' => '2026-07-12',
            'total_price' => 3000000,
            'status' => RentalStatus::PENDING_VERIFICATION,
            'type' => RentalType::SELF_DRIVE,
            'verification_status' => VerificationStatus::NEEDS_REVIEW,
            'ktp_path' => 'ktp/test.jpg',
            'selfie_path' => 'selfie/test.jpg',
        ]);

        $response = $this->post("/dashboard/reservations/{$rental->id}/cancel");

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $rental->refresh();
        $this->assertEquals(RentalStatus::CANCELLED, $rental->status);
        $this->assertEquals(CarStatus::AVAILABLE, $this->car->fresh()->status);
    }
}
