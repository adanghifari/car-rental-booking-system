<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->renameColumn('type', 'vehicle_type');
            $table->renameColumn('seat', 'seat_count');
            $table->renameColumn('rental_fee', 'daily_rate');
        });

        // Normalize existing type and transmission values to lowercase to match the new string-backed Enums.
        // Also replaces spaces with underscores, e.g. "City Car" -> "city_car".
        DB::table('cars')->update([
            'vehicle_type' => DB::raw("LOWER(REPLACE(vehicle_type, ' ', '_'))"),
            'transmission' => DB::raw("CASE WHEN LOWER(transmission) = 'manual' THEN 'manual' ELSE 'automatic' END"),
        ]);

        Schema::table('cars', function (Blueprint $table) {
            $table->boolean('self_drive_available')->default(false)->after('status');
            $table->boolean('driver_available')->default(false)->after('self_drive_available');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn(['self_drive_available', 'driver_available']);
            $table->renameColumn('vehicle_type', 'type');
            $table->renameColumn('seat_count', 'seat');
            $table->renameColumn('daily_rate', 'rental_fee');
        });
    }
};
