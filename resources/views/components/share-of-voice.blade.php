@props(['snapshot', 'brandName'])
@php
    use A2ZWeb\BrandGeoNova\Support\Presentation;

    // competitor_data: map "Name" => {mention_count, total_prompts, visibility_score}
    $entries = collect([['name' => $brandName, 'score' => (float) ($snapshot->visibilityScore ?? 0), 'color' => '#3b82f6', 'own' => true]]);

    foreach ((array) ($snapshot->competitors ?? []) as $name => $row) {
        $entries->push([
            'name' => is_string($name) ? $name : ($row['name'] ?? '—'),
            'score' => (float) ($row['visibility_score'] ?? 0),
            'color' => Presentation::SOV_PALETTE[($entries->count() - 1) % count(Presentation::SOV_PALETTE)],
            'own' => false,
        ]);
    }

    $total = max(1e-6, $entries->sum('score'));
@endphp
<div>
    <div class="flex h-4 w-full overflow-hidden rounded-full bg-white/5">
        @foreach ($entries as $entry)
            <div style="width: {{ $entry['score'] / $total * 100 }}%; background: {{ $entry['color'] }}"
                 title="{{ $entry['name'] }} · {{ number_format($entry['score'], 1) }}%"></div>
        @endforeach
    </div>
    <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1.5 text-xs">
        @foreach ($entries as $entry)
            <span class="flex items-center gap-1.5 {{ $entry['own'] ? 'font-bold' : 'text-zinc-400' }}">
                <span class="h-2.5 w-2.5 rounded-full" style="background: {{ $entry['color'] }}"></span>
                {{ $entry['name'] }} <span class="tabular-nums">{{ number_format($entry['score'], 1) }}%</span>
            </span>
        @endforeach
    </div>
</div>
