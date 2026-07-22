@props(['score', 'size' => 96, 'label' => null])
@php
    use A2ZWeb\BrandGeoNova\Support\Presentation;

    [, , $hex] = Presentation::score($score);
    $radius = 40;
    $circumference = 2 * M_PI * $radius;
    $dash = $score !== null ? $circumference * min(100, max(0, $score)) / 100 : 0;
@endphp
<div class="relative inline-flex items-center justify-center" style="width: {{ $size }}px; height: {{ $size }}px">
    <svg viewBox="0 0 100 100" class="h-full w-full -rotate-90">
        <circle cx="50" cy="50" r="{{ $radius }}" fill="none" stroke="rgba(255,255,255,.08)" stroke-width="9" />
        <circle cx="50" cy="50" r="{{ $radius }}" fill="none" stroke="{{ $hex }}" stroke-width="9" stroke-linecap="round"
                stroke-dasharray="{{ $dash }} {{ $circumference }}" />
    </svg>
    <div class="absolute inset-0 flex flex-col items-center justify-center">
        <span class="text-2xl font-extrabold tabular-nums" style="color: {{ $hex }}">{{ $score !== null ? number_format($score, 1) : '—' }}</span>
        @if ($label)
            <span class="text-[9px] font-semibold uppercase tracking-widest text-zinc-500">{{ $label }}</span>
        @endif
    </div>
</div>
