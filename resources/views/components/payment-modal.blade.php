<div id="payment-modal" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 hidden backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl mx-4 max-h-[95vh] overflow-hidden transform transition-all duration-300 scale-95">
        <!-- Header -->
        <div class="px-8 py-6 bg-gradient-to-r from-blue-600 to-blue-700 text-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold">Complete Payment</h3>
                        <p class="text-blue-100 text-sm">Secure payment processing</p>
                    </div>
                </div>
                <button type="button" onclick="closePaymentModal()" class="text-white hover:text-blue-200 transition-colors p-2 rounded-lg hover:bg-white hover:bg-opacity-10">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Progress Steps -->
        <div class="px-8 py-4 bg-gray-50 border-b">
            <div class="flex items-center justify-center space-x-8">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-semibold">1</div>
                    <span class="text-sm font-medium text-blue-600">Reservation Details</span>
                </div>
                <div class="w-12 h-0.5 bg-gray-300"></div>
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center text-sm font-semibold">2</div>
                    <span class="text-sm font-medium text-gray-500">Payment Method</span>
                </div>
                <div class="w-12 h-0.5 bg-gray-300"></div>
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center text-sm font-semibold">3</div>
                    <span class="text-sm font-medium text-gray-500">Confirmation</span>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="p-8">
            <!-- Loading State -->
            <div id="payment-loading" class="hidden">
                <div class="flex flex-col items-center justify-center py-12 space-y-4">
                    <div class="animate-spin rounded-full h-16 w-16 border-4 border-blue-200 border-t-blue-600"></div>
                    <p class="text-gray-600">Loading payment details...</p>
                </div>
            </div>

            <!-- Content that will be populated -->
            <div id="payment-content" class="hidden">
                <!-- Content will be populated here -->
            </div>

            <!-- Error State -->
            <div id="payment-error" class="hidden">
                <div class="text-center py-12">
                    <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-800 mb-2">Unable to Load Details</h4>
                    <p class="text-gray-600 mb-6" id="error-message">Failed to load reservation details</p>
                    <button onclick="closePaymentModal()" class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentReservation = null;

function openPaymentModal(reservationId) {
    console.log('Opening payment modal for reservation:', reservationId);
    
    // Show modal with animation
    const modal = document.getElementById('payment-modal');
    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.querySelector('.transform').classList.remove('scale-95');
        modal.querySelector('.transform').classList.add('scale-100');
    }, 10);
    
    document.getElementById('payment-loading').classList.remove('hidden');
    document.getElementById('payment-content').classList.add('hidden');
    document.getElementById('payment-error').classList.add('hidden');
    document.body.style.overflow = 'hidden';

    // Fetch reservation details
    fetch(`/payments/reservations/${reservationId}/payment-details`)
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
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
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Reservation Summary -->
            <div class="lg:col-span-2">
                <div class="bg-white border border-gray-200 rounded-xl p-6">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">Reservation Summary</h4>
                    
                    <!-- Guest Information -->
                    <div class="mb-6">
                        <h5 class="text-sm font-medium text-gray-700 mb-3">Guest Information</h5>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Guest Name</p>
                                    <p class="font-medium">${currentReservation.guest.first_name} ${currentReservation.guest.last_name}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Email</p>
                                    <p class="font-medium text-sm">${currentReservation.guest.email}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                                <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Contact Number</p>
                                    <p class="font-medium">${currentReservation.guest.contact_number}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stay Details -->
                    <div class="mb-6">
                        <h5 class="text-sm font-medium text-gray-700 mb-3">Stay Details</h5>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="text-center p-4 bg-blue-50 rounded-lg">
                                <p class="text-2xl font-bold text-blue-600">${currentReservation.nights}</p>
                                <p class="text-xs text-gray-600">Nights</p>
                            </div>
                            <div class="text-center p-4 bg-green-50 rounded-lg">
                                <p class="text-sm font-semibold text-green-700">${currentReservation.check_in_date}</p>
                                <p class="text-xs text-gray-600">Check-in</p>
                            </div>
                            <div class="text-center p-4 bg-purple-50 rounded-lg">
                                <p class="text-sm font-semibold text-purple-700">${currentReservation.check_out_date}</p>
                                <p class="text-xs text-gray-600">Check-out</p>
                            </div>
                        </div>
                    </div>

                    <!-- Room Information -->
                    <div>
                        <h5 class="text-sm font-medium text-gray-700 mb-3">Room Assignment</h5>
                        <div class="flex items-center space-x-3 p-4 bg-gray-50 rounded-lg">
                            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Assigned Rooms</p>
                                <p class="font-medium">${currentReservation.room_numbers}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Section -->
            <div class="space-y-6">
                <!-- Total Amount -->
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white">
                    <p class="text-sm opacity-90">Total Amount</p>
                    <p class="text-3xl font-bold">₱${parseFloat(currentReservation.total_amount).toLocaleString()}</p>
                    <p class="text-xs opacity-80 mt-1">Inclusive of all taxes and fees</p>
                </div>

                <!-- Payment Methods -->
                <div class="bg-white border border-gray-200 rounded-xl p-6">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">Select Payment Method</h4>
                    
                    <div class="space-y-3">
                        <!-- Cash Payment -->
                        <button onclick="processCashPayment()" class="w-full p-4 border-2 border-gray-200 hover:border-blue-500 bg-white rounded-xl text-left transition-all duration-200 hover:shadow-md group">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-800">Cash Payment</p>
                                    <p class="text-sm text-gray-600">Pay with cash at reception</p>
                                </div>
                                <div class="w-2 h-2 bg-blue-500 rounded-full opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            </div>
                        </button>

                        <!-- Card Payment -->
                        <button onclick="processCardPayment()" class="w-full p-4 border-2 border-gray-200 hover:border-purple-500 bg-white rounded-xl text-left transition-all duration-200 hover:shadow-md group">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center group-hover:bg-purple-200 transition-colors">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-800">Card Payment</p>
                                    <p class="text-sm text-gray-600">Simulate card transaction</p>
                                </div>
                                <div class="w-2 h-2 bg-purple-500 rounded-full opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            </div>
                        </button>

                        <!-- Online Payment -->
                        <button onclick="processOnlinePayment()" class="w-full p-4 border-2 border-gray-200 hover:border-green-500 bg-white rounded-xl text-left transition-all duration-200 hover:shadow-md group">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center group-hover:bg-green-200 transition-colors">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-800">Online Payment</p>
                                    <p class="text-sm text-gray-600">Pay with card via Stripe</p>
                                </div>
                                <div class="w-2 h-2 bg-green-500 rounded-full opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Security Badge -->
                <div class="text-center p-4 bg-gray-50 rounded-lg border">
                    <div class="flex items-center justify-center space-x-2 text-sm text-gray-600">
                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <span>Secure SSL Encryption</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- QR Code Modal -->
        <div id="qr-code-section" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-60 backdrop-blur-sm">
            <div class="bg-white rounded-2xl p-8 max-w-md mx-4 transform transition-all duration-300 scale-95">
                <div class="text-center">
                    <h4 class="text-xl font-semibold text-gray-800 mb-2">Scan to Pay</h4>
                    <p class="text-gray-600 mb-6">Use your phone to scan the QR code</p>
                    
                    <div class="bg-white p-4 rounded-lg border-2 border-dashed border-gray-200 mb-6">
                        <img id="qr-code-image" src="" alt="Payment QR Code" class="w-64 h-64 mx-auto">
                    </div>
                    
                    <div class="flex space-x-3 justify-center">
                        <button onclick="copyPaymentLink()" class="px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            <span>Copy Link</span>
                        </button>
                        <a id="direct-payment-link" href="#" target="_blank" class="px-6 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors flex items-center space-x-2 no-underline">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            <span>Open Page</span>
                        </a>
                    </div>
                    
                    <button onclick="closeQRCode()" class="mt-4 text-gray-500 hover:text-gray-700 transition-colors text-sm">
                        Close
                    </button>
                </div>
            </div>
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
    const modal = document.getElementById('payment-modal');
    modal.querySelector('.transform').classList.remove('scale-100');
    modal.querySelector('.transform').classList.add('scale-95');
    
    setTimeout(() => {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        currentReservation = null;
    }, 300);
}

