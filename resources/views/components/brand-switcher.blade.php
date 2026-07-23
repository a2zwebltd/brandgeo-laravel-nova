@props(['brands', 'brand', 'isDefault' => false])
{{-- Styled as a select control — border, caret and hover state — so it reads as
     openable at a glance. The menu renders even for a single-brand account: it
     still carries that brand's URL and the "set as default" star. --}}
<div x-data="{ open: false }" class="relative flex items-center gap-2" @click.outside="open = false" @keydown.escape.window="open = false">
    <span class="text-[10px] font-semibold uppercase tracking-widest text-zinc-500">Select brand</span>
    <button @click="open = !open" type="button" :aria-expanded="open" aria-haspopup="listbox"
            class="group flex items-center gap-2 rounded-lg border border-zinc-300 bg-white px-3 py-1.5 text-sm font-semibold shadow-sm transition hover:border-violet-500/60 hover:bg-violet-50 focus:outline-none focus:ring-2 focus:ring-violet-500/40 dark:border-white/15 dark:bg-white/5 dark:hover:bg-white/10"
            :class="open && 'border-violet-500/60 ring-2 ring-violet-500/30'">
        <span class="h-2 w-2 rounded-full bg-gradient-to-r from-violet-400 to-blue-400"></span>
        {{ $brand->name }}
        @if ($isDefault)
            <span class="rounded bg-violet-500/15 px-1.5 py-0.5 text-[9px] font-bold uppercase text-violet-700 dark:text-violet-300">default</span>
        @endif
        @if (count($brands) > 1)
            <span class="rounded bg-zinc-100 px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wider text-zinc-500 dark:bg-white/10 dark:text-zinc-400">{{ count($brands) }} brands</span>
        @endif
        <svg class="h-3.5 w-3.5 shrink-0 text-zinc-400 transition-transform group-hover:text-violet-500 dark:text-zinc-500"
             :class="open && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
        </svg>
    </button>

    <div x-show="open" x-cloak x-transition role="listbox"
         class="absolute right-0 top-full z-50 mt-2 w-72 overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-2xl dark:border-white/10 dark:bg-zinc-900">
        <p class="border-b border-zinc-200 px-3 py-2 text-[10px] font-bold uppercase tracking-widest text-zinc-400 dark:border-white/5 dark:text-zinc-500">
            Switch brand · ★ sets the default
        </p>
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
</div>
