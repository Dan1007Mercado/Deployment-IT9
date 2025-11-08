@extends('layouts.dashboard')

@section('content')
<div class="flex min-h-screen">
    @include('components.sidebar')
    <main class="flex-1 bg-[#f8fafc]">
        @include('components.topnav', ['title' => 'Employee Management'])
        <div class="px-8 py-6">
            @php /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users */ @endphp
            <div class="mb-6">
                <h2 class="text-2xl font-bold mb-2">Users</h2>
                <p class="text-gray-500 text-sm">Manage hotel staff and view user activities</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow p-6 flex flex-col items-center">
                    <div class="text-3xl font-bold mb-2">15</div>
                    <div class="flex items-center space-x-2 text-blue-600"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" /></svg><span>Total Staff</span></div>
                </div>
                <div class="bg-white rounded-lg shadow p-6 flex flex-col items-center">
                    <div class="text-3xl font-bold mb-2">12</div>
                    <div class="flex items-center space-x-2 text-green-600"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 17l6-6 4 4 8-8" /></svg><span>Active Staff</span></div>
                </div>
                <div class="bg-white rounded-lg shadow p-6 flex flex-col items-center">
                    <div class="text-3xl font-bold mb-2">5</div>
                    <div class="flex items-center space-x-2 text-yellow-600"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01" /></svg><span>Outstanding</span></div>
                </div>
                <div class="bg-white rounded-lg shadow p-6 flex flex-col items-center">
                    <div class="text-3xl font-bold mb-2">3</div>
                    <div class="flex items-center space-x-2 text-red-600"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-1.414 1.414A9.969 9.969 0 0021 12c0 2.21-.72 4.253-1.936 5.936l1.414 1.414A11.955 11.955 0 0023 12c0-3.042-1.135-5.824-3-7.864z" /></svg><span>Inactive</span></div>
                </div>
            </div>
            <div class="flex items-center mb-6 space-x-3">
                <input type="text" placeholder="Search by name or role" class="w-full max-w-lg px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button class="px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-700 hover:bg-gray-100">Status</button>
                <button class="px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-700 hover:bg-gray-100">Export Users</button>
                <button id="addUserBtn" class="ml-auto px-6 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 flex items-center"><svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg> Add User</button>
            </div>
                        <div class="bg-white rounded-lg shadow overflow-x-auto">
                                @if(session('success'))
                                    <div class="mb-3 px-4 py-2 rounded bg-emerald-50 text-emerald-700 border border-emerald-200">{{ session('success') }}</div>
                                @endif
                                <table class="min-w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Login</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse(($users ?? collect()) as $user)
                        @php /** @var \App\Models\User $user */ @endphp
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <span class="h-10 w-10 rounded-full bg-blue-500 text-white flex items-center justify-center font-bold mr-3">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($user->name ?? '', 0, 1)) }}</span>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $user->phone ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-semibold">{{ $user->role ?? 'Staff' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" {{ ($user->is_active ?? true) ? 'checked' : '' }} class="sr-only peer">
                                    <div class="relative w-11 h-6 rounded-full bg-gray-200 transition-colors duration-300 shadow-inner peer-checked:bg-emerald-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:h-5 after:w-5 after:rounded-full after:bg-white after:shadow after:transition-all peer-checked:after:translate-x-5"></div>
                                    <span class="ml-2 text-sm font-semibold {{ ($user->is_active ?? true) ? 'text-emerald-600' : 'text-gray-600' }} peer-checked:text-emerald-600">{{ ($user->is_active ?? true) ? 'Active' : 'Inactive' }}</span>
                                </label>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ optional($user->last_login_at)->format('n/j/Y') ?? now()->format('n/j/Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button class="text-blue-600 hover:text-blue-900 mr-3"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 13l6.536-6.536a2 2 0 112.828 2.828L11.828 15H9v-2.828z" /></svg></button>
                                <button class="text-red-600 hover:text-red-900"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                            </td>
                        </tr>
                        @empty
                        <!-- fallback demo rows -->
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <span class="h-10 w-10 rounded-full bg-blue-500 text-white flex items-center justify-center font-bold mr-3">A</span>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">Alice Johnson</div>
                                        <div class="text-sm text-gray-500">alice@hotel.com</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">+1 234 567 890</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-semibold">Manager</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" checked class="sr-only peer">
                                    <div class="relative w-11 h-6 rounded-full bg-gray-200 transition-colors duration-300 shadow-inner peer-checked:bg-emerald-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:h-5 after:w-5 after:rounded-full after:bg-white after:shadow after:transition-all peer-checked:after:translate-x-5"></div>
                                    <span class="ml-2 text-sm font-semibold text-emerald-600 peer-checked:text-emerald-600">Active</span>
                                </label>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">1/12/2024</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button class="text-blue-600 hover:text-blue-900 mr-3"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 13l6.536-6.536a2 2 0 112.828 2.828L11.828 15H9v-2.828z" /></svg></button>
                                <button class="text-red-600 hover:text-red-900"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                            </td>
                        </tr>
                        @endforelse
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <span class="h-10 w-10 rounded-full bg-purple-500 text-white flex items-center justify-center font-bold mr-3">B</span>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">Bob Smith</div>
                                        <div class="text-sm text-gray-500">bob@hotel.com</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">+1 234 567 891</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-semibold">Receptionist</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" checked class="sr-only peer">
                                    <div class="relative w-11 h-6 rounded-full bg-gray-200 transition-colors duration-300 shadow-inner peer-checked:bg-emerald-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:h-5 after:w-5 after:rounded-full after:bg-white after:shadow after:transition-all peer-checked:after:translate-x-5"></div>
                                    <span class="ml-2 text-sm font-semibold text-emerald-600 peer-checked:text-emerald-600">Active</span>
                                </label>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">1/12/2024</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button class="text-blue-600 hover:text-blue-900 mr-3"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 13l6.536-6.536a2 2 0 112.828 2.828L11.828 15H9v-2.828z" /></svg></button>
                                <button class="text-red-600 hover:text-red-900"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <span class="h-10 w-10 rounded-full bg-pink-500 text-white flex items-center justify-center font-bold mr-3">C</span>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">Carol Davis</div>
                                        <div class="text-sm text-gray-500">carol@hotel.com</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">+1 234 567 892</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 rounded-full bg-purple-100 text-purple-700 text-xs font-semibold">Housekeeper</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer">
                                    <div class="relative w-11 h-6 rounded-full bg-gray-200 transition-colors duration-300 shadow-inner peer-checked:bg-emerald-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:h-5 after:w-5 after:rounded-full after:bg-white after:shadow after:transition-all peer-checked:after:translate-x-5"></div>
                                    <span class="ml-2 text-sm font-semibold text-gray-600 peer-checked:text-emerald-600">Inactive</span>
                                </label>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">1/12/2024</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                                <button class="text-blue-600 hover:text-blue-900 mr-3"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 13l6.536-6.536a2 2 0 112.828 2.828L11.828 15H9v-2.828z" /></svg></button>
                                                                <button class="text-red-600 hover:text-red-900"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                                                        </td>
                                                </tr>
                                        </tbody>
                                </table>
                        </div>
                </div>

                <!-- Add User Modal -->
                <div id="userModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
                    <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6 relative">
                        <button id="closeUserModal" class="absolute top-3 right-4 text-gray-400 hover:text-gray-700 text-2xl">&times;</button>
                        <h3 class="text-lg font-semibold mb-4">Add User</h3>
                        <form id="userForm" method="POST" action="{{ route('users.store') }}" class="space-y-4">
                            @csrf
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                        <label class="block text-sm text-gray-600 mb-1">First Name</label>
                                                        <input name="firstName" type="text" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required />
                                                </div>
                                                <div>
                                                        <label class="block text-sm text-gray-600 mb-1">Last Name</label>
                                                        <input name="lastName" type="text" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required />
                                                </div>
                                                <div>
                                                        <label class="block text-sm text-gray-600 mb-1">Email</label>
                                                        <input name="email" type="email" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required />
                                                </div>
                                                <div>
                                                        <label class="block text-sm text-gray-600 mb-1">Phone</label>
                                                        <input name="phone" type="text" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="+63 900 000 0000" />
                                                </div>
                                                <div>
                                                        <label class="block text-sm text-gray-600 mb-1">Role</label>
                                                        <select name="role" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                                <option>Receptionist</option>
                                                                <option>Housekeeper</option>
                                                                <option>Maintenance</option>
                                                                <option>Security</option>
                                                        </select>
                                                </div>
                                                <div>
                                                        <label class="block text-sm text-gray-600 mb-1">Status</label>
                                                        <label class="inline-flex items-center cursor-pointer select-none">
                                                                <input type="checkbox" name="active" checked class="sr-only peer">
                                                                <div class="relative w-11 h-6 rounded-full bg-gray-200 transition-colors duration-300 shadow-inner peer-checked:bg-emerald-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:h-5 after:w-5 after:rounded-full after:bg-white after:shadow after:transition-all peer-checked:after:translate-x-5"></div>
                                                                <span class="ml-2 text-sm font-semibold text-emerald-600 peer-checked:text-emerald-600">Active</span>
                                                        </label>
                                                </div>
                                        </div>
                                        <div class="flex justify-end gap-3 pt-2">
                                                <button type="button" id="cancelUserModal" class="px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-100">Cancel</button>
                                                <button type="submit" class="px-5 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700">Save User</button>
                                        </div>
                                </form>
                        </div>
                </div>
        
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const modal = document.getElementById('userModal');
                    const openBtn = document.getElementById('addUserBtn');
                    const closeBtn = document.getElementById('closeUserModal');
                    const cancelBtn = document.getElementById('cancelUserModal');
                    const form = document.getElementById('userForm');

                    function openModal() { modal.classList.remove('hidden'); }
                    function closeModal() { modal.classList.add('hidden'); }

                    openBtn && openBtn.addEventListener('click', (e) => { e.preventDefault(); openModal(); });
                    closeBtn && closeBtn.addEventListener('click', closeModal);
                    cancelBtn && cancelBtn.addEventListener('click', closeModal);
                    window.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });

                    function roleBadge(role) {
                        const map = {                           
                            'Receptionist': 'bg-green-100 text-green-700',
                            'Housekeeper': 'bg-purple-100 text-purple-700',
                            'Maintenance': 'bg-amber-100 text-amber-700',
                            'Security': 'bg-indigo-100 text-indigo-700'
                        };
                        return map[role] || 'bg-gray-100 text-gray-700';
                    }

                                // Let the form submit normally to the server; just close modal visually
                                form && form.addEventListener('submit', function() { setTimeout(() => modal.classList.add('hidden'), 0); });
                });
                </script>