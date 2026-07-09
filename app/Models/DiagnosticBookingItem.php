<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiagnosticBookingItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'diagnostic_booking_id',
        'diagnostic_id',
        'test_name',
        'price',
        'qty',
        'amount',
    ];

    public function booking()
    {
        return $this->belongsTo(DiagnosticBooking::class, 'diagnostic_booking_id');
    }

    public function diagnostic()
    {
        return $this->belongsTo(Diagnostic::class);
    }
}
