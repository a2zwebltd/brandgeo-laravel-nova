@php
    use A2ZWeb\BrandGeoClient\Enums\AuditMode;
    use A2ZWeb\BrandGeoClient\Enums\SubscriptionStatus;
    use A2ZWeb\BrandGeoNova\Support\Presentation;

    $sub = $account->subscription;
    $appUrl = Presentation::appUrl();
    $embedded = request('embedded');

    $planBadge = match ($sub->status) {
        SubscriptionStatus::Trial => ['Trial', 'bg-amber-500/15 text-amber-700 dark:text-amber-300 border-amber-500/30'],
        SubscriptionStatus::Free => ['Free · full access', 'bg-emerald-500/15 text-emerald-700 dark:text-emerald-300 border-emerald-500/30'],
        SubscriptionStatus::Active => ['Paid · '.($sub->plan ?? 'subscribed'), 'bg-emerald-500/15 text-emerald-700 dark:text-emerald-300 border-emerald-500/30'],
        SubscriptionStatus::Expired => ['Trial expired', 'bg-red-500/15 text-red-700 dark:text-red-300 border-red-500/30'],
    };

    $monitor = $monitoring['monitor'] ?? null;
    $snapshot = $monitor?->latestSnapshot;
    $trend = $monitoring['trend'] ?? null;
    $overall = $audit?->overallScore ?? $brand->latestAudit?->overallScore;

    // Web − trained: the number the mode legend below the hero explains.
    $webScore = $overallScores['web'] ?? null;
    $trainedScore = $overallScores['trained'] ?? null;
    $gap = ($webScore !== null && $trainedScore !== null) ? round($webScore - $trainedScore, 1) : null;
@endphp

