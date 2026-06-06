<?php

namespace App\Enums;

enum VehicleType: string
{
    case CITY_CAR = 'city_car';
    case LCGC = 'lcgc';
    case HATCHBACK = 'hatchback';
    case SEDAN = 'sedan';
    case MPV = 'mpv';
    case SUV = 'suv';
    case PICKUP = 'pickup';
    case LUXURY = 'luxury';
    case PARIWISATA = 'pariwisata';

    /**
     * Get all enum values.
     *
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $type) => $type->value, self::cases());
    }

    /**
     * Get human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::CITY_CAR => 'City Car',
            self::LCGC => 'LCGC',
            self::HATCHBACK => 'Hatchback',
            self::SEDAN => 'Sedan',
            self::MPV => 'MPV',
            self::SUV => 'SUV',
            self::PICKUP => 'Pickup',
            self::LUXURY => 'Luxury',
            self::PARIWISATA => 'Pariwisata',
        };
    }
}
