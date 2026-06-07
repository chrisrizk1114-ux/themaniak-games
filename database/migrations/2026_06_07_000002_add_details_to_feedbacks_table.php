<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feedbacks', function (Blueprint $table) {
            $table->string('category', 30)->nullable()->after('subject');
            $table->string('game', 80)->nullable()->after('category');
            $table->text('special_details')->nullable()->after('message');
            $table->json('player_info')->nullable()->after('special_details');
        });
    }

    public function down(): void
    {
        Schema::table('feedbacks', function (Blueprint $table) {
            $table->dropColumn(['category', 'game', 'special_details', 'player_info']);
        });
    }
};
