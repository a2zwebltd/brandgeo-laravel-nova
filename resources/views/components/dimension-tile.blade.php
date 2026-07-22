@props(['shortKey', 'trained', 'web' => null, 'showWeb' => false])
@php
    use A2ZWeb\BrandGeoNova\Support\Presentation;

    $accent = Presentation::SECTION_ACCENTS[$shortKey] ?? 'zinc';
    [$trainedText] = Presentation::score($trained);
    [$webText] = Presentation::score($web);
@endphp
<div class="rounded-2xl border border-white/10 bg-zinc-900/70 p-4" title="{{ Presentation::SECTION_DESCRIPTIONS[$shortKey] ?? '' }}">
    <div class="flex items-center justify-between">
        <p class="text-xs font-bold text-{{ $accent }}-300">{{ Presentation::SECTION_LABELS[$shortKey] ?? $shortKey }}</p>
        <span class="cursor-help text-[10px] text-zinc-600">(?)</span>
    </div>

    <div class="mt-3 space-y-2">
        <div>
            <div class="flex items-baseline justify-between text-[10px] uppercase tracking-wider text-zinc-500">
                <span>Trained</span>
                <span class="text-sm font-extrabold tabular-nums {{ $trainedText }}">{{ $trained !== null ? number_format($trained, 0) : '—' }}</span>
            </div>
            <div class="mt-1 h-1.5 overflow-hidden rounded-full bg-white/5">
                <div class="h-full rounded-full bg-{{ $accent }}-400" style="width: {{ (int) ($trained ?? 0) }}%"></div>
            </div>
        </div>
        @if ($showWeb)
            <div>
                <div class="flex items-baseline justify-between text-[10px] uppercase tracking-wider text-zinc-500">
                    <span>Web search</span>
                    <span class="text-sm font-extrabold tabular-nums {{ $webText }}">{{ $web !== null ? number_format($web, 0) : '—' }}</span>
                </div>
                <div class="mt-1 h-1.5 overflow-hidden rounded-full bg-white/5">
                    <div class="h-full rounded-full bg-violet-400" style="width: {{ (int) ($web ?? 0) }}%"></div>
                </div>
            </div>
        @endif
    </div>
</div>
