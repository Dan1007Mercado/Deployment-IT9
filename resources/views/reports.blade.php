@extends('layouts.dashboard')

@section('content')
<div class="flex min-h-screen">
    @include('components.sidebar')

    <!-- Main Content -->
    <main class="flex-1 bg-[#f8fafc]">
        @include('components.topnav', ['title' => 'Reports & Analytics'])

        <!-- Reports Content -->
        <div class="px-8 py-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-semibold mb-1">Reports</h2>
                    <p class="text-gray-500 text-sm">Last 30 days • See 1</p>
                </div>
                <button class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Report
                </button>
            </div>

            <!-- Metrics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <!-- Today's Report Card -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-start justify-between mb-2">
                        <div class="bg-blue-100 p-2 rounded">
                            <svg class="h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <span class="text-green-500 text-sm font-medium">+5%</span>
                    </div>
                    <p class="text-gray-500 text-sm mb-1">Today's Report</p>
                    <p class="text-2xl font-bold">₱45,000</p>
                    <p class="text-xs text-gray-400 mt-1">Total revenue today and arrival</p>
                </div>

                <!-- Arrivals Occupancy Card -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-start justify-between mb-2">
                        <div class="bg-green-100 p-2 rounded">
                            <svg class="h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <span class="text-green-500 text-sm font-medium">+7%</span>
                    </div>
                    <p class="text-gray-500 text-sm mb-1">Arrivals Occupancy</p>
                    <p class="text-2xl font-bold">78%</p>
                    <p class="text-xs text-gray-400 mt-1">Occupancy level and arrival</p>
                </div>

                <!-- Revenue by Room Type Card -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-start justify-between mb-2">
                        <div class="bg-purple-100 p-2 rounded">
                            <svg class="h-6 w-6 text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                        </div>
                        <span class="text-green-500 text-sm font-medium">+3%</span>
                    </div>
                    <p class="text-gray-500 text-sm mb-1">Revenue by Room Type</p>
                    <p class="text-2xl font-bold">₱320,000</p>
                    <p class="text-xs text-gray-400 mt-1">Revenue by types by room category</p>
                </div>

                <!-- Recreational Performance Card -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-start justify-between mb-2">
                        <div class="bg-orange-100 p-2 rounded">
                            <svg class="h-6 w-6 text-orange-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <span class="text-green-500 text-sm font-medium">+2%</span>
                    </div>
                    <p class="text-gray-500 text-sm mb-1">Recreational Performance</p>
                    <p class="text-2xl font-bold">15 bookings</p>
                    <p class="text-xs text-gray-400 mt-1">Facility including entertainment and self</p>
                </div>
            </div>

            <!-- Generate Reports Section -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold mb-4 flex items-center">
                    <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Generate Reports
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Report Type</label>
                        <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option>Select report type</option>
                            <option>Revenue Report</option>
                            <option>Occupancy Report</option>
                            <option>Guest Report</option>
                            <option>Financial Report</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                        <input type="date" value="01/07/2024" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                        <input type="date" value="31/12/2024" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">PDF Document</label>
                    <div class="flex items-center space-x-2">
                        <input type="text" placeholder="Enter document name" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <button class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Browse</button>
                    </div>
                </div>
                
                <button class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 font-medium">
                    Generate Report
                </button>
            </div>

            <!-- Quick Insights -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold mb-4 flex items-center">
                    <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    Quick Insights
                </h3>
                
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Best Performance</span>
                        <span class="text-sm font-bold text-green-600">₱58,000</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Avg. Daily Revenue</span>
                        <span class="text-sm font-bold text-blue-600">₱12,400</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Peak Season</span>
                        <span class="text-sm font-medium text-gray-600">85% Occupancy</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Seasonal Growth</span>
                        <span class="text-sm font-bold text-red-600">+18.5%</span>
                    </div>
                </div>
            </div>

            <!-- Recent Reports -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold flex items-center">
                        <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Recent Reports
                    </h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Report Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Range</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Generated</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Monthly Revenue Report</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Revenue</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Oct 1 - Oct 31, 2025</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2 days ago</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button class="text-blue-600 hover:text-blue-900 mr-3">Download</button>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Occupancy Analysis</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Occupancy</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Dec 1 - Dec 31, 2025</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">1 day ago</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button class="text-blue-600 hover:text-blue-900 mr-3">Download</button>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Guest Demographics</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Guest</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Jan 1 - Dec 31, 2025</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">3 days ago</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button class="text-blue-600 hover:text-blue-900 mr-3">Download</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection