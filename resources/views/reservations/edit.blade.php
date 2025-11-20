@extends('layouts.dashboard')

@section('content')
<div class="flex min-h-screen">
    @include('components.sidebar')

    <main class="flex-1 bg-gray-50">
        @include('components.topnav', ['title' => 'Edit Reservation'])

        <div class="px-6 py-4">
            <!-- Success/Error Messages -->
            @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl flex items-center shadow-sm">
                <svg class="h-5 w-5 text-green-600 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-green-800 font-medium">{{ session('success') }}</span>
            </div>
            @endif

            @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl shadow-sm">
                <div class="flex items-center mb-2">
                    <svg class="h-5 w-5 text-red-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-red-800 font-medium">Please fix the following errors:</span>
                </div>
                <ul class="list-disc list-inside text-red-700 text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Edit Reservation</h2>
                        <p class="text-gray-600 mt-1">Update reservation details and room assignments</p>
                    </div>
                    <div class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                        ID: #{{ $reservation->reservation_id }}
                    </div>
                </div>

                <form action="{{ route('reservations.update', $reservation) }}" method="POST" id="reservation-form">
                    @csrf
                    @method('PUT')

                    <!-- Guest Information -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">Guest Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                                <input type="text" name="first_name" id="first_name" 
                                       value="{{ $reservation->guest->first_name }}" 
                                       class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" required>
                            </div>
                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                                <input type="text" name="last_name" id="last_name" 
                                       value="{{ $reservation->guest->last_name }}" 
                                       class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" required>
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input type="email" name="email" id="email" 
                                       value="{{ $reservation->guest->email }}" 
                                       class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" required>
                            </div>
                            <div>
                                <label for="contact_number" class="block text-sm font-medium text-gray-700 mb-2">Contact Number</label>
                                <input type="text" name="contact_number" id="contact_number" 
                                       value="{{ $reservation->guest->contact_number }}" 
                                       class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" required>
                            </div>
                        </div>
                    </div>

                    <!-- Reservation Details -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">Reservation Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="check_in_date" class="block text-sm font-medium text-gray-700 mb-2">Check-in Date</label>
                                <input type="date" name="check_in_date" id="check_in_date" 
                                       value="{{ $reservation->check_in_date->format('Y-m-d') }}" 
                                       class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" required>
                            </div>
                            <div>
                                <label for="check_out_date" class="block text-sm font-medium text-gray-700 mb-2">Check-out Date</label>
                                <input type="date" name="check_out_date" id="check_out_date" 
                                       value="{{ $reservation->check_out_date->format('Y-m-d') }}" 
                                       class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" required>
                            </div>
                            <div>
                                <label for="num_guests" class="block text-sm font-medium text-gray-700 mb-2">Number of Guests</label>
                                <input type="number" name="num_guests" id="num_guests" 
                                       value="{{ $reservation->num_guests }}" 
                                       class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" min="1" required>
                            </div>
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select name="status" id="status" class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    <option value="pending" {{ $reservation->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="confirmed" {{ $reservation->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>  
                                    <option value="cancelled" {{ $reservation->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                            <div>
                                <label for="total_amount" class="block text-sm font-medium text-gray-700 mb-2">Total Amount</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">₱</span>
                                    <input type="number" name="total_amount" id="total_amount" 
                                           value="{{ $reservation->total_amount }}" 
                                           class="w-full border border-gray-300 rounded-xl pl-8 pr-4 py-3 bg-gray-50 text-gray-700" 
                                           step="0.01" required readonly>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Amount auto-calculated based on rooms and nights</p>
                            </div>
                            <div>
                                <label for="booking_source" class="block text-sm font-medium text-gray-700 mb-2">Booking Source</label>
                                <select name="booking_source" id="booking_source" class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    <option value="walk-in" {{ $reservation->booking_source == 'walk-in' ? 'selected' : '' }}>Walk-in</option>
                                    <option value="phone" {{ $reservation->booking_source == 'phone' ? 'selected' : '' }}>Phone</option>
                                    <option value="online" {{ $reservation->booking_source == 'online' ? 'selected' : '' }}>Online</option>
                                    <option value="agent" {{ $reservation->booking_source == 'agent' ? 'selected' : '' }}>Agent</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Room Management -->
                    <div class="mb-8">
                        <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Room Management</h3>
                            <button type="button" id="add-room-btn" 
                                    class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors text-sm font-medium">
                                <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Add Room
                            </button>
                        </div>
                        
                        <!-- Current Rooms -->
                        <div id="current-rooms" class="mb-6">
                            <h4 class="text-md font-medium text-gray-700 mb-3">Current Rooms</h4>
                            <div class="space-y-3" id="rooms-container">
                                @php
                                    $currentRooms = [];
                                    foreach($reservation->bookings as $booking) {
                                        foreach($booking->rooms as $bookingRoom) {
                                            $currentRooms[] = [
                                                'room_id' => $bookingRoom->room_id,
                                                'room_number' => $bookingRoom->room->room_number,
                                                'room_type' => $bookingRoom->room->roomType->type_name,
                                                'price' => $bookingRoom->room_price,
                                                'booking_room_id' => $bookingRoom->booking_room_id
                                            ];
                                        }
                                    }
                                @endphp
                                
                                @foreach($currentRooms as $index => $room)
                                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-xl bg-white room-item" data-room-id="{{ $room['room_id'] }}">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <span class="text-blue-600 font-semibold">{{ $room['room_number'] }}</span>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">Room {{ $room['room_number'] }}</div>
                                            <div class="text-sm text-gray-500">{{ $room['room_type'] }}</div>
                                            <div class="text-sm font-semibold text-green-600">₱{{ number_format($room['price']) }}/night</div>
                                        </div>
                                    </div>
                                    <button type="button" class="remove-room-btn text-red-600 hover:text-red-800 p-2 transition-colors" 
                                            data-booking-room-id="{{ $room['booking_room_id'] }}"
                                            {{ count($currentRooms) <= 1 ? 'disabled' : '' }}>
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                    <input type="hidden" name="current_rooms[]" value="{{ $room['room_id'] }}">
                                </div>
                                @endforeach
                            </div>
                            @if(count($currentRooms) <= 1)
                            <p class="text-xs text-red-500 mt-2">⚠️ At least 1 room is required. You cannot remove the last room.</p>
                            @endif
                        </div>

                        <!-- Available Rooms to Add -->
                        <div id="available-rooms-section" class="hidden">
                            <h4 class="text-md font-medium text-gray-700 mb-3">Available Rooms</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="available-rooms-list">
                                <!-- Available rooms will be loaded here -->
                            </div>
                        </div>
                    </div>

                    <!-- Special Requests -->
                    <div class="mb-8">
                        <label for="special_requests" class="block text-sm font-medium text-gray-700 mb-2">Special Requests</label>
                        <textarea name="special_requests" id="special_requests" rows="3" 
                                  class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-none">{{ $reservation->special_requests }}</textarea>
                    </div>

                    <!-- Pricing Summary -->
                    <div class="mb-8 bg-gray-50 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Pricing Summary</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Room Charges</span>
                                <span class="font-medium" id="room-charges">₱0.00</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Nights</span>
                                <span class="font-medium" id="nights-count">0 nights</span>
                            </div>
                            <div class="border-t border-gray-200 pt-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-semibold text-gray-900">Total Amount</span>
                                    <span class="text-xl font-bold text-blue-600" id="total-amount-display">₱0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                        <a href="{{ route('reservations.index') }}" 
                           class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors font-medium">
                            Cancel
                        </a>
                        <button type="submit" id="update-btn"
                                class="px-6 py-3 bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all shadow-sm font-medium flex items-center">
                            <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Update Reservation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<!-- Add Room Modal -->
<div id="add-room-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl max-h-[80vh] overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-semibold">Add Room to Reservation</h3>
                <button type="button" onclick="closeAddRoomModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
            </div>
        </div>
        <div class="p-6 overflow-y-auto max-h-[50vh]">
            <div id="modal-available-rooms" class="space-y-4">
                <!-- Available rooms will be loaded here -->
            </div>
        </div>
        <div class="p-6 border-t border-gray-200 bg-gray-50">
            <div class="flex justify-between items-center">
                <div class="text-sm text-gray-600">
                    <span id="selected-rooms-count">0</span> room(s) selected
                </div>
                <div class="flex space-x-3">
                    <button type="button" onclick="closeAddRoomModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors">Cancel</button>
                    <button type="button" onclick="confirmRoomSelection()" class="px-6 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors">Add Selected Rooms</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Global variables
let selectedRooms = [];
let roomPrices = {};
let currentRooms = @json($currentRooms);

// Initialize room data
document.addEventListener('DOMContentLoaded', function() {
    // Store initial room prices
    currentRooms.forEach(room => {
        roomPrices[room.room_id] = room.price;
    });
    
    // Calculate initial total
    calculateTotalAmount();
    
    // Add event listeners
    initializeEventListeners();
    
    // Update remove button states
    updateRemoveButtonStates();
});

function initializeEventListeners() {
    // Date change listeners for recalculating total
    const checkInDate = document.getElementById('check_in_date');
    const checkOutDate = document.getElementById('check_out_date');
    
    if (checkInDate && checkOutDate) {
        checkInDate.addEventListener('change', calculateTotalAmount);
        checkOutDate.addEventListener('change', calculateTotalAmount);
    }
    
    // Number of guests change listener
    const numGuests = document.getElementById('num_guests');
    if (numGuests) {
        numGuests.addEventListener('change', calculateTotalAmount);
    }
    
    // Add room button
    const addRoomBtn = document.getElementById('add-room-btn');
    if (addRoomBtn) {
        addRoomBtn.addEventListener('click', openAddRoomModal);
    }
    
    // Remove room buttons
    document.querySelectorAll('.remove-room-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const roomItem = this.closest('.room-item');
            const roomId = roomItem.dataset.roomId;
            removeRoom(roomId, roomItem);
        });
    });
    
    // Form submission
    const form = document.getElementById('reservation-form');
    if (form) {
        form.addEventListener('submit', handleFormSubmit);
    }
}

function validateMinimumRooms() {
    const roomItems = document.querySelectorAll('.room-item');
    return roomItems.length >= 1;
}

function updateRemoveButtonStates() {
    const roomItems = document.querySelectorAll('.room-item');
    const removeButtons = document.querySelectorAll('.remove-room-btn');
    const warningElement = document.getElementById('rooms-container').nextElementSibling;
    
    if (roomItems.length <= 1) {
        removeButtons.forEach(btn => {
            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');
            btn.classList.remove('hover:text-red-800');
        });
        // Show warning message
        if (!warningElement || !warningElement.classList.contains('text-red-500')) {
            const warning = document.createElement('p');
            warning.className = 'text-xs text-red-500 mt-2';
            warning.innerHTML = '⚠️ At least 1 room is required. You cannot remove the last room.';
            document.getElementById('current-rooms').appendChild(warning);
        }
    } else {
        removeButtons.forEach(btn => {
            btn.disabled = false;
            btn.classList.remove('opacity-50', 'cursor-not-allowed');
            btn.classList.add('hover:text-red-800');
        });
        // Remove warning message if exists
        if (warningElement && warningElement.classList.contains('text-red-500')) {
            warningElement.remove();
        }
    }
}

function calculateTotalAmount() {
    const checkIn = new Date(document.getElementById('check_in_date').value);
    const checkOut = new Date(document.getElementById('check_out_date').value);
    
    if (!checkIn || !checkOut || checkOut <= checkIn) {
        return;
    }
    
    const nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
    
    // Calculate total from current rooms
    let total = 0;
    document.querySelectorAll('.room-item').forEach(roomItem => {
        const roomId = roomItem.dataset.roomId;
        const price = roomPrices[roomId] || 0;
        total += price * nights;
    });
    
    // Update displays
    document.getElementById('nights-count').textContent = `${nights} night${nights !== 1 ? 's' : ''}`;
    document.getElementById('room-charges').textContent = `₱${total.toLocaleString()}`;
    document.getElementById('total-amount-display').textContent = `₱${total.toLocaleString()}`;
    document.getElementById('total_amount').value = total;
}

function openAddRoomModal() {
    const checkIn = document.getElementById('check_in_date').value;
    const checkOut = document.getElementById('check_out_date').value;
    
    if (!checkIn || !checkOut) {
        alert('Please select check-in and check-out dates first');
        return;
    }
    
    // Load available rooms
    loadAvailableRooms(checkIn, checkOut);
    document.getElementById('add-room-modal').classList.remove('hidden');
}

function closeAddRoomModal() {
    document.getElementById('add-room-modal').classList.add('hidden');
    selectedRooms = [];
    updateSelectedRoomsCount();
}

async function loadAvailableRooms(checkIn, checkOut) {
    try {
        const currentRoomIds = Array.from(document.querySelectorAll('.room-item')).map(item => item.dataset.roomId);
        
        const response = await fetch('{{ route("reservations.available-rooms") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                check_in_date: checkIn,
                check_out_date: checkOut,
                room_type_id: {{ $reservation->room_type_id }},
                exclude_rooms: currentRoomIds
            })
        });

        const data = await response.json();
        
        if (data.success) {
            displayAvailableRooms(data.rooms);
        } else {
            alert('Failed to load available rooms: ' + (data.message || 'Unknown error'));
        }
    } catch (error) {
        console.error('Error loading available rooms:', error);
        alert('Failed to load available rooms. Please check your connection and try again.');
    }
}

