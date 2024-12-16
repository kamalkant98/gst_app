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
        Schema::create('users_inquiry', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('name'); // Name of the user
            $table->string('email'); // Email of the user
            $table->string('mobile',15); // Phone number (optional)
            $table->string('form_type');
            $table->text('message')->nullable(); // Inquiry message
            $table->integer('otp')->nullable();
            $table->timestamp("otp_expires_at");
            $table->boolean('is_verified')->default(0);
            $table->timestamps(); // Created at and Updated at columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_inquiry');
    }
};
