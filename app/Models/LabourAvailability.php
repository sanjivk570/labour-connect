<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LabourAvailability extends Model
{
    protected $fillable = [
        'labour_detail_id',
        'day_of_week',
        'is_available',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'is_available' => 'boolean',
    ];

    public function labourDetail(): BelongsTo
    {
        return $this->belongsTo(LabourDetail::class);
    }
}
