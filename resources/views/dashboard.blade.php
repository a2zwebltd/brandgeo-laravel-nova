@php
    use A2ZWeb\BrandGeoClient\Enums\AuditMode;
    use A2ZWeb\BrandGeoClient\Enums\SubscriptionStatus;
    use A2ZWeb\BrandGeoNova\Support\Presentation;

    $sub = $account->subscription;
    $appUrl = Presentation::appUrl();
    $embedded = request('embedded');

    $planBadge = match ($sub->status) {
        SubscriptionStatus::Trial => ['Trial', 'bg-amber-500/15 text-amber-300 border-amber-500/30'],
        SubscriptionStatus::Free => ['Free · full access', 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30'],
        SubscriptionStatus::Active => ['Paid · '.($sub->plan ?? 'subscribed'), 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30'],
        SubscriptionStatus::Expired => ['Trial expired', 'bg-red-500/15 text-red-300 border-red-500/30'],
    };

    $monitor = $monitoring['monitor'] ?? null;
    $snapshot = $monitor?->latestSnapshot;
    $trend = $monitoring['trend'] ?? null;
    $overall = $audit?->overallScore ?? $brand->latestAudit?->overallScore;
@endphp

<x-brandgeo-nova::layout :title="'BrandGEO — '.$brand->name">
    <x-slot:headerRight>
        <span class="rounded-full border px-3 py-1 text-xs font-bold {{ $planBadge[1] }}">{{ $planBadge[0] }}</span>
        <x-brandgeo-nova::brand-switcher :brands="$brands" :brand="$brand" :is-default="$isDefault" />
    </x-slot:headerRight>

    {{-- ============================ Brand hero ============================ --}}
    <section class="rounded-3xl border border-white/10 bg-gradient-to-br from-zinc-900 to-zinc-950 p-6">
        <div class="flex flex-wrap items-center gap-6">
            <x-brandgeo-nova::score-ring :score="$overall" :size="104" label="AI visibility" />
            <div class="min-w-0 flex-1">
                <h1 class="truncate text-2xl font-extrabold">{{ $brand->name }}</h1>
                <p class="mt-0.5 truncate text-sm text-zinc-400">
                    <a href="{{ $brand->url }}" target="_blank" rel="noopener" class="hover:underline">{{ $brand->url }}</a>
                    @if ($brand->industry) · {{ $brand->industry }} @endif
                </p>
                <div class="mt-2 flex flex-wrap items-center gap-2 text-[11px] font-semibold">
                    @if ($brand->latestAudit)
                        <span class="rounded-md bg-blue-500/10 px-2 py-1 text-blue-300">audit · {{ $brand->latestAudit->status->value }}</span>
                    @endif
                    @if ($brand->monitor)
                        <span class="rounded-md bg-purple-500/10 px-2 py-1 text-purple-300">monitor · {{ $brand->monitor->status->value }}</span>
                    @endif
                    @if ($sub->onTrial)
                        <span class="rounded-md bg-amber-500/10 px-2 py-1 text-amber-300">⏳ {{ $sub->trialDaysRemaining }} trial days left — engines beyond Gemini locked</span>
                    @endif
                </div>
            </div>
            <div class="flex shrink-0 flex-col items-end gap-2">
                <a href="{{ $appUrl }}/brands/{{ $brand->uuid }}" target="_blank" rel="noopener"
                   class="rounded-xl bg-gradient-to-r from-violet-600 to-blue-600 px-4 py-2 text-sm font-bold text-white shadow-lg shadow-violet-600/25 hover:from-violet-500 hover:to-blue-500">
                    Open full dashboard at BrandGEO ↗
                </a>
                <a href="{{ route('brandgeo-nova.dashboard', array_filter(['brand' => $brand->uuid, 'fresh' => 1, 'embedded' => $embedded])) }}"
                   class="text-[11px] text-zinc-500 hover:text-zinc-300">⟳ Refresh data</a>
            </div>
        </div>
    </section>

    @if ($paywalled)
        <section class="mt-5 rounded-2xl border border-amber-500/30 bg-amber-500/10 px-6 py-4 text-sm text-amber-200">
            🔒 <strong>Subscription required</strong> — this account's trial has expired, so detail data returns <code class="rounded bg-black/30 px-1">402</code>.
            <a href="{{ $appUrl }}/pricing" target="_blank" rel="noopener" class="font-bold underline">Upgrade at BrandGEO ↗</a>
        </section>
    @endif

    {{-- ==================== Monitoring — directly under the hero ==================== --}}
    @if (! $monitor && ! $paywalled)
        {{-- Monitoring is off for this brand — no empty widgets, just the pointer. --}}
        <section class="mt-6 flex flex-wrap items-center gap-3 rounded-2xl border border-white/10 bg-zinc-900/50 px-6 py-4">
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
                <h2 class="text-sm font-bold uppercase tracking-widest text-zinc-400">Monitoring</h2>
                <span class="rounded-md bg-purple-500/10 px-2 py-0.5 text-[11px] font-bold text-purple-300">{{ $monitor->status->value }}</span>
                <span class="text-xs text-zinc-500">last run {{ $monitor->lastRunAt?->diffForHumans() ?? 'never' }}</span>
                <a href="{{ $appUrl }}/monitors/{{ $monitor->uuid }}" target="_blank" rel="noopener" class="ml-auto text-xs font-semibold text-violet-400 hover:underline">Open monitor in BrandGEO ↗</a>
            </div>

            {{-- KPI row + trend --}}
            <div class="grid gap-4 lg:grid-cols-3">
                <div class="grid grid-cols-2 gap-3">
                    @foreach ([
                        ['Visibility', $snapshot?->visibilityScore !== null ? number_format($snapshot->visibilityScore, 1) : '—', Presentation::score($snapshot?->visibilityScore)[0]],
                        ['Mentions', ($snapshot?->mentionCount ?? '—').' / '.($snapshot?->totalPrompts ?? '—'), 'text-zinc-100'],
                        ['Avg position', $snapshot?->avgPosition !== null ? number_format($snapshot->avgPosition, 1) : '—', 'text-zinc-100'],
                        ['Net sentiment', $snapshot?->sentiment?->netScore !== null ? number_format($snapshot->sentiment->netScore, 0) : '—', ($snapshot?->sentiment?->netScore ?? 0) >= 0 ? 'text-emerald-400' : 'text-red-400'],
                    ] as [$label, $value, $color])
                        <div class="rounded-2xl border border-white/10 bg-zinc-900/70 p-4">
                            <p class="text-[10px] font-semibold uppercase tracking-wider text-zinc-500">{{ $label }}</p>
                            <p class="mt-1 text-2xl font-extrabold tabular-nums {{ $color }}">{{ $value }}</p>
                        </div>
                    @endforeach
                </div>
                <div class="rounded-2xl border border-white/10 bg-zinc-900/70 p-4 lg:col-span-2">
                    <p class="mb-2 text-[10px] font-semibold uppercase tracking-wider text-zinc-500">
                        Visibility trend · {{ $trend?->daysApplied }}d window <span class="text-zinc-600">(plan max {{ $trend?->daysMax }}d)</span>
                    </p>
                    @if ($trend)
                        <x-brandgeo-nova::trend-chart :trend="$trend" />
                    @endif
                </div>
            </div>

            @unless ($snapshot)
                <p class="mt-3 rounded-xl border border-white/10 bg-zinc-900/50 px-4 py-3 text-xs text-zinc-500">
                    No monitoring data yet — the first weekly run hasn't produced a snapshot. Results appear here automatically after the next scheduled run (Mondays).
                </p>
            @endunless

            {{-- Share of voice + competitors + citations --}}
            @if ($snapshot)
                <div class="mt-4 grid gap-4 lg:grid-cols-2">
                    <div class="rounded-2xl border border-white/10 bg-zinc-900/70 p-5">
                        <p class="mb-3 text-[10px] font-bold uppercase tracking-widest text-zinc-500">Share of voice</p>
                        <x-brandgeo-nova::share-of-voice :snapshot="$snapshot" :brand-name="$monitor->brandName" />
                        <div class="mt-5">
                            <p class="mb-2 text-[10px] font-bold uppercase tracking-widest text-zinc-500">Competitors</p>
                            <x-brandgeo-nova::competitor-table :snapshot="$snapshot" />
                        </div>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-zinc-900/70 p-5">
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
                                        <p class="mt-0.5 text-[11px] leading-snug text-zinc-400">“{{ $custom['query'] }}”</p>
                                    </div>
                                    <span class="text-xl font-extrabold tabular-nums {{ Presentation::score($custom['rate'])[0] }}">{{ number_format($custom['rate'], 0) }}%</span>
                                </div>
                                <p class="mt-2 text-xs text-zinc-400">Appeared in {{ $custom['visible'] }} of {{ $custom['total'] }} answers</p>
                                <div class="mt-2 flex items-center gap-2">
                                    @foreach ($custom['providers'] as $provider => $info)
                                        <span class="flex items-center gap-1 text-[10px] {{ $info['mentioned'] ? 'text-zinc-300' : 'text-zinc-600' }}">
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
                <h2 class="text-sm font-bold uppercase tracking-widest text-zinc-400">Latest audit · {{ $audit->createdAt?->format('M j, Y') }}</h2>
                <span class="rounded-md px-2 py-0.5 text-[11px] font-bold uppercase {{ $audit->isComplete() ? 'bg-emerald-500/10 text-emerald-300' : 'bg-zinc-500/10 text-zinc-400' }}">{{ $audit->status->value }}</span>
                <a href="{{ $appUrl }}/audit/{{ $audit->uuid }}/details" target="_blank" rel="noopener" class="ml-auto text-xs font-semibold text-violet-400 hover:underline">View full report in BrandGEO ↗</a>
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
                $modeGroups = collect($audit->reports ?? [])
                    ->sortBy(fn ($report) => array_search($report->provider->value, $providerOrder))
                    ->groupBy(fn ($report) => $report->mode->value)
                    ->sortKeysDesc(); // web_search before trained
            @endphp
            <div class="mt-4 space-y-6" x-data="{ open: null }">
                @foreach ($modeGroups as $mode => $reports)
                    <div>
                        <p class="mb-2 flex items-center gap-2 text-[11px] font-bold uppercase tracking-widest text-zinc-400">
                            {{ $mode === 'web_search' ? '🌐 Online · Web Search AI Data' : '🧠 Offline · Trained AI Data' }}
                            <span class="font-semibold normal-case tracking-normal text-zinc-600">— {{ $mode === 'web_search' ? 'what engines find live online' : 'what engines know from memory' }}</span>
                        </p>
                        <div class="space-y-2">
                            @foreach ($reports as $report)
                                @php
                                    $rowKey = $mode.'-'.$report->provider->value;
                                    $color = Presentation::providerColor($report->provider);
                                    [$scoreText] = Presentation::score($report->normalizedScore);
                                @endphp
                                <div class="overflow-hidden rounded-2xl border bg-zinc-900/70 transition {{ $report->isLocked() ? 'border-dashed border-white/10' : 'border-white/10 hover:border-violet-500/50' }}"
                                     :class="open === '{{ $rowKey }}' && 'border-violet-500/50'">
                                    <button
                                        @if (! $report->isLocked() && ! $report->isFailed()) @click="open = open === '{{ $rowKey }}' ? null : '{{ $rowKey }}'" @endif
                                        class="group flex w-full items-center gap-3 px-5 py-3 text-left {{ $report->isLocked() ? 'cursor-default opacity-70' : 'cursor-pointer hover:bg-white/[0.03]' }}">
                                        <span class="h-3 w-3 shrink-0 rounded-full" style="background: {{ $color }}"></span>
                                        <span class="font-bold">{{ Presentation::providerLabel($report->provider) }}</span>
                                        @if ($report->isLocked())
                                            <span class="ml-auto flex items-center gap-1.5 text-xs text-zinc-500">🔒 Locked on this plan — <a href="{{ $appUrl }}/pricing" target="_blank" rel="noopener" class="font-semibold text-violet-400 hover:underline">upgrade to unlock</a></span>
                                        @elseif ($report->isFailed())
                                            <span class="ml-auto text-xs text-red-400">{{ $report->error }}</span>
                                        @else
                                            <span class="ml-auto text-xl font-extrabold tabular-nums {{ $scoreText }}">{{ $report->normalizedScore !== null ? number_format($report->normalizedScore, 1) : '—' }}</span>
                                            <span class="text-xs font-bold text-zinc-500">{{ $report->grade }}</span>
                                            {{-- Explicit expand affordance --}}
                                            <span class="ml-2 flex items-center gap-1.5 rounded-lg border border-violet-500/30 bg-violet-500/10 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-violet-300 transition group-hover:border-violet-400 group-hover:bg-violet-500/20 group-hover:text-violet-200">
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
                <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-violet-300">
                    ✨ GEO Action Plan
                    @if ($audit->recommendations->isPreview())
                        <span class="rounded-full bg-amber-500/15 px-2 py-0.5 text-[10px] normal-case text-amber-300">preview · {{ $audit->recommendations->lockedActions }} actions locked</span>
                    @else
                        <span class="rounded-full bg-emerald-500/15 px-2 py-0.5 text-[10px] normal-case text-emerald-300">full access</span>
                    @endif
                    @if ($audit->recommendations->overallScore !== null)
                        <span class="ml-2 text-sm font-extrabold text-violet-300">{{ $audit->recommendations->overallScore }}/10 AI readiness</span>
                    @endif
                    <a href="{{ $appUrl }}/audit/{{ $audit->uuid }}/recommendations" target="_blank" rel="noopener" class="ml-auto text-xs font-semibold normal-case text-violet-400 hover:underline">Full action plan in BrandGEO ↗</a>
                </div>
                @if ($audit->recommendations->executiveSummary)
                    <p class="mt-2 text-sm text-zinc-300">{{ $audit->recommendations->executiveSummary }}</p>
                @endif
                <ul class="mt-3 grid gap-1.5 md:grid-cols-2">
                    @foreach (array_slice($audit->recommendations->actionPlan, 0, 8) as $action)
                        <li class="flex items-start gap-2 text-sm text-zinc-300">
                            <span class="mt-0.5 rounded bg-violet-500/20 px-1.5 text-[10px] font-bold text-violet-300">{{ $action->priority?->value ?? '—' }}</span>
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
                       class="flex items-center gap-3 rounded-xl border border-white/10 bg-zinc-900/50 px-4 py-2 text-sm transition hover:border-blue-500/40">
                        <span class="text-zinc-400">{{ $entry->createdAt?->format('Y-m-d') }}</span>
                        <span class="rounded bg-white/5 px-1.5 py-0.5 text-[10px] font-semibold uppercase text-zinc-500">{{ $entry->status->value }}</span>
                        <span class="ml-auto font-bold tabular-nums {{ Presentation::score($entry->overallScore)[0] }}">{{ $entry->overallScore !== null ? number_format($entry->overallScore, 1) : '—' }}</span>
                        <span class="text-zinc-600">↗</span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
</x-brandgeo-nova::layout>
