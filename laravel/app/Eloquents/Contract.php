<?php

namespace App\Eloquents;

class Contract extends BaseModel
{
    // protected $fillable = [
    //     'name',
    // ];
    protected $guarded = ["id", "created_at", "updated_at", "version"];
    protected $hidden = ['version'];

    public function groups()
    {
        return $this->hasMany(Group::class);
    }

    public function scopeAllContract($query)
    {
        return $query->where('id', '<>', 1);
    }
}
