<x-brandgeo-nova::layout title="BrandGEO — error">
    <div class="mx-auto mt-16 max-w-lg rounded-2xl border border-red-500/30 bg-red-500/10 p-8 text-center">
        <p class="text-4xl">⚠️</p>
        <h1 class="mt-3 text-xl font-bold text-red-700 dark:text-red-300">Couldn't load BrandGEO data</h1>
        <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">{{ $message }}</p>
        <a href="{{ url(config('brandgeo-nova.path')) }}" class="mt-5 inline-block rounded-xl bg-zinc-900 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-700 dark:bg-white/10 dark:hover:bg-white/20">Retry</a>
    </div>
</x-brandgeo-nova::layout>
