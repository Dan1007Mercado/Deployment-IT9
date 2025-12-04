@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-blue-50 to-white">
    <!-- Hero Section -->
    <div class="relative bg-gradient-to-r from-blue-600 to-blue-800 text-white">
        <div class="absolute inset-0 bg-black opacity-20"></div>
        <div class="container mx-auto px-6 py-24 relative">
            <div class="max-w-3xl">
                <h1 class="text-5xl font-bold mb-6 leading-tight">
                    Experience Luxury & Comfort
                </h1>
                <p class="text-xl mb-8 text-blue-100">
                    Discover our exquisite rooms and suites designed for your ultimate comfort and relaxation.
                </p>
                <a href="#room-types" 
                   class="inline-block bg-white text-blue-600 px-8 py-4 rounded-lg font-semibold hover:bg-blue-50 transition-all shadow-lg">
                    Explore Rooms
                </a>
            </div>
        </div>
    </div>

    <!-- Room Types Section -->
    <section id="room-types" class="py-16 bg-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">Our Rooms & Suites</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Each room is thoughtfully designed with premium amenities to ensure a memorable stay
                </p>
            </div>

            <!-- Room Types Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($roomTypes as $roomType)
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-200 hover:shadow-xl transition-all duration-300">
                    <!-- Room Image -->
                    <div class="h-64 bg-gradient-to-br from-blue-100 to-blue-300 relative">
                        <!-- You can add dynamic images here later -->
                        <div class="absolute inset-0 flex items-center justify-center">
                            <svg class="w-32 h-32 text-blue-200" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                            </svg>
                        </div>
                        <!-- Price Tag -->
                        <div class="absolute top-4 right-4 bg-white px-4 py-2 rounded-lg shadow-md">
                            <span class="text-2xl font-bold text-blue-600">₱{{ number_format($roomType->base_price) }}</span>
                            <span class="text-gray-500 text-sm">/night</span>
                        </div>
                    </div>

                    <!-- Room Details -->
                    <div class="p-6">
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">{{ $roomType->type_name }}</h3>
                        <p class="text-gray-600 mb-4">{{ $roomType->description }}</p>
                        
                        <!-- Amenities -->
                        <div class="mb-4">
                            <div class="flex items-center text-sm text-gray-500 mb-2">
                                <svg class="w-5 h-5 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                                </svg>
                                <span>Capacity: {{ $roomType->capacity }} guests</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="w-5 h-5 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Available Rooms: {{ $roomType->rooms->where('room_status', 'available')->count() }}</span>
                            </div>
                        </div>

                        <!-- Action Button -->
                        <button 
                            onclick="openBookingModal({{ $roomType->room_type_id }})"
                            class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors flex items-center justify-center"
                            data-room-type-id="{{ $roomType->room_type_id }}"
                            data-room-type-name="{{ $roomType->type_name }}"
                            data-room-price="{{ $roomType->base_price }}"
                            data-room-capacity="{{ $roomType->capacity }}">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Book Now
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 bg-gradient-to-b from-white to-blue-50">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Secure Booking</h3>
                    <p class="text-gray-600">Your reservation is secured with our encrypted booking system</p>
                </div>
                <div class="text-center">
                    <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Best Price Guarantee</h3>
                    <p class="text-gray-600">We guarantee the best rates for our direct bookings</p>
                </div>
                <div class="text-center">
                    <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-2">24/7 Support</h3>
                    <p class="text-gray-600">Our team is available round the clock for assistance</p>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Booking Modal -->
