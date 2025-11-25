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

    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setApplicationName(config('app.name'));
        $this->client->setScopes(Google_Service_Gmail::GMAIL_SEND);
        $this->client->setAuthConfig(storage_path('app/credentials.json'));
        $this->client->setAccessType('offline');
        $this->client->setPrompt('select_account consent');

        // Load or get new token
        $this->setAccessToken();

        $this->service = new Google_Service_Gmail($this->client);
    }

    private function setAccessToken()
    {
        $tokenPath = storage_path('app/gmail-token.json');
        
        if (file_exists($tokenPath)) {
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            $this->client->setAccessToken($accessToken);
        }

        // If token is expired, refresh it
        if ($this->client->isAccessTokenExpired()) {
            if ($this->client->getRefreshToken()) {
                $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
            } else {
                // You might want to handle this case differently
                throw new \Exception('Refresh token is missing. Please re-authenticate.');
            }
            
            // Save the new token
            if (!file_exists(dirname($tokenPath))) {
                mkdir(dirname($tokenPath), 0700, true);
            }
            file_put_contents($tokenPath, json_encode($this->client->getAccessToken()));
        }
    }

    public function sendReservationCreatedEmail($reservation, $guest)
    {
        $subject = "Reservation Created - Confirmation Required - Grand Paradise Hotel";
        
        $message = $this->createReservationCreatedTemplate($reservation, $guest);
        
        return $this->sendEmail($guest->email, $subject, $message);
    }

    public function sendReservationConfirmedEmail($reservation, $guest, $payment = null)
    {
        $subject = "Reservation Confirmed - Booking Complete - Grand Paradise Hotel";
        
        $message = $this->createReservationConfirmedTemplate($reservation, $guest, $payment);
        
        return $this->sendEmail($guest->email, $subject, $message);
    }

    public function sendReservationCancellationWarning($reservation, $guest)
    {
        $subject = "URGENT: Reservation Cancellation Warning - Grand Paradise Hotel";
        
        $message = $this->createCancellationWarningTemplate($reservation, $guest);
        
        return $this->sendEmail($guest->email, $subject, $message);
    }

    public function sendReservationCancelledEmail($reservation, $guest)
    {
        $subject = "Reservation Cancelled - Grand Paradise Hotel";
        
        $message = $this->createCancellationTemplate($reservation, $guest);
        
        return $this->sendEmail($guest->email, $subject, $message);
    }

    private function createReservationCreatedTemplate($reservation, $guest)
    {
        $roomNumbers = [];
        if ($reservation->bookings) {
            foreach($reservation->bookings as $booking) {
                if ($booking->rooms) {
                    foreach($booking->rooms as $bookingRoom) {
                        if ($bookingRoom->room) {
                            $roomNumbers[] = $bookingRoom->room->room_number;
                        }
                    }
                }
            }
        }
        $roomNumbers = array_unique($roomNumbers);
        $roomNumbersDisplay = count($roomNumbers) > 0 ? implode(', ', $roomNumbers) : 'To be assigned';

        $checkIn = \Carbon\Carbon::parse($reservation->check_in_date);
        $checkOut = \Carbon\Carbon::parse($reservation->check_out_date);
        $nights = $checkIn->diffInDays($checkOut);

        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: 'Arial', sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
                .container { max-width: 600px; margin: 0 auto; background: #ffffff; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px 20px; text-align: center; }
                .content { padding: 30px; background: #f9f9f9; }
                .footer { background: #2c3e50; color: white; padding: 20px; text-align: center; font-size: 12px; }
                .warning { background: #fff3cd; border: 1px solid #ffeaa7; padding: 20px; margin: 20px 0; border-radius: 5px; }
                .info-box { background: white; border: 1px solid #e1e8ed; padding: 20px; margin: 15px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
                .button { display: inline-block; padding: 12px 30px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; margin: 10px 0; }
                .highlight { color: #e74c3c; font-weight: bold; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🏨 Grand Paradise Hotel</h1>
                    <h2>Reservation Created Successfully</h2>
                </div>
                
                <div class='content'>
                    <p>Dear <strong>{$guest->first_name} {$guest->last_name}</strong>,</p>
                    
                    <p>Thank you for choosing Grand Paradise Hotel! Your reservation has been successfully created and is currently <span class='highlight'>pending confirmation</span>.</p>
                    
                    <div class='info-box'>
                        <h3 style='color: #2c5aa0; margin-top: 0;'>📋 Reservation Summary</h3>
                        <p><strong>Reservation ID:</strong> #{$reservation->reservation_id}</p>
                        <p><strong>Check-in Date:</strong> {$checkIn->format('F d, Y')}</p>
                        <p><strong>Check-out Date:</strong> {$checkOut->format('F d, Y')}</p>
                        <p><strong>Duration:</strong> {$nights} night(s)</p>
                        <p><strong>Number of Guests:</strong> {$reservation->num_guests}</p>
                    </div>
                    
                    <div class='info-box'>
                        <h3 style='color: #2c5aa0; margin-top: 0;'>🛏️ Room Information</h3>
                        <p><strong>Room Type:</strong> {$reservation->roomType->type_name}</p>
                        <p><strong>Room Number(s):</strong> {$roomNumbersDisplay}</p>
                        <p><strong>Total Amount:</strong> <span style='font-size: 1.2em; color: #27ae60;'>₱" . number_format($reservation->total_amount, 2) . "</span></p>
                    </div>
                    
                    <div class='warning'>
                        <h4 style='color: #e74c3c; margin-top: 0;'>⚠️ Important: Payment Required</h4>
                        <p>Your reservation will be <span class='highlight'>automatically cancelled</span> if payment is not completed within <strong>24 hours</strong>.</p>
                        <p>To secure your booking, please complete the payment as soon as possible.</p>
                    </div>
                    
                    <div style='text-align: center; margin: 25px 0;'>
                        <p>Need assistance with your payment?</p>
                        <p>Contact our reservation team at <strong>+63 2 8122 4567</strong> or reply to this email.</p>
                    </div>
                    
                    <p>We look forward to welcoming you to Grand Paradise Hotel!</p>
                    
                    <p>Warm regards,<br>
                    <strong>The Grand Paradise Hotel Team</strong></p>
                </div>
                
                <div class='footer'>
                    <p>Grand Paradise Hotel • 123 Paradise Street, Makati City, Metro Manila, Philippines</p>
                    <p>📞 +63 2 8122 4567 • ✉️ info@grandparadise.com • 🌐 www.grandparadise.com</p>
                    <p style='margin-top: 15px; font-size: 11px; color: #bdc3c7;'>
                        This is an automated message. Please do not reply to this email.
                    </p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    private function createReservationConfirmedTemplate($reservation, $guest, $payment = null)
    {
        $roomNumbers = [];
        if ($reservation->bookings) {
            foreach($reservation->bookings as $booking) {
                if ($booking->rooms) {
                    foreach($booking->rooms as $bookingRoom) {
                        if ($bookingRoom->room) {
                            $roomNumbers[] = $bookingRoom->room->room_number;
                        }
                    }
                }
            }
        }
        $roomNumbers = array_unique($roomNumbers);
        $roomNumbersDisplay = count($roomNumbers) > 0 ? implode(', ', $roomNumbers) : 'To be assigned';

        $checkIn = \Carbon\Carbon::parse($reservation->check_in_date);
        $checkOut = \Carbon\Carbon::parse($reservation->check_out_date);
        $nights = $checkIn->diffInDays($checkOut);

        $paymentInfo = "";
        if ($payment) {
            $paymentDate = \Carbon\Carbon::parse($payment->payment_date);
            $paymentInfo = "
            <div class='info-box'>
                <h3 style='color: #27ae60; margin-top: 0;'>💳 Payment Details</h3>
                <p><strong>Payment Method:</strong> " . ucfirst($payment->payment_method) . "</p>
                <p><strong>Amount Paid:</strong> <span style='color: #27ae60; font-weight: bold;'>₱" . number_format($payment->amount, 2) . "</span></p>
                <p><strong>Payment Date:</strong> {$paymentDate->format('F d, Y g:i A')}</p>
                <p><strong>Transaction ID:</strong> {$payment->transaction_id}</p>
                <p><strong>Payment Status:</strong> <span style='color: #27ae60;'>" . ucfirst($payment->payment_status) . "</span></p>
            </div>
            ";
        }

        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: 'Arial', sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
                .container { max-width: 600px; margin: 0 auto; background: #ffffff; }
                .header { background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%); color: white; padding: 30px 20px; text-align: center; }
                .content { padding: 30px; background: #f9f9f9; }
                .footer { background: #2c3e50; color: white; padding: 20px; text-align: center; font-size: 12px; }
                .success { background: #d4edda; border: 1px solid #c3e6cb; padding: 25px; margin: 20px 0; border-radius: 8px; text-align: center; }
                .info-box { background: white; border: 1px solid #e1e8ed; padding: 20px; margin: 15px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
                .instructions { background: #e8f4fd; border: 1px solid #b6e0fe; padding: 20px; margin: 20px 0; border-radius: 8px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🏨 Grand Paradise Hotel</h1>
                    <h2>Reservation Confirmed! 🎉</h2>
                </div>
                
                <div class='content'>
                    <div class='success'>
                        <h3 style='margin: 0; color: #155724;'>✅ Your Booking is Confirmed!</h3>
                        <p style='margin: 10px 0 0 0; font-size: 1.1em;'>We're excited to welcome you to Grand Paradise Hotel!</p>
                    </div>
                    
                    <p>Dear <strong>{$guest->first_name} {$guest->last_name}</strong>,</p>
                    
                    <p>Your reservation has been successfully confirmed and your payment has been processed. Your booking is now secured!</p>
                    
                    <div class='info-box'>
                        <h3 style='color: #2c5aa0; margin-top: 0;'>📋 Booking Confirmation</h3>
                        <p><strong>Reservation ID:</strong> <span style='background: #f8f9fa; padding: 2px 8px; border-radius: 4px;'>#{$reservation->reservation_id}</span></p>
                        <p><strong>Check-in:</strong> {$checkIn->format('l, F d, Y')} (2:00 PM)</p>
                        <p><strong>Check-out:</strong> {$checkOut->format('l, F d, Y')} (12:00 PM)</p>
                        <p><strong>Duration:</strong> {$nights} night(s)</p>
                        <p><strong>Guests:</strong> {$reservation->num_guests} person(s)</p>
                    </div>
                    
                    <div class='info-box'>
                        <h3 style='color: #2c5aa0; margin-top: 0;'>🛏️ Accommodation Details</h3>
                        <p><strong>Room Type:</strong> {$reservation->roomType->type_name}</p>
                        <p><strong>Room Number(s):</strong> <strong>{$roomNumbersDisplay}</strong></p>
                        <p><strong>Total Amount:</strong> <span style='font-size: 1.2em; color: #27ae60; font-weight: bold;'>₱" . number_format($reservation->total_amount, 2) . "</span></p>
                    </div>
                    
                    {$paymentInfo}
                    
                    <div class='instructions'>
                        <h3 style='color: #2c5aa0; margin-top: 0;'>📝 Check-in Information</h3>
                        <p>• <strong>Check-in Time:</strong> 2:00 PM onwards</p>
                        <p>• <strong>Check-out Time:</strong> 12:00 PM</p>
                        <p>• <strong>Required at Check-in:</strong> Valid government-issued ID</p>
                        <p>• <strong>Early Check-in/Late Check-out:</strong> Subject to availability and additional charges</p>
                        <p>• <strong>Parking:</strong> Complimentary parking available</p>
                    </div>
                    
                    <div style='background: #fff3e0; border: 1px solid #ffb74d; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                        <p style='margin: 0; color: #e65100;'><strong>💡 Tip:</strong> Present this confirmation email at the front desk for faster check-in.</p>
                    </div>
                    
                    <p>Should you have any questions or require special arrangements, please don't hesitate to contact us.</p>
                    
                    <p>We can't wait to make your stay memorable!</p>
                    
                    <p>Warm regards,<br>
                    <strong>The Grand Paradise Hotel Team</strong></p>
                </div>
                
                <div class='footer'>
                    <p>Grand Paradise Hotel • 123 Paradise Street, Makati City, Metro Manila, Philippines</p>
                    <p>📞 +63 2 8122 4567 • ✉️ info@grandparadise.com • 🌐 www.grandparadise.com</p>
                    <p style='margin-top: 15px; font-size: 11px; color: #bdc3c7;'>
                        This is an automated message. Please do not reply to this email.
                    </p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    private function createCancellationWarningTemplate($reservation, $guest)
    {
        $checkIn = \Carbon\Carbon::parse($reservation->check_in_date);
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: 'Arial', sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
                .container { max-width: 600px; margin: 0 auto; background: #ffffff; }
                .header { background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%); color: white; padding: 30px 20px; text-align: center; }
                .content { padding: 30px; background: #f9f9f9; }
                .footer { background: #2c3e50; color: white; padding: 20px; text-align: center; font-size: 12px; }
                .urgent { background: #f8d7da; border: 1px solid #f5c6cb; padding: 25px; margin: 20px 0; border-radius: 8px; }
                .info-box { background: white; border: 1px solid #e1e8ed; padding: 20px; margin: 15px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
                .button { display: inline-block; padding: 12px 30px; background: #e74c3c; color: white; text-decoration: none; border-radius: 5px; margin: 10px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🏨 Grand Paradise Hotel</h1>
                    <h2>Reservation Cancellation Warning</h2>
                </div>
                
                <div class='content'>
                    <div class='urgent'>
                        <h3 style='margin: 0; color: #721c24;'>🚨 URGENT: Payment Required to Secure Your Booking</h3>
                        <p style='margin: 10px 0 0 0; font-size: 1.1em;'>Your reservation will be cancelled in less than 1 hour if payment is not completed.</p>
                    </div>
                    
                    <p>Dear <strong>{$guest->first_name} {$guest->last_name}</strong>,</p>
                    
                    <p>We noticed that your reservation <strong>#{$reservation->reservation_id}</strong> is still pending payment. This is a final reminder to complete your payment to avoid automatic cancellation.</p>
                    
                    <div class='info-box'>
                        <h3 style='color: #e74c3c; margin-top: 0;'>📋 Reservation Details</h3>
                        <p><strong>Reservation ID:</strong> #{$reservation->reservation_id}</p>
                        <p><strong>Check-in Date:</strong> {$checkIn->format('F d, Y')}</p>
                        <p><strong>Room Type:</strong> {$reservation->roomType->type_name}</p>
                        <p><strong>Total Amount:</strong> ₱" . number_format($reservation->total_amount, 2) . "</p>
                    </div>
                    
                    <div style='background: #fff3cd; border: 1px solid #ffeaa7; padding: 20px; border-radius: 5px; margin: 20px 0;'>
                        <h4 style='margin: 0 0 10px 0; color: #856404;'>⏰ Immediate Action Required</h4>
                        <p style='margin: 0;'>Your reservation will be <strong>automatically cancelled in 1 hour</strong> if payment is not completed.</p>
                    </div>
                    
                    <p>To secure your booking, please complete your payment immediately through our payment portal or contact our reservation team for assistance.</p>
                    
                    <div style='text-align: center; margin: 25px 0;'>
                        <p><strong>Need help with payment?</strong></p>
                        <p>Call us now: <strong style='color: #e74c3c;'>+63 2 8122 4567</strong></p>
                        <p>Email: <strong>reservations@grandparadise.com</strong></p>
                    </div>
                    
                    <p>We'd hate to see your reservation cancelled. Act now to secure your stay!</p>
                    
                    <p>Sincerely,<br>
                    <strong>The Grand Paradise Hotel Team</strong></p>
                </div>
                
                <div class='footer'>
                    <p>Grand Paradise Hotel • 123 Paradise Street, Makati City, Metro Manila, Philippines</p>
                    <p>📞 +63 2 8122 4567 • ✉️ info@grandparadise.com</p>
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
            <meta charset='UTF-8'>
            <style>
                body { font-family: 'Arial', sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
                .container { max-width: 600px; margin: 0 auto; background: #ffffff; }
                .header { background: #95a5a6; color: white; padding: 30px 20px; text-align: center; }
                .content { padding: 30px; background: #f9f9f9; }
                .footer { background: #2c3e50; color: white; padding: 20px; text-align: center; font-size: 12px; }
                .cancelled { background: #e9ecef; border: 1px solid #dee2e6; padding: 25px; margin: 20px 0; border-radius: 8px; text-align: center; }
                .info-box { background: white; border: 1px solid #e1e8ed; padding: 20px; margin: 15px 0; border-radius: 8px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🏨 Grand Paradise Hotel</h1>
                    <h2>Reservation Cancelled</h2>
                </div>
                
                <div class='content'>
                    <div class='cancelled'>
                        <h3 style='margin: 0; color: #6c757d;'>❌ Reservation Cancelled</h3>
                        <p style='margin: 10px 0 0 0;'>We're sorry to see you go.</p>
                    </div>
                    
                    <p>Dear <strong>{$guest->first_name} {$guest->last_name}</strong>,</p>
                    
                    <p>Your reservation <strong>#{$reservation->reservation_id}</strong> has been cancelled as we did not receive payment within the required timeframe.</p>
                    
                    <div class='info-box'>
                        <h3 style='color: #6c757d; margin-top: 0;'>Cancelled Reservation</h3>
                        <p><strong>Reservation ID:</strong> #{$reservation->reservation_id}</p>
                        <p><strong>Room Type:</strong> {$reservation->roomType->type_name}</p>
                        <p><strong>Cancellation Reason:</strong> No payment received within 24 hours</p>
                        <p><strong>Cancellation Date:</strong> " . now()->format('F d, Y g:i A') . "</p>
                    </div>
                    
                    <p>If you believe this cancellation was made in error, or if you'd like to make a new reservation, please contact our reservation team.</p>
                    
                    <p>We hope to welcome you to Grand Paradise Hotel in the future!</p>
                    
                    <p>Sincerely,<br>
                    <strong>The Grand Paradise Hotel Team</strong></p>
                </div>
                
                <div class='footer'>
                    <p>Grand Paradise Hotel • 123 Paradise Street, Makati City, Metro Manila, Philippines</p>
                    <p>📞 +63 2 8122 4567 • ✉️ info@grandparadise.com</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    private function sendEmail($to, $subject, $message)
    {
        try {
            // Prepare message
            $message = new Google_Service_Gmail_Message();
            
            $rawMessage = "To: {$to}\r\n";
            $rawMessage .= "Subject: {$subject}\r\n";
            $rawMessage .= "MIME-Version: 1.0\r\n";
            $rawMessage .= "Content-Type: text/html; charset=utf-8\r\n";
            $rawMessage .= "\r\n" . $message;
            
            $encodedMessage = base64_encode($rawMessage);
            $encodedMessage = str_replace(['+', '/', '='], ['-', '_', ''], $encodedMessage);
            $message->setRaw($encodedMessage);
            
            // Send message
            $this->service->users_messages->send('me', $message);
            
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
        $accessToken = $this->client->fetchAccessTokenWithAuthCode($code);
        $this->client->setAccessToken($accessToken);

        // Save the token to file
        $tokenPath = storage_path('app/gmail-token.json');
        if (!file_exists(dirname($tokenPath))) {
            mkdir(dirname($tokenPath), 0700, true);
        }
        file_put_contents($tokenPath, json_encode($this->client->getAccessToken()));

        return $accessToken;
    }
}