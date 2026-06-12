<?php

namespace Tests\Feature;

use App\Enums\CarStatus;
use App\Enums\PaymentStatus;
use App\Enums\RentalStatus;
use App\Enums\RentalType;
use App\Enums\VerificationStatus;
use App\Models\Car;
use App\Models\PaymentHistory;
use App\Models\Rental;
use App\Models\User;
use App\Services\FaceVerificationService;
use App\Services\CustomerNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CustomerNotificationsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $customer;
    private User $otherCustomer;
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

        $this->otherCustomer = User::factory()->create([
            'role' => User::ROLE_CUSTOMER,
        ]);

        $this->car = Car::create([
            'brand' => 'Honda',
            'name' => 'Brio',
            'description' => 'Compact hatchback',
            'transmission' => 'automatic',
            'seat_count' => 5,
            'year' => 2024,
            'cc' => 1200,
            'vehicle_type' => 'city_car',
            'color' => 'White',
            'daily_rate' => 400000,
            'license_plate' => 'B 1234 AB',
            'status' => CarStatus::AVAILABLE,
            'image' => 'brio.jpg',
            'rating' => 4.8,
        ]);
    }

    public function test_booking_submit_creates_verification_notifications(): void
    {
        $this->actingAs($this->customer);

        $this->app->instance(FaceVerificationService::class, new class extends FaceVerificationService {
            public function verify(UploadedFile $ktpFile, UploadedFile $selfieFile): array
            {
                return [
                    'verified' => false,
                    'payload' => ['mock' => true, 'verified' => false],
                ];
            }
        });

        $response = $this->post('/booking/submit', [
            'car_id' => $this->car->id,
            'start_date' => '2026-07-10',
            'end_date' => '2026-07-12',
            'service_type' => 'self_drive',
            'ktp' => UploadedFile::fake()->image('ktp.jpg')->size(100),
            'selfie' => UploadedFile::fake()->image('selfie.jpg')->size(100),
        ]);

        $response->assertRedirect();

        $rental = Rental::firstOrFail();
        $this->assertEquals(RentalStatus::PENDING_VERIFICATION, $rental->status);
        $this->assertEquals(VerificationStatus::NEEDS_REVIEW, $rental->verification_status);

        $titles = $this->customer->notifications()->pluck('data')->map(fn ($data) => $data['title'] ?? null)->filter()->all();

        $this->assertContains('Booking Masuk Verifikasi', $titles);
        $this->assertContains('Menunggu Review Admin', $titles);
    }

    public function test_payment_flow_creates_payment_available_and_paid_notifications(): void
    {
        $this->actingAs($this->customer);

        $rental = Rental::create([
            'user_id' => $this->customer->id,
            'car_id' => $this->car->id,
            'start_date' => '2026-07-10',
            'end_date' => '2026-07-12',
            'total_price' => 800000,
            'status' => RentalStatus::PENDING_VERIFICATION,
            'type' => RentalType::SELF_DRIVE,
            'verification_status' => VerificationStatus::VERIFIED,
            'verified_at' => now()->subMinutes(10),
            'ktp_path' => 'ktp/test.jpg',
            'selfie_path' => 'selfie/test.jpg',
        ]);

        $payResponse = $this->post("/booking/detail/{$rental->id}/pay");
        $payResponse->assertRedirect(route('booking.detail', ['rental' => $rental->id]));

        $rental->refresh();
        $this->assertEquals(RentalStatus::PREPAID, $rental->status);
        $this->assertEquals(1, $this->customer->notifications()->where('data->title', 'Pembayaran Tersedia')->count());

        $payment = PaymentHistory::where('rental_id', $rental->id)->latest()->firstOrFail();

        $this->post('/booking/simulate-payment', [
            'rental_id' => $rental->id,
        ])->assertRedirect(route('booking.detail', ['rental' => $rental->id]));

        $this->assertEquals(RentalStatus::ONGOING, $rental->fresh()->status);
        $this->assertEquals(PaymentStatus::PAID, $payment->fresh()->status);
        $this->assertEquals(1, $this->customer->notifications()->where('data->title', 'Pembayaran Berhasil')->count());
    }

    public function test_customer_cannot_see_other_users_notifications(): void
    {
        $service = app(CustomerNotificationService::class);

        $customerRental = Rental::create([
            'user_id' => $this->customer->id,
            'car_id' => $this->car->id,
            'start_date' => '2026-08-10',
            'end_date' => '2026-08-12',
            'total_price' => 800000,
            'status' => RentalStatus::PENDING_VERIFICATION,
            'type' => RentalType::SELF_DRIVE,
            'verification_status' => VerificationStatus::VERIFIED,
            'verified_at' => now(),
            'ktp_path' => 'ktp/customer.jpg',
            'selfie_path' => 'selfie/customer.jpg',
        ]);

        $otherRental = Rental::create([
            'user_id' => $this->otherCustomer->id,
            'car_id' => $this->car->id,
            'start_date' => '2026-08-15',
            'end_date' => '2026-08-17',
            'total_price' => 800000,
            'status' => RentalStatus::PENDING_VERIFICATION,
            'type' => RentalType::SELF_DRIVE,
            'verification_status' => VerificationStatus::VERIFIED,
            'verified_at' => now(),
            'ktp_path' => 'ktp/other.jpg',
            'selfie_path' => 'selfie/other.jpg',
        ]);

        $service->notifyBookingVerificationStarted($customerRental);
        $service->notifyRentalCancelled($otherRental);

        $response = $this->actingAs($this->customer)->get('/notifications');
        $response->assertOk();
        $response->assertSee('Booking Masuk Verifikasi');
        $response->assertDontSee('Booking Dibatalkan');
    }

    public function test_mark_notification_as_read_clears_unread_state(): void
    {
        $service = app(CustomerNotificationService::class);

        $rental = Rental::create([
            'user_id' => $this->customer->id,
            'car_id' => $this->car->id,
            'start_date' => '2026-09-01',
            'end_date' => '2026-09-03',
            'total_price' => 800000,
            'status' => RentalStatus::PENDING_VERIFICATION,
            'type' => RentalType::SELF_DRIVE,
            'verification_status' => VerificationStatus::VERIFIED,
            'verified_at' => now(),
            'ktp_path' => 'ktp/read.jpg',
            'selfie_path' => 'selfie/read.jpg',
        ]);

        $service->notifyRentalCancelled($rental);

        $notification = $this->customer->notifications()->firstOrFail();
        $this->assertNull($notification->read_at);
        $this->assertEquals(1, $this->customer->unreadNotifications()->count());

        $this->actingAs($this->customer)
            ->post("/notifications/{$notification->id}/read")
            ->assertRedirect();

        $this->assertNotNull($notification->fresh()->read_at);
        $this->assertEquals(0, $this->customer->fresh()->unreadNotifications()->count());
    }
}
