@props(['trend'])
@php
    /** SVG area chart of overall visibility (points oldest-first). */
    $points = collect($trend->points)->filter(fn ($p) => $p->visibilityScore !== null)->values();
    $width = 720;
    $height = 160;
    $padX = 8;
    $padY = 12;

    $coords = $points->map(function ($p, $i) use ($points, $width, $height, $padX, $padY) {
        $x = $points->count() > 1
            ? $padX + ($width - 2 * $padX) * $i / ($points->count() - 1)
            : $width / 2;
        $y = $padY + ($height - 2 * $padY) * (1 - min(100, $p->visibilityScore) / 100);

        return [round($x, 1), round($y, 1)];
    });
    $polyline = $coords->map(fn ($c) => implode(',', $c))->implode(' ');
    $area = $coords->isNotEmpty()
        ? "{$padX},".($height - $padY).' '.$polyline.' '.($width - $padX).','.($height - $padY)
        : '';
@endphp
<div>
    @if ($points->isEmpty())
        <p class="py-8 text-center text-sm text-zinc-600">No snapshots in the current window yet.</p>
    @else
        <svg viewBox="0 0 {{ $width }} {{ $height }}" class="w-full" preserveAspectRatio="none" role="img" aria-label="Visibility trend">
            <defs>
                <linearGradient id="trendFill" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#8b5cf6" stop-opacity="0.35" />
                    <stop offset="100%" stop-color="#8b5cf6" stop-opacity="0" />
                </linearGradient>
            </defs>
            @foreach ([25, 50, 75] as $grid)
                <line x1="{{ $padX }}" x2="{{ $width - $padX }}"
                      y1="{{ $padY + ($height - 2 * $padY) * (1 - $grid / 100) }}"
                      y2="{{ $padY + ($height - 2 * $padY) * (1 - $grid / 100) }}"
                      stroke="rgba(255,255,255,.05)" stroke-width="1" />
            @endforeach
            <polygon points="{{ $area }}" fill="url(#trendFill)" />
            <polyline points="{{ $polyline }}" fill="none" stroke="#a78bfa" stroke-width="2.5" stroke-linejoin="round" stroke-linecap="round" />
            @foreach ($coords as $i => [$x, $y])
                <circle cx="{{ $x }}" cy="{{ $y }}" r="3.5" fill="#8b5cf6" stroke="#09090b" stroke-width="1.5">
                    <title>{{ $points[$i]->date?->format('Y-m-d') }} · {{ number_format($points[$i]->visibilityScore, 1) }}</title>
                </circle>
            @endforeach
        </svg>
        <div class="mt-1 flex justify-between text-[10px] text-zinc-600">
            <span>{{ $points->first()->date?->format('M j') }}</span>
            <span>{{ $points->last()->date?->format('M j') }}</span>
        </div>
    @endif
</div>
