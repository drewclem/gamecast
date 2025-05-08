<?php

use App\Enums\QuestionStatus;
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
        Schema::create('questions', function (Blueprint $table) {
            $table->uuid('id')->index()->primary();
            $table->text('question')->index();
            $table->foreignUuid('game_id')->constrained('games');
            $table->enum('status', QuestionStatus::getStatuses())->index()
                ->default(QuestionStatus::PENDING);
            $table->boolean('is_active')->default(true);
            $table->foreignUuid('host_id')->constrained('hosts');
            $table->integer('order_index')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
