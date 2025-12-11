<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('reports', function (Blueprint $table) {
            // Add new columns for database storage
            $table->longText('file_content')->nullable()->after('file_path');
            $table->integer('file_size')->nullable()->after('file_content');
            $table->string('mime_type')->default('application/pdf')->after('file_size');
            $table->unsignedBigInteger('generated_by')->nullable()->after('mime_type');
            
            // Add foreign key constraint if not exists
            $table->foreign('generated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('reports', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['generated_by']);
            
            // Drop columns
            $table->dropColumn(['file_content', 'file_size', 'mime_type', 'generated_by']);
        });
    }
};