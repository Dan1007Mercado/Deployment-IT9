@extends('layouts.dashboard')

@section('content')
<div class="flex min-h-screen">
    @include('components.sidebar')

    <main class="flex-1 bg-gray-50">
    @include('components.topnav', [
    'title' => 'Reservations Management',
    'subtitle' => 'Manage all guest reservations and room assignments'
    ])

        <div class="px-6 py-4">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm font-medium">Total Reservations</p>
                            <p class="text-3xl font-bold mt-1">{{ $reservations->total() }}</p>
                        </div>
                        <div class="p-3 bg-white bg-opacity-20 rounded-xl">
                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-2xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-yellow-100 text-sm font-medium">Pending</p>
                            <p class="text-3xl font-bold mt-1">
                                {{ $reservations->where('status', 'pending')->count() }}
                            </p>
                        </div>
                        <div class="p-3 bg-white bg-opacity-20 rounded-xl">
                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm font-medium">Confirmed</p>
                            <p class="text-3xl font-bold mt-1">
                                {{ $reservations->where('status', 'confirmed')->count() }}
                            </p>
                        </div>
                        <div class="p-3 bg-white bg-opacity-20 rounded-xl">
                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100 text-sm font-medium">Active Stays</p>
                            <p class="text-3xl font-bold mt-1">
                                {{ $reservations->where('status', 'checked-in')->count() }}
                            </p>
                        </div>
                        <div class="p-3 bg-white bg-opacity-20 rounded-xl">
                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Success Message -->
            @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl flex items-center shadow-sm">
                <svg class="h-5 w-5 text-green-600 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-green-800 font-medium">{{ session('success') }}</span>
            </div>
            @endif

            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                
                <div class="flex items-center space-x-3 mt-4 sm:mt-0">
                    <!-- Search Form -->
                    <form method="GET" action="{{ route('reservations.index') }}" class="relative">
                        @csrf
                        <input type="text" 
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Search reservations..." 
                               class="w-64 pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <!-- Hidden field to preserve per_page parameter -->
                        @if(request('per_page'))
                            <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                        @endif
                    </form>
                    <a href="{{ route('reservations.create') }}" 
                       class="flex items-center px-4 py-2.5 bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all shadow-sm font-medium">
                        <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        New Reservation
                    </a>
                </div>
            </div>

            <!-- Reservations Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">All Reservations</h3>
                        <div class="flex items-center space-x-4">
                            <!-- Entries Per Page Filter -->
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-600">Show</span>
                                <select id="per-page-filter" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
                                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                                </select>
                                <span class="text-sm text-gray-600">entries</span>
                            </div>
                            <div class="text-sm text-gray-500">
                                Showing {{ $reservations->firstItem() ?? 0 }}-{{ $reservations->lastItem() ?? 0 }} of {{ $reservations->total() }} results
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Guest Information</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Room Details</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Stay Period</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Reservation Status</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($reservations as $reservation)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <!-- Guest Information -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-12 w-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center text-white font-bold shadow-sm">
                                            {{ strtoupper(substr($reservation->guest->first_name, 0, 1)) }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="font-semibold text-gray-900">
                                                {{ $reservation->guest->first_name }} {{ $reservation->guest->last_name }}
                                            </div>
                                            <div class="text-sm text-gray-500 flex items-center mt-1">
                                                <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                </svg>
                                                {{ $reservation->guest->email }}
                                            </div>
                                            <div class="text-xs text-gray-400 flex items-center mt-1">
                                                <svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                                </svg>
                                                {{ $reservation->guest->contact_number }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Room Details -->
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-gray-900">
                                        @php
                                            $roomNumbers = [];
                                            foreach($reservation->bookings as $booking) {
                                                foreach($booking->rooms as $bookingRoom) {
                                                    $roomNumbers[] = $bookingRoom->room->room_number;
                                                }
                                            }
                                            $roomNumbers = array_unique($roomNumbers);
                                        @endphp
                                        @if(count($roomNumbers) > 0)
                                            {{ implode(', ', $roomNumbers) }}
                                        @else
                                            <span class="text-red-500">No rooms assigned</span>
                                        @endif
                                    </div>
                                    <div class="text-sm text-gray-600 mt-1">{{ $reservation->roomType->type_name }}</div>
                                    <div class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full inline-block mt-1">
                                        {{ count($roomNumbers) }} room(s)
                                    </div>
                                </td>

                                <!-- Stay Period -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $reservation->check_in_date->format('M j, Y') }}
                                    </div>
                                    <div class="text-sm text-gray-900">
                                        {{ $reservation->check_out_date->format('M j, Y') }}
                                    </div>
                                    <div class="text-xs text-gray-500 bg-blue-50 text-blue-700 px-2 py-1 rounded-full inline-block mt-1">
                                        {{ $reservation->nights }} night(s)
                                    </div>
                                </td>

                                <!-- Status -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusConfig = [
                                            'pending' => ['color' => 'bg-yellow-100 text-yellow-800 border border-yellow-200', 'icon' => '⏳'],
                                            'confirmed' => ['color' => 'bg-green-100 text-green-800 border border-green-200', 'icon' => '✅'],
                                            'checked-in' => ['color' => 'bg-blue-100 text-blue-800 border border-blue-200', 'icon' => '🏨'],
                                            'checked-out' => ['color' => 'bg-gray-100 text-gray-800 border border-gray-200', 'icon' => '📤'],
                                            'cancelled' => ['color' => 'bg-red-100 text-red-800 border border-red-200', 'icon' => '❌']
                                        ];
                                        $config = $statusConfig[$reservation->status] ?? ['color' => 'bg-gray-100 text-gray-800 border border-gray-200', 'icon' => '📄'];
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium {{ $config['color'] }}">
                                        <span class="mr-1.5">{{ $config['icon'] }}</span>
                                        {{ ucfirst($reservation->status) }}
                                    </span>
                                    @if($reservation->booking_source)
                                    <div class="text-xs text-gray-500 mt-2">
                                        Source: {{ ucfirst($reservation->booking_source) }}
                                    </div>
                                    @endif
                                </td>

                                <!-- Amount -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-lg font-bold text-gray-900">₱{{ number_format($reservation->total_amount) }}</div>
                                    <div class="text-xs text-gray-500 flex items-center mt-1">
                                        <svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        {{ $reservation->num_guests }} guest(s)
                                    </div>
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-2">
                                        <!-- Action Menu -->
                                        <div class="relative inline-block">
                                            <button type="button" 
                                                    class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-700 hover:bg-gray-50 transition-colors text-sm font-medium shadow-sm"
                                                    onclick="toggleActionMenu('menu-{{ $reservation->reservation_id }}')">
                                                <span class="mr-2">Actions</span>
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </button>
                                            <div id="menu-{{ $reservation->reservation_id }}" 
                                                 class="hidden absolute right-0 mt-1 w-56 bg-white rounded-xl shadow-lg border border-gray-200 z-10 py-1">
                                                <!-- Confirm Button (for pending) -->
                                                @if($reservation->status === 'pending')
                                                    <button type="button" 
                                                            onclick="openPaymentModal({{ $reservation->reservation_id }})"
                                                            class="flex items-center w-full px-4 py-2 text-sm text-green-600 font-medium hover:bg-gray-100">
                                                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        Confirm & Process Payment
                                                    </button>
                                                    @endif

                                                <a href="{{ route('reservations.edit', $reservation) }}" 
                                                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                    Edit Reservation
                                                </a>
                                                <a href="{{ route('reservations.show', $reservation) }}" 
                                                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    View Details
                                                </a>
                                                <div class="border-t border-gray-200 my-1"></div>
                                                
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="h-16 w-16 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">
                                            @if(request('search'))
                                                No reservations found for "{{ request('search') }}"
                                            @else
                                                No reservations found
                                            @endif
                                        </h3>
                                        <p class="text-gray-500 mb-6">
                                            @if(request('search'))
                                                Try adjusting your search terms
                                            @else
                                                Get started by creating your first reservation
                                            @endif
                                        </p>
                                        <a href="{{ route('reservations.create') }}" 
                                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                                            <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                            Create First Reservation
                                        </a>
                                        @if(request('search'))
                                        <a href="{{ route('reservations.index') }}" 
                                           class="inline-flex items-center px-4 py-2 mt-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                                            Clear Search
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination & Entries Info -->
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <!-- Entries Info -->
                        <div class="text-sm text-gray-600">
                            Showing {{ $reservations->firstItem() ?? 0 }} to {{ $reservations->lastItem() ?? 0 }} of {{ $reservations->total() }} entries
                        </div>

                        <!-- Pagination -->
                        @if($reservations->hasPages())
                        <div class="flex items-center space-x-2">
                            <!-- Previous Button -->
                            @if($reservations->onFirstPage())
                            <span class="px-3 py-1.5 border border-gray-300 rounded-lg text-gray-400 cursor-not-allowed text-sm">
                                Previous
                            </span>
                            @else
                            <a href="{{ $reservations->previousPageUrl() }}&per_page={{ request('per_page', 15) }}{{ request('search') ? '&search=' . request('search') : '' }}" 
                               class="px-3 py-1.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors text-sm">
                                Previous
                            </a>
                            @endif

                            <!-- Page Numbers -->
                            <div class="flex items-center space-x-1">
                                @foreach ($reservations->getUrlRange(1, $reservations->lastPage()) as $page => $url)
                                    @if ($page == $reservations->currentPage())
                                    <span class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-sm font-medium">
                                        {{ $page }}
                                    </span>
                                    @else
                                    <a href="{{ $url }}&per_page={{ request('per_page', 15) }}{{ request('search') ? '&search=' . request('search') : '' }}" 
                                       class="px-3 py-1.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors text-sm">
                                        {{ $page }}
                                    </a>
                                    @endif
                                @endforeach
                            </div>

                            <!-- Next Button -->
                            @if($reservations->hasMorePages())
                            <a href="{{ $reservations->nextPageUrl() }}&per_page={{ request('per_page', 15) }}{{ request('search') ? '&search=' . request('search') : '' }}" 
                               class="px-3 py-1.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors text-sm">
                                Next
                            </a>
                            @else
                            <span class="px-3 py-1.5 border border-gray-300 rounded-lg text-gray-400 cursor-not-allowed text-sm">
                                Next
                            </span>
                            @endif
                        </div>
                        @endif

                        <!-- Entries Per Page (Bottom) -->
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-600">Show</span>
                            <select id="per-page-filter-bottom" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
                                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                            </select>
                            <span class="text-sm text-gray-600">entries per page</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
