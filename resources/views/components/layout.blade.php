@props(['title' => 'BrandGEO — Visibility Dashboard'])
@php
    use A2ZWeb\BrandGeoNova\Support\Presentation;

    $embedded = request()->boolean('embedded');
    $appUrl = Presentation::appUrl();
    // Nova hands us its current colour scheme on the iframe URL; standalone
    // hits fall back to the stored Nova preference / the OS setting (below).
    $theme = in_array(request('theme'), ['dark', 'light'], true) ? request('theme') : null;
@endphp
<!DOCTYPE html>
<html lang="en" class="{{ $theme === 'dark' ? 'dark' : '' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    <script>
        // Resolve the colour scheme before paint, so a dark Nova never flashes
        // a white dashboard. Order: ?theme= → Nova's stored preference (same
        // origin, so its localStorage is readable) → the OS setting.
        (function () {
            var forced = @js($theme);

            if (! forced) {
                var stored = null;

                try {
                    stored = localStorage.getItem('nova.theme') || localStorage.getItem('theme');
                } catch (e) { /* storage blocked — fall through to the OS setting */ }

                forced = stored === 'dark' || stored === 'light'
                    ? stored
                    : (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            }

            document.documentElement.classList.toggle('dark', forced === 'dark');
        })();
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Class-based dark mode: the `dark` class on <html> is driven by Nova,
        // not by the OS, so the panel always matches the surrounding admin.
        tailwind.config = { darkMode: 'class' };
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; }
        .bg-grid {
            background-image: radial-gradient(circle at 1px 1px, rgb(139 92 246 / .10) 1px, transparent 0);
            background-size: 24px 24px;
        }
        .dark .bg-grid {
            background-image: radial-gradient(circle at 1px 1px, rgb(139 92 246 / .12) 1px, transparent 0);
        }
        [x-cloak] { display: none !important; }
    </style>
    <script>
        // Nova toggles its theme without reloading the iframe, so follow the
        // parent's live updates too (see dist/tool.js for the sender).
        window.addEventListener('message', function (event) {
            if (event.data && (event.data.brandgeoTheme === 'dark' || event.data.brandgeoTheme === 'light')) {
                document.documentElement.classList.toggle('dark', event.data.brandgeoTheme === 'dark');
            }
        });
    </script>
    @if ($embedded)
        {{-- Kill viewport-relative heights or the parent/iframe resize loop never converges. --}}
        <style>
            html, body { overflow: hidden; height: auto !important; min-height: 0 !important; }
        </style>
        <script>
            // Embedded in the Nova SPA iframe: report our content height to the
            // parent so the iframe auto-resizes and never shows scrollbars.
            (function () {
                var lastHeight = 0;
                var post = function () {
                    var height = document.body.scrollHeight;
                    if (Math.abs(height - lastHeight) > 2) {
                        lastHeight = height;
                        parent.postMessage({ brandgeoHeight: height }, '*');
                    }
                };
                window.addEventListener('load', function () {
                    post();
                    new ResizeObserver(post).observe(document.body);
                });
            })();
        </script>
    @endif
</head>
<body class="min-h-screen bg-zinc-50 text-zinc-900 antialiased dark:bg-zinc-950 dark:text-zinc-100">
    {{-- BrandGEO branded header --}}
    <header class="sticky top-0 z-40 border-b border-zinc-200 bg-gradient-to-r from-violet-50 via-white to-blue-50/90 backdrop-blur dark:border-white/10 dark:from-violet-950 dark:via-zinc-950 dark:to-blue-950/95">
        <div class="mx-auto flex max-w-7xl items-center gap-4 px-6 {{ $embedded ? 'py-2.5' : 'py-4' }}">
            <a href="{{ route('brandgeo-nova.dashboard', array_filter(['embedded' => request('embedded')])) }}" class="flex items-center">
                <img src="{{ route('brandgeo-nova.logo') }}" alt="BrandGEO" class="{{ $embedded ? 'h-7' : 'h-9' }} w-auto" />
            </a>
            @unless ($embedded)
                <span class="rounded-full border border-zinc-200 bg-white px-2.5 py-0.5 text-[11px] font-semibold uppercase tracking-widest text-zinc-500 dark:border-white/10 dark:bg-white/5 dark:text-zinc-400">Nova · Client Dashboard</span>
            @endunless
            <div class="ml-auto flex items-center gap-3 text-sm">
                {{ $headerRight ?? '' }}
                @unless ($embedded)
                    <a href="{{ url(config('nova.path', '/nova')) }}" class="text-zinc-500 transition hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white">← Back to Nova</a>
                @endunless
            </div>
        </div>
    </header>

    <main class="bg-grid mx-auto max-w-7xl px-6 py-6">
        @if (session('brandgeo-nova.status'))
            <div class="mb-6 flex items-center gap-3 rounded-2xl border border-emerald-500/30 bg-emerald-500/10 px-5 py-4 text-sm text-emerald-700 dark:text-emerald-300">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                {{ session('brandgeo-nova.status') }}
            </div>
        @endif

        {{ $slot }}
    </main>

    <footer class="mx-auto max-w-7xl px-6 pb-8 pt-4 text-center text-xs text-zinc-400 dark:text-zinc-600">
        Powered by <a href="https://brandgeo.co" class="font-semibold text-violet-600 hover:underline dark:text-violet-400">BrandGEO</a> — AI brand visibility monitoring · data via the BrandGEO API v1
    </footer>
</body>
</html>
