<?php

namespace Tests\Feature;

use App\Enums\CarStatus;
use App\Enums\RentalStatus;
use App\Enums\RentalType;
use App\Enums\VerificationStatus;
use App\Models\Car;
use App\Models\Rental;
use App\Models\User;
use App\Services\CustomerNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BackofficeNotificationsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $customer;
    private Car $car;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $this->customer = User::factory()->create([
            'role' => User::ROLE_CUSTOMER,
        ]);

        $this->car = Car::create([
            'brand' => 'Toyota',
            'name' => 'Avanza',
            'description' => 'MPV',
            'transmission' => 'automatic',
            'seat_count' => 7,
            'year' => 2024,
            'cc' => 1500,
            'vehicle_type' => 'mpv',
            'color' => 'Silver',
            'daily_rate' => 500000,
            'license_plate' => 'B 1234 TEST',
            'status' => CarStatus::AVAILABLE,
            'image' => 'avanza.jpg',
            'rating' => 4.8,
        ]);
    }

    public function test_admin_receives_backend_notification_for_new_reservation(): void
    {
        $rental = Rental::create([
            'user_id' => $this->customer->id,
            'car_id' => $this->car->id,
            'start_date' => '2026-08-01',
            'end_date' => '2026-08-03',
            'total_price' => 1000000,
            'status' => RentalStatus::PENDING_VERIFICATION,
            'type' => RentalType::SELF_DRIVE,
            'verification_status' => VerificationStatus::PENDING,
            'ktp_path' => 'ktp/test.jpg',
            'selfie_path' => 'selfie/test.jpg',
        ]);

        app(CustomerNotificationService::class)->notifyBookingVerificationStarted($rental);

        $notification = $this->admin->notifications()
            ->where('data->audience', 'admin')
            ->first();

        $this->assertNotNull($notification);
        $this->assertSame('Reservasi Masuk', $notification->data['title'] ?? null);
        $this->assertSame('reservations', $notification->data['category'] ?? null);
        $this->assertSame($rental->id, $notification->data['rental_id'] ?? null);
    }

    public function test_admin_can_mark_notification_as_read(): void
    {
        $rental = Rental::create([
            'user_id' => $this->customer->id,
            'car_id' => $this->car->id,
            'start_date' => '2026-08-10',
            'end_date' => '2026-08-12',
            'total_price' => 1000000,
            'status' => RentalStatus::PENDING_VERIFICATION,
            'type' => RentalType::SELF_DRIVE,
            'verification_status' => VerificationStatus::NEEDS_REVIEW,
            'ktp_path' => 'ktp/test.jpg',
            'selfie_path' => 'selfie/test.jpg',
        ]);

        app(CustomerNotificationService::class)->notifyVerificationNeedsReview($rental);

        $notification = $this->admin->notifications()
            ->where('data->audience', 'admin')
            ->firstOrFail();

        $this->actingAs($this->admin)
            ->post(route('backoffice.notifications.read', $notification->id))
            ->assertRedirect();

        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_open_admin_notification_marks_as_read_and_redirects(): void
    {
        $rental = Rental::create([
            'user_id' => $this->customer->id,
            'car_id' => $this->car->id,
            'start_date' => '2026-08-20',
            'end_date' => '2026-08-22',
            'total_price' => 1000000,
            'status' => RentalStatus::PENDING_VERIFICATION,
            'type' => RentalType::SELF_DRIVE,
            'verification_status' => VerificationStatus::NEEDS_REVIEW,
            'ktp_path' => 'ktp/test.jpg',
            'selfie_path' => 'selfie/test.jpg',
        ]);

        app(CustomerNotificationService::class)->notifyVerificationNeedsReview($rental);

        $notification = $this->admin->notifications()
            ->where('data->audience', 'admin')
            ->firstOrFail();

        $this->actingAs($this->admin)
            ->get(route('backoffice.notifications.open', $notification->id))
            ->assertRedirect(route('backoffice.reservations', [
                'status_filter' => 'waiting_review',
                'rental_id' => $rental->id,
            ]));

        $this->assertNotNull($notification->fresh()->read_at);
    }
}
