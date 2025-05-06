<!DOCTYPE html>
<html lang="it">
<head>
    <!-- meta, title, link CSS -->
    @livewireStyles
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-gray-50">
<div class="container mx-auto py-8">
    @yield('content')
</div>
@livewireScripts
</body>
</html>
