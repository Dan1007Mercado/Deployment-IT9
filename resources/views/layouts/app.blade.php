<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] min-h-screen">
    @include('partials.navbar')

    {{-- Check if it's home page --}}
    @if(request()->is('/'))
        {{-- Full width for home page --}}
        <main class="w-full">
            @yield('content')
        </main>
    @else
        {{-- Container + sidebar for other pages --}}
        <div class="container mx-auto p-6 lg:flex lg:gap-6">
            @includeWhen(View::exists('partials.sidebar'), 'partials.sidebar')
            
            <main class="flex-1">
                @yield('content')
            </main>
        </div>
    @endif
    
    <div id="toast-container" class="toast-container position-fixed bottom-0 end-0 p-3"></div>

    {{-- Include modals if they exist --}}
    @yield('modals')
    
    <script src="{{ asset('js/site.js') }}"></script>
    <script src="{{ asset('js/sidebar.js') }}"></script>
    <script src="{{ asset('js/auth.js') }}"></script>
    
    {{-- Page-specific scripts --}}
    @yield('scripts')
</body>
</html>