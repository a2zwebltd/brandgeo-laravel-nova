@props(['run'])
@php
    use A2ZWeb\BrandGeoNova\Support\Presentation;

    $sentimentColor = match ($run->sentiment) {
        'positive' => 'text-emerald-600 dark:text-emerald-400',
        'negative' => 'text-red-600 dark:text-red-400',
        default => 'text-zinc-500',
    };
@endphp
<details class="group rounded-xl border border-zinc-200 bg-white open:border-violet-500/30 dark:border-white/10 dark:bg-zinc-900/70">
    <summary class="flex cursor-pointer list-none items-center gap-3 px-4 py-2.5 text-sm [&::-webkit-details-marker]:hidden">
        <span class="h-2.5 w-2.5 shrink-0 rounded-full" style="background: {{ Presentation::providerColor($run->provider) }}"
              title="{{ Presentation::providerLabel($run->provider) }}"></span>
        <span class="min-w-0 flex-1 truncate text-zinc-600 dark:text-zinc-300">{{ $run->prompt }}</span>
        @if ($run->brandMentioned)
            <span class="shrink-0 rounded bg-emerald-500/10 px-1.5 py-0.5 text-[10px] font-bold text-emerald-700 dark:text-emerald-300">✓ mentioned{{ $run->brandPosition ? " #{$run->brandPosition}" : '' }}</span>
        @else
            <span class="shrink-0 rounded bg-zinc-100 px-1.5 py-0.5 text-[10px] font-semibold text-zinc-500 dark:bg-white/5">not mentioned</span>
        @endif
        @if ($run->sentiment)
            <span class="shrink-0 text-[10px] font-semibold uppercase {{ $sentimentColor }}">{{ $run->sentiment }}</span>
        @endif
        <span class="shrink-0 text-[10px] text-zinc-400 dark:text-zinc-600">{{ $run->executedAt?->format('M j') }}</span>
        <span class="shrink-0 text-zinc-400 transition group-open:rotate-90 dark:text-zinc-600">›</span>
    </summary>
    <div class="space-y-3 border-t border-zinc-200 px-4 py-3 text-sm dark:border-white/5">
        <div>
            <p class="text-[10px] font-bold uppercase tracking-wider text-zinc-500">Prompt · {{ Presentation::providerLabel($run->provider) }}</p>
            <p class="mt-1 text-zinc-600 dark:text-zinc-300">{{ $run->prompt }}</p>
        </div>
        @if ($run->response)
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-zinc-500">AI answer</p>
                <p class="mt-1 whitespace-pre-line text-xs leading-relaxed text-zinc-500 dark:text-zinc-400">{{ $run->response }}</p>
            </div>
        @endif
        @if ($run->citations)
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-zinc-500">Citations</p>
                <ul class="mt-1 space-y-0.5 text-xs">
                    @foreach (array_slice($run->citations, 0, 5) as $citation)
                        <li><a class="text-violet-600 hover:underline dark:text-violet-400" target="_blank" rel="noopener" href="{{ $citation['url'] ?? '#' }}">{{ $citation['domain'] ?? $citation['url'] ?? '—' }}</a></li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</details>
