<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chess_games', function (Blueprint $table) {
            $table->id();
            $table->uuid('token')->unique();
            $table->foreignId('white_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('black_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('invited_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('status', 20)->default('pending');
            $table->json('state')->nullable();
            $table->unsignedInteger('version')->default(0);
            $table->boolean('game_over')->default(false);
            $table->string('winner', 10)->nullable();
            $table->string('game_over_title')->nullable();
            $table->string('game_over_msg')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();

            $table->index(['white_user_id', 'status']);
            $table->index(['black_user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chess_games');
    }
};
