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
        Schema::create('health_package_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('health_package_id')->constrained('health_packages')->onDelete('cascade');
            $table->foreignId('diagnostic_id')->constrained('diagnostics')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('health_package_tests');
    }
};
