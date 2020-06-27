<?php

namespace App\Eloquents;

use Carbon\CarbonImmutable;

class DesiredShift extends BaseModel
{
    protected $guarded = ["id", "created_at", "updated_at", "version"];
    protected $hidden = ['version', "created_at", "updated_at", "sysinfo"];

    // protected $dates = ['time_start', 'time_end'];

    public function scopeRange($query, CarbonImmutable $from, CarbonImmutable $to)
    {
        return $query->whereBetween(
            'date_target', [$from->toDateString(), $to->toDateString()]
        );
    }
}
