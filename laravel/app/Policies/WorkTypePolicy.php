<?php

namespace App\Policies;

use App\User;
use App\WorkType;
use Illuminate\Auth\Access\HandlesAuthorization;

class WorkTypePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any work types.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the work type.
     *
     * @param  \App\User  $user
     * @param  \App\WorkType  $workType
     * @return mixed
     */
    public function view(User $user, WorkType $workType)
    {
        //
    }

    /**
     * Determine whether the user can create work types.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the work type.
     *
     * @param  \App\User  $user
     * @param  \App\WorkType  $workType
     * @return mixed
     */
    public function update(User $user, WorkType $workType)
    {
        //
    }

    /**
     * Determine whether the user can delete the work type.
     *
     * @param  \App\User  $user
     * @param  \App\WorkType  $workType
     * @return mixed
     */
    public function delete(User $user, WorkType $workType)
    {
        //
    }

    /**
     * Determine whether the user can restore the work type.
     *
     * @param  \App\User  $user
     * @param  \App\WorkType  $workType
     * @return mixed
     */
    public function restore(User $user, WorkType $workType)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the work type.
     *
     * @param  \App\User  $user
     * @param  \App\WorkType  $workType
     * @return mixed
     */
    public function forceDelete(User $user, WorkType $workType)
    {
        //
    }
}
