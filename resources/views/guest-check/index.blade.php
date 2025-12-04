@extends('layouts.dashboard')

@section('content')
<div class="flex min-h-screen">
    @include('components.sidebar')

    <!-- Main Content -->
    <main class="flex-1 bg-[#f8fafc]">
        @include('components.topnav', [
            'title' => 'Guest Check-In/Out',
            'subtitle' => 'Manage guest arrivals and departures'
        ])
        
        <!-- Subheader -->
        <div class="bg-white border-b">
            <div class="px-8 py-4">
                <div class="flex items-center justify-between">
                    <!-- Date Filter and Search -->
                    <div class="flex items-center space-x-4">
                        <!-- Date Filter -->
                        <form method="GET" action="{{ route('guest-check.index') }}" class="flex items-center space-x-2">
                            <input type="date" name="date" value="{{ $today }}" 
                                   class="border rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded text-sm hover:bg-blue-600 transition-colors">
                                Filter
                            </button>
                            <a href="{{ route('guest-check.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm hover:bg-gray-300 transition-colors">
                                Today
                            </a>
                        </form>

                        <!-- Search Form -->
                        <form method="GET" action="{{ route('guest-check.index') }}" class="flex items-center">
                            <div class="relative">
                                <input type="text" name="search" value="{{ $search }}" 
                                       placeholder="Search guest or reservation..."
                                       class="border rounded px-3 py-2 text-sm w-64 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @if($search)
                                <a href="{{ route('guest-check.index', ['date' => $today]) }}" 
                                   class="absolute right-2 top-2 text-gray-400 hover:text-gray-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </a>
                                @endif
                            </div>
                            <button type="submit" class="ml-2 bg-gray-800 text-white px-4 py-2 rounded text-sm hover:bg-gray-900 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="px-8 py-6">
            
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Today's Arrivals Card - Clickable -->
            <button onclick="showTab('checkins')" 
                    class="bg-white rounded-xl shadow p-6 border-2 border-blue-300 hover:shadow-lg transition-shadow text-left cursor-pointer hover:border-blue-400 active:scale-[0.98] transition-all">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-600">Today's Arrivals</p>
                        <p class="text-xs text-gray-500 mt-1">Click to view check-ins</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <p class="text-2xl font-bold text-blue-600">{{ $checkIns->total() }}</p>
                        <div class="p-3 bg-blue-100 rounded-lg border border-blue-200">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                <!-- Click indicator -->
                <div class="mt-4 flex items-center text-xs text-blue-500">
                    <span>View check-ins →</span>
                </div>
            </button>

            <!-- Today's Departures Card - Clickable -->
            <button onclick="showTab('checkouts')" 
                    class="bg-white rounded-xl shadow p-6 border-2 border-red-300 hover:shadow-lg transition-shadow text-left cursor-pointer hover:border-red-400 active:scale-[0.98] transition-all">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-600">Today's Departures</p>
                        <p class="text-xs text-gray-500 mt-1">Click to view check-outs</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <p class="text-2xl font-bold text-red-600">{{ $checkOuts->total() }}</p>
                        <div class="p-3 bg-red-100 rounded-lg border border-red-200">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                <!-- Click indicator -->
                <div class="mt-4 flex items-center text-xs text-red-500">
                    <span>View check-outs →</span>
                </div>
            </button>

            <!-- Currently In House Card - Clickable -->
            <button onclick="showTab('current')" 
                    class="bg-white rounded-xl shadow p-6 border-2 border-green-300 hover:shadow-lg transition-shadow text-left cursor-pointer hover:border-green-400 active:scale-[0.98] transition-all">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-600">Currently In House</p>
                        <p class="text-xs text-gray-500 mt-1">Click to view checked-in guests</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <p class="text-2xl font-bold text-green-600">{{ $currentGuests->total() }}</p>
                        <div class="p-3 bg-green-100 rounded-lg border border-green-200">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                <!-- Click indicator -->
                <div class="mt-4 flex items-center text-xs text-green-500">
                    <span>View checked-in guests →</span>
                </div>
            </button>
        </div>
            <!-- Tabs Navigation -->
            <div class="mb-6">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8">
                        <button onclick="showTab('checkins')" 
                                class="tab-button py-4 px-1 border-b-2 font-medium text-sm active border-blue-500 text-blue-600" 
                                data-tab="checkins">
                            <span class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                </svg>
                                Today's Check-Ins ({{ $checkIns->total() }})
                            </span>
                        </button>
                        <button onclick="showTab('checkouts')" 
                                class="tab-button py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300" 
                                data-tab="checkouts">
                            <span class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                Today's Check-Outs ({{ $checkOuts->total() }})
                            </span>
                        </button>
                        <button onclick="showTab('current')" 
                                class="tab-button py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300" 
                                data-tab="current">
                            <span class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                Currently Checked-In ({{ $currentGuests->total() }})
                            </span>
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Check-Ins Tab -->
            <div id="checkins-tab" class="tab-content">
                <div class="bg-white rounded-xl shadow border-2 border-gray-300">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-gray-800">Today's Arrivals</h2>
                                <p class="text-sm text-gray-500">Guests scheduled to check-in on {{ \Carbon\Carbon::parse($today)->format('F d, Y') }}</p>
                            </div>
                            <div class="flex items-center space-x-4">
                                @if($search)
                                <div class="text-sm text-blue-600 bg-blue-50 px-3 py-1 rounded-full">
                                    Search: "{{ $search }}"
                                </div>
                                @endif
                                <span class="px-3 py-1 text-sm bg-blue-100 text-blue-800 rounded-full">
                                    {{ $checkIns->total() }} guests
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    @if($checkIns->isEmpty())
                    <div class="p-8 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        @if($search)
                            <p class="text-lg font-medium mb-2">No check-ins found for "{{ $search }}"</p>
                            <p class="text-sm">Try a different search term or clear the search.</p>
                        @else
                            <p class="text-lg font-medium mb-2">No check-ins scheduled for today</p>
                            <p class="text-sm">All guests are checked in or no arrivals scheduled.</p>
                        @endif
                    </div>
                    @else
                    <div class="p-1">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guest</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reservation</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room(s)</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($checkIns as $booking)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                    </svg>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $booking->reservation->guest->first_name }} {{ $booking->reservation->guest->last_name }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $booking->reservation->guest->email }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">#{{ $booking->reservation->reservation_id }}</div>
                                            <div class="text-sm text-gray-500">
                                                {{ \Carbon\Carbon::parse($booking->reservation->check_in_date)->format('M d') }} - 
                                                {{ \Carbon\Carbon::parse($booking->reservation->check_out_date)->format('M d') }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($booking->rooms as $bookingRoom)
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                        {{ $bookingRoom->room->room_number }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                Reserved
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                            <button onclick="checkInGuest('{{ $booking->booking_id }}', '{{ $booking->reservation->guest->first_name }} {{ $booking->reservation->guest->last_name }}')"
                                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-lg shadow-sm text-white bg-blue-500 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Check In
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- Pagination -->
                        @if($checkIns->hasPages())
                        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-xl">
                            <div class="flex flex-col md:flex-row items-center justify-between">
                                <div class="text-sm text-gray-500 mb-2 md:mb-0">
                                    Showing {{ $checkIns->firstItem() }} to {{ $checkIns->lastItem() }} of {{ $checkIns->total() }} entries
                                </div>
                                <div class="flex space-x-1">
                                    {{ $checkIns->appends(['date' => $today, 'search' => $search])->links('pagination::tailwind') }}
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Check-Outs Tab -->
            <div id="checkouts-tab" class="tab-content hidden">
                <div class="bg-white rounded-xl shadow border-2 border-gray-300">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-gray-800">Today's Departures</h2>
                                <p class="text-sm text-gray-500">Guests scheduled to check-out on {{ \Carbon\Carbon::parse($today)->format('F d, Y') }}</p>
                            </div>
                            <div class="flex items-center space-x-4">
                                @if($search)
                                <div class="text-sm text-red-600 bg-red-50 px-3 py-1 rounded-full">
                                    Search: "{{ $search }}"
                                </div>
                                @endif
                                <span class="px-3 py-1 text-sm bg-red-100 text-red-800 rounded-full">
                                    {{ $checkOuts->total() }} guests
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    @if($checkOuts->isEmpty())
                    <div class="p-8 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        @if($search)
                            <p class="text-lg font-medium mb-2">No check-outs found for "{{ $search }}"</p>
                            <p class="text-sm">Try a different search term or clear the search.</p>
                        @else
                            <p class="text-lg font-medium mb-2">No check-outs scheduled for today</p>
                            <p class="text-sm">All guests have checked out or no departures scheduled.</p>
                        @endif
                    </div>
                    @else
                    <div class="p-1">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guest</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room(s)</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check-Out Time</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($checkOuts as $booking)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                    </svg>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $booking->reservation->guest->first_name }} {{ $booking->reservation->guest->last_name }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        Room: {{ $booking->rooms->first()->room->room_number ?? 'N/A' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($booking->rooms as $bookingRoom)
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                        {{ $bookingRoom->room->room_number }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                @if($booking->reservation->check_out_time)
                                                    {{ \Carbon\Carbon::parse($booking->reservation->check_out_time)->format('g:i A') }}
                                                @else
                                                    12:00 PM
                                                @endif
                                            </div>
                                            <div class="text-xs text-gray-500">Expected</div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                            <button onclick="checkOutGuest('{{ $booking->booking_id }}', '{{ $booking->reservation->guest->first_name }} {{ $booking->reservation->guest->last_name }}')"
                                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-lg shadow-sm text-white bg-red-500 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                                </svg>
                                                Check Out
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- Pagination -->
                        @if($checkOuts->hasPages())
                        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-xl">
                            <div class="flex flex-col md:flex-row items-center justify-between">
                                <div class="text-sm text-gray-500 mb-2 md:mb-0">
                                    Showing {{ $checkOuts->firstItem() }} to {{ $checkOuts->lastItem() }} of {{ $checkOuts->total() }} entries
                                </div>
                                <div class="flex space-x-1">
                                    {{ $checkOuts->appends(['date' => $today, 'search' => $search])->links('pagination::tailwind') }}
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Current Guests Tab -->
            <div id="current-tab" class="tab-content hidden">
                <div class="bg-white rounded-xl shadow border-2 border-gray-300">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-gray-800">Currently Checked-In Guests</h2>
                                <p class="text-sm text-gray-500">All guests currently staying at the hotel</p>
                            </div>
                            <div class="flex items-center space-x-4">
                                @if($search)
                                <div class="text-sm text-green-600 bg-green-50 px-3 py-1 rounded-full">
                                    Search: "{{ $search }}"
                                </div>
                                @endif
                                <span class="px-3 py-1 text-sm bg-green-100 text-green-800 rounded-full">
                                    {{ $currentGuests->total() }} guests
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    @if($currentGuests->isEmpty())
                    <div class="p-8 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        @if($search)
                            <p class="text-lg font-medium mb-2">No guests found for "{{ $search }}"</p>
                            <p class="text-sm">Try a different search term or clear the search.</p>
                        @else
                            <p class="text-lg font-medium mb-2">No guests currently checked in</p>
                            <p class="text-sm">All rooms are available or no guests have checked in yet.</p>
                        @endif
                    </div>
                    @else
                    <div class="p-1">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guest</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room(s)</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Checked In</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($currentGuests as $booking)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10 bg-green-100 rounded-full flex items-center justify-center">
                                                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                    </svg>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $booking->reservation->guest->first_name }} {{ $booking->reservation->guest->last_name }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $booking->reservation->guest->email }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($booking->rooms as $bookingRoom)
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                                        {{ $bookingRoom->room->room_number }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ $booking->actual_check_in ? \Carbon\Carbon::parse($booking->actual_check_in)->format('M d, Y g:i A') : 'N/A' }}
                                            </div>
                                            <div class="text-xs text-gray-500">Actual check-in</div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                            <button onclick="checkOutGuest('{{ $booking->booking_id }}', '{{ $booking->reservation->guest->first_name }} {{ $booking->reservation->guest->last_name }}')"
                                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-lg shadow-sm text-white bg-red-500 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                                </svg>
                                                Early Check-Out
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- Pagination -->
                        @if($currentGuests->hasPages())
                        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-xl">
                            <div class="flex flex-col md:flex-row items-center justify-between">
                                <div class="text-sm text-gray-500 mb-2 md:mb-0">
                                    Showing {{ $currentGuests->firstItem() }} to {{ $currentGuests->lastItem() }} of {{ $currentGuests->total() }} entries
                                </div>
                                <div class="flex space-x-1">
                                    {{ $currentGuests->appends(['date' => $today, 'search' => $search])->links('pagination::tailwind') }}
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Toast Notification Container -->
<div id="toast-container" class="fixed top-6 right-6 z-50 space-y-4"></div>

<script>
// Tab functionality
function showTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });
    
    // Remove active class from all buttons
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active', 'border-blue-500', 'text-blue-600');
        button.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
    });
    
    // Show selected tab
    document.getElementById(`${tabName}-tab`).classList.remove('hidden');
    
    // Activate selected button
    const activeButton = document.querySelector(`[data-tab="${tabName}"]`);
    activeButton.classList.add('active', 'border-blue-500', 'text-blue-600');
    activeButton.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
}

