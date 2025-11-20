<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CancelExpiredReservations extends Command
{
    protected $signature = 'reservations:cancel-expired';
    protected $description = 'Cancel expired pending reservations';

    public function handle()
    {
        $expiredReservations = Reservation::where('status', 'pending')
            ->where(function($query) {
                $query->where('expires_at', '<', now())
                      ->orWhere('check_in_date', '<=', Carbon::now()->addDays(2));
            })
            ->get();

        $canceledCount = 0;

        foreach ($expiredReservations as $reservation) {
            if ($reservation->shouldAutoCancel()) {
                $reservation->update(['status' => 'cancelled']);
                $canceledCount++;
                
                $this->info("Cancelled reservation #{$reservation->reservation_id} for {$reservation->guest->first_name} {$reservation->guest->last_name}");
            }
        }

        $this->info("Cancelled {$canceledCount} expired reservations.");
        
        return Command::SUCCESS;
    }
}