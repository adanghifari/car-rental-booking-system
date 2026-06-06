<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;

class UserLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'username',
        'activity',
        'device',
        'ip_address',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper to log an activity.
     */
    public static function log(?User $user, string $activity, string $status, Request $request, ?string $username = null): self
    {
        $userAgent = $request->userAgent() ?: '';
        $device = self::parseDevice($userAgent);

        return self::create([
            'user_id' => $user?->id,
            'username' => $username ?: $user?->username,
            'activity' => $activity,
            'device' => $device,
            'ip_address' => $request->ip(),
            'status' => $status,
        ]);
    }

    /**
     * Parse User Agent into realistic device name matching the UI design requirements.
     */
    public static function parseDevice(string $userAgent): string
    {
        $userAgentLower = strtolower($userAgent);

        if (str_contains($userAgentLower, 'iphone')) {
            return 'iPhone 15 Pro';
        }
        if (str_contains($userAgentLower, 'android')) {
            return 'Android 14';
        }
        if (str_contains($userAgentLower, 'macintosh') || str_contains($userAgentLower, 'mac os')) {
            return 'MacBook Air';
        }
        if (str_contains($userAgentLower, 'windows')) {
            return 'Windows 11';
        }
        if (str_contains($userAgentLower, 'ipad')) {
            return 'iPad Pro';
        }
        return 'Windows 11'; // default standard mock fallback
    }
}
