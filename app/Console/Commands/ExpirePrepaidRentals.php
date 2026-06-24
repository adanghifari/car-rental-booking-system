<?php

namespace App\Console\Commands;

use App\Enums\RentalStatus;
use App\Models\Rental;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ExpirePrepaidRentals extends Command
{
    protected $signature = 'rentals:expire-prepaid';

    protected $description = 'Clean up expired prepaid rentals and expired verified rentals, mark payment as expired, and delete identity files.';

    public function handle(): int
    {
        $expired = Rental::query()
            ->where(function ($query) {
                // 1. PREPAID rentals past prepaid_expires_at
                $query->where(function ($q) {
                    $q->where('status', RentalStatus::PREPAID)
                      ->whereNotNull('prepaid_expires_at')
                      ->where('prepaid_expires_at', '<=', now());
                })
                // 2. Verified rentals that never paid/progressed and passed the 3-hour window from verified_at
                ->orWhere(function ($q) {
                    $q->where('status', RentalStatus::PENDING_VERIFICATION)
                      ->where('verification_status', \App\Enums\VerificationStatus::VERIFIED)
                      ->whereNotNull('verified_at')
                      ->where('verified_at', '<=', now()->subHours(3));
                })
                // 3. Stale booking holds that never received identity uploads
                ->orWhere(function ($q) {
                    $q->where('status', RentalStatus::PENDING_VERIFICATION)
                      ->where('verification_status', \App\Enums\VerificationStatus::PENDING)
                      ->where('ktp_path', '')
                      ->where('selfie_path', '')
                      ->where('created_at', '<=', now()->subHour());
                });
            })
            ->with(['car', 'paymentHistories'])
            ->get();

        if ($expired->isEmpty()) {
            $this->info('No expired rentals found.');
            return self::SUCCESS;
        }

        DB::transaction(function () use ($expired) {
            foreach ($expired as $rental) {
                if ($rental->ktp_path) {
                    Storage::disk('local')->delete($rental->ktp_path);
                }

                if ($rental->selfie_path) {
                    Storage::disk('local')->delete($rental->selfie_path);
                }

                $latestPayment = $rental->paymentHistories()->latest()->first();
                if ($latestPayment) {
                    $latestPayment->status = \App\Enums\PaymentStatus::EXPIRED;
                    $latestPayment->save();
                }

                $rental->status = RentalStatus::EXPIRED;
                $rental->verification_status = \App\Enums\VerificationStatus::CANCELLED;
                $rental->prepaid_expires_at = null;
                $rental->ktp_path = '';
                $rental->selfie_path = '';
                $rental->save();

                app(\App\Services\CustomerNotificationService::class)->notifyPaymentExpired($rental);
            }
        });

        $this->info('Expired rentals cleaned up successfully.');

        return self::SUCCESS;
    }
}
