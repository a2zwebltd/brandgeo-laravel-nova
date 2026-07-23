@props(['snapshot'])
@php
    $citations = collect((array) ($snapshot->topCitations ?? []))->take(10);
@endphp
<ul class="divide-y divide-zinc-200 dark:divide-white/5">
    @forelse ($citations as $citation)
        <li class="flex items-center gap-3 py-2">
            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-md bg-zinc-100 text-[10px] font-bold text-zinc-500 dark:bg-white/5 dark:text-zinc-400">{{ $loop->iteration }}</span>
            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-semibold">{{ $citation['domain'] ?? $citation['url'] ?? '—' }}</p>
                @if (! empty($citation['context']))
                    <p class="truncate text-xs text-zinc-500">{{ \Illuminate\Support\Str::limit($citation['context'], 80) }}</p>
                @endif
            </div>
            @if (! empty($citation['url']))
                <a href="{{ $citation['url'] }}" target="_blank" rel="noopener" class="shrink-0 text-xs font-semibold text-violet-600 hover:underline dark:text-violet-400">Visit ↗</a>
            @endif
        </li>
    @empty
        <li class="py-3 text-center text-sm text-zinc-400 dark:text-zinc-600">No citations captured yet.</li>
    @endforelse
</ul>
