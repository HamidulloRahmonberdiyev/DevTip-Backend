<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('interview_sessions', function (Blueprint $table) {
            $table->foreignId('technology_id')->nullable()->after('level_id')->constrained()->nullOnDelete();
            $table->foreignId('lang_id')->nullable()->after('technology_id')->constrained('languages')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('interview_sessions', function (Blueprint $table) {
            $table->dropForeign(['technology_id']);
            $table->dropForeign(['lang_id']);
        });
    }
};
