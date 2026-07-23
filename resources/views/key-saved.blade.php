<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>BrandGEO — key saved</title>
    <script>
        // Same theme resolve as the layout, so this interstitial matches Nova.
        (function () {
            var forced = new URLSearchParams(location.search).get('theme');

            if (forced !== 'dark' && forced !== 'light') {
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
    <script>tailwind.config = { darkMode: 'class' };</script>
</head>
<body class="flex min-h-screen items-center justify-center bg-zinc-50 text-zinc-900 dark:bg-zinc-950 dark:text-zinc-100">
    <div class="text-center">
        <div class="mx-auto h-10 w-10 animate-spin rounded-full border-2 border-violet-500 border-t-transparent"></div>
        <p class="mt-4 text-sm font-semibold">API key saved — loading your BrandGEO data…</p>
    </div>
    <script>
        (function () {
            var target = @js($target);

            if (window.top !== window.self) {
                // Embedded in the Nova SPA iframe: reload the whole page so the
                // tool boots fresh and the iframe starts with the new key.
                window.top.location.reload();
            } else {
                window.location.replace(target);
            }
        })();
    </script>
</body>
</html>
