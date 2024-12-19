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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();  // Auto-increment primary key
            $table->string('code')->unique();  // Unique coupon code
            $table->string('description')->nullable();
            $table->enum('type', ['flat', 'percentage']); // Coupon type: 'flat' or 'percentage'
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->decimal('value', 10, 2);  // Discount value (flat amount or percentage)
            $table->date('expires_at')->nullable();  // Optional expiry date
            $table->timestamps();  // Created at and updated at timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
