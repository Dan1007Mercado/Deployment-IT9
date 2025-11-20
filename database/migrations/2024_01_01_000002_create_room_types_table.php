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
        Schema::create('room_types', function (Blueprint $table) {
            $table->id('room_type_id');
            $table->string('type_name', 100);
            $table->text('description')->nullable();
            $table->integer('capacity');
            $table->decimal('base_price', 10, 2);
            $table->text('amenities')->nullable();
            $table->integer('total_rooms')->default(0);
            $table->timestamps();
            $table->unique('type_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_types');
    }
};