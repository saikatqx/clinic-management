<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DiagnosticCategory extends Model
{
    use HasFactory;

    protected $table = 'diagnostic_categories';

    protected $appends = ['image_url'];

    public $fillable = [
        'name',
        'type',
        'description',
        'status',
        'image',
    ];

    protected $casts = [
        'name' => 'string',
        'type' => 'string',
        'description' => 'string',
        'status' => 'boolean',
        'image' => 'string',
    ];

    public static $rules = [
        'name' => 'required|string|max:255|unique:diagnostic_categories,name',
        'type' => 'required|in:diag,path',
        'description' => 'nullable|string',
        'status' => 'nullable|boolean',
        'image' => 'nullable|image|mimes:jpg,jpeg,png,svg,webp|max:1024',
    ];

    public function diagnostics(): HasMany
    {
        return $this->hasMany(Diagnostic::class);
    }

    public function getImageUrlAttribute(): string
    {
        return $this->image ? asset('images/diagnostic_categories/' . $this->image) : '';
    }
}
