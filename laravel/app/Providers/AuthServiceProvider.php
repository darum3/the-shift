<?php

namespace App\Providers;

use App\Eloquents\Group;
use App\Eloquents\UserGroup;
use App\Policies\GroupPolicy;
use App\Policies\UserGroupPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
        Group::class => GroupPolicy::class,
        UserGroup::class => UserGroupPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('ADM', function() {
            return Auth::user()->isSysAdmin();
        });

        Gate::define('MNG', function() {
            $group = Group::whereContractId(session('contract_id'))->whereFlgAdmin(true)->first();
            return optional(UserGroup::whereUserId(Auth::user()->id)->whereGroupId($group->id))->count() > 0;
        });

        Gate::define('G-MNG', function() {
            return UserGroup::whereUserId(Auth::user()->id)->whereGroupId(session('group_id'))->count() > 0;
        });
    }
}
