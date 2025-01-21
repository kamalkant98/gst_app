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
        Schema::create('documents', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key
            $table->unsignedBigInteger('query_id'); // Foreign key or related ID
            $table->string('file_url'); // URL or file path
            $table->enum('form_type', ['business_registration', 'itr_queries', 'tds_queries','gst_queries','talk_to_tax_expert','schedule_call'])->nullable(); // Form type as ENUM
            $table->timestamps(); // Created at and Updated at timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
