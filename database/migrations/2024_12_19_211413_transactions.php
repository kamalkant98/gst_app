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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key
            $table->unsignedBigInteger('user_id'); // Reference to the user
            $table->unsignedBigInteger('order_id')->nullable(); // Optional reference to an order
            $table->string('form_type');
            $table->enum('payment_method', ['credit_card', 'debit_card', 'paypal', 'net_banking', 'wallet'])->nullable();
            $table->enum('transaction_type', ['credit', 'debit'])->nullable();
            $table->decimal('amount', 10, 2)->nullable(false); // Payment amount
            $table->string('currency', 10)->default('INR'); // Payment currency
            $table->string('coupon_code')->nullable(); // Coupon code (if applied)
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending'); // Transaction status
            $table->text('description')->nullable(); // Additional transaction description
            $table->string('transaction_reference')->unique()->nullable(); // Unique transaction reference
            $table->string('txnid')->unique(); // Unique transaction reference
            $table->text('hash')->nullable(); // Unique transaction reference
            $table->timestamps(); // Created at and Updated at
            $table->softDeletes(); // Deleted at for soft delete
            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users_inquiry')->onDelete('cascade');
            // $table->foreign('order_id')->references('id')->on('schedule_call')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
