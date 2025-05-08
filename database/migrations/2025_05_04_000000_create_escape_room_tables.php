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
        // Rooms table
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('room_key')->unique(); // e.g. 'room1'
            $table->string('name');
            $table->text('description');
            $table->boolean('is_final')->default(false);
            $table->timestamps();
        });

        // Objects table
        Schema::create('objects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->string('object_key'); // e.g. 'kabinet2'
            $table->string('name');
            $table->text('description');
            $table->boolean('is_container')->default(false);
            $table->boolean('is_locked')->nullable();
            $table->string('required_item')->nullable();
            $table->string('code')->nullable(); // For safes
            $table->foreignId('parent_object_id')->nullable()->constrained('objects')->onDelete('cascade');
            $table->timestamps();

            // Composite unique key
            $table->unique(['room_id', 'object_key', 'parent_object_id']);
        });

        // Items table
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('object_id')->constrained()->onDelete('cascade');
            $table->string('item_key'); // e.g. 'sleutel'
            $table->string('name');
            $table->text('description');
            $table->boolean('takeable')->default(false);
            $table->text('content')->nullable(); // For readable items
            $table->timestamps();

            // Composite unique key
            $table->unique(['object_id', 'item_key']);
        });

        // Player sessions table
        Schema::create('player_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->unique();
            $table->foreignId('current_room_id')->constrained('rooms');
            $table->timestamp('start_time');
            $table->timestamp('end_time')->nullable();
            $table->timestamps();
        });

        // Player inventory table
        Schema::create('player_inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_session_id')->constrained()->onDelete('cascade');
            $table->string('item_key');
            $table->timestamps();

            // Composite unique key
            $table->unique(['player_session_id', 'item_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_inventory');
        Schema::dropIfExists('player_sessions');
        Schema::dropIfExists('items');
        Schema::dropIfExists('objects');
        Schema::dropIfExists('rooms');
    }
};