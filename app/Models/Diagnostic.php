<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Diagnostic extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    public const IMAGE = 'image';

    protected $table = 'diagnostics';

    protected $appends = ['image_url'];

    public $fillable = [
        'diagnostic_category_id',
        'name',
        'price',
        'status',
    ];

    protected $casts = [
        'diagnostic_category_id' => 'integer',
        'name' => 'string',
        'price' => 'decimal:2',
        'status' => 'boolean',
    ];

    public static $rules = [
        'diagnostic_category_id' => 'required|exists:diagnostic_categories,id',
        'name' => 'required|string|max:255|unique:diagnostics,name',
        'price' => 'required|numeric|min:0',
        'status' => 'nullable|boolean',
        'image' => 'nullable|mimes:jpg,jpeg,png,svg,webp',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(DiagnosticCategory::class, 'diagnostic_category_id');
    }

    public function getImageUrlAttribute(): string
    {
        $media = $this->getFirstMedia(self::IMAGE);

        return $media ? $media->getFullUrl() : '';
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
