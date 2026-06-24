<?php

use App\Models\Rental;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->string('booking_code', 30)->nullable()->after('id');
        });

        Rental::query()
            ->whereNull('booking_code')
            ->orderBy('id')
            ->chunkById(100, function ($rentals): void {
                foreach ($rentals as $rental) {
                    $rental->forceFill([
                        'booking_code' => Rental::makeBookingCode($rental),
                    ])->saveQuietly();
                }
            });

        Schema::table('rentals', function (Blueprint $table) {
            $table->unique('booking_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->dropUnique(['booking_code']);
            $table->dropColumn('booking_code');
        });
    }
};
