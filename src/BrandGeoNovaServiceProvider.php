<?php

namespace A2ZWeb\BrandGeoNova;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Http\Middleware\Authenticate;
use Laravel\Nova\Http\Middleware\Authorize;
use Laravel\Nova\Nova;

class BrandGeoNovaServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/brandgeo-nova.php', 'brandgeo-nova');
    }

    public function boot(): void
    {
        $this->registerRoutes();
        $this->registerViews();
        $this->registerPublishing();
    }

    private function registerRoutes(): void
    {
        // The branded dashboard itself (standalone page, also loaded in the
        // SPA tool's iframe) — behind Nova auth AND the viewNova gate.
        Route::middleware($this->dashboardMiddleware())
            ->prefix(config('brandgeo-nova.path'))
            ->name('brandgeo-nova.')
            ->group(__DIR__.'/../routes/web.php');

        // The Inertia page inside the Nova SPA (shares Nova's menu/chrome).
        // Authenticate before Authorize, matching Nova's own tool scaffold:
        // guests get the login redirect rather than a bare 403.
        if (class_exists(Nova::class)) {
            $this->app->booted(function () {
                Nova::router(['nova', Authenticate::class, Authorize::class], 'brandgeo')
                    ->group(__DIR__.'/../routes/inertia.php');
            });
        }
    }

    /**
     * The configured middleware, with Nova's Authorize (the viewNova gate)
     * always enforced. Authenticate alone only checks that someone is logged
     * in, yet these routes show account data and write the API key to .env,
     * so a published config from before v1.0.8 must not leave them open to
     * every logged-in user.
     *
     * @return array<int, string>
     */
    private function dashboardMiddleware(): array
    {
        $middleware = array_values((array) config('brandgeo-nova.middleware', []));

        if (class_exists(Authorize::class) && ! in_array(Authorize::class, $middleware, true)) {
            $middleware[] = Authorize::class;
        }

        return $middleware;
    }

    private function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'brandgeo-nova');
    }

    private function registerPublishing(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__.'/../config/brandgeo-nova.php' => config_path('brandgeo-nova.php'),
        ], 'brandgeo-nova-config');
    }
}
