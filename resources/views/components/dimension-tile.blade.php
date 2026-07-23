@props(['shortKey', 'trained', 'web' => null, 'showWeb' => false])
@php
    use A2ZWeb\BrandGeoNova\Support\Presentation;

    $accent = Presentation::SECTION_ACCENTS[$shortKey] ?? 'zinc';
    [$trainedText] = Presentation::score($trained);
    [$webText] = Presentation::score($web);
@endphp
<div class="rounded-2xl border border-zinc-200 bg-white p-4 dark:border-white/10 dark:bg-zinc-900/70" title="{{ Presentation::SECTION_DESCRIPTIONS[$shortKey] ?? '' }}">
    <div class="flex items-center justify-between">
        <p class="text-xs font-bold text-{{ $accent }}-600 dark:text-{{ $accent }}-300">{{ Presentation::SECTION_LABELS[$shortKey] ?? $shortKey }}</p>
        <span class="cursor-help text-[10px] text-zinc-400 dark:text-zinc-600">(?)</span>
    </div>

    <div class="mt-3 space-y-2">
        <div>
            <div class="flex items-baseline justify-between text-[10px] uppercase tracking-wider text-zinc-500">
                <span>Trained</span>
                <span class="text-sm font-extrabold tabular-nums {{ $trainedText }}">
                    {{ $trained !== null ? number_format($trained, 0) : '—' }}@if ($trained !== null)<span class="text-[9px] font-bold text-zinc-400 dark:text-zinc-600">/100</span>@endif
                </span>
            </div>
            <div class="mt-1 h-1.5 overflow-hidden rounded-full bg-zinc-200 dark:bg-white/5">
                <div class="h-full rounded-full bg-{{ $accent }}-500 dark:bg-{{ $accent }}-400" style="width: {{ (int) ($trained ?? 0) }}%"></div>
            </div>
        </div>
        @if ($showWeb)
            <div>
                <div class="flex items-baseline justify-between text-[10px] uppercase tracking-wider text-zinc-500">
                    <span>Web search</span>
                    <span class="text-sm font-extrabold tabular-nums {{ $webText }}">
                        {{ $web !== null ? number_format($web, 0) : '—' }}@if ($web !== null)<span class="text-[9px] font-bold text-zinc-400 dark:text-zinc-600">/100</span>@endif
                    </span>
                </div>
                <div class="mt-1 h-1.5 overflow-hidden rounded-full bg-zinc-200 dark:bg-white/5">
                    <div class="h-full rounded-full bg-violet-500 dark:bg-violet-400" style="width: {{ (int) ($web ?? 0) }}%"></div>
                </div>
            </div>
        @endif
    </div>
</div>
