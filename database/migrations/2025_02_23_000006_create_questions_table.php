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
            $table->enum('type', ['text', 'coding', 'scenario']);
            $table->json('title')->nullable();      // {"uz": "...", "ru": "...", "en": "..."}
            $table->json('question');
            $table->json('answer')->nullable();
            $table->json('expected_keywords')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
