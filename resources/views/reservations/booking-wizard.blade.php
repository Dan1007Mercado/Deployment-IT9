@extends('layouts.dashboard')

@section('content')
<div class="flex min-h-screen">
    @include('components.sidebar')
    
    <main class="flex-1 bg-[#f8fafc]">
        @include('components.topnav', ['title' => 'New Reservation'])

        <div class="px-8 py-6">
            <div class="max-w-6xl mx-auto">
                <!-- Booking Wizard -->
                <div class="bg-white rounded-lg shadow-lg">
                    <!-- Progress Steps -->
                    <div class="border-b border-gray-200">
                        <div class="px-8 py-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div class="flex flex-col items-center">
                                        <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold">1</div>
                                        <span class="text-sm font-medium mt-2">Guest Details</span>
                                    </div>
                                    <div class="w-16 h-1 bg-gray-300"></div>
                                    <div class="flex flex-col items-center">
                                        <div class="w-10 h-10 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center font-bold">2</div>
                                        <span class="text-sm font-medium mt-2">Dates & Guests</span>
                                    </div>
                                    <div class="w-16 h-1 bg-gray-300"></div>
                                    <div class="flex flex-col items-center">
                                        <div class="w-10 h-10 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center font-bold">3</div>
                                        <span class="text-sm font-medium mt-2">Room Type</span>
                                    </div>
                                    <div class="w-16 h-1 bg-gray-300"></div>
                                    <div class="flex flex-col items-center">
                                        <div class="w-10 h-10 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center font-bold">4</div>
                                        <span class="text-sm font-medium mt-2">Select Rooms</span>
                                    </div>
                                    <div class="w-16 h-1 bg-gray-300"></div>
                                    <div class="flex flex-col items-center">
                                        <div class="w-10 h-10 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center font-bold">5</div>
                                        <span class="text-sm font-medium mt-2">Confirm</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Wizard Content -->
                    <div class="p-8">
                        <!-- Step 1: Guest Details -->
                        <div id="step-1" class="step-content">
                            <h3 class="text-xl font-semibold mb-6">Guest Information</h3>
                            <form id="guest-form" class="grid grid-cols-2 gap-6">
                                @csrf
                                <div>
                                    <label class="block text-sm font-medium mb-1">First Name *</label>
                                    <input type="text" id="first_name" name="first_name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1">Last Name *</label>
                                    <input type="text" id="last_name" name="last_name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200" required>
                                </div>
                                <div class="relative">
                                    <label class="block text-sm font-medium mb-1">Email *</label>
                                    <input type="email" id="email" name="email" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200" 
                                           required
                                           onblur="validateEmail()">
                                    <div id="email-error" class="text-red-500 text-xs mt-1 hidden"></div>
                                    <div id="email-success" class="text-green-500 text-xs mt-1 hidden"></div>
                                </div>
                                <div class="relative">
                                    <label class="block text-sm font-medium mb-1">Contact Number *</label>
                                    <input type="text" 
                                           id="contact_number" 
                                           name="contact_number" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200" 
                                           placeholder="09********1"
                                           oninput="validatePhoneSimple()"
                                           required>
                                    <div id="phone-error" class="text-red-500 text-xs mt-1 hidden"></div>
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium mb-1">Booking Source</label>
                                    <select name="booking_source" id="booking_source" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200" required>
                                        <option value="walk-in">Walk-in</option>
                                        <option value="phone">Phone</option>
                                    </select>
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium mb-1">Special Requests</label>
                                    <textarea name="special_requests" id="special_requests" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200" placeholder="Any special requirements..."></textarea>
                                </div>
                            </form>
                            <div class="flex justify-end mt-8">
                                <button onclick="validateAndProceed()" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Next: Dates & Guests</button>
                            </div>
                        </div>

                        <!-- Step 2: Date & Guest Selection -->
                        <div id="step-2" class="step-content hidden">
                            <h3 class="text-xl font-semibold mb-6">Select Dates & Number of Guests</h3>
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium mb-1">Check-in Date *</label>
                                    <input type="date" id="check_in_date" name="check_in_date" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200" 
                                           min="{{ date('Y-m-d', strtotime('+1 day')) }}" 
                                           onchange="calculateNights()"
                                           required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1">Check-out Date *</label>
                                    <input type="date" id="check_out_date" name="check_out_date" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200" 
                                           onchange="calculateNights()"
                                           required>
                                </div>
                                <div class="relative">
                                    <label class="block text-sm font-medium mb-1">Number of Guests *</label>
                                    <input type="number" id="num_guests" name="num_guests" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200" 
                                           min="1" max="8" value="1" 
                                           oninput="validateGuestCount()"
                                           required>
                                    <div id="guests-error" class="text-red-500 text-xs mt-1 hidden"></div>
                                    <div id="guests-info" class="text-xs text-gray-500 mt-1">
                                        Maximum 8 guests per reservation. Room types will be filtered based on this number.
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1">Nights</label>
                                    <input type="text" id="nights_display" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50" readonly>
                                </div>
                            </div>
                            
                            <!-- Room Capacity Summary -->
                            <div id="capacity-summary" class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg hidden">
                                <h4 class="font-semibold text-blue-800 mb-1 text-sm">Room Capacity Summary</h4>
                                <p class="text-xs text-blue-700" id="capacity-message"></p>
                            </div>
                            
                            <!-- No Rooms Warning -->
                            <div id="no-rooms-warning" class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg hidden">
                                <div class="flex items-start">
                                    <svg class="h-5 w-5 text-yellow-600 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                    </svg>
                                    <div>
                                        <h4 class="font-semibold text-yellow-800 text-sm mb-1">No Suitable Rooms Found</h4>
                                        <p class="text-xs text-yellow-700" id="no-rooms-message"></p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-between mt-8">
                                <button onclick="prevStep(1)" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">Previous</button>
                                <button id="next-step-2" onclick="checkRoomCapacityAndProceed()" class="px-6 py-2 bg-gray-300 text-gray-500 rounded-lg cursor-not-allowed" disabled>Next: Choose Room Type</button>
                            </div>
                        </div>

                        <!-- Step 3: Room Type Selection (FILTERED) -->
                        <div id="step-3" class="step-content hidden">
                            <h3 class="text-xl font-semibold mb-6">Select Room Type</h3>
                            
                            <!-- Filter Info -->
                            <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <span class="font-medium text-blue-800">Filtered for:</span>
                                        <span id="guest-count-display" class="ml-2 font-bold text-blue-900"></span> guests
                                    </div>
                                    <div id="available-room-types-count" class="text-sm text-blue-700">
                                        <!-- Will be populated by JS -->
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Room Types Grid (Will be populated by JS) -->
                            <div id="room-type-selection" class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                                <!-- Room types will be dynamically loaded here -->
                                <div class="col-span-3 text-center py-8">
                                    <div class="animate-pulse">
                                        <div class="h-10 w-10 mx-auto bg-gray-300 rounded-full mb-4"></div>
                                        <div class="h-3 w-48 mx-auto bg-gray-300 rounded mb-2"></div>
                                        <div class="h-3 w-32 mx-auto bg-gray-300 rounded"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- No Rooms Available Message -->
                            <div id="no-room-types-message" class="hidden text-center py-8">
                                <svg class="h-12 w-12 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">No Room Types Available</h3>
                                <p class="text-gray-600 mb-4" id="no-room-types-text"></p>
                                <button onclick="prevStep(2)" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                    Adjust Guest Count or Dates
                                </button>
                            </div>

                            <div class="flex justify-between mt-8">
                                <button onclick="prevStep(2)" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">Previous</button>
                                <button id="next-to-rooms" class="px-6 py-2 bg-gray-300 text-gray-500 rounded-lg cursor-not-allowed" disabled>Next: Select Rooms</button>
                            </div>
                        </div>

                        <!-- Step 4: Room Selection -->
                        <div id="step-4" class="step-content hidden">
                            <h3 class="text-xl font-semibold mb-6">Select Rooms</h3>
                            <div class="mb-6">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <span id="selected-room-type" class="font-semibold"></span>
                                        <span id="available-rooms-count" class="text-sm text-gray-500 ml-2"></span>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        Price: <span id="room-type-price" class="font-semibold"></span>/night
                                    </div>
                                </div>
                            </div>
                            
                            <div id="available-rooms" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                                <!-- Available rooms will be loaded here -->
                            </div>

                            <!-- Selected Rooms Cart -->
                            <div id="selected-rooms-cart" class="bg-gray-50 rounded-lg p-4 mb-6 hidden">
                                <h4 class="font-semibold mb-3">Selected Rooms</h4>
                                <div id="selected-rooms-list" class="space-y-2">
                                    <!-- Selected rooms will appear here -->
                                </div>
                                <div class="mt-3 pt-3 border-t border-gray-200">
                                    <div class="flex justify-between font-semibold">
                                        <span>Subtotal:</span>
                                        <span id="cart-subtotal">₱0.00</span>
                                    </div>
                                    <div class="flex justify-between text-sm text-gray-600">
                                        <span>Nights:</span>
                                        <span id="cart-nights">0</span>
                                    </div>
                                    <div class="flex justify-between font-bold text-lg mt-2">
                                        <span>Total:</span>
                                        <span id="cart-total">₱0.00</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-between mt-8">
                                <button onclick="prevStep(3)" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">Previous</button>
                                <button id="next-to-confirm" class="px-6 py-2 bg-gray-300 text-gray-500 rounded-lg cursor-not-allowed" disabled>Next: Confirm Booking</button>
                            </div>
                        </div>

                        <!-- Step 5: Confirmation -->
                        <div id="step-5" class="step-content hidden">
                            <h3 class="text-xl font-semibold mb-6">Confirm Reservation</h3>
                            <div class="bg-gray-50 rounded-lg p-6 mb-6">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <h4 class="font-semibold mb-2">Guest Information</h4>
                                        <div id="confirm-guest-details" class="text-sm text-gray-600"></div>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold mb-2">Stay Details</h4>
                                        <div id="confirm-stay-details" class="text-sm text-gray-600"></div>
                                    </div>
                                    <div class="col-span-2">
                                        <h4 class="font-semibold mb-2">Selected Rooms</h4>
                                        <div id="confirm-rooms-list" class="text-sm text-gray-600"></div>
                                    </div>
                                    <div class="col-span-2">
                                        <h4 class="font-semibold mb-2">Pricing</h4>
                                        <div id="confirm-pricing" class="text-sm text-gray-600"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-between mt-8">
                                <button onclick="prevStep(4)" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">Previous</button>
                                <button onclick="submitReservation()" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center">
                                    <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Confirm Reservation
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Toast Container -->
<div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

