<x-brandgeo-nova::layout title="Connect BrandGEO">
    <div class="mx-auto mt-10 max-w-xl">
        <div class="overflow-hidden rounded-3xl border border-zinc-200 bg-white shadow-2xl shadow-violet-950/10 dark:border-white/10 dark:bg-zinc-900/80 dark:shadow-violet-950/50">
            <div class="bg-gradient-to-r from-violet-600 to-blue-600 px-8 py-6">
                <h1 class="text-2xl font-extrabold text-white">Connect your BrandGEO account</h1>
                <p class="mt-1 text-sm text-violet-100">Paste your API key to unlock brands, audits and monitoring data right inside Nova.</p>
            </div>

            <div class="space-y-5 px-8 py-7">
                @if ($error)
                    <div class="flex items-start gap-3 rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-700 dark:text-red-300">
                        <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" /></svg>
                        {{ $error }}
                    </div>
                @endif

                @error('api_key')
                    <div class="flex items-start gap-3 rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-700 dark:text-red-300">
                        <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" /></svg>
                        {{ $message }}
                    </div>
                @enderror

                <form method="POST" action="{{ route('brandgeo-nova.api-key.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="api_key" class="mb-1.5 block text-sm font-semibold text-zinc-700 dark:text-zinc-300">BrandGEO API key</label>
                        <input
                            id="api_key"
                            name="api_key"
                            type="password"
                            required
                            autocomplete="off"
                            value="{{ old('api_key') }}"
                            placeholder="1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
                            class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 font-mono text-sm text-zinc-900 placeholder-zinc-400 outline-none transition focus:border-violet-500 focus:ring-2 focus:ring-violet-500/30 dark:border-white/10 dark:bg-zinc-950 dark:text-zinc-100 dark:placeholder-zinc-600"
                        />
                        <p class="mt-1.5 text-xs text-zinc-500">
                            Generated at <span class="font-semibold text-zinc-600 dark:text-zinc-400">Settings → API</span> in your BrandGEO dashboard.
                            The key is validated against the live API before being saved to this app's <code class="rounded bg-zinc-100 px-1 dark:bg-white/5">.env</code>.
                        </p>
                    </div>

                    <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-violet-600 to-blue-600 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-violet-600/25 transition hover:from-violet-500 hover:to-blue-500">
                        Verify &amp; save key
                    </button>
                </form>

                <p class="text-center text-xs text-zinc-400 dark:text-zinc-600">
                    No account yet? <a href="https://brandgeo.co" class="font-semibold text-violet-600 hover:underline dark:text-violet-400">Run a free AI visibility audit at brandgeo.co →</a>
                </p>
            </div>
        </div>
    </div>
</x-brandgeo-nova::layout>
