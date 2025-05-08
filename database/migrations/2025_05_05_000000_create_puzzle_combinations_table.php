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
        // Puzzle combinations table for complex puzzles
        Schema::create('puzzle_combinations', function (Blueprint $table) {
            $table->id();
            $table->string('combination_key')->unique();
            $table->string('required_items'); // Comma-separated item keys
            $table->string('target_object_key');
            $table->string('result_action'); // unlock, reveal, create, etc.
            $table->string('result_item_key')->nullable();
            $table->text('success_message');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('puzzle_combinations');
    }
};
