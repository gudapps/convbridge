<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name', 'Laravel') }}</title>
    @livewireStyles
</head>
<body>

    <div class="container">
        @yield('content')
    </div>

    @livewireScripts
</body>
</html>
