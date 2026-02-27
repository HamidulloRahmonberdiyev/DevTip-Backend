<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('google_id', 255)->unique()->nullable()->after('email');
            $table->string('avatar', 500)->nullable()->after('google_id');
            $table->foreignId('current_level_id')->nullable()->after('avatar')->constrained('levels')->nullOnDelete();
        });

        // Google login uchun password nullable (MySQL)
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE users MODIFY password VARCHAR(255) NULL');
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE users MODIFY password VARCHAR(255) NOT NULL');
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['current_level_id']);
            $table->dropColumn(['google_id', 'avatar', 'current_level_id']);
        });
    }
};
