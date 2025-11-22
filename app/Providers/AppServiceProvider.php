<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * @var string|null
     */
    protected $namespace = 'App\\Http\\Controllers';
    protected $adminNamespace = 'App\\Http\\Controllers\\Admin';
    protected $providerNamespace = 'App\\Http\\Controllers\\Provider';
    protected $apiNamespace = 'App\\Http\\Controllers\\Api';

    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->apiNamespace)
            ->group(base_path('routes/api.php'));

        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));

        if (file_exists(base_path('routes/admin.php')))
        {
            Route::prefix('admin')
                ->middleware('web')
                ->namespace($this->adminNamespace)
                ->group(base_path('routes/admin.php'));
        }

        if (file_exists(base_path('routes/provider.php')))
        {
            Route::prefix('provider')
                ->middleware('web')
                ->namespace($this->providerNamespace)
                ->group(base_path('routes/provider.php'));
        }
    }

    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}