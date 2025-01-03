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
        Schema::create('business_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('plan')->nullable();
            $table->text('documents')->nullable();
            $table->integer('user_id')->nullable();
            // $table->string('documents')->nullable(); // Store file paths as JSON
            $table->enum('status', ['pending', 'done'])->default('pending');
            $table->string('coupon_id')->nullable();
            $table->decimal('total_amount', 10, 2)->default('0');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_registrations');
    }
};
