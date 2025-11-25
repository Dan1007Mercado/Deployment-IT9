<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReservationConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $reservation;
    public $subject;

    public function __construct(Reservation $reservation, $subject = null)
    {
        $this->reservation = $reservation;
        $this->subject = $subject ?: 'Reservation Confirmation - ' . config('app.name');
    }

    public function build()
    {
        return $this->subject($this->subject)
                    ->view('emails.reservation-confirmation');
    }
}