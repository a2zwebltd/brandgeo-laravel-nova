@props(['score', 'size' => 'md'])
@php
    use A2ZWeb\BrandGeoNova\Support\Presentation;

    [$tone] = Presentation::score($score);
    $text = $size === 'lg' ? 'text-4xl' : 'text-2xl';
    $suffix = $size === 'lg' ? 'text-base' : 'text-xs';
@endphp
<span class="font-extrabold tabular-nums {{ $tone }} {{ $text }}">
    {{ $score !== null ? number_format($score, 1) : '—' }}@if ($score !== null)<span class="{{ $suffix }} font-bold text-zinc-400 dark:text-zinc-500">/100</span>@endif
</span>
