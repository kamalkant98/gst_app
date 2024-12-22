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
        Schema::create('tds_tcs_queries', function (Blueprint $table) {
            $table->id();
            $table->string('tan_number')->nullable();
            $table->string('no_of_employees')->nullable();
            $table->string('no_of_entries')->nullable();
            $table->string('tax_planning_of_employees')->nullable();
            $table->integer('user_id'); // Store file paths as JSON
            $table->enum('status', ['pending', 'done'])->default('pending');
            $table->string('coupon_id')->nullable();
            $table->decimal('total_amount', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tds_tcs_queries');
    }
};
