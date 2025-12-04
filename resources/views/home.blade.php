<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Azure Grand Hotel - Luxury Accommodation' }}</title>
    
    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>
    
    {{-- Lucide Icons --}}
    <script src="https://unpkg.com/lucide@latest"></script>
    
    {{-- Custom Styles --}}
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        * {
            font-family: 'Inter', sans-serif;
        }
        
        .animate-fade-in {
            animation: fadeIn 1s ease-out;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .hover-lift {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .hover-lift:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        /* Custom scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        
        /* Smooth scroll behavior */
        html {
            scroll-behavior: smooth;
        }
        
        /* Scroll animation */
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-slide-up {
            animation: slideUp 0.6s ease-out forwards;
        }
        
        /* Toast notifications */
        .toast {
            animation: slideInRight 0.3s ease-out;
        }
        
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

        /* Room selection styles */
        .room-checkbox:checked + .room-card-content {
            border-color: #10b981;
            background-color: #f0fdf4;
        }

        .room-checkbox {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }
    </style>
</head>
<body class="min-h-screen bg-gray-50 text-gray-900">
    {{-- Main Content --}}
    <main>
        {{-- Hero Section --}}
        <section class="relative h-[70vh] min-h-[500px] flex items-center justify-center overflow-hidden">
            <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ asset('images/hero-hotel.jpg') }}')">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-900/95 to-blue-800/70"></div>
            </div>
            
            <div class="relative z-10 text-center px-4 max-w-4xl mx-auto">
                <h1 class="text-4xl md:text-6xl font-bold text-white mb-6 animate-fade-in">
                    Welcome to Azure Grand Hotel
                </h1>
                <p class="text-lg md:text-xl text-blue-100 mb-8 max-w-2xl mx-auto">
                    Experience luxury and comfort in the heart of the city. Book your perfect stay today.
                </p>
                <a href="#rooms" class="inline-flex items-center justify-center rounded-md text-lg px-8 py-6 bg-blue-600 text-white hover:bg-blue-700 transition-colors shadow-lg hover-lift">
                    Explore Our Rooms
                </a>
            </div>
        </section>

        {{-- Room Types Section --}}
        <section id="rooms" class="py-16 px-4 bg-gradient-to-b from-white to-blue-50">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4 animate-slide-up">Our Rooms & Suites</h2>
                    <p class="text-lg md:text-xl text-gray-600 max-w-2xl mx-auto animate-slide-up" style="animation-delay: 0.2s;">
                        Each room is thoughtfully designed with premium amenities to ensure a memorable stay
                    </p>
                </div>

                {{-- Room Types Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @php
                        // Fetch room types from database
                        $roomTypes = \App\Models\RoomType::all();
                    @endphp
                    
                    @foreach($roomTypes as $roomType)
                        @php
                            // Count available rooms for this type
                            $availableRooms = \App\Models\Room::where('room_type_id', $roomType->room_type_id)
                                ->where('room_status', 'available')
                                ->count();
                        @endphp
                        
                        <div class="overflow-hidden hover:shadow-xl transition-all duration-300 border border-gray-200 bg-white rounded-lg hover-lift animate-slide-up" 
                             style="animation-delay: {{ $loop->index * 0.1 }}s;">
                            <div class="relative h-56 overflow-hidden">
                                <div class="w-full h-full bg-gradient-to-br from-blue-100 to-blue-300 flex items-center justify-center">
                                    <svg class="w-24 h-24 text-blue-200" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                                    </svg>
                                </div>
                                <!-- Price Tag -->
                                <div class="absolute top-4 right-4 bg-white px-3 py-1.5 rounded-lg shadow-sm">
                                    <span class="text-xl font-bold text-blue-600">₱{{ number_format($roomType->base_price) }}</span>
                                    <span class="text-gray-500 text-xs">/night</span>
                                </div>
                                <!-- Type Badge -->
                                <div class="absolute top-4 left-4 px-3 py-1 rounded-full text-xs font-semibold bg-blue-500 text-white">
                                    {{ $roomType->type_name }}
                                </div>
                                <!-- Available Rooms -->
                                <div class="absolute bottom-4 left-4 px-3 py-1 rounded-full text-xs font-semibold bg-white/90 text-gray-700">
                                    {{ $availableRooms }} available
                                </div>
                            </div>
                            
                            <div class="p-5">
                                <h3 class="text-xl font-bold mb-2">{{ $roomType->type_name }}</h3>
                                <div class="flex items-center gap-2 text-sm text-gray-600 mb-3">
                                    <i data-lucide="users" class="h-4 w-4"></i>
                                    <span>Up to {{ $roomType->capacity }} guests</span>
                                </div>
                                
                                <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $roomType->description }}</p>
                                
                                <div class="flex flex-wrap gap-1.5 mb-4">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium text-gray-800" style="background-color: #C8D5E1;">
                                        <i data-lucide="wifi" class="h-3 w-3 inline mr-1"></i> WiFi
                                    </span>
                                    <span class="px-2 py-1 rounded-full text-xs font-medium text-gray-800" style="background-color: #C8D5E1;">
                                        <i data-lucide="snowflake" class="h-3 w-3 inline mr-1"></i> AC
                                    </span>
                                    @if($roomType->base_price > 3000)
                                    <span class="px-2 py-1 rounded-full text-xs font-medium text-gray-800" style="background-color: #C8D5E1;">
                                        <i data-lucide="tv" class="h-3 w-3 inline mr-1"></i> Premium TV
                                    </span>
                                    @endif
                                </div>
                                
                                <div class="text-2xl font-bold text-blue-600 mb-4">
                                    ₱{{ number_format($roomType->base_price) }}
                                    <span class="text-sm font-normal text-gray-600"> / night</span>
                                </div>
                                
                                <button 
                                    onclick="startBooking({{ $roomType->room_type_id }})"
                                    class="w-full inline-flex items-center justify-center rounded-md px-4 py-2.5 bg-blue-600 text-white hover:bg-blue-700 transition-colors shadow-sm hover:shadow-md hover-lift text-sm"
                                >
                                    <i data-lucide="calendar" class="h-4 w-4 mr-2"></i>
                                    Book Now
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Other sections remain the same... --}}
        <!-- ... rest of your sections (about, amenities, contact, etc.) ... -->
    </main>

    {{-- Toast Container --}}
    <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

    {{-- Booking Flow Modals --}}
    
    <!-- Guest + Dates Modal -->
    <div id="guestDatesModal" class="hidden fixed inset-0 z-50 bg-black/80" onclick="closeModalOnBackdrop('guestDatesModal')">
        <div class="fixed left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-lg bg-white rounded-lg shadow-lg p-6 max-h-[90vh] overflow-y-auto custom-scrollbar" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold">Guest Information & Dates</h2>
                <button onclick="closeModal('guestDatesModal')" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="space-y-6">
                <!-- Guest Information -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Guest Information</h3>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">First Name *</label>
                            <input type="text" id="guestFirstName" name="first_name" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Last Name *</label>
                            <input type="text" id="guestLastName" name="last_name" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                    </div>

                    <div class="relative mb-4">
                        <label class="block text-sm font-medium mb-1">Email *</label>
                        <input type="email" id="guestEmail" name="email" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               required
                               onblur="validateGuestEmail()">
                        <div id="guestEmailError" class="text-red-500 text-xs mt-1 hidden"></div>
                        <div id="guestEmailSuccess" class="text-green-500 text-xs mt-1 hidden"></div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">Contact Number *</label>
                        <input type="tel" id="guestPhone" name="contact_number" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Special Requests (Optional)</label>
                        <textarea id="guestRequests" name="special_requests" rows="2"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Any special requirements..."></textarea>
                    </div>
                </div>

                <!-- Dates Selection -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Stay Details</h3>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Check-in Date *</label>
                            <input type="date" id="checkInDate" name="check_in_date" 
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Check-out Date *</label>
                            <input type="date" id="checkOutDate" name="check_out_date" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Number of Guests *</label>
                            <input type="number" id="numGuests" name="num_guests" min="1" value="1"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Number of Rooms *</label>
                            <input type="number" id="numRooms" name="num_rooms" min="1" max="5" value="1"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                    </div>

                    <div class="mt-2">
                        <label class="block text-sm font-medium mb-1">Nights</label>
                        <input type="text" id="nightsDisplay" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50" readonly>
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button onclick="checkAvailabilityAndProceed()" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Next: Select Rooms
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Rooms Selection Modal -->
    <div id="roomsModal" class="hidden fixed inset-0 z-50 bg-black/80" onclick="closeModalOnBackdrop('roomsModal')">
        <div class="fixed left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-6xl bg-white rounded-lg shadow-lg p-6 max-h-[90vh] overflow-y-auto custom-scrollbar" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold">Select Rooms</h2>
                <button onclick="closeModal('roomsModal')" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="mb-6">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <span id="selectedRoomTypeName" class="font-semibold text-lg"></span>
                        <span id="availableRoomsCount" class="text-sm text-gray-500 ml-2"></span>
                    </div>
                    <div class="text-sm text-gray-500">
                        Price: <span id="roomTypePrice" class="font-semibold"></span>/night
                        | Select up to: <span id="maxRoomsToSelect" class="font-semibold text-blue-600">1</span> rooms
                    </div>
                </div>
                
                <!-- Selected Rooms Counter -->
                <div class="mb-4">
                    <div class="flex items-center space-x-4">
                        <div class="text-sm">
                            <span class="font-medium text-gray-700">Selected:</span>
                            <span id="selectedRoomsCounter" class="ml-2 font-bold text-green-600">0</span>
                            <span>/</span>
                            <span id="totalRoomsNeeded" class="font-medium">1</span>
                        </div>
                        <div class="flex-1">
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div id="selectionProgress" class="bg-green-600 h-2.5 rounded-full" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Available Rooms Grid -->
            <div id="availableRoomsList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mb-6">
                <!-- Available rooms will be loaded here -->
                <div class="col-span-full text-center py-8 text-gray-500">
                    <div class="animate-pulse">
                        <div class="h-12 w-12 mx-auto bg-gray-300 rounded-full mb-4"></div>
                        <div class="h-4 w-48 mx-auto bg-gray-300 rounded mb-2"></div>
                        <div class="h-4 w-32 mx-auto bg-gray-300 rounded"></div>
                    </div>
                </div>
            </div>

            <!-- Selected Rooms Cart -->
            <div id="selectedRoomsCart" class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6 hidden">
                <h4 class="font-semibold mb-3 text-green-800">Selected Rooms</h4>
                <div id="selectedRoomsList" class="space-y-2">
                    <!-- Selected rooms will appear here -->
                </div>
                <div class="mt-3 pt-3 border-t border-green-200">
                    <div class="flex justify-between font-semibold">
                        <span>Subtotal:</span>
                        <span id="cartSubtotal">₱0.00</span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Nights:</span>
                        <span id="cartNights">0</span>
                    </div>
                    <div class="flex justify-between font-bold text-lg mt-2">
                        <span>Total Amount:</span>
                        <span id="cartTotal" class="text-green-700">₱0.00</span>
                    </div>
                </div>
            </div>

            <div class="flex justify-between">
                <button onclick="showGuestDatesModal()" 
                        class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Back
                </button>
                <button id="proceedToPaymentBtn" onclick="showPaymentModal()"
                        class="px-6 py-2 bg-gray-300 text-gray-500 rounded-lg cursor-not-allowed" disabled>
                    Proceed to Payment
                </button>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div id="paymentModal" class="hidden fixed inset-0 z-50 bg-black/80" onclick="closeModalOnBackdrop('paymentModal')">
        <div class="fixed left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-2xl bg-white rounded-lg shadow-lg p-6 max-h-[90vh] overflow-y-auto custom-scrollbar" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold">Complete Your Booking</h2>
                <button onclick="closeModal('paymentModal')" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="space-y-6">
                <!-- Booking Summary -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="font-semibold mb-3">Booking Summary</h4>
                    <div id="bookingSummary" class="space-y-2 text-sm">
                        <!-- Booking summary will be populated here -->
                    </div>
                </div>

                <!-- Payment Methods -->
                <div>
                    <label class="block text-sm font-medium mb-3">Payment Method *</label>
                    <div class="space-y-3">
                        <!-- Cash -->
                        <label class="flex items-center space-x-3 border rounded-lg p-4 cursor-pointer hover:bg-gray-50 transition-colors payment-method">
                            <input type="radio" name="paymentMethod" value="credit_card" checked class="w-4 h-4 text-blue-600">
                            <div class="flex-1">
                                <div class="font-medium">Pay Using (Credit Card)</div>
                                <div class="text-sm text-gray-600">Pay Using Credit Card</div>
                            </div>
                        </label>

                        <!-- Online -->
                        <label class="flex items-center space-x-3 border rounded-lg p-4 cursor-pointer hover:bg-gray-50 transition-colors payment-method">
                            <input type="radio" name="paymentMethod" value="online" class="w-4 h-4 text-blue-600">
                            <div class="flex-1">
                                <div class="font-medium">Online Payment</div>
                                <div class="text-sm text-gray-600">Pay now via Stripe</div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Terms Agreement -->
                <div>
                    <label class="flex items-start space-x-3">
                        <input type="checkbox" id="agreeTerms" required class="w-4 h-4 mt-1 text-blue-600">
                        <div class="text-sm text-gray-700">
                            I agree to the <a href="#" class="text-blue-600 hover:underline">Terms and Conditions</a> and 
                            <a href="#" class="text-blue-600 hover:underline">Cancellation Policy</a> of Azure Grand Hotel.
                        </div>
                    </label>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-between pt-4">
                    <button onclick="showRoomsModal()" 
                            class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        Back to Rooms
                    </button>
                    <button onclick="completeBooking()" 
                            class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Complete Booking
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="hidden fixed inset-0 z-50 bg-black/80" onclick="closeModalOnBackdrop('successModal')">
        <div class="fixed left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white rounded-lg shadow-lg overflow-hidden" onclick="event.stopPropagation()">
            <div class="bg-gradient-to-br from-green-50 to-white p-8">
                <div class="text-center mb-6">
                    <div class="w-20 h-20 mx-auto mb-4 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Booking Confirmed!</h2>
                    <p class="text-gray-600">Your reservation has been successfully created</p>
                </div>

                <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Reference:</span>
                            <span id="successRef" class="font-medium">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Status:</span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                ✓ CONFIRMED
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total:</span>
                            <span id="successTotal" class="font-bold text-green-600">-</span>
                        </div>
                    </div>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <h4 class="font-semibold text-blue-800 mb-2">Next Steps</h4>
                    <ul class="text-sm text-blue-700 space-y-2">
                        <li class="flex items-start">
                            <svg class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            A confirmation email has been sent to your email address
                        </li>
                        <li class="flex items-start">
                            <svg class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Please present your ID at check-in
                        </li>
                    </ul>
                </div>

                <div class="flex gap-3">
                    <button onclick="closeModal('successModal')"
                            class="flex-1 inline-flex items-center justify-center rounded-md px-4 py-3 border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 transition-colors font-medium">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    <script>
        // Global variables
        let selectedRoomTypeId = null;
        let selectedRooms = [];
        let bookingData = {};
        let tempReference = null;
        let maxRoomsToSelect = 1;
        let availableRoomsData = [];

        // Initialize Lucide icons
        lucide.createIcons();

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
            
            const toast = document.createElement('div');
            toast.id = toastId;
            toast.className = `${typeClasses[type]} rounded-lg shadow-lg p-4 flex items-center justify-between min-w-80 transform transition-all duration-300 translate-x-full toast`;
            toast.innerHTML = `
                <div class="flex items-center">
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

        // Start booking process
        function startBooking(roomTypeId) {
            console.log('Starting booking for room type:', roomTypeId);
            selectedRoomTypeId = roomTypeId;
            
            // Reset previous selections
            selectedRooms = [];
            bookingData = {};
            tempReference = null;
            
            // Get room type info for display
            const roomCard = document.querySelector(`[onclick="startBooking(${roomTypeId})"]`).closest('.overflow-hidden');
            const roomTypeName = roomCard.querySelector('h3').textContent;
            const roomTypePrice = roomCard.querySelector('.text-2xl').textContent.replace('₱', '').replace(',', '').split(' ')[0];
            
            // Store for later use
            bookingData.room_type_name = roomTypeName;
            bookingData.room_type_price = parseFloat(roomTypePrice);
            
            // Show guest + dates modal
            showGuestDatesModal();
        }

        // Modal functions
        function showGuestDatesModal() {
            closeAllModals();
            document.getElementById('guestDatesModal').classList.remove('hidden');
            
            // Set default dates
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            const dayAfter = new Date();
            dayAfter.setDate(dayAfter.getDate() + 2);
            
            document.getElementById('checkInDate').value = tomorrow.toISOString().split('T')[0];
            document.getElementById('checkOutDate').value = dayAfter.toISOString().split('T')[0];
            
            // Calculate nights
            calculateNights();
        }

        function showRoomsModal() {
            closeAllModals();
            document.getElementById('roomsModal').classList.remove('hidden');
            
            // Display room type info
            document.getElementById('selectedRoomTypeName').textContent = bookingData.room_type_name;
            document.getElementById('roomTypePrice').textContent = `₱${bookingData.room_type_price.toLocaleString()}`;
            document.getElementById('maxRoomsToSelect').textContent = maxRoomsToSelect;
            document.getElementById('totalRoomsNeeded').textContent = maxRoomsToSelect;
            
            // Update selected rooms counter
            updateSelectedRoomsCounter();
        }

        function showPaymentModal() {
            closeAllModals();
            document.getElementById('paymentModal').classList.remove('hidden');
            
            // Update booking summary
            updateBookingSummary();
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        function closeModalOnBackdrop(modalId) {
            if (event.target.id === modalId || event.target.id.includes(modalId)) {
                closeModal(modalId);
            }
        }

        function closeAllModals() {
            document.getElementById('guestDatesModal').classList.add('hidden');
            document.getElementById('roomsModal').classList.add('hidden');
            document.getElementById('paymentModal').classList.add('hidden');
            document.getElementById('successModal').classList.add('hidden');
        }

        // Validate guest email
        async function validateGuestEmail() {
            const email = document.getElementById('guestEmail').value;
            const firstName = document.getElementById('guestFirstName').value;
            const lastName = document.getElementById('guestLastName').value;
            const emailError = document.getElementById('guestEmailError');
            const emailSuccess = document.getElementById('guestEmailSuccess');
            const emailField = document.getElementById('guestEmail');
            
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
                return false;
            }
            
            try {
                const response = await fetch('/reservations/check-email', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        email: email,
                        first_name: firstName,
                        last_name: lastName
                    })
                });
                
                const data = await response.json();
                
                if (data.conflict) {
                    emailError.textContent = data.error_message || 'Email already registered to a different person';
                    emailError.classList.remove('hidden');
                    emailField.classList.add('border-red-500');
                    return false;
                } else if (data.exists) {
                    emailSuccess.textContent = `Welcome back ${data.guest_name}!`;
                    emailSuccess.classList.remove('hidden');
                    emailField.classList.add('border-green-500');
                } else {
                    emailSuccess.textContent = 'Email is available';
                    emailSuccess.classList.remove('hidden');
                    emailField.classList.add('border-green-500');
                }
                return true;
            } catch (error) {
                console.error('Email validation error:', error);
                return true;
            }
        }

        // Calculate nights
        function calculateNights() {
            const checkIn = new Date(document.getElementById('checkInDate').value);
            const checkOut = new Date(document.getElementById('checkOutDate').value);
            
            if (checkIn && checkOut && checkOut > checkIn) {
                const nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
                document.getElementById('nightsDisplay').value = `${nights} night${nights !== 1 ? 's' : ''}`;
                return nights;
            }
            return 0;
        }

        // Check availability and proceed to room selection
        async function checkAvailabilityAndProceed() {
            // Validate guest info first
            const firstName = document.getElementById('guestFirstName').value;
            const lastName = document.getElementById('guestLastName').value;
            const email = document.getElementById('guestEmail').value;
            const phone = document.getElementById('guestPhone').value;
            const checkIn = document.getElementById('checkInDate').value;
            const checkOut = document.getElementById('checkOutDate').value;
            const numGuests = document.getElementById('numGuests').value;
            const numRooms = document.getElementById('numRooms').value;
            
            // Basic validation
            if (!firstName || !lastName || !email || !phone || !checkIn || !checkOut) {
                showToast('Please fill in all required fields', 'error');
                return;
            }
            
            if (new Date(checkOut) <= new Date(checkIn)) {
                showToast('Check-out date must be after check-in date', 'error');
                return;
            }
            
            // Validate email
            const emailValid = await validateGuestEmail();
            if (!emailValid) {
                showToast('Please fix the email error before proceeding', 'error');
                return;
            }
            
            // Check availability
            showToast('Checking availability...', 'info');
            
            try {
                const response = await fetch('/hotel/check-availability', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        check_in_date: checkIn,
                        check_out_date: checkOut,
                        room_type_id: selectedRoomTypeId,
                        num_rooms: parseInt(numRooms) // Send the number of rooms requested
                    })
                });
                
                const data = await response.json();
                console.log('Availability response:', data);
                
                if (data.success) {
                    if (data.is_available) {
                        // Store booking data
                        bookingData.first_name = firstName;
                        bookingData.last_name = lastName;
                        bookingData.email = email;
                        bookingData.contact_number = phone;
                        bookingData.special_requests = document.getElementById('guestRequests').value;
                        bookingData.check_in_date = checkIn;
                        bookingData.check_out_date = checkOut;
                        bookingData.num_guests = parseInt(numGuests);
                        bookingData.num_rooms = parseInt(numRooms);
                        bookingData.nights = calculateNights();
                        bookingData.total_amount = data.total_amount;
                        bookingData.room_type_price = data.room_type.base_price;
                        
                        // Set max rooms to select
                        maxRoomsToSelect = parseInt(numRooms);
                        
                        // Load available rooms for selection
                        await loadAvailableRoomsForSelection();
                    } else {
                        showToast(`Only ${data.available_rooms} rooms available (needed: ${numRooms})`, 'error');
                    }
                } else {
                    showToast('Failed to check availability: ' + (data.message || 'Unknown error'), 'error');
                }
            } catch (error) {
                console.error('Availability check error:', error);
                showToast('Failed to check availability. Please try again.', 'error');
            }
        }

        // Load available rooms for selection (like booking wizard)
        async function loadAvailableRoomsForSelection() {
            showToast('Loading available rooms...', 'info');
            
            try {
                // Get ALL available rooms for selection
                const response = await fetch('/hotel/check-availability', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        check_in_date: bookingData.check_in_date,
                        check_out_date: bookingData.check_out_date,
                        room_type_id: selectedRoomTypeId,
                        num_rooms: 20 // Get many rooms for selection
                    })
                });

                const data = await response.json();
                console.log('Available rooms response:', data);
                
                if (data.success) {
                    // Store available rooms data
                    availableRoomsData = data.available_rooms_list || [];
                    
                    // Display available rooms for selection
                    displayAvailableRoomsForSelection(availableRoomsData);
                    showRoomsModal();
                } else {
                    showToast('Failed to load available rooms: ' + (data.message || 'Unknown error'), 'error');
                }
            } catch (error) {
                console.error('Error loading available rooms:', error);
                showToast('Failed to load available rooms. Please try again.', 'error');
            }
        }

        // Display available rooms for selection (like booking wizard)
        function displayAvailableRoomsForSelection(rooms) {
            const container = document.getElementById('availableRoomsList');
            
            document.getElementById('availableRoomsCount').textContent = `(${rooms.length} available)`;
            
            container.innerHTML = '';
            
            if (rooms.length === 0) {
                container.innerHTML = `
                    <div class="col-span-4 text-center py-8 text-gray-500">
                        <svg class="h-12 w-12 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="text-lg font-medium">No rooms available</h3>
                        <p class="mt-2">No rooms of this type are available for the selected dates.</p>
                        <button onclick="showGuestDatesModal()" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Choose Different Dates
                        </button>
                    </div>
                `;
                return;
            }
            
            rooms.forEach(room => {
                const isSelected = selectedRooms.some(r => r.room_id === room.room_id);
                const roomElement = document.createElement('div');
                roomElement.className = 'relative';
                roomElement.innerHTML = `
                    <input type="checkbox" id="room-${room.room_id}" 
                           class="room-checkbox" 
                           ${isSelected ? 'checked' : ''}
                           ${selectedRooms.length >= maxRoomsToSelect && !isSelected ? 'disabled' : ''}
                           onchange="toggleRoomSelection(${room.room_id}, '${room.room_number}', ${room.floor}, ${bookingData.room_type_price})">
                    <label for="room-${room.room_id}" class="cursor-pointer">
                        <div class="room-card-content border rounded-lg p-4 transition-all duration-200 ${
                            isSelected ? 'border-green-500 bg-green-50' : 
                            selectedRooms.length >= maxRoomsToSelect ? 'opacity-50 border-gray-200 bg-gray-50' : 
                            'border-gray-200 bg-white hover:border-blue-300 hover:shadow-md'
                        }">
                            <div class="mb-3">
                                <h4 class="font-semibold text-lg">Room ${room.room_number}</h4>
                                <p class="text-sm text-gray-600">Floor ${room.floor}</p>
                            </div>
                            <div class="space-y-2">
                                <p class="text-lg font-bold text-green-600">₱${bookingData.room_type_price.toLocaleString()}/night</p>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Select room</span>
                                    ${isSelected ? 
                                        '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">✓ Selected</span>' : 
                                        ''}
                                </div>
                            </div>
                        </div>
                    </label>
                `;
                container.appendChild(roomElement);
            });
            
            // Update UI
            updateSelectedRoomsCart();
            updateSelectedRoomsCounter();
        }

        // Toggle room selection
        function toggleRoomSelection(roomId, roomNumber, floor, price) {
            const index = selectedRooms.findIndex(r => r.room_id === roomId);
            
            if (index === -1) {
                // Add room if we haven't reached the limit
                if (selectedRooms.length < maxRoomsToSelect) {
                    selectedRooms.push({
                        room_id: roomId,
                        room_number: roomNumber,
                        floor: floor,
                        price: price
                    });
                } else {
                    showToast(`You can only select up to ${maxRoomsToSelect} rooms`, 'warning');
                    return;
                }
            } else {
                // Remove room
                selectedRooms.splice(index, 1);
            }
            
            // Update UI
            updateSelectedRoomsCart();
            updateSelectedRoomsCounter();
            
            // Re-render rooms to update disabled states
            displayAvailableRoomsForSelection(availableRoomsData);
        }

        // Update selected rooms counter
        function updateSelectedRoomsCounter() {
            const counter = document.getElementById('selectedRoomsCounter');
            const progress = document.getElementById('selectionProgress');
            
            counter.textContent = selectedRooms.length;
            
            // Update progress bar
            const progressPercent = (selectedRooms.length / maxRoomsToSelect) * 100;
            progress.style.width = `${progressPercent}%`;
            
            // Enable/disable proceed button
            const proceedBtn = document.getElementById('proceedToPaymentBtn');
            if (selectedRooms.length === maxRoomsToSelect) {
                proceedBtn.disabled = false;
                proceedBtn.classList.remove('bg-gray-300', 'text-gray-500', 'cursor-not-allowed');
                proceedBtn.classList.add('bg-blue-600', 'text-white', 'hover:bg-blue-700');
            } else {
                proceedBtn.disabled = true;
                proceedBtn.classList.remove('bg-blue-600', 'text-white', 'hover:bg-blue-700');
                proceedBtn.classList.add('bg-gray-300', 'text-gray-500', 'cursor-not-allowed');
            }
        }

        // Update selected rooms cart
        function updateSelectedRoomsCart() {
            const cart = document.getElementById('selectedRoomsCart');
            const list = document.getElementById('selectedRoomsList');
            const nights = bookingData.nights || 0;
            
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
                            <span class="font-medium">Room ${room.room_number}</span>
                            <span class="text-sm text-gray-500 ml-2">(Floor ${room.floor})</span>
                            <span class="text-sm text-gray-500 ml-2">₱${room.price.toLocaleString()}/night</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="font-semibold">₱${roomTotal.toLocaleString()}</span>
                            <button onclick="removeRoomFromSelection(${room.room_id})" class="text-red-600 hover:text-red-800 p-1">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    `;
                    list.appendChild(roomElement);
                });
                
                document.getElementById('cartSubtotal').textContent = `₱${subtotal.toLocaleString()}`;
                document.getElementById('cartNights').textContent = nights;
                document.getElementById('cartTotal').textContent = `₱${subtotal.toLocaleString()}`;
                
                // Update booking data
                bookingData.total_amount = subtotal;
                bookingData.room_ids = selectedRooms.map(r => r.room_id);
                bookingData.selected_rooms = selectedRooms;
            } else {
                cart.classList.add('hidden');
            }
        }

        // Remove room from selection
        function removeRoomFromSelection(roomId) {
            selectedRooms = selectedRooms.filter(room => room.room_id !== roomId);
            
            // Update UI
            updateSelectedRoomsCart();
            updateSelectedRoomsCounter();
            
            // Uncheck the checkbox
            const checkbox = document.getElementById(`room-${roomId}`);
            if (checkbox) {
                checkbox.checked = false;
            }
            
            // Re-render rooms
            displayAvailableRoomsForSelection(availableRoomsData);
        }

        // Update booking summary for payment
        function updateBookingSummary() {
            const summaryDiv = document.getElementById('bookingSummary');
            const nights = bookingData.nights || 0;
            const totalAmount = bookingData.total_amount || 0;
            
            summaryDiv.innerHTML = `
                <div class="flex justify-between">
                    <span class="text-gray-600">Guest:</span>
                    <span class="font-medium">${bookingData.first_name} ${bookingData.last_name}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Email:</span>
                    <span class="font-medium">${bookingData.email}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Check-in:</span>
                    <span class="font-medium">${new Date(bookingData.check_in_date).toLocaleDateString()}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Check-out:</span>
                    <span class="font-medium">${new Date(bookingData.check_out_date).toLocaleDateString()}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Duration:</span>
                    <span class="font-medium">${nights} night${nights !== 1 ? 's' : ''}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Guests:</span>
                    <span class="font-medium">${bookingData.num_guests}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Rooms:</span>
                    <span class="font-medium">${selectedRooms.map(r => `Room ${r.room_number}`).join(', ')}</span>
                </div>
                <div class="flex justify-between pt-3 border-t mt-2">
                    <span class="font-bold text-gray-900">Total Amount:</span>
                    <span class="text-lg font-bold text-blue-600">₱${totalAmount.toLocaleString()}</span>
                </div>
            `;
        }

        // Complete booking
        async function completeBooking() {
            // Validate terms agreement
            if (!document.getElementById('agreeTerms').checked) {
                showToast('Please agree to the terms and conditions', 'error');
                return;
            }
            
            const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked').value;
            
            // Step 1: Prepare booking (create temp session)
            showToast('Preparing your booking...', 'info');
            
            try {
                const prepareResponse = await fetch('/hotel/booking/prepare', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        room_type_id: selectedRoomTypeId,
                        check_in_date: bookingData.check_in_date,
                        check_out_date: bookingData.check_out_date,
                        num_rooms: selectedRooms.length,
                        num_guests: bookingData.num_guests
                    })
                });
                
                const prepareResult = await prepareResponse.json();
                
                if (prepareResult.success) {
                    tempReference = prepareResult.temp_reference;
                    
                    // Step 2: Confirm booking with guest details
                    showToast('Creating your reservation...', 'info');
                    
                    const confirmResponse = await fetch('/hotel/booking/confirm', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            first_name: bookingData.first_name,
                            last_name: bookingData.last_name,
                            email: bookingData.email,
                            contact_number: bookingData.contact_number,
                            special_requests: bookingData.special_requests,
                            payment_method: paymentMethod,
                            temp_reference: tempReference
                        })
                    });
                    
                    const confirmResult = await confirmResponse.json();
                    
                    if (confirmResult.success) {
                        if (paymentMethod === 'online' && confirmResult.redirect_url) {
                            // Redirect to Stripe for online payment
                            showToast('Redirecting to payment...', 'info');
                            setTimeout(() => {
                                window.location.href = confirmResult.redirect_url;
                            }, 1000);
                        } else {
                            // Show success for cash payment
                            showSuccessModal(confirmResult);
                        }
                    } else {
                        showToast('Booking failed: ' + (confirmResult.message || 'Unknown error'), 'error');
                    }
                } else {
                    showToast('Preparation failed: ' + (prepareResult.message || 'Unknown error'), 'error');
                }
            } catch (error) {
                console.error('Booking error:', error);
                showToast('Booking failed. Please try again.', 'error');
            }
        }

        // Show success modal
        function showSuccessModal(result) {
            document.getElementById('successRef').textContent = result.booking_reference || result.reservation_id;
            document.getElementById('successTotal').textContent = `₱${bookingData.total_amount.toLocaleString()}`;
            
            closeAllModals();
            document.getElementById('successModal').classList.remove('hidden');
        }

        // Helper functions
        function isValidEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Home page booking system initialized');
            
            // Date change listeners
            document.getElementById('checkInDate')?.addEventListener('change', function() {
                const checkOut = document.getElementById('checkOutDate');
                if (checkOut.value && new Date(checkOut.value) <= new Date(this.value)) {
                    const nextDay = new Date(this.value);
                    nextDay.setDate(nextDay.getDate() + 1);
                    checkOut.value = nextDay.toISOString().split('T')[0];
                }
                calculateNights();
            });
            
            document.getElementById('checkOutDate')?.addEventListener('change', function() {
                calculateNights();
            });

            // Real-time email validation
            let emailTimeout;
            document.getElementById('guestEmail')?.addEventListener('input', function() {
                clearTimeout(emailTimeout);
                emailTimeout = setTimeout(() => {
                    validateGuestEmail();
                }, 500);
            });
            
            // Number of rooms input validation
            document.getElementById('numRooms')?.addEventListener('change', function() {
                const numRooms = parseInt(this.value);
                if (numRooms < 1) {
                    this.value = 1;
                } else if (numRooms > 10) {
                    this.value = 10;
                    showToast('Maximum 10 rooms per booking', 'info');
                }
            });
            
            // Intersection Observer for scroll animations
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-slide-up');
                    }
                });
            }, observerOptions);

            // Observe elements for animation
            document.querySelectorAll('#rooms .animate-slide-up').forEach(el => {
                observer.observe(el);
            });
        });
    </script>
</body>
</html>