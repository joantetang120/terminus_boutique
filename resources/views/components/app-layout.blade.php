<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Terminus-Boutique' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
     
     <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/logo-blue.png') }}">

<link rel="icon" type="image/png" sizes="192x192" href="{{ asset('img/logo-blue.png') }}">

<link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
</head>
<body>
    <div class="app-layout">
        @include('layouts.partials.topbar')
        @include('layouts.partials.sidebar')
        <main class="main-content">
            @include('layouts.partials.flash-messages')
            {{ $slot }}
        </main>
    </div>
</body>
</html>
