<!DOCTYPE html>
<html lang="vi" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title inertia>{{ config('app.name', 'nhaiaptis') }}</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">

    @vite(['resources/js/app.js'])
    @inertiaHead
</head>
<body class="h-full font-sans antialiased bg-slate-50 text-slate-900">
    @inertia
</body>
</html>
