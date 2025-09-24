<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Doctor extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'specialty_id',
        'name',
        'email',
        'phone',
        'qualification',
        'bio',
        'profile_image',
        'is_active',
    ];

    /**
     * Relationship: Doctor belongs to a Specialty
     */
    public function specialty()
    {
        return $this->belongsTo(Specialty::class, 'specialty_id');
    }
}