<div id="bookingModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background Overlay -->
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeBookingModal()"></div>
        
        <!-- Modal Container -->
        <div class="inline-block w-full max-w-4xl my-8 text-left align-middle transition-all transform bg-white rounded-2xl shadow-xl">
            <!-- Modal Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900" id="modal-title">Book Your Stay</h3>
                <button onclick="closeBookingModal()" class="text-gray-400 hover:text-gray-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Stepper -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center justify-center">
                    <div class="flex items-center space-x-8">
                        <div class="flex items-center">
                            <div id="step-1-indicator" class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold">1</div>
                            <span class="ml-2 text-sm font-medium text-gray-700">Dates</span>
                        </div>
                        <div class="w-16 h-1 bg-gray-300"></div>
                        <div class="flex items-center">
                            <div id="step-2-indicator" class="w-8 h-8 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center font-bold">2</div>
                            <span class="ml-2 text-sm font-medium text-gray-500">Guest Info</span>
                        </div>
                        <div class="w-16 h-1 bg-gray-300"></div>
                        <div class="flex items-center">
                            <div id="step-3-indicator" class="w-8 h-8 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center font-bold">3</div>
                            <span class="ml-2 text-sm font-medium text-gray-500">Payment</span>
                        </div>
                        <div class="w-16 h-1 bg-gray-300"></div>
                        <div class="flex items-center">
                            <div id="step-4-indicator" class="w-8 h-8 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center font-bold">4</div>
                            <span class="ml-2 text-sm font-medium text-gray-500">Confirm</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Content -->
            <div class="px-6 py-6">
                <!-- Step 1: Dates Selection -->
                <div id="step-1-content" class="step-content">
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold mb-2" id="room-type-selected"></h4>
                        <div class="text-gray-600">
                            Price: <span id="room-price-display" class="font-bold text-blue-600"></span> per night
                        </div>
                    </div>

                    <form id="dates-form" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium mb-2">Check-in Date *</label>
                                <input type="date" id="check_in_date" name="check_in_date" 
                                       min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Check-out Date *</label>
                                <input type="date" id="check_out_date" name="check_out_date"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium mb-2">Number of Guests *</label>
                                <input type="number" id="num_guests" name="num_guests" min="1" max="10" value="2"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <p class="text-sm text-gray-500 mt-1" id="capacity-info"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Duration</label>
                                <div class="px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg">
                                    <div id="nights-display" class="text-lg font-semibold">Select dates</div>
                                    <div id="dates-range" class="text-sm text-gray-600"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Available Rooms -->
                        <div id="available-rooms-section" class="hidden">
                            <label class="block text-sm font-medium mb-4">Select Available Rooms</label>
                            <div id="available-rooms-list" class="space-y-3 max-h-60 overflow-y-auto p-3 border border-gray-200 rounded-lg">
                                <!-- Available rooms will be loaded here -->
                            </div>
                        </div>

                        <!-- Price Summary -->
                        <div id="price-summary" class="bg-blue-50 border border-blue-200 rounded-lg p-4 hidden">
                            <h5 class="font-semibold text-blue-800 mb-2">Price Summary</h5>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Room Price per night:</span>
                                    <span id="room-price-summary" class="font-medium"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Number of nights:</span>
                                    <span id="nights-summary" class="font-medium"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Number of rooms:</span>
                                    <span id="rooms-count-summary" class="font-medium">0</span>
                                </div>
                                <div class="border-t pt-2 mt-2">
                                    <div class="flex justify-between text-lg font-bold text-blue-700">
                                        <span>Total Amount:</span>
                                        <span id="total-amount-summary">₱0.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="flex justify-end mt-8">
                        <button id="next-to-guest" 
                                onclick="nextStep(2)"
                                class="px-8 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                disabled>
                            Next: Guest Information
                        </button>
                    </div>
                </div>

                <!-- Step 2: Guest Information -->
                <div id="step-2-content" class="step-content hidden">
                    <h4 class="text-lg font-semibold mb-6">Guest Information</h4>
                    
                    <form id="guest-form" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium mb-2">First Name *</label>
                                <input type="text" id="first_name" name="first_name"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Last Name *</label>
                                <input type="text" id="last_name" name="last_name"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       required>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium mb-2">Email *</label>
                                <input type="email" id="email" name="email"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       required>
                                <div id="email-validation" class="mt-2 text-sm hidden"></div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Contact Number *</label>
                                <input type="tel" id="contact_number" name="contact_number"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       required>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2">Special Requests</label>
                            <textarea id="special_requests" name="special_requests" rows="3"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Any special requirements or requests..."></textarea>
                        </div>
                    </form>

                    <div class="flex justify-between mt-8">
                        <button onclick="prevStep(1)"
                                class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors">
                            Previous
                        </button>
                        <button id="next-to-payment" 
                                onclick="nextStep(3)"
                                class="px-8 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                            Next: Payment
                        </button>
                    </div>
                </div>

                <!-- Step 3: Payment -->
                <div id="step-3-content" class="step-content hidden">
                    <h4 class="text-lg font-semibold mb-6">Select Payment Method</h4>
                    
                    <div class="space-y-4 mb-8">
                        <!-- Payment Method Selection -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="border-2 border-transparent rounded-lg p-4 cursor-pointer hover:border-blue-500 payment-method"
                                 data-method="cash" onclick="selectPaymentMethod('cash')">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-4">
                                        <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h5 class="font-semibold">Pay with Cash</h5>
                                        <p class="text-sm text-gray-600">Pay at reception</p>
                                    </div>
                                </div>
                            </div>

                            <div class="border-2 border-transparent rounded-lg p-4 cursor-pointer hover:border-blue-500 payment-method"
                                 data-method="card" onclick="selectPaymentMethod('card')">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                                        <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/>
                                            <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h5 class="font-semibold">Credit/Debit Card</h5>
                                        <p class="text-sm text-gray-600">Pay with card</p>
                                    </div>
                                </div>
                            </div>

                            <div class="border-2 border-transparent rounded-lg p-4 cursor-pointer hover:border-blue-500 payment-method"
                                 data-method="online" onclick="selectPaymentMethod('online')">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mr-4">
                                        <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM4.332 8.027a6.012 6.012 0 011.912-2.706C6.512 5.73 6.974 6 7.5 6A1.5 1.5 0 019 7.5V8a2 2 0 004 0 2 2 0 011.523-1.943A5.977 5.977 0 0116 10c0 .34-.028.675-.083 1H15a2 2 0 00-2 2v2.197A5.973 5.973 0 0110 16v-2a2 2 0 00-2-2 2 2 0 01-2-2 2 2 0 00-1.668-1.973z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h5 class="font-semibold">Online Payment</h5>
                                        <p class="text-sm text-gray-600">Pay online with Stripe</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Details -->
                        <div id="payment-details" class="hidden">
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mt-4">
                                <div class="flex justify-between items-center mb-4">
                                    <h5 class="font-semibold text-gray-700">Payment Summary</h5>
                                    <div class="text-xl font-bold text-blue-700" id="payment-total">₱0.00</div>
                                </div>
                                
                                <!-- Card Payment Form (hidden by default) -->
                                <div id="card-payment-form" class="hidden space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium mb-2">Card Number</label>
                                        <input type="text" placeholder="1234 5678 9012 3456" 
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium mb-2">Expiry Date</label>
                                            <input type="text" placeholder="MM/YY"
                                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium mb-2">CVC</label>
                                            <input type="text" placeholder="123"
                                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        </div>
                                    </div>
                                </div>

                                <!-- Cash Payment Instructions -->
                                <div id="cash-payment-instructions" class="hidden">
                                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 text-yellow-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                            </svg>
                                            <p class="text-yellow-700">
                                                Please proceed to the reception desk to complete your payment in cash.
                                                Your reservation will be confirmed upon payment.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Online Payment Instructions -->
                                <div id="online-payment-instructions" class="hidden">
                                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                                        <p class="text-purple-700 mb-4">
                                            You will be redirected to a secure payment gateway to complete your payment.
                                        </p>
                                        <div class="flex items-center text-sm text-purple-600">
                                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            Secure payment powered by Stripe
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between mt-8">
                        <button onclick="prevStep(2)"
                                class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors">
                            Previous
                        </button>
                        <button id="process-payment" 
                                onclick="processPayment()"
                                class="px-8 py-3 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition-colors hidden">
                            Process Payment
                        </button>
                    </div>
                </div>

                <!-- Step 4: Confirmation -->
                <div id="step-4-content" class="step-content hidden">
                    <div class="text-center mb-8">
                        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <h4 class="text-2xl font-bold text-gray-800 mb-2">Booking Confirmed!</h4>
                        <p class="text-gray-600">Your reservation has been successfully created</p>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-6 mb-6">
                        <div id="confirmation-details" class="space-y-4">
                            <!-- Details will be populated here -->
                        </div>
                    </div>

                    <div class="flex justify-center mt-8">
                        <button onclick="closeBookingModal()"
                                class="px-8 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Global variables
