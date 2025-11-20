<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->enum('booking_source', ['walk-in', 'phone', 'online', 'agent'])->default('walk-in')->after('reservation_type');
            $table->text('special_requests')->nullable()->after('total_amount');
            $table->timestamp('expires_at')->nullable()->after('reservation_date');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn(['booking_source', 'special_requests', 'expires_at']);
        });
    }
};