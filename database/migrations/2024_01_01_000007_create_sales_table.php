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
        Schema::create('sales', function (Blueprint $table) {
            $table->id('sale_id');
            $table->foreignId('booking_id')->constrained('bookings', 'booking_id');
            $table->decimal('room_revenue', 10, 2);
            $table->integer('nights_sold');
            $table->date('sale_date');
            $table->timestamps();
            $table->unique('booking_id');
            $table->index('sale_date');
            $table->index(['sale_date', 'room_revenue']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};