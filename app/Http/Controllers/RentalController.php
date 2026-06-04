<?php

namespace App\Http\Controllers;

use App\Enums\CarStatus;
use App\Enums\RentalStatus;
use App\Enums\RentalType;
use App\Http\Responses\ApiResponse;
use App\Models\Car;
use App\Models\Rental;
use App\Services\FaceVerificationService;
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
                return ApiResponse::error('Car is not available.', 409);
            }

            $startDate = Carbon::parse($validated['start_date']);
            $endDate = Carbon::parse($validated['end_date']);
            $days = max(1, $startDate->diffInDays($endDate));

            $ktpPath = Storage::disk('local')->putFile('ktp', $request->file('ktp'));
            $selfiePath = Storage::disk('local')->putFile('selfie', $request->file('selfie'));

            $rental = Rental::create([
                'user_id' => $user->id,
                'car_id' => $car->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'total_price' => $car->rental_fee * $days,
                'status' => RentalStatus::PREPAID,
                'type' => $validated['type'],
                'prepaid_expires_at' => now()->addDay(),
                'ktp_path' => $ktpPath,
                'selfie_path' => $selfiePath,
                'verification_passed' => true,
                'verified_at' => now(),
            ]);

            $car->status = CarStatus::UNAVAILABLE;
            $car->save();

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
            $rental->save();

            $car = $rental->car;
            if ($car) {
                $car->status = CarStatus::AVAILABLE;
                $car->save();
            }

            return ApiResponse::success([
                'rental' => $rental,
            ], 'Rental returned.');
        });
    }
}
