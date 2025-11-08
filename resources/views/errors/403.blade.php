<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Unauthorized Access</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full">
            <div class="bg-white shadow-lg rounded-lg p-8 text-center">
                <!-- Error Icon -->
                <div class="mb-6">
                    <svg class="mx-auto h-24 w-24 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>

                <!-- Error Code -->
                <h1 class="text-6xl font-bold text-gray-800 mb-4">403</h1>

                <!-- Error Title -->
                <h2 class="text-2xl font-semibold text-gray-700 mb-4">Unauthorized Access</h2>

                <!-- Error Message -->
                <p class="text-gray-600 mb-6">
                    @if(isset($exception) && $exception->getMessage())
                        {{ $exception->getMessage() }}
                    @else
                        You do not have permission to access this page. This area is restricted to authorized personnel only.
                    @endif
                </p>

                <!-- Additional Info -->
                <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-6">
                    <p class="text-sm text-red-800">
                        <strong>Access Denied:</strong> Only users with the receptionist role can access this resource.
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-3">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded transition duration-200">
                            Return to Dashboard
                        </a>
                        <form action="{{ route('logout') }}" method="POST" class="w-full">
                            @csrf
                            <button type="submit" class="w-full bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded transition duration-200">
                                Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded transition duration-200">
                            Go to Login
                        </a>
                    @endauth
                </div>

                <!-- Help Text -->
                <p class="text-sm text-gray-500 mt-6">
                    If you believe this is an error, please contact your system administrator.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
