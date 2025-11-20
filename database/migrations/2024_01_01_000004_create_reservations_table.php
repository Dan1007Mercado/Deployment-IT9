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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id('reservation_id');
            $table->foreignId('guest_id')->constrained('guests', 'guest_id');
            $table->foreignId('room_type_id')->constrained('room_types', 'room_type_id');
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->integer('num_guests');
            $table->integer('num_nights')->virtualAs('DATEDIFF(check_out_date, check_in_date)');
            $table->decimal('total_amount', 10, 2);
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
            $table->timestamp('reservation_date')->useCurrent();
            $table->enum('reservation_type', ['walk-in', 'advance'])->default('walk-in');
            $table->timestamps();
            $table->index(['check_in_date', 'check_out_date']);
            $table->index('status');
            $table->index(['room_type_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};