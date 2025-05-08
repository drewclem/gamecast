<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('watchers', function (Blueprint $table) {
            $table->uuid('id')->index()->primary();
            $table->foreignUuid('game_id')->constrained('games');
            $table->string('gamertag')->index();
            $table->timestamp('last_active_at')->nullable();
            $table->timestamps();

            $table->unique(['game_id', 'gamertag']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('watchers');
    }
};
