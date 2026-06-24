<?php

namespace Database\Factories;

use App\Enums\CarStatus;
use App\Enums\TransmissionType;
use App\Enums\VehicleType;
use App\Models\Car;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Car>
 */
class CarFactory extends Factory
{
    protected $model = Car::class;

    public function definition(): array
    {
        return [
            'brand'                => $this->faker->randomElement(['Toyota', 'Honda', 'Suzuki', 'Daihatsu', 'Mitsubishi', 'Nissan']),
            'name'                 => $this->faker->randomElement(['Avanza', 'Brio', 'Ertiga', 'Sigra', 'Xpander', 'Livina', 'Rush', 'Fortuner']),
            'description'          => $this->faker->sentence(10),
            'transmission'         => $this->faker->randomElement(TransmissionType::values()),
            'seat_count'           => $this->faker->randomElement([4, 5, 7, 8]),
            'year'                 => $this->faker->numberBetween(2015, 2024),
            'cc'                   => $this->faker->randomElement([1000, 1200, 1500, 1800, 2000, 2400]),
            'vehicle_type'         => $this->faker->randomElement(VehicleType::values()),
            'color'                => $this->faker->safeColorName(),
            'daily_rate'           => $this->faker->randomElement([200000, 250000, 300000, 350000, 400000, 500000]),
            'license_plate'        => strtoupper($this->faker->lexify('B #### ???')),
            'status'               => CarStatus::AVAILABLE->value,
            'image'                => null,
            'gallery_images'       => null,
            'rating'               => $this->faker->randomFloat(1, 3.0, 5.0),
            'self_drive_available' => true,
            'driver_available'     => false,
        ];
    }

    /**
     * State: mobil tersedia.
     */
    public function available(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CarStatus::AVAILABLE->value,
        ]);
    }

    /**
     * State: mobil sedang maintenance/tidak tersedia.
     */
    public function unavailable(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CarStatus::UNAVAILABLE->value,
        ]);
    }
}
