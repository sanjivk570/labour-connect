<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LabourCategory extends Model
{
    protected $fillable = [
        'slug',
        'name_en',
        'name_hi',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Localised name based on the active app locale.
     */
    public function getNameAttribute(): string
    {
        return app()->getLocale() === 'hi' ? $this->name_hi : $this->name_en;
    }

    public function skills(): HasMany
    {
        return $this->hasMany(Skill::class);
    }
}
