<?php

use A2ZWeb\BrandGeoNova\Http\Controllers\DashboardController;
use A2ZWeb\BrandGeoNova\Http\Controllers\DefaultBrandController;
use A2ZWeb\BrandGeoNova\Http\Controllers\SaveApiKeyController;
use Illuminate\Support\Facades\Route;

Route::get('/', DashboardController::class)->name('dashboard');
Route::post('/api-key', SaveApiKeyController::class)->name('api-key.store');
Route::post('/default-brand', DefaultBrandController::class)->name('default-brand.store');

// Bundled BrandGEO logo — served from the package so no vendor:publish is needed.
Route::get('/logo.png', fn () => response()->file(
    dirname(__DIR__).'/resources/img/brandgeo-logo.png',
    ['Cache-Control' => 'public, max-age=86400'],
))->name('logo');
