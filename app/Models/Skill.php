<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Skill extends Model
{
    protected $fillable = [
        'labour_category_id',
        'slug',
        'name_en',
        'name_hi',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getNameAttribute(): string
    {
        return app()->getLocale() === 'hi' ? $this->name_hi : $this->name_en;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(LabourCategory::class, 'labour_category_id');
    }
}
