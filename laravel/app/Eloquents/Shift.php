<?php

namespace App\Eloquents;

use Carbon\CarbonImmutable;

class Shift extends BaseModel
{
    protected $guarded = ["id", "created_at", "updated_at", "version"];
    protected $hidden = ['version'];

    ////
    // Relation
    public function work_type()
    {
        return $this->belongsTo(WorkType::class);
    }

    ////
    // Scope Query
    public function scopeDate($query, CarbonImmutable $date)
    {
        return $query->where([
            ['start_datetime' , '>=', $date->startOfDay()->hour(5)],
            ['start_datetime', '<', $date->startOfDay()->addDay()->hour(5)],
        ]);
    }

    public function scopeAfter($query, CarbonImmutable $date)
    {
        return $query->where('start_datetime', '>=', $date->startOfDay()->hour(5));
    }
}
