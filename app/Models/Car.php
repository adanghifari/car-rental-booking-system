<?php

namespace App\Models;

use App\Enums\CarStatus;
use App\Enums\VehicleType;
use App\Enums\TransmissionType;
use App\Services\CloudinaryMediaService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

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

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function scopeWithReviewMetrics(Builder $query): Builder
    {
        return $query->withAvg('reviews', 'rating')
            ->withCount('reviews');
    }

    public function getAverageRatingAttribute(): float
    {
        $loadedAverage = $this->getAttribute('reviews_avg_rating');

        if (! is_null($loadedAverage)) {
            return round((float) $loadedAverage, 1);
        }

        if (! is_null($this->rating)) {
            return round((float) $this->rating, 1);
        }

        return round($this->reviews()->avg('rating') ?? 0.0, 1);
    }

    public function getHasRatingAttribute(): bool
    {
        return $this->average_rating > 0;
    }

    public function getRatingDisplayAttribute(): string
    {
        return $this->has_rating
            ? number_format($this->average_rating, 1)
            : 'Belum ada Rating';
    }

    public function getTotalReviewsAttribute(): int
    {
        $loadedCount = $this->getAttribute('reviews_count');

        if (! is_null($loadedCount)) {
            return (int) $loadedCount;
        }

        return $this->reviews()->count();
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->resolveMediaUrl($this->image);
    }

    public function getGalleryImageUrlsAttribute(): array
    {
        return collect($this->gallery_images ?? [])
            ->map(fn ($path) => is_string($path) ? $this->resolveMediaUrl($path) : null)
            ->filter()
            ->values()
            ->all();
    }

    private function resolveMediaUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        $cloudinaryUrl = app(CloudinaryMediaService::class)->url($path);
        if ($cloudinaryUrl) {
            return $cloudinaryUrl;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/')) {
            return $path;
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::url($path);
        }

        return asset($path);
    }
}
