<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('company_settings') || Schema::hasColumn('company_settings', 'company_description')) {
            return;
        }

        Schema::table('company_settings', function (Blueprint $table) {
            $table->text('company_description')->nullable()->after('company_email');
        });

        DB::table('company_settings')
            ->whereNull('company_description')
            ->update([
                'company_description' => 'MD Car Rental adalah penyedia layanan sewa mobil terpercaya, aman, dan nyaman untuk berbagai kebutuhan perjalanan.',
            ]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('company_settings') || ! Schema::hasColumn('company_settings', 'company_description')) {
            return;
        }

        Schema::table('company_settings', function (Blueprint $table) {
            $table->dropColumn('company_description');
        });
    }
};
