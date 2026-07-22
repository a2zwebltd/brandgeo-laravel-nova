<?php

use Laravel\Nova\Http\Middleware\Authenticate;

return [

    /*
    |--------------------------------------------------------------------------
    | Route path
    |--------------------------------------------------------------------------
    |
    | The dashboard is served OUTSIDE the Nova SPA (a fully branded page) but
    | behind Nova's auth middleware, and linked from the Nova menu.
    |
    */

    'path' => env('BRANDGEO_NOVA_PATH', 'brandgeo-dashboard'),

    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    |
    | Applied to the dashboard routes. Defaults to the web group plus Nova's
    | Authenticate middleware, so only Nova-authorized users can access it.
    |
    */

    'middleware' => [
        'web',
        Authenticate::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Default brand
    |--------------------------------------------------------------------------
    |
    | The brand uuid shown by default. Saved from the dashboard UI ("Set as
    | default") via the same .env writer as the API key. When unset and the
    | account has exactly one brand, it is auto-selected.
    |
    */

    'default_brand' => env('BRANDGEO_DEFAULT_BRAND'),

    /*
    |--------------------------------------------------------------------------
    | Cache TTL
    |--------------------------------------------------------------------------
    |
    | Seconds to cache API responses. Keeps repeat page loads fast and trial
    | accounts under their 30 req/min limit. The "Refresh data" button
    | bypasses it.
    |
    */

    'cache_ttl' => env('BRANDGEO_NOVA_CACHE_TTL', 60),

    /*
    |--------------------------------------------------------------------------
    | BrandGEO app URL
    |--------------------------------------------------------------------------
    |
    | Base URL for "Open in BrandGEO" deep links. When null it is derived
    | from brandgeo-client.base_url by stripping the /api/v1 suffix.
    |
    */

    'app_url' => env('BRANDGEO_APP_URL'),

];
