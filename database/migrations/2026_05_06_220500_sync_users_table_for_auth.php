<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        if (! Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default('customer')->after('password');
            });
        }

        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            if (Schema::hasColumn('users', 'username')) {
                DB::statement('ALTER TABLE users ALTER COLUMN username DROP NOT NULL');
            }

            if (Schema::hasColumn('users', 'phone')) {
                DB::statement('ALTER TABLE users ALTER COLUMN phone DROP NOT NULL');
            }
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        if (Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
            });
        }
    }
};
