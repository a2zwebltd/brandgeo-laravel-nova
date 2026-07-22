<?php

use A2ZWeb\BrandGeoNova\Http\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/', ToolController::class)->name('brandgeo-nova.tool');
