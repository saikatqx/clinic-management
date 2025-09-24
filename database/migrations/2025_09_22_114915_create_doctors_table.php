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
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();

            // Specialty relation
            $table->foreignId('specialty_id')
                ->constrained('specialties')
                ->onDelete('cascade'); // ✅ Correct spelling

            $table->string('name');                        // Doctor name
            $table->string('email')->unique();             // Unique email
            $table->string('phone', 15)->nullable();       // Optional phone number
            $table->text('qualification')->nullable();     // MBBS, MD, etc.
            $table->text('bio')->nullable();               // Short bio
            $table->string('profile_image')->nullable();   // Path to profile image
            $table->boolean('is_active')->default(1);      // Active/Inactive
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
