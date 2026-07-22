@props(['finding'])
@php
    use A2ZWeb\BrandGeoNova\Support\Presentation;

    $sentiment = $finding['sentiment'] ?? 'neutral';
    [$border, $iconColor, $glyph] = Presentation::FINDING_SENTIMENT[$sentiment] ?? Presentation::FINDING_SENTIMENT['neutral'];
    $category = Presentation::FINDING_CATEGORY_LABELS[$finding['category'] ?? 'knowledge'] ?? 'Knowledge';
@endphp
<div class="rounded-xl border-l-2 {{ $border }} bg-white/[0.03] p-3">
    <div class="flex items-center gap-2">
        <span class="{{ $iconColor }} text-xs">{{ $glyph }}</span>
        <p class="text-sm font-bold">{{ $finding['title'] ?? '' }}</p>
        <span class="ml-auto rounded bg-white/5 px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wider text-zinc-500">{{ $category }}</span>
    </div>
    <p class="mt-1 text-xs leading-relaxed text-zinc-400">{{ $finding['description'] ?? '' }}</p>
</div>
