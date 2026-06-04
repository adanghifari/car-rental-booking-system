<?php

namespace App\Console\Commands;

use App\Enums\CarStatus;
use App\Enums\RentalStatus;
use App\Models\Rental;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ExpirePrepaidRentals extends Command
{
    protected $signature = 'rentals:expire-prepaid';

    protected $description = 'Delete prepaid rentals that passed the 24-hour window.';

    public function handle(): int
    {
        $expired = Rental::query()
            ->where('status', RentalStatus::PREPAID)
            ->whereNotNull('prepaid_expires_at')
            ->where('prepaid_expires_at', '<=', now())
            ->with('car')
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

                if ($rental->car) {
                    $rental->car->status = CarStatus::AVAILABLE;
                    $rental->car->save();
                }

                $rental->delete();
            }
        });

        $this->info('Expired rentals cleaned up.');

        return self::SUCCESS;
    }
}
