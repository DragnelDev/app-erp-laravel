<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title inertia>{{ config('app.name', 'ERP') }}</title>

    <!-- Tema: aplica la preferencia guardada (o la del sistema) antes de pintar -->
    <script>
        (function() {
            try {
                var mode = localStorage.getItem('theme') || 'system';
                var dark = mode === 'dark' || (mode === 'system' && window.matchMedia('(prefers-color-scheme: dark)')
                    .matches);
                document.documentElement.classList.toggle('dark', dark);
                document.documentElement.style.colorScheme = dark ? 'dark' : 'light';
            } catch (e) {}
        })();
    </script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @routes
    @vite(['resources/js/app.ts', "resources/js/Pages/{$page['component']}.vue"])
    @inertiaHead
</head>

<body class="bg-slate-50 font-sans text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-100">
    @inertia
</body>

</html>
