<?php

namespace Tests\Feature;

use App\Enums\CarStatus;
use App\Enums\TransmissionType;
use App\Enums\VehicleType;
use App\Models\Car;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Test suite untuk Manajemen Mobil – Backoffice Dashboard.
 *
 * Mencakup:
 * - Form POST (Create) dengan validasi lengkap
 * - Form PUT (Edit) dengan validasi lengkap
 * - Validasi field wajib (required fields)
 * - Validasi enum (vehicle_type, transmission)
 * - Validasi layanan (minimal satu layanan harus dipilih)
 * - Validasi plat nomor unik
 * - Update status (available ↔ maintenance)
 * - Hapus mobil (delete)
 * - Akses hanya admin (non-admin dilarang)
 */
class CarManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $customer;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $this->customer = User::factory()->create([
            'role' => 'customer',
        ]);
    }

    // ─────────────────────────────────────────────────────
    // Helper: membuat fake file gambar tanpa GD extension
    // ─────────────────────────────────────────────────────

    /**
     * Membuat fake image file tanpa memerlukan GD extension.
     * Menggunakan binary JPEG minimal yang valid.
     */
    private function fakeImage(string $name = 'car.jpg', string $mimeType = 'image/jpeg'): UploadedFile
    {
        // Minimal valid JPEG binary (1x1 pixel)
        $jpegBinary = "\xFF\xD8\xFF\xE0\x00\x10JFIF\x00\x01\x01\x00\x00\x01\x00\x01\x00\x00"
            . "\xFF\xDB\x00C\x00\x08\x06\x06\x07\x06\x05\x08\x07\x07\x07\t\t"
            . "\x08\n\x0C\x14\r\x0C\x0B\x0B\x0C\x19\x12\x13\x0F\x14\x1D\x1A"
            . "\x1F\x1E\x1D\x1A\x1C\x1C $.' \",#\x1C\x1C(7),01444\x1F'9=82<.342\x1E"
            . "\xFF\xC0\x00\x0B\x08\x00\x01\x00\x01\x01\x01\x11\x00\xFF\xC4\x00"
            . "\x1F\x00\x00\x01\x05\x01\x01\x01\x01\x01\x01\x00\x00\x00\x00\x00"
            . "\x00\x00\x00\x01\x02\x03\x04\x05\x06\x07\x08\t\n\x0B\xFF\xC4\x00"
            . "\xB5\x10\x00\x02\x01\x03\x03\x02\x04\x03\x05\x05\x04\x04\x00\x00"
            . "\x01}\x01\x02\x03\x00\x04\x11\x05\x12!1A\x06\x13Qa\x07\"q\x142\x81"
            . "\xFF\xDA\x00\x08\x01\x01\x00\x00?\x00\xFB\xD2\x8A(\x03\xFF\xD9";

        $tmpPath = tempnam(sys_get_temp_dir(), 'fake_img_');
        file_put_contents($tmpPath, $jpegBinary);

        return new UploadedFile(
            $tmpPath,
            $name,
            $mimeType,
            null,
            true // test mode
        );
    }

    /**
     * Data valid untuk create mobil.
     */
    private function validCarPayload(array $overrides = []): array
    {
        return array_merge([
            'brand'                => 'Toyota',
            'name'                 => 'Avanza',
            'description'          => 'Mobil keluarga yang nyaman.',
            'transmission'         => TransmissionType::MANUAL->value,
            'seat_count'           => 7,
            'year'                 => 2022,
            'cc'                   => 1500,
            'vehicle_type'         => VehicleType::MPV->value,
            'color'                => 'Putih',
            'daily_rate'           => 350000,
            'license_plate'        => 'B 1234 TES',
            'image'                => $this->fakeImage(),
            'self_drive_available' => '1',
            'driver_available'     => '0',
        ], $overrides);
    }

    // ─────────────────────────────────────────────────────
    // CREATE (POST /dashboard/cars) – form storeCar
    // ─────────────────────────────────────────────────────

    /** [CREATE] Admin dapat membuat mobil baru dengan data lengkap */
    public function test_admin_can_create_car_with_valid_data(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('backoffice.cars.store'), $this->validCarPayload());

        $response->assertRedirect(route('backoffice.cars'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('cars', [
            'brand'        => 'Toyota',
            'name'         => 'Avanza',
            'license_plate' => 'B 1234 TES',
            'status'       => CarStatus::AVAILABLE->value,
        ]);
    }

    /** [CREATE] Semua jenis vehicle_type yang valid harus diterima */
    public function test_create_car_accepts_all_valid_vehicle_types(): void
    {
        $this->actingAs($this->admin);

        foreach (VehicleType::cases() as $index => $type) {
            $plate = 'B ' . (1000 + $index) . ' TYPE';
            $response = $this->post(route('backoffice.cars.store'), $this->validCarPayload([
                'vehicle_type'  => $type->value,
                'license_plate' => $plate,
                'name'          => 'Car ' . $type->value,
            ]));

            $response->assertRedirect(route('backoffice.cars'));
            $this->assertDatabaseHas('cars', [
                'vehicle_type'  => $type->value,
                'license_plate' => $plate,
            ]);
        }
    }

    /** [CREATE] Semua jenis transmisi yang valid harus diterima */
    public function test_create_car_accepts_all_valid_transmission_types(): void
    {
        $this->actingAs($this->admin);

        foreach (TransmissionType::cases() as $index => $type) {
            $plate = 'B ' . (2000 + $index) . ' TRNS';
            $response = $this->post(route('backoffice.cars.store'), $this->validCarPayload([
                'transmission'  => $type->value,
                'license_plate' => $plate,
                'name'          => 'Car Transmission ' . $type->value,
            ]));

            $response->assertRedirect(route('backoffice.cars'));
            $this->assertDatabaseHas('cars', [
                'transmission'  => $type->value,
                'license_plate' => $plate,
            ]);
        }
    }

    /** [CREATE] Mobil dengan galeri gambar dapat dibuat */
    public function test_create_car_with_gallery_images(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('backoffice.cars.store'), $this->validCarPayload([
            'gallery_images' => [
                $this->fakeImage('gallery1.jpg'),
                $this->fakeImage('gallery2.jpg'),
            ],
        ]));

        $response->assertRedirect(route('backoffice.cars'));
        $this->assertDatabaseHas('cars', ['name' => 'Avanza']);

        $car = Car::where('license_plate', 'B 1234 TES')->first();
        $this->assertNotNull($car);
        $this->assertIsArray($car->gallery_images);
        $this->assertCount(2, $car->gallery_images);
    }

    // ─────────────────────────────────────────────────────
    // VALIDASI CREATE – field wajib & enum
    // ─────────────────────────────────────────────────────

    /** [VALIDASI] vehicle_type tidak valid harus ditolak */
    public function test_create_car_rejects_invalid_vehicle_type(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('backoffice.cars.store'), $this->validCarPayload([
            'vehicle_type' => 'helicopter',
        ]));

        $response->assertSessionHasErrors(['vehicle_type']);
        $this->assertDatabaseMissing('cars', ['license_plate' => 'B 1234 TES']);
    }

    /** [VALIDASI] Transmisi tidak valid harus ditolak */
    public function test_create_car_rejects_invalid_transmission(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('backoffice.cars.store'), $this->validCarPayload([
            'transmission' => 'semi_automatic',
        ]));

        $response->assertSessionHasErrors(['transmission']);
    }

    /** [VALIDASI] Field brand wajib diisi */
    public function test_create_car_requires_brand(): void
    {
        $this->actingAs($this->admin);

        $payload = $this->validCarPayload();
        unset($payload['brand']);

        $response = $this->post(route('backoffice.cars.store'), $payload);

        $response->assertSessionHasErrors(['brand']);
    }

    /** [VALIDASI] Field name wajib diisi */
    public function test_create_car_requires_name(): void
    {
        $this->actingAs($this->admin);

        $payload = $this->validCarPayload();
        unset($payload['name']);

        $response = $this->post(route('backoffice.cars.store'), $payload);

        $response->assertSessionHasErrors(['name']);
    }

    /** [VALIDASI] Field license_plate wajib diisi */
    public function test_create_car_requires_license_plate(): void
    {
        $this->actingAs($this->admin);

        $payload = $this->validCarPayload();
        unset($payload['license_plate']);

        $response = $this->post(route('backoffice.cars.store'), $payload);

        $response->assertSessionHasErrors(['license_plate']);
    }

    /** [VALIDASI] Field description wajib diisi */
    public function test_create_car_requires_description(): void
    {
        $this->actingAs($this->admin);

        $payload = $this->validCarPayload();
        unset($payload['description']);

        $response = $this->post(route('backoffice.cars.store'), $payload);

        $response->assertSessionHasErrors(['description']);
    }

    /** [VALIDASI] Plat nomor harus unik */
    public function test_create_car_rejects_duplicate_license_plate(): void
    {
        $this->actingAs($this->admin);

        // Buat mobil pertama
        $this->post(route('backoffice.cars.store'), $this->validCarPayload());

        // Coba buat mobil kedua dengan plat yang sama
        $response = $this->post(route('backoffice.cars.store'), $this->validCarPayload([
            'name' => 'Avanza Duplikat',
        ]));

        $response->assertSessionHasErrors(['license_plate']);
        $this->assertDatabaseMissing('cars', ['name' => 'Avanza Duplikat']);
    }

    /** [VALIDASI] Minimal satu layanan (self_drive atau driver) harus dipilih */
    public function test_create_car_requires_at_least_one_service(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('backoffice.cars.store'), $this->validCarPayload([
            'self_drive_available' => '0',
            'driver_available'     => '0',
        ]));

        $response->assertSessionHasErrors(['service_selection']);
    }

    /** [VALIDASI] Daily rate tidak boleh negatif */
    public function test_create_car_rejects_negative_daily_rate(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('backoffice.cars.store'), $this->validCarPayload([
            'daily_rate' => -1,
        ]));

        $response->assertSessionHasErrors(['daily_rate']);
    }

    /** [VALIDASI] Tahun tidak boleh di bawah 1990 */
    public function test_create_car_rejects_year_below_1990(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('backoffice.cars.store'), $this->validCarPayload([
            'year' => 1989,
        ]));

        $response->assertSessionHasErrors(['year']);
    }

    /** [VALIDASI] Gambar utama (image) wajib di-upload saat create */
    public function test_create_car_requires_image(): void
    {
        $this->actingAs($this->admin);

        $payload = $this->validCarPayload();
        unset($payload['image']);

        $response = $this->post(route('backoffice.cars.store'), $payload);

        $response->assertSessionHasErrors(['image']);
    }

    // ─────────────────────────────────────────────────────
    // EDIT (PUT /dashboard/cars/{car}) – form updateCar
    // ─────────────────────────────────────────────────────

    /** [EDIT] Admin dapat memperbarui data mobil */
    public function test_admin_can_update_car(): void
    {
        $this->actingAs($this->admin);

        $car = Car::factory()->create([
            'brand'                => 'Honda',
            'name'                 => 'Brio',
            'transmission'         => TransmissionType::MANUAL->value,
            'vehicle_type'         => VehicleType::CITY_CAR->value,
            'license_plate'        => 'B 9999 OLD',
            'daily_rate'           => 200000,
            'status'               => CarStatus::AVAILABLE->value,
            'self_drive_available' => true,
            'driver_available'     => false,
            'seat_count'           => 4,
            'year'                 => 2020,
            'cc'                   => 1200,
            'color'                => 'Merah',
            'description'          => 'Mobil lama',
        ]);

        $response = $this->put(route('backoffice.cars.update', $car), [
            'brand'                => 'Honda',
            'name'                 => 'Brio Updated',
            'description'          => 'Deskripsi diperbarui',
            'transmission'         => TransmissionType::AUTOMATIC->value,
            'seat_count'           => 5,
            'year'                 => 2023,
            'cc'                   => 1300,
            'vehicle_type'         => VehicleType::HATCHBACK->value,
            'color'                => 'Biru',
            'daily_rate'           => 250000,
            'license_plate'        => 'B 9999 NEW',
            'self_drive_available' => '1',
            'driver_available'     => '0',
        ]);

        $response->assertRedirect(route('backoffice.cars'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('cars', [
            'id'           => $car->id,
            'name'         => 'Brio Updated',
            'transmission' => TransmissionType::AUTOMATIC->value,
            'vehicle_type' => VehicleType::HATCHBACK->value,
            'license_plate' => 'B 9999 NEW',
            'daily_rate'   => 250000,
        ]);
    }

    /** [EDIT] Admin dapat mengganti gambar utama saat update */
    public function test_admin_can_update_car_with_new_image(): void
    {
        $this->actingAs($this->admin);

        $car = Car::factory()->create([
            'brand'                => 'Suzuki',
            'name'                 => 'Ertiga',
            'transmission'         => TransmissionType::AUTOMATIC->value,
            'vehicle_type'         => VehicleType::MPV->value,
            'license_plate'        => 'B 7777 IMG',
            'daily_rate'           => 300000,
            'status'               => CarStatus::AVAILABLE->value,
            'self_drive_available' => true,
            'driver_available'     => false,
            'seat_count'           => 7,
            'year'                 => 2021,
            'cc'                   => 1500,
            'color'                => 'Silver',
            'description'          => 'Ertiga MPV',
        ]);

        $response = $this->put(route('backoffice.cars.update', $car), [
            'brand'                => 'Suzuki',
            'name'                 => 'Ertiga',
            'description'          => 'Ertiga MPV Updated',
            'transmission'         => TransmissionType::AUTOMATIC->value,
            'seat_count'           => 7,
            'year'                 => 2021,
            'cc'                   => 1500,
            'vehicle_type'         => VehicleType::MPV->value,
            'color'                => 'Silver',
            'daily_rate'           => 300000,
            'license_plate'        => 'B 7777 IMG',
            'image'                => $this->fakeImage('new_image.jpg'),
            'self_drive_available' => '1',
            'driver_available'     => '0',
        ]);

        $response->assertRedirect(route('backoffice.cars'));
        $response->assertSessionHas('success');
    }

    /** [VALIDASI EDIT] Tidak dapat mengupdate dengan vehicle_type tidak valid */
    public function test_update_car_rejects_invalid_vehicle_type(): void
    {
        $this->actingAs($this->admin);

        $car = Car::factory()->create([
            'brand'                => 'Daihatsu',
            'name'                 => 'Sigra',
            'transmission'         => TransmissionType::MANUAL->value,
            'vehicle_type'         => VehicleType::MPV->value,
            'license_plate'        => 'B 5555 UPD',
            'daily_rate'           => 200000,
            'status'               => CarStatus::AVAILABLE->value,
            'self_drive_available' => true,
            'driver_available'     => false,
            'seat_count'           => 7,
            'year'                 => 2020,
            'cc'                   => 1000,
            'color'                => 'Hitam',
            'description'          => 'Sigra LCGC',
        ]);

        $response = $this->put(route('backoffice.cars.update', $car), [
            'brand'                => 'Daihatsu',
            'name'                 => 'Sigra',
            'description'          => 'Sigra LCGC',
            'transmission'         => TransmissionType::MANUAL->value,
            'seat_count'           => 7,
            'year'                 => 2020,
            'cc'                   => 1000,
            'vehicle_type'         => 'tank', // Invalid
            'color'                => 'Hitam',
            'daily_rate'           => 200000,
            'license_plate'        => 'B 5555 UPD',
            'self_drive_available' => '1',
            'driver_available'     => '0',
        ]);

        $response->assertSessionHasErrors(['vehicle_type']);
        $this->assertDatabaseHas('cars', [
            'id'           => $car->id,
            'vehicle_type' => VehicleType::MPV->value, // Tidak berubah
        ]);
    }

    /** [VALIDASI EDIT] Plat nomor harus unik kecuali milik mobil yang sedang diedit */
    public function test_update_car_allows_same_license_plate_for_same_car(): void
    {
        $this->actingAs($this->admin);

        $car = Car::factory()->create([
            'brand'                => 'Toyota',
            'name'                 => 'Rush',
            'transmission'         => TransmissionType::MANUAL->value,
            'vehicle_type'         => VehicleType::SUV->value,
            'license_plate'        => 'B 3333 SAM',
            'daily_rate'           => 400000,
            'status'               => CarStatus::AVAILABLE->value,
            'self_drive_available' => true,
            'driver_available'     => false,
            'seat_count'           => 7,
            'year'                 => 2022,
            'cc'                   => 1500,
            'color'                => 'Hitam',
            'description'          => 'Toyota Rush SUV',
        ]);

        // Update dengan plat yang sama (harus berhasil, bukan gagal karena "duplicate")
        $response = $this->put(route('backoffice.cars.update', $car), [
            'brand'                => 'Toyota',
            'name'                 => 'Rush Updated',
            'description'          => 'Toyota Rush SUV Updated',
            'transmission'         => TransmissionType::AUTOMATIC->value,
            'seat_count'           => 7,
            'year'                 => 2022,
            'cc'                   => 1500,
            'vehicle_type'         => VehicleType::SUV->value,
            'color'                => 'Hitam',
            'daily_rate'           => 420000,
            'license_plate'        => 'B 3333 SAM', // Plat sama
            'self_drive_available' => '1',
            'driver_available'     => '0',
        ]);

        $response->assertRedirect(route('backoffice.cars'));
        $this->assertDatabaseHas('cars', [
            'id'   => $car->id,
            'name' => 'Rush Updated',
        ]);
    }

    // ─────────────────────────────────────────────────────
    // UPDATE STATUS (PATCH /dashboard/cars/{car}/status)
    // ─────────────────────────────────────────────────────

    /** [STATUS] Admin dapat mengubah status mobil ke maintenance */
    public function test_admin_can_set_car_to_maintenance(): void
    {
        $this->actingAs($this->admin);

        $car = Car::factory()->create([
            'status'               => CarStatus::AVAILABLE->value,
            'self_drive_available' => true,
            'driver_available'     => false,
        ]);

        $response = $this->patch(route('backoffice.cars.update-status', $car), [
            'status' => CarStatus::UNAVAILABLE->value,
        ]);

        $response->assertRedirect(route('backoffice.cars'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('cars', [
            'id'     => $car->id,
            'status' => CarStatus::UNAVAILABLE->value,
        ]);
    }

    /** [STATUS] Admin dapat mengaktifkan kembali mobil dari maintenance */
    public function test_admin_can_set_car_back_to_available(): void
    {
        $this->actingAs($this->admin);

        $car = Car::factory()->create([
            'status'               => CarStatus::UNAVAILABLE->value,
            'self_drive_available' => true,
            'driver_available'     => false,
        ]);

        $response = $this->patch(route('backoffice.cars.update-status', $car), [
            'status' => CarStatus::AVAILABLE->value,
        ]);

        $response->assertRedirect(route('backoffice.cars'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('cars', [
            'id'     => $car->id,
            'status' => CarStatus::AVAILABLE->value,
        ]);
    }

    /** [STATUS] Status tidak valid harus ditolak */
    public function test_update_car_status_rejects_invalid_status(): void
    {
        $this->actingAs($this->admin);

        $car = Car::factory()->create([
            'status'               => CarStatus::AVAILABLE->value,
            'self_drive_available' => true,
            'driver_available'     => false,
        ]);

        $response = $this->patch(route('backoffice.cars.update-status', $car), [
            'status' => 'broken',
        ]);

        $response->assertSessionHasErrors(['status']);
    }

    // ─────────────────────────────────────────────────────
    // DELETE (DELETE /dashboard/cars/{car})
    // ─────────────────────────────────────────────────────

    /** [DELETE] Admin dapat menghapus mobil */
    public function test_admin_can_delete_car(): void
    {
        $this->actingAs($this->admin);

        $car = Car::factory()->create([
            'brand'                => 'Mitsubishi',
            'name'                 => 'Xpander',
            'self_drive_available' => true,
            'driver_available'     => false,
        ]);

        $response = $this->delete(route('backoffice.cars.destroy', $car));

        $response->assertRedirect(route('backoffice.cars'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('cars', ['id' => $car->id]);
    }

    // ─────────────────────────────────────────────────────
    // AKSES KONTROL – Non-admin dilarang
    // ─────────────────────────────────────────────────────

    /** [AKSES] Customer tidak dapat mengakses halaman manajemen mobil */
    public function test_customer_cannot_access_cars_dashboard(): void
    {
        $this->actingAs($this->customer);

        $response = $this->get(route('backoffice.cars'));

        // Harus di-redirect atau mendapat forbidden
        $response->assertStatus(302);
    }

    /** [AKSES] Customer tidak dapat membuat mobil */
    public function test_customer_cannot_create_car(): void
    {
        $this->actingAs($this->customer);

        $response = $this->post(route('backoffice.cars.store'), $this->validCarPayload());

        $response->assertStatus(302);
        $this->assertDatabaseMissing('cars', ['license_plate' => 'B 1234 TES']);
    }

    /** [AKSES] Guest (tidak login) tidak dapat membuat mobil */
    public function test_guest_cannot_create_car(): void
    {
        $response = $this->post(route('backoffice.cars.store'), $this->validCarPayload());

        $response->assertRedirect(route('login'));
        $this->assertDatabaseMissing('cars', ['license_plate' => 'B 1234 TES']);
    }

    /** [AKSES] Guest tidak dapat menghapus mobil */
    public function test_guest_cannot_delete_car(): void
    {
        $car = Car::factory()->create([
            'self_drive_available' => true,
            'driver_available'     => false,
        ]);

        $response = $this->delete(route('backoffice.cars.destroy', $car));

        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('cars', ['id' => $car->id]);
    }

    // ─────────────────────────────────────────────────────
    // LISTING (GET /dashboard/cars)
    // ─────────────────────────────────────────────────────

    /** [LISTING] Admin dapat melihat halaman daftar mobil */
    public function test_admin_can_view_cars_listing(): void
    {
        $this->actingAs($this->admin);

        Car::factory()->count(3)->create([
            'self_drive_available' => true,
            'driver_available'     => false,
        ]);

        $response = $this->get(route('backoffice.cars'));

        $response->assertStatus(200);
        $response->assertViewIs('backoffice.cars');
        $response->assertViewHas('cars');
        $response->assertViewHas('stats');
    }

    /** [LISTING] Halaman listing menampilkan stat mobil yang benar */
    public function test_cars_listing_shows_correct_stats(): void
    {
        $this->actingAs($this->admin);

        Car::factory()->count(2)->create([
            'status'               => CarStatus::AVAILABLE->value,
            'self_drive_available' => true,
            'driver_available'     => false,
        ]);

        Car::factory()->create([
            'status'               => CarStatus::UNAVAILABLE->value,
            'self_drive_available' => true,
            'driver_available'     => false,
        ]);

        $response = $this->get(route('backoffice.cars'));

        $response->assertStatus(200);
        $response->assertViewHas('stats', function ($stats) {
            return $stats['total'] === 3
                && $stats['available'] === 2
                && $stats['maintenance'] === 1;
        });
    }
}
