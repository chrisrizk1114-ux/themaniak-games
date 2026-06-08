<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chess_game_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chess_game_id')->constrained('chess_games')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('body', 500);
            $table->timestamp('created_at')->useCurrent();

            $table->index(['chess_game_id', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chess_game_messages');
    }
};
