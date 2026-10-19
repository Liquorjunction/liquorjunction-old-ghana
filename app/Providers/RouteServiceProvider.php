<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';
    protected $dashboardNamespace = 'App\Http\Controllers\Dashboard';
   // protected $frontNamespace = 'App\Http\Controllers\Frontend';
    protected $apisNamespace = 'App\Http\Controllers\APIs';
    

    public const HOME = '/login';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot()
    {
        //
        $this->configureRateLimiting();
        parent::boot();
    }

    public function map()
    {
        $this->mapApiRoutes();
        $this->mapDashboardRoutes();
        $this->mapApisRoutes();
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
       /* Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));*/
        Route::namespace($this->namespace)
            ->group(base_path('routes/web.php'));
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
        // Route::prefix('api')
        //     ->middleware('api')
        //     ->namespace($this->namespace)
        //     ->group(base_path('routes/api.php'));
          Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));

 

            Route::group([
                'namespace'  => "{$this->namespace}\Api\V1",
                'prefix'     => 'api/v1',
            ], function ($router) {
                require base_path('routes/api_v1.php');
            });

            Route::group([
                'namespace'  => "{$this->namespace}\Api\V2",
                'prefix'     => 'api/v2',
            ], function ($router) {
                require base_path('routes/api_v2.php');
            });
    
    }

    /**
     * Define the "Dashboard" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapDashboardRoutes()
    {
        Route::prefix(env('BACKEND_PATH'))
            ->middleware('auth')
            ->namespace($this->dashboardNamespace)
            ->group(base_path('routes/dashboard.php'));
    }

    /**
     * Define the "APIs" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApisRoutes()
    {
        Route::prefix("/api/v1")
            ->middleware('web')
            ->namespace($this->apisNamespace)
            ->group(base_path('routes/api.php'));
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }

}