// Toast Notification Function
function showToast(type, message) {
    const container = document.getElementById('toast-container');
    
    // Define colors based on type
    const colors = {
        success: {
            bg: 'bg-green-500',
            border: 'border-green-600',
            icon: `<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                   </svg>`
        },
        error: {
            bg: 'bg-red-500',
            border: 'border-red-600',
            icon: `<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                   </svg>`
        },
        info: {
            bg: 'bg-blue-500',
            border: 'border-blue-600',
            icon: `<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                   </svg>`
        }
    };
    
    const toastId = 'toast-' + Date.now();
    const toast = document.createElement('div');
    toast.id = toastId;
    toast.className = `flex items-center p-4 text-white rounded-lg shadow-lg ${colors[type].bg} border ${colors[type].border} transform transition-all duration-300 translate-x-full opacity-0`;
    toast.innerHTML = `
        <div class="flex items-center">
            ${colors[type].icon}
            <span class="font-medium">${message}</span>
        </div>
        <button onclick="closeToast('${toastId}')" class="ml-4 text-white hover:text-gray-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    `;
    
    container.appendChild(toast);
    
    // Animate in
    setTimeout(() => {
        toast.classList.remove('translate-x-full', 'opacity-0');
        toast.classList.add('translate-x-0', 'opacity-100');
    }, 10);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        closeToast(toastId);
    }, 5000);
}

