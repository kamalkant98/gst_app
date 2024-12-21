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
            $table->integer('user_id')->after('documents'); // Store file paths as JSON
            $table->enum('status', ['pending', 'done'])->default('pending')->after('documents');
            $table->string('coupon_id')->nullable()->after('documents');
            $table->decimal('total_amount', 10, 2)->after('documents');
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
