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
        Schema::create('diagnostic_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_no')->unique();
            $table->string('type')->nullable(); // e.g. diag or path
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('patient_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('assign_doctor_id')->nullable()->constrained('doctors')->onDelete('set null');
            $table->string('patient_name');
            $table->string('referred_doctor_type')->nullable();
            $table->foreignId('referred_doctor_id')->nullable()->constrained('doctors')->onDelete('set null');
            $table->string('referred_doctor_name')->nullable();
            $table->string('mobile');
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->date('booking_date');
            $table->string('booking_time');
            $table->string('collection_type'); // home or clinic
            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->string('payment_status')->default('pending');
            $table->string('payment_method')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('booking_status')->default('pending');
            $table->text('remarks')->nullable();
            $table->string('payment_id')->nullable();
            $table->string('report_pdf_path')->nullable();
            $table->foreignId('report_uploaded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('report_uploaded_at')->nullable();
            $table->boolean('is_rescheduled')->default(false);
            $table->timestamp('rescheduled_at')->nullable();
            $table->foreignId('rescheduled_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diagnostic_bookings');
    }
};
