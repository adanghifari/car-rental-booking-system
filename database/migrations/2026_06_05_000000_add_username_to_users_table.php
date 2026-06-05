<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        if (! Schema::hasColumn('users', 'username')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('username', 50)->nullable()->after('name');
            });
        }

        DB::table('users')
            ->select(['id', 'name'])
            ->whereNull('username')
            ->orderBy('id')
            ->chunkById(100, function ($users): void {
                foreach ($users as $user) {
                    $baseUsername = Str::of((string) $user->name)
                        ->lower()
                        ->ascii()
                        ->replaceMatches('/[^a-z0-9]+/', '.')
                        ->trim('.')
                        ->value();

                    $baseUsername = $baseUsername !== '' ? $baseUsername : 'user';
                    $candidate = $baseUsername;
                    $suffix = 1;

                    while (DB::table('users')
                        ->where('username', $candidate)
                        ->where('id', '!=', $user->id)
                        ->exists()) {
                        $candidate = $baseUsername.'.'.$suffix;
                        $suffix++;
                    }

                    DB::table('users')
                        ->where('id', $user->id)
                        ->update(['username' => $candidate]);
                }
            }, 'id');

        Schema::table('users', function (Blueprint $table) {
            $table->unique('username');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('users') || ! Schema::hasColumn('users', 'username')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropColumn('username');
        });
    }
};
