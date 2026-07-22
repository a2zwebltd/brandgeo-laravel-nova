<?php

namespace A2ZWeb\BrandGeoNova\Http\Controllers;

use A2ZWeb\BrandGeoClient\BrandGeoClient;
use A2ZWeb\BrandGeoClient\Exceptions\AuthenticationException;
use A2ZWeb\BrandGeoClient\Exceptions\MissingApiKeyException;
use A2ZWeb\BrandGeoNova\Support\DashboardData;
use A2ZWeb\BrandGeoNova\Support\MonitorInsights;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Throwable;

class DashboardController extends Controller
{
    /**
     * Brand-centric dashboard: the selected brand's monitoring, audit
     * (per-engine drill-down), recommendations and history — shaped by the
     * account's plan (trial: locked engines + preview recommendations).
     */
    public function __invoke(Request $request, BrandGeoClient $client): View
    {
        if (! config('brandgeo-client.api_key')) {
            return view('brandgeo-nova::setup', ['error' => null]);
        }

        $data = new DashboardData($client);

        try {
            $account = $data->account();
            $brands = $data->brands();
        } catch (MissingApiKeyException|AuthenticationException $e) {
            return view('brandgeo-nova::setup', [
                'error' => 'The configured BRANDGEO_API_KEY is no longer valid ('.class_basename($e).'). Paste a fresh key from Settings → API.',
            ]);
        } catch (Throwable $e) {
            return view('brandgeo-nova::error', ['message' => $e->getMessage()]);
        }

        if ($brands === []) {
            return view('brandgeo-nova::no-brands', ['account' => $account]);
        }

        // Selected brand: explicit ?brand → configured default → single brand.
        $requested = $request->query('brand');
        $default = config('brandgeo-nova.default_brand');
        $brand = collect($brands)->firstWhere('uuid', $requested)
            ?? collect($brands)->firstWhere('uuid', $default)
            ?? (count($brands) === 1 ? $brands[0] : null);

        // Multiple brands and no default chosen yet → brand picker.
        if ($brand === null) {
            return view('brandgeo-nova::choose-brand', [
                'account' => $account,
                'brands' => $brands,
            ]);
        }

        if ($request->boolean('fresh')) {
            $data->flush($brand->uuid);
            $account = $data->account();
            $brands = $data->brands();
            $brand = collect($brands)->firstWhere('uuid', $brand->uuid) ?? $brand;
        }

        // Paywallable sections (expired trial → 402): guard each independently.
        [$audits, $auditsPaywalled] = DashboardData::guard(fn () => $data->audits($brand->uuid));
        [$auditDetail, $detailPaywalled] = DashboardData::guard(fn () => $data->latestAuditDetail($brand->uuid));
        [$monitoring, $monitorPaywalled] = DashboardData::guard(fn () => $data->monitoring($brand));

        $paywalled = $auditsPaywalled || $detailPaywalled || $monitorPaywalled;

        return view('brandgeo-nova::dashboard', [
            'account' => $account,
            'brands' => $brands,
            'brand' => $brand,
            'isDefault' => $brand->uuid === $default,
            'audits' => $audits ?? [],
            'audit' => $auditDetail,
            'dimensions' => $data->dimensionAverages($auditDetail),
            'overallScores' => $data->overallScores($auditDetail),
            'monitoring' => $monitoring,
            'categoryInsights' => $monitoring
                ? MonitorInsights::categories($monitoring['runs'], $monitoring['templates'])
                : [],
            'customQueries' => $monitoring
                ? MonitorInsights::customQueries($monitoring['runs'], $monitoring['templates'])
                : [],
            'paywalled' => $paywalled,
        ]);
    }
}
