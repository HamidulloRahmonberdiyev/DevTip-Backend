<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('technology_id')->constrained();
            $table->foreignId('skill_id')->nullable()->constrained();
            $table->foreignId('lang_id')->constrained('languages')->cascadeOnDelete();
            $table->enum('type', ['text', 'coding', 'scenario']);
            $table->string('title')->nullable();
            $table->text('question');
            $table->text('answer')->nullable();
            $table->json('expected_keywords')->nullable();
            $table->decimal('rating', 3, 2)->unsigned()->default(0); // 0–5 yulduz, o'rtacha
            $table->unsignedInteger('rating_count')->default(0); // nechta baho berilgan
            $table->unsignedInteger('views')->default(0);        // ko'rilishlar soni
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
