<?php
// app/Console/Kernel.php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Check pending reservations every 30 minutes
        $schedule->command('reservations:check-pending')->everyThirtyMinutes();
        
        // Clean up expired room holds hourly
        $schedule->command('reservations:clean-holds')->hourly();
        
        // Your existing cancel-expired command
        $schedule->command('reservations:cancel-expired')->everyMinute();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}