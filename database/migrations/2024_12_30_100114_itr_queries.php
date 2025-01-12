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
        Schema::create('itr_queries', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key
            $table->string('user_id');
            $table->string('income_type'); // Income type as a string
            $table->boolean('resident'); // Resident status (true/false)
            $table->boolean('business_income')->nullable(); // Business income as decimal
            $table->boolean('profit_loss')->nullable(); // Profit or loss as decimal
            $table->boolean('income_tax_forms')->nullable(); // Tax forms as string
            $table->boolean('services')->nullable(); // Services as string
            $table->unsignedBigInteger('coupon_id')->nullable(); // Coupon ID as a foreign key
            $table->decimal('amount', 15, 2)->nullable(); // Amount as decimal
            $table->timestamps(); // Created at and updated at timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itr_queries');
    }
};