function closeQRCode() {
    const qrSection = document.getElementById('qr-code-section');
    qrSection.classList.add('hidden');
}

function processCashPayment() {
    if (!currentReservation) return;
    
    if (confirm('Mark this reservation as paid with cash and confirm it?')) {
        const formData = new FormData();
        formData.append('reservation_id', currentReservation.reservation_id);
        formData.append('amount_paid', currentReservation.total_amount);
        formData.append('_token', '{{ csrf_token() }}');

        fetch('/payments/process-cash', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccessMessage('Cash payment recorded! Reservation confirmed.');
            } else {
                alert('❌ ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('❌ Payment failed: ' + error.message);
        });
    }
}

function processCardPayment() {
    if (!currentReservation) return;
    
    if (confirm('Simulate card payment and confirm this reservation?')) {
        const formData = new FormData();
        formData.append('reservation_id', currentReservation.reservation_id);
        formData.append('_token', '{{ csrf_token() }}');

        fetch('/payments/process-card', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccessMessage('Card payment processed! Reservation confirmed.');
            } else {
                alert('❌ ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('❌ Payment failed: ' + error.message);
        });
    }
}

function processOnlinePayment() {
    if (!currentReservation) return;
    
    const formData = new FormData();
    formData.append('reservation_id', currentReservation.reservation_id);
    formData.append('_token', '{{ csrf_token() }}');

    fetch('/payments/process-online', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show QR code section
            const qrSection = document.getElementById('qr-code-section');
            qrSection.classList.remove('hidden');
            setTimeout(() => {
                qrSection.querySelector('.transform').classList.remove('scale-95');
                qrSection.querySelector('.transform').classList.add('scale-100');
            }, 10);
            
            if (data.qr_code_url) {
                document.getElementById('qr-code-image').src = data.qr_code_url;
            }
            document.getElementById('direct-payment-link').href = data.payment_url;
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ Failed to create payment session: ' + error.message);
    });
}

function copyPaymentLink() {
    const paymentLink = document.getElementById('direct-payment-link').href;
    navigator.clipboard.writeText(paymentLink).then(function() {
        alert('Payment link copied to clipboard!');
    }, function(err) {
        console.error('Could not copy text: ', err);
        alert('Failed to copy link');
    });
}

function showSuccessMessage(message) {
    alert('✅ ' + message);
    closePaymentModal();
    setTimeout(() => window.location.reload(), 1000);
}

// Close modal when clicking outside
document.getElementById('payment-modal').addEventListener('click', function(e) {
    if (e.target.id === 'payment-modal') {
        closePaymentModal();
    }
});

// Close QR code when clicking outside
document.getElementById('qr-code-section').addEventListener('click', function(e) {
    if (e.target.id === 'qr-code-section') {
        closeQRCode();
    }
});
</script>