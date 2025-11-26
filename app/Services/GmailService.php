<?php
// app/Services/GmailService.php

namespace App\Services;

use Google_Client;
use Google_Service_Gmail;
use Google_Service_Gmail_Message;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GmailService
{
    protected $client;
    protected $service;
    protected $isAuthenticated = false;

    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setApplicationName(config('app.name'));
        $this->client->setScopes(Google_Service_Gmail::GMAIL_SEND);
        $this->client->setAuthConfig(storage_path('app/credentials.json'));
        $this->client->setAccessType('offline');
        $this->client->setPrompt('select_account consent');
        $this->client->setRedirectUri(url('http://localhost:8000/oauth2callback'));

        try {
            // Load or get new token
            $this->setAccessToken();
            $this->isAuthenticated = true;
            $this->service = new Google_Service_Gmail($this->client);
        } catch (\Exception $e) {
            Log::warning('GmailService not authenticated: ' . $e->getMessage());
            $this->isAuthenticated = false;
        }
    }

    private function setAccessToken()
    {
        $tokenPath = storage_path('app/gmail-token.json');
        
        if (!file_exists($tokenPath)) {
            throw new \Exception('Gmail token not found. Please run php artisan gmail:setup');
        }

        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $this->client->setAccessToken($accessToken);

        // If token is expired, refresh it
        if ($this->client->isAccessTokenExpired()) {
            if ($this->client->getRefreshToken()) {
                $accessToken = $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
            } else {
                throw new \Exception('Refresh token is missing. Please re-authenticate.');
            }
            
            // Save the new token
            if (!file_exists(dirname($tokenPath))) {
                mkdir(dirname($tokenPath), 0700, true);
            }
            file_put_contents($tokenPath, json_encode($this->client->getAccessToken()));
        }
    }

    public function isAuthenticated()
    {
        return $this->isAuthenticated;
    }

    // ADD THIS METHOD - Payment Confirmation Email (FIXED NAME)
    public function sendPaymentConfirmedEmail($reservation, $guest, $payment)
    {
        if (!$this->isAuthenticated) {
            Log::warning('GmailService not authenticated - email not sent for payment confirmation');
            return false;
        }

        try {
            $subject = "Payment Confirmed - Booking Complete - AzureHotel";
            $message = $this->createPaymentConfirmedTemplate($reservation, $guest, $payment);
            return $this->sendEmail($guest->email, $subject, $message);
        } catch (\Exception $e) {
            Log::error('Failed to send payment confirmed email: ' . $e->getMessage());
            return false;
        }
    }

    public function sendReservationCreatedEmail($reservation, $guest)
    {
        if (!$this->isAuthenticated) {
            Log::warning('GmailService not authenticated - email not sent for reservation creation');
            return false;
        }

        try {
            $subject = "Reservation Created - Confirmation Required - AzureHotel";
            $message = $this->createReservationCreatedTemplate($reservation, $guest);
            return $this->sendEmail($guest->email, $subject, $message);
        } catch (\Exception $e) {
            Log::error('Failed to send reservation created email: ' . $e->getMessage());
            return false;
        }
    }

    public function sendReservationConfirmedEmail($reservation, $guest, $payment = null)
    {
        if (!$this->isAuthenticated) {
            Log::warning('GmailService not authenticated - email not sent for reservation confirmation');
            return false;
        }

        try {
            $subject = "Reservation Confirmed - Booking Complete - AzureHotel";
            $message = $this->createReservationConfirmedTemplate($reservation, $guest, $payment);
            return $this->sendEmail($guest->email, $subject, $message);
        } catch (\Exception $e) {
            Log::error('Failed to send reservation confirmed email: ' . $e->getMessage());
            return false;
        }
    }

    public function sendReservationCancellationWarning($reservation, $guest)
    {
        if (!$this->isAuthenticated) {
            Log::warning('GmailService not authenticated - email not sent for cancellation warning');
            return false;
        }

        try {
            $subject = "URGENT: Reservation Cancellation Warning - AzureHotel";
            $message = $this->createCancellationWarningTemplate($reservation, $guest);
            return $this->sendEmail($guest->email, $subject, $message);
        } catch (\Exception $e) {
            Log::error('Failed to send cancellation warning email: ' . $e->getMessage());
            return false;
        }
    }

    public function sendReservationCancelledEmail($reservation, $guest)
    {
        if (!$this->isAuthenticated) {
            Log::warning('GmailService not authenticated - email not sent for cancellation');
            return false;
        }

        try {
            $subject = "Reservation Cancelled - AzureHotel";
            $message = $this->createCancellationTemplate($reservation, $guest);
            return $this->sendEmail($guest->email, $subject, $message);
        } catch (\Exception $e) {
            Log::error('Failed to send cancellation email: ' . $e->getMessage());
            return false;
        }
    }

    private function sendEmail($to, $subject, $message)
    {
        try {
            // Prepare message
            $gmailMessage = new Google_Service_Gmail_Message();
            
            $rawMessage = "To: {$to}\r\n";
            $rawMessage .= "Subject: {$subject}\r\n";
            $rawMessage .= "MIME-Version: 1.0\r\n";
            $rawMessage .= "Content-Type: text/html; charset=utf-8\r\n";
            $rawMessage .= "\r\n" . $message;
            
            $encodedMessage = base64_encode($rawMessage);
            $encodedMessage = str_replace(['+', '/', '='], ['-', '_', ''], $encodedMessage);
            $gmailMessage->setRaw($encodedMessage);
            
            // Send message
            $this->service->users_messages->send('me', $gmailMessage);
            
            Log::info("Email sent successfully to: {$to} - Subject: {$subject}");
            return true;
            
        } catch (\Exception $e) {
            Log::error("Failed to send email to {$to}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get authorization URL for first-time setup
     */
    public function getAuthUrl()
    {
        return $this->client->createAuthUrl();
    }

    /**
     * Set authorization code for first-time setup
     */
    public function setAuthCode($code)
    {
        try {
            $accessToken = $this->client->fetchAccessTokenWithAuthCode($code);
            $this->client->setAccessToken($accessToken);

            // Check if there was an error
            if (array_key_exists('error', $accessToken)) {
                throw new \Exception($accessToken['error_description'] ?? 'Authentication failed');
            }

            // Save the token to file
            $tokenPath = storage_path('app/gmail-token.json');
            if (!file_exists(dirname($tokenPath))) {
                mkdir(dirname($tokenPath), 0700, true);
            }
            file_put_contents($tokenPath, json_encode($this->client->getAccessToken()));

            return $accessToken;
        } catch (\Exception $e) {
            Log::error('Gmail authentication failed: ' . $e->getMessage());
            throw $e;
        }
    }

    // Email Template Methods

    private function createReservationCreatedTemplate($reservation, $guest)
    {
        $checkIn = \Carbon\Carbon::parse($reservation->check_in_date);
        $checkOut = \Carbon\Carbon::parse($reservation->check_out_date);
        $nights = $checkIn->diffInDays($checkOut);
        
        // Get room numbers
        $roomNumbers = [];
        foreach($reservation->bookings as $booking) {
            foreach($booking->rooms as $bookingRoom) {
                $roomNumbers[] = $bookingRoom->room->room_number;
            }
        }
        $roomNumbers = implode(', ', array_unique($roomNumbers));

        return "
            <!DOCTYPE html>
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: #2c5aa0; color: white; padding: 20px; text-align: center; }
                    .content { background: #f9f9f9; padding: 20px; }
                    .footer { background: #eee; padding: 10px; text-align: center; font-size: 12px; }
                    .reservation-details { background: white; padding: 15px; border-radius: 5px; margin: 15px 0; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>AzureHotel</h1>
                        <h2>Reservation Created</h2>
                    </div>
                    
                    <div class='content'>
                        <p>Dear {$guest->first_name} {$guest->last_name},</p>
                        
                        <p>Your reservation has been successfully created. Please complete your payment within 24 hours to confirm your booking.</p>
                        
                        <div class='reservation-details'>
                            <h3>Reservation Details:</h3>
                            <p><strong>Reservation ID:</strong> {$reservation->reservation_id}</p>
                            <p><strong>Check-in:</strong> {$checkIn->format('F j, Y')}</p>
                            <p><strong>Check-out:</strong> {$checkOut->format('F j, Y')}</p>
                            <p><strong>Nights:</strong> {$nights}</p>
                            <p><strong>Rooms:</strong> {$roomNumbers}</p>
                            <p><strong>Total Amount:</strong> ₱" . number_format($reservation->total_amount, 2) . "</p>
                            <p><strong>Guests:</strong> {$reservation->num_guests}</p>
                        </div>
                        
                        <p><strong>Important:</strong> This reservation will expire on " . $reservation->expires_at->format('F j, Y g:i A') . "</p>
                        
                        <p>Thank you for choosing AzureHotel!</p>
                    </div>
                    
                    <div class='footer'>
                        <p>AzureHotel<br>
                        Contact: 09225548058 | Email: Inq_AzureHotel@gmail.com</p>
                    </div>
                </div>
            </body>
            </html>
        ";
    }

    private function createReservationConfirmedTemplate($reservation, $guest, $payment = null)
    {
        $checkIn = \Carbon\Carbon::parse($reservation->check_in_date);
        $checkOut = \Carbon\Carbon::parse($reservation->check_out_date);
        $nights = $checkIn->diffInDays($checkOut);
        
        // Get room numbers
        $roomNumbers = [];
        foreach($reservation->bookings as $booking) {
            foreach($booking->rooms as $bookingRoom) {
                $roomNumbers[] = $bookingRoom->room->room_number;
            }
        }
        $roomNumbers = implode(', ', array_unique($roomNumbers));

        $paymentSection = '';
        if ($payment) {
            $paymentSection = "
                <div class='payment-details'>
                    <h3>Payment Details:</h3>
                    <p><strong>Payment Method:</strong> " . ucfirst($payment->payment_method) . "</p>
                    <p><strong>Amount Paid:</strong> ₱" . number_format($payment->amount, 2) . "</p>
                    <p><strong>Payment Date:</strong> " . $payment->payment_date->format('F j, Y') . "</p>
                </div>
            ";
        }

        return "
            <!DOCTYPE html>
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: #27ae60; color: white; padding: 20px; text-align: center; }
                    .content { background: #f9f9f9; padding: 20px; }
                    .footer { background: #eee; padding: 10px; text-align: center; font-size: 12px; }
                    .reservation-details { background: white; padding: 15px; border-radius: 5px; margin: 15px 0; }
                    .payment-details { background: #e8f5e8; padding: 15px; border-radius: 5px; margin: 15px 0; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>AzureHotel</h1>
                        <h2>Reservation Confirmed! 🎉</h2>
                    </div>
                    
                    <div class='content'>
                        <p>Dear {$guest->first_name} {$guest->last_name},</p>
                        
                        <p>Your reservation has been confirmed! We look forward to welcoming you to AzureHotel.</p>
                        
                        <div class='reservation-details'>
                            <h3>Reservation Details:</h3>
                            <p><strong>Reservation ID:</strong> {$reservation->reservation_id}</p>
                            <p><strong>Check-in:</strong> {$checkIn->format('F j, Y')}</p>
                            <p><strong>Check-out:</strong> {$checkOut->format('F j, Y')}</p>
                            <p><strong>Nights:</strong> {$nights}</p>
                            <p><strong>Rooms:</strong> {$roomNumbers}</p>
                            <p><strong>Total Amount:</strong> ₱" . number_format($reservation->total_amount, 2) . "</p>
                            <p><strong>Guests:</strong> {$reservation->num_guests}</p>
                        </div>
                        
                        {$paymentSection}
                        
                        <p><strong>Check-in Instructions:</strong><br>
                        - Please bring a valid ID<br>
                        - Check-in time: 2:00 PM<br>
                        - Check-out time: 12:00 PM</p>
                        
                        <p>Thank you for choosing AzureHotel!</p>
                    </div>
                    
                    <div class='footer'>
                        <p>AzureHotel<br>
                        Contact: 09225548058 | Email: Inq_AzureHotel@gmail.com</p>
                    </div>
                </div>
            </body>
            </html>
        ";
    }

    private function createCancellationWarningTemplate($reservation, $guest)
    {
        $expiryTime = $reservation->expires_at->format('F j, Y g:i A');
        
        return "
            <!DOCTYPE html>
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: #e67e22; color: white; padding: 20px; text-align: center; }
                    .content { background: #f9f9f9; padding: 20px; }
                    .footer { background: #eee; padding: 10px; text-align: center; font-size: 12px; }
                    .warning { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 15px 0; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>AzureHotel</h1>
                        <h2>Reservation Expiring Soon ⚠️</h2>
                    </div>
                    
                    <div class='content'>
                        <p>Dear {$guest->first_name} {$guest->last_name},</p>
                        
                        <div class='warning'>
                            <h3>URGENT: Your reservation will expire in 1 hour!</h3>
                            <p>Your reservation #{$reservation->reservation_id} is about to expire at {$expiryTime}.</p>
                        </div>
                        
                        <p>To keep your reservation active, please complete your payment immediately.</p>
                        
                        <p><strong>Total Amount Due:</strong> ₱" . number_format($reservation->total_amount, 2) . "</p>
                        
                        <p>If we don't receive payment within the next hour, your reservation will be automatically cancelled.</p>
                        
                        <p>Thank you,<br>AzureHotel Team</p>
                    </div>
                    
                    <div class='footer'>
                        <p>AzureHotel<br>
                        Contact: 09225548058 | Email: Inq_AzureHotel@gmail.com</p>
                    </div>
                </div>
            </body>
            </html>
        ";
    }

    private function createCancellationTemplate($reservation, $guest)
    {
        return "
            <!DOCTYPE html>
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: #e74c3c; color: white; padding: 20px; text-align: center; }
                    .content { background: #f9f9f9; padding: 20px; }
                    .footer { background: #eee; padding: 10px; text-align: center; font-size: 12px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>Azure Hotel</h1>
                        <h2>Reservation Cancelled</h2>
                    </div>
                    
                    <div class='content'>
                        <p>Dear {$guest->first_name} {$guest->last_name},</p>
                        
                        <p>We're sorry to inform you that your reservation #{$reservation->reservation_id} has been cancelled.</p>
                        
                        <p><strong>Cancellation Reason:</strong> {$reservation->cancellation_reason}</p>
                        
                        <p>If this was a mistake or you have any questions, please contact us immediately.</p>
                        
                        <p>We hope to serve you in the future.</p>
                        
                        <p>Sincerely,<br>AzureHotel Team</p>
                    </div>

                    <div class='footer'>
                        <p>AzureHotel<br>
                        Contact: 09225548058 | Email: Inq_AzureHotel@gmail.com</p>
                    </div>
                </div>
            </body>
            </html>
        ";
    }

    /**
     * Create payment confirmation email template
     */
    private function createPaymentConfirmedTemplate($reservation, $guest, $payment)
    {
        $checkIn = \Carbon\Carbon::parse($reservation->check_in_date);
        $checkOut = \Carbon\Carbon::parse($reservation->check_out_date);
        $nights = $checkIn->diffInDays($checkOut);
        
        // Get room numbers
        $roomNumbers = [];
        foreach($reservation->bookings as $booking) {
            foreach($booking->rooms as $bookingRoom) {
                $roomNumbers[] = $bookingRoom->room->room_number;
            }
        }
        $roomNumbers = implode(', ', array_unique($roomNumbers));

        // Format payment method display
        $paymentMethod = ucfirst(str_replace('_', ' ', $payment->payment_method));
        $paymentDate = $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('F j, Y g:i A') : now()->format('F j, Y g:i A');
        
        // Format transaction ID based on payment method
        $transactionId = $payment->transaction_id;
        if ($payment->payment_method === 'cash') {
            $transactionId = "Cash Payment - " . ($payment->transaction_id ?? 'N/A');
        } elseif ($payment->payment_method === 'credit_card') {
            $transactionId = "Card Payment - " . ($payment->transaction_id ?? 'N/A');
        } elseif ($payment->payment_method === 'online') {
            $transactionId = "Online Payment - " . ($payment->transaction_id ?? 'N/A');
        }

        return "
            <!DOCTYPE html>
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: #27ae60; color: white; padding: 20px; text-align: center; }
                    .content { background: #f9f9f9; padding: 20px; }
                    .footer { background: #eee; padding: 10px; text-align: center; font-size: 12px; }
                    .reservation-details { background: white; padding: 15px; border-radius: 5px; margin: 15px 0; }
                    .payment-details { background: #e8f5e8; padding: 15px; border-radius: 5px; margin: 15px 0; border-left: 4px solid #27ae60; }
                    .success-badge { background: #27ae60; color: white; padding: 10px 15px; border-radius: 5px; display: inline-block; margin: 10px 0; }
                    .instructions { background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 15px 0; border-left: 4px solid #2196f3; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>AzureHotel</h1>
                        <h2>Payment Confirmed! 🎉</h2>
                    </div>
                    
                    <div class='content'>
                        <div class='success-badge'>
                            <strong>✅ Payment Successful! Your reservation is now confirmed.</strong>
                        </div>
                        
                        <p>Dear {$guest->first_name} {$guest->last_name},</p>
                        
                        <p>We're pleased to inform you that your payment has been processed successfully and your reservation is now confirmed.</p>
                        
                        <div class='payment-details'>
                            <h3>💰 Payment Details:</h3>
                            <p><strong>Payment Method:</strong> {$paymentMethod}</p>
                            <p><strong>Amount Paid:</strong> ₱" . number_format($payment->amount, 2) . "</p>
                            <p><strong>Transaction ID:</strong> {$transactionId}</p>
                            <p><strong>Payment Date:</strong> {$paymentDate}</p>
                            <p><strong>Payment Status:</strong> <span style='color: #27ae60; font-weight: bold;'>Completed</span></p>
                        </div>
                        
                        <div class='reservation-details'>
                            <h3>📅 Reservation Details:</h3>
                            <p><strong>Reservation ID:</strong> {$reservation->reservation_id}</p>
                            <p><strong>Check-in:</strong> {$checkIn->format('F j, Y')} (2:00 PM)</p>
                            <p><strong>Check-out:</strong> {$checkOut->format('F j, Y')} (12:00 PM)</p>
                            <p><strong>Duration:</strong> {$nights} night" . ($nights > 1 ? 's' : '') . "</p>
                            <p><strong>Room(s):</strong> {$roomNumbers}</p>
                            <p><strong>Total Amount:</strong> ₱" . number_format($reservation->total_amount, 2) . "</p>
                            <p><strong>Number of Guests:</strong> {$reservation->num_guests}</p>
                        </div>
                        
                        <div class='instructions'>
                            <h3>📋 Check-in Instructions:</h3>
                            <p>• Please bring a valid government-issued ID</p>
                            <p>• Check-in time: 2:00 PM</p>
                            <p>• Check-out time: 12:00 PM</p>
                            <p>• Early check-in/late check-out subject to availability</p>
                            <p>• Contact us for any special requests or assistance</p>
                        </div>
                        
                        <p>We look forward to welcoming you to AzureHotel and ensuring you have a comfortable and memorable stay!</p>
                        
                        <p>Warm regards,<br>
                        <strong>The AzureHotel Team</strong></p>
                    </div>
                    
                    <div class='footer'>
                        <p><strong>AzureHotel</strong><br>
                        📞 Contact: 09225548058 | 📧 Email: Inq_AzureHotel@gmail.com<br>
                        ⏰ Front Desk Hours: 24/7</p>
                        <p><em>Thank you for choosing AzureHotel for your accommodation needs!</em></p>
                    </div>
                </div>
            </body>
            </html>
        ";
    }

    // REMOVE OR COMMENT OUT THIS DUPLICATE METHOD - it's causing confusion
    /*
    private function sendPaymentConfirmationEmail($reservation, $payment)
    {
        try {
            \Log::info("=== PAYMENT EMAIL DEBUG START ===");
            \Log::info("Reservation ID: " . $reservation->reservation_id);
            \Log::info("Payment ID: " . $payment->payment_id);
            \Log::info("Guest Email: " . $reservation->guest->email);

            // Reload relationships to ensure we have fresh data
            $reservation->load(['guest', 'roomType', 'bookings.rooms.room']);
            \Log::info("Relationships loaded successfully");

            // Use the GmailService
            $gmailService = new \App\Services\GmailService();
            \Log::info("GmailService instantiated");

            $isAuthenticated = $gmailService->isAuthenticated();
            \Log::info("GmailService authenticated: " . ($isAuthenticated ? 'YES' : 'NO'));

            if (!$isAuthenticated) {
                \Log::error('GmailService not authenticated - cannot send email');
                \Log::info("=== PAYMENT EMAIL DEBUG END (FAILED AUTH) ===");
                return false;
            }

            \Log::info("Attempting to send payment confirmation email...");
            $emailSent = $gmailService->sendPaymentConfirmedEmail($reservation, $reservation->guest, $payment);
            
            \Log::info("Email send result: " . ($emailSent ? 'SUCCESS' : 'FAILED'));
            \Log::info("=== PAYMENT EMAIL DEBUG END ===");
            
            return $emailSent;
            
        } catch (\Exception $e) {
            \Log::error('PAYMENT EMAIL EXCEPTION: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            \Log::info("=== PAYMENT EMAIL DEBUG END (EXCEPTION) ===");
            return false;
        }
    }
    */
}