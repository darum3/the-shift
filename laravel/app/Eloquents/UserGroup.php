<?php

namespace App\Eloquents;

use Illuminate\Database\Eloquent\Model;

class UserGroup extends BaseModel
{
    protected $table = 'user_group';

    protected $guarded = ["id", "created_at", "updated_at", "version"];
    protected $hidden = ['version'];
}
