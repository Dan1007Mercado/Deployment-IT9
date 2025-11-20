<div id="payment-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-t-2xl">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold">Complete Reservation & Payment</h3>
                <button type="button" onclick="closePaymentModal()" class="text-white hover:text-gray-200 transition-colors">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6">
            <!-- Loading State -->
            <div id="payment-loading" class="hidden">
                <div class="flex items-center justify-center py-8">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
                </div>
            </div>

            <!-- Content that will be populated -->
            <div id="payment-content" class="hidden">
                <!-- Guest & Reservation Details will go here -->
            </div>

            <!-- Error State -->
            <div id="payment-error" class="hidden">
                <div class="text-center py-8">
                    <svg class="h-16 w-16 text-red-500 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                    <p class="text-red-600" id="error-message">Failed to load reservation details</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentReservation = null;

function openPaymentModal(reservationId) {
    console.log('Opening payment modal for reservation:', reservationId);
    
    // Show modal and loading state
    document.getElementById('payment-modal').classList.remove('hidden');
    document.getElementById('payment-loading').classList.remove('hidden');
    document.getElementById('payment-content').classList.add('hidden');
    document.getElementById('payment-error').classList.add('hidden');
    document.body.style.overflow = 'hidden';

    // Fetch reservation details
    fetch(`/payments/reservations/${reservationId}/payment-details`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Received data:', data);
            
            if (data.success) {
                currentReservation = data.reservation;
                showPaymentContent();
            } else {
                throw new Error(data.message || 'Failed to load reservation details');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showPaymentError(error.message);
        });
}

function showPaymentContent() {
    document.getElementById('payment-loading').classList.add('hidden');
    document.getElementById('payment-error').classList.add('hidden');
    
    const content = document.getElementById('payment-content');
    content.classList.remove('hidden');
    content.innerHTML = `
        <!-- Guest & Reservation Details -->
        <div class="mb-6">
            <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-3">Reservation Details</h4>
            <div class="bg-gray-50 rounded-xl p-4 space-y-3">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500">Guest Name</p>
                        <p class="font-medium">${currentReservation.guest.first_name} ${currentReservation.guest.last_name}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Contact</p>
                        <p class="font-medium">${currentReservation.guest.email}<br>${currentReservation.guest.contact_number}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Stay Period</p>
                        <p class="font-medium">${currentReservation.check_in_date} to ${currentReservation.check_out_date}<br>(${currentReservation.nights} nights)</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Total Amount</p>
                        <p class="font-medium text-lg text-green-600">₱${parseFloat(currentReservation.total_amount).toLocaleString()}</p>
                    </div>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Rooms</p>
                    <p class="font-medium">${currentReservation.room_numbers}</p>
                </div>
            </div>
        </div>

        <!-- Simple Payment Method -->
        <div class="mb-6">
            <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-3">Payment Method</h4>
            <div class="space-y-3">
                <button type="button" 
                        onclick="processCashPayment()"
                        class="w-full p-4 border-2 border-blue-500 bg-blue-50 rounded-xl text-center transition-all hover:bg-blue-100">
                    <div class="flex flex-col items-center">
                        <svg class="h-8 w-8 text-blue-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span class="font-medium text-blue-700">Cash Payment</span>
                        <span class="text-sm text-blue-600 mt-1">Mark as Paid with Cash</span>
                    </div>
                </button>
                
                <button type="button" 
                        onclick="processCardPayment()"
                        class="w-full p-4 border-2 border-purple-500 bg-purple-50 rounded-xl text-center transition-all hover:bg-purple-100">
                    <div class="flex flex-col items-center">
                        <svg class="h-8 w-8 text-purple-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        <span class="font-medium text-purple-700">Card Payment (Demo)</span>
                        <span class="text-sm text-purple-600 mt-1">Simulate Card Payment</span>
                    </div>
                </button>
            </div>
        </div>

        <!-- Test Info -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
            <p class="text-sm text-yellow-800">
                <strong>Demo Mode:</strong> This is a simulation. No real payments are processed.
            </p>
        </div>
    `;
}

function showPaymentError(message) {
    document.getElementById('payment-loading').classList.add('hidden');
    document.getElementById('payment-content').classList.add('hidden');
    
    const errorDiv = document.getElementById('payment-error');
    errorDiv.classList.remove('hidden');
    document.getElementById('error-message').textContent = message;
}

function closePaymentModal() {
    document.getElementById('payment-modal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    currentReservation = null;
}

function processCashPayment() {
    if (!currentReservation) return;
    
    if (confirm('Mark this reservation as paid with cash and confirm it?')) {
        processPayment('cash');
    }
}

function processCardPayment() {
    if (!currentReservation) return;
    
    if (confirm('Simulate card payment and confirm this reservation?')) {
        processPayment('card');
    }
}

function processPayment(method) {
    const formData = new FormData();
    formData.append('reservation_id', currentReservation.reservation_id);
    formData.append('_token', '{{ csrf_token() }}');

    const endpoint = method === 'cash' ? '/payments/process-cash' : '/payments/process-card';
    
    // Add amount for cash payments
    if (method === 'cash') {
        formData.append('amount_paid', currentReservation.total_amount);
    }

    fetch(endpoint, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ ' + data.message);
            closePaymentModal();
            // Reload page to see updated status
            setTimeout(() => window.location.reload(), 1000);
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ Payment failed: ' + error.message);
    });
}

// Close modal when clicking outside
document.getElementById('payment-modal').addEventListener('click', function(e) {
    if (e.target.id === 'payment-modal') {
        closePaymentModal();
    }
});
</script>