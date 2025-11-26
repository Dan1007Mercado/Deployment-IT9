@extends('layouts.dashboard')

@section('content')
<div class="flex min-h-screen">
    @include('components.sidebar')

    <!-- Main Content -->
    <main class="flex-1 bg-[#f8fafc]">
    @include('components.topnav', [
    'title' => 'Dashboard',
    'subtitle' => 'Quick Overview '
    ])
        
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
                            <a href="{{ route('dashboard') }}" class="bg-gray-500 text-white px-4 py-2 rounded text-sm hover:bg-gray-600">
                                Reset
                            </a>
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
                            <p class="text-xs text-gray-500 mt-1">{{ $occupied_rooms }} rooms currently occupied</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <p class="text-2xl font-bold text-gray-900">{{ $available_rooms }}</p>
                            <div class="p-3 bg-green-100 rounded-lg border border-green-200">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Today's Check-ins -->
                <a href="{{ route('bookings.index') }}?check_in_date={{ today()->format('Y-m-d') }}" class="bg-white rounded-xl shadow p-6 cursor-pointer hover:shadow-lg transition-shadow block border-2 border-blue-300">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-600">Today's Check-ins</p>
                            <p class="text-xs text-gray-500 mt-1">Guests checked in today</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <p class="text-2xl font-bold text-blue-600">{{ $today_checkins }}</p>
                            <div class="p-3 bg-blue-100 rounded-lg border border-blue-200">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Today's Check-outs -->
                <a href="{{ route('bookings.index') }}?check_out_date={{ today()->format('Y-m-d') }}" class="bg-white rounded-xl shadow p-6 cursor-pointer hover:shadow-lg transition-shadow block border-2 border-purple-300">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-600">Today's Check-outs</p>
                            <p class="text-xs text-gray-500 mt-1">Guests checked out today</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <p class="text-2xl font-bold text-purple-600">{{ $today_checkouts }}</p>
                            <div class="p-3 bg-purple-100 rounded-lg border border-purple-200">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Filtered Sales -->
                <a href="{{ route('report') }}?start_date={{ $start_date }}&end_date={{ $end_date }}" class="bg-white rounded-xl shadow p-6 cursor-pointer hover:shadow-lg transition-shadow block border-2 border-green-300">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-600">Filtered Sales</p>
                            <p class="text-xs text-gray-500 mt-1">From {{ $start_date }} to {{ $end_date }}</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <p class="text-2xl font-bold text-green-600">₱{{ number_format($total_revenue, 2) }}</p>
                            <div class="p-3 bg-green-100 rounded-lg border border-green-200">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Additional Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Total Guests -->
                <a href="{{ route('guests') }}" class="bg-white rounded-xl shadow p-6 cursor-pointer hover:shadow-lg transition-shadow block border-2 border-orange-300">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-600">Total Guests</p>
                            <p class="text-xs text-gray-500 mt-1">Registered guests in system</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <p class="text-2xl font-bold text-orange-600">{{ $total_guests }}</p>
                            <div class="p-3 bg-orange-100 rounded-lg border border-orange-200">
                                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Total Reservations -->
                <a href="{{ route('reservations.index') }}" class="bg-white rounded-xl shadow p-6 cursor-pointer hover:shadow-lg transition-shadow block border-2 border-indigo-300">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-600">Total Reservations</p>
                            <p class="text-xs text-gray-500 mt-1">All-time reservations</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <p class="text-2xl font-bold text-indigo-600">{{ $total_reservations }}</p>
                            <div class="p-3 bg-indigo-100 rounded-lg border border-indigo-200">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Analytics and Recent Reservations Side by Side -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Recent Reservations Table - Left Side (Auto height) -->
                <div class="bg-white rounded-xl shadow border-2 border-gray-300">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-gray-800">Recent Reservations</h2>
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('reservations.index') }}" class="text-blue-500 hover:text-blue-700 text-sm font-medium">
                                    View All →
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="p-1">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guest</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room Type</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($recent_reservations as $reservation)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $reservation->guest->first_name }} {{ $reservation->guest->last_name }}
                                            </div>
                                            <div class="text-xs text-gray-500">{{ $reservation->guest->email }}</div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                            {{ $reservation->roomType->type_name }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            @php
                                                $statusColors = [
                                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                                    'confirmed' => 'bg-green-100 text-green-800',
                                                    'cancelled' => 'bg-red-100 text-red-800',
                                                    'completed' => 'bg-blue-100 text-blue-800'
                                                ];
                                            @endphp
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$reservation->status] ?? 'bg-gray-100 text-gray-800' }}">
                                                {{ ucfirst($reservation->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                            ₱{{ number_format($reservation->total_amount, 2) }}
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">
                                            <div class="flex flex-col items-center">
                                                <svg class="w-12 h-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                                </svg>
                                                No recent reservations found.
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Pagination -->
                    @if($recent_reservations->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-xl">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-500">
                                Showing {{ $recent_reservations->firstItem() }} to {{ $recent_reservations->lastItem() }} of {{ $recent_reservations->total() }} entries
                            </div>
                            <div class="flex space-x-1">
                                {{ $recent_reservations->links() }}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Analytics Section - Right Side (Fixed compact height) -->
                <div class="bg-white rounded-xl shadow border-2 border-gray-300 self-start">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-gray-800">Sales Analytics</h2>
                                <p class="text-sm text-gray-500">Revenue performance overview</p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-600 bg-gray-100 px-2 py-1 rounded">Last 6 months</span>
                            </div>
                        </div>
                    </div>
                    <!-- Fixed Compact Chart Container -->
                    <div class="p-6">
                        <div class="relative" style="height: 250px;">
                            <canvas id="revenueChart"></canvas>
                        </div>
                        <!-- Chart Legend -->
                        <div class="flex justify-center mt-4">
                            <div class="flex items-center space-x-2 text-sm text-gray-600">
                                <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                <span>Monthly Revenue</span>
                            </div>
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
                        borderWidth: 2,
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
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleFont: {
                                size: 12
                            },
                            bodyFont: {
                                size: 12
                            },
                            padding: 10,
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
                                },
                                maxRotation: 45
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                font: {
                                    size: 10
                                },
                                callback: function(value) {
                                    if (value >= 1000000) {
                                        return '₱' + (value / 1000000).toFixed(1) + 'M';
                                    } else if (value >= 1000) {
                                        return '₱' + (value / 1000).toFixed(0) + 'K';
                                    }
                                    return '₱' + value;
                                }
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });
        }   
    });
</script>

@endsection