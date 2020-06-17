<?php

namespace App\Eloquents;

use Carbon\CarbonImmutable;

class ShiftFixedDate extends BaseModel
{
    protected $guarded = ["id", "created_at", "updated_at", "version"];
    // protected $dates = ['date_fixed'];

    ////
    // scope Query
    public function scopeAfter($query, CarbonImmutable $date)
    {
        return $query->where('date_fixed', '>=', $date);
    }

    public function scopeMonth($query, CarbonImmutable $date)
    {
        return $query->whereBetween('date_fixed', [$date->startOfMOnth()->startOfDay(), $date->endOfMonth()->endOfDay()]);
    }
}
