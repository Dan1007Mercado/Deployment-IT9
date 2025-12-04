@extends('layouts.dashboard')

@section('content')
<div class="flex min-h-screen">
    @include('components.sidebar')

    <!-- Main Content -->
    <main class="flex-1 bg-[#f8fafc]">
    @include('components.topnav', [
            'title' => 'Employee Management',
            'subtitle' => 'Manage Employees and Accounts'
        ])

        <!-- Add Employee Modal -->
        <div id="employeeModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-8 relative">
                <button id="closeModalBtn" class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 text-2xl">&times;</button>
                <h2 class="text-xl font-semibold mb-6">Add New Employee</h2>
                <form id="employeeForm" action="{{ route('users.store') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium mb-1">First Name</label>
                            <input type="text" name="firstName" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Last Name</label>
                            <input type="text" name="lastName" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Email</label>
                            <input type="email" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Phone</label>
                            <input type="text" name="phone" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200" placeholder="+1234567890">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Role</label>
                            <select name="role" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200" required>
                                <option value="">Select Role</option>
                                <option value="admin">Admin</option>
                                <option value="receptionist">Receptionist</option>
                                <option value="staff">Staff</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Password</label>
                            <input type="password" name="password" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200" required minlength="8">
                        </div>
                    </div>
                    <div class="flex items-center mb-6">
                        <input type="checkbox" name="is_active" id="is_active" value="1" checked class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                        <label for="is_active" class="ml-2 text-sm text-gray-900">Active Account</label>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" id="closeModalBtn2" class="px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-100">Cancel</button>
                        <button type="submit" class="px-6 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 flex items-center">
                            <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add Employee
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Employee Modal -->
        <div id="editEmployeeModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-8 relative">
                <button id="closeEditModalBtn" class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 text-2xl">&times;</button>
                <h2 class="text-xl font-semibold mb-6">Edit Employee</h2>
                <form id="editEmployeeForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="user_id" id="edit_user_id">
                    <div class="grid grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium mb-1">First Name</label>
                            <input type="text" name="firstName" id="edit_firstName" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Last Name</label>
                            <input type="text" name="lastName" id="edit_lastName" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Email</label>
                            <input type="email" name="email" id="edit_email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Phone</label>
                            <input type="text" name="phone" id="edit_phone" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200" placeholder="+1234567890">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Role</label>
                            <select name="role" id="edit_role" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200" required>
                                <option value="">Select Role</option>
                                <option value="admin">Admin</option>
                                <option value="receptionist">Receptionist</option>
                                <option value="staff">Staff</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">New Password (optional)</label>
                            <input type="password" name="password" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200" placeholder="Leave blank to keep current" minlength="8">
                        </div>
                    </div>
                    <div class="flex items-center mb-6">
                        <input type="checkbox" name="is_active" id="edit_is_active" value="1" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                        <label for="edit_is_active" class="ml-2 text-sm text-gray-900">Active Account</label>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" id="closeEditModalBtn2" class="px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-100">Cancel</button>
                        <button type="submit" class="px-6 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 flex items-center">
                            <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Update Employee
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Employee Content -->
        <div class="px-8 py-6">
            <!-- Header Section -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold mb-2">Employees</h2>
                <p class="text-gray-500 text-sm">Manage hotel staff and their permissions</p>
            </div>

            <!-- Controls Section -->
            <div class="flex items-center justify-between mb-6">
                <!-- Search -->
                <div class="flex-1 max-w-md">
                    <div class="relative">
                        <input type="text" placeholder="Search employees..." class="w-full px-4 py-2 pl-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <svg class="absolute left-3 top-3 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center space-x-3">
                    <button id="newEmployeeBtn" class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Employee
                    </button>
                </div>
            </div>

            <!-- Employees Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Login</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <span class="h-10 w-10 rounded-full bg-blue-500 text-white flex items-center justify-center font-bold">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-500">ID: {{ $user->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $user->email }}</div>
                                <div class="text-sm text-gray-500">{{ $user->phone ?? 'No phone' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 
                                       ($user->role === 'receptionist' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $user->last_login_at ? $user->last_login_at->format('M j, Y g:i A') : 'Never' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button class="edit-employee-btn text-blue-600 hover:text-blue-900 mr-3" 
                                        data-user-id="{{ $user->id }}"
                                        data-first-name="{{ explode(' ', $user->name)[0] ?? '' }}"
                                        data-last-name="{{ explode(' ', $user->name)[1] ?? '' }}"
                                        data-email="{{ $user->email }}"
                                        data-phone="{{ $user->phone }}"
                                        data-role="{{ $user->role }}"
                                        data-is-active="{{ $user->is_active }}">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" 
                                            {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('employeeModal');
    const editModal = document.getElementById('editEmployeeModal');
    const openModalBtn = document.getElementById('newEmployeeBtn');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const closeModalBtn2 = document.getElementById('closeModalBtn2');
    const closeEditModalBtn = document.getElementById('closeEditModalBtn');
    const closeEditModalBtn2 = document.getElementById('closeEditModalBtn2');
    const form = document.getElementById('employeeForm');
    const editForm = document.getElementById('editEmployeeForm');

    // New Employee Modal
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

    // Edit Employee Modal
    document.addEventListener('click', function(e) {
        if (e.target.closest('.edit-employee-btn')) {
            const btn = e.target.closest('.edit-employee-btn');
            
            // Pre-fill form with user data
            document.getElementById('edit_user_id').value = btn.dataset.userId;
            document.getElementById('edit_firstName').value = btn.dataset.firstName;
            document.getElementById('edit_lastName').value = btn.dataset.lastName;
            document.getElementById('edit_email').value = btn.dataset.email;
            document.getElementById('edit_phone').value = btn.dataset.phone;
            document.getElementById('edit_role').value = btn.dataset.role;
            document.getElementById('edit_is_active').checked = btn.dataset.isActive === '1';
            
            // Set form action
            editForm.action = `/employee/${btn.dataset.userId}`;
            
            editModal.classList.remove('hidden');
        }
    });

    closeEditModalBtn.addEventListener('click', function() {
        editModal.classList.add('hidden');
    });
    
    closeEditModalBtn2.addEventListener('click', function() {
        editModal.classList.add('hidden');
    });

    window.addEventListener('click', function(e) {
        if (e.target === modal) modal.classList.add('hidden');
        if (e.target === editModal) editModal.classList.add('hidden');
    });

    // Form submission handled by Laravel
});
</script>

@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        alert('{{ session('success') }}');
    });
</script>
@endif

@if(session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        alert('{{ session('error') }}');
    });
</script>
@endif
@endsection