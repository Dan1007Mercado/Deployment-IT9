<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .success-icon {
            animation: checkmark 0.8s ease-in-out;
        }
        @keyframes checkmark {
            0% { transform: scale(0); opacity: 0; }
            50% { transform: scale(1.2); opacity: 1; }
            100% { transform: scale(1); opacity: 1; }
        }
        .fade-in {
            animation: fadeIn 1s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl p-6 sm:p-8 md:p-10 max-w-sm sm:max-w-md md:max-w-lg lg:max-w-xl w-full mx-4 fade-in border border-gray-200">
        <div class="text-center">
            <!-- Success Icon with Animation -->
            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-4 sm:mb-6 success-icon">
                <svg class="w-8 h-8 sm:w-10 sm:h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            
            <!-- Heading -->
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2 sm:mb-3">Payment Successful!</h1>
            
            <!-- Subtext -->
            <p class="text-gray-600 text-base sm:text-lg mb-6 sm:mb-8 leading-relaxed px-2 sm:px-0">Thank you for your payment. Your reservation is now confirmed and ready. We've sent a confirmation email to your inbox.</p>
            
            <!-- Reservation Details (Conditional) -->
            @if(isset($reservation))
            <div class="bg-gray-50 rounded-xl p-4 sm:p-6 mb-6 sm:mb-8 text-left border border-gray-200">
                <h3 class="font-semibold text-gray-900 text-base sm:text-lg mb-3 sm:mb-4">Your Reservation Details</h3>
                <div class="space-y-2">
                    <p class="text-sm sm:text-base text-gray-700"><span class="font-medium">Reservation ID:</span> {{ $reservation->reservation_id }}</p>
                    <p class="text-sm sm:text-base text-gray-700"><span class="font-medium">Guest Name:</span> {{ $reservation->guest->first_name }} {{ $reservation->guest->last_name }}</p>
                    <p class="text-sm sm:text-base text-gray-700"><span class="font-medium">Total Amount:</span> ₱{{ number_format($reservation->total_amount, 2) }}</p>
                </div>
            </div>
            @endif
            
            <!-- Next Steps Section (Consumer-Focused) -->
            <div class="bg-blue-50 rounded-xl p-4 sm:p-6 mb-6 sm:mb-8 border border-blue-200">
                <h3 class="font-semibold text-blue-900 text-base sm:text-lg mb-2">What's Next?</h3>
                <ul class="text-sm sm:text-base text-blue-800 space-y-1">
                    <li>• Check your email for reservation details and check-in instructions.</li>
                    <li>• Save this page or note your Reservation ID for reference.</li>
                    <li>• Contact us if you need to make changes or have questions.</li>
                </ul>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center">
                <a href="{{ url('/hotel-website') }}" class="inline-flex items-center justify-center bg-blue-600 text-white px-6 sm:px-8 py-3 sm:py-4 rounded-lg hover:bg-blue-700 transition-all duration-200 font-semibold text-base sm:text-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Hotel
                </a>
                
            </div>
            
            <!-- Footer Note -->
            <p class="text-gray-500 text-xs sm:text-sm mt-4 sm:mt-6">Need help? <a href="Azure@hotel.com" class="text-blue-600 hover:underline">Contact Support</a></p>
        </div>
    </div>
</body>
</html>
