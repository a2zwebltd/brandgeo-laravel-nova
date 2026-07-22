<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>BrandGEO — key saved</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex min-h-screen items-center justify-center bg-zinc-950 text-zinc-100">
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
