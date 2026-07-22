<?php

namespace A2ZWeb\BrandGeoNova\Support;

use A2ZWeb\BrandGeoClient\Data\PromptRun;
use A2ZWeb\BrandGeoClient\Data\PromptTemplate;

/**
 * Port of BrandGEO's App\Support\MonitorInsights, computed client-side from
 * the public API's prompt runs + templates.
 */
class MonitorInsights
{
    /**
     * Group non-custom runs by their template's prompt category.
     *
     * @param  list<PromptRun>  $runs
     * @param  list<PromptTemplate>  $templates
     * @return list<array{category: string, visible: int, total: int, rate: float, sentiment: ?string, providers: array<string, bool>}>
     */
    public static function categories(array $runs, array $templates): array
    {
        $templateById = collect($templates)->keyBy('id');

        $grouped = collect($runs)
            ->filter(function (PromptRun $run) use ($templateById) {
                $template = $run->promptTemplateId !== null ? $templateById->get($run->promptTemplateId) : null;

                return $template !== null && ! $template->isCustom;
            })
            ->groupBy(fn (PromptRun $run) => $templateById->get($run->promptTemplateId)->category->value);

        return $grouped->map(function ($runs, string $category) {
            $visible = $runs->where('brandMentioned', true)->count();
            $total = $runs->count();

            $providers = [];
            foreach ($runs as $run) {
                $providers[$run->provider->value] = ($providers[$run->provider->value] ?? false) || $run->brandMentioned === true;
            }

            return [
                'category' => $category,
                'visible' => $visible,
                'total' => $total,
                'rate' => $total > 0 ? round($visible / $total * 100, 1) : 0.0,
                'sentiment' => $runs->pluck('sentiment')->filter()->countBy()->sortDesc()->keys()->first(),
                'providers' => $providers,
            ];
        })->sortKeys()->values()->all();
    }

    /**
     * One row per custom tracked query, best (min) brand position per engine.
     *
     * @param  list<PromptRun>  $runs
     * @param  list<PromptTemplate>  $templates
     * @return list<array{query: string, visible: int, total: int, rate: float, sentiment: ?string, providers: array<string, array{mentioned: bool, position: ?int}>}>
     */
    public static function customQueries(array $runs, array $templates): array
    {
        $customTemplates = collect($templates)->filter(fn (PromptTemplate $t) => $t->isCustom)->keyBy('id');

        return collect($runs)
            ->filter(fn (PromptRun $run) => $run->promptTemplateId !== null && $customTemplates->has($run->promptTemplateId))
            ->groupBy('promptTemplateId')
            ->map(function ($runs, int $templateId) use ($customTemplates) {
                $visible = $runs->where('brandMentioned', true)->count();
                $total = $runs->count();

                $providers = [];
                foreach ($runs as $run) {
                    $key = $run->provider->value;
                    $mentioned = ($providers[$key]['mentioned'] ?? false) || $run->brandMentioned === true;
                    $positions = array_filter([
                        $providers[$key]['position'] ?? null,
                        $run->brandMentioned ? $run->brandPosition : null,
                    ]);

                    $providers[$key] = [
                        'mentioned' => $mentioned,
                        'position' => $positions === [] ? null : min($positions),
                    ];
                }

                return [
                    'query' => $customTemplates->get($templateId)->template,
                    'visible' => $visible,
                    'total' => $total,
                    'rate' => $total > 0 ? round($visible / $total * 100, 1) : 0.0,
                    'sentiment' => $runs->pluck('sentiment')->filter()->countBy()->sortDesc()->keys()->first(),
                    'providers' => $providers,
                ];
            })
            ->sortByDesc('rate')
            ->values()
            ->all();
    }
}
