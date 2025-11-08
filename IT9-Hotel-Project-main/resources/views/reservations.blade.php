@extends('layouts.dashboard')

@section('content')
<div class="flex min-h-screen">
    @include('components.sidebar')

    <!-- Main Content -->
    <main class="flex-1 bg-[#f8fafc]">
        @include('components.topnav', ['title' => 'Reservations Management'])

        <!-- Edit Guest Modal -->
        <div id="editGuestModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-3xl p-6 relative">
                <button id="closeEditGuestBtn" class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 text-2xl">&times;</button>
                <h2 class="text-xl font-semibold mb-6">Edit Reservation</h2>
                <form id="editGuestForm">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">First Name</label>
                            <input type="text" id="editFirstName" name="firstName" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Last Name</label>
                            <input type="text" id="editLastName" name="lastName" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Email</label>
                            <input type="email" id="editEmail" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Room</label>
                            <input type="text" id="editRoom" name="room" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Room Type</label>
                            <select id="editRoomType" name="roomType" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200">
                                <option value="Deluxe">Deluxe</option>
                                <option value="Standard">Standard</option>
                                <option value="Suite">Suite</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Check In</label>
                            <input type="date" id="editCheckIn" name="checkIn" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Check Out</label>
                            <input type="date" id="editCheckOut" name="checkOut" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Status</label>
                            <select id="editStatus" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200">
                                <option value="Checked In">Checked In</option>
                                <option value="Upcoming">Upcoming</option>
                                <option value="Pending">Pending</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Price (₱)</label>
                            <input type="number" id="editPrice" name="price" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200" min="1" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Nights</label>
                            <input type="number" id="editNights" name="nights" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200" min="1" required>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" id="closeEditGuestBtn2" class="px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-100">Cancel</button>
                        <button type="submit" class="px-6 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700">Update Reservation</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- New Reservation Modal -->
        <div id="reservationModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-3xl p-8 relative">
                <button id="closeModalBtn" class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 text-2xl">&times;</button>
                <h2 class="text-xl font-semibold mb-6">Reservation Details</h2>
                <form id="reservationForm">
                    <div class="grid grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium mb-1">Guest First Name</label>
                            <input type="text" name="firstName" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Guest Last Name</label>
                            <input type="text" name="lastName" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Room</label>
                            <input type="text" name="room" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Room Type</label>
                            <select name="roomType" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200">
                                <option value="">Select a room</option>
                                <option value="Deluxe">Deluxe</option>
                                <option value="Standard">Standard</option>
                                <option value="Suite">Suite</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Email</label>
                            <input type="email" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Check In Date</label>
                            <input type="date" name="checkIn" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Nights</label>
                            <input type="number" name="nights" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200" min="1" value="1" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Check Out Date</label>
                            <input type="date" name="checkOut" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Price (₱)</label>
                            <input type="number" name="price" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200" min="1" value="1" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Status</label>
                            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200">
                                <option value="Checked In">Checked In</option>
                                <option value="Upcoming">Upcoming</option>
                                <option value="Pending">Pending</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" id="closeModalBtn2" class="px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-100">Cancel</button>
                        <button type="submit" class="px-6 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 flex items-center"><svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg> Create Reservation</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Reservations Content -->
        <div class="px-8 py-6">
            <!-- Header Section -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold mb-2">Reservations</h2>
                <p class="text-gray-500 text-sm">Manage hotel reservations and bookings</p>
            </div>

            <!-- Controls Section -->
            <div class="flex items-center justify-between mb-6">
                <!-- Search -->
                <div class="flex-1 max-w-md">
                    <div class="relative">
                        <input type="text" placeholder="Search by guest name or room..." class="w-full px-4 py-2 pl-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <svg class="absolute left-3 top-3 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center space-x-3">
                    <button class="flex items-center px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                        <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Date Range
                    </button>
                    <button class="flex items-center px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                        <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Filter
                    </button>
                    <button id="newReservationBtn" class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        New Reservation
                    </button>
                </div>
            </div>

            <!-- Reservations Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guest</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check-in</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check-out</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <!-- Reservation 1 -->
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <span class="h-10 w-10 rounded-full bg-blue-500 text-white flex items-center justify-center font-bold">J</span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">Jane Smith</div>
                                        <div class="text-sm text-gray-500">jane.smith@email.com</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">Room 102</div>
                                <div class="text-sm text-gray-500">Deluxe</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Nov 14, 2025</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Nov 18, 2025</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Checked In</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">₱345</div>
                                <div class="text-xs text-gray-500">4 nights</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button class="edit-guest-btn text-blue-600 hover:text-blue-900 mr-3"
                                    data-firstname="Jane"
                                    data-lastname="Smith"
                                    data-email="jane.smith@email.com"
                                    data-room="Room 102"
                                    data-roomtype="Deluxe"
                                    data-checkin="2025-11-14"
                                    data-checkout="2025-11-18"
                                    data-nights="4"
                                    data-price="345"
                                    data-status="Checked In">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button class="text-red-600 hover:text-red-900">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </td>
                        </tr>

                        <!-- Reservation 2 -->
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <span class="h-10 w-10 rounded-full bg-purple-500 text-white flex items-center justify-center font-bold">E</span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">Emily Davis</div>
                                        <div class="text-sm text-gray-500">emily.d@email.com</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">Room 305</div>
                                <div class="text-sm text-gray-500">Standard</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Nov 15, 2025</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Nov 17, 2025</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">Upcoming</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">₱198</div>
                                <div class="text-xs text-gray-500">2 nights</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button class="edit-guest-btn text-blue-600 hover:text-blue-900 mr-3"
                                    data-firstname="Emily"
                                    data-lastname="Davis"
                                    data-email="emily.d@email.com"
                                    data-room="Room 305"
                                    data-roomtype="Standard"
                                    data-checkin="2025-11-15"
                                    data-checkout="2025-11-17"
                                    data-nights="2"
                                    data-price="198"
                                    data-status="Upcoming">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button class="text-red-600 hover:text-red-900">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </td>
                        </tr>

                        <!-- Reservation 3 -->
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <span class="h-10 w-10 rounded-full bg-indigo-500 text-white flex items-center justify-center font-bold">S</span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">Sarah Johnson</div>
                                        <div class="text-sm text-gray-500">sarahj@email.com</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">Room 215</div>
                                <div class="text-sm text-gray-500">Suite</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Nov 12, 2025</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Nov 20, 2025</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Checked In</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">₱1,495</div>
                                <div class="text-xs text-gray-500">8 nights</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button class="edit-guest-btn text-blue-600 hover:text-blue-900 mr-3"
                                    data-firstname="Sarah"
                                    data-lastname="Johnson"
                                    data-email="sarahj@email.com"
                                    data-room="Room 215"
                                    data-roomtype="Suite"
                                    data-checkin="2025-11-12"
                                    data-checkout="2025-11-20"
                                    data-nights="8"
                                    data-price="1495"
                                    data-status="Checked In">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button class="text-red-600 hover:text-red-900">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </td>
                        </tr>

                        <!-- Reservation 4 -->
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <span class="h-10 w-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold">M</span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">Michael Brown</div>
                                        <div class="text-sm text-gray-500">michael.b@email.com</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">Room 401</div>
                                <div class="text-sm text-gray-500">Standard</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Nov 16, 2025</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Nov 21, 2025</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">₱299</div>
                                <div class="text-xs text-gray-500">5 nights</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button class="edit-guest-btn text-blue-600 hover:text-blue-900 mr-3"
                                    data-firstname="Michael"
                                    data-lastname="Brown"
                                    data-email="michael.b@email.com"
                                    data-room="Room 401"
                                    data-roomtype="Standard"
                                    data-checkin="2025-11-16"
                                    data-checkout="2025-11-21"
                                    data-nights="5"
                                    data-price="299"
                                    data-status="Pending">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button class="text-red-600 hover:text-red-900">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </td>
                        </tr>

                        <!-- Reservation 5 -->
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <span class="h-10 w-10 rounded-full bg-purple-600 text-white flex items-center justify-center font-bold">R</span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">Robert Wilson</div>
                                        <div class="text-sm text-gray-500">robert.w@email.com</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">Room 508</div>
                                <div class="text-sm text-gray-500">Deluxe</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Nov 14, 2025</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Nov 20, 2025</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Checked In</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">₱1,100</div>
                                <div class="text-xs text-gray-500">6 nights</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button class="edit-guest-btn text-blue-600 hover:text-blue-900 mr-3"
                                    data-firstname="Robert"
                                    data-lastname="Wilson"
                                    data-email="robert.w@email.com"
                                    data-room="Room 508"
                                    data-roomtype="Deluxe"
                                    data-checkin="2025-11-14"
                                    data-checkout="2025-11-20"
                                    data-nights="6"
                                    data-price="1100"
                                    data-status="Checked In">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button class="text-red-600 hover:text-red-900">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('reservationModal');
    const editGuestModal = document.getElementById('editGuestModal');
    const openModalBtn = document.getElementById('newReservationBtn');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const closeModalBtn2 = document.getElementById('closeModalBtn2');
    const closeEditGuestBtn = document.getElementById('closeEditGuestBtn');
    const closeEditGuestBtn2 = document.getElementById('closeEditGuestBtn2');
    const form = document.getElementById('reservationForm');
    const editGuestForm = document.getElementById('editGuestForm');
    let editingGuestRow = null;

    // Helper function to format dates
    function formatDate(dateStr) {
        if (!dateStr) return '';
        const date = new Date(dateStr + 'T00:00:00');
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        return `${months[date.getMonth()]} ${date.getDate()}, ${date.getFullYear()}`;
    }

    // Helper function to format price
    function formatPrice(price) {
        return Number(price).toLocaleString();
    }

    // Helper function to get status badge class
    function getStatusClass(status) {
        const classes = {
            'Confirmed': 'bg-green-100 text-green-800',
            'Pending': 'bg-yellow-100 text-yellow-800',
            'Checked In': 'bg-blue-100 text-blue-800',
            'Upcoming': 'bg-purple-100 text-purple-800'
        };
        return classes[status] || 'bg-gray-100 text-gray-800';
    }

    // New Reservation Modal
    openModalBtn.addEventListener('click', function(e) {
        e.preventDefault();
        modal.classList.remove('hidden');
    });
    closeModalBtn.addEventListener('click', function() {
        modal.classList.add('hidden');
    });
    closeModalBtn2.addEventListener('click', function() {
        modal.classList.add('hidden');
    });
    
    // Edit Guest Modal
    document.addEventListener('click', function(e) {
        if (e.target.closest('.edit-guest-btn')) {
            const btn = e.target.closest('.edit-guest-btn');
            editingGuestRow = btn.closest('tr');
            
            // Pre-fill form
            document.getElementById('editFirstName').value = btn.dataset.firstname;
            document.getElementById('editLastName').value = btn.dataset.lastname;
            document.getElementById('editEmail').value = btn.dataset.email;
            document.getElementById('editRoom').value = btn.dataset.room;
            document.getElementById('editRoomType').value = btn.dataset.roomtype;
            document.getElementById('editCheckIn').value = btn.dataset.checkin;
            document.getElementById('editCheckOut').value = btn.dataset.checkout;
            document.getElementById('editNights').value = btn.dataset.nights;
            document.getElementById('editPrice').value = btn.dataset.price;
            document.getElementById('editStatus').value = btn.dataset.status;
            
            editGuestModal.classList.remove('hidden');
        }
    });

    closeEditGuestBtn.addEventListener('click', function() {
        editGuestModal.classList.add('hidden');
    });
    closeEditGuestBtn2.addEventListener('click', function() {
        editGuestModal.classList.add('hidden');
    });

    // Handle Edit Guest Form Submit
    editGuestForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const data = Object.fromEntries(new FormData(editGuestForm));
        
        if (editingGuestRow) {
            const initial = data.firstName[0].toUpperCase();
            
            // Update entire row with all new data
            editingGuestRow.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10">
                            <span class="h-10 w-10 rounded-full bg-blue-500 text-white flex items-center justify-center font-bold">${initial}</span>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">${data.firstName} ${data.lastName}</div>
                            <div class="text-sm text-gray-500">${data.email}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">${data.room}</div>
                    <div class="text-sm text-gray-500">${data.roomType}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${formatDate(data.checkIn)}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${formatDate(data.checkOut)}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${getStatusClass(data.status)}">${data.status}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-bold text-gray-900">₱${formatPrice(data.price)}</div>
                    <div class="text-xs text-gray-500">${data.nights} nights</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <button class="edit-guest-btn text-blue-600 hover:text-blue-900 mr-3"
                        data-firstname="${data.firstName}"
                        data-lastname="${data.lastName}"
                        data-email="${data.email}"
                        data-room="${data.room}"
                        data-roomtype="${data.roomType}"
                        data-checkin="${data.checkIn}"
                        data-checkout="${data.checkOut}"
                        data-nights="${data.nights}"
                        data-price="${data.price}"
                        data-status="${data.status}">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </button>
                    <button class="text-red-600 hover:text-red-900">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </td>
            `;
        }
        
        editGuestModal.classList.add('hidden');
        editGuestForm.reset();
        editingGuestRow = null;
    });

    window.addEventListener('click', function(e) {
        if (e.target === modal) modal.classList.add('hidden');
        if (e.target === editGuestModal) editGuestModal.classList.add('hidden');
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const data = Object.fromEntries(new FormData(form));
        const tbody = document.querySelector('table tbody');
        const row = document.createElement('tr');
        row.className = 'hover:bg-gray-50';
        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10">
                        <span class="h-10 w-10 rounded-full bg-blue-500 text-white flex items-center justify-center font-bold">${data.firstName[0]}</span>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">${data.firstName} ${data.lastName}</div>
                        <div class="text-sm text-gray-500">${data.email}</div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">${data.room}</div>
                <div class="text-sm text-gray-500">${data.roomType}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${data.checkIn}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${data.checkOut}</td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${getStatusClass(data.status)}">${data.status}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-bold text-gray-900">₱${data.price}</div>
                <div class="text-xs text-gray-500">${data.nights} nights</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button class="edit-guest-btn text-blue-600 hover:text-blue-900 mr-3"
                    data-firstname="${data.firstName}"
                    data-lastname="${data.lastName}"
                    data-email="${data.email}">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </button>
                <button class="text-red-600 hover:text-red-900">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </td>
        `;
        tbody.appendChild(row);
        modal.classList.add('hidden');
        form.reset();
    });
    
    function getStatusClass(status) {
        switch(status) {
            case 'Checked In': return 'bg-green-100 text-green-800';
            case 'Upcoming': return 'bg-purple-100 text-purple-800';
            case 'Pending': return 'bg-yellow-100 text-yellow-800';
            case 'Cancelled': return 'bg-red-100 text-red-800';
            default: return 'bg-gray-100 text-gray-800';
        }
    }
});
</script>
@endsection