<script>
    // Global variables
    let currentStep = 1;
    let selectedRoomType = null;
    let selectedRooms = [];
    let emailValidated = false;
    let numGuests = 1;
    let allRoomTypes = @json($roomTypes); // Pass PHP room types to JS

    // Simple phone number validation
    function validatePhoneSimple() {
        const phoneInput = document.getElementById('contact_number');
        const phoneError = document.getElementById('phone-error');
        const phone = phoneInput.value.trim();
        
        // Clear previous states
        phoneError.textContent = '';
        phoneError.classList.add('hidden');
        phoneInput.classList.remove('border-red-500', 'border-green-500');
        
        if (phone === '') {
            return false;
        }
        
        const cleaned = phone.replace(/\D/g, '');
        
        if (!cleaned.startsWith('09')) {
            phoneError.textContent = 'Phone number must start with 09';
            phoneError.classList.remove('hidden');
            phoneInput.classList.add('border-red-500');
            return false;
        }
        
        if (cleaned.length !== 11) {
            phoneError.textContent = 'Phone number must be 11 digits (e.g., 09123456789)';
            phoneError.classList.remove('hidden');
            phoneInput.classList.add('border-red-500');
            return false;
        }
        
        phoneInput.classList.add('border-green-500');
        return true;
    }

    // Guest count validation
    function validateGuestCount() {
        const guestsInput = document.getElementById('num_guests');
        const guestsError = document.getElementById('guests-error');
        const nextButton = document.getElementById('next-step-2');
        const guests = parseInt(guestsInput.value);
        
        // Clear previous states
        guestsError.textContent = '';
        guestsError.classList.add('hidden');
        guestsInput.classList.remove('border-red-500', 'border-green-500');
        
        // Disable next button by default
        nextButton.disabled = true;
        nextButton.classList.remove('bg-blue-600', 'text-white', 'hover:bg-blue-700');
        nextButton.classList.add('bg-gray-300', 'text-gray-500', 'cursor-not-allowed');
        
        // Check if empty
        if (!guests || isNaN(guests)) {
            guestsError.textContent = 'Number of guests is required';
            guestsError.classList.remove('hidden');
            guestsInput.classList.add('border-red-500');
            return false;
        }
        
        // Check if within range (1 to 8)
        if (guests < 1 || guests > 8) {
            guestsError.textContent = 'Number of guests must be between 1 and 8';
            guestsError.classList.remove('hidden');
            guestsInput.classList.add('border-red-500');
            return false;
        }
        
        // Store the number of guests
        numGuests = guests;
        
        // If valid, show green border
        guestsInput.classList.add('border-green-500');
        
        // Enable next button only if dates are valid
        const checkIn = document.getElementById('check_in_date').value;
        const checkOut = document.getElementById('check_out_date').value;
        
        if (checkIn && checkOut && new Date(checkOut) > new Date(checkIn)) {
            nextButton.disabled = false;
            nextButton.classList.remove('bg-gray-300', 'text-gray-500', 'cursor-not-allowed');
            nextButton.classList.add('bg-blue-600', 'text-white', 'hover:bg-blue-700');
        }
        
        return true;
    }

    // Check room capacity and proceed to room type selection
    async function checkRoomCapacityAndProceed() {
        const guests = parseInt(document.getElementById('num_guests').value);
        const checkIn = document.getElementById('check_in_date').value;
        const checkOut = document.getElementById('check_out_date').value;
        
        // Validate dates
        if (!checkIn || !checkOut) {
            showToast('Please select check-in and check-out dates', 'error');
            return;
        }
        
        if (new Date(checkOut) <= new Date(checkIn)) {
            showToast('Check-out date must be after check-in date', 'error');
            return;
        }
        
        // Validate guest count
        if (!validateGuestCount()) {
            showToast('Please fix the guest count error', 'error');
            return;
        }
        
        // Show loading
        showToast('Checking room capacities...', 'info');
        
        // Filter room types by capacity
        const suitableRoomTypes = allRoomTypes.filter(roomType => roomType.capacity >= guests);
        
        if (suitableRoomTypes.length === 0) {
            // Show no rooms warning
            document.getElementById('no-rooms-warning').classList.remove('hidden');
            document.getElementById('capacity-summary').classList.add('hidden');
            document.getElementById('no-rooms-message').textContent = 
                `We don't have any room types that can accommodate ${guests} guests. The maximum room capacity is ${Math.max(...allRoomTypes.map(rt => rt.capacity))} guests.`;
            
            // Disable next button
            document.getElementById('next-step-2').disabled = true;
            document.getElementById('next-step-2').classList.remove('bg-blue-600', 'text-white', 'hover:bg-blue-700');
            document.getElementById('next-step-2').classList.add('bg-gray-300', 'text-gray-500', 'cursor-not-allowed');
            
            showToast(`No room types can accommodate ${guests} guests`, 'error');
            return;
        } else {
            // Show capacity summary
            document.getElementById('no-rooms-warning').classList.add('hidden');
            document.getElementById('capacity-summary').classList.remove('hidden');
            
            const maxCapacity = Math.max(...suitableRoomTypes.map(rt => rt.capacity));
            const roomTypesCount = suitableRoomTypes.length;
            
            document.getElementById('capacity-message').textContent = 
                `${roomTypesCount} room type${roomTypesCount !== 1 ? 's' : ''} can accommodate ${guests} guests. Maximum room capacity: ${maxCapacity} guests.`;
            
            // Proceed to room type selection
            nextStep(3);
            
            // Load filtered room types
            loadFilteredRoomTypes(suitableRoomTypes, guests);
        }
    }

    // Load filtered room types
    function loadFilteredRoomTypes(roomTypes, guests) {
        const container = document.getElementById('room-type-selection');
        const guestCountDisplay = document.getElementById('guest-count-display');
        const countDisplay = document.getElementById('available-room-types-count');
        const noRoomsMessage = document.getElementById('no-room-types-message');
        const noRoomsText = document.getElementById('no-room-types-text');
        
        // Update guest count display
        guestCountDisplay.textContent = guests;
        
        // Clear container
        container.innerHTML = '';
        
        if (roomTypes.length === 0) {
            // Show no rooms message
            container.classList.add('hidden');
            noRoomsMessage.classList.remove('hidden');
            noRoomsText.textContent = `No room types can accommodate ${guests} guests. Please adjust your guest count.`;
            countDisplay.textContent = '0 room types available';
            return;
        }
        
        // Show container
        container.classList.remove('hidden');
        noRoomsMessage.classList.add('hidden');
        
        // Update count display
        countDisplay.textContent = `${roomTypes.length} room type${roomTypes.length !== 1 ? 's' : ''} available`;
        
        // Display filtered room types
        roomTypes.forEach(roomType => {
            const roomTypeElement = document.createElement('div');
            roomTypeElement.className = 'border border-gray-200 rounded-lg p-4 cursor-pointer room-type-card hover:border-blue-500 transition-colors';
            roomTypeElement.dataset.roomTypeId = roomType.room_type_id;
            roomTypeElement.innerHTML = `
                <div class="text-center">
                    <h4 class="font-semibold text-lg">${roomType.type_name}</h4>
                    <p class="text-gray-600 text-sm mt-1">${roomType.description}</p>
                    <div class="mt-2">
                        <span class="text-2xl font-bold text-blue-600">₱${roomType.base_price.toLocaleString()}</span>
                        <span class="text-gray-500">/night</span>
                    </div>
                    <div class="mt-2 text-sm">
                        <span class="text-gray-500">Capacity: </span>
                        <span class="font-medium ${roomType.capacity >= numGuests ? 'text-green-600' : 'text-red-600'}">
                            ${roomType.capacity} guest${roomType.capacity !== 1 ? 's' : ''}
                        </span>
                    </div>
                    <div class="mt-4">
                        <button type="button" onclick="selectRoomType(${roomType.room_type_id}, ${roomType.capacity})" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                            Select
                        </button>
                    </div>
                </div>
            `;
            container.appendChild(roomTypeElement);
        });
    }

    // Select room type
    function selectRoomType(roomTypeId, capacity) {
        selectedRoomType = roomTypeId;
        
        // Update UI
        document.querySelectorAll('.room-type-card').forEach(card => {
            const cardRoomTypeId = parseInt(card.dataset.roomTypeId);
            card.classList.remove('border-blue-500', 'bg-blue-50');
            if (cardRoomTypeId === roomTypeId) {
                card.classList.add('border-blue-500', 'bg-blue-50');
            }
        });
        
        // Enable next button
        const nextButton = document.getElementById('next-to-rooms');
        nextButton.disabled = false;
        nextButton.classList.remove('bg-gray-300', 'text-gray-500', 'cursor-not-allowed');
        nextButton.classList.add('bg-blue-600', 'text-white', 'hover:bg-blue-700');
    }

    // Email validation function
    async function validateEmail() {
        const email = document.getElementById('email').value;
        const firstName = document.getElementById('first_name').value;
        const lastName = document.getElementById('last_name').value;
        const emailError = document.getElementById('email-error');
        const emailSuccess = document.getElementById('email-success');
        const emailField = document.getElementById('email');
        
        // Clear previous messages
        emailError.classList.add('hidden');
        emailSuccess.classList.add('hidden');
        emailField.classList.remove('border-red-500', 'border-green-500');
        
        // Basic email format validation
        if (!isValidEmail(email)) {
            if (email) {
                emailError.textContent = 'Please enter a valid email address';
                emailError.classList.remove('hidden');
                emailField.classList.add('border-red-500');
            }
            emailValidated = false;
            return;
        }
        
        try {
            const response = await fetch('{{ route("reservations.check-email") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    email: email,
                    first_name: firstName,
                    last_name: lastName
                })
            });
            
            const data = await response.json();
            
            if (data.conflict) {
                emailError.textContent = data.error_message;
                emailError.classList.remove('hidden');
                emailField.classList.add('border-red-500');
                emailValidated = false;
                showToast(data.error_message, 'error');
            } else if (data.exists) {
                emailSuccess.textContent = `Email found for ${data.guest_name}`;
                emailSuccess.classList.remove('hidden');
                emailField.classList.add('border-green-500');
                emailValidated = true;
                showToast(`Welcome back ${data.guest_name}!`, 'success');
            } else {
                emailSuccess.textContent = 'Email is available';
                emailSuccess.classList.remove('hidden');
                emailField.classList.add('border-green-500');
                emailValidated = true;
            }
        } catch (error) {
            console.error('Email validation error:', error);
            emailError.textContent = 'Failed to validate email. Please try again.';
            emailError.classList.remove('hidden');
            emailField.classList.add('border-red-500');
            emailValidated = false;
        }
    }

    // Validate and proceed to next step
    async function validateAndProceed() {
        // First validate email
        await validateEmail();
        
        const emailError = document.getElementById('email-error');
        const emailField = document.getElementById('email');
        const firstName = document.getElementById('first_name').value;
        const lastName = document.getElementById('last_name').value;
        const contact = document.getElementById('contact_number').value;
        
        // Check if all required fields are filled
        if (!firstName || !lastName || !emailField.value || !contact) {
            showToast('Please fill in all required fields', 'error');
            return;
        }
        
        // Check if email has validation error
        if (!emailValidated || !emailError.classList.contains('hidden')) {
            showToast('Please fix the email error before proceeding', 'error');
            return;
        }
        
        // Validate phone number
        if (!validatePhoneSimple()) {
            showToast('Phone number must start with 09 and be 11 digits', 'error');
            return;
        }
        
        // All good, proceed to next step
        nextStep(2);
    }

    // Toast notification
    function showToast(message, type = 'info') {
        const toastContainer = document.getElementById('toast-container');
        const toastId = 'toast-' + Date.now();
        
        const typeClasses = {
            success: 'bg-green-500 text-white',
            error: 'bg-red-500 text-white',
            info: 'bg-blue-500 text-white',
            warning: 'bg-yellow-500 text-white'
        };
        
        const icon = {
            success: '✓',
            error: '✗',
            info: 'ℹ',
            warning: '⚠'
        };
        
        const toast = document.createElement('div');
        toast.id = toastId;
        toast.className = `${typeClasses[type]} rounded-lg shadow-lg p-4 flex items-center justify-between min-w-80 transform transition-all duration-300 translate-x-full`;
        toast.innerHTML = `
            <div class="flex items-center">
                <span class="font-bold mr-2">${icon[type]}</span>
                <span>${message}</span>
            </div>
            <button onclick="closeToast('${toastId}')" class="ml-4 text-white hover:text-gray-200">
                ✕
            </button>
        `;
        
        toastContainer.appendChild(toast);
        
        // Animate in
        setTimeout(() => {
            toast.classList.remove('translate-x-full');
        }, 10);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            closeToast(toastId);
        }, 5000);
    }
    
    function closeToast(toastId) {
        const toast = document.getElementById(toastId);
        if (toast) {
            toast.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => {
                toast.remove();
            }, 300);
        }
    }

    // Helper function to validate email format
    function isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    // Step navigation
    function nextStep(step) {
        console.log(`Moving from step ${currentStep} to step ${step}`);
        document.getElementById(`step-${currentStep}`).classList.add('hidden');
        document.getElementById(`step-${step}`).classList.remove('hidden');
        currentStep = step;
        updateProgress(step);
        
        if (step === 4) {
            loadAvailableRooms();
        }
        
        if (step === 5) {
            updateConfirmation();
        }
    }

    function prevStep(step) {
        console.log(`Moving back to step ${step}`);
        document.getElementById(`step-${currentStep}`).classList.add('hidden');
        document.getElementById(`step-${step}`).classList.remove('hidden');
        currentStep = step;
        updateProgress(step);
    }

    function updateProgress(step) {
        const steps = document.querySelectorAll('.flex.flex-col.items-center');
        steps.forEach((stepEl, index) => {
            const number = stepEl.querySelector('div');
            if (index < step) {
                number.classList.remove('bg-gray-300', 'text-gray-600');
                number.classList.add('bg-blue-600', 'text-white');
            } else {
                number.classList.remove('bg-blue-600', 'text-white');
                number.classList.add('bg-gray-300', 'text-gray-600');
            }
        });
    }

    // Date calculation
    function calculateNights() {
        const checkIn = new Date(document.getElementById('check_in_date').value);
        const checkOut = new Date(document.getElementById('check_out_date').value);
        
        if (checkIn && checkOut && checkOut > checkIn) {
            const nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
            document.getElementById('nights_display').value = `${nights} night${nights !== 1 ? 's' : ''}`;
            return nights;
        }
        document.getElementById('nights_display').value = '0 nights';
        return 0;
    }

    // Load available rooms
    async function loadAvailableRooms() {
        const checkIn = document.getElementById('check_in_date').value;
        const checkOut = document.getElementById('check_out_date').value;
        
        console.log('Loading available rooms with:', { checkIn, checkOut, selectedRoomType });
        
        if (!checkIn || !checkOut || !selectedRoomType) {
            alert('Please complete all previous steps first');
            return;
        }

        try {
            const response = await fetch('{{ route("reservations.available-rooms") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    check_in_date: checkIn,
                    check_out_date: checkOut,
                    room_type_id: selectedRoomType
                })
            });

            const data = await response.json();
            console.log('Available rooms response:', data);
            
            if (data.success) {
                displayAvailableRooms(data.rooms);
            } else {
                showToast('Failed to load available rooms: ' + (data.message || 'Unknown error'), 'error');
            }
        } catch (error) {
            console.error('Error loading available rooms:', error);
            showToast('Failed to load available rooms. Please check your connection and try again.', 'error');
        }
    }

    // Display available rooms
    function displayAvailableRooms(rooms) {
        const container = document.getElementById('available-rooms');
        const roomTypeName = rooms.length > 0 ? rooms[0].room_type : 'Selected Room Type';
        const roomTypePrice = rooms.length > 0 ? rooms[0].price : 0;
        
        document.getElementById('selected-room-type').textContent = roomTypeName;
        document.getElementById('available-rooms-count').textContent = `(${rooms.length} available)`;
        document.getElementById('room-type-price').textContent = `₱${roomTypePrice.toLocaleString()}`;
        
        container.innerHTML = '';
        
        if (rooms.length === 0) {
            container.innerHTML = `
                <div class="col-span-3 text-center py-8 text-gray-500">
                    <svg class="h-12 w-12 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-lg font-medium">No rooms available</h3>
                    <p class="mt-2">No rooms of this type are available for the selected dates.</p>
                    <button onclick="prevStep(3)" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Choose Different Dates or Room Type
                    </button>
                </div>
            `;
            return;
        }
        
        rooms.forEach(room => {
            const isSelected = selectedRooms.some(r => r.room_id === room.room_id);
            const roomElement = document.createElement('div');
            roomElement.className = `border rounded-lg overflow-hidden cursor-pointer transition-all duration-200 ${
                isSelected ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-blue-300 hover:shadow-md'
            }`;
            
            // Generate room type image path
            const roomTypeName = room.room_type.toLowerCase().replace(/\s+/g, '-');
            const imagePath = `/images/rooms/${roomTypeName}.jpg`;
            
            roomElement.innerHTML = `
                <div class="relative h-32 overflow-hidden bg-gradient-to-br from-blue-100 to-blue-300">
                    <img src="${imagePath}" 
                         alt="${room.room_type}" 
                         class="w-full h-full object-cover"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="absolute inset-0 hidden items-center justify-center bg-gradient-to-br from-blue-100 to-blue-300">
                        <svg class="w-16 h-16 text-blue-200" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                        </svg>
                    </div>
                    <div class="absolute top-2 right-2">
                        <input type="checkbox" ${isSelected ? 'checked' : ''} 
                               onchange="toggleRoomSelection(${room.room_id}, '${room.room_number}', ${room.price}, this.checked)"
                               class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500">
                    </div>
                    <div class="absolute bottom-2 left-2 px-2 py-1 rounded-full text-xs font-semibold bg-white/90 text-gray-700">
                        Floor ${room.floor}
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="font-semibold text-lg">Room ${room.room_number}</h4>
                            <p class="text-sm text-gray-600">${room.room_type}</p>
                            <p class="text-lg font-bold text-green-600 mt-2">₱${room.price.toLocaleString()}/night</p>
                        </div>
                    </div>
                </div>
            `;
            container.appendChild(roomElement);
        });
    }

    // Toggle room selection
    function toggleRoomSelection(roomId, roomNumber, price, isSelected) {
        console.log('Room selection toggled:', roomId, isSelected);
        
        if (isSelected) {
            selectedRooms.push({
                room_id: roomId,
                room_number: roomNumber,
                price: price
            });
        } else {
            selectedRooms = selectedRooms.filter(room => room.room_id !== roomId);
        }
        
        updateSelectedRoomsCart();
    }

    // Update selected rooms cart
    function updateSelectedRoomsCart() {
        const cart = document.getElementById('selected-rooms-cart');
        const list = document.getElementById('selected-rooms-list');
        const nights = calculateNights();
        
        if (selectedRooms.length > 0) {
            cart.classList.remove('hidden');
            
            list.innerHTML = '';
            let subtotal = 0;
            
            selectedRooms.forEach(room => {
                const roomTotal = room.price * nights;
                subtotal += roomTotal;
                
                const roomElement = document.createElement('div');
                roomElement.className = 'flex justify-between items-center py-2';
                roomElement.innerHTML = `
                    <div>
                        <span class="font-medium">${room.room_number}</span>
                        <span class="text-sm text-gray-500 ml-2">₱${room.price.toLocaleString()}/night</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="font-semibold">₱${roomTotal.toLocaleString()}</span>
                        <button onclick="removeRoom(${room.room_id})" class="text-red-600 hover:text-red-800 p-1">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                `;
                list.appendChild(roomElement);
            });
            
            document.getElementById('cart-subtotal').textContent = `₱${subtotal.toLocaleString()}`;
            document.getElementById('cart-nights').textContent = nights;
            document.getElementById('cart-total').textContent = `₱${subtotal.toLocaleString()}`;
            
            // Enable next button
            const nextButton = document.getElementById('next-to-confirm');
            nextButton.disabled = false;
            nextButton.classList.remove('bg-gray-300', 'text-gray-500', 'cursor-not-allowed');
            nextButton.classList.add('bg-blue-600', 'text-white', 'hover:bg-blue-700');
        } else {
            cart.classList.add('hidden');
            const nextButton = document.getElementById('next-to-confirm');
            nextButton.disabled = true;
            nextButton.classList.remove('bg-blue-600', 'text-white', 'hover:bg-blue-700');
            nextButton.classList.add('bg-gray-300', 'text-gray-500', 'cursor-not-allowed');
        }
    }

    // Remove room from selection
    function removeRoom(roomId) {
        console.log('Removing room:', roomId);
        selectedRooms = selectedRooms.filter(room => room.room_id !== roomId);
        updateSelectedRoomsCart();
        
        // Uncheck the checkbox
        const checkbox = document.querySelector(`input[onchange*="${roomId}"]`);
        if (checkbox) {
            checkbox.checked = false;
        }
        
        // Update room card appearance
        const roomCards = document.querySelectorAll('.border.rounded-lg');
        roomCards.forEach(card => {
            if (card.querySelector(`input[onchange*="${roomId}"]`)) {
                card.classList.remove('border-green-500', 'bg-green-50');
                card.classList.add('border-gray-200');
            }
        });
    }

    // Update confirmation details
    function updateConfirmation() {
        const checkIn = document.getElementById('check_in_date').value;
        const checkOut = document.getElementById('check_out_date').value;
        const firstName = document.getElementById('first_name').value;
        const lastName = document.getElementById('last_name').value;
        const email = document.getElementById('email').value;
        const phone = document.getElementById('contact_number').value;
        const bookingSource = document.getElementById('booking_source').value;
        const specialRequests = document.getElementById('special_requests').value;
        const numGuests = document.getElementById('num_guests').value;
        const nights = calculateNights();
        const totalAmount = selectedRooms.reduce((sum, room) => sum + (room.price * nights), 0);
        
        // Guest details
        document.getElementById('confirm-guest-details').innerHTML = `
            <p><strong>Name:</strong> ${firstName} ${lastName}</p>
            <p><strong>Email:</strong> ${email}</p>
            <p><strong>Phone:</strong> ${phone}</p>
            <p><strong>Source:</strong> ${bookingSource}</p>
            ${specialRequests ? `<p><strong>Requests:</strong> ${specialRequests}</p>` : ''}
        `;
        
        // Stay details
        document.getElementById('confirm-stay-details').innerHTML = `
            <p><strong>Check-in:</strong> ${new Date(checkIn).toLocaleDateString()}</p>
            <p><strong>Check-out:</strong> ${new Date(checkOut).toLocaleDateString()}</p>
            <p><strong>Duration:</strong> ${nights} nights</p>
            <p><strong>Guests:</strong> ${numGuests}</p>
        `;
        
        // Rooms list
        document.getElementById('confirm-rooms-list').innerHTML = selectedRooms.map(room => 
            `<div class="flex justify-between py-1 border-b border-gray-100">
                <span>Room ${room.room_number}</span>
                <span>₱${room.price.toLocaleString()}/night</span>
            </div>`
        ).join('');
        
        // Pricing
        document.getElementById('confirm-pricing').innerHTML = `
            <div class="flex justify-between py-1">
                <span>Room charges (${nights} nights):</span>
                <span>₱${totalAmount.toLocaleString()}</span>
            </div>
            <div class="flex justify-between py-2 font-bold border-t mt-2 pt-2 text-lg">
                <span>Total Amount:</span>
                <span>₱${totalAmount.toLocaleString()}</span>
            </div>
        `;
    }

    // Submit reservation
    async function submitReservation() {
        // First, validate email one more time before submission
        await validateEmail();
        
        // Check if email has validation error
        const emailError = document.getElementById('email-error');
        if (!emailValidated || !emailError.classList.contains('hidden')) {
            showToast('Please fix the email error before proceeding', 'error');
            return;
        }
        
        // Validate phone number before submission
        if (!validatePhoneSimple()) {
            showToast('Phone number must start with 09 and be 11 digits', 'error');
            return;
        }
        
        // Validate number of guests
        if (!validateGuestCount()) {
            showToast('Number of guests must be between 1 and 8', 'error');
            return;
        }
        
        const checkIn = document.getElementById('check_in_date').value;
        const checkOut = document.getElementById('check_out_date').value;
        const firstName = document.getElementById('first_name').value;
        const lastName = document.getElementById('last_name').value;
        const email = document.getElementById('email').value;
        const phone = document.getElementById('contact_number').value;
        const bookingSource = document.getElementById('booking_source').value;
        const specialRequests = document.getElementById('special_requests').value;
        const numGuests = document.getElementById('num_guests').value;
        const nights = calculateNights();
        const totalAmount = selectedRooms.reduce((sum, room) => sum + (room.price * nights), 0);
        
        const reservationData = {
            first_name: firstName,
            last_name: lastName,
            email: email,
            contact_number: phone,
            check_in_date: checkIn,
            check_out_date: checkOut,
            num_guests: parseInt(numGuests),
            room_ids: selectedRooms.map(room => room.room_id),
            booking_source: bookingSource,
            special_requests: specialRequests,
            total_amount: totalAmount
        };

        console.log('Submitting reservation:', reservationData);

        try {
            const response = await fetch('{{ route("reservations.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(reservationData)
            });

            const result = await response.json();
            console.log('Reservation response:', result);
            
            if (result.success) {
                showToast('Reservation created successfully!', 'success');
                setTimeout(() => {
                    window.location.href = '{{ route("reservations.index") }}';
                }, 1500);
            } else {
                // Show validation errors if any
                if (result.errors) {
                    let errorMessage = 'Please fix the following errors:\n';
                    for (const field in result.errors) {
                        errorMessage += `• ${result.errors[field][0]}\n`;
                    }
                    showToast(errorMessage, 'error');
                } else {
                    showToast('Failed to create reservation: ' + (result.message || 'Unknown error'), 'error');
                }
            }
        } catch (error) {
            console.error('Error submitting reservation:', error);
            showToast('Failed to create reservation. Please try again.', 'error');
        }
    }

    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Booking wizard initialized');
        
        // Initialize guest count validation
        validateGuestCount();
        
        // Date change listeners
        document.getElementById('check_in_date').addEventListener('change', function() {
            const checkOut = document.getElementById('check_out_date');
            if (checkOut.value && new Date(checkOut.value) <= new Date(this.value)) {
                const nextDay = new Date(this.value);
                nextDay.setDate(nextDay.getDate() + 1);
                checkOut.value = nextDay.toISOString().split('T')[0];
            }
            calculateNights();
            validateGuestCount(); // Re-enable/disable next button
        });
        
        document.getElementById('check_out_date').addEventListener('change', function() {
            calculateNights();
            validateGuestCount(); // Re-enable/disable next button
        });

        // Add click handler to the Next button in step 3
        document.getElementById('next-to-rooms').addEventListener('click', function() {
            if (!selectedRoomType) {
                showToast('Please select a room type first', 'error');
                return;
            }
            
            // Validate dates
            const checkIn = document.getElementById('check_in_date').value;
            const checkOut = document.getElementById('check_out_date').value;
            
            if (!checkIn || !checkOut) {
                showToast('Please select check-in and check-out dates first', 'error');
                prevStep(2);
                return;
            }
            
            if (new Date(checkOut) <= new Date(checkIn)) {
                showToast('Check-out date must be after check-in date', 'error');
                prevStep(2);
                return;
            }
            
            nextStep(4);
        });
        
        document.getElementById('next-to-confirm').addEventListener('click', function () {
            console.log("Next to confirm clicked.");

            if (selectedRooms.length === 0) {
                showToast('Please select at least one room first.', 'error');
                return;
            }

            nextStep(5); // go to confirmation step
        });
        
        // Real-time email validation on input
        let emailTimeout;
        document.getElementById('email').addEventListener('input', function() {
            clearTimeout(emailTimeout);
            emailTimeout = setTimeout(() => {
                validateEmail();
            }, 500);
        });
        
        // Real-time phone validation on input
        let phoneTimeout;
        document.getElementById('contact_number').addEventListener('input', function() {
            clearTimeout(phoneTimeout);
            phoneTimeout = setTimeout(() => {
                validatePhoneSimple();
            }, 500);
        });
        
        // Real-time guest count validation
        document.getElementById('num_guests').addEventListener('input', function() {
            validateGuestCount();
        });
    });

    // Initialize
    updateProgress(1);
</script>

<style>
.step-content {
    transition: all 0.3s ease;
}

.room-type-card.selected {
    border-color: #3b82f6;
    background-color: #eff6ff;
}

/* Toast animations */
@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOutRight {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}

#toast-container > div {
    animation: slideInRight 0.3s ease-out;
}

#toast-container > div.removing {
    animation: slideOutRight 0.3s ease-in;
}
</style>
@endsection