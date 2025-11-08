<header class="w-full lg:max-w-4xl max-w-[335px] text-sm mb-6 not-has-[nav]:hidden mx-auto px-6">
    <nav class="flex items-center justify-between gap-4 py-4">
        <div class="flex items-center gap-3">
            <a href="/" class="text-lg font-semibold">{{ config('app.name', 'Hotel') }}</a>
        </div>

        <div class="hidden lg:flex items-center gap-4">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline-block">
                        @csrf
                        <button type="submit" class="inline-block px-5 py-1.5 text-sm leading-normal">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A] rounded-sm text-sm leading-normal">Log in</a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">Register</a>
                    @endif
                @endauth
            @endif
        </div>

        <!-- Mobile menu button -->
        <div class="lg:hidden">
            <button id="mobile-menu-button" class="p-2 rounded-md border border-transparent hover:border-[#19140035]">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3 6h14M3 10h14M3 14h14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </div>
    </nav>

    <!-- Mobile menu (hidden by default) -->
    <div id="mobile-menu" class="hidden px-4 pb-4">
        @if (Route::has('login'))
            @auth
                <a href="{{ url('/dashboard') }}" class="block py-2">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="block py-2">Log in</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="block py-2">Register</a>
                @endif
            @endauth
        @endif
    </div>
</header>