<x-brandgeo-nova::layout :title="'BrandGEO — '.$brand->name">
    <x-slot:headerRight>
        <span class="rounded-full border px-3 py-1 text-xs font-bold {{ $planBadge[1] }}">{{ $planBadge[0] }}</span>
        <x-brandgeo-nova::brand-switcher :brands="$brands" :brand="$brand" :is-default="$isDefault" />
    </x-slot:headerRight>

    {{-- ============================ Brand hero ============================ --}}
    <section class="rounded-3xl border border-zinc-200 bg-gradient-to-br from-white to-zinc-50 p-6 dark:border-white/10 dark:from-zinc-900 dark:to-zinc-950">
        <div class="flex flex-wrap items-center gap-6">
            {{-- Both audit modes, like the BrandGEO app's dual rings: the web-search
                 and trained averages tell different stories (a trial key often has a
                 healthy Gemini web score while the trained average sits near zero). --}}
            @if ($webScore !== null || $trainedScore !== null)
                {{-- Mode captions sit BELOW the rings — inside the circle they
                     overflow it (two-line "OFFLINE · TRAINED" spills past the arc). --}}
                <div class="flex shrink-0 items-center gap-5">
                    @if ($webScore !== null)
                        @php [$webTone, , , $webBand] = Presentation::score($webScore); @endphp
                        <div class="flex flex-col items-center gap-1.5">
                            <x-brandgeo-nova::score-ring :score="$webScore" :size="104" />
                            <span class="whitespace-nowrap text-[10px] font-bold uppercase tracking-widest text-violet-600 dark:text-violet-300">Online · Web</span>
                            <span class="rounded-full bg-zinc-100 px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider dark:bg-white/5 {{ $webTone }}">{{ $webBand }}</span>
                        </div>
                    @endif
                    @if ($trainedScore !== null)
                        @php [$trainedTone, , , $trainedBand] = Presentation::score($trainedScore); @endphp
                        <div class="flex flex-col items-center gap-1.5">
                            <x-brandgeo-nova::score-ring :score="$trainedScore" :size="$webScore !== null ? 88 : 104" />
                            <span class="whitespace-nowrap text-[10px] font-bold uppercase tracking-widest text-zinc-500">Offline · Trained</span>
                            <span class="rounded-full bg-zinc-100 px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider dark:bg-white/5 {{ $trainedTone }}">{{ $trainedBand }}</span>
                        </div>
                    @endif
                    {{-- The gap the legend below explains, as an actual number. --}}
                    @if ($gap !== null)
                        @php
                            $gapTone = match (true) {
                                $gap > 0 => 'text-emerald-600 dark:text-emerald-400',
                                $gap < 0 => 'text-red-600 dark:text-red-400',
                                default => 'text-zinc-500',
                            };
                            $gapArrow = $gap > 0 ? '▲' : ($gap < 0 ? '▼' : '•');
                        @endphp
                        <div class="flex flex-col items-center gap-1.5">
                            <span class="text-lg font-extrabold tabular-nums {{ $gapTone }}">{{ $gapArrow }} {{ $gap > 0 ? '+' : '' }}{{ number_format($gap, 1) }}</span>
                            <span class="whitespace-nowrap text-[10px] font-bold uppercase tracking-widest text-zinc-500">Web vs trained gap</span>
                        </div>
                    @endif
                </div>
            @else
                <x-brandgeo-nova::score-ring :score="$overall" :size="104" label="AI visibility" />
            @endif
            <div class="min-w-0 flex-1">
                <h1 class="truncate text-2xl font-extrabold">{{ $brand->name }}</h1>
                <p class="mt-0.5 truncate text-sm text-zinc-500 dark:text-zinc-400">
                    <a href="{{ $brand->url }}" target="_blank" rel="noopener" class="hover:underline">{{ $brand->url }}</a>
                    @if ($brand->industry) · {{ $brand->industry }} @endif
                </p>
                <div class="mt-2 flex flex-wrap items-center gap-2 text-[11px] font-semibold">
                    @if ($brand->latestAudit)
                        <span class="rounded-md bg-blue-500/10 px-2 py-1 text-blue-700 dark:text-blue-300">audit · {{ $brand->latestAudit->status->value }}</span>
                    @endif
                    @if ($brand->monitor)
                        <span class="rounded-md bg-purple-500/10 px-2 py-1 text-purple-700 dark:text-purple-300">monitor · {{ $brand->monitor->status->value }}</span>
                    @endif
                    @if ($sub->onTrial)
                        <span class="rounded-md bg-amber-500/10 px-2 py-1 text-amber-700 dark:text-amber-300">⏳ {{ $sub->trialDaysRemaining }} trial days left</span>
                    @endif
                </div>
            </div>
            <div class="flex shrink-0 flex-col items-end gap-2">
                <a href="{{ $appUrl }}/brands/{{ $brand->uuid }}" target="_blank" rel="noopener"
                   class="rounded-xl bg-gradient-to-r from-violet-600 to-blue-600 px-4 py-2 text-sm font-bold text-white shadow-lg shadow-violet-600/25 hover:from-violet-500 hover:to-blue-500">
                    Open full dashboard at BrandGEO ↗
                </a>
                <a href="{{ route('brandgeo-nova.dashboard', array_filter(['brand' => $brand->uuid, 'fresh' => 1, 'embedded' => $embedded])) }}"
                   class="text-[11px] text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300">⟳ Refresh data</a>
            </div>
        </div>
    </section>

    {{-- How to read every number on this page: the 0–100 bands, then what the
         two AI data modes mean and how their gap should be read. --}}
    @if ($audit || $overall !== null)
        <div class="mt-4">
            <x-brandgeo-nova::score-scale />
        </div>
    @endif

    @if ($webScore !== null && $trainedScore !== null)
        <div class="mt-3">
            <x-brandgeo-nova::mode-legend />
        </div>
    @endif

    @if ($paywalled)
        <section class="mt-5 rounded-2xl border border-amber-500/30 bg-amber-500/10 px-6 py-4 text-sm text-amber-800 dark:text-amber-200">
            🔒 <strong>Subscription required</strong> — this account's trial has expired, so detail data returns <code class="rounded bg-black/10 px-1 dark:bg-black/30">402</code>.
            <a href="{{ $appUrl }}/pricing" target="_blank" rel="noopener" class="font-bold underline">Upgrade at BrandGEO ↗</a>
        </section>
    @endif

    {{-- ==================== Monitoring — directly under the hero ==================== --}}
    @if (! $monitor && ! $paywalled)
        {{-- Monitoring is off for this brand — no empty widgets, just the pointer. --}}
        <section class="mt-6 flex flex-wrap items-center gap-3 rounded-2xl border border-zinc-200 bg-white px-6 py-4 dark:border-white/10 dark:bg-zinc-900/50">
            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-purple-500/10 text-lg">📡</span>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-bold">Monitoring is off for this brand</p>
                <p class="text-xs text-zinc-500">Enable weekly AI visibility tracking — mentions, share of voice, sentiment and citations across all 5 engines.</p>
            </div>
            <a href="{{ $appUrl }}/brands/{{ $brand->uuid }}/monitor" target="_blank" rel="noopener"
               class="shrink-0 rounded-xl bg-gradient-to-r from-purple-600 to-violet-600 px-4 py-2 text-xs font-bold text-white hover:from-purple-500 hover:to-violet-500">
                Enable in BrandGEO ↗
            </a>
        </section>
    @endif

    @if ($monitor)
        <section class="mt-6">
            <div class="mb-3 flex items-center gap-2">
                <span class="h-2 w-2 rounded-full bg-purple-400"></span>
                <h2 class="text-sm font-bold uppercase tracking-widest text-zinc-500 dark:text-zinc-400">Monitoring</h2>
                <span class="rounded-md bg-purple-500/10 px-2 py-0.5 text-[11px] font-bold text-purple-700 dark:text-purple-300">{{ $monitor->status->value }}</span>
                <span class="text-xs text-zinc-500">last run {{ $monitor->lastRunAt?->diffForHumans() ?? 'never' }}</span>
                <a href="{{ $appUrl }}/monitors/{{ $monitor->uuid }}" target="_blank" rel="noopener" class="ml-auto text-xs font-semibold text-violet-600 hover:underline dark:text-violet-400">Open monitor in BrandGEO ↗</a>
            </div>

            {{-- KPI row + trend --}}
            <div class="grid gap-4 lg:grid-cols-3">
                <div class="grid grid-cols-2 gap-3">
                    @foreach ([
                        ['Visibility', $snapshot?->visibilityScore !== null ? number_format($snapshot->visibilityScore, 1).'%' : '—', Presentation::score($snapshot?->visibilityScore)[0]],
                        ['Mentions', ($snapshot?->mentionCount ?? '—').' / '.($snapshot?->totalPrompts ?? '—'), 'text-zinc-900 dark:text-zinc-100'],
                        ['Avg position', $snapshot?->avgPosition !== null ? number_format($snapshot->avgPosition, 1) : '—', 'text-zinc-900 dark:text-zinc-100'],
                        ['Net sentiment', $snapshot?->sentiment?->netScore !== null ? number_format($snapshot->sentiment->netScore, 0) : '—', ($snapshot?->sentiment?->netScore ?? 0) >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400'],
                    ] as [$label, $value, $color])
                        <div class="rounded-2xl border border-zinc-200 bg-white p-4 dark:border-white/10 dark:bg-zinc-900/70">
                            <p class="text-[10px] font-semibold uppercase tracking-wider text-zinc-500">{{ $label }}</p>
                            <p class="mt-1 text-2xl font-extrabold tabular-nums {{ $color }}">{{ $value }}</p>
                        </div>
                    @endforeach
                </div>
                <div class="rounded-2xl border border-zinc-200 bg-white p-4 dark:border-white/10 dark:bg-zinc-900/70 lg:col-span-2">
                    <p class="mb-2 text-[10px] font-semibold uppercase tracking-wider text-zinc-500">
                        Visibility trend · {{ $trend?->daysApplied }}d window <span class="text-zinc-400 dark:text-zinc-600">(plan max {{ $trend?->daysMax }}d)</span>
                    </p>
                    @if ($trend)
                        <x-brandgeo-nova::trend-chart :trend="$trend" />
                    @endif
                </div>
            </div>

            @unless ($snapshot)
                <p class="mt-3 rounded-xl border border-zinc-200 bg-white px-4 py-3 text-xs text-zinc-500 dark:border-white/10 dark:bg-zinc-900/50">
                    No monitoring data yet — the first weekly run hasn't produced a snapshot. Results appear here automatically after the next scheduled run (Mondays).
                </p>
            @endunless

            {{-- Share of voice + competitors + citations --}}
            @if ($snapshot)
                <div class="mt-4 grid gap-4 lg:grid-cols-2">
                    <div class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-white/10 dark:bg-zinc-900/70">
                        <p class="mb-3 text-[10px] font-bold uppercase tracking-widest text-zinc-500">Share of voice</p>
                        <x-brandgeo-nova::share-of-voice :snapshot="$snapshot" :brand-name="$monitor->brandName" />
                        <div class="mt-5">
                            <p class="mb-2 text-[10px] font-bold uppercase tracking-widest text-zinc-500">Competitors</p>
                            <x-brandgeo-nova::competitor-table :snapshot="$snapshot" />
                        </div>
                    </div>
                    <div class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-white/10 dark:bg-zinc-900/70">
                        <p class="mb-2 text-[10px] font-bold uppercase tracking-widest text-zinc-500">Top cited sources</p>
                        <x-brandgeo-nova::citations :snapshot="$snapshot" />
                    </div>
                </div>
            @endif

            {{-- Category insights + custom queries --}}
            @if ($categoryInsights !== [] || $customQueries !== [])
                <div class="mt-4">
                    <p class="mb-2 text-[10px] font-bold uppercase tracking-widest text-zinc-500">Prompt category insights</p>
                    <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                        @foreach ($categoryInsights as $insight)
                            <x-brandgeo-nova::category-card :insight="$insight" />
                        @endforeach
                        @foreach ($customQueries as $custom)
                            <div class="rounded-2xl border border-violet-500/20 bg-violet-500/5 p-4">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <p class="text-sm font-bold">Custom query</p>
                                        <p class="mt-0.5 text-[11px] leading-snug text-zinc-500 dark:text-zinc-400">“{{ $custom['query'] }}”</p>
                                    </div>
                                    <span class="text-xl font-extrabold tabular-nums {{ Presentation::score($custom['rate'])[0] }}">{{ number_format($custom['rate'], 0) }}%</span>
                                </div>
                                <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">Appeared in {{ $custom['visible'] }} of {{ $custom['total'] }} answers</p>
                                <div class="mt-2 flex items-center gap-2">
                                    @foreach ($custom['providers'] as $provider => $info)
                                        <span class="flex items-center gap-1 text-[10px] {{ $info['mentioned'] ? 'text-zinc-600 dark:text-zinc-300' : 'text-zinc-400 dark:text-zinc-600' }}">
                                            <span class="h-2.5 w-2.5 rounded-full {{ $info['mentioned'] ? '' : 'opacity-20' }}" style="background: {{ Presentation::providerColor($provider) }}"></span>
                                            @if ($info['position'])#{{ $info['position'] }}@endif
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Recent AI answers --}}
            @if (! empty($monitoring['runs']))
                <div class="mt-4">
                    <p class="mb-2 text-[10px] font-bold uppercase tracking-widest text-zinc-500">Recent AI answers</p>
                    <div class="space-y-2">
                        @foreach (array_slice($monitoring['runs'], 0, 10) as $run)
                            <x-brandgeo-nova::run-row :run="$run" />
                        @endforeach
                    </div>
                </div>
            @endif
        </section>
    @endif

    {{-- ==================== Audit: dimensions overview ==================== --}}
    @if ($audit)
        <section class="mt-8">
            <div class="mb-3 flex items-center gap-2">
                <span class="h-2 w-2 rounded-full bg-blue-400"></span>
                <h2 class="text-sm font-bold uppercase tracking-widest text-zinc-500 dark:text-zinc-400">Latest audit · {{ $audit->createdAt?->format('M j, Y') }}</h2>
                <span class="rounded-md px-2 py-0.5 text-[11px] font-bold uppercase {{ $audit->isComplete() ? 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-300' : 'bg-zinc-500/10 text-zinc-500 dark:text-zinc-400' }}">{{ $audit->status->value }}</span>
                <a href="{{ $appUrl }}/audit/{{ $audit->uuid }}/details" target="_blank" rel="noopener" class="ml-auto text-xs font-semibold text-violet-600 hover:underline dark:text-violet-400">View full report in BrandGEO ↗</a>
            </div>

            <div class="grid grid-cols-2 gap-3 md:grid-cols-3 xl:grid-cols-6">
                @foreach (array_keys(Presentation::SECTIONS) as $shortKey)
                    <x-brandgeo-nova::dimension-tile
                        :short-key="$shortKey"
                        :trained="$dimensions['trained'][$shortKey]"
                        :web="$dimensions['web'][$shortKey]"
                        :show-web="$dimensions['hasWeb']" />
                @endforeach
            </div>

            {{-- Engines with drill-down — grouped by mode: Online (web search) first, then Offline (trained) --}}
            @php
                $providerOrder = array_keys(Presentation::PROVIDER_COLORS);
                $reports = collect($audit->reports ?? []);

                // Engines with results float to the top, best score first, and the
                // OTHER mode's section keeps the same engine order (each engine is
                // ranked by its best score across both modes), so an engine and its
                // counterpart sit in the same slot. Locked/unscored slots sink to
                // the bottom; the provider palette order only breaks ties.
                $bestScores = $reports
                    ->groupBy(fn ($report) => $report->provider->value)
                    ->map(fn ($group) => $group->max(fn ($report) => $report->isLocked() ? null : $report->normalizedScore));

                $modeGroups = $reports
                    ->sort(fn ($a, $b) => (($bestScores[$b->provider->value] ?? -1) <=> ($bestScores[$a->provider->value] ?? -1))
                        ?: (array_search($a->provider->value, $providerOrder) <=> array_search($b->provider->value, $providerOrder)))
                    ->values()
                    ->groupBy(fn ($report) => $report->mode->value)
                    ->sortKeysDesc(); // web_search before trained
            @endphp
            <div class="mt-4 space-y-6" x-data="{ open: null }">
                @foreach ($modeGroups as $mode => $reports)
                    <div>
                        <p class="mb-2 flex items-center gap-2 text-[11px] font-bold uppercase tracking-widest text-zinc-500 dark:text-zinc-400">
                            {{ $mode === 'web_search' ? '🌐 Online · Web Search AI Data' : '🧠 Offline · Trained AI Data' }}
                            <span class="font-semibold normal-case tracking-normal text-zinc-400 dark:text-zinc-600">— {{ $mode === 'web_search' ? 'what engines find live online' : 'what engines know from memory' }}</span>
                        </p>
                        <div class="space-y-2">
                            @foreach ($reports as $report)
                                @php
                                    $rowKey = $mode.'-'.$report->provider->value;
                                    $color = Presentation::providerColor($report->provider);
                                    [$scoreText, , , $scoreBand] = Presentation::score($report->normalizedScore);
                                @endphp
                                <div class="overflow-hidden rounded-2xl border bg-white transition dark:bg-zinc-900/70 {{ $report->isLocked() ? 'border-dashed border-zinc-300 dark:border-white/10' : 'border-zinc-200 hover:border-violet-500/50 dark:border-white/10' }}"
                                     :class="open === '{{ $rowKey }}' && 'border-violet-500/50'">
                                    <button
                                        @if (! $report->isLocked() && ! $report->isFailed()) @click="open = open === '{{ $rowKey }}' ? null : '{{ $rowKey }}'" @endif
                                        class="group flex w-full items-center gap-3 px-5 py-3 text-left {{ $report->isLocked() ? 'cursor-default opacity-70' : 'cursor-pointer hover:bg-zinc-50 dark:hover:bg-white/[0.03]' }}">
                                        <span class="h-3 w-3 shrink-0 rounded-full" style="background: {{ $color }}"></span>
                                        <span class="font-bold">{{ Presentation::providerLabel($report->provider) }}</span>
                                        @if ($report->isLocked())
                                            <span class="ml-auto flex items-center gap-1.5 text-xs text-zinc-500">🔒 Locked on this plan — <a href="{{ $appUrl }}/pricing" target="_blank" rel="noopener" class="font-semibold text-violet-600 hover:underline dark:text-violet-400">upgrade to unlock</a></span>
                                        @elseif ($report->isFailed())
                                            <span class="ml-auto text-xs text-red-600 dark:text-red-400">{{ $report->error }}</span>
                                        @else
                                            <span class="ml-auto text-xl font-extrabold tabular-nums {{ $scoreText }}">
                                                {{ $report->normalizedScore !== null ? number_format($report->normalizedScore, 1) : '—' }}@if ($report->normalizedScore !== null)<span class="text-xs font-bold text-zinc-400 dark:text-zinc-500">/100</span>@endif
                                            </span>
                                            {{-- Plain-language band instead of a letter grade: the 0–100
                                                 scale above already says everything an A–F would. --}}
                                            @if ($report->normalizedScore !== null)
                                                <span class="rounded-full bg-zinc-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider dark:bg-white/5 {{ $scoreText }}">{{ $scoreBand }}</span>
                                            @endif
                                            {{-- Explicit expand affordance --}}
                                            <span class="ml-2 flex items-center gap-1.5 rounded-lg border border-violet-500/30 bg-violet-500/10 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-violet-700 transition group-hover:border-violet-400 group-hover:bg-violet-500/20 dark:text-violet-300 dark:group-hover:text-violet-200">
                                                <span x-text="open === '{{ $rowKey }}' ? 'Hide details' : 'View details'">View details</span>
                                                <svg class="h-3 w-3 transition-transform" :class="open === '{{ $rowKey }}' && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                                            </span>
                                        @endif
                                    </button>
                                    @unless ($report->isLocked() || $report->isFailed())
                                        <div x-show="open === '{{ $rowKey }}'" x-cloak x-transition.opacity.duration.150ms>
                                            <x-brandgeo-nova::engine-detail :report="$report" />
                                        </div>
                                    @endunless
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- GEO Action Plan --}}
        @if ($audit->recommendations)
            <section class="mt-6 rounded-2xl border border-violet-500/20 bg-violet-500/5 p-5">
                <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-violet-700 dark:text-violet-300">
                    ✨ GEO Action Plan
                    @if ($audit->recommendations->isPreview())
                        <span class="rounded-full bg-amber-500/15 px-2 py-0.5 text-[10px] normal-case text-amber-700 dark:text-amber-300">preview · {{ $audit->recommendations->lockedActions }} actions locked</span>
                    @else
                        <span class="rounded-full bg-emerald-500/15 px-2 py-0.5 text-[10px] normal-case text-emerald-700 dark:text-emerald-300">full access</span>
                    @endif
                    @if ($audit->recommendations->overallScore !== null)
                        <span class="ml-2 text-sm font-extrabold text-violet-700 dark:text-violet-300">{{ $audit->recommendations->overallScore }}/10 AI readiness</span>
                    @endif
                    <a href="{{ $appUrl }}/audit/{{ $audit->uuid }}/recommendations" target="_blank" rel="noopener" class="ml-auto text-xs font-semibold normal-case text-violet-600 hover:underline dark:text-violet-400">Full action plan in BrandGEO ↗</a>
                </div>
                @if ($audit->recommendations->executiveSummary)
                    <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">{{ $audit->recommendations->executiveSummary }}</p>
                @endif
                <ul class="mt-3 grid gap-1.5 md:grid-cols-2">
                    @foreach (array_slice($audit->recommendations->actionPlan, 0, 8) as $action)
                        <li class="flex items-start gap-2 text-sm text-zinc-600 dark:text-zinc-300">
                            <span class="mt-0.5 rounded bg-violet-500/20 px-1.5 text-[10px] font-bold text-violet-700 dark:text-violet-300">{{ $action->priority?->value ?? '—' }}</span>
                            {{ $action->title }}
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif
    @endif

    {{-- ==================== Audit history ==================== --}}
    @if (count($audits) > 1)
        <section class="mt-8">
            <h2 class="mb-2 text-[10px] font-bold uppercase tracking-widest text-zinc-500">Audit history</h2>
            <div class="space-y-1.5">
                @foreach ($audits as $entry)
                    <a href="{{ $appUrl }}/audit/{{ $entry->uuid }}" target="_blank" rel="noopener"
                       class="flex items-center gap-3 rounded-xl border border-zinc-200 bg-white px-4 py-2 text-sm transition hover:border-blue-500/40 dark:border-white/10 dark:bg-zinc-900/50">
                        <span class="text-zinc-500 dark:text-zinc-400">{{ $entry->createdAt?->format('Y-m-d') }}</span>
                        <span class="rounded bg-zinc-100 px-1.5 py-0.5 text-[10px] font-semibold uppercase text-zinc-500 dark:bg-white/5">{{ $entry->status->value }}</span>
                        <span class="ml-auto font-bold tabular-nums {{ Presentation::score($entry->overallScore)[0] }}">
                            {{ $entry->overallScore !== null ? number_format($entry->overallScore, 1) : '—' }}@if ($entry->overallScore !== null)<span class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500">/100</span>@endif
                        </span>
                        <span class="text-zinc-400 dark:text-zinc-600">↗</span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
</x-brandgeo-nova::layout>
