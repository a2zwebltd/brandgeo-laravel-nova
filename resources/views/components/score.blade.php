@props(['score', 'size' => 'md'])
@php
    // Mirrors BrandGEO's ScoreColors bands (A≥80 … F<20).
    $tone = match (true) {
        $score === null => 'text-zinc-500',
        $score >= 80 => 'text-emerald-400',
        $score >= 60 => 'text-green-400',
        $score >= 40 => 'text-amber-400',
        $score >= 20 => 'text-orange-400',
        default => 'text-red-400',
    };
    $text = $size === 'lg' ? 'text-4xl' : 'text-2xl';
@endphp
<span class="font-extrabold tabular-nums {{ $tone }} {{ $text }}">{{ $score !== null ? number_format($score, 1) : '—' }}</span>
