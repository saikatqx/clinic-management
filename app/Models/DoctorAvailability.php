<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorAvailability extends Model
{
    protected $table = 'doctor_availabilities';

    protected $fillable = [
        'doctor_id', 'day_of_week', 'start_time', 'end_time', 'slot_minutes'
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
