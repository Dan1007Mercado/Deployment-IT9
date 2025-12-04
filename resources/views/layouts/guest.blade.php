<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Azure Grand Hotel - Luxury Accommodation')</title>
    
    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>
    
    {{-- Lucide Icons --}}
    <script src="https://unpkg.com/lucide@latest"></script>
    
    {{-- Custom Styles --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
    @stack('styles')
</head>
<body class="min-h-screen bg-background text-foreground">
    {{-- Main Content --}}
    @yield('content')
    
    {{-- Scripts --}}
    <script>
        // Initialize Lucide icons
        lucide.createIcons();
    </script>
    
    @stack('scripts')
</body>
</html>