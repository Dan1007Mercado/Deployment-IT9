
@extends('layouts.auth')

@section('content')
<div class="min-h-screen w-full bg-[#1e3a5f] flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl p-8 w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Hotel Management</h1>
            <p class="text-sm text-gray-600">Log in to hotel console</p>
        </div>
        <form id="login-form" action="{{ route('login.post') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" id="email" placeholder="name@example.com" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" required>
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" id="password" placeholder="••••••••" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" required>
            </div>
            @if($errors->any())
                <div class="mb-3 text-red-600 text-sm text-center">
                    {{ $errors->first() }}
                </div>
            @endif
            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center">
                    <input type="checkbox" name="remember" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-2">
                    <span class="text-gray-700">Remember me</span>
                </label>
                <a href="#" class="text-blue-600 hover:text-blue-700">Forgot password?</a>
            </div>
            <button type="submit" class="w-full bg-[#2c3e50] text-white py-2.5 rounded-md hover:bg-[#34495e] transition-colors font-medium">Sign In</button>
        </form>
        <div class="mt-6 text-center text-sm">
            <span class="text-gray-600">Need an account? </span>
            <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-700 font-medium">Create account</a>
        </div>
    </div>
</div>
@endsection