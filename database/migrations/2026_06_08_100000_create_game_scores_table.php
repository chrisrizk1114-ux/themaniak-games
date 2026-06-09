<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('game', 64);
            $table->unsignedInteger('score')->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'game']);
            $table->index(['game', 'score']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_scores');
    }
};
