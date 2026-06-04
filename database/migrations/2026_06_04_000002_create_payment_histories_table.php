<?php

use App\Enums\PaymentStatus;
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
        Schema::create('payment_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rental_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('amount');
            $table->string('status', 20)->default(PaymentStatus::PENDING->value);
            $table->string('provider', 50)->default('midtrans');
            $table->string('provider_order_id', 100)->unique();
            $table->string('snap_token', 100)->nullable();
            $table->text('redirect_url')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->index(['status', 'provider']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_histories');
    }
};
