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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentListTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;
    private Car $car;

    protected function setUp(): void
    {
        parent::setUp();

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

    public function test_guest_redirected_to_login_on_payment_list(): void
    {
        $response = $this->get(route('pembayaran.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_customer_can_access_payment_list(): void
    {
        $response = $this->actingAs($this->customer)->get(route('pembayaran.index'));
        $response->assertOk();
        $response->assertViewIs('frontliner.pages.pembayaran');
        $response->assertSee('Riwayat Pembayaran');
    }

    public function test_customer_sees_stats_cards(): void
    {
        // Create a paid rental
        $rentalPaid = $this->createRentalWithPayment(PaymentStatus::PAID, 900000);
        // Create a pending rental
        $rentalPending = $this->createRentalWithPayment(PaymentStatus::PENDING, 450000);

        $response = $this->actingAs($this->customer)->get(route('pembayaran.index'));
        $response->assertOk();

        // Total spent should be Rp 900.000
        $response->assertSee('Rp 900.000');
        // Pending count should be 1
        $response->assertSee('1');
        // Total transactions count should be 2
        $response->assertSee('2');
    }

    public function test_customer_filters_by_status(): void
    {
        $paidPayment = $this->createRentalWithPayment(PaymentStatus::PAID, 900000, 'ORDER-PAID');
        $pendingPayment = $this->createRentalWithPayment(PaymentStatus::PENDING, 450000, 'ORDER-PENDING');

        // Filter status: paid
        $response = $this->actingAs($this->customer)->get(route('pembayaran.index', ['status' => 'paid']));
        $response->assertOk();
        $response->assertSee('ORDER-PAID');
        $response->assertDontSee('ORDER-PENDING');

        // Filter status: pending
        $response = $this->actingAs($this->customer)->get(route('pembayaran.index', ['status' => 'pending']));
        $response->assertOk();
        $response->assertSee('ORDER-PENDING');
        $response->assertDontSee('ORDER-PAID');
    }

    public function test_customer_searches_by_order_id_and_car_name(): void
    {
        $paidPayment = $this->createRentalWithPayment(PaymentStatus::PAID, 900000, 'ORDER-ABC');
        
        // Create another car with different brand
        $otherCar = Car::create(array_merge($this->car->toArray(), [
            'brand' => 'Honda',
            'name' => 'Civic',
            'license_plate' => 'B 9999 XX',
        ]));
        
        $otherPayment = $this->createRentalWithPayment(
            PaymentStatus::PAID,
            1500000,
            'ORDER-XYZ',
            null,
            $otherCar
        );

        // Search for 'Honda'
        $response = $this->actingAs($this->customer)->get(route('pembayaran.index', ['q' => 'Honda']));
        $response->assertOk();
        $response->assertSee('ORDER-XYZ');
        $response->assertDontSee('ORDER-ABC');

        // Search for 'ORDER-ABC'
        $response = $this->actingAs($this->customer)->get(route('pembayaran.index', ['q' => 'ORDER-ABC']));
        $response->assertOk();
        $response->assertSee('ORDER-ABC');
        $response->assertDontSee('ORDER-XYZ');
    }

    public function test_customer_cannot_see_others_payments(): void
    {
        $otherCustomer = User::factory()->create([
            'role' => User::ROLE_CUSTOMER,
        ]);

        // Payment by the current customer
        $myPayment = $this->createRentalWithPayment(PaymentStatus::PAID, 900000, 'ORDER-MINE');

        // Payment by other customer
        $otherPayment = $this->createRentalWithPayment(
            PaymentStatus::PAID,
            900000,
            'ORDER-OTHER',
            $otherCustomer
        );

        $response = $this->actingAs($this->customer)->get(route('pembayaran.index'));
        $response->assertOk();
        $response->assertSee('ORDER-MINE');
        $response->assertDontSee('ORDER-OTHER');
    }

    private function createRentalWithPayment(
        PaymentStatus $status,
        int $price,
        ?string $orderId = null,
        ?User $user = null,
        ?Car $car = null
    ): PaymentHistory {
        $orderId = $orderId ?: 'ORDER-' . uniqid();
        $user = $user ?: $this->customer;
        $car = $car ?: $this->car;

        $rental = Rental::create([
            'user_id' => $user->id,
            'car_id' => $car->id,
            'start_date' => '2026-07-10',
            'end_date' => '2026-07-12',
            'total_price' => $price,
            'status' => $status === PaymentStatus::PAID ? RentalStatus::ONGOING : RentalStatus::PREPAID,
            'type' => RentalType::SELF_DRIVE,
            'verification_status' => VerificationStatus::VERIFIED,
            'verified_at' => now()->subHour(),
            'prepaid_expires_at' => now()->addHours(4),
            'ktp_path' => 'ktp/test.jpg',
            'selfie_path' => 'selfie/test.jpg',
        ]);

        return PaymentHistory::create([
            'rental_id' => $rental->id,
            'amount' => $price,
            'status' => $status,
            'provider' => 'midtrans',
            'provider_order_id' => $orderId,
            'snap_token' => 'snap-token',
            'redirect_url' => 'https://example.test/pay',
            'payload' => ['payment_type' => 'bank_transfer', 'va_numbers' => [['bank' => 'bca']]],
        ]);
    }
}