let currentStep = 1;
let selectedRoomTypeId = null;
let selectedRoomTypeName = null;
let selectedRoomPrice = 0;
let selectedRooms = [];
let selectedPaymentMethod = null;
let emailValidated = false;

// Open booking modal
function openBookingModal(roomTypeId) {
    const button = document.querySelector(`[data-room-type-id="${roomTypeId}"]`);
    selectedRoomTypeId = roomTypeId;
    selectedRoomTypeName = button.dataset.roomTypeName;
    selectedRoomPrice = parseFloat(button.dataset.roomPrice);
    const capacity = parseInt(button.dataset.roomCapacity);

    // Update modal title
    document.getElementById('modal-title').textContent = `Book ${selectedRoomTypeName}`;
    document.getElementById('room-type-selected').textContent = selectedRoomTypeName;
    document.getElementById('room-price-display').textContent = `₱${selectedRoomPrice.toLocaleString()}`;
    document.getElementById('capacity-info').textContent = `Max capacity: ${capacity} guests`;

    // Reset form
    document.getElementById('dates-form').reset();
    document.getElementById('guest-form').reset();
    document.getElementById('num_guests').max = capacity;
    
    // Reset steps
    resetSteps();
    
    // Show modal
    document.getElementById('bookingModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

// Close booking modal
function closeBookingModal() {
    document.getElementById('bookingModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    resetBooking();
}

// Reset booking data
function resetBooking() {
    currentStep = 1;
    selectedRoomTypeId = null;
    selectedRoomTypeName = null;
    selectedRoomPrice = 0;
    selectedRooms = [];
    selectedPaymentMethod = null;
    emailValidated = false;
}

// Reset steps
function resetSteps() {
    currentStep = 1;
    updateStepIndicators();
    
    // Show step 1, hide others
    document.querySelectorAll('.step-content').forEach(step => step.classList.add('hidden'));
    document.getElementById('step-1-content').classList.remove('hidden');
    
    // Reset step indicators
    document.querySelectorAll('[id$="indicator"]').forEach(indicator => {
        indicator.classList.remove('bg-blue-600', 'text-white');
        indicator.classList.add('bg-gray-300', 'text-gray-600');
    });
    document.getElementById('step-1-indicator').classList.add('bg-blue-600', 'text-white');
}

// Update step indicators
function updateStepIndicators() {
    const indicators = ['step-1-indicator', 'step-2-indicator', 'step-3-indicator', 'step-4-indicator'];
    
    indicators.forEach((id, index) => {
        const indicator = document.getElementById(id);
        if (index + 1 < currentStep) {
            // Completed step
            indicator.classList.remove('bg-gray-300', 'text-gray-600');
            indicator.classList.add('bg-green-500', 'text-white');
        } else if (index + 1 === currentStep) {
            // Current step
            indicator.classList.remove('bg-gray-300', 'text-gray-600', 'bg-green-500');
            indicator.classList.add('bg-blue-600', 'text-white');
        } else {
            // Upcoming step
            indicator.classList.remove('bg-blue-600', 'text-white', 'bg-green-500');
            indicator.classList.add('bg-gray-300', 'text-gray-600');
        }
    });
}

// Navigate to next step
async function nextStep(step) {
    // Validate current step before proceeding
    if (currentStep === 1 && !validateStep1()) {
        return;
    }
    if (currentStep === 2 && !await validateStep2()) {
        return;
    }
    if (currentStep === 3 && !validateStep3()) {
        return;
    }

    // Hide current step
    document.getElementById(`step-${currentStep}-content`).classList.add('hidden');
    
    // Show next step
    document.getElementById(`step-${step}-content`).classList.remove('hidden');
    
    currentStep = step;
    updateStepIndicators();
    
    // Special handling for each step
    if (step === 3) {
        updatePaymentSummary();
    } else if (step === 4) {
        updateConfirmationDetails();
    }
}

// Navigate to previous step
function prevStep(step) {
    document.getElementById(`step-${currentStep}-content`).classList.add('hidden');
    document.getElementById(`step-${step}-content`).classList.remove('hidden');
    currentStep = step;
    updateStepIndicators();
}

// Validate step 1 (Dates and Rooms)
function validateStep1() {
    const checkIn = document.getElementById('check_in_date').value;
    const checkOut = document.getElementById('check_out_date').value;
    const numGuests = document.getElementById('num_guests').value;
    
    if (!checkIn || !checkOut || !numGuests) {
        alert('Please fill in all required fields');
        return false;
    }
    
    if (new Date(checkOut) <= new Date(checkIn)) {
        alert('Check-out date must be after check-in date');
        return false;
    }
    
    if (selectedRooms.length === 0) {
        alert('Please select at least one room');
        return false;
    }
    
    return true;
}

// Validate step 2 (Guest Information)
async function validateStep2() {
    const firstName = document.getElementById('first_name').value;
    const lastName = document.getElementById('last_name').value;
    const email = document.getElementById('email').value;
    const contact = document.getElementById('contact_number').value;
    
    if (!firstName || !lastName || !email || !contact) {
        alert('Please fill in all required fields');
        return false;
    }
    
    // Validate email
    if (!isValidEmail(email)) {
        alert('Please enter a valid email address');
        return false;
    }
    
    // Check email availability
    const emailValid = await checkEmailAvailability(email, firstName, lastName);
    if (!emailValid) {
        return false;
    }
    
    return true;
}

// Validate step 3 (Payment)
function validateStep3() {
    if (!selectedPaymentMethod) {
        alert('Please select a payment method');
        return false;
    }
    return true;
}

// Calculate nights
function calculateNights() {
    const checkIn = document.getElementById('check_in_date').value;
    const checkOut = document.getElementById('check_out_date').value;
    
    if (checkIn && checkOut) {
        const start = new Date(checkIn);
        const end = new Date(checkOut);
        const nights = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
        
        document.getElementById('nights-display').textContent = `${nights} night${nights !== 1 ? 's' : ''}`;
        document.getElementById('dates-range').textContent = 
            `${formatDate(start)} to ${formatDate(end)}`;
        
        updatePriceSummary(nights);
        
        // Check availability if dates are selected
        if (nights > 0) {
            checkAvailability();
        }
        
        return nights;
    }
    return 0;
}

// Format date
function formatDate(date) {
    return date.toLocaleDateString('en-US', { 
        month: 'short', 
        day: 'numeric', 
        year: 'numeric' 
    });
}

// Update price summary
function updatePriceSummary(nights) {
    const roomPrice = selectedRoomPrice;
    const roomsCount = selectedRooms.length;
    const total = roomPrice * nights * roomsCount;
    
    document.getElementById('room-price-summary').textContent = `₱${roomPrice.toLocaleString()}`;
    document.getElementById('nights-summary').textContent = nights;
    document.getElementById('rooms-count-summary').textContent = roomsCount;
    document.getElementById('total-amount-summary').textContent = `₱${total.toLocaleString()}`;
    
    // Enable/disable next button
    const nextButton = document.getElementById('next-to-guest');
    nextButton.disabled = !(nights > 0 && roomsCount > 0);
    
    // Show/hide price summary
    const priceSummary = document.getElementById('price-summary');
    if (nights > 0 && roomsCount > 0) {
        priceSummary.classList.remove('hidden');
    } else {
        priceSummary.classList.add('hidden');
    }
}

// Check room availability
async function checkAvailability() {
    const checkIn = document.getElementById('check_in_date').value;
    const checkOut = document.getElementById('check_out_date').value;
    const numGuests = document.getElementById('num_guests').value;
    
    if (!checkIn || !checkOut || !selectedRoomTypeId) {
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
                room_type_id: selectedRoomTypeId
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            displayAvailableRooms(data.rooms);
        } else {
            console.error('Failed to check availability:', data.message);
        }
    } catch (error) {
        console.error('Error checking availability:', error);
    }
}

// Display available rooms
function displayAvailableRooms(rooms) {
    const container = document.getElementById('available-rooms-list');
    const section = document.getElementById('available-rooms-section');
    
    if (rooms.length === 0) {
        container.innerHTML = `
            <div class="text-center py-4 text-gray-500">
                <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p>No rooms available for selected dates</p>
            </div>
        `;
        section.classList.remove('hidden');
        selectedRooms = [];
        updatePriceSummary(calculateNights());
        return;
    }
    
    container.innerHTML = '';
    rooms.forEach(room => {
        const isSelected = selectedRooms.some(r => r.room_id === room.room_id);
        const roomElement = document.createElement('div');
        roomElement.className = `flex items-center justify-between p-3 border rounded-lg ${isSelected ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-blue-300'}`;
        roomElement.innerHTML = `
            <div class="flex items-center">
                <input type="checkbox" id="room-${room.room_id}" 
                       ${isSelected ? 'checked' : ''}
                       onchange="toggleRoomSelection(${room.room_id}, '${room.room_number}', ${room.price})"
                       class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                <label for="room-${room.room_id}" class="ml-3">
                    <span class="font-medium">Room ${room.room_number}</span>
                    <span class="text-sm text-gray-500 ml-2">(Floor ${room.floor})</span>
                </label>
            </div>
            <div class="text-right">
                <div class="font-semibold text-blue-600">₱${room.price.toLocaleString()}/night</div>
                ${room.image_path ? 
                    `<img src="${room.image_path}" alt="Room ${room.room_number}" class="mt-2 w-20 h-12 object-cover rounded">` : 
                    ''
                }
            </div>
        `;
        container.appendChild(roomElement);
    });
    
    section.classList.remove('hidden');
}

// Toggle room selection
function toggleRoomSelection(roomId, roomNumber, price) {
    const checkbox = document.getElementById(`room-${roomId}`);
    
    if (checkbox.checked) {
        selectedRooms.push({
            room_id: roomId,
            room_number: roomNumber,
            price: price
        });
    } else {
        selectedRooms = selectedRooms.filter(room => room.room_id !== roomId);
    }
    
    updatePriceSummary(calculateNights());
}

// Validate email format
function isValidEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Check email availability
async function checkEmailAvailability(email, firstName, lastName) {
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
        const validationDiv = document.getElementById('email-validation');
        
        if (data.conflict) {
            validationDiv.innerHTML = `
                <div class="text-red-600 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    ${data.error_message}
                </div>
            `;
            validationDiv.classList.remove('hidden');
            emailValidated = false;
            return false;
        } else if (data.exists) {
            validationDiv.innerHTML = `
                <div class="text-green-600 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    Welcome back ${data.guest_name}!
                </div>
            `;
            validationDiv.classList.remove('hidden');
            emailValidated = true;
            return true;
        } else {
            validationDiv.classList.add('hidden');
            emailValidated = true;
            return true;
        }
    } catch (error) {
        console.error('Error checking email:', error);
        return true; // Allow proceeding even if validation fails
    }
}

// Select payment method
function selectPaymentMethod(method) {
    selectedPaymentMethod = method;
    
    // Remove selection from all methods
    document.querySelectorAll('.payment-method').forEach(el => {
        el.classList.remove('border-blue-500', 'bg-blue-50');
        el.classList.add('border-transparent');
    });
    
    // Add selection to chosen method
    const selectedEl = document.querySelector(`[data-method="${method}"]`);
    selectedEl.classList.remove('border-transparent');
    selectedEl.classList.add('border-blue-500', 'bg-blue-50');
    
    // Show payment details
    document.getElementById('payment-details').classList.remove('hidden');
    document.getElementById('process-payment').classList.remove('hidden');
    
    // Show appropriate payment form
    document.getElementById('card-payment-form').classList.add('hidden');
    document.getElementById('cash-payment-instructions').classList.add('hidden');
    document.getElementById('online-payment-instructions').classList.add('hidden');
    
    if (method === 'card') {
        document.getElementById('card-payment-form').classList.remove('hidden');
    } else if (method === 'cash') {
        document.getElementById('cash-payment-instructions').classList.remove('hidden');
    } else if (method === 'online') {
        document.getElementById('online-payment-instructions').classList.remove('hidden');
    }
}

// Update payment summary
function updatePaymentSummary() {
    const nights = calculateNights();
    const roomPrice = selectedRoomPrice;
    const roomsCount = selectedRooms.length;
    const total = roomPrice * nights * roomsCount;
    
    document.getElementById('payment-total').textContent = `₱${total.toLocaleString()}`;
}

// Process payment
async function processPayment() {
    if (!selectedPaymentMethod) {
        alert('Please select a payment method');
        return;
    }
    
    // Collect all data
    const checkIn = document.getElementById('check_in_date').value;
    const checkOut = document.getElementById('check_out_date').value;
    const numGuests = document.getElementById('num_guests').value;
    const firstName = document.getElementById('first_name').value;
    const lastName = document.getElementById('last_name').value;
    const email = document.getElementById('email').value;
    const contact = document.getElementById('contact_number').value;
    const specialRequests = document.getElementById('special_requests').value;
    const nights = calculateNights();
    const totalAmount = selectedRoomPrice * nights * selectedRooms.length;
    
    const bookingData = {
        first_name: firstName,
        last_name: lastName,
        email: email,
        contact_number: contact,
        check_in_date: checkIn,
        check_out_date: checkOut,
        num_guests: parseInt(numGuests),
        room_ids: selectedRooms.map(room => room.room_id),
        booking_source: 'online',
        special_requests: specialRequests,
        total_amount: totalAmount,
        payment_method: selectedPaymentMethod
    };
    
    try {
        // Create reservation
        const response = await fetch('{{ route("guest.booking.confirm") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(bookingData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            // If online payment, redirect to payment gateway
            if (selectedPaymentMethod === 'online') {
                // Handle online payment redirection
                if (result.payment_url) {
                    window.location.href = result.payment_url;
                }
            } else {
                // For cash/card, proceed to confirmation
                nextStep(4);
            }
        } else {
            alert('Failed to create booking: ' + (result.message || 'Unknown error'));
        }
    } catch (error) {
        console.error('Error processing booking:', error);
        alert('Failed to process booking. Please try again.');
    }
}

// Update confirmation details
function updateConfirmationDetails() {
    const checkIn = document.getElementById('check_in_date').value;
    const checkOut = document.getElementById('check_out_date').value;
    const firstName = document.getElementById('first_name').value;
    const lastName = document.getElementById('last_name').value;
    const email = document.getElementById('email').value;
    const contact = document.getElementById('contact_number').value;
    const nights = calculateNights();
    const roomsList = selectedRooms.map(room => `Room ${room.room_number}`).join(', ');
    
    const detailsHtml = `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <h6 class="font-semibold text-gray-700">Guest Information</h6>
                <p class="text-gray-600">${firstName} ${lastName}</p>
                <p class="text-gray-600">${email}</p>
                <p class="text-gray-600">${contact}</p>
            </div>
            <div>
                <h6 class="font-semibold text-gray-700">Stay Details</h6>
                <p class="text-gray-600">Check-in: ${formatDate(new Date(checkIn))}</p>
                <p class="text-gray-600">Check-out: ${formatDate(new Date(checkOut))}</p>
                <p class="text-gray-600">${nights} nights</p>
            </div>
            <div class="md:col-span-2">
                <h6 class="font-semibold text-gray-700">Room Information</h6>
                <p class="text-gray-600">Room Type: ${selectedRoomTypeName}</p>
                <p class="text-gray-600">Rooms: ${roomsList}</p>
                <p class="text-gray-600">Payment Method: ${selectedPaymentMethod.toUpperCase()}</p>
            </div>
            <div class="md:col-span-2 border-t pt-4">
                <div class="flex justify-between text-lg font-bold text-blue-700">
                    <span>Total Amount:</span>
                    <span>₱${(selectedRoomPrice * nights * selectedRooms.length).toLocaleString()}</span>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('confirmation-details').innerHTML = detailsHtml;
}

// Initialize event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Date change listeners
    document.getElementById('check_in_date').addEventListener('change', function() {
        const checkOut = document.getElementById('check_out_date');
        if (checkOut.value && new Date(checkOut.value) <= new Date(this.value)) {
            const nextDay = new Date(this.value);
            nextDay.setDate(nextDay.getDate() + 1);
            checkOut.value = nextDay.toISOString().split('T')[0];
        }
        calculateNights();
    });
    
    document.getElementById('check_out_date').addEventListener('change', calculateNights);
    document.getElementById('num_guests').addEventListener('input', calculateNights);
    
    // Email validation on blur
    document.getElementById('email').addEventListener('blur', async function() {
        const firstName = document.getElementById('first_name').value;
        const lastName = document.getElementById('last_name').value;
        if (this.value && firstName && lastName) {
            await checkEmailAvailability(this.value, firstName, lastName);
        }
    });
});
</script>
@endpush

<style>
.step-content {
    transition: opacity 0.3s ease;
}

#available-rooms-list {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 #f1f5f9;
}

#available-rooms-list::-webkit-scrollbar {
    width: 6px;
}

#available-rooms-list::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

#available-rooms-list::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

#available-rooms-list::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

.payment-method {
    transition: all 0.2s ease;
}

.payment-method:hover {
    transform: translateY(-2px);
}
</style>