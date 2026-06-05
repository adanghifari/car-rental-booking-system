<?php

namespace App\Models;

use App\Enums\CarStatus;
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
        'seat',
        'year',
        'cc',
        'type',
        'color',
        'rental_fee',
        'license_plate',
        'status',
        'image',
        'gallery_images',
        'rating',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'seat' => 'integer',
        'year' => 'integer',
        'cc' => 'integer',
        'rating' => 'float',
        'gallery_images' => 'array',
        'status' => CarStatus::class,
    ];

    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class);
    }
}
