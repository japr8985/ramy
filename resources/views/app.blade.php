<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>

    <!-- Metadatos de la PWA -->
    <meta name="theme-color" content="#0A0C13">
    <link rel="manifest" href="/build/manifest.webmanifest">
    <link rel="apple-touch-icon" href="/favicon/icon-192x192.png">

    @vite(['resources/css/app.css', 'resources/js/app.ts'])
</head>
<body>
    <div id="app"></div>
</body>
</html>