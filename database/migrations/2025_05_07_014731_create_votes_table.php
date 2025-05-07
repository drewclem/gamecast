<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->uuid('id')->index()->primary();
            $table->foreignUuid('game_id')->constrained('games');
            $table->foreignUuid('watcher_id')->constrained('watchers');
            $table->foreignUuid('question_id')->constrained('questions');
            $table->tinyInteger('choice')->index();
            $table->timestamps();

            $table->unique(['game_id', 'watcher_id', 'question_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};
