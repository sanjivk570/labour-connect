<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabourDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'labour_category_id',
        'full_name',
        'date_of_birth',
        'gender',
        'photo_path',
        'address_line',
        'city',
        'district',
        'state',
        'pincode',
        'latitude',
        'longitude',
        'max_travel_distance_km',
        'experience_years',
        'work_type',
        'bio',
        'registration_step',
        'is_available_today',
        'profile_completed_at',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'is_available_today' => 'boolean',
        'profile_completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(LabourCategory::class, 'labour_category_id');
    }

    public function skills(): HasMany
    {
        return $this->hasMany(LabourSkill::class);
    }

    public function rates(): HasMany
    {
        return $this->hasMany(LabourRate::class);
    }

    public function availabilities(): HasMany
    {
        return $this->hasMany(LabourAvailability::class);
    }
}
