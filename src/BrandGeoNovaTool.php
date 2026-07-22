<?php

namespace A2ZWeb\BrandGeoNova;

use Illuminate\Http\Request;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool;

class BrandGeoNovaTool extends Tool
{
    public function boot(): void
    {
        Nova::script('brandgeo-nova', __DIR__.'/../dist/tool.js');
    }

    public function menu(Request $request): MenuSection
    {
        return BrandGeoNova::menuSection();
    }
}
