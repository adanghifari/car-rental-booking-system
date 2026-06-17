<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('company_settings')) {
            return;
        }

        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->default('MD Car Rental');
            $table->text('address');
            $table->text('maps_directions_url')->nullable();
            $table->text('maps_embed_url')->nullable();
            $table->timestamps();
        });

        DB::table('company_settings')->insert([
            'company_name' => 'MD Car Rental',
            'address' => 'Jl. Gatot Subroto No.5, Ujung Pandang Baru, Kec. Tallo, Kota Makassar, Sulawesi Selatan 90212',
            'maps_directions_url' => 'https://www.google.com/maps/dir/?api=1&destination=Jl.+Gatot+Subroto+No.5,+Ujung+Pandang+Baru,+Kec.+Tallo,+Kota+Makassar,+Sulawesi+Selatan+90212',
            'maps_embed_url' => 'https://maps.google.com/maps?q=Jl.%20Gatot%20Subroto%20No.5%2C%20Ujung%20Pandang%20Baru%2C%20Kec.%20Tallo%2C%20Kota%20Makassar%2C%20Sulawesi%20Selatan%2090212&t=&z=16&ie=UTF8&iwloc=&output=embed',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('company_settings');
    }
};
