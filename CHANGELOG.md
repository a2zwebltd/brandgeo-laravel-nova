# Changelog

All notable changes to `a2zwebltd/brandgeo-laravel-nova` are documented in this file.

## [1.0.8] - 2026-09-23

### Security
- The standalone dashboard routes (`/{path}`, `POST /{path}/api-key`, `POST /{path}/default-brand`) ran behind Nova's `Authenticate` only, which checks login but not the `viewNova` gate. Any logged-in user, admin or not, could view the BrandGEO account data and overwrite `BRANDGEO_API_KEY` / `BRANDGEO_DEFAULT_BRAND` in `.env`. The default `middleware` config now includes Nova's `Authorize`, and the service provider appends it whenever a published config leaves it out, so existing installs are protected by a composer update alone.
