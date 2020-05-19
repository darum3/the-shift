<?php

namespace App\Eloquents;

use Carbon\CarbonImmutable;

class ShiftFixedDate extends BaseModel
{
    ////
    // scope Query
    public function scopeAfter($query, CarbonImmutable $date)
    {
        return $query->where('date_fixed', '>=', $date);
    }
}
