<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_answer_id')->constrained('user_answers')->cascadeOnDelete();
            $table->decimal('score', 5, 2);
            $table->text('strengths')->nullable();
            $table->text('weaknesses')->nullable();
            $table->json('missing_keywords')->nullable();
            $table->text('ai_answer')->nullable();
            $table->decimal('confidence_score', 5, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_evaluations');
    }
};
