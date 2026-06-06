<?php

namespace App\Http\Controllers;

use App\Enums\CarStatus;
use App\Enums\VehicleType;
use App\Enums\TransmissionType;
use App\Http\Responses\ApiResponse;
use App\Models\Car;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CarController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 10);
        $perPage = max(1, min(100, $perPage));

        $cars = Car::query()->latest()->paginate($perPage);

        return ApiResponse::pagination($cars, 'Cars retrieved.', 'cars');
    }

    public function show(Car $car): JsonResponse
    {
        return ApiResponse::success([
            'car' => $car,
        ], 'Car retrieved.');
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'brand' => ['required', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'transmission' => ['required', Rule::in(TransmissionType::values())],
            'seat_count' => ['required', 'integer', 'min:1'],
            'year' => ['sometimes', 'required', 'integer', 'min:1990', 'max:' . (int) now()->addYear()->year],
            'cc' => ['sometimes', 'required', 'integer', 'min:1', 'max:99999'],
            'vehicle_type' => ['required', Rule::in(VehicleType::values())],
            'color' => ['required', 'string', 'max:50'],
            'daily_rate' => ['required', 'integer', 'min:0'],
            'license_plate' => ['required', 'string', 'max:30', 'unique:cars,license_plate'],
            'status' => ['sometimes', 'required', Rule::in(CarStatus::values())],
            'image' => ['sometimes', 'required', 'string'],
            'rating' => ['sometimes', 'required', 'numeric', 'between:0,5'],
            'self_drive_available' => ['sometimes', 'required', 'boolean'],
            'driver_available' => ['sometimes', 'required', 'boolean'],
        ]);

        if ($validator->fails()) {
            return ApiResponse::validation($validator->errors()->toArray());
        }

        $car = Car::create($validator->validated());

        return ApiResponse::created([
            'car' => $car,
        ], 'Car created.');
    }

    public function update(Request $request, Car $car): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'brand' => ['sometimes', 'required', 'string', 'max:100'],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'required', 'string'],
            'transmission' => ['sometimes', 'required', Rule::in(TransmissionType::values())],
            'seat_count' => ['sometimes', 'required', 'integer', 'min:1'],
            'year' => ['sometimes', 'required', 'integer', 'min:1990', 'max:' . (int) now()->addYear()->year],
            'cc' => ['sometimes', 'required', 'integer', 'min:1', 'max:99999'],
            'vehicle_type' => ['sometimes', 'required', Rule::in(VehicleType::values())],
            'color' => ['sometimes', 'required', 'string', 'max:50'],
            'daily_rate' => ['sometimes', 'required', 'integer', 'min:0'],
            'license_plate' => ['sometimes', 'required', 'string', 'max:30', 'unique:cars,license_plate,' . $car->id],
            'status' => ['sometimes', 'required', Rule::in(CarStatus::values())],
            'image' => ['sometimes', 'required', 'string'],
            'rating' => ['sometimes', 'required', 'numeric', 'between:0,5'],
            'self_drive_available' => ['sometimes', 'required', 'boolean'],
            'driver_available' => ['sometimes', 'required', 'boolean'],
        ]);

        if ($validator->fails()) {
            return ApiResponse::validation($validator->errors()->toArray());
        }

        $car->fill($validator->validated());
        $car->save();

        return ApiResponse::success([
            'car' => $car,
        ], 'Car updated.');
    }

    public function destroy(Car $car): JsonResponse
    {
        $car->delete();

        return ApiResponse::success(null, 'Car deleted.');
    }
}
