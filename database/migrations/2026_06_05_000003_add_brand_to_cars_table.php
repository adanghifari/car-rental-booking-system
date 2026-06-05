<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->string('brand', 100)->nullable()->after('name');
        });

        DB::table('cars')->orderBy('id')->chunkById(100, function ($cars) {
            foreach ($cars as $car) {
                $parts = preg_split('/\s+/', trim((string) $car->name), 2) ?: [];
                $brand = $parts[0] ?? null;
                $name = $parts[1] ?? $car->name;

                DB::table('cars')
                    ->where('id', $car->id)
                    ->update([
                        'brand' => $brand,
                        'name' => $name,
                    ]);
            }
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn('brand');
        });
    }
};
