<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Diagnostic extends Model
{
    use HasFactory;

    protected $table = 'diagnostics';

    protected $appends = ['image_url'];

    public $fillable = [
        'diagnostic_category_id',
        'name',
        'price',
        'status',
        'image',
    ];

    protected $casts = [
        'diagnostic_category_id' => 'integer',
        'name' => 'string',
        'price' => 'decimal:2',
        'status' => 'boolean',
        'image' => 'string',
    ];

    public static $rules = [
        'diagnostic_category_id' => 'required|exists:diagnostic_categories,id',
        'name' => 'required|string|max:255|unique:diagnostics,name',
        'price' => 'required|numeric|min:0',
        'status' => 'nullable|boolean',
        'image' => 'nullable|image|mimes:jpg,jpeg,png,svg,webp|max:1024',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(DiagnosticCategory::class, 'diagnostic_category_id');
    }

    public function getImageUrlAttribute(): string
    {
        return $this->image ? asset('images/diagnostics/' . $this->image) : '';
    }

    public function bookingItems()
    {
        return $this->hasMany(DiagnosticBookingItem::class);
    }

    public function healthPackages()
    {
        return $this->belongsToMany(
            \App\Models\HealthPackage::class,
            'health_package_tests',
            'diagnostic_id',
            'health_package_id'
        );
    }
}
