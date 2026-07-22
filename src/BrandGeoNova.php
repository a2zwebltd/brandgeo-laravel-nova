<?php

namespace A2ZWeb\BrandGeoNova;

use Laravel\Nova\Menu\MenuSection;

class BrandGeoNova
{
    /**
     * SPA menu entry — navigates to the embedded tool page (/nova/brandgeo).
     * Hosts that customize Nova::mainMenu() add `BrandGeoNova::menuSection()`
     * to their menu array; hosts on the default menu get it from the Tool.
     */
    public static function menuSection(): MenuSection
    {
        return MenuSection::make('BrandGEO')
            ->path('/brandgeo')
            ->icon('globe-alt');
    }
}
