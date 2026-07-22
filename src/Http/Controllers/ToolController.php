<?php

namespace A2ZWeb\BrandGeoNova\Http\Controllers;

use Illuminate\Routing\Controller;
use Inertia\Response;

class ToolController extends Controller
{
    /**
     * The Nova SPA page hosting the branded dashboard in an iframe.
     */
    public function __invoke(): Response
    {
        return inertia('BrandGeoNovaTool', [
            'src' => url(config('brandgeo-nova.path')).'?embedded=1',
        ]);
    }
}
