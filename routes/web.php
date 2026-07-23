<?php

use A2ZWeb\BrandGeoNova\Http\Controllers\DashboardController;
use A2ZWeb\BrandGeoNova\Http\Controllers\DefaultBrandController;
use A2ZWeb\BrandGeoNova\Http\Controllers\SaveApiKeyController;
use Illuminate\Support\Facades\Route;

Route::get('/', DashboardController::class)->name('dashboard');
Route::post('/api-key', SaveApiKeyController::class)->name('api-key.store');
Route::post('/default-brand', DefaultBrandController::class)->name('default-brand.store');

// Bundled BrandGEO logo — served from the package so no vendor:publish is needed.
// Deliberately extensionless: a typical nginx site serves *.png as static files
// and never forwards them to PHP, so a `/logo.png` route 404s on those hosts
// (it only ever worked under Herd/Valet, which route everything to PHP).
$logo = fn () => response()->file(
    dirname(__DIR__).'/resources/img/brandgeo-logo.png',
    ['Content-Type' => 'image/png', 'Cache-Control' => 'public, max-age=86400'],
);

Route::get('/logo', $logo)->name('logo');

// Legacy URL, kept for pages cached before the rename.
Route::get('/logo.png', $logo)->name('logo.png');
