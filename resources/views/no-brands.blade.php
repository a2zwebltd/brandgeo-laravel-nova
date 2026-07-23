<x-brandgeo-nova::layout title="BrandGEO — no brands yet">
    @php $appUrl = \A2ZWeb\BrandGeoNova\Support\Presentation::appUrl(); @endphp
    <div class="mx-auto mt-16 max-w-lg rounded-2xl border border-zinc-200 bg-white p-8 text-center dark:border-white/10 dark:bg-zinc-900/70">
        <p class="text-4xl">🛰️</p>
        <h1 class="mt-3 text-xl font-bold">No brands on this account yet</h1>
        <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Connected as {{ $account->email }} — run your first AI visibility audit in BrandGEO and it will show up here.</p>
        <a href="{{ $appUrl }}/dashboard" target="_blank" rel="noopener"
           class="mt-5 inline-block rounded-xl bg-gradient-to-r from-violet-600 to-blue-600 px-5 py-2.5 text-sm font-bold text-white hover:from-violet-500 hover:to-blue-500">
            Open BrandGEO Dashboard ↗
        </a>
    </div>
</x-brandgeo-nova::layout>
