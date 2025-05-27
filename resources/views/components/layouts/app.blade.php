<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name', 'Laravel') }}</title>

    @livewireStyles
    @livewireScripts
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div>
        <img src="{{ asset('images/ConvBridge_Logo_100x88_wo_bg.png') }}" alt="Conversion Bridge Logo" class="h-16">
    </div>
    {{ $slot }}
</body>
</html>
