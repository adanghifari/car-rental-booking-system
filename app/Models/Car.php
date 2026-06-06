<?php

namespace App\Models;

use App\Enums\CarStatus;
use App\Enums\VehicleType;
use App\Enums\TransmissionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Car extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'brand',
        'name',
        'description',
        'transmission',
        'seat_count',
        'year',
        'cc',
        'vehicle_type',
        'color',
        'daily_rate',
        'license_plate',
        'status',
        'image',
        'gallery_images',
        'rating',
        'self_drive_available',
        'driver_available',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'seat_count' => 'integer',
        'year' => 'integer',
        'cc' => 'integer',
        'rating' => 'float',
        'gallery_images' => 'array',
        'status' => CarStatus::class,
        'vehicle_type' => VehicleType::class,
        'transmission' => TransmissionType::class,
        'self_drive_available' => 'boolean',
        'driver_available' => 'boolean',
    ];

    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class);
    }
}
