<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_holds', function (Blueprint $table) {
            $table->id('hold_id');
            $table->foreignId('room_id')->constrained('rooms', 'room_id');
            $table->string('session_id');
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->timestamp('expires_at');
            $table->timestamps();
            
            $table->index(['room_id', 'expires_at']);
            $table->index('session_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_holds');
    }
};