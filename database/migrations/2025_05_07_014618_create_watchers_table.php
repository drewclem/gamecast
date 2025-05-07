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
        Schema::create('watchers', function (Blueprint $table) {
            $table->uuid('id')->index()->primary();
            $table->foreignUuid('game_id')->constrained('games');
            $table->string('email')->index();
            $table->string('nickname')->index();
            $table->timestamp('last_active_at')->nullable();
            $table->timestamps();

            $table->unique(['game_id', 'email']);
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
