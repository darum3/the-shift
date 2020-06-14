<?php

namespace App;

use App\Eloquents\Contract;
use App\Eloquents\Group;
use App\Eloquents\UserGroup;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function groups()
    {
        return $this->belongsToMany(Group::class, UserGroup::class);
    }

    public function isSysAdmin()
    {
        return $this->flg_system_admin == true;
    }

    public function isManager(int $contractId)
    {
        $groups = $this->load('groups')->groups;
        return $groups->first(function($value, $key) use($contractId) {
            return $value->flg_admin && $value->contract_id === $contractId;
        });
    }
}
