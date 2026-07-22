<?php

namespace A2ZWeb\BrandGeoNova\Http\Controllers;

use A2ZWeb\BrandGeoClient\BrandGeoClient;
use A2ZWeb\BrandGeoNova\Support\DashboardData;
use A2ZWeb\BrandGeoNova\Support\EnvWriter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DefaultBrandController extends Controller
{
    /**
     * Persist the default brand (shown on every dashboard load) to .env.
     */
    public function __invoke(Request $request, BrandGeoClient $client): RedirectResponse
    {
        $validated = $request->validate([
            'brand' => ['required', 'uuid'],
        ]);

        $brands = (new DashboardData($client))->brands();
        $brand = collect($brands)->firstWhere('uuid', $validated['brand']);

        if ($brand === null) {
            return back()->withErrors(['brand' => 'That brand does not belong to the connected BrandGEO account.']);
        }

        EnvWriter::set('BRANDGEO_DEFAULT_BRAND', $brand->uuid);
        config()->set('brandgeo-nova.default_brand', $brand->uuid);

        return redirect()
            ->route('brandgeo-nova.dashboard', array_filter(['embedded' => $request->input('embedded')]))
            ->with('brandgeo-nova.status', "Default brand set to {$brand->name}.");
    }
}
