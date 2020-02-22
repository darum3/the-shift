<?php

namespace App\Providers;

use App\Eloquents\Group;
use App\Policies\GroupPolicy;
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
            $contractGroup = Auth::user()->groups->first(function($item) { return $item->contract_id == session('contract_id'); });
            return $contractGroup ? $contractGroup->flg_admin : false;
        });
    }
}
