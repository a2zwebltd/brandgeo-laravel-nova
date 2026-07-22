@props(['insight'])
@php
    use A2ZWeb\BrandGeoNova\Support\Presentation;

    [$rateText] = Presentation::score($insight['rate']);
    $label = ucwords(str_replace('_', ' ', $insight['category']));
@endphp
<div class="rounded-2xl border border-white/10 bg-zinc-900/70 p-4">
    <div class="flex items-start justify-between gap-2">
        <div>
            <p class="text-sm font-bold">{{ $label }}</p>
            <p class="mt-0.5 text-[11px] leading-snug text-zinc-500">{{ Presentation::CATEGORY_DESCRIPTIONS[$insight['category']] ?? '' }}</p>
        </div>
        <span class="text-xl font-extrabold tabular-nums {{ $rateText }}">{{ number_format($insight['rate'], 0) }}%</span>
    </div>
    <p class="mt-2 text-xs text-zinc-400">Appeared in {{ $insight['visible'] }} of {{ $insight['total'] }} answers</p>
    <div class="mt-2 flex items-center gap-2">
        @foreach ($insight['providers'] as $provider => $mentioned)
            <span class="h-2.5 w-2.5 rounded-full {{ $mentioned ? '' : 'opacity-20' }}"
                  style="background: {{ Presentation::providerColor($provider) }}"
                  title="{{ Presentation::providerLabel($provider) }}: {{ $mentioned ? 'brand surfaced' : 'not mentioned' }}"></span>
        @endforeach
        @if ($insight['sentiment'])
            <span class="ml-auto rounded bg-white/5 px-1.5 py-0.5 text-[10px] font-semibold uppercase text-zinc-400">{{ $insight['sentiment'] }}</span>
        @endif
    </div>
</div>
