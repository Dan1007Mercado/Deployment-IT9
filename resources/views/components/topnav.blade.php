<!-- Top Bar -->
<div class="flex items-center justify-between px-8 py-6 border-b bg-white">
    <div>
        <h1 class="text-2xl font-semibold">{{ $title }}</h1>
        @if(isset($subtitle))
        <p class="text-gray-500 mt-1">{{ $subtitle }}</p>
        @endif
    </div>
    <div class="flex items-center space-x-4">
        <!-- User Dropdown -->
        <div class="relative">
            <button id="userMenuButton" class="flex items-center space-x-2 focus:outline-none">
                <span class="bg-blue-100 text-blue-700 rounded-full w-10 h-10 flex items-center justify-center font-bold text-lg">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}{{ strtoupper(substr(explode(' ', auth()->user()->name ?? 'User')[1] ?? substr(auth()->user()->name ?? 'U', 1, 1), 0, 1)) }}
                </span>
                <div class="text-right">
                    <div class="font-semibold">{{ auth()->user()->name ?? 'User' }}</div>
                    <div class="text-xs text-gray-500">{{ auth()->user()->email ?? '' }}</div>
                </div>
                <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            
            <!-- Dropdown Menu -->
            <div id="userDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-50 border border-gray-200">
                <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Profile</a>
                <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Settings</a>
                <hr class="my-1">
                <form method="POST" action="{{ route('logout') }}" id="logoutForm">
                    @csrf
                    <button type="submit" class="block w-full text-left px-4 py-2 text-red-600 hover:bg-gray-100">Logout</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // User dropdown toggle
    document.addEventListener('DOMContentLoaded', function() {
        const userMenuButton = document.getElementById('userMenuButton');
        const userDropdown = document.getElementById('userDropdown');

        if (userMenuButton && userDropdown) {
            userMenuButton.addEventListener('click', function(e) {
                e.stopPropagation();
                userDropdown.classList.toggle('hidden');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!userMenuButton.contains(e.target) && !userDropdown.contains(e.target)) {
                    userDropdown.classList.add('hidden');
                }
            });

            // Prevent dropdown from closing when clicking inside it
            userDropdown.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }
    });
</script>