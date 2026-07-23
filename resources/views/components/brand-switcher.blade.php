@props(['brands', 'brand', 'isDefault' => false])
<div x-data="{ open: false }" class="relative flex items-center gap-2" @click.outside="open = false">
    <span class="text-[10px] font-semibold uppercase tracking-widest text-zinc-500">Select brand</span>
    <button @click="open = !open"
            class="flex items-center gap-2 rounded-lg border border-zinc-200 bg-white px-3 py-1.5 text-sm font-semibold transition hover:border-violet-500/40 dark:border-white/10 dark:bg-white/5">
        <span class="h-2 w-2 rounded-full bg-gradient-to-r from-violet-400 to-blue-400"></span>
        {{ $brand->name }}
        @if ($isDefault)
            <span class="rounded bg-violet-500/15 px-1.5 py-0.5 text-[9px] font-bold uppercase text-violet-700 dark:text-violet-300">default</span>
        @endif
        @if (count($brands) > 1)
            <span class="text-zinc-500">▾</span>
        @endif
    </button>

    @if (count($brands) > 1)
        <div x-show="open" x-cloak x-transition
             class="absolute right-0 top-full z-50 mt-2 w-72 overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-2xl dark:border-white/10 dark:bg-zinc-900">
            @foreach ($brands as $option)
                <div class="flex items-center gap-2 border-b border-zinc-200 px-3 py-2 last:border-0 dark:border-white/5 {{ $option->uuid === $brand->uuid ? 'bg-violet-500/10' : 'hover:bg-zinc-50 dark:hover:bg-white/5' }}">
                    <a class="min-w-0 flex-1"
                       href="{{ route('brandgeo-nova.dashboard', array_filter(['brand' => $option->uuid, 'embedded' => request('embedded')])) }}">
                        <p class="truncate text-sm font-semibold">{{ $option->name }}</p>
                        <p class="truncate text-[11px] text-zinc-500">{{ $option->url }}</p>
                    </a>
                    <form method="POST" action="{{ route('brandgeo-nova.default-brand.store') }}">
                        @csrf
                        <input type="hidden" name="brand" value="{{ $option->uuid }}">
                        <input type="hidden" name="embedded" value="{{ request('embedded') }}">
                        <button type="submit" class="rounded px-1.5 py-0.5 text-[10px] font-semibold text-zinc-400 hover:bg-zinc-100 hover:text-zinc-900 dark:hover:bg-white/10 dark:hover:text-white"
                                title="Set as default brand">★</button>
                    </form>
                </div>
            @endforeach
        </div>
    @endif
</div>
