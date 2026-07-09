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
        Schema::create('diagnostic_booking_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diagnostic_booking_id')->constrained('diagnostic_bookings')->onDelete('cascade');
            $table->foreignId('diagnostic_id')->constrained('diagnostics')->onDelete('cascade');
            $table->string('test_name');
            $table->decimal('price', 10, 2);
            $table->integer('qty')->default(1);
            $table->decimal('amount', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diagnostic_booking_items');
    }
};
