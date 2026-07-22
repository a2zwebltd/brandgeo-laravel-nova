@props(['snapshot'])
@php
    use A2ZWeb\BrandGeoNova\Support\Presentation;

    $own = (float) ($snapshot->visibilityScore ?? 0);
    $rows = collect((array) ($snapshot->competitors ?? []))
        ->map(fn ($row, $name) => [
            'name' => is_string($name) ? $name : ($row['name'] ?? '—'),
            'mentions' => (int) ($row['mention_count'] ?? 0),
            'total' => (int) ($row['total_prompts'] ?? 0),
            'score' => (float) ($row['visibility_score'] ?? 0),
        ])
        ->sortByDesc('score')
        ->values();
@endphp
<table class="w-full text-sm">
    <thead>
        <tr class="text-left text-[10px] font-semibold uppercase tracking-wider text-zinc-500">
            <th class="pb-2">Competitor</th>
            <th class="pb-2">Mentions</th>
            <th class="pb-2 w-1/3">Visibility</th>
            <th class="pb-2 text-right">vs. your brand</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-white/5">
        @forelse ($rows as $row)
            @php
                $delta = $own - $row['score'];
                [$text, $bar] = Presentation::score($row['score']);
            @endphp
            <tr>
                <td class="py-2 font-semibold">{{ $row['name'] }}</td>
                <td class="py-2 text-zinc-400">{{ $row['mentions'] }} / {{ $row['total'] }}</td>
                <td class="py-2 pr-4">
                    <div class="flex items-center gap-2">
                        <div class="h-1.5 flex-1 overflow-hidden rounded-full bg-white/5">
                            <div class="h-full {{ $bar }}" style="width: {{ min(100, $row['score']) }}%"></div>
                        </div>
                        <span class="tabular-nums text-xs {{ $text }}">{{ number_format($row['score'], 1) }}%</span>
                    </div>
                </td>
                <td class="py-2 text-right tabular-nums font-semibold {{ $delta >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                    {{ $delta >= 0 ? '+' : '' }}{{ number_format($delta, 1) }}
                </td>
            </tr>
        @empty
            <tr><td colspan="4" class="py-3 text-center text-zinc-600">No competitor data in the latest snapshot.</td></tr>
        @endforelse
    </tbody>
</table>
