@extends('layouts.dashboard')

@section('content')
<div class="flex min-h-screen">
    @include('components.sidebar')
    
    <main class="flex-1 bg-gray-50">
        @include('components.topnav', ['title' => 'Reservation Details'])

        <div class="px-6 py-4">
            <!-- Header Section -->
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <a href="{{ route('reservations.index') }}" 
                           class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-2">
                            <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back to Reservations
                        </a>
                        <h1 class="text-2xl font-bold text-gray-900">Reservation #{{ $reservation->reservation_id }}</h1>
                        <p class="text-gray-600 mt-1">Created on {{ $reservation->created_at->format('F j, Y \a\t g:i A') }}</p>
                    </div>
                    <div>
                        @php
                            $statusConfig = [
                                'pending' => ['color' => 'bg-yellow-100 text-yellow-800', 'border' => 'border-yellow-200'],
                                'confirmed' => ['color' => 'bg-green-100 text-green-800', 'border' => 'border-green-200'],
                                'checked-in' => ['color' => 'bg-blue-100 text-blue-800', 'border' => 'border-blue-200'],
                                'checked-out' => ['color' => 'bg-gray-100 text-gray-800', 'border' => 'border-gray-200'],
                                'cancelled' => ['color' => 'bg-red-100 text-red-800', 'border' => 'border-red-200']
                            ];
                            $config = $statusConfig[$reservation->status] ?? ['color' => 'bg-gray-100 text-gray-800', 'border' => 'border-gray-200'];
                        @endphp
                        <span class="inline-flex items-center px-4 py-2 rounded-lg border {{ $config['border'] }} {{ $config['color'] }} font-semibold text-sm">
                            {{ ucfirst($reservation->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column - Reservation Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Guest Information Card -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-semibold text-gray-900">Guest Information</h2>
                            <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
                                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                                    <p class="text-gray-900 font-medium">{{ $reservation->guest->first_name }} {{ $reservation->guest->last_name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                                    <div class="flex items-center text-gray-600">
                                        <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        {{ $reservation->guest->email }}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Contact Number</label>
                                    <div class="flex items-center text-gray-600">
                                        <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                        {{ $reservation->guest->contact_number }}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Guest Type</label>
                                    <p class="text-gray-600">{{ ucfirst($reservation->guest->guest_type ?? 'Regular') }}</p>
                                </div>
                            </div>
                        </div>

                        @if($reservation->special_requests)
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Special Requests</label>
                            <p class="text-gray-600 bg-gray-50 rounded-lg p-4 text-sm">
                                {{ $reservation->special_requests }}
                            </p>
                        </div>
                        @endif
                    </div>

                    <!-- Stay Details Card -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-semibold text-gray-900">Stay Details</h2>
                            <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center">
                                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="text-center p-4 border border-gray-200 rounded-lg">
                                <div class="text-blue-600 font-semibold mb-1">Check-in</div>
                                <div class="text-lg font-bold text-gray-900">{{ $reservation->check_in_date->format('M j, Y') }}</div>
                                <div class="text-sm text-gray-500">3:00 PM</div>
                            </div>

                            <div class="text-center p-4 border border-gray-200 rounded-lg">
                                <div class="text-green-600 font-semibold mb-1">Check-out</div>
                                <div class="text-lg font-bold text-gray-900">{{ $reservation->check_out_date->format('M j, Y') }}</div>
                                <div class="text-sm text-gray-500">11:00 AM</div>
                            </div>

                            <div class="text-center p-4 border border-gray-200 rounded-lg">
                                <div class="text-purple-600 font-semibold mb-1">Duration</div>
                                <div class="text-lg font-bold text-gray-900">{{ $reservation->nights }} night(s)</div>
                                <div class="text-sm text-gray-500">{{ $reservation->num_guests }} guest(s)</div>
                            </div>
                        </div>
                    </div>

                    <!-- Rooms & Pricing Card -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-semibold text-gray-900">Rooms & Pricing</h2>
                            <div class="w-8 h-8 bg-purple-500 rounded-lg flex items-center justify-center">
                                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                            </div>
                        </div>

                        <!-- Room Type -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">Room Type</label>
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <h3 class="font-semibold text-gray-900">{{ $reservation->roomType->type_name }}</h3>
                                        <p class="text-gray-600 text-sm mt-1">{{ $reservation->roomType->description }}</p>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-lg font-bold text-blue-600">₱{{ number_format($reservation->roomType->base_price) }}</div>
                                        <div class="text-sm text-gray-500">per night</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Assigned Rooms -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">Assigned Rooms</label>
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
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                @foreach($roomNumbers as $roomNumber)
                                <div class="bg-green-50 border border-green-200 rounded-lg p-3 text-center">
                                    <div class="text-green-600 font-semibold">Room {{ $roomNumber }}</div>
                                    <div class="text-xs text-green-500 mt-1">Assigned</div>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
                                <div class="text-yellow-600 font-semibold">No Rooms Assigned</div>
                                <div class="text-xs text-yellow-500 mt-1">Pending room assignment</div>
                            </div>
                            @endif
                        </div>

                        <!-- Pricing Breakdown -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Pricing Breakdown</label>
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600">Room charges ({{ $reservation->nights }} nights)</span>
                                        <span class="font-medium">₱{{ number_format($reservation->total_amount) }}</span>
                                    </div>
                                    
                                    @if($reservation->tax_amount && $reservation->tax_amount > 0)
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-gray-600">Tax ({{ $reservation->tax_rate ?? 12 }}%)</span>
                                        <span class="font-medium">₱{{ number_format($reservation->tax_amount) }}</span>
                                    </div>
                                    @endif
                                    
                                    @if($reservation->discount_amount && $reservation->discount_amount > 0)
                                    <div class="flex justify-between items-center text-sm text-green-600">
                                        <span>Discount</span>
                                        <span class="font-medium">-₱{{ number_format($reservation->discount_amount) }}</span>
                                    </div>
                                    @endif
                                    
                                    <div class="border-t border-gray-200 pt-3">
                                        <div class="flex justify-between items-center text-lg font-bold">
                                            <span>Total Amount</span>
                                            <span class="text-blue-600">₱{{ number_format($reservation->total_amount) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Additional Information -->
                <div class="space-y-6">
                    <!-- Booking Information Card -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-semibold text-gray-900">Booking Information</h2>
                            <div class="w-8 h-8 bg-gray-500 rounded-lg flex items-center justify-center">
                                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Booking Source</label>
                                <p class="text-gray-900">{{ ucfirst($reservation->booking_source) }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Reservation Type</label>
                                <p class="text-gray-900">{{ ucfirst($reservation->reservation_type) }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Number of Guests</label>
                                <p class="text-gray-900">{{ $reservation->num_guests }} guest(s)</p>
                            </div>
                            
                            @if($reservation->expires_at)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Expiration</label>
                                <p class="text-gray-900 {{ $reservation->expires_at->isPast() ? 'text-red-600' : '' }}">
                                    {{ $reservation->expires_at->format('M j, Y g:i A') }}
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Timeline Card -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-semibold text-gray-900">Reservation Timeline</h2>
                            <div class="w-8 h-8 bg-orange-500 rounded-lg flex items-center justify-center">
                                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center mt-0.5">
                                    <svg class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">Reservation Created</p>
                                    <p class="text-xs text-gray-500">{{ $reservation->created_at->format('M j, Y g:i A') }}</p>
                                </div>
                            </div>

                            @if($reservation->status === 'confirmed' || $reservation->status === 'checked-in' || $reservation->status === 'checked-out')
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 w-6 h-6 bg-green-500 rounded-full flex items-center justify-center mt-0.5">
                                    <svg class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">Reservation Confirmed</p>
                                    <p class="text-xs text-gray-500">
                                        @if($reservation->updated_at)
                                            {{ $reservation->updated_at->format('M j, Y g:i A') }}
                                        @else
                                            {{ $reservation->created_at->format('M j, Y g:i A') }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                            @endif

                            @if($reservation->status === 'pending' || $reservation->status === 'confirmed')
                            <div class="flex items-start space-x-3 opacity-60">
                                <div class="flex-shrink-0 w-6 h-6 bg-gray-300 rounded-full flex items-center justify-center mt-0.5">
                                    <svg class="h-3 w-3 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">Scheduled Check-in</p>
                                    <p class="text-xs text-gray-500">{{ $reservation->check_in_date->format('M j, Y') }} at 3:00 PM</p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-3 opacity-60">
                                <div class="flex-shrink-0 w-6 h-6 bg-gray-300 rounded-full flex items-center justify-center mt-0.5">
                                    <svg class="h-3 w-3 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">Scheduled Check-out</p>
                                    <p class="text-xs text-gray-500">{{ $reservation->check_out_date->format('M j, Y') }} at 11:00 AM</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection