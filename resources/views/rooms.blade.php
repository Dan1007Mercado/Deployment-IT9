@extends('layouts.dashboard')

@section('content')
<div class="flex min-h-screen">
    @include('components.sidebar')

    <!-- Main Content -->
    <main class="flex-1 bg-[#f8fafc]">
    @include('components.topnav', [
    'title' => 'Room Management',
    'subtitle' => 'Manage hotel rooms and view room status '
    ])

        <!-- Add/Edit Room Modal -->
        <div id="roomModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-8 relative">
                <button id="closeRoomModalBtn" class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 text-2xl">&times;</button>
                <h2 id="modalTitle" class="text-2xl font-bold mb-6">Add Room</h2>
                <form id="roomForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="room_id" name="room_id">
                    <div class="grid grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium mb-1">Room Number</label>
                            <input type="text" name="room_number" id="room_number" class="w-full px-3 py-2 border rounded-lg" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Room Type</label>
                            <select name="room_type_id" id="room_type_id" class="w-full px-3 py-2 border rounded-lg">
                                @foreach($roomTypes as $type)
                                    <option value="{{ $type->room_type_id }}">{{ $type->type_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Floor</label>
                            <input type="text" name="floor" id="floor" class="w-full px-3 py-2 border rounded-lg" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Status</label>
                            <select name="room_status" id="room_status" class="w-full px-3 py-2 border rounded-lg">
                                <option value="available">Available</option>
                                <option value="occupied">Occupied</option>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium mb-1">Room Image</label>
                            <input type="file" name="image" id="roomImageInput" accept="image/*" class="w-full">
                            <img id="roomImagePreview" src="" alt="Preview" class="mt-2 h-32 w-full object-cover rounded hidden" />
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" id="closeRoomModalBtn2" class="px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-100">Cancel</button>
                        <button type="submit" id="submitBtn" class="px-6 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700">Add Room</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
                <h3 class="text-lg font-semibold mb-4">Confirm Delete</h3>
                <p class="text-gray-600 mb-6">Are you sure you want to delete this room? This action cannot be undone.</p>
                <div class="flex justify-end space-x-3">
                    <button type="button" id="cancelDeleteBtn" class="px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-100">Cancel</button>
                    <button type="button" id="confirmDeleteBtn" class="px-6 py-2 rounded-lg bg-red-600 text-white font-semibold hover:bg-red-700">Delete</button>
                </div>
            </div>
        </div>

        <!-- Rooms Content -->
        <div class="px-8 py-6">
            

            <!-- Controls -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex-1 max-w-md">
                    <input type="text" id="searchInput" placeholder="Search by room no." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex items-center space-x-3">
                    <select id="statusFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Status</option>
                        <option value="available">Available</option>
                        <option value="occupied">Occupied</option>
                    </select>
                    <button id="addRoomBtn" class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Room
                    </button>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">All Rooms</p>
                            <p class="text-2xl font-bold">{{ $rooms->where('room_status', 'available')->count() }}</p>
                        </div>
                        <div class="p-2 bg-green-100 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                
            </div>

            <!-- Room Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-4" id="roomsGrid">
                @foreach($rooms as $room)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden room-card" data-status="{{ $room->room_status }}">
                    <div class="relative">
                        <img src="{{ $room->image_path ? Storage::url($room->image_path) : 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?w=300&h=150&fit=crop' }}" 
                             alt="Room {{ $room->room_number }}" class="w-full h-32 object-cover">
                        
                    </div>
                    <div class="p-3">
                        <div class="flex items-start justify-between mb-2">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900">Room {{ $room->room_number }}</h3>
                                <p class="text-xs text-gray-500">{{ $room->roomType->type_name }} • Floor {{ $room->floor }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs text-gray-600">{{ $room->roomType->capacity }} Guests</span>
                            <span class="text-sm font-bold text-green-600">₱{{ number_format($room->roomType->base_price, 0) }}</span>
                        </div>

                        <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                            <div class="text-xs text-gray-500 truncate flex-1 mr-2">
                                {{ Str::limit($room->roomType->amenities ?? 'No amenities', 20) }}
                            </div>
                            <div class="flex space-x-1">
                                <button class="edit-room text-blue-600 hover:text-blue-800 p-1" 
                                        data-room='@json($room)'>
                                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button class="delete-room text-red-600 hover:text-red-800 p-1" 
                                        data-room-id="{{ $room->room_id }}">
                                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            @if($rooms->isEmpty())
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No rooms</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating a new room.</p>
                <div class="mt-6">
                    <button id="addRoomEmptyBtn" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Room
                    </button>
                </div>
            </div>
            @endif
        </div>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('roomModal');
    const deleteModal = document.getElementById('deleteModal');
    const addRoomBtn = document.getElementById('addRoomBtn');
    const addRoomEmptyBtn = document.getElementById('addRoomEmptyBtn');
    const closeModalBtn = document.getElementById('closeRoomModalBtn');
    const closeModalBtn2 = document.getElementById('closeRoomModalBtn2');
    const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    const form = document.getElementById('roomForm');
    const modalTitle = document.getElementById('modalTitle');
    const submitBtn = document.getElementById('submitBtn');
    const imageInput = document.getElementById('roomImageInput');
    const imagePreview = document.getElementById('roomImagePreview');
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const roomsGrid = document.getElementById('roomsGrid');
    const roomCards = document.querySelectorAll('.room-card');

    let currentRoomId = null;

    // Open Add Room Modal
    [addRoomBtn, addRoomEmptyBtn].forEach(btn => {
        if (btn) btn.addEventListener('click', function(e) {
            e.preventDefault();
            resetForm();
            modalTitle.textContent = 'Add Room';
            submitBtn.textContent = 'Add Room';
            form.action = "{{ route('rooms.store') }}";
            form.method = 'POST';
            // Remove any existing _method hidden input
            const existingMethod = form.querySelector('input[name="_method"]');
            if (existingMethod) {
                existingMethod.remove();
            }
            modal.classList.remove('hidden');
        });
    });

    // Close Modal
    [closeModalBtn, closeModalBtn2].forEach(btn => {
        btn.addEventListener('click', function() {
            modal.classList.add('hidden');
        });
    });

    // Close modals on outside click
    window.addEventListener('click', function(e) {
        if (e.target === modal) modal.classList.add('hidden');
        if (e.target === deleteModal) deleteModal.classList.add('hidden');
    });

    // Image preview
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

    // Edit Room - Fixed version
    document.addEventListener('click', function(e) {
        if (e.target.closest('.edit-room')) {
            const editBtn = e.target.closest('.edit-room');
            const roomData = JSON.parse(editBtn.dataset.room.replace(/&quot;/g, '"'));
            
            modalTitle.textContent = 'Edit Room';
            submitBtn.textContent = 'Update Room';
            
            // Fill form with room data
            document.getElementById('room_id').value = roomData.room_id;
            document.getElementById('room_number').value = roomData.room_number;
            document.getElementById('room_type_id').value = roomData.room_type_id;
            document.getElementById('floor').value = roomData.floor;
            document.getElementById('room_status').value = roomData.room_status;
            
            // Handle image preview
            if (roomData.image_path) {
                imagePreview.src = `/storage/${roomData.image_path}`;
                imagePreview.classList.remove('hidden');
            } else {
                imagePreview.src = '';
                imagePreview.classList.add('hidden');
            }
            
            // Set form action and method for update
            form.action = `/rooms/${roomData.room_id}`;
            form.method = 'POST';
            
            // Remove any existing _method input
            const existingMethod = form.querySelector('input[name="_method"]');
            if (existingMethod) {
                existingMethod.remove();
            }
            
            // Add PUT method hidden input
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'PUT';
            form.appendChild(methodInput);
            
            currentRoomId = roomData.room_id;
            modal.classList.remove('hidden');
        }
    });

    // Delete Room
    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-room')) {
            const deleteBtn = e.target.closest('.delete-room');
            currentRoomId = deleteBtn.dataset.roomId;
            deleteModal.classList.remove('hidden');
        }
    });

    // Confirm Delete
    confirmDeleteBtn.addEventListener('click', function() {
        if (currentRoomId) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/rooms/${currentRoomId}`;
            form.innerHTML = `
                @csrf
                @method('DELETE')
            `;
            document.body.appendChild(form);
            form.submit();
        }
    });

    // Cancel Delete
    cancelDeleteBtn.addEventListener('click', function() {
        deleteModal.classList.add('hidden');
        currentRoomId = null;
    });

    // Search and Filter
    function filterRooms() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value;
        
        roomCards.forEach(card => {
            const roomNumber = card.querySelector('h3').textContent.toLowerCase();
            const status = card.dataset.status;
            
            const matchesSearch = roomNumber.includes(searchTerm);
            const matchesStatus = !statusValue || status === statusValue;
            
            if (matchesSearch && matchesStatus) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    searchInput.addEventListener('input', filterRooms);
    statusFilter.addEventListener('change', filterRooms);

    // Reset form function
    function resetForm() {
        form.reset();
        document.getElementById('room_id').value = '';
        imagePreview.src = '';
        imagePreview.classList.add('hidden');
        currentRoomId = null;
        
        // Remove any existing _method input
        const existingMethod = form.querySelector('input[name="_method"]');
        if (existingMethod) {
            existingMethod.remove();
        }
    }

    // Form submission
    form.addEventListener('submit', function(e) {
        // Let the form submit normally - the backend will handle it
    });
});
</script>
@endsection