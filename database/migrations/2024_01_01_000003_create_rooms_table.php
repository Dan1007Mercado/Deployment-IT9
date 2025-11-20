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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id('room_id');
            $table->foreignId('room_type_id')->constrained('room_types', 'room_type_id');
            $table->string('room_number', 10);
            $table->string('floor', 10);
            $table->enum('room_status', ['available', 'occupied', 'cleaning', 'maintenance'])->default('available');
            $table->timestamps();
            $table->unique('room_number');
            $table->index('room_status');
            $table->index(['room_type_id', 'room_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};