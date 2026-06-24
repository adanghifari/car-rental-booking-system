<?php

namespace App\Http\Controllers;

use App\Enums\CarStatus;
use App\Enums\RentalStatus;
use App\Enums\RentalType;
use App\Http\Responses\ApiResponse;
use App\Models\Car;
use App\Models\Rental;
use App\Services\CloudinaryMediaService;
use App\Services\FaceVerificationService;
use App\Support\BookingAvailability;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use RuntimeException;

class RentalController extends Controller
{
    public function store(Request $request, FaceVerificationService $faceVerification): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'car_id' => ['required', 'integer', 'exists:cars,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'type' => ['required', Rule::in(RentalType::values())],
            'ktp' => ['required', 'file', 'image', 'max:5120'],
            'selfie' => ['required', 'file', 'image', 'max:5120'],
        ]);

        if ($validator->fails()) {
            return ApiResponse::validation($validator->errors()->toArray());
        }

        $validated = $validator->validated();
        $user = $request->user();

        if (! $user) {
            return ApiResponse::unauthorized('Unauthenticated.');
        }

        try {
            $verification = $faceVerification->verify($request->file('ktp'), $request->file('selfie'));
        } catch (RuntimeException $exception) {
            return ApiResponse::error($exception->getMessage(), 503);
        }

        if (! $verification['verified']) {
            return ApiResponse::error('Face verification failed. Please upload again.', 422);
        }

        return DB::transaction(function () use ($validated, $user, $verification, $request) {
            $car = Car::query()->lockForUpdate()->find($validated['car_id']);

            if (! $car) {
                return ApiResponse::notFound('Car not found.');
            }

            if ($car->status !== CarStatus::AVAILABLE) {
                return ApiResponse::error(BookingAvailability::unavailabilityMessage('operational_unavailable'), 409);
            }

            $startDate = Carbon::parse($validated['start_date']);
            $endDate = Carbon::parse($validated['end_date']);
            $availability = BookingAvailability::checkCarAvailability($car, $startDate, $endDate);
            if (! $availability['available']) {
                return ApiResponse::error(BookingAvailability::unavailabilityMessage($availability['reason'] ?? 'overlap'), 409);
            }

            $days = max(1, $startDate->diffInDays($endDate));

            $cloudinary = app(CloudinaryMediaService::class);
            if ($cloudinary->configured()) {
                $ktpPath = $cloudinary->uploadPrivate($request->file('ktp'), 'rentals/ktp');
                $selfiePath = $cloudinary->uploadPrivate($request->file('selfie'), 'rentals/selfie');
            } else {
                $ktpPath = Storage::disk('local')->putFile('ktp', $request->file('ktp'));
                $selfiePath = Storage::disk('local')->putFile('selfie', $request->file('selfie'));
            }

            $rental = Rental::create([
                'user_id' => $user->id,
                'car_id' => $car->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'total_price' => $car->daily_rate * $days,
                'status' => RentalStatus::PREPAID,
                'type' => $validated['type'],
                'prepaid_expires_at' => now()->addDay(),
                'ktp_path' => $ktpPath,
                'selfie_path' => $selfiePath,
                'verification_passed' => true,
                'verified_at' => now(),
                'buffer_before_days' => BookingAvailability::DEFAULT_BUFFER_BEFORE_DAYS,
                'buffer_after_days' => BookingAvailability::DEFAULT_BUFFER_AFTER_DAYS,
            ]);

            return ApiResponse::created([
                'rental' => $rental,
                'verification' => $verification['payload'],
            ], 'Rental created.');
        });
    }

    public function markReturned(Rental $rental, Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return ApiResponse::unauthorized('Unauthenticated.');
        }

        if ($rental->user_id !== $user->id) {
            return ApiResponse::forbidden('Forbidden.');
        }

        if ($rental->status !== RentalStatus::ONGOING) {
            return ApiResponse::error('Rental is not ongoing.', 409);
        }

        return DB::transaction(function () use ($rental) {
            $rental->status = RentalStatus::RETURNED;
            $rental->returned_at = now();
            $rental->post_buffer_released_at = null;
            $rental->post_buffer_released_by = null;
            $rental->save();

            return ApiResponse::success([
                'rental' => $rental,
            ], 'Rental returned.');
        });
    }

    public function index(Request $request): JsonResponse
    {
        $query = Rental::query()->with(['user', 'car']);

        // Filter by status (prepaid, ongoing, returned)
        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        // Filter by tipe mobil (car type)
        if ($request->filled('tipe_mobil')) {
            $query->whereHas('car', function ($q) use ($request) {
                $q->where('vehicle_type', strtolower($request->query('tipe_mobil')));
            });
        } elseif ($request->filled('car_type')) {
            $query->whereHas('car', function ($q) use ($request) {
                $q->where('vehicle_type', strtolower($request->query('car_type')));
            });
        }

        // Filter by start date (tanggal mulai sewa)
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('start_date', $request->query('tanggal_mulai'));
        } elseif ($request->filled('start_date')) {
            $query->whereDate('start_date', $request->query('start_date'));
        }

        $perPage = (int) $request->query('per_page', 10);
        $perPage = max(1, min(100, $perPage));

        $rentals = $query->latest('id')->paginate($perPage);

        return ApiResponse::pagination($rentals, 'Rentals retrieved.', 'rentals');
    }

    public function count(): JsonResponse
    {
        $total = Rental::count();
        $prepaid = Rental::where('status', RentalStatus::PREPAID->value)->count();
        $ongoing = Rental::where('status', RentalStatus::ONGOING->value)->count();
        $returned = Rental::where('status', RentalStatus::RETURNED->value)->count();

        return ApiResponse::success([
            'total' => $total,
            'prepaid' => $prepaid,
            'ongoing' => $ongoing,
            'returned' => $returned,
        ], 'Rental counts retrieved.');
    }

    public function show(Rental $rental): JsonResponse
    {
        $rental->load(['user', 'car']);

        return ApiResponse::success([
            'rental' => $rental,
        ], 'Rental retrieved.');
    }

    public function update(Request $request, Rental $rental): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => ['sometimes', 'required', 'integer', 'exists:users,id'],
            'car_id' => ['sometimes', 'required', 'integer', 'exists:cars,id'],
            'start_date' => ['sometimes', 'required', 'date'],
            'end_date' => ['sometimes', 'required', 'date', 'after_or_equal:start_date'],
            'total_price' => ['sometimes', 'required', 'integer', 'min:0'],
            'status' => ['sometimes', 'required', Rule::in(RentalStatus::values())],
            'type' => ['sometimes', 'required', Rule::in(RentalType::values())],
            'returned_at' => ['sometimes', 'nullable', 'date'],
            'prepaid_expires_at' => ['sometimes', 'nullable', 'date'],
            'verification_passed' => ['sometimes', 'required', 'boolean'],
            'verified_at' => ['sometimes', 'nullable', 'date'],
        ]);

        if ($validator->fails()) {
            return ApiResponse::validation($validator->errors()->toArray());
        }

        $validated = $validator->validated();

        return DB::transaction(function () use ($validated, $rental) {
            $targetCarId = (int) ($validated['car_id'] ?? $rental->car_id);
            $car = Car::query()->lockForUpdate()->find($targetCarId);

            if (! $car) {
                return ApiResponse::notFound('Car not found.');
            }

            if ($car->status !== CarStatus::AVAILABLE) {
                return ApiResponse::error(BookingAvailability::unavailabilityMessage('operational_unavailable'), 409);
            }

            $rental->fill($validated);

            if (
                (!isset($validated['total_price'])) &&
                (isset($validated['start_date']) || isset($validated['end_date']) || isset($validated['car_id']))
            ) {
                if ($car) {
                    $startDate = Carbon::parse($rental->start_date);
                    $endDate = Carbon::parse($rental->end_date);
                    $days = max(1, $startDate->diffInDays($endDate));
                    $rental->total_price = $car->daily_rate * $days;
                }
            }

            $availability = BookingAvailability::checkCarAvailability(
                $car,
                Carbon::parse($rental->start_date),
                Carbon::parse($rental->end_date),
                $rental->id
            );
            if (! $availability['available']) {
                return ApiResponse::error(BookingAvailability::unavailabilityMessage($availability['reason'] ?? 'overlap'), 409);
            }

            if (isset($validated['status']) && $validated['status'] === RentalStatus::RETURNED->value && !$rental->returned_at) {
                $rental->returned_at = now();
                $rental->post_buffer_released_at = null;
                $rental->post_buffer_released_by = null;
            }

            $rental->save();

            return ApiResponse::success([
                'rental' => $rental->fresh(['user', 'car']),
            ], 'Rental updated.');
        });
    }

    public function destroy(Rental $rental): JsonResponse
    {
        return DB::transaction(function () use ($rental) {
            $rental->delete();

            return ApiResponse::success(null, 'Rental deleted.');
        });
    }
}
