<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            // Add if not exists
            if (!Schema::hasColumn('payments', 'stripe_payment_url')) {
                $table->string('stripe_session_id')->nullable()->after('payment_method');
                $table->text('stripe_payment_url')->nullable()->after('stripe_session_id');
                
            }
        });
    }

    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('stripe_payment_url');
        });
    }
};