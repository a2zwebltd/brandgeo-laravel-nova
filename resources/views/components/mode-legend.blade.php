@php
    /**
     * Explains what "Offline Trained AI Data" vs "Online Web Search AI Data"
     * mean, and how to read the gap between them. Copy is kept verbatim in sync
     * with the BrandGEO app's audit.partials.mode-legend, so a client sees the
     * same explanation here and at brandgeo.co.
     */
@endphp
<div class="rounded-2xl border border-zinc-200 bg-zinc-100/60 px-6 py-5 dark:border-white/10 dark:bg-zinc-900/50">
    <div class="grid gap-4 sm:grid-cols-2">
        <div class="flex items-start gap-2.5">
            <span class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full bg-zinc-400 dark:bg-zinc-500"></span>
            <p class="text-xs leading-relaxed text-zinc-600 dark:text-zinc-300">
                <span class="font-semibold text-zinc-700 dark:text-zinc-200">Offline Trained AI Data</span> — what the AI already knows about your brand from its training data, with no internet access. This is the AI's memory.
            </p>
        </div>
        <div class="flex items-start gap-2.5">
            <span class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full bg-violet-500"></span>
            <p class="text-xs leading-relaxed text-zinc-600 dark:text-zinc-300">
                <span class="font-semibold text-violet-700 dark:text-violet-300">Online Web Search AI Data</span> — what the AI finds live on the web when it looks your brand up. This is closest to what real users see when they ask about you in chat.
            </p>
        </div>
    </div>

    {{-- How to read the gap between the two scores. --}}
    <div class="mt-4 flex items-start gap-2.5 border-t border-zinc-200/70 pt-4 dark:border-white/10">
        <svg class="mt-0.5 h-4 w-4 shrink-0 text-zinc-400 dark:text-zinc-500" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18m-7.5-3h15M6.75 6l-3.6 7.2a3.75 3.75 0 0 0 7.2 0L6.75 6Zm0 0 10.5-1.5m0 0 3.6 7.2a3.75 3.75 0 0 1-7.2 0l3.6-7.2Z" />
        </svg>
        <p class="text-xs leading-relaxed text-zinc-600 dark:text-zinc-300">
            <span class="font-semibold text-zinc-700 dark:text-zinc-200">The web–trained gap</span> is Online Web Search minus Offline Trained.
            A <span class="font-semibold text-emerald-600 dark:text-emerald-400">positive</span> gap means engines find your brand live but barely recall it from memory — earn mentions and authority on trusted sources to close it.
            A <span class="font-semibold text-red-600 dark:text-red-400">negative</span> gap means engines remember you but your live web footprint is thinner.
        </p>
    </div>
</div>
