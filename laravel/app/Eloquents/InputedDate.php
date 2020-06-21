<?php

namespace App\Eloquents;

use Carbon\CarbonImmutable;

class InputedDate extends BaseModel
{
    protected $guarded = ["id", "created_at", "updated_at", "version"];
    protected $hidden = ['version'];

    public function scopeRange($query, CarbonImmutable $from, CarbonImmutable $to)
    {
        return $query->whereBetween(
            'date_target', [$from->toDateString(), $to->toDateString()]
        );
    }
}
