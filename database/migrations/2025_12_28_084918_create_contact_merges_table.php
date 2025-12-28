<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('contact_merges', function (Blueprint $table) {
            $table->id();

            // Master contact
            $table->foreignId('master_contact_id')
                  ->constrained('contacts')
                  ->cascadeOnDelete();

            // Secondary contact (original)
            $table->foreignId('secondary_contact_id')
                  ->constrained('contacts')
                  ->cascadeOnDelete();

            // Snapshot of secondary contact data
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('profile_image')->nullable();
            $table->string('additional_file')->nullable();

            $table->unsignedBigInteger('merged_by')->nullable();
            $table->timestamps();

            // Prevent duplicate merges
            $table->unique(['master_contact_id', 'secondary_contact_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_merges');
    }
};
