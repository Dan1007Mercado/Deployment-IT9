@extends('layouts.dashboard')

@section('content')
<div class="flex min-h-screen">
    @include('components.sidebar')

    <!-- Main Content -->
    <main class="flex-1 bg-[#f8fafc]">
        @include('components.topnav', ['title' => 'Hotel Management Dashboard'])
        
        <!-- Subheader -->
        <div class="bg-white border-b">
            <div class="px-8 py-4">
                <div class="flex items-center justify-between">
                    
                    <div class="flex items-center space-x-4">
                        <!-- Date Filter -->
                        <form method="GET" action="{{ route('dashboard') }}" class="flex items-center space-x-2">
                            <input type="date" name="start_date" value="{{ $start_date }}" 
                                   class="border rounded px-3 py-2 text-sm">
                            <span class="text-gray-500">to</span>
                            <input type="date" name="end_date" value="{{ $end_date }}" 
                                   class="border rounded px-3 py-2 text-sm">
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded text-sm hover:bg-blue-600">
                                Filter
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="px-8 py-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Available Rooms Card -->
                <a href="{{ route('rooms') }}" class="bg-white rounded-xl shadow p-6 cursor-pointer hover:shadow-lg transition-shadow block border-2 border-green-300">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-600">Available Rooms</p>
                            <p class="text-2xl font-bold text-gray-900 text-right">{{ $available_rooms }}</p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-lg ml-4 border border-green-200">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">{{ $occupied_rooms }} rooms currently occupied</p>
                </a>

                <!-- Today's Check-ins -->
                <a href="{{ route('reservations') }}?filter=today_checkins" class="bg-white rounded-xl shadow p-6 cursor-pointer hover:shadow-lg transition-shadow block border-2 border-blue-300">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-600">Today's Check-ins</p>
                            <p class="text-2xl font-bold text-blue-600 text-right">{{ $today_checkins }}</p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-lg ml-4 border border-blue-200">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Guests arriving today</p>
                </a>

                <!-- Today's Check-outs -->
                <a href="{{ route('reservations') }}?filter=today_checkouts" class="bg-white rounded-xl shadow p-6 cursor-pointer hover:shadow-lg transition-shadow block border-2 border-purple-300">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-600">Today's Check-outs</p>
                            <p class="text-2xl font-bold text-purple-600 text-right">{{ $today_checkouts }}</p>
                        </div>
                        <div class="p-3 bg-purple-100 rounded-lg ml-4 border border-purple-200">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Guests departing today</p>
                </a>

                <!-- Total Revenue -->
                <a href="{{ route('reports') }}" class="bg-white rounded-xl shadow p-6 cursor-pointer hover:shadow-lg transition-shadow block border-2 border-green-300">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                            <p class="text-2xl font-bold text-green-600 text-right">₱{{ number_format($total_revenue, 2) }}</p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-lg ml-4 border border-green-200">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">From {{ $start_date }} to {{ $end_date }}</p>
                </a>
            </div>

            <!-- Additional Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Total Guests -->
                <a href="{{ route('guests') }}" class="bg-white rounded-xl shadow p-6 cursor-pointer hover:shadow-lg transition-shadow block border-2 border-orange-300">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-600">Total Guests</p>
                            <p class="text-2xl font-bold text-orange-600 text-right">{{ $total_guests }}</p>
                        </div>
                        <div class="p-3 bg-orange-100 rounded-lg ml-4 border border-orange-200">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Registered guests in system</p>
                </a>

                <!-- Total Reservations -->
                <a href="{{ route('reservations') }}" class="bg-white rounded-xl shadow p-6 cursor-pointer hover:shadow-lg transition-shadow block border-2 border-indigo-300">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-600">Total Reservations</p>
                            <p class="text-2xl font-bold text-indigo-600 text-right">{{ $total_reservations }}</p>
                        </div>
                        <div class="p-3 bg-indigo-100 rounded-lg ml-4 border border-indigo-200">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">All-time reservations</p>
                </a>
            </div>

            <!-- Analytics and Recent Reservations Side by Side -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Recent Reservations Table - Left Side -->
                <div class="bg-white rounded-xl shadow p-4 border-2 border-gray-300">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-md font-semibold">Recent Reservations</h2>
                        <div class="flex items-center space-x-2">
                            <select class="border rounded px-2 py-1 text-xs">
                                <option>10 entries</option>
                                <option>25 entries</option>
                                <option>50 entries</option>
                                <option>100 entries</option>
                            </select>
                            <a href="{{ route('reservations') }}" class="text-blue-500 hover:text-blue-700 text-xs font-medium">
                                View All →
                            </a>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guest</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room Type</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($recent_reservations->take(10) as $reservation)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 whitespace-nowrap">
                                        <div class="text-xs font-medium text-gray-900">
                                            {{ $reservation->guest->first_name }} {{ $reservation->guest->last_name }}
                                        </div>
                                        <div class="text-xs text-gray-500">{{ $reservation->guest->email }}</div>
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap text-xs text-gray-900">
                                        {{ $reservation->roomType->type_name }}
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap">
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'confirmed' => 'bg-green-100 text-green-800',
                                                'cancelled' => 'bg-red-100 text-red-800',
                                                'completed' => 'bg-blue-100 text-blue-800'
                                            ];
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$reservation->status] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ ucfirst($reservation->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap text-xs font-medium text-gray-900">
                                        ₱{{ number_format($reservation->total_amount, 2) }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-2 text-center text-xs text-gray-500">
                                        No recent reservations found.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination -->
                    <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-200">
                        <div class="text-xs text-gray-500">
                            Showing 1 to 10 of {{ $recent_reservations->count() }} entries
                        </div>
                        <div class="flex space-x-1">
                            <button class="px-3 py-1 text-xs bg-gray-200 rounded hover:bg-gray-300">Previous</button>
                            <button class="px-3 py-1 text-xs bg-blue-500 text-white rounded">1</button>
                            <button class="px-3 py-1 text-xs bg-gray-200 rounded hover:bg-gray-300">2</button>
                            <button class="px-3 py-1 text-xs bg-gray-200 rounded hover:bg-gray-300">3</button>
                            <button class="px-3 py-1 text-xs bg-gray-200 rounded hover:bg-gray-300">Next</button>
                        </div>
                    </div>
                </div>

                <!-- Analytics Section - Right Side -->
                <div class="bg-white rounded-xl shadow p-4 border-2 border-gray-300">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h2 class="text-md font-semibold">Revenue Analytics</h2>
                            <p class="text-xs text-gray-500">Revenue performance overview</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-xs text-gray-600">Last 6 months</span>
                        </div>
                    </div>
                    <!-- Chart - Smaller -->
                    <div class="mb-4">
                        <div class="bg-white rounded">
                            <canvas id="revenueChart" height="120"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Revenue Analytics Line Chart
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('revenueChart');
        
        if (ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($revenue_chart_labels),
                    datasets: [{
                        label: 'Monthly Revenue',
                        data: @json($monthly_revenue_data),
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#3b82f6',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 3,
                        pointHoverRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            callbacks: {
                                label: function(context) {
                                    return '₱' + context.parsed.y.toLocaleString();
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 10
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                font: {
                                    size: 10
                                },
                                callback: function(value) {
                                    return '₱' + (value / 1000).toLocaleString() + 'K';
                                }
                            }
                        }
                    }
                }
            });
        }   
    });
</script>

@endsection