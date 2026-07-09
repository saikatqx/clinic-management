<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class HealthPackageBooking extends Model
{
    protected $fillable = [
        'booking_no',
        'user_id',
        'patient_name',
        'referred_doctor_type',
        'referred_doctor_id',
        'referred_doctor_name',
        'mobile',
        'email',
        'address',
        'booking_date',
        'booking_time',
        'collection_type',
        'subtotal',
        'discount',
        'total_amount',
        'payment_status',
        'payment_method',
        'transaction_id',
        'booking_status',
        'remarks',
        'payment_id',
        'report_pdf_path',
        'report_uploaded_by',
        'report_uploaded_at',
        'is_rescheduled',
        'rescheduled_at',
        'rescheduled_by',
    ];

    protected $casts = [
        'is_rescheduled' => 'boolean',
        'rescheduled_at' => 'datetime',
        'report_uploaded_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(HealthPackageBookingItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function doctor(): HasOne
    {
        return $this->hasOne(Doctor::class, 'user_id');
    }

    public function rescheduledBy()
    {
        return $this->belongsTo(User::class, 'rescheduled_by');
    }

    public function reportUploadedBy()
    {
        return $this->belongsTo(User::class, 'report_uploaded_by');
    }

    public function referredDoctor()
    {
        return $this->belongsTo(Doctor::class, 'referred_doctor_id');
    }

    public function getBookingStatusLabelAttribute()
    {
        return match ($this->booking_status) {
            'pending' => 'Pending',
            'confirmed' => 'Confirmed',
            'sample_collected' => 'Sample Collected',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            default => ucfirst(str_replace('_', ' ', $this->booking_status)),
        };
    }

    public function getBookingStatusBadgeAttribute()
    {
        return match ($this->booking_status) {
            'pending' => 'warning',
            'confirmed' => 'primary',
            'sample_collected' => 'info',
            'completed' => 'success',
            'cancelled' => 'danger',
            default => 'secondary',
        };
    }

    public function getPaymentStatusLabelAttribute()
    {
        return match ($this->payment_status) {
            'paid' => 'Paid',
            'failed' => 'Failed',
            'pending' => 'Pending',
            default => ucfirst(str_replace('_', ' ', $this->payment_status)),
        };
    }

    public function getPaymentStatusBadgeAttribute()
    {
        return match ($this->payment_status) {
            'paid' => 'success',
            'failed' => 'danger',
            'pending' => 'warning',
            default => 'secondary',
        };
    }

    public function payment()
    {
        return $this->morphOne(
            Payment::class,
            'payable'
        );
    }
}