// Close toast function
function closeToast(toastId) {
    const toast = document.getElementById(toastId);
    if (toast) {
        toast.classList.remove('translate-x-0', 'opacity-100');
        toast.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }
}

// Check-In Function
function checkInGuest(bookingId, guestName) {
    if (confirm(`Are you sure you want to check in ${guestName}?`)) {
        // Show loading state
        const checkInBtn = document.querySelector(`button[onclick*="${bookingId}"]`);
        const originalText = checkInBtn.innerHTML;
        checkInBtn.innerHTML = `<svg class="animate-spin h-4 w-4 mr-1 text-white" fill="none" viewBox="0 0 24 24">
                                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg> Checking In...`;
        checkInBtn.disabled = true;
        
        fetch(`/guest-check/quick-checkin/${bookingId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('success', `✓ ${guestName} successfully checked in!`);
                // Reload page after 1.5 seconds to show updated status
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                showToast('error', `✗ Failed to check in ${guestName}: ${data.message}`);
                checkInBtn.innerHTML = originalText;
                checkInBtn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('error', `✗ Error during check-in. Please try again.`);
            checkInBtn.innerHTML = originalText;
            checkInBtn.disabled = false;
        });
    }
}

// Check-Out Function
function checkOutGuest(bookingId, guestName) {
    if (confirm(`Are you sure you want to check out ${guestName}?`)) {
        // Show loading state
        const checkOutBtn = document.querySelector(`button[onclick*="${bookingId}"]`);
        const originalText = checkOutBtn.innerHTML;
        checkOutBtn.innerHTML = `<svg class="animate-spin h-4 w-4 mr-1 text-white" fill="none" viewBox="0 0 24 24">
                                   <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                   <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                 </svg> Checking Out...`;
        checkOutBtn.disabled = true;
        
        fetch(`/guest-check/quick-checkout/${bookingId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('success', `✓ ${guestName} successfully checked out!`);
                // Reload page after 1.5 seconds to show updated status
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                showToast('error', `✗ Failed to check out ${guestName}: ${data.message}`);
                checkOutBtn.innerHTML = originalText;
                checkOutBtn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('error', `✗ Error during check-out. Please try again.`);
            checkOutBtn.innerHTML = originalText;
            checkOutBtn.disabled = false;
        });
    }
}

