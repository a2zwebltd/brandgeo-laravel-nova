# BrandGEO for Laravel Nova

[![Packagist Version](https://img.shields.io/packagist/v/a2zwebltd/brandgeo-laravel-nova.svg)](https://packagist.org/packages/a2zwebltd/brandgeo-laravel-nova)
[![Downloads](https://img.shields.io/packagist/dt/a2zwebltd/brandgeo-laravel-nova.svg)](https://packagist.org/packages/a2zwebltd/brandgeo-laravel-nova)
![PHP](https://img.shields.io/badge/PHP-%5E8.2-blue)
![Laravel](https://img.shields.io/badge/Laravel-11%20%7C%2012%20%7C%2013-blue)

A fully branded [BrandGEO](https://brandgeo.co) client dashboard inside your Laravel Nova admin: account & subscription status, brands with AI visibility scores, per-engine audit drill-downs, monitoring KPIs, share of voice, citations and trends — everything your BrandGEO API key can see, shaped by the account's plan (trial accounts see locked engines and preview recommendations, exactly like the BrandGEO app).

Built on [`a2zwebltd/brandgeo-laravel-client`](https://github.com/a2zwebltd/brandgeo-laravel-client).

## Screenshot

![BrandGEO dashboard embedded in Laravel Nova](https://raw.githubusercontent.com/a2zwebltd/brandgeo-laravel-nova/refs/heads/main/img/brandgeo-laravel-nova-screenshot.png)

## Features

- **Embedded in the Nova SPA** — a real Nova tool page at `/nova/brandgeo` sharing Nova's menu and chrome (no JS build step; the iframe auto-resizes, no scrollbars). A standalone page at `/brandgeo-dashboard` is also available.
- **Brand-centric dashboard** — a default brand (auto-selected for single-brand accounts, persisted to `.env`) with a header switcher; per-brand view with monitoring first, then the latest audit.
- **Monitoring** — KPI row, SVG visibility-trend chart, share of voice, competitor table, top cited sources, prompt-category insights, custom queries and recent AI answers with expandable responses.
- **Audit drill-down** — engines grouped by mode (Online · Web Search first, then Offline · Trained), each expanding into key findings, analysis, the six visibility dimensions with confidence levels, and web sources.
- **API key onboarding** — when `BRANDGEO_API_KEY` is missing, a branded form validates the key against the live API and writes it to the host `.env`; the page fully reloads with the new data.
- **Plan-aware** — trial keys render locked-engine stubs and preview action plans; expired trials surface the 402 paywall state; deep links open the full BrandGEO dashboard.

## Requirements

- PHP 8.2+
- Laravel 11 / 12 / 13
- Laravel Nova ^5.0
- A BrandGEO account ([brandgeo.co](https://brandgeo.co)) — the built-in form will ask for your API key on first run

## Installation

```bash
composer require a2zwebltd/brandgeo-laravel-nova
```

Register the tool in your `NovaServiceProvider`:

```php
use A2ZWeb\BrandGeoNova\BrandGeoNovaTool;

public function tools(): array
{
    return [
        new BrandGeoNovaTool,
        // ...
    ];
}
```

If your app customizes `Nova::mainMenu()`, add the menu section yourself:

```php
use A2ZWeb\BrandGeoNova\BrandGeoNova;

Nova::mainMenu(fn () => [
    // ...your sections...
    BrandGeoNova::menuSection(),
]);
```

That's it — open **BrandGEO** in the Nova sidebar. If no API key is configured yet, the onboarding form appears; the submitted key is validated against the live BrandGEO API before being saved to `.env`.

## Configuration

```bash
php artisan vendor:publish --tag=brandgeo-nova-config
```

| Key | Default | Description |
| --- | --- | --- |
| `path` | `brandgeo-dashboard` | URL of the standalone dashboard (also loaded in the Nova iframe) |
| `middleware` | `['web', Nova Authenticate]` | Only Nova-authorized users can access it |
| `default_brand` | `env('BRANDGEO_DEFAULT_BRAND')` | Brand uuid opened by default ("Set as default" writes it) |
| `cache_ttl` | `60` | Seconds to cache API responses (the "Refresh data" button bypasses it) |
| `app_url` | derived from client `base_url` | Base for "Open in BrandGEO" deep links |

Client settings (base URL, timeouts, TLS verify) come from [`brandgeo-laravel-client`](https://github.com/a2zwebltd/brandgeo-laravel-client) — see `BRANDGEO_BASE_URL`, `BRANDGEO_API_KEY`, `BRANDGEO_VERIFY_SSL`.

> Running `config:cache`? Re-run it after saving a key or default brand — the forms write `.env`, which cached configs ignore.

## BrandGEO API resources

- [BrandGEO](https://brandgeo.co) — AI brand visibility audits & monitoring
- [API documentation](https://brandgeo.co/developers) — endpoints, auth, plan access, FAQ
- [Interactive API playground](https://brandgeo.co/developers/playground) — try every endpoint in the browser
- [OpenAPI 3.1 spec (YAML)](https://brandgeo.co/developers/openapi.yaml) — machine-readable contract
- [Get your API key](https://brandgeo.co/settings/api) — Settings → API in your dashboard

## Security Vulnerabilities

If you discover any security related issues, please email [biuro@a2zweb.co](mailto:biuro@a2zweb.co) instead of using the issue tracker.

## License

MIT. See [LICENSE](LICENSE.md).

## Credits

Developed and maintained by the **A2Z WEB** crew:
* [Dawid Makowski](https://github.com/makowskid)
* Website: [https://a2zweb.co/](https://a2zweb.co/)
* GitHub: [https://github.com/a2zwebltd/](https://github.com/a2zwebltd/)
