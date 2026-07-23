@php
    /**
     * Always-visible 0–100 quality scale (Low → Excellent), so any score on the
     * page reads as good/bad at a glance. Bands, ranges and colours come from
     * Presentation so this can't drift from the scores themselves. Mirrors the
     * BrandGEO app's audit.partials.score-scale.
     */
    use A2ZWeb\BrandGeoNova\Support\Presentation;
@endphp
<div class="flex flex-wrap items-center gap-x-4 gap-y-2 rounded-xl border border-zinc-200 bg-zinc-100/60 px-4 py-2.5 dark:border-white/10 dark:bg-zinc-900/50">
    <span class="text-[11px] font-semibold uppercase tracking-wider text-zinc-500">Score scale · 0–100</span>
    @foreach (Presentation::SCORE_BANDS as $label => [$range, $mid])
        @php [$text, $bar] = Presentation::score((float) $mid); @endphp
        <span class="inline-flex items-center gap-1.5 text-[11px]">
            <span class="h-2 w-2 shrink-0 rounded-full {{ $bar }}"></span>
            <span class="font-semibold {{ $text }}">{{ $label }}</span>
            <span class="tabular-nums text-zinc-400 dark:text-zinc-500">{{ $range }}</span>
        </span>
    @endforeach
</div>
