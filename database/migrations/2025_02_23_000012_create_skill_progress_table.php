<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skill_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('skill_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('total_questions')->default(0);
            $table->decimal('average_score', 5, 2);
            $table->foreignId('level_id')->constrained()->cascadeOnDelete();
            $table->timestamp('last_practiced_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'skill_id', 'level_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skill_progress');
    }
};
