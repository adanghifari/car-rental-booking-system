<?php

use App\Enums\RentalStatus;
use App\Enums\RentalType;
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
        Schema::create('rentals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('car_id')->constrained()->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedBigInteger('total_price');
            $table->string('status', 20)->default(RentalStatus::PREPAID->value);
            $table->string('type', 30)->default(RentalType::SELF_DRIVE->value);
            $table->timestamp('returned_at')->nullable();
            $table->timestamp('prepaid_expires_at')->nullable();
            $table->string('ktp_path');
            $table->string('selfie_path');
            $table->boolean('verification_passed')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'prepaid_expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rentals');
    }
};
