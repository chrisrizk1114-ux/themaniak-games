<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chess_games', function (Blueprint $table) {
            $table->string('room_code', 8)->nullable()->unique()->after('token');
        });

        Schema::table('chess_games', function (Blueprint $table) {
            $table->dropForeign(['black_user_id']);
        });

        Schema::table('chess_games', function (Blueprint $table) {
            $table->unsignedBigInteger('black_user_id')->nullable()->change();
            $table->foreign('black_user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::create('friend_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('recipient_id')->constrained('users')->cascadeOnDelete();
            $table->string('body', 1000);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['sender_id', 'recipient_id', 'created_at']);
            $table->index(['recipient_id', 'read_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('friend_messages');

        Schema::table('chess_games', function (Blueprint $table) {
            $table->dropForeign(['black_user_id']);
            $table->dropColumn('room_code');
        });

        Schema::table('chess_games', function (Blueprint $table) {
            $table->unsignedBigInteger('black_user_id')->nullable(false)->change();
            $table->foreign('black_user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
