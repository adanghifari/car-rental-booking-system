<?php

namespace App\Support;

use App\Enums\CarStatus;
use App\Enums\RentalStatus;
use App\Models\Car;
use App\Models\Rental;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection;

class BookingAvailability
{
    public const DEFAULT_BUFFER_BEFORE_DAYS = 2;
    public const DEFAULT_BUFFER_AFTER_DAYS = 1;

    /**
     * Active booking statuses that block date availability.
     *
     * @return array<int, RentalStatus>
     */
    public static function activeRentalStatuses(): array
    {
        return [
            RentalStatus::PENDING_VERIFICATION,
            RentalStatus::PREPAID,
            RentalStatus::ONGOING,
        ];
    }

    public static function isOperationallyAvailable(Car $car): bool
    {
        return $car->status === CarStatus::AVAILABLE;
    }

    public static function bufferBeforeDays(Rental $rental): int
    {
        return max(0, (int) ($rental->buffer_before_days ?? self::DEFAULT_BUFFER_BEFORE_DAYS));
    }

    public static function bufferAfterDays(Rental $rental): int
    {
        return max(0, (int) ($rental->buffer_after_days ?? self::DEFAULT_BUFFER_AFTER_DAYS));
    }

    public static function effectiveStartDate(Rental $rental): Carbon
    {
        return Carbon::parse($rental->start_date)->startOfDay()->subDays(self::bufferBeforeDays($rental));
    }

    public static function effectiveEndDate(Rental $rental): Carbon
    {
        return Carbon::parse($rental->end_date)->startOfDay()->addDays(self::bufferAfterDays($rental));
    }

    public static function returnedPostBufferEndDate(Rental $rental): Carbon
    {
        return Carbon::parse($rental->end_date)->startOfDay()->addDays(self::bufferAfterDays($rental));
    }

    public static function hasActivePostBuffer(Rental $rental, CarbonInterface|string|null $asOf = null): bool
    {
        if ($rental->status !== RentalStatus::RETURNED || $rental->post_buffer_released_at) {
            return false;
        }

        $moment = self::normalizeDate($asOf ?? now());

        return $moment->lte(self::returnedPostBufferEndDate($rental));
    }

    /**
     * @return array{available: bool, reason: null|string, rental: ?Rental}
     */
    public static function checkCarAvailability(
        Car $car,
        CarbonInterface|string $startDate,
        CarbonInterface|string $endDate,
        ?int $ignoreRentalId = null
    ): array {
        if (! self::isOperationallyAvailable($car)) {
            return [
                'available' => false,
                'reason' => 'operational_unavailable',
                'rental' => null,
            ];
        }

        $requestStart = self::normalizeDate($startDate);
        $requestEnd = self::normalizeDate($endDate);

        foreach (self::candidateRentals($car->id, $ignoreRentalId) as $rental) {
            if (in_array($rental->status, self::activeRentalStatuses(), true)
                && self::rangesOverlap(
                    $requestStart,
                    $requestEnd,
                    self::effectiveStartDate($rental),
                    self::effectiveEndDate($rental)
                )) {
                return [
                    'available' => false,
                    'reason' => 'overlap',
                    'rental' => $rental,
                ];
            }

            if (self::hasActivePostBuffer($rental)
                && self::rangesOverlap(
                    $requestStart,
                    $requestEnd,
                    Carbon::parse($rental->end_date)->startOfDay(),
                    self::returnedPostBufferEndDate($rental)
                )) {
                return [
                    'available' => false,
                    'reason' => 'post_buffer',
                    'rental' => $rental,
                ];
            }
        }

        return [
            'available' => true,
            'reason' => null,
            'rental' => null,
        ];
    }

    public static function rentalHasOverlap(
        int $carId,
        CarbonInterface|string $startDate,
        CarbonInterface|string $endDate,
        ?int $ignoreRentalId = null
    ): bool {
        $car = Car::query()->find($carId);
        if (! $car) {
            return false;
        }

        return ! self::checkCarAvailability($car, $startDate, $endDate, $ignoreRentalId)['available'];
    }

    public static function currentOngoingRental(Car $car, CarbonInterface|string|null $date = null): ?Rental
    {
        $day = self::normalizeDate($date ?? now());

        return Rental::query()
            ->where('car_id', $car->id)
            ->where('status', RentalStatus::ONGOING)
            ->whereDate('start_date', '<=', $day->toDateString())
            ->whereNull('returned_at')
            ->orderBy('start_date')
            ->first();
    }

    /**
     * @return array<int, Rental>
     */
    public static function impactedRentalsForOperationalHold(Car $car, CarbonInterface|string|null $fromDate = null): array
    {
        $from = self::normalizeDate($fromDate ?? now());

        return Rental::query()
            ->where('car_id', $car->id)
            ->whereIn('status', self::activeRentalStatuses())
            ->whereDate('end_date', '>=', $from->toDateString())
            ->orderBy('start_date')
            ->get()
            ->all();
    }

    public static function unavailabilityMessage(string $reason): string
    {
        return match ($reason) {
            'operational_unavailable' => 'Mobil sedang tidak tersedia secara operasional.',
            'post_buffer' => 'Mobil masih dalam masa buffer setelah rental sebelumnya.',
            default => 'Mobil tidak tersedia pada tanggal yang dipilih karena sudah memiliki reservasi.',
        };
    }

    private static function normalizeDate(CarbonInterface|string $date): Carbon
    {
        return $date instanceof CarbonInterface
            ? Carbon::instance($date)->startOfDay()
            : Carbon::parse($date)->startOfDay();
    }

    /**
     * @return Collection<int, Rental>
     */
    private static function candidateRentals(int $carId, ?int $ignoreRentalId = null): Collection
    {
        return Rental::query()
            ->where('car_id', $carId)
            ->when($ignoreRentalId, fn ($query) => $query->where('id', '!=', $ignoreRentalId))
            ->where(function ($query) {
                $query->whereIn('status', self::activeRentalStatuses())
                    ->orWhere('status', RentalStatus::RETURNED);
            })
            ->orderBy('start_date')
            ->get();
    }

    private static function rangesOverlap(
        CarbonInterface $firstStart,
        CarbonInterface $firstEnd,
        CarbonInterface $secondStart,
        CarbonInterface $secondEnd
    ): bool {
        return $firstStart->lte($secondEnd) && $firstEnd->gte($secondStart);
    }
}
