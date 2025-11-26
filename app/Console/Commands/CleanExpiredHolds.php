<?php
// app/Console/Commands/CleanExpiredHolds.php

namespace App\Console\Commands;

use App\Models\RoomHold;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CleanExpiredHolds extends Command
{
    protected $signature = 'reservations:clean-holds';
    protected $description = 'Clean up expired room holds';

    public function handle()
    {
        $deleted = RoomHold::where('expires_at', '<', Carbon::now())->delete();
        
        $this->info("Cleaned up {$deleted} expired room holds.");
        
        return 0;
    }
}