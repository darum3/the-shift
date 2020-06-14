<?php

namespace App\Eloquents;

use Carbon\CarbonImmutable;

class ShiftFixedDate extends BaseModel
{
    protected $guarded = ["id", "created_at", "updated_at", "version"];

    ////
    // scope Query
    public function scopeAfter($query, CarbonImmutable $date)
    {
        return $query->where('date_fixed', '>=', $date);
    }
}
