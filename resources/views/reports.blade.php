@extends('layouts.dashboard')

@section('content')
<div class="flex min-h-screen">
    @include('components.sidebar')

    <!-- Main Content -->
    <main class="flex-1 bg-[#f8fafc]">
        @include('components.topnav', ['title' => 'Reports & Analytics'])

        <!-- Reports Content -->
        <div class="px-8 py-6">
            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                    @if(session('download_url'))
                        <div class="mt-2">
                            <a href="{{ session('download_url') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                <svg class="h-4 w-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Download Report
                            </a>
                            <span class="mx-2">|</span>
                            <a href="{{ session('view_url') }}" target="_blank" class="text-green-600 hover:text-green-800 font-medium">
                                <svg class="h-4 w-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                View Report
                            </a>
                        </div>
                    @endif
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    {{ session('error') }}
                </div>
            @endif

            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-semibold mb-1">Reports</h2>
                    <p class="text-gray-500 text-sm">Last 30 days • {{ $recentReports->total() ?? 0 }} reports generated</p>
                </div>
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
                        <span class="text-green-500 text-sm font-medium">+{{ $todayRevenueGrowth }}%</span>
                    </div>
                    <p class="text-gray-500 text-sm mb-1">Today's Report</p>
                    <p class="text-2xl font-bold">₱{{ number_format($todayRevenue, 0) }}</p>
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
                        <span class="text-green-500 text-sm font-medium">+{{ $occupancyGrowth }}%</span>
                    </div>
                    <p class="text-gray-500 text-sm mb-1">Arrivals Occupancy</p>
                    <p class="text-2xl font-bold">{{ $currentOccupancy }}%</p>
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
                        <span class="text-green-500 text-sm font-medium">+{{ $revenueGrowth }}%</span>
                    </div>
                    <p class="text-gray-500 text-sm mb-1">Revenue by Room Type</p>
                    <p class="text-2xl font-bold">₱{{ number_format($monthlyRevenue, 0) }}</p>
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
                        <span class="text-green-500 text-sm font-medium">+{{ $recreationalGrowth }}%</span>
                    </div>
                    <p class="text-gray-500 text-sm mb-1">Recreational Performance</p>
                    <p class="text-2xl font-bold">{{ $recreationalBookings }} bookings</p>
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
                
                <form action="{{ route('report.generate') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Report Type</label>
                            <select name="report_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Select report type</option>
                                <option value="revenue">Revenue Report</option>
                                <option value="occupancy">Occupancy Report</option>
                                <option value="guest">Guest Report</option>
                              
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                            <input type="date" name="start_date" value="{{ $defaultStartDate }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                            <input type="date" name="end_date" value="{{ $defaultEndDate }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">PDF Document</label>
                        <div class="flex items-center space-x-2">
                            <input type="text" name="document_name" placeholder="Enter document name" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    
                    <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 font-medium">
                        Generate Report
                    </button>
                </form>
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
                        <span class="text-sm font-bold text-green-600">₱{{ number_format($bestPerformance, 0) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Avg. Daily Revenue</span>
                        <span class="text-sm font-bold text-blue-600">₱{{ number_format($avgDailyRevenue, 0) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Peak Season</span>
                        <span class="text-sm font-medium text-gray-600">{{ $peakSeasonOccupancy }}% Occupancy</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Seasonal Growth</span>
                        <span class="text-sm font-bold text-red-600">+{{ $seasonalGrowth }}%</span>
                    </div>
                </div>
            </div>

            <!-- Recent Reports Section with Pagination -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-6 border-b flex justify-between items-center">
                    <h3 class="text-lg font-semibold flex items-center">
                        <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Recent Reports
                    </h3>
                    
                    @if($recentReports->total() > 0)
                    <div class="text-sm text-gray-500">
                        Showing {{ $recentReports->firstItem() }} to {{ $recentReports->lastItem() }} of {{ $recentReports->total() }} reports
                    </div>
                    @endif
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
                            @if($recentReports->count() > 0)
                                @foreach($recentReports as $report)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $report->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($report->type == 'revenue') bg-green-100 text-green-800
                                            @elseif($report->type == 'occupancy') bg-blue-100 text-blue-800
                                            @elseif($report->type == 'guest') bg-purple-100 text-purple-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst($report->type) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $report->start_date->format('M j') }} - {{ $report->end_date->format('M j, Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $report->created_at->diffForHumans() }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('report.download', $report->id) }}" 
                                               class="text-blue-600 hover:text-blue-900 inline-flex items-center"
                                               title="Download PDF">
                                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                Download
                                            </a>
                                            <a href="{{ route('report.view', $report->id) }}" 
                                               target="_blank"
                                               class="text-green-600 hover:text-green-900 inline-flex items-center"
                                               title="View in Browser">
                                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                View
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center">
                                        <div class="flex flex-col items-center justify-center text-gray-400">
                                            <svg class="h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <p class="text-lg font-medium text-gray-500 mb-1">No reports generated yet</p>
                                            <p class="text-sm">Generate your first report using the form above</p>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($recentReports->hasPages())
                <div class="px-6 py-4 border-t bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Page {{ $recentReports->currentPage() }} of {{ $recentReports->lastPage() }}
                        </div>
                        <div class="flex space-x-2">
                            <!-- Previous Page Button -->
                            @if($recentReports->onFirstPage())
                                <span class="px-3 py-1 bg-gray-200 text-gray-500 rounded cursor-not-allowed">
                                    <svg class="h-4 w-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                    </svg>
                                    Previous
                                </span>
                            @else
                                <a href="{{ $recentReports->previousPageUrl() }}" class="px-3 py-1 bg-white border border-gray-300 text-gray-700 rounded hover:bg-gray-50 hover:border-gray-400">
                                    <svg class="h-4 w-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                    </svg>
                                    Previous
                                </a>
                            @endif

                            <!-- Page Numbers -->
                            <div class="flex space-x-1">
                                @foreach($recentReports->getUrlRange(1, $recentReports->lastPage()) as $page => $url)
                                    @if($page == $recentReports->currentPage())
                                        <span class="px-3 py-1 bg-blue-600 text-white rounded">{{ $page }}</span>
                                    @elseif($page >= $recentReports->currentPage() - 2 && $page <= $recentReports->currentPage() + 2)
                                        <a href="{{ $url }}" class="px-3 py-1 bg-white border border-gray-300 text-gray-700 rounded hover:bg-gray-50 hover:border-gray-400">{{ $page }}</a>
                                    @elseif($page == 1)
                                        <a href="{{ $url }}" class="px-3 py-1 bg-white border border-gray-300 text-gray-700 rounded hover:bg-gray-50 hover:border-gray-400">1</a>
                                        @if($recentReports->currentPage() > 4)
                                            <span class="px-2 py-1 text-gray-500">...</span>
                                        @endif
                                    @elseif($page == $recentReports->lastPage())
                                        @if($recentReports->currentPage() < $recentReports->lastPage() - 3)
                                            <span class="px-2 py-1 text-gray-500">...</span>
                                        @endif
                                        <a href="{{ $url }}" class="px-3 py-1 bg-white border border-gray-300 text-gray-700 rounded hover:bg-gray-50 hover:border-gray-400">{{ $page }}</a>
                                    @endif
                                @endforeach
                            </div>

                            <!-- Next Page Button -->
                            @if($recentReports->hasMorePages())
                                <a href="{{ $recentReports->nextPageUrl() }}" class="px-3 py-1 bg-white border border-gray-300 text-gray-700 rounded hover:bg-gray-50 hover:border-gray-400">
                                    Next
                                    <svg class="h-4 w-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            @else
                                <span class="px-3 py-1 bg-gray-200 text-gray-500 rounded cursor-not-allowed">
                                    Next
                                    <svg class="h-4 w-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </main>
</div>

<!-- Add SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: '{{ session('success') }}',
        showConfirmButton: true,
        timer: 3000
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: '{{ session('error') }}',
        showConfirmButton: true
    });
</script>
@endif

<script>
// Handle report download with loading indicator
document.addEventListener('DOMContentLoaded', function() {
    // Download buttons
    const downloadButtons = document.querySelectorAll('a[href*="download"]');
    
    downloadButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const originalHTML = this.innerHTML;
            this.innerHTML = '<svg class="animate-spin h-4 w-4 inline mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Downloading...';
            this.style.pointerEvents = 'none';
            
            // Reset button after 5 seconds if download doesn't start
            setTimeout(() => {
                this.innerHTML = originalHTML;
                this.style.pointerEvents = 'auto';
            }, 5000);
        });
    });
});
</script>
@endsection