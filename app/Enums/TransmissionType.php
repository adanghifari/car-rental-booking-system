<?php

namespace App\Enums;

enum TransmissionType: string
{
    case MANUAL = 'manual';
    case AUTOMATIC = 'automatic';

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
            self::MANUAL => 'Manual',
            self::AUTOMATIC => 'Automatic',
        };
    }
}
