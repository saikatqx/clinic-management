<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class DiagnosticCategory extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    public const IMAGE = 'image';

    protected $table = 'diagnostic_categories';

    protected $appends = ['image_url'];

    public $fillable = [
        'name',
        'type',
        'description',
        'status',
    ];

    protected $casts = [
        'name' => 'string',
        'type' => 'string',
        'description' => 'string',
        'status' => 'boolean',
    ];

    public static $rules = [
        'name' => 'required|string|max:255|unique:diagnostic_categories,name',
        'type' => 'required|in:diag,path',
        'description' => 'nullable|string',
        'status' => 'nullable|boolean',
        'image' => 'nullable|mimes:jpg,jpeg,png,svg,webp',
    ];

    public function diagnostics(): HasMany
    {
        return $this->hasMany(Diagnostic::class);
    }

    public function getImageUrlAttribute(): string
    {
        $media = $this->getFirstMedia(self::IMAGE);

        return $media ? $media->getFullUrl() : '';
    }
}
