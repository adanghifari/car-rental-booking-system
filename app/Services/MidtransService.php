<?php

namespace App\Services;

use App\Models\Rental;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class MidtransService
{
    public function createTransaction(Rental $rental, string $orderId): array
    {
        $serverKey = (string) config('services.midtrans.server_key');
        $isProduction = (bool) config('services.midtrans.is_production', false);

        if ($serverKey === '') {
            if (config('app.env') === 'local') {
                return [
                    'token' => 'mock-snap-token-' . rand(1000, 9999),
                    'redirect_url' => route('booking.simulate-payment', ['rental_id' => $rental->id]),
                ];
            }
            throw new RuntimeException('Midtrans server key is not configured.');
        }

        $endpoint = $isProduction
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

        $expiryTime = $rental->prepaid_expires_at ?? ($rental->verified_at ? $rental->verified_at->addHours(3) : now()->addHours(3));
        $durationInMinutes = max(1, (int) now()->diffInMinutes($expiryTime));

        $payload = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $rental->total_price,
            ],
            'customer_details' => [
                'first_name' => $rental->user->name ?? 'Customer',
                'email' => $rental->user->email ?? null,
            ],
            'item_details' => [
                [
                    'id' => 'rental-'.$rental->id,
                    'price' => $rental->total_price,
                    'quantity' => 1,
                    'name' => trim(($rental->car->brand ?? '').' '.($rental->car->name ?? '')),
                ],
            ],
            'callbacks' => [
                'finish' => route('booking.detail', ['rental' => $rental->id]),
            ],
            'expiry' => [
                'start_time' => now()->format('Y-m-d H:i:s O'),
                'unit' => 'minute',
                'duration' => $durationInMinutes,
            ],
        ];

        $response = Http::acceptJson()
            ->withBasicAuth($serverKey, '')
            ->post($endpoint, $payload);

        if (! $response->successful()) {
            throw new RuntimeException('Midtrans transaction failed.');
        }

        return $response->json();
    }

    public function getTransactionStatus(string $orderId): ?string
    {
        $serverKey = (string) config('services.midtrans.server_key');
        $isProduction = (bool) config('services.midtrans.is_production', false);

        if ($serverKey === '') {
            return null;
        }

        $endpoint = $isProduction
            ? "https://api.midtrans.com/v2/{$orderId}/status"
            : "https://api.sandbox.midtrans.com/v2/{$orderId}/status";

        $response = \Illuminate\Support\Facades\Http::acceptJson()
            ->withBasicAuth($serverKey, '')
            ->get($endpoint);

        if (! $response->successful()) {
            return null;
        }

        return $response->json('transaction_status');
    }

    public function getTransactionDetails(string $orderId): ?array
    {
        $serverKey = (string) config('services.midtrans.server_key');
        $isProduction = (bool) config('services.midtrans.is_production', false);

        if ($serverKey === '') {
            return null;
        }

        $endpoint = $isProduction
            ? "https://api.midtrans.com/v2/{$orderId}/status"
            : "https://api.sandbox.midtrans.com/v2/{$orderId}/status";

        $response = \Illuminate\Support\Facades\Http::acceptJson()
            ->withBasicAuth($serverKey, '')
            ->get($endpoint);

        if (! $response->successful()) {
            return null;
        }

        return $response->json();
    }

    public function verifySignature(array $payload): bool
    {
        $serverKey = (string) config('services.midtrans.server_key');

        if ($serverKey === '') {
            return false;
        }

        $orderId = (string) ($payload['order_id'] ?? '');
        $statusCode = (string) ($payload['status_code'] ?? '');
        $grossAmount = (string) ($payload['gross_amount'] ?? '');
        $signatureKey = (string) ($payload['signature_key'] ?? '');

        $expected = hash('sha512', $orderId.$statusCode.$grossAmount.$serverKey);

        return hash_equals($expected, $signatureKey);
    }
}
