<?php
namespace App\Services;

use Google_Client;
use Google_Service_Gmail;
use Google_Service_Gmail_Message;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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

        if ($this->client->isAccessTokenExpired()) {
            if ($this->client->getRefreshToken()) {
                $accessToken = $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
            } else {
                throw new \Exception('Refresh token is missing. Please re-authenticate.');
            }
            
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

    public function sendOnlineBookingConfirmation($reservation, $guest, $payment)
    {
        if (!$this->isAuthenticated) {
            Log::warning('GmailService not authenticated - email not sent for online booking confirmation');
            return false;
        }

        try {
            $subject = "Booking Confirmed! Payment Received - AzureHotel";
            $message = $this->createOnlineBookingConfirmationTemplate($reservation, $guest, $payment);
            return $this->sendEmail($guest->email, $subject, $message);
        } catch (\Exception $e) {
            Log::error('Failed to send online booking confirmation email: ' . $e->getMessage());
            return false;
        }
    }

    public function sendCreditCardBookingConfirmation($reservation, $guest, $payment)
    {
        if (!$this->isAuthenticated) {
            Log::warning('GmailService not authenticated - email not sent for credit card booking confirmation');
            return false;
        }

        try {
            $subject = "Booking Confirmed! Payment Received - AzureHotel";
            $message = $this->createCreditCardBookingConfirmationTemplate($reservation, $guest, $payment);
            return $this->sendEmail($guest->email, $subject, $message);
        } catch (\Exception $e) {
            Log::error('Failed to send credit card booking confirmation email: ' . $e->getMessage());
            return false;
        }
    }

    private function sendEmail($to, $subject, $message)
    {
        try {
            $gmailMessage = new Google_Service_Gmail_Message();
            
            $rawMessage = "To: {$to}\r\n";
            $rawMessage .= "Subject: {$subject}\r\n";
            $rawMessage .= "MIME-Version: 1.0\r\n";
            $rawMessage .= "Content-Type: text/html; charset=utf-8\r\n";
            $rawMessage .= "\r\n" . $message;
            
            $encodedMessage = base64_encode($rawMessage);
            $encodedMessage = str_replace(['+', '/', '='], ['-', '_', ''], $encodedMessage);
            $gmailMessage->setRaw($encodedMessage);
            
            $this->service->users_messages->send('me', $gmailMessage);
            
            Log::info("Email sent successfully to: {$to} - Subject: {$subject}");
            return true;
            
        } catch (\Exception $e) {
            Log::error("Failed to send email to {$to}: " . $e->getMessage());
            return false;
        }
    }

    public function getAuthUrl()
    {
        return $this->client->createAuthUrl();
    }

    public function setAuthCode($code)
    {
        try {
            $accessToken = $this->client->fetchAccessTokenWithAuthCode($code);
            $this->client->setAccessToken($accessToken);

            if (array_key_exists('error', $accessToken)) {
                throw new \Exception($accessToken['error_description'] ?? 'Authentication failed');
            }

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

    private function createOnlineBookingConfirmationTemplate($reservation, $guest, $payment)
    {
        $checkIn = Carbon::parse($reservation->check_in_date);
        $checkOut = Carbon::parse($reservation->check_out_date);
        $nights = $checkIn->diffInDays($checkOut);
        
        $roomNumbers = [];
        foreach($reservation->bookings as $booking) {
            foreach($booking->rooms as $bookingRoom) {
                $roomNumbers[] = $bookingRoom->room->room_number;
            }
        }
        $roomNumbers = array_unique($roomNumbers);
        $roomNumbersStr = implode(', ', $roomNumbers);
        $numRooms = count($roomNumbers);

        $roomPrice = $reservation->roomType->base_price;
        $subtotal = $roomPrice * $nights * $numRooms;
        
        $paymentMethod = 'Online Payment (Credit/Debit Card)';
        $paymentDate = $payment->payment_date 
            ? Carbon::parse($payment->payment_date)->format('F j, Y h:i A') 
            : now()->format('F j, Y h:i A');
        
        $isStripePayment = $payment->payment_method === 'online' && $payment->stripe_session_id;
        $transactionDisplay = $isStripePayment 
            ? "Stripe Transaction: " . ($payment->stripe_payment_intent_id ?? $payment->transaction_id)
            : "Transaction ID: " . $payment->transaction_id;

        return "
            <!DOCTYPE html>
            <html>
            <head>
                <style>
                    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
                    .container { max-width: 600px; margin: 0 auto; background: #ffffff; }
                    .header { background: linear-gradient(135deg, #2c5aa0 0%, #1e3a8a 100%); color: white; padding: 30px 20px; text-align: center; }
                    .content { padding: 30px; }
                    .footer { background: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #666; border-top: 1px solid #e9ecef; }
                    .badge { background: #27ae60; color: white; padding: 10px 20px; border-radius: 30px; display: inline-block; font-weight: bold; margin-bottom: 20px; }
                    .section { background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #2c5aa0; }
                    .payment-section { background: #e8f5e8; border-left: 4px solid #27ae60; }
                    .info-section { background: #e3f2fd; border-left: 4px solid #2196f3; }
                    .summary { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 15px; }
                    .summary-item { background: white; padding: 15px; border-radius: 6px; border: 1px solid #e9ecef; }
                    h3 { color: #2c5aa0; margin-top: 0; }
                    .highlight { color: #27ae60; font-weight: bold; }
                    @media (max-width: 480px) {
                        .summary { grid-template-columns: 1fr; }
                        .header { padding: 20px 15px; }
                        .content { padding: 20px; }
                    }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1 style='margin: 0; font-size: 28px;'>AzureHotel</h1>
                        <p style='margin: 10px 0 0; opacity: 0.9;'>Booking Confirmation</p>
                    </div>
                    
                    <div class='content'>
                        <div style='text-align: center;'>
                            <div class='badge'>✅ BOOKING CONFIRMED & PAID</div>
                        </div>
                        
                        <p>Dear <strong>{$guest->first_name} {$guest->last_name}</strong>,</p>
                        
                        <p>Thank you for choosing AzureHotel! Your online booking has been successfully processed and confirmed. 
                           Your payment has been received and your reservation is now guaranteed.</p>
                        
                        <div class='section payment-section'>
                            <h3>💰 Payment Information</h3>
                            <div class='summary'>
                                <div class='summary-item'>
                                    <strong>Payment Method:</strong><br>
                                    <span class='highlight'>{$paymentMethod}</span>
                                </div>
                                <div class='summary-item'>
                                    <strong>Amount Paid:</strong><br>
                                    <span class='highlight'>₱" . number_format($payment->amount, 2) . "</span>
                                </div>
                                <div class='summary-item'>
                                    <strong>Transaction:</strong><br>
                                    {$transactionDisplay}
                                </div>
                                <div class='summary-item'>
                                    <strong>Payment Date:</strong><br>
                                    {$paymentDate}
                                </div>
                            </div>
                            <p style='margin-top: 15px; padding: 10px; background: white; border-radius: 6px;'>
                                <strong>Payment Status:</strong> 
                                <span style='color: #27ae60; font-weight: bold;'>✅ Completed</span>
                            </p>
                        </div>
                        
                        <div class='section'>
                            <h3>📅 Booking Summary</h3>
                            <div class='summary'>
                                <div class='summary-item'>
                                    <strong>Booking Reference:</strong><br>
                                    {$payment->transaction_id}
                                </div>
                                <div class='summary-item'>
                                    <strong>Reservation ID:</strong><br>
                                    {$reservation->reservation_id}
                                </div>
                                <div class='summary-item'>
                                    <strong>Check-in Date:</strong><br>
                                    <span class='highlight'>{$checkIn->format('F j, Y')}</span><br>
                                    <small>From 2:00 PM</small>
                                </div>
                                <div class='summary-item'>
                                    <strong>Check-out Date:</strong><br>
                                    <span class='highlight'>{$checkOut->format('F j, Y')}</span><br>
                                    <small>Until 12:00 PM</small>
                                </div>
                                <div class='summary-item'>
                                    <strong>Duration:</strong><br>
                                    {$nights} night" . ($nights > 1 ? 's' : '') . "
                                </div>
                                <div class='summary-item'>
                                    <strong>Number of Guests:</strong><br>
                                    {$reservation->num_guests} guest" . ($reservation->num_guests > 1 ? 's' : '') . "
                                </div>
                                <div class='summary-item'>
                                    <strong>Room Type:</strong><br>
                                    {$reservation->roomType->type_name}
                                </div>
                                <div class='summary-item'>
                                    <strong>Room Number(s):</strong><br>
                                    {$roomNumbersStr}<br>
                                    <small>{$numRooms} room" . ($numRooms > 1 ? 's' : '') . "</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class='section'>
                            <h3>💵 Amount Breakdown</h3>
                            <div style='background: white; padding: 15px; border-radius: 6px;'>
                                <table style='width: 100%; border-collapse: collapse;'>
                                    <tr>
                                        <td style='padding: 8px 0; border-bottom: 1px solid #eee;'>Room Rate (per night):</td>
                                        <td style='padding: 8px 0; border-bottom: 1px solid #eee; text-align: right;'>₱" . number_format($roomPrice, 2) . "</td>
                                    </tr>
                                    <tr>
                                        <td style='padding: 8px 0; border-bottom: 1px solid #eee;'>Subtotal ({$nights} nights × {$numRooms} rooms):</td>
                                        <td style='padding: 8px 0; border-bottom: 1px solid #eee; text-align: right;'>₱" . number_format($subtotal, 2) . "</td>
                                    </tr>
                                    <tr>
                                        <td style='padding: 8px 0; border-bottom: 1px solid #eee;'>Taxes & Fees:</td>
                                        <td style='padding: 8px 0; border-bottom: 1px solid #eee; text-align: right;'>₱0.00</td>
                                    </tr>
                                    <tr style='font-weight: bold;'>
                                        <td style='padding: 12px 0;'>Total Amount Paid:</td>
                                        <td style='padding: 12px 0; text-align: right; color: #27ae60;'>₱" . number_format($reservation->total_amount, 2) . "</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <div class='section info-section'>
                            <h3>📋 Important Information</h3>
                            <ul style='margin: 0; padding-left: 20px;'>
                                <li>Please bring a <strong>valid government-issued ID</strong> upon check-in</li>
                                <li>Check-in time: <strong>2:00 PM</strong> onwards</li>
                                <li>Check-out time: <strong>before 12:00 PM</strong></li>
                                <li>Early check-in and late check-out are subject to room availability</li>
                                <li>Special requests: <strong>" . ($reservation->special_requests ?: 'None') . "</strong></li>
                                <li>Cancellations must be made at least 24 hours before check-in</li>
                            </ul>
                        </div>
                        
                        <div style='text-align: center; margin: 30px 0; padding: 20px; background: #f8f9fa; border-radius: 8px;'>
                            <p style='margin: 0;'><strong>Need to modify or cancel your booking?</strong></p>
                            <p style='margin: 10px 0 0;'>Contact our reservations team immediately with your booking reference.</p>
                        </div>
                        
                        <p>We look forward to welcoming you to AzureHotel and ensuring you have a comfortable and memorable stay!</p>
                        
                        <p>Best regards,<br>
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

    private function createCreditCardBookingConfirmationTemplate($reservation, $guest, $payment)
    {
        $checkIn = Carbon::parse($reservation->check_in_date);
        $checkOut = Carbon::parse($reservation->check_out_date);
        $nights = $checkIn->diffInDays($checkOut);
        
        $roomNumbers = [];
        foreach($reservation->bookings as $booking) {
            foreach($booking->rooms as $bookingRoom) {
                $roomNumbers[] = $bookingRoom->room->room_number;
            }
        }
        $roomNumbers = array_unique($roomNumbers);
        $roomNumbersStr = implode(', ', $roomNumbers);
        $numRooms = count($roomNumbers);

        $roomPrice = $reservation->roomType->base_price;
        $subtotal = $roomPrice * $nights * $numRooms;
        
        // Updated payment details for successful payment
        $paymentMethod = 'Credit Card Payment';
        $paymentDate = $payment->payment_date 
            ? Carbon::parse($payment->payment_date)->format('F j, Y h:i A') 
            : now()->format('F j, Y h:i A');
        
        return "
            <!DOCTYPE html>
            <html>
            <head>
                <style>
                    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
                    .container { max-width: 600px; margin: 0 auto; background: #ffffff; }
                    .header { background: linear-gradient(135deg, #2c5aa0 0%, #1e3a8a 100%); color: white; padding: 30px 20px; text-align: center; }
                    .content { padding: 30px; }
                    .footer { background: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #666; border-top: 1px solid #e9ecef; }
                    .badge { background: #27ae60; color: white; padding: 10px 20px; border-radius: 30px; display: inline-block; font-weight: bold; margin-bottom: 20px; }
                    .section { background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #2c5aa0; }
                    .payment-section { background: #e8f5e8; border-left: 4px solid #27ae60; }
                    .info-section { background: #e3f2fd; border-left: 4px solid #2196f3; }
                    .summary { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 15px; }
                    .summary-item { background: white; padding: 15px; border-radius: 6px; border: 1px solid #e9ecef; }
                    h3 { color: #2c5aa0; margin-top: 0; }
                    .highlight { color: #27ae60; font-weight: bold; }
                    @media (max-width: 480px) {
                        .summary { grid-template-columns: 1fr; }
                        .header { padding: 20px 15px; }
                        .content { padding: 20px; }
                    }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1 style='margin: 0; font-size: 28px;'>AzureHotel</h1>
                        <p style='margin: 10px 0 0; opacity: 0.9;'>Booking Confirmation</p>
                    </div>
                    
                    <div class='content'>
                        <div style='text-align: center;'>
                            <div class='badge'>✅ BOOKING CONFIRMED & PAID</div>
                        </div>
                        
                        <p>Dear <strong>{$guest->first_name} {$guest->last_name}</strong>,</p>
                        
                        <p>Thank you for choosing AzureHotel! Your credit card payment has been successfully processed and your booking is now confirmed. 
                           Your payment has been received and your reservation is now guaranteed.</p>
                        
                        <div class='section payment-section'>
                            <h3>💰 Payment Information</h3>
                            <div class='summary'>
                                <div class='summary-item'>
                                    <strong>Payment Method:</strong><br>
                                    <span class='highlight'>{$paymentMethod}</span>
                                </div>
                                <div class='summary-item'>
                                    <strong>Amount Paid:</strong><br>
                                    <span class='highlight'>₱" . number_format($payment->amount, 2) . "</span>
                                </div>
                                <div class='summary-item'>
                                    <strong>Transaction ID:</strong><br>
                                    {$payment->transaction_id}
                                </div>
                                <div class='summary-item'>
                                    <strong>Payment Date:</strong><br>
                                    {$paymentDate}
                                </div>
                            </div>
                            <p style='margin-top: 15px; padding: 10px; background: white; border-radius: 6px;'>
                                <strong>Payment Status:</strong> 
                                <span style='color: #27ae60; font-weight: bold;'>✅ Completed</span>
                            </p>
                        </div>
                        
                        <div class='section'>
                            <h3>📅 Booking Summary</h3>
                            <div class='summary'>
                                <div class='summary-item'>
                                    <strong>Booking Reference:</strong><br>
                                    {$payment->transaction_id}
                                </div>
                                <div class='summary-item'>
                                    <strong>Reservation ID:</strong><br>
                                    {$reservation->reservation_id}
                                </div>
                                <div class='summary-item'>
                                    <strong>Check-in Date:</strong><br>
                                    <span class='highlight'>{$checkIn->format('F j, Y')}</span><br>
                                    <small>From 2:00 PM</small>
                                </div>
                                <div class='summary-item'>
                                    <strong>Check-out Date:</strong><br>
                                    <span class='highlight'>{$checkOut->format('F j, Y')}</span><br>
                                    <small>Until 12:00 PM</small>
                                </div>
                                <div class='summary-item'>
                                    <strong>Duration:</strong><br>
                                    {$nights} night" . ($nights > 1 ? 's' : '') . "
                                </div>
                                <div class='summary-item'>
                                    <strong>Number of Guests:</strong><br>
                                    {$reservation->num_guests} guest" . ($reservation->num_guests > 1 ? 's' : '') . "
                                </div>
                                <div class='summary-item'>
                                    <strong>Room Type:</strong><br>
                                    {$reservation->roomType->type_name}
                                </div>
                                <div class='summary-item'>
                                    <strong>Room Number(s):</strong><br>
                                    {$roomNumbersStr}<br>
                                    <small>{$numRooms} room" . ($numRooms > 1 ? 's' : '') . "</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class='section'>
                            <h3>💵 Amount Breakdown</h3>
                            <div style='background: white; padding: 15px; border-radius: 6px;'>
                                <table style='width: 100%; border-collapse: collapse;'>
                                    <tr>
                                        <td style='padding: 8px 0; border-bottom: 1px solid #eee;'>Room Rate (per night):</td>
                                        <td style='padding: 8px 0; border-bottom: 1px solid #eee; text-align: right;'>₱" . number_format($roomPrice, 2) . "</td>
                                    </tr>
                                    <tr>
                                        <td style='padding: 8px 0; border-bottom: 1px solid #eee;'>Subtotal ({$nights} nights × {$numRooms} rooms):</td>
                                        <td style='padding: 8px 0; border-bottom: 1px solid #eee; text-align: right;'>₱" . number_format($subtotal, 2) . "</td>
                                    </tr>
                                    <tr>
                                        <td style='padding: 8px 0; border-bottom: 1px solid #eee;'>Taxes & Fees:</td>
                                        <td style='padding: 8px 0; border-bottom: 1px solid #eee; text-align: right;'>₱0.00</td>
                                    </tr>
                                    <tr style='font-weight: bold;'>
                                        <td style='padding: 12px 0;'>Total Amount Paid:</td>
                                        <td style='padding: 12px 0; text-align: right; color: #27ae60;'>₱" . number_format($reservation->total_amount, 2) . "</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <div class='section info-section'>
                            <h3>📋 Important Information</h3>
                            <ul style='margin: 0; padding-left: 20px;'>
                                <li>Please bring a <strong>valid government-issued ID</strong> upon check-in</li>
                                <li>Check-in time: <strong>2:00 PM</strong> onwards</li>
                                <li>Check-out time: <strong>before 12:00 PM</strong></li>
                                <li>Early check-in and late check-out are subject to room availability</li>
                                <li>Special requests: <strong>" . ($reservation->special_requests ?: 'None') . "</strong></li>
                                <li>Cancellations must be made at least 24 hours before check-in</li>
                            </ul>
                        </div>
                        
                        <div style='text-align: center; margin: 30px 0; padding: 20px; background: #f8f9fa; border-radius: 8px;'>
                            <p style='margin: 0;'><strong>Need to modify or cancel your booking?</strong></p>
                            <p style='margin: 10px 0 0;'>Contact our reservations team immediately with your booking reference.</p>
                        </div>
                        
                        <p>We look forward to welcoming you to AzureHotel and ensuring you have a comfortable and memorable stay!</p>
                        
                        <p>Best regards,<br>
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

    // ... [keep other methods as they are] ...
}