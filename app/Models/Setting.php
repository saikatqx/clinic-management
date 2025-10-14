<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'clinic_name',
        'email',
        'mobile',
        'address',
        'location_link',
        'top_notice',
        'favicon',
        'clinic_logo',
        'facebook',
        'instagram',
        'youtube',
        'linkedin',
    ];
}
