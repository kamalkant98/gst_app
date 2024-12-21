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
        Schema::create('gst_queries', function (Blueprint $table) {
            $table->id();
            $table->string('gst_number')->nullable();
            $table->string('type_of_taxpayer')->nullable();
            $table->string('return_filling_frequency')->nullable();
            $table->string('type_of_return')->nullable();
            $table->string('service_type')->nullable();
            $table->integer('user_inquiry_id'); // Store file paths as JSON
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gst_queries');
    }
};