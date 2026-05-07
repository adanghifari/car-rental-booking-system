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

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_username_unique');
        }

        $dropColumns = [];

        if (Schema::hasColumn('users', 'username')) {
            $dropColumns[] = 'username';
        }

        if (Schema::hasColumn('users', 'phone')) {
            $dropColumns[] = 'phone';
        }

        if ($dropColumns !== []) {
            Schema::table('users', function (Blueprint $table) use ($dropColumns) {
                $table->dropColumn($dropColumns);
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'username')) {
                $table->string('username')->nullable()->unique();
            }

            if (! Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 15)->nullable();
            }
        });
    }
};
