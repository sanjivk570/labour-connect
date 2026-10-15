<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LabourSkill extends Model
{
    protected $fillable = [
        'labour_detail_id',
        'skill_id',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function labourDetail(): BelongsTo
    {
        return $this->belongsTo(LabourDetail::class);
    }

    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }
}
