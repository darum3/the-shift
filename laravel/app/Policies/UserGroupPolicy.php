<?php

namespace App\Policies;

use App\Eloquents\Group;
use App\Eloquents\UserGroup;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserGroupPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any user groups.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the user group.
     *
     * @param  \App\User  $user
     * @param  \App\Eloquents\UserGroup  $userGroup
     * @return mixed
     */
    public function view(User $user, UserGroup $userGroup)
    {
        //
    }

    /**
     * Determine whether the user can create user groups.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the user group.
     *
     * @param  \App\User  $user
     * @param  \App\Eloquents\UserGroup  $userGroup
     * @return mixed
     */
    public function update(User $user, UserGroup $userGroup)
    {
        // Adminまたは、Manager（組織の管理グループ所属＝本部）だったらOK
        if ($user->isSysAdmin() || $user->isManager($userGroup->load('group')->group->contract_id)) {
            return true;
        }

        // 自分がグループのAdmin(=店長)だったらOK
        $userGroup = UserGroup::whereUserId($user->id)->whereGroupId($userGroup->group_id)->first();
        return optional($userGroup)->flg_admin;
    }

    /**
     * Determine whether the user can delete the user group.
     *
     * @param  \App\User  $user
     * @param  \App\Eloquents\UserGroup  $userGroup
     * @return mixed
     */
    public function delete(User $user, UserGroup $userGroup)
    {
        // AdminはOK
        if ($user->isSysAdmin()) {
            return true;
        }
        // 自分がAdminグループ所属だったらOK
        $groups = Group::whereContractId(session('contract_id'))->get();
        $adminGroup = $groups->first(function($value, $key) {
            return $value->flg_admin;
        });
        if (!is_null(UserGroup::whereUserId($user->id)->whereGroupId($adminGroup->id)->get())) {
            return true;
        }

        // 自分がグループのAdmin(=店長)だったらOK
        $userGroup = UserGroup::whereUserId($user->id)->whereGroupId($userGroup->group_id)->get();
        return optional($userGroup)->flg_admin;
    }

    /**
     * Determine whether the user can restore the user group.
     *
     * @param  \App\User  $user
     * @param  \App\Eloquents\UserGroup  $userGroup
     * @return mixed
     */
    public function restore(User $user, UserGroup $userGroup)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the user group.
     *
     * @param  \App\User  $user
     * @param  \App\Eloquents\UserGroup  $userGroup
     * @return mixed
     */
    public function forceDelete(User $user, UserGroup $userGroup)
    {
        //
    }
}
