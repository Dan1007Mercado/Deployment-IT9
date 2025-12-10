@extends('layouts.dashboard')

@section('content')
<div class="flex min-h-screen">
    @include('components.sidebar')

    <!-- Main Content -->
    <main class="flex-1 bg-[#f8fafc]">
        @include('components.topnav', ['title' => 'Transactions', 'subtitle' => 'Track all guest payments and financial transactions'])

        <!-- Transactions Content -->
        <div class="px-8 py-6">
            <!-- Summary Cards - Updated Design -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Total Sales Card -->
                <div class="bg-white rounded-xl shadow p-6 border-2 border-blue-300 hover:shadow-lg transition-shadow">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-600">Total Sales</p>
                            <p class="text-xs text-gray-500 mt-1">All-time revenue</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <p class="text-2xl font-bold text-blue-600">₱{{ number_format($totalRevenue, 2) }}</p>
                            <div class="p-3 bg-blue-100 rounded-lg border border-blue-200">
                                <svg class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    @if($revenueGrowth > 0)
                    <div class="mt-4 flex items-center text-xs text-green-600">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                        <span>↑ {{ $revenueGrowth }}% from last month</span>
                    </div>
                    @endif
                </div>

                <!-- Completed Today Card -->
                <div class="bg-white rounded-xl shadow p-6 border-2 border-green-300 hover:shadow-lg transition-shadow">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-600">Completed Today</p>
                            <p class="text-xs text-gray-500 mt-1">Today's successful payments</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <p class="text-2xl font-bold text-green-600">₱{{ number_format($todayRevenue, 2) }}</p>
                            <div class="p-3 bg-green-100 rounded-lg border border-green-200">
                                <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 text-xs text-gray-600">
                        {{ $todayCount }} payments completed
                    </div>
                </div>

            </div>

            <!-- Controls -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center space-x-3 flex-1">
                    <form method="GET" action="{{ route('guests') }}" id="searchForm" class="flex items-center space-x-3 flex-1">
                        <div class="relative flex-1 max-w-md">
                            <input type="text" 
                                   name="search" 
                                   id="searchInput" 
                                   value="{{ $search ?? '' }}" 
                                   placeholder="Search by guest name, room, or transaction ID..." 
                                   class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                   autocomplete="off">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            @if(!empty($search))
                            <button type="button" 
                                    onclick="clearSearch()" 
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                            @endif
                        </div>
                        <select name="status" 
                                class="px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                onchange="this.form.submit()">
                            <option value="">All Status</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        </select>
                        <select name="method" 
                                class="px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                onchange="this.form.submit()">
                            <option value="">All Methods</option>
                            <option value="cash" {{ request('method') == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="credit_card" {{ request('method') == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                            
                        </select>
                    </form>
                </div>
            </div>

            <!-- Transactions Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">All Transactions</h3>
                        <div class="flex items-center space-x-4">
                            <div class="text-sm text-gray-500">
                                Showing {{ $transactions->firstItem() ?? 0 }}-{{ $transactions->lastItem() ?? 0 }} of {{ $transactions->total() }} results
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaction ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guest</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Method</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($transactions as $transaction)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <!-- Masked ID for display -->
                                        <div class="text-sm font-medium text-gray-900">
                                            @if($transaction->transaction_id)
                                                @php
                                                    $transactionId = (string) $transaction->transaction_id;
                                                    $maskedId = substr($transactionId, 0, 4) . str_repeat('*', max(0, strlen($transactionId) - 8)) . substr($transactionId, -4);
                                                @endphp
                                                #{{ $maskedId }}
                                            @else
                                                #N/A
                                            @endif
                                        </div>
                                        <!-- Full ID for admin (shown on hover or with permission) -->
                                        <div class="text-xs text-gray-500 mt-1 hidden admin-view" data-full-id="{{ $transaction->transaction_id ?? 'N/A' }}">
                                            Full ID: #{{ $transaction->transaction_id ?? 'N/A' }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 rounded-full bg-blue-500 text-white flex items-center justify-center font-bold mr-3">
                                            {{ substr($transaction->booking->reservation->guest->first_name ?? 'G', 0, 1) }}{{ substr($transaction->booking->reservation->guest->last_name ?? 'U', 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $transaction->booking->reservation->guest->first_name ?? 'Guest' }} {{ $transaction->booking->reservation->guest->last_name ?? 'User' }}
                                            </div>
                                            <div class="text-sm text-gray-500">{{ $transaction->booking->reservation->guest->email ?? 'No email' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $transaction->booking->rooms->first()->room->room_number ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500">{{ $transaction->booking->reservation->roomType->type_name ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">₱{{ number_format($transaction->amount, 2) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @switch($transaction->payment_method)
                                            @case('credit_card')
                                            @case('debit_card')
                                                <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                                </svg>
                                                @break
                                            @case('cash')
                                                <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                @break
                                            @case('gcash')
                                            @case('paymaya')
                                                <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                </svg>
                                                @break
                                            @default
                                                <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                        @endswitch
                                        <span class="text-sm text-gray-900 capitalize">
                                            {{ str_replace('_', ' ', $transaction->payment_method) }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $transaction->payment_date->format('M j, Y') }}</div>
                                    <div class="text-sm text-gray-500">{{ $transaction->payment_date->format('h:i A') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @switch($transaction->payment_status)
                                        @case('completed')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                                <span class="mr-1.5">✅</span>
                                                Completed
                                            </span>
                                            @break
                                        @case('refunded')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                                <span class="mr-1.5">↩️</span>
                                                Refunded
                                            </span>
                                            @break
                                        @default
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                                <span class="mr-1.5">📄</span>
                                                {{ $transaction->payment_status }}
                                            </span>
                                    @endswitch
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="h-16 w-16 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">
                                            @if(!empty($search))
                                                No transactions found for "{{ $search }}"
                                            @else
                                                No transactions found
                                            @endif
                                        </h3>
                                        <p class="text-gray-500 mb-6">
                                            @if(!empty($search))
                                                Try adjusting your search terms
                                            @else
                                                All transactions will appear here
                                            @endif
                                        </p>
                                        @if(!empty($search))
                                        <button onclick="clearSearch()" 
                                                class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                                            Clear Search
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($transactions->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <!-- Entries Info -->
                        <div class="text-sm text-gray-600">
                            Showing {{ $transactions->firstItem() ?? 0 }} to {{ $transactions->lastItem() ?? 0 }} of {{ $transactions->total() }} entries
                        </div>

                        <!-- Pagination -->
                        <div class="flex items-center space-x-2">
                            <!-- Previous Button -->
                            @if($transactions->onFirstPage())
                            <span class="px-3 py-1.5 border border-gray-300 rounded-lg text-gray-400 cursor-not-allowed text-sm">
                                Previous
                            </span>
                            @else
                            <a href="{{ $transactions->previousPageUrl() }}{{ !empty($search) ? '&search=' . $search : '' }}{{ request('status') ? '&status=' . request('status') : '' }}{{ request('method') ? '&method=' . request('method') : '' }}{{ request('per_page') ? '&per_page=' . request('per_page') : '' }}" 
                               class="px-3 py-1.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors text-sm">
                                Previous
                            </a>
                            @endif

                            <!-- Page Numbers -->
                            <div class="flex items-center space-x-1">
                                @foreach ($transactions->getUrlRange(1, $transactions->lastPage()) as $page => $url)
                                    @if ($page == $transactions->currentPage())
                                    <span class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-sm font-medium">
                                        {{ $page }}
                                    </span>
                                    @else
                                    <a href="{{ $url }}{{ !empty($search) ? '&search=' . $search : '' }}{{ request('status') ? '&status=' . request('status') : '' }}{{ request('method') ? '&method=' . request('method') : '' }}{{ request('per_page') ? '&per_page=' . request('per_page') : '' }}" 
                                       class="px-3 py-1.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors text-sm">
                                        {{ $page }}
                                    </a>
                                    @endif
                                @endforeach
                            </div>

                            <!-- Next Button -->
                            @if($transactions->hasMorePages())
                            <a href="{{ $transactions->nextPageUrl() }}{{ !empty($search) ? '&search=' . $search : '' }}{{ request('status') ? '&status=' . request('status') : '' }}{{ request('method') ? '&method=' . request('method') : '' }}{{ request('per_page') ? '&per_page=' . request('per_page') : '' }}" 
                               class="px-3 py-1.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors text-sm">
                                Next
                            </a>
                            @else
                            <span class="px-3 py-1.5 border border-gray-300 rounded-lg text-gray-400 cursor-not-allowed text-sm">
                                Next
                            </span>
                            @endif
                        </div>

                        <!-- Entries Per Page -->
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-600">Show</span>
                            <select id="per-page-filter" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" onchange="changePerPage(this.value)">
                                <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                                <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
                                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                            </select>
                            <span class="text-sm text-gray-600">entries</span>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </main>
</div>

<!-- JavaScript for showing full ID on hover for admins -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show full transaction ID on hover for admins
    const transactionCells = document.querySelectorAll('td:first-child');
    transactionCells.forEach(cell => {
        const adminView = cell.querySelector('.admin-view');
        if (adminView) {
            cell.addEventListener('mouseenter', function() {
                // Check if user is admin
                const isAdmin = {{ auth()->user() && auth()->user()->role === 'admin' ? 'true' : 'false' }};
                if (isAdmin) {
                    adminView.classList.remove('hidden');
                }
            });
            
            cell.addEventListener('mouseleave', function() {
                adminView.classList.add('hidden');
            });
        }
    });

    // Entries per page filter
    function changePerPage(value) {
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', value);
        // Preserve search and filter parameters
        @if(!empty($search))
            url.searchParams.set('search', '{{ $search }}');
        @endif
        @if(request('status'))
            url.searchParams.set('status', '{{ request('status') }}');
        @endif
        @if(request('method'))
            url.searchParams.set('method', '{{ request('method') }}');
        @endif
        window.location.href = url.toString();
    }

    // Auto-submit search form when typing (with debounce)
    const searchInput = document.getElementById('searchInput');
    let searchTimeout;
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                document.getElementById('searchForm').submit();
            }, 500);
        });
    }
    
    // Clear search function
    window.clearSearch = function() {
        searchInput.value = '';
        document.getElementById('searchForm').submit();
    }
});
</script>

<style>
.overflow-x-auto::-webkit-scrollbar {
    height: 6px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
@endsection