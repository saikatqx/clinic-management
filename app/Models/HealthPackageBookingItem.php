<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HealthPackageBookingItem extends Model
{
    protected $fillable = [
        'health_package_booking_id',
        'health_package_id',
        'package_name',
        'actual_price',
        'package_price',
        'tests_json',
    ];

    protected $casts = [
        'tests_json' => 'array',
    ];

    public function booking()
    {
        return $this->belongsTo(HealthPackageBooking::class, 'health_package_booking_id');
    }

    public function package()
    {
        return $this->belongsTo(HealthPackage::class, 'health_package_id');
    }
}
