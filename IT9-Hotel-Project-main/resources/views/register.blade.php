@extends('layouts.auth')

@section('content')
<div class="min-h-screen w-full flex items-center justify-center bg-[#1e3a5f]">
    <div class="bg-white rounded-lg shadow-xl p-8 w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">WELCOME</h1>
            <p class="text-sm text-gray-600">Create a manager account</p>
        </div>
        <form id="register-form" action="{{ route('register.post') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                <input type="text" name="name" id="name" placeholder="John Doe" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" required>
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" id="email" placeholder="John.1829511@email.com" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" required>
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <div class="relative">
                    <input type="password" name="password" id="password" placeholder="••••••••" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" required>
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-.274.832-.642 1.624-1.104 2.354M15.362 17.362A9.958 9.958 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.978 9.978 0 012.638-4.362" /></svg>
                    </span>
                </div>
            </div>
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                <div class="relative">
                    <input type="password" name="password_confirmation" id="password_confirmation" placeholder="••••••••" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" required>
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-.274.832-.642 1.624-1.104 2.354M15.362 17.362A9.958 9.958 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.978 9.978 0 012.638-4.362" /></svg>
                    </span>
                </div>
            </div>
            <button type="submit" class="w-full px-4 py-2 bg-[#1b1b18] text-white rounded font-semibold text-lg">Create Account</button>
        </form>
        <div class="mt-6 text-center text-sm text-gray-600">
            Already have an account? <a href="{{ route('login') }}" class="text-blue-600 font-semibold hover:underline">Sign in</a>
        </div>
        @if($errors->any())
            <div class="mt-3 text-red-600 text-sm">
                <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
@endsection