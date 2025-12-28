<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();

            $table->enum('gender', ['male', 'female', 'other'])->nullable();

            $table->string('profile_image')->nullable();
            $table->string('additional_file')->nullable();

            // Merge related fields (Practical 2)
            $table->boolean('is_merged')->default(false);
            $table->unsignedBigInteger('merged_into')->nullable();

             // Audit fields
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->timestamps();
            $table->softDeletes(); // adds deleted_at

            $table->foreign('merged_into')
                  ->references('id')
                  ->on('contacts')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};

