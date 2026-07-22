<?php

namespace A2ZWeb\BrandGeoNova\Http\Controllers;

use A2ZWeb\BrandGeoClient\Exceptions\AuthenticationException;
use A2ZWeb\BrandGeoClient\Exceptions\BrandGeoException;
use A2ZWeb\BrandGeoClient\Facades\BrandGeo;
use A2ZWeb\BrandGeoNova\Support\EnvWriter;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class SaveApiKeyController extends Controller
{
    /**
     * Validate the submitted BrandGEO API key against the live API, then
     * persist it to the host app's .env. The key is never stored unless
     * it authenticates successfully.
     */
    public function __invoke(Request $request): Response
    {
        $validated = $request->validate([
            'api_key' => ['required', 'string', 'min:20'],
        ]);

        $key = trim($validated['api_key']);

        try {
            $account = BrandGeo::withApiKey($key)->account()->get();
        } catch (AuthenticationException) {
            return back()->withErrors(['api_key' => 'This API key was rejected by BrandGEO (401 Unauthorized). Check it at Settings → API.'])->withInput();
        } catch (BrandGeoException $e) {
            return back()->withErrors(['api_key' => 'BrandGEO API error while validating the key: '.$e->getMessage()])->withInput();
        } catch (Throwable $e) {
            return back()->withErrors(['api_key' => 'Could not reach the BrandGEO API: '.$e->getMessage()])->withInput();
        }

        EnvWriter::set('BRANDGEO_API_KEY', $key);
        config()->set('brandgeo-client.api_key', $key);

        // Single-brand accounts (the common case) get their default brand
        // auto-selected; multi-brand accounts see the picker on redirect.
        try {
            $brands = BrandGeo::withApiKey($key)->brands()->list(perPage: 2)->items;

            if (count($brands) === 1) {
                EnvWriter::set('BRANDGEO_DEFAULT_BRAND', $brands[0]->uuid);
                config()->set('brandgeo-nova.default_brand', $brands[0]->uuid);
            }
        } catch (Throwable) {
            // Non-fatal — the dashboard will offer the picker.
        }

        session()->flash('brandgeo-nova.status', sprintf(
            'API key verified and saved — connected as %s (%s, %s plan).',
            $account->email,
            $account->subscription->status->value,
            $account->subscription->plan ?? 'no',
        ));

        // Full page reload (top window when embedded in the Nova SPA) so the
        // dashboard boots fresh with the new key and loads all data.
        return response()->view('brandgeo-nova::key-saved', [
            'target' => route('brandgeo-nova.dashboard', array_filter(['embedded' => $request->input('embedded')])),
        ]);
    }
}
