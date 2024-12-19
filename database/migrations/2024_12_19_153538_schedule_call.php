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
        Schema::create('schedule_call', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('user_id'); // Name of the user
            $table->string('query_type'); //.comment("1=Income Tax Returns, 2=TDS Returns, 3=GST Returns, 4=Business Registration And Licenses, 5=NRI Taxation, 6=Consultancy Services, 7=Other Query"); // Email of the user
            $table->string('plan'); //.comment("1= 10min,2=20min,3=30min");
            $table->datetime('call_datetime'); // Phone number (optional)
            $table->string('language'); //.comment("1=Hindi,2=English");
            $table->enum('status', ['pending', 'done'])->default('pending');
            $table->text('message')->nullable(); // Inquiry message
            $table->string('coupon_id');
            $table->decimal('total_amount', 10, 2);
            $table->timestamps(); // Created at and Updated at columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_call');
    }
};
