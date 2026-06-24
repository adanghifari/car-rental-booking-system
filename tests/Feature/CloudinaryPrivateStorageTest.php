<?php

namespace Tests\Feature;

use App\Models\Car;
use App\Models\Rental;
use App\Models\User;
use App\Services\CloudinaryMediaService;
use App\Services\FaceVerificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class CloudinaryPrivateStorageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    public function test_rental_documents_are_uploaded_privately_to_cloudinary_when_configured(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_CUSTOMER]);
        $car = Car::factory()->create(['status' => \App\Enums\CarStatus::AVAILABLE]);

        // Mock CloudinaryMediaService
        $cloudinaryMock = Mockery::mock(CloudinaryMediaService::class);
        $cloudinaryMock->shouldReceive('configured')->andReturn(true);
        $cloudinaryMock->shouldReceive('uploadPrivate')
            ->twice()
            ->andReturn('cloudinary-private://rentals/docs/mock_public_id');

        $this->app->instance(CloudinaryMediaService::class, $cloudinaryMock);

        // Mock FaceVerificationService to auto-verify
        $faceVerificationMock = Mockery::mock(FaceVerificationService::class);
        $faceVerificationMock->shouldReceive('verify')->andReturn([
            'verified' => true,
            'payload' => ['confidence' => 0.95],
        ]);
        $this->app->instance(FaceVerificationService::class, $faceVerificationMock);

        $ktp = UploadedFile::fake()->image('ktp.jpg');
        $selfie = UploadedFile::fake()->image('selfie.jpg');

        $response = $this->actingAs($user)->postJson('/api/v1/rentals', [
            'car_id' => $car->id,
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
            'type' => 'Self Drive',
            'ktp' => $ktp,
            'selfie' => $selfie,
        ]);

        $response->assertCreated();

        $rental = Rental::first();
        $this->assertEquals('cloudinary-private://rentals/docs/mock_public_id', $rental->ktp_path);
        $this->assertEquals('cloudinary-private://rentals/docs/mock_public_id', $rental->selfie_path);
    }

    public function test_document_route_redirects_admin_to_cloudinary_signed_url(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $car = Car::factory()->create();
        
        $rental = Rental::create([
            'user_id' => $admin->id,
            'car_id' => $car->id,
            'start_date' => now(),
            'end_date' => now()->addDays(2),
            'total_price' => 500000,
            'status' => \App\Enums\RentalStatus::PREPAID,
            'type' => \App\Enums\RentalType::SELF_DRIVE,
            'ktp_path' => 'cloudinary-private://rentals/ktp/mock_ktp_id',
            'selfie_path' => 'cloudinary-private://rentals/selfie/mock_selfie_id',
        ]);

        // Mock CloudinaryMediaService
        $cloudinaryMock = Mockery::mock(CloudinaryMediaService::class);
        $cloudinaryMock->shouldReceive('isCloudinaryPath')->with('cloudinary-private://rentals/ktp/mock_ktp_id')->andReturn(true);
        $cloudinaryMock->shouldReceive('url')->with('cloudinary-private://rentals/ktp/mock_ktp_id')->andReturn('https://res.cloudinary.com/dmtfegojx/image/authenticated/s--mocksignature--/rentals/ktp/mock_ktp_id');

        $this->app->instance(CloudinaryMediaService::class, $cloudinaryMock);

        $response = $this->actingAs($admin)->get("/dashboard/rentals/{$rental->id}/document/ktp");

        $response->assertRedirect('https://res.cloudinary.com/dmtfegojx/image/authenticated/s--mocksignature--/rentals/ktp/mock_ktp_id');
    }
}
