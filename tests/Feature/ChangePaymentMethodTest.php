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
use App\Services\MidtransService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ChangePaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;
    private Car $car;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        $this->customer = User::factory()->create([
            'role' => User::ROLE_CUSTOMER,
        ]);

        $this->car = Car::create([
            'brand' => 'Toyota',
            'name' => 'Avanza',
            'description' => 'Family MPV',
            'transmission' => 'automatic',
            'seat_count' => 7,
            'year' => 2024,
            'cc' => 1300,
            'vehicle_type' => 'mpv',
            'color' => 'Silver',
            'daily_rate' => 450000,
            'license_plate' => 'B 1234 CD',
            'status' => CarStatus::AVAILABLE,
            'image' => 'avanza.jpg',
            'rating' => 4.7,
        ]);
    }

    public function test_change_payment_method_button_is_visible_for_eligible_rental(): void
    {
        $rental = $this->createPrepaidRental();

        $response = $this->actingAs($this->customer)->get(route('booking.detail', ['rental' => $rental->id]));

        $response->assertOk();
        $response->assertSee('Bayar Sekarang via Midtrans');
        $response->assertSee('Ganti Metode Pembayaran');
        $response->assertSee('Mengganti metode pembayaran tidak menambah batas waktu pembayaran.');
    }

    public function test_change_payment_method_button_is_hidden_when_not_verified(): void
    {
        $rental = Rental::create([
            'user_id' => $this->customer->id,
            'car_id' => $this->car->id,
            'start_date' => '2026-07-10',
            'end_date' => '2026-07-12',
            'total_price' => 900000,
            'status' => RentalStatus::PENDING_VERIFICATION,
            'type' => RentalType::SELF_DRIVE,
            'verification_status' => VerificationStatus::NEEDS_REVIEW,
            'verified_at' => now()->subHour(),
            'ktp_path' => 'ktp/test.jpg',
            'selfie_path' => 'selfie/test.jpg',
        ]);

        PaymentHistory::create([
            'rental_id' => $rental->id,
            'amount' => $rental->total_price,
            'status' => PaymentStatus::PENDING,
            'provider' => 'midtrans',
            'provider_order_id' => 'OLD-ORDER-1',
            'snap_token' => 'old-token',
            'redirect_url' => 'https://example.test/old',
            'payload' => ['seed' => true],
        ]);

        $response = $this->actingAs($this->customer)->get(route('booking.detail', ['rental' => $rental->id]));

        $response->assertOk();
        $response->assertDontSee('Ganti Metode Pembayaran');
    }

    public function test_change_payment_method_button_is_hidden_when_payment_is_expired(): void
    {
        $rental = $this->createPrepaidRental([
            'prepaid_expires_at' => now()->subMinute(),
        ]);

        $response = $this->actingAs($this->customer)->get(route('booking.detail', ['rental' => $rental->id]));

        $response->assertOk();
        $response->assertDontSee('Ganti Metode Pembayaran');
    }

    public function test_change_payment_method_recreates_payment_without_resetting_expiry(): void
    {
        $this->app->instance(MidtransService::class, new class extends MidtransService {
            public function createTransaction(Rental $rental, string $orderId): array
            {
                return [
                    'token' => 'new-snap-token',
                    'redirect_url' => 'https://example.test/new-payment',
                ];
            }
        });

        $rental = $this->createPrepaidRental();
        $oldExpiry = $rental->prepaid_expires_at;

        $oldPayment = $rental->paymentHistories()->firstOrFail();

        $response = $this->actingAs($this->customer)->post(route('booking.change-payment-method', ['rental' => $rental->id]));

        $response->assertRedirect('https://example.test/new-payment');

        $rental->refresh();
        $newPayment = $rental->paymentHistories()->latest('id')->firstOrFail();

        $this->assertEquals($oldExpiry?->toDateTimeString(), $rental->prepaid_expires_at?->toDateTimeString());
        $this->assertEquals(PaymentStatus::CANCELLED, $oldPayment->fresh()->status);
        $this->assertEquals(PaymentStatus::PENDING, $newPayment->status);
        $this->assertNotEquals($oldPayment->provider_order_id, $newPayment->provider_order_id);
        $this->assertGreaterThan($oldPayment->id, $newPayment->id);
    }

    public function test_change_payment_method_after_expiry_marks_rental_expired(): void
    {
        $this->app->instance(MidtransService::class, new class extends MidtransService {
            public function createTransaction(Rental $rental, string $orderId): array
            {
                return [
                    'token' => 'should-not-be-used',
                    'redirect_url' => 'https://example.test/should-not-be-used',
                ];
            }
        });

        $rental = $this->createPrepaidRental([
            'prepaid_expires_at' => now()->subMinute(),
        ]);

        $response = $this->actingAs($this->customer)->post(route('booking.change-payment-method', ['rental' => $rental->id]));

        $response->assertRedirect(route('booking.detail', ['rental' => $rental->id]));
        $response->assertSessionHas('error', 'Waktu pembayaran telah habis. Booking dibatalkan dan mobil kembali tersedia.');

        $rental->refresh();
        $this->assertEquals(RentalStatus::EXPIRED, $rental->status);
        $this->assertNull($rental->prepaid_expires_at);
        $this->assertEquals(CarStatus::AVAILABLE, $this->car->fresh()->status);
        $this->assertEquals(PaymentStatus::EXPIRED, $rental->paymentHistories()->latest('id')->firstOrFail()->status);
    }

    public function test_webhook_ignores_cancelled_old_payment_and_keeps_rental_prepaid(): void
    {
        Config::set('services.midtrans.server_key', 'test-secret');

        $rental = $this->createPrepaidRental();
        $oldPayment = $rental->paymentHistories()->firstOrFail();

        $oldPayment->status = PaymentStatus::CANCELLED;
        $oldPayment->save();

        $payload = $this->makeWebhookPayload($oldPayment->provider_order_id, 'settlement', $oldPayment->amount);

        $response = $this->postJson('/api/v1/payment/webhook', $payload);

        $response->assertOk();
        $rental->refresh();
        $this->assertEquals(RentalStatus::PREPAID, $rental->status);
        $this->assertEquals(PaymentStatus::CANCELLED, $oldPayment->fresh()->status);
    }

    public function test_webhook_accepts_latest_active_payment_and_marks_rental_ongoing(): void
    {
        Config::set('services.midtrans.server_key', 'test-secret');

        $rental = $this->createPrepaidRental();
        $oldPayment = $rental->paymentHistories()->firstOrFail();

        $oldPayment->status = PaymentStatus::CANCELLED;
        $oldPayment->save();

        $newPayment = PaymentHistory::create([
            'rental_id' => $rental->id,
            'amount' => $rental->total_price,
            'status' => PaymentStatus::PENDING,
            'provider' => 'midtrans',
            'provider_order_id' => 'NEW-ORDER-1',
            'snap_token' => 'new-token',
            'redirect_url' => 'https://example.test/new',
            'payload' => ['seed' => true],
        ]);

        $payload = $this->makeWebhookPayload($newPayment->provider_order_id, 'settlement', $newPayment->amount);

        $response = $this->postJson('/api/v1/payment/webhook', $payload);

        $response->assertOk();

        $rental->refresh();
        $this->assertEquals(RentalStatus::ONGOING, $rental->status);
        $this->assertEquals(PaymentStatus::PAID, $newPayment->fresh()->status);
        $this->assertEquals(CarStatus::UNAVAILABLE, $this->car->fresh()->status);
    }

    private function createPrepaidRental(array $overrides = []): Rental
    {
        $rental = Rental::create(array_merge([
            'user_id' => $this->customer->id,
            'car_id' => $this->car->id,
            'start_date' => '2026-07-10',
            'end_date' => '2026-07-12',
            'total_price' => 900000,
            'status' => RentalStatus::PREPAID,
            'type' => RentalType::SELF_DRIVE,
            'verification_status' => VerificationStatus::VERIFIED,
            'verified_at' => now()->subHour(),
            'prepaid_expires_at' => now()->addHours(4),
            'ktp_path' => 'ktp/test.jpg',
            'selfie_path' => 'selfie/test.jpg',
        ], $overrides));

        PaymentHistory::create([
            'rental_id' => $rental->id,
            'amount' => $rental->total_price,
            'status' => PaymentStatus::PENDING,
            'provider' => 'midtrans',
            'provider_order_id' => 'OLD-ORDER-'.$rental->id,
            'snap_token' => 'old-token-'.$rental->id,
            'redirect_url' => 'https://example.test/old-'.$rental->id,
            'payload' => ['seed' => true],
        ]);

        return $rental->fresh(['paymentHistories']);
    }

    private function makeWebhookPayload(string $orderId, string $transactionStatus, int $grossAmount): array
    {
        $statusCode = '200';
        $grossAmountString = (string) $grossAmount;
        $signatureKey = hash('sha512', $orderId.$statusCode.$grossAmountString.'test-secret');

        return [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmountString,
            'signature_key' => $signatureKey,
            'transaction_status' => $transactionStatus,
        ];
    }
}
