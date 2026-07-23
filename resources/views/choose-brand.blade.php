<x-brandgeo-nova::layout title="Choose your brand — BrandGEO">
    <div class="mx-auto mt-8 max-w-2xl">
        <div class="mb-6 text-center">
            <h1 class="text-2xl font-extrabold">Choose your default brand</h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Connected as {{ $account->email }} — pick which brand this dashboard should open with.</p>
        </div>

        <div class="space-y-3">
            @foreach ($brands as $brand)
                <div class="flex items-center gap-4 rounded-2xl border border-zinc-200 bg-white p-5 transition hover:border-violet-500/40 dark:border-white/10 dark:bg-zinc-900/70">
                    <div class="min-w-0 flex-1">
                        <p class="truncate font-bold">{{ $brand->name }}</p>
                        <p class="truncate text-xs text-zinc-500">{{ $brand->url }}@if($brand->industry) · {{ $brand->industry }}@endif</p>
                    </div>
                    @if ($brand->latestAudit?->overallScore !== null)
                        <x-brandgeo-nova::score-ring :score="$brand->latestAudit->overallScore" :size="56" />
                    @endif
                    <div class="flex shrink-0 flex-col gap-2">
                        <form method="POST" action="{{ route('brandgeo-nova.default-brand.store') }}">
                            @csrf
                            <input type="hidden" name="brand" value="{{ $brand->uuid }}">
                            <input type="hidden" name="embedded" value="{{ request('embedded') }}">
                            <button type="submit" class="w-full rounded-lg bg-gradient-to-r from-violet-600 to-blue-600 px-4 py-1.5 text-xs font-bold text-white hover:from-violet-500 hover:to-blue-500">
                                Set as default
                            </button>
                        </form>
                        <a href="{{ route('brandgeo-nova.dashboard', array_filter(['brand' => $brand->uuid, 'embedded' => request('embedded')])) }}"
                           class="rounded-lg border border-zinc-200 px-4 py-1.5 text-center text-xs font-semibold text-zinc-600 hover:bg-zinc-50 dark:border-white/10 dark:text-zinc-300 dark:hover:bg-white/5">
                            View once
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-brandgeo-nova::layout>