function toggleActionMenu(menuId) {
    const menu = document.getElementById(menuId);
    menu.classList.toggle('hidden');
    
    // Close other open menus
    document.querySelectorAll('[id^="menu-"]').forEach(otherMenu => {
        if (otherMenu.id !== menuId) {
            otherMenu.classList.add('hidden');
        }
    });
}

// Close menus when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('[onclick*="toggleActionMenu"]')) {
        document.querySelectorAll('[id^="menu-"]').forEach(menu => {
            menu.classList.add('hidden');
        });
    }
});

// Entries per page filter
document.addEventListener('DOMContentLoaded', function() {
    const perPageFilterTop = document.getElementById('per-page-filter');
    const perPageFilterBottom = document.getElementById('per-page-filter-bottom');
    
    function changePerPage(value) {
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', value);
        // Preserve search parameter
        @if(request('search'))
            url.searchParams.set('search', '{{ request('search') }}');
        @endif
        window.location.href = url.toString();
    }
    
    if (perPageFilterTop) {
        perPageFilterTop.addEventListener('change', function() {
            changePerPage(this.value);
        });
    }
    
    if (perPageFilterBottom) {
        perPageFilterBottom.addEventListener('change', function() {
            changePerPage(this.value);
        });
    }
    
    // Sync both select values
    if (perPageFilterTop && perPageFilterBottom) {
        perPageFilterTop.addEventListener('change', function() {
            perPageFilterBottom.value = this.value;
        });
        
        perPageFilterBottom.addEventListener('change', function() {
            perPageFilterTop.value = this.value;
        });
    }

    // Auto-submit search form when typing (with debounce)
    const searchInput = document.querySelector('input[name="search"]');
    let searchTimeout;
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.form.submit();
            }, 500);
        });
    }
});
</script>

<style>
.overflow-x-auto::-webkit-scrollbar {
    height: 6px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
@include('components.payment-modal')
@endsection