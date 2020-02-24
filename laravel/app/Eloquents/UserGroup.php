<?php

namespace App\Eloquents;

use App\User;

class UserGroup extends BaseModel
{
    protected $table = 'user_group';

    protected $guarded = ["id", "created_at", "updated_at", "version"];
    protected $hidden = ['version'];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
