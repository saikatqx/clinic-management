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
        Schema::create('health_package_booking_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('health_package_booking_id')->constrained('health_package_bookings')->onDelete('cascade');
            $table->foreignId('health_package_id')->constrained('health_packages')->onDelete('cascade');
            $table->string('package_name');
            $table->decimal('actual_price', 10, 2);
            $table->decimal('package_price', 10, 2);
            $table->text('tests_json')->nullable(); // holds array of tests
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('health_package_booking_items');
    }
};
