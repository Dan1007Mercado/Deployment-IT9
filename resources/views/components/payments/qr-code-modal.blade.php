<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment QR Code</title>
    <!-- Use a more reliable QR code library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        #qrcode {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 256px;
        }
        #qrcode img {
            width: 256px;
            height: 256px;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full">
            <h2 class="text-2xl font-bold text-center mb-6">Scan to Pay</h2>
            
            <!-- QR Code Container -->
            <div id="qrcode" class="mb-6 border-2 border-dashed border-gray-300 rounded-lg p-4 bg-white">
                <!-- QR code will be generated here -->
            </div>
            
            <!-- Payment Info -->
            <div class="bg-gray-50 p-4 rounded-lg mb-6">
                <p class="font-semibold">Reservation #<span id="reservation-id"></span></p>
                <p>Amount: <span id="payment-amount"></span></p>
                <p>Status: <span id="payment-status" class="font-semibold text-yellow-600">Pending</span></p>
            </div>
            
            <!-- Direct Link -->
            <div class="text-center mb-6">
                <p class="text-sm text-gray-600 mb-2">Or click the link below:</p>
                <a id="payment-link" href="#" target="_blank" 
                   class="text-blue-600 hover:text-blue-800 underline break-all text-sm">
                    Pay Now
                </a>
            </div>
            
            <!-- Status Check -->
            <div class="text-center">
                <button onclick="checkPaymentStatus()" 
                        class="bg-green-500 text-white px-6 py-2 rounded-lg hover:bg-green-600">
                    Check Payment Status
                </button>
                <p id="status-message" class="mt-2 text-sm"></p>
            </div>
        </div>
    </div>

    <script>
        // Get data from URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        const paymentUrl = urlParams.get('payment_url');
        const sessionId = urlParams.get('session_id');
        const reservationId = urlParams.get('reservation_id');
        const amount = urlParams.get('amount');
        
        console.log('Payment URL:', paymentUrl);
        console.log('Session ID:', sessionId);
        
        // Display info
        document.getElementById('reservation-id').textContent = reservationId;
        document.getElementById('payment-amount').textContent = '₱' + parseFloat(amount).toFixed(2);
        document.getElementById('payment-link').href = paymentUrl;
        document.getElementById('payment-link').textContent = paymentUrl ? 'Payment Link' : 'No link available';
        
        // Generate QR Code
        function generateQRCode() {
            if (!paymentUrl) {
                console.error('No payment URL provided');
                document.getElementById('qrcode').innerHTML = 
                    '<p class="text-red-500 text-center">No payment URL available</p>';
                return;
            }
            
            try {
                // Clear previous QR code
                document.getElementById('qrcode').innerHTML = '';
                
                // Generate new QR code
                new QRCode(document.getElementById("qrcode"), {
                    text: paymentUrl,
                    width: 256,
                    height: 256,
                    colorDark: "#000000",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.H
                });
                
                console.log('QR code generated successfully');
            } catch (error) {
                console.error('QR code generation error:', error);
                document.getElementById('qrcode').innerHTML = 
                    '<p class="text-red-500 text-center">Failed to generate QR code</p>';
            }
        }
        
        // Generate QR code when page loads
        window.onload = generateQRCode;
        
        // Check payment status
        function checkPaymentStatus() {
            if (!sessionId || !reservationId) {
                document.getElementById('status-message').textContent = 'Missing session or reservation ID';
                document.getElementById('status-message').className = 'mt-2 text-sm text-red-600';
                return;
            }
            
            document.getElementById('status-message').textContent = 'Checking...';
            document.getElementById('status-message').className = 'mt-2 text-sm text-blue-600';
            
            fetch(`/payments/check-status?session_id=${sessionId}&reservation_id=${reservationId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Payment status response:', data);
                    if (data.success) {
                        if (data.payment_status === 'paid') {
                            document.getElementById('payment-status').textContent = 'Paid';
                            document.getElementById('payment-status').className = 'font-semibold text-green-600';
                            document.getElementById('status-message').textContent = 'Payment successful!';
                            document.getElementById('status-message').className = 'mt-2 text-sm text-green-600';
                            
                            // Redirect after 3 seconds
                            setTimeout(() => {
                                window.location.href = `/payments/stripe/success?session_id=${sessionId}`;
                            }, 3000);
                        } else {
                            document.getElementById('status-message').textContent = 
                                data.message || 'Payment still pending...';
                        }
                    } else {
                        document.getElementById('status-message').textContent = 
                            'Error: ' + (data.message || 'Unknown error');
                        document.getElementById('status-message').className = 'mt-2 text-sm text-red-600';
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    document.getElementById('status-message').textContent = 'Error checking status: ' + error.message;
                    document.getElementById('status-message').className = 'mt-2 text-sm text-red-600';
                });
        }
        
        // Auto-check every 10 seconds
        setInterval(checkPaymentStatus, 10000);
    </script>
</body>
</html>