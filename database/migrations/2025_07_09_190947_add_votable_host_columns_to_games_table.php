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
        Schema::table('games', function (Blueprint $table) {
            $table->foreignUuid('votable_host_1_id')->nullable()->constrained('hosts')->nullOnDelete();
            $table->foreignUuid('votable_host_2_id')->nullable()->constrained('hosts')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropForeign(['votable_host_1_id']);
            $table->dropForeign(['votable_host_2_id']);
            $table->dropColumn('votable_host_1_id');
            $table->dropColumn('votable_host_2_id');
        });
    }
};
