<?php

namespace App\Eloquents;

use App\Eloquents\UserGroup;
use App\User;

class Group extends BaseModel
{
    protected $guarded = ["id", "created_at", "updated_at", "version"];
    protected $hidden = ['version'];

    public function users()
    {
        return $this->belongsToMany(User::class, UserGroup::class)->withPivot('flg_admin');
    }
}
