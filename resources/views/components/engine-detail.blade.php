@props(['report'])
@php
    use A2ZWeb\BrandGeoNova\Support\Presentation;

    $result = $report->result ?? [];
    $analysis = $result['analysis'] ?? [];
    $metadata = $result['audit_metadata'] ?? [];
    $sectionScores = $result['scoring_summary']['section_scores'] ?? [];

    // Weakest-dimension callout (only when < 60), mirrors engine-panel.
    $weakest = collect($sectionScores)
        ->map(fn ($entry) => Presentation::sectionScore($entry))
        ->filter(fn ($score) => $score !== null)
        ->sort();
    $weakestKey = $weakest->keys()->first();
    $weakestScore = $weakest->first();
@endphp
<div class="space-y-5 border-t border-white/5 px-5 py-5">
    {{-- Key findings --}}
    @if ($report->findings)
        <div>
            <p class="mb-2 text-[10px] font-bold uppercase tracking-widest text-zinc-500">Key findings · {{ count($report->findings) }} insights</p>
            <div class="grid gap-2 md:grid-cols-2">
                @foreach ($report->findings as $finding)
                    <x-brandgeo-nova::finding :finding="$finding" />
                @endforeach
            </div>
        </div>
    @endif

    {{-- Weakest dimension callout --}}
    @if ($weakestKey !== null && $weakestScore < 60)
        <div class="flex items-center gap-2 rounded-xl border border-amber-500/30 bg-amber-500/10 px-4 py-2.5 text-sm text-amber-200">
            ⚠ Weakest dimension: <strong>{{ Presentation::SECTION_LABELS[$weakestKey] ?? $weakestKey }}</strong>
            — {{ number_format($weakestScore, 0) }}/100
        </div>
    @endif

    {{-- Metadata ribbon --}}
    @if ($metadata !== [])
        <div class="flex flex-wrap gap-2 text-[11px] text-zinc-500">
            @if (! empty($metadata['knowledge_cutoff']))
                <span class="rounded bg-white/5 px-2 py-1">Knowledge cutoff: <strong class="text-zinc-300">{{ $metadata['knowledge_cutoff'] }}</strong></span>
            @endif
            @if (! empty($metadata['data_source']))
                <span class="rounded bg-white/5 px-2 py-1">Data source: <strong class="text-zinc-300">{{ ucwords(str_replace('_', ' ', $metadata['data_source'])) }}</strong></span>
            @endif
            @if ($report->model)
                <span class="rounded bg-white/5 px-2 py-1">Model: <strong class="text-zinc-300">{{ $report->model }}</strong></span>
            @endif
        </div>
    @endif

    {{-- Analysis --}}
    @if (! empty($analysis['visibility_summary']))
        <p class="text-sm leading-relaxed text-zinc-300">{{ $analysis['visibility_summary'] }}</p>
    @endif

    @if (! empty($analysis['top_strengths']) || ! empty($analysis['top_weaknesses']) || ! empty($analysis['priority_actions']))
        <div class="grid gap-4 md:grid-cols-3">
            @if (! empty($analysis['top_strengths']))
                <div class="rounded-xl border border-emerald-500/20 bg-emerald-500/5 p-3">
                    <p class="mb-1.5 text-[10px] font-bold uppercase tracking-widest text-emerald-300">Strengths</p>
                    <ul class="space-y-1 text-xs text-zinc-300">
                        @foreach ($analysis['top_strengths'] as $item)
                            <li>• {{ is_array($item) ? ($item['detail'] ?? implode(' — ', array_filter($item, 'is_string'))) : $item }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if (! empty($analysis['top_weaknesses']))
                <div class="rounded-xl border border-red-500/20 bg-red-500/5 p-3">
                    <p class="mb-1.5 text-[10px] font-bold uppercase tracking-widest text-red-300">Areas for improvement</p>
                    <ul class="space-y-1 text-xs text-zinc-300">
                        @foreach ($analysis['top_weaknesses'] as $item)
                            <li>• {{ is_array($item) ? trim(($item['issue'] ?? '').(isset($item['recommendation']) ? ' — '.$item['recommendation'] : '')) : $item }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if (! empty($analysis['priority_actions']))
                <div class="rounded-xl border border-violet-500/20 bg-violet-500/5 p-3">
                    <p class="mb-1.5 text-[10px] font-bold uppercase tracking-widest text-violet-300">Priority actions</p>
                    <ol class="space-y-1 text-xs text-zinc-300">
                        @foreach ($analysis['priority_actions'] as $i => $action)
                            <li>{{ $i + 1 }}. {{ is_array($action) ? implode(' ', array_filter($action, 'is_string')) : $action }}</li>
                        @endforeach
                    </ol>
                </div>
            @endif
        </div>
    @endif

    {{-- Six dimensions with sub-fields --}}
    <div class="grid gap-4 md:grid-cols-2">
        @foreach (Presentation::SECTIONS as $shortKey => $fullKey)
            @php
                $section = $result[$fullKey] ?? null;
                $score = Presentation::sectionScore($sectionScores[$shortKey] ?? null);
                $accent = Presentation::SECTION_ACCENTS[$shortKey];
                [$scoreText] = Presentation::score($score);
            @endphp
            @if (is_array($section))
                <div class="rounded-xl border border-white/10 bg-white/[0.02] p-4">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-bold text-{{ $accent }}-300">{{ Presentation::SECTION_LABELS[$shortKey] }}</p>
                        <span class="text-sm font-extrabold tabular-nums {{ $scoreText }}">{{ $score !== null ? number_format($score, 0) : '—' }}<span class="text-[10px] text-zinc-600">/100</span></span>
                    </div>
                    <div class="mt-3 space-y-2.5">
                        @foreach ($section as $subKey => $sub)
                            @if (is_array($sub) && array_key_exists('score', $sub))
                                @php $confidence = strtoupper((string) ($sub['confidence'] ?? '')); @endphp
                                <div>
                                    <div class="flex items-center gap-2 text-[11px]">
                                        <span class="text-zinc-400">{{ Presentation::FIELD_LABELS[$subKey] ?? ucwords(str_replace('_', ' ', $subKey)) }}</span>
                                        @if (isset(Presentation::CONFIDENCE_CLASSES[$confidence]))
                                            <span class="rounded px-1 py-px text-[9px] font-bold {{ Presentation::CONFIDENCE_CLASSES[$confidence] }}">{{ $confidence }}</span>
                                        @endif
                                        <span class="ml-auto tabular-nums font-bold text-zinc-300">{{ (int) $sub['score'] }}</span>
                                    </div>
                                    <div class="mt-1 h-1 overflow-hidden rounded-full bg-white/5">
                                        <div class="h-full bg-{{ $accent }}-400" style="width: {{ min(100, max(0, (int) $sub['score'])) }}%"></div>
                                    </div>
                                    @php
                                        $answer = $sub['answer'] ?? null;
                                        $answerText = match (true) {
                                            is_bool($answer) => $answer ? 'Yes' : 'No',
                                            is_string($answer) => $answer,
                                            is_array($answer) => collect($answer)->map(fn ($a) => is_array($a) ? ($a['name'] ?? $a['detail'] ?? implode(' ', array_filter($a, 'is_string'))) : (string) $a)->take(6)->implode(', '),
                                            default => null,
                                        };
                                    @endphp
                                    @if ($answerText !== null && $answerText !== '')
                                        <p class="mt-1 text-[11px] leading-snug text-zinc-500">{{ \Illuminate\Support\Str::limit($answerText, 160) }}</p>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    {{-- Confidence distribution footnote --}}
    @php $distribution = collect($analysis['confidence_distribution'] ?? [])->filter(); @endphp
    @if ($distribution->sum() > 0)
        <p class="text-[11px] text-zinc-600">
            Confidence:
            @foreach ($distribution as $level => $count)
                <span class="mr-2">{{ strtoupper($level) }} × {{ $count }}</span>
            @endforeach
        </p>
    @endif

    {{-- Web sources --}}
    @if ($report->sources)
        <div>
            <p class="mb-1.5 text-[10px] font-bold uppercase tracking-widest text-zinc-500">Web sources ({{ count($report->sources) }})</p>
            <ul class="grid gap-1 text-xs md:grid-cols-2">
                @foreach (array_slice($report->sources, 0, 12) as $source)
                    @php $sourceUrl = is_array($source) ? ($source['url'] ?? null) : (is_string($source) ? $source : null); @endphp
                    <li class="truncate">
                        @if ($sourceUrl)
                            <a href="{{ $sourceUrl }}" target="_blank" rel="noopener" class="text-violet-400 hover:underline">{{ is_array($source) ? ($source['domain'] ?? $sourceUrl) : $sourceUrl }}</a>
                        @else
                            <span class="text-zinc-500">{{ is_array($source) ? implode(' ', array_filter($source, 'is_string')) : $source }}</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
