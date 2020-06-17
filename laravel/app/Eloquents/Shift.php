<?php

namespace App\Eloquents;

use App\User;
use Carbon\CarbonImmutable;

class Shift extends BaseModel
{
    protected $guarded = ["id", "created_at", "updated_at", "version"];
    protected $hidden = ['version'];
    protected $dates = ['start_datetime', 'end_datetime'];

    ////
    // Relation
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function work_type()
    {
        return $this->belongsTo(WorkType::class);
    }

    public function off_hour()
    {
        return $this->belongsTo(OffHour::class);
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

    public function scopeMonth($query, int $groupId, int $userId, CarbonImmutable $carbon)
    {
        $start = $carbon->startOfMonth()->startOfDay()->hour(5);
        $end = $carbon->startOfMonth()->startOfDay()->addMOnth()->hour(5);
        return $query->where([
            ['group_id', '=', $groupId],
            ['user_id', '=', $userId],
            ['start_datetime', '>=', $start],
            ['start_datetime', '<', $end],
        ]);
    }
}
