<?php

use App\Enums\GameStatus;
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
        Schema::create('games', function (Blueprint $table) {
            $table->uuid('id')->index()->primary();
            $table->string('title')->index();
            $table->string('slug')->index();
            $table->text('description')->index()->nullable();
            $table->enum('status', GameStatus::getStatuses())->index()->default(GameStatus::UPCOMING);
            $table->string('access_password')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->foreignUuid('show_id')->constrained('shows');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
