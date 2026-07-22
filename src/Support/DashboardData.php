<?php

namespace A2ZWeb\BrandGeoNova\Support;

use A2ZWeb\BrandGeoClient\BrandGeoClient;
use A2ZWeb\BrandGeoClient\Data\Account;
use A2ZWeb\BrandGeoClient\Data\Audit;
use A2ZWeb\BrandGeoClient\Data\Brand;
use A2ZWeb\BrandGeoClient\Enums\AuditMode;
use A2ZWeb\BrandGeoClient\Exceptions\SubscriptionRequiredException;
use Illuminate\Support\Facades\Cache;

/**
 * Cached API fan-out for the dashboard. Every call is memoized in the app
 * cache (trial keys are limited to 30 req/min) — `forget()` implements the
 * "Refresh data" button.
 */
class DashboardData
{
    private string $prefix;

    public function __construct(private readonly BrandGeoClient $client)
    {
        $this->prefix = 'brandgeo-nova.'.substr(sha1((string) config('brandgeo-client.api_key')), 0, 12);
    }

    private function remember(string $key, callable $resolve): mixed
    {
        $cacheKey = "{$this->prefix}.{$key}";
        $ttl = (int) config('brandgeo-nova.cache_ttl', 60);

        $value = Cache::remember($cacheKey, $ttl, $resolve);

        // A cached entry written under a different autoload state (composer
        // install in flight, opcache serving a stale classmap, a DTO renamed
        // between package versions) unserializes to __PHP_Incomplete_Class
        // instead of throwing. Drop the poisoned entry and resolve fresh so
        // the dashboard self-heals instead of 500-ing until the TTL passes.
        if ($this->holdsIncompleteClass($value)) {
            Cache::forget($cacheKey);

            $value = Cache::remember($cacheKey, $ttl, $resolve);
        }

        return $value;
    }

    private function holdsIncompleteClass(mixed $value): bool
    {
        if (is_object($value) && get_class($value) === \__PHP_Incomplete_Class::class) {
            return true;
        }

        if (is_array($value)) {
            foreach ($value as $item) {
                if ($this->holdsIncompleteClass($item)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function flush(?string $brandUuid = null): void
    {
        $keys = ['account', 'brands'];

        if ($brandUuid !== null) {
            $keys = [...$keys, ...array_map(
                fn (string $suffix) => "brand.{$brandUuid}.{$suffix}",
                ['audits', 'audit-detail', 'monitor', 'competitors', 'templates', 'runs', 'trend'],
            )];
        }

        foreach ($keys as $key) {
            Cache::forget("{$this->prefix}.{$key}");
        }
    }

    public function account(): Account
    {
        return $this->remember('account', fn () => $this->client->account()->get());
    }

    /** @return list<Brand> */
    public function brands(): array
    {
        return $this->remember('brands', fn () => $this->client->brands()->list(perPage: 100)->items);
    }

    /** @return list<Audit> */
    public function audits(string $brandUuid): array
    {
        return $this->remember(
            "brand.{$brandUuid}.audits",
            fn () => $this->client->audits()->list(brand: $brandUuid, perPage: 25)->items,
        );
    }

    /** Latest completed audit with reports + recommendations, or null. */
    public function latestAuditDetail(string $brandUuid): ?Audit
    {
        return $this->remember("brand.{$brandUuid}.audit-detail", function () use ($brandUuid) {
            $latest = collect($this->audits($brandUuid))->first(fn (Audit $audit) => $audit->isComplete())
                ?? ($this->audits($brandUuid)[0] ?? null);

            return $latest ? $this->client->audits()->get($latest->uuid) : null;
        });
    }

    /**
     * Full monitoring bundle for the brand's monitor, or null when the brand
     * is not monitored. Throws SubscriptionRequiredException through (caller
     * renders the paywall state).
     *
     * @return array{monitor: mixed, competitors: array, templates: array, runs: array, trend: mixed}|null
     */
    public function monitoring(Brand $brand): ?array
    {
        if ($brand->monitor === null) {
            return null;
        }

        $uuid = $brand->monitor->uuid;

        return $this->remember("brand.{$brand->uuid}.monitor", function () use ($uuid) {
            $account = $this->account();
            $days = min(90, $account->quota->trendHistoryDays);

            return [
                'monitor' => $this->client->monitors()->get($uuid),
                'competitors' => $this->client->monitors()->competitors($uuid, perPage: 25)->items,
                'templates' => $this->client->monitors()->promptTemplates($uuid, perPage: 100)->items,
                'runs' => $this->client->monitors()->runs($uuid, perPage: 50)->items,
                'trend' => $this->client->monitors()->trend($uuid, days: $days),
            ];
        });
    }

    /**
     * Average normalized score per audit mode, mirroring the BrandGEO app's
     * AuditRequest::overallAverageScore(). Audit::overallScore alone is the
     * trained-mode average — on a trial with only Gemini unlocked that reads
     * as a misleading near-zero while web search scores healthily.
     *
     * @return array{trained: ?float, web: ?float}
     */
    public function overallScores(?Audit $audit): array
    {
        $scores = ['trained' => [], 'web' => []];

        foreach ($audit?->reports ?? [] as $report) {
            if ($report->isLocked() || $report->normalizedScore === null) {
                continue;
            }

            $scores[$report->mode === AuditMode::WebSearch ? 'web' : 'trained'][] = $report->normalizedScore;
        }

        $average = fn (array $values) => $values === []
            ? null
            : round(array_sum($values) / count($values), 1);

        return [
            'trained' => $average($scores['trained']),
            'web' => $average($scores['web']),
        ];
    }

    /**
     * Aggregate per-dimension averages across the audit's reports.
     *
     * @return array{trained: array<string, ?float>, web: array<string, ?float>, hasWeb: bool}
     */
    public function dimensionAverages(?Audit $audit): array
    {
        $sums = ['trained' => [], 'web' => []];

        foreach ($audit?->reports ?? [] as $report) {
            if ($report->isLocked() || ! is_array($report->result)) {
                continue;
            }

            $bucket = $report->mode === AuditMode::WebSearch ? 'web' : 'trained';
            $sectionScores = $report->result['scoring_summary']['section_scores'] ?? [];

            foreach (array_keys(Presentation::SECTIONS) as $shortKey) {
                $score = Presentation::sectionScore($sectionScores[$shortKey] ?? null);

                if ($score !== null) {
                    $sums[$bucket][$shortKey][] = $score;
                }
            }
        }

        $average = fn (array $bucket) => collect(Presentation::SECTIONS)->keys()->mapWithKeys(fn (string $key) => [
            $key => isset($bucket[$key]) ? round(array_sum($bucket[$key]) / count($bucket[$key]), 1) : null,
        ])->all();

        return [
            'trained' => $average($sums['trained']),
            'web' => $average($sums['web']),
            'hasWeb' => $sums['web'] !== [],
        ];
    }

    /**
     * Wrap a paywallable section: returns [value, paywalled-bool].
     */
    public static function guard(callable $resolve): array
    {
        try {
            return [$resolve(), false];
        } catch (SubscriptionRequiredException) {
            return [null, true];
        }
    }
}
