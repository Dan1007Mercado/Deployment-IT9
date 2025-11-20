@extends('layouts.dashboard')

@section('content')
<div class="flex min-h-screen">
    @include('components.sidebar')

    <main class="flex-1 bg-[#f8fafc]">
    @include('components.topnav', [
    'title' => 'Reservations Management',
    'subtitle' => 'Manage all guest reservations and room assignments'
    ])
        <div class="px-8 py-6">
            <!-- Success Message -->
            @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
            @endif

            
            <div class="flex items-center justify-between mb-6">
                <div class="flex-1 max-w-md">
                    <input type="text" placeholder="Search by guest name..." class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('reservations.create') }}" class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        New Booking Wizard
                    </a>
                </div>
            </div>

            <!-- Reservations Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Guest</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rooms</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dates</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($reservations as $reservation)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold">
                                        {{ strtoupper(substr($reservation->guest->first_name, 0, 1)) }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="font-medium text-gray-900">
                                            {{ $reservation->guest->first_name }} {{ $reservation->guest->last_name }}
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $reservation->guest->email }}</div>
                                        <div class="text-xs text-gray-400">{{ $reservation->guest->contact_number }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">
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
                                <div class="text-sm text-gray-500">{{ $reservation->roomType->type_name }}</div>
                                <div class="text-xs text-gray-400">{{ count($roomNumbers) }} room(s)</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $reservation->check_in_date->format('M j, Y') }}</div>
                                <div class="text-sm text-gray-900">{{ $reservation->check_out_date->format('M j, Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $reservation->nights }} nights</div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'confirmed' => 'bg-green-100 text-green-800', 
                                        'cancelled' => 'bg-red-100 text-red-800'
                                    ];
                                @endphp
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$reservation->status] ?? 'bg-gray-100' }}">
                                    {{ ucfirst($reservation->status) }}
                                </span>
                                @if($reservation->booking_source)
                                <div class="text-xs text-gray-500 mt-1">{{ ucfirst($reservation->booking_source) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-900">₱{{ number_format($reservation->total_amount) }}</div>
                                <div class="text-xs text-gray-500">{{ $reservation->num_guests }} guest(s)</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex space-x-2">
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
                                    <a href="{{ route('reservations.edit', $reservation) }}" class="text-blue-600 hover:text-blue-900">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('reservations.destroy', $reservation) }}" method="POST" onsubmit="return confirm('Delete this reservation?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                <svg class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <h3 class="mt-4 text-lg font-medium">No reservations found</h3>
                                <p class="mt-2">Get started by creating your first reservation</p>
                                <a href="{{ route('reservations.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                    <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Create First Reservation
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($reservations->hasPages(10))
            <div class="mt-6">
                {{ $reservations->links() }}
            </div>
            @endif
        </div>
    </main>
</div>
@include('components.payment-modal')
@endsection