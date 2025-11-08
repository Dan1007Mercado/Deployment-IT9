@extends('layouts.dashboard')

@section('content')
<div class="flex min-h-screen">
    @include('components.sidebar')

    <!-- Main Content -->
    <main class="flex-1 bg-[#f8fafc]">
        @include('components.topnav', ['title' => 'Room Management'])

        <!-- Add Room Modal -->
        <div id="roomModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-8 relative">
                <button id="closeRoomModalBtn" class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 text-2xl">&times;</button>
                <h2 class="text-2xl font-bold mb-6">Add Room</h2>
                <form id="roomForm">
                    <div class="grid grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium mb-1">Room Number</label>
                            <input type="text" name="number" class="w-full px-3 py-2 border rounded-lg" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Room Type</label>
                            <select name="type" class="w-full px-3 py-2 border rounded-lg">
                                <option value="Deluxe">Deluxe</option>
                                <option value="Standard">Standard</option>
                                <option value="Suite">Suite</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Status</label>
                            <select name="status" class="w-full px-3 py-2 border rounded-lg">
                                <option value="Vacant">Vacant</option>
                                <option value="Occupied">Occupied</option>
                                <option value="Maintenance">Maintenance</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Price (₱)</label>
                            <input type="number" name="price" class="w-full px-3 py-2 border rounded-lg" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Capacity</label>
                            <input type="number" name="capacity" class="w-full px-3 py-2 border rounded-lg" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Amenities (comma separated)</label>
                            <input type="text" name="amenities" class="w-full px-3 py-2 border rounded-lg" placeholder="AC, WIFI, TV">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium mb-1">Room Image</label>
                            <input type="file" name="image" id="roomImageInput" accept="image/*" class="w-full">
                            <img id="roomImagePreview" src="" alt="Preview" class="mt-2 h-32 w-full object-cover rounded hidden" />
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" id="closeRoomModalBtn2" class="px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-100">Cancel</button>
                        <button type="submit" class="px-6 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700">Add Room</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Rooms Content -->
        <div class="px-8 py-6">
            <div class="mb-6">
                <h2 class="text-xl font-semibold mb-2">Room rates</h2>
                <p class="text-gray-500 text-sm">Manage hotel room and view rooms</p>
            </div>

            <!-- Controls -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex-1 max-w-md">
                    <input type="text" placeholder="Search by room no." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex items-center space-x-3">
                    <button class="flex items-center px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                        <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Status
                    </button>
                    <button class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">All</button>
                    <button class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Rooms
                    </button>
                </div>
            </div>

            <!-- Room Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Room 1 -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="relative">
                        <img src="https://images.unsplash.com/photo-1611892440504-42a792e24d32?w=400" alt="Room 101" class="w-full h-48 object-cover">
                        <span class="absolute top-3 right-3 bg-green-500 text-white text-xs px-3 py-1 rounded-full">Vacant</span>
                    </div>
                    <div class="p-4">
                        <h3 class="text-lg font-semibold mb-1">Room 101</h3>
                        <p class="text-gray-500 text-sm mb-3">Deluxe</p>
                        <div class="flex items-center space-x-2 mb-4 text-sm text-gray-600">
                            <span class="px-2 py-1 bg-gray-100 rounded">2</span>
                            <span class="px-2 py-1 bg-gray-100 rounded">AC</span>
                            <span class="px-2 py-1 bg-gray-100 rounded">WIFI</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xl font-bold">₱4,000</span>
                            <div class="flex space-x-2">
                                <button class="text-blue-600 hover:text-blue-900">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button class="text-red-600 hover:text-red-900">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Room 2 -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="relative">
                        <img src="https://images.unsplash.com/photo-1590490360182-c33d57733427?w=400" alt="Room 102" class="w-full h-48 object-cover">
                        <span class="absolute top-3 right-3 bg-green-500 text-white text-xs px-3 py-1 rounded-full">Vacant</span>
                    </div>
                    <div class="p-4">
                        <h3 class="text-lg font-semibold mb-1">Room 102</h3>
                        <p class="text-gray-500 text-sm mb-3">Standard</p>
                        <div class="flex items-center space-x-2 mb-4 text-sm text-gray-600">
                            <span class="px-2 py-1 bg-gray-100 rounded">1</span>
                            <span class="px-2 py-1 bg-gray-100 rounded">AC</span>
                            <span class="px-2 py-1 bg-gray-100 rounded">WIFI</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xl font-bold">₱2,500</span>
                            <div class="flex space-x-2">
                                <button class="text-blue-600 hover:text-blue-900">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button class="text-red-600 hover:text-red-900">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Room 3 -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="relative">
                        <img src="https://images.unsplash.com/photo-1566665797739-1674de7a421a?w=400" alt="Room 103" class="w-full h-48 object-cover">
                        <span class="absolute top-3 right-3 bg-blue-500 text-white text-xs px-3 py-1 rounded-full">Occupied</span>
                    </div>
                    <div class="p-4">
                        <h3 class="text-lg font-semibold mb-1">Room 103</h3>
                        <p class="text-gray-500 text-sm mb-3">Standard</p>
                        <div class="flex items-center space-x-2 mb-4 text-sm text-gray-600">
                            <span class="px-2 py-1 bg-gray-100 rounded">2</span>
                            <span class="px-2 py-1 bg-gray-100 rounded">AC</span>
                            <span class="px-2 py-1 bg-gray-100 rounded">WIFI</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xl font-bold">₱7,500</span>
                            <div class="flex space-x-2">
                                <button class="text-blue-600 hover:text-blue-900">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button class="text-red-600 hover:text-red-900">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('roomModal');
    const openModalBtn = Array.from(document.querySelectorAll('button.bg-blue-600')).find(btn => btn.textContent.includes('Add Rooms'));
    const closeModalBtn = document.getElementById('closeRoomModalBtn');
    const closeModalBtn2 = document.getElementById('closeRoomModalBtn2');
    const form = document.getElementById('roomForm');
    const imageInput = document.getElementById('roomImageInput');
    const imagePreview = document.getElementById('roomImagePreview');

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
    window.addEventListener('click', function(e) {
        if (e.target === modal) modal.classList.add('hidden');
    });

    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(evt) {
                imagePreview.src = evt.target.result;
                imagePreview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            imagePreview.src = '';
            imagePreview.classList.add('hidden');
        }
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const data = Object.fromEntries(new FormData(form));
        const grid = document.querySelector('.grid.grid-cols-1');
        const card = document.createElement('div');
        card.className = 'bg-white rounded-lg shadow overflow-hidden';
        card.innerHTML = `
            <div class="relative">
                <img src="${imagePreview.src || 'https://via.placeholder.com/400x200?text=Room'}" alt="Room ${data.number}" class="w-full h-48 object-cover">
                <span class="absolute top-3 right-3 ${getStatusClass(data.status)} text-white text-xs px-3 py-1 rounded-full">${data.status}</span>
            </div>
            <div class="p-4">
                <h3 class="text-lg font-semibold mb-1">Room ${data.number}</h3>
                <p class="text-gray-500 text-sm mb-3">${data.type}</p>
                <div class="flex items-center space-x-2 mb-4 text-sm text-gray-600">
                    <span class="px-2 py-1 bg-gray-100 rounded">${data.capacity}</span>
                    ${data.amenities.split(',').map(a => `<span class="px-2 py-1 bg-gray-100 rounded">${a.trim()}</span>`).join('')}
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xl font-bold">₱${data.price}</span>
                    <div class="flex space-x-2">
                        <button class="text-blue-600 hover:text-blue-900">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>
                        <button class="text-red-600 hover:text-red-900">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        `;
        grid.appendChild(card);
        modal.classList.add('hidden');
        form.reset();
        imagePreview.src = '';
        imagePreview.classList.add('hidden');
    });
    function getStatusClass(status) {
        switch(status) {
            case 'Vacant': return 'bg-green-500';
            case 'Occupied': return 'bg-red-500';
            case 'Maintenance': return 'bg-yellow-500';
            default: return 'bg-gray-500';
        }
    }
});
</script>
@endsection