<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('chess_games', 'room_code')) {
            Schema::table('chess_games', function (Blueprint $table) {
                $table->string('room_code', 8)->nullable()->unique()->after('token');
            });
        }

        if (! Schema::hasTable('friend_messages')) {
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

        Schema::disableForeignKeyConstraints();

        try {
            Schema::table('chess_games', function (Blueprint $table) {
                $table->dropForeign(['black_user_id']);
            });
        } catch (\Throwable) {
            // Foreign key may already be dropped from a partial run.
        }

        DB::statement('ALTER TABLE chess_games MODIFY black_user_id BIGINT UNSIGNED NULL');

        try {
            Schema::table('chess_games', function (Blueprint $table) {
                $table->foreign('black_user_id')->references('id')->on('users')->cascadeOnDelete();
            });
        } catch (\Throwable) {
            // Foreign key may already exist.
        }

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::dropIfExists('friend_messages');

        if (Schema::hasColumn('chess_games', 'room_code')) {
            Schema::disableForeignKeyConstraints();

            try {
                Schema::table('chess_games', function (Blueprint $table) {
                    $table->dropForeign(['black_user_id']);
                });
            } catch (\Throwable) {
                // ignore
            }

            Schema::table('chess_games', function (Blueprint $table) {
                $table->dropColumn('room_code');
            });

            DB::statement('ALTER TABLE chess_games MODIFY black_user_id BIGINT UNSIGNED NOT NULL');

            try {
                Schema::table('chess_games', function (Blueprint $table) {
                    $table->foreign('black_user_id')->references('id')->on('users')->cascadeOnDelete();
                });
            } catch (\Throwable) {
                // ignore
            }

            Schema::enableForeignKeyConstraints();
        }
    }
};
