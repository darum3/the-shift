<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * The path to the "home" route for your application.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));

        // Admin
        Route::prefix('admin')
            ->middleware(['web', 'auth', 'can:ADM'])
            ->namespace($this->namespace.'\Admin')
            ->group(base_path('routes/web/admin.php'));

        // グループ全体の管理者（manage：シフトを組む人の統括を想定）
        Route::prefix('manage')
            ->middleware(['web', 'auth', 'can:MNG'])
            ->namespace($this->namespace.'\Manage')
            ->group(base_path('routes/web/manage.php'));

        // グループの管理者（シフトを組む人を想定）
        Route::prefix('g-manage')
            ->middleware(['web', 'auth', 'can:G-MNG'])
            ->namespace($this->namespace.'\GroupManage')
            ->group(base_path('routes/web/g-manage.php'));

        // メンバー（グループ管理者も可）
        Route::prefix('member')
            ->middleware(['web', 'auth', 'can:MEMBER,G-MNG'])
            ->group(base_path('routes/web/member.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
    }
}
