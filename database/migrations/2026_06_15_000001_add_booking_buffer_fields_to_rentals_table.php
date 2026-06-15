<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->unsignedTinyInteger('buffer_before_days')->default(2)->after('verification_status');
            $table->unsignedTinyInteger('buffer_after_days')->default(1)->after('buffer_before_days');
            $table->timestamp('post_buffer_released_at')->nullable()->after('buffer_after_days');
            $table->foreignId('post_buffer_released_by')->nullable()->after('post_buffer_released_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->dropConstrainedForeignId('post_buffer_released_by');
            $table->dropColumn([
                'buffer_before_days',
                'buffer_after_days',
                'post_buffer_released_at',
            ]);
        });
    }
};
