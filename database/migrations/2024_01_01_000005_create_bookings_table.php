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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id('booking_id');
            $table->foreignId('reservation_id')->constrained('reservations', 'reservation_id');
            $table->foreignId('room_id')->constrained('rooms', 'room_id');
            $table->timestamp('actual_check_in')->nullable();
            $table->timestamp('actual_check_out')->nullable();
            $table->enum('booking_status', ['reserved', 'checked-in', 'checked-out', 'cancelled', 'no-show','pending'])->default('reserved');
            $table->timestamp('booking_date')->useCurrent();
            $table->timestamps();
            $table->unique('reservation_id');
            $table->index('booking_status');
            $table->index(['room_id', 'booking_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};