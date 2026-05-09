<?php

namespace App\Enums;

enum CarStatus: string
{
    case AVAILABLE = 'available';
    case UNAVAILABLE = 'unavailable';

    /**
     * Get all enum values.
     *
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $status) => $status->value, self::cases());
    }
}