<?php

namespace App\Eloquents;

class WorkType extends BaseModel
{
    protected $guarded = ["id", "created_at", "updated_at", "version"];
    protected $hidden = ['version', 'created_at', 'deleted_at', 'updated_at', 'sysinfo'];
}
