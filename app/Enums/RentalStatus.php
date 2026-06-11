<?php

namespace App\Enums;

enum RentalStatus: string
{
    case PENDING_VERIFICATION = 'pending_verification';
    case PREPAID = 'prepaid';
    case ONGOING = 'ongoing';
    case RETURNED = 'returned';
    case CANCELLED = 'cancelled';
    case EXPIRED = 'expired';

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
