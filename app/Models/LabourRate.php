<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LabourRate extends Model
{
    protected $fillable = [
        'labour_detail_id',
        'rate_unit',
        'amount',
        'is_active',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function labourDetail(): BelongsTo
    {
        return $this->belongsTo(LabourDetail::class);
    }
}
