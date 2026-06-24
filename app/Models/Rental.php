<?php

namespace App\Models;

use App\Enums\RentalStatus;
use App\Enums\RentalType;
use App\Enums\VerificationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Rental extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::created(function (self $rental): void {
            if (! blank($rental->booking_code)) {
                return;
            }

            $rental->forceFill([
                'booking_code' => self::makeBookingCode($rental),
            ])->saveQuietly();
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'booking_code',
        'user_id',
        'car_id',
        'start_date',
        'end_date',
        'total_price',
        'status',
        'type',
        'returned_at',
        'prepaid_expires_at',
        'ktp_path',
        'selfie_path',
        'verification_passed',
        'verified_at',
        'verification_status',
        'buffer_before_days',
        'buffer_after_days',
        'post_buffer_released_at',
        'post_buffer_released_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'returned_at' => 'datetime',
        'prepaid_expires_at' => 'datetime',
        'verified_at' => 'datetime',
        'post_buffer_released_at' => 'datetime',
        'verification_passed' => 'boolean',
        'total_price' => 'integer',
        'buffer_before_days' => 'integer',
        'buffer_after_days' => 'integer',
        'status' => RentalStatus::class,
        'type' => RentalType::class,
        'verification_status' => VerificationStatus::class,
    ];

    public static function makeBookingCode(self $rental): string
    {
        $date = ($rental->created_at ?? now())->format('Ymd');

        return sprintf('BOOK-%s-%04d', $date, $rental->id);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function paymentHistories(): HasMany
    {
        return $this->hasMany(PaymentHistory::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function postBufferReleasedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'post_buffer_released_by');
    }
}
