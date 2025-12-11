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
        Schema::create('payments', function (Blueprint $table) {
            $table->id('payment_id');
            $table->foreignId('booking_id')->constrained('bookings', 'booking_id');
            $table->decimal('amount', 10, 2);
            $table->timestamp('payment_date')->useCurrent();
            $table->enum('payment_method', ['cash', 'credit_card',  'online']);
            $table->string('transaction_id', 100)->nullable();
            $table->enum('payment_status', ['pending', 'completed', 'refunded'])->default('pending');
            $table->string('sandbox_reference', 100)->nullable();
            $table->timestamps();
            $table->index('payment_method');
            $table->index('payment_status');
            $table->index('transaction_id');
            $table->index(['booking_id', 'payment_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};