function displayAvailableRooms(rooms) {
    const container = document.getElementById('modal-available-rooms');
    container.innerHTML = '';
    
    if (rooms.length === 0) {
        container.innerHTML = `
            <div class="text-center py-8 text-gray-500">
                <svg class="h-12 w-12 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="text-lg font-medium">No rooms available</h3>
                <p class="mt-2">No additional rooms are available for the selected dates.</p>
            </div>
        `;
        return;
    }
    
    rooms.forEach(room => {
        const isSelected = selectedRooms.some(r => r.room_id === room.room_id);
        const roomElement = document.createElement('div');
        roomElement.className = `flex items-center justify-between p-4 border rounded-xl cursor-pointer transition-colors ${
            isSelected ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-blue-300'
        }`;
        roomElement.innerHTML = `
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <span class="text-blue-600 font-semibold">${room.room_number}</span>
                </div>
                <div>
                    <div class="font-medium text-gray-900">Room ${room.room_number}</div>
                    <div class="text-sm text-gray-500">${room.room_type}</div>
                    <div class="text-sm font-semibold text-green-600">₱${room.price.toLocaleString()}/night</div>
                </div>
            </div>
            <div class="flex items-center">
                <input type="checkbox" ${isSelected ? 'checked' : ''} 
                       onchange="toggleRoomSelection(${room.room_id}, '${room.room_number}', '${room.room_type}', ${room.price}, this.checked)"
                       class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500">
            </div>
        `;
        container.appendChild(roomElement);
    });
}

function toggleRoomSelection(roomId, roomNumber, roomType, price, isSelected) {
    if (isSelected) {
        selectedRooms.push({
            room_id: roomId,
            room_number: roomNumber,
            room_type: roomType,
            price: price
        });
    } else {
        selectedRooms = selectedRooms.filter(room => room.room_id !== roomId);
    }
    updateSelectedRoomsCount();
}

function updateSelectedRoomsCount() {
    const countElement = document.getElementById('selected-rooms-count');
    if (countElement) {
        countElement.textContent = selectedRooms.length;
    }
}

function confirmRoomSelection() {
    if (selectedRooms.length === 0) {
        alert('Please select at least one room to add');
        return;
    }
    
    // Add selected rooms to current rooms
    selectedRooms.forEach(room => {
        addRoomToReservation(room);
        roomPrices[room.room_id] = room.price;
    });
    
    // Recalculate total
    calculateTotalAmount();
    
    // Update remove button states
    updateRemoveButtonStates();
    
    closeAddRoomModal();
}

function addRoomToReservation(room) {
    const roomsContainer = document.getElementById('rooms-container');
    
    const roomElement = document.createElement('div');
    roomElement.className = 'flex items-center justify-between p-4 border border-gray-200 rounded-xl bg-white room-item';
    roomElement.dataset.roomId = room.room_id;
    roomElement.innerHTML = `
        <div class="flex items-center space-x-4">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                <span class="text-blue-600 font-semibold">${room.room_number}</span>
            </div>
            <div>
                <div class="font-medium text-gray-900">Room ${room.room_number}</div>
                <div class="text-sm text-gray-500">${room.room_type}</div>
                <div class="text-sm font-semibold text-green-600">₱${room.price.toLocaleString()}/night</div>
            </div>
        </div>
        <button type="button" class="remove-room-btn text-red-600 hover:text-red-800 p-2 transition-colors">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
        </button>
        <input type="hidden" name="new_rooms[]" value="${room.room_id}">
    `;
    
    roomsContainer.appendChild(roomElement);
    
    // Add event listener to remove button
    roomElement.querySelector('.remove-room-btn').addEventListener('click', function() {
        removeRoom(room.room_id, roomElement);
    });
}

function removeRoom(roomId, roomElement) {
    const roomItems = document.querySelectorAll('.room-item');
    
    // Check if this is the last room
    if (roomItems.length <= 1) {
        alert('Error: A reservation must have at least 1 room. You cannot remove the last room.');
        return;
    }
    
    if (confirm('Are you sure you want to remove this room from the reservation?')) {
        roomElement.remove();
        delete roomPrices[roomId];
        calculateTotalAmount();
        updateRemoveButtonStates();
    }
}

function handleFormSubmit(e) {
    // Validate minimum rooms
    if (!validateMinimumRooms()) {
        e.preventDefault(); // Stop form submission
        alert('Error: A reservation must have at least 1 room. Please add at least one room before updating.');
        return;
    }
    
    const updateBtn = document.getElementById('update-btn');
    const originalText = updateBtn.innerHTML;
    
    // Show loading state
    updateBtn.disabled = true;
    updateBtn.innerHTML = `
        <svg class="animate-spin h-5 w-5 mr-2 text-white" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Updating...
    `;
    
    // Form will submit normally after this
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('add-room-modal');
    if (event.target === modal) {
        closeAddRoomModal();
    }
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    calculateTotalAmount();
});
</script>

<style>
.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

.remove-room-btn:disabled {
    cursor: not-allowed;
    opacity: 0.5;
}

.remove-room-btn:disabled:hover {
    color: #dc2626 !important;
}
</style>
@endsection