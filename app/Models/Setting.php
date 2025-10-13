<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'clinic_name',
        'favicon',
        'clinic_logo',
        'address',
        'mobile',
        'email',
        'location_link',
    ];
}