// Initialize first tab
document.addEventListener('DOMContentLoaded', function() {
    showTab('checkins');
    
    // Show success message from session if exists
    @if(session('success'))
        showToast('success', '{{ session('success') }}');
    @endif
    
    @if(session('error'))
        showToast('error', '{{ session('error') }}');
    @endif
    
    // Preserve search term in input when changing tabs
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput && searchInput.value) {
        // Keep the search value when switching tabs
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', function() {
                // Search is already preserved via form parameters
            });
        });
    }
});
</script>

<style>
.tab-button.active {
    border-color: #3b82f6;
    color: #3b82f6;
}

/* Toast animations */
@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOut {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}

.toast-slide-in {
    animation: slideIn 0.3s ease-out forwards;
}

.toast-slide-out {
    animation: slideOut 0.3s ease-in forwards;
}

/* Pagination styling */
.pagination {
    display: flex;
    list-style: none;
    padding: 0;
}

.page-item {
    margin: 0 2px;
}

.page-link {
    display: block;
    padding: 0.5rem 0.75rem;
    background-color: white;
    border: 1px solid #d1d5db;
    color: #374151;
    text-decoration: none;
    border-radius: 0.375rem;
    transition: all 0.2s;
}

.page-link:hover {
    background-color: #f3f4f6;
    border-color: #9ca3af;
}

.page-item.active .page-link {
    background-color: #3b82f6;
    border-color: #3b82f6;
    color: white;
}

.page-item.disabled .page-link {
    color: #9ca3af;
    pointer-events: none;
    background-color: #f9fafb;
}
</style>
@endsection