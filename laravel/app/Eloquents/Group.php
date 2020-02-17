<?php

namespace App\Eloquents;

use Illuminate\Database\Eloquent\Model;

class Group extends BaseModel
{
    protected $guarded = ["id", "created_at", "updated_at", "version"];
    protected $hidden = ['version'];
}
