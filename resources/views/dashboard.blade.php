
@extends('layouts.dashboard')

@section('content')
<div class="flex min-h-screen">
    @include('components.sidebar')

    <!-- Main Content -->
    <main class="flex-1 bg-[#f8fafc]">
        @include('components.topnav', ['title' => 'Hotel Management Dashboard'])

        <!-- Analytics Section -->
        <div class="px-8 py-6">
            <div class="bg-white rounded-xl shadow p-6 mb-8">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-lg font-semibold">Revenue Analytics</h2>
                        <p class="text-sm text-gray-500">Track hotel's revenue performance over time</p>
                    </div>
                    <button class="border px-4 py-2 rounded text-gray-700 flex items-center"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v16h16V4H4zm8 8v4m0-4V8m0 4h4m-4 0H8" /></svg>Refresh</button>
                </div>
                <!-- Chart Placeholder -->
                <div class="mb-6">
                    <div class="flex items-center space-x-6 mb-4">
                        <span class="flex items-center"><span class="w-5 h-2 bg-blue-500 mr-2"></span>Room Revenue</span>
                        <span class="flex items-center"><span class="w-5 h-2 bg-yellow-400 mr-2"></span>Additional Services</span>
                    </div>
                    <div class="bg-white rounded">
                        <canvas id="revenueChart" height="80"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recent Reservations -->
            <div>
                <h2 class="text-lg font-semibold mb-4">Recent Reservations</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Reservation Card -->
                    <div class="bg-white rounded-lg shadow p-4 flex flex-col justify-between">
                        <div>
                            <div class="font-semibold">Jane Smith</div>
                            <div class="text-sm text-gray-500 mb-2">102 – Deluxe</div>
                            <span class="inline-block px-3 py-1 text-xs rounded-full bg-green-100 text-green-700 mb-2">checked-in</span>
                            <div class="text-xs text-gray-600">Check-in:</div>
                            <div class="text-sm font-medium">Nov 14, 2025</div>
                            <div class="text-xs text-gray-600">Check-out:</div>
                            <div class="text-sm font-medium mb-2">Nov 18, 2025</div>
                        </div>
                        <div class="flex items-center justify-between mt-2">
                            <div class="font-bold text-lg">₱345</div>
                            <div class="flex space-x-2">
                                <a href="#" class="text-blue-500 hover:text-blue-700"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 13l6.536-6.536a2 2 0 112.828 2.828L11.828 15H9v-2.828z" /></svg></a>
                                <a href="#" class="text-red-500 hover:text-red-700"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></a>
                            </div>
                        </div>
                    </div>
                    <!-- Repeat for other reservations -->
                    <div class="bg-white rounded-lg shadow p-4 flex flex-col justify-between">
                        <div>
                            <div class="font-semibold">Emily Davis</div>
                            <div class="text-sm text-gray-500 mb-2">305 – Standard</div>
                            <span class="inline-block px-3 py-1 text-xs rounded-full bg-purple-100 text-purple-700 mb-2">upcoming</span>
                            <div class="text-xs text-gray-600">Check-in:</div>
                            <div class="text-sm font-medium">Nov 15, 2025</div>
                            <div class="text-xs text-gray-600">Check-out:</div>
                            <div class="text-sm font-medium mb-2">Nov 17, 2025</div>
                        </div>
                        <div class="flex items-center justify-between mt-2">
                            <div class="font-bold text-lg">₱198</div>
                            <div class="flex space-x-2">
                                <a href="#" class="text-blue-500 hover:text-blue-700"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 13l6.536-6.536a2 2 0 112.828 2.828L11.828 15H9v-2.828z" /></svg></a>
                                <a href="#" class="text-red-500 hover:text-red-700"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></a>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-4 flex flex-col justify-between">
                        <div>
                            <div class="font-semibold">Sarah Johnson</div>
                            <div class="text-sm text-gray-500 mb-2">215 – Suite</div>
                            <span class="inline-block px-3 py-1 text-xs rounded-full bg-green-100 text-green-700 mb-2">checked-in</span>
                            <div class="text-xs text-gray-600">Check-in:</div>
                            <div class="text-sm font-medium">Nov 12, 2025</div>
                            <div class="text-xs text-gray-600">Check-out:</div>
                            <div class="text-sm font-medium mb-2">Nov 20, 2025</div>
                        </div>
                        <div class="flex items-center justify-between mt-2">
                            <div class="font-bold text-lg">₱1,495</div>
                            <div class="flex space-x-2">
                                <a href="#" class="text-blue-500 hover:text-blue-700"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 13l6.536-6.536a2 2 0 112.828 2.828L11.828 15H9v-2.828z" /></svg></a>
                                <a href="#" class="text-red-500 hover:text-red-700"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></a>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-4 flex flex-col justify-between">
                        <div>
                            <div class="font-semibold">Michael Brown</div>
                            <div class="text-sm text-gray-500 mb-2">401 – Standard</div>
                            <span class="inline-block px-3 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700 mb-2">pending</span>
                            <div class="text-xs text-gray-600">Check-in:</div>
                            <div class="text-sm font-medium">Nov 16, 2025</div>
                            <div class="text-xs text-gray-600">Check-out:</div>
                            <div class="text-sm font-medium mb-2">Nov 21, 2025</div>
                        </div>
                        <div class="flex items-center justify-between mt-2">
                            <div class="font-bold text-lg">₱299</div>
                            <div class="flex space-x-2">
                                <a href="#" class="text-blue-500 hover:text-blue-700"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 13l6.536-6.536a2 2 0 112.828 2.828L11.828 15H9v-2.828z" /></svg></a>
                                <a href="#" class="text-red-500 hover:text-red-700"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></a>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-4 flex flex-col justify-between">
                        <div>
                            <div class="font-semibold">Robert Wilson</div>
                            <div class="text-sm text-gray-500 mb-2">508 – Deluxe</div>
                            <span class="inline-block px-3 py-1 text-xs rounded-full bg-green-100 text-green-700 mb-2">checked-in</span>
                            <div class="text-xs text-gray-600">Check-in:</div>
                            <div class="text-sm font-medium">Nov 14, 2025</div>
                            <div class="text-xs text-gray-600">Check-out:</div>
                            <div class="text-sm font-medium mb-2">Nov 20, 2025</div>
                        </div>
                        <div class="flex items-center justify-between mt-2">
                            <div class="font-bold text-lg">₱1,100</div>
                            <div class="flex space-x-2">
                                <a href="#" class="text-blue-500 hover:text-blue-700"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 13l6.536-6.536a2 2 0 112.828 2.828L11.828 15H9v-2.828z" /></svg></a>
                                <a href="#" class="text-red-500 hover:text-red-700"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></a>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-4 flex flex-col justify-between">
                        <div>
                            <div class="font-semibold">Chloe Garcia</div>
                            <div class="text-sm text-gray-500 mb-2">203 – Standard</div>
                            <span class="inline-block px-3 py-1 text-xs rounded-full bg-red-100 text-red-700 mb-2">cancelled</span>
                            <div class="text-xs text-gray-600">Check-in:</div>
                            <div class="text-sm font-medium">Nov 18, 2025</div>
                            <div class="text-xs text-gray-600">Check-out:</div>
                            <div class="text-sm font-medium mb-2">Nov 22, 2025</div>
                        </div>
                        <div class="flex items-center justify-between mt-2">
                            <div class="font-bold text-lg">₱150</div>
                            <div class="flex space-x-2">
                                <a href="#" class="text-blue-500 hover:text-blue-700"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 13l6.536-6.536a2 2 0 112.828 2.828L11.828 15H9v-2.828z" /></svg></a>
                                <a href="#" class="text-red-500 hover:text-red-700"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></a>
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
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [
                        {
                            label: 'Room Revenue',
                            data: [12000, 15000, 13500, 18000, 21000, 19500, 23000, 25000, 22000, 26000, 28000, 30000],
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: '#3b82f6',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        },
                        {
                            label: 'Additional Services',
                            data: [3000, 3500, 4000, 4200, 5000, 4800, 5500, 6000, 5800, 6500, 7000, 7500],
                            borderColor: '#fbbf24',
                            backgroundColor: 'rgba(251, 191, 36, 0.1)',
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: '#fbbf24',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }
                    ]
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
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: {
                                size: 14
                            },
                            bodyFont: {
                                size: 13
                            },
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += '₱' + context.parsed.y.toLocaleString();
                                    return label;
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
                                    size: 12
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                font: {
                                    size: 12
                                },
                                callback: function(value) {
                                    return '₱' + (value / 1000) + 'K';
                                }
                            }
                        }
                    },
                    interaction: {
                        mode: 'nearest',
                        axis: 'x',
                        intersect: false
                    }
                }
            });
        }
    });
</script>

@endsection