@props(['score', 'size' => 96, 'label' => null])
@php
    use A2ZWeb\BrandGeoNova\Support\Presentation;

    [$textClass] = Presentation::score($score);
    $radius = 40;
    $circumference = 2 * M_PI * $radius;
    $dash = $score !== null ? $circumference * min(100, max(0, $score)) / 100 : 0;
@endphp
{{-- The arc inherits the band colour from currentColor, so one class covers
     both themes (raw stroke="#hex" can't carry a dark: variant). --}}
<div class="relative inline-flex items-center justify-center {{ $textClass }}" style="width: {{ $size }}px; height: {{ $size }}px">
    <svg viewBox="0 0 100 100" class="h-full w-full -rotate-90">
        <circle cx="50" cy="50" r="{{ $radius }}" fill="none" stroke-width="9" class="stroke-zinc-200 dark:stroke-white/10" />
        <circle cx="50" cy="50" r="{{ $radius }}" fill="none" stroke="currentColor" stroke-width="9" stroke-linecap="round"
                stroke-dasharray="{{ $dash }} {{ $circumference }}" />
    </svg>
    <div class="absolute inset-0 flex flex-col items-center justify-center">
        <span class="text-xl font-extrabold tabular-nums">
            {{ $score !== null ? number_format($score, 1) : '—' }}@if ($score !== null)<span class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500">/100</span>@endif
        </span>
        @if ($label)
            <span class="text-[9px] font-semibold uppercase tracking-widest text-zinc-500">{{ $label }}</span>
        @endif
    </div>
</div>
