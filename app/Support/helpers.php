<?php

use App\Models\Car;
use App\Models\Rental;
use App\Support\BookingAvailability;

if (! function_exists('booking_active_rental_statuses')) {
    function booking_active_rental_statuses(): array
    {
        return BookingAvailability::activeRentalStatuses();
    }
}

if (! function_exists('booking_rental_has_overlap')) {
    function booking_rental_has_overlap(int $carId, string $startDate, string $endDate, ?int $ignoreRentalId = null): bool
    {
        $car = Car::query()->find($carId);

        if (! $car) {
            return false;
        }

        $result = BookingAvailability::checkCarAvailability($car, $startDate, $endDate, $ignoreRentalId);

        return ! $result['available'] && in_array($result['reason'], ['overlap', 'post_buffer'], true);
    }
}

if (! function_exists('booking_car_availability_result')) {
    function booking_car_availability_result(Car $car, string $startDate, string $endDate, ?int $ignoreRentalId = null): array
    {
        return BookingAvailability::checkCarAvailability($car, $startDate, $endDate, $ignoreRentalId);
    }
}

if (! function_exists('booking_car_is_listable_for_date')) {
    function booking_car_is_listable_for_date(Car $car, string $date): bool
    {
        return BookingAvailability::checkCarAvailability($car, $date, $date)['available'];
    }
}

if (! function_exists('booking_unavailability_message')) {
    function booking_unavailability_message(string $reason): string
    {
        return BookingAvailability::unavailabilityMessage($reason);
    }
}

if (! function_exists('booking_release_identity_files')) {
    function booking_release_identity_files(Rental $rental): void
    {
        if ($rental->ktp_path) {
            \Illuminate\Support\Facades\Storage::disk('local')->delete($rental->ktp_path);
        }

        if ($rental->selfie_path) {
            \Illuminate\Support\Facades\Storage::disk('local')->delete($rental->selfie_path);
        }

        $rental->ktp_path = '';
        $rental->selfie_path = '';
    }
}
