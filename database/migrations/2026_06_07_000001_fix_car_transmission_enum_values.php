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
        // Convert any non-manual transmission values to automatic
        if (Schema::hasTable('cars')) {
            DB::table('cars')
                ->whereNotIn('transmission', ['manual', 'automatic'])
                ->update([
                    'transmission' => 'automatic'
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No action needed for rollback as automatic is a valid state
    }
};
