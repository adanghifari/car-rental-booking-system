<?php

namespace App\Enums;

enum RentalStatus: string
{
    case PREPAID = 'prepaid';
    case ONGOING = 'ongoing';
    case RETURNED = 'returned';

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
