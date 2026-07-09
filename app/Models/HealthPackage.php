<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HealthPackage extends Model
{
    public const GENDER_MALE = 'MALE';

    public const GENDER_FEMALE = 'FEMALE';

    public const GENDER_BOTH = 'BOTH';

    protected $fillable = [
        'name',
        'description',
        'actual_price',
        'package_price',
        'status',
        'gender',
        'image', // add this
    ];

    protected $casts = [
        'actual_price' => 'decimal:2',
        'package_price' => 'decimal:2',
        'status' => 'boolean',
        'gender' => 'string',
    ];

    public function diagnostics()
    {
        return $this->belongsToMany(
            Diagnostic::class,
            'health_package_tests',
            'health_package_id',
            'diagnostic_id'
        );
    }
}
