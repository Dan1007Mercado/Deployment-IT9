<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Remove foreign key constraint first
            $table->dropForeign(['room_id']);
            // Remove the redundant column
            $table->dropColumn('room_id');
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Add back the column for rollback
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
        });
    }
};