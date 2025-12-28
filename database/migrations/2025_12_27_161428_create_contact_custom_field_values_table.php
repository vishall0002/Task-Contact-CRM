<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('contact_custom_field_values', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('contact_id');
            $table->unsignedBigInteger('custom_field_id');

            $table->text('field_value')->nullable();

            // Audit fields
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->timestamps();
            $table->softDeletes(); // adds deleted_at

            // Foreign keys
            $table->foreign('contact_id')
                  ->references('id')
                  ->on('contacts')
                  ->cascadeOnDelete();

            $table->foreign('custom_field_id')
                  ->references('id')
                  ->on('custom_fields')
                  ->cascadeOnDelete();

            // Optional: link users table (recommended)
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('deleted_by')->references('id')->on('users')->nullOnDelete();

            // Prevent duplicate field for same contact
            $table->unique(['contact_id', 'custom_field_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_custom_field_values');
    }
};

