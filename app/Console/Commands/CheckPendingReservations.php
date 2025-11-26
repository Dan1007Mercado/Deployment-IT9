<?php
// app/Console/Commands/CheckPendingReservations.php

namespace App\Console\Commands;

use App\Models\Reservation;
use App\Services\GmailService;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CheckPendingReservations extends Command
{
    protected $signature = 'reservations:check-pending';
    protected $description = 'Check and handle pending reservations - send warnings and auto-cancel';

    public function handle()
    {
        $now = Carbon::now();
        $this->info("Starting pending reservations check at: " . $now->format('Y-m-d H:i:s'));
        
        try {
            $gmailService = new GmailService();
            if ($gmailService->isAuthenticated()) {
                $this->info("✅ Gmail Service is authenticated");
            } else {
                $this->warn("⚠️  Gmail API not authenticated. Emails will not be sent.");
                $this->line("Run 'php artisan gmail:setup' to authenticate.");
            }
        } catch (\Exception $e) {
            $this->error("GmailService initialization failed: " . $e->getMessage());
            $this->warn("Continuing without email functionality...");
            $gmailService = null;
        }

        // Find pending reservations created 23 hours ago (send warning)
        $warningTime = $now->copy()->subHours(23);
        $reservationsForWarning = Reservation::with(['guest', 'roomType'])
            ->where('status', 'pending')
            ->where('created_at', '<=', $warningTime)
            ->whereNull('cancelled_at')
            ->get();

        $this->info("Found {$reservationsForWarning->count()} reservations for warning");

        foreach ($reservationsForWarning as $reservation) {
            try {
                $emailSent = false;
                if ($gmailService && $gmailService->isAuthenticated()) {
                    $emailSent = $gmailService->sendReservationCancellationWarning($reservation, $reservation->guest);
                }
                
                if ($emailSent) {
                    $this->info("✓ Sent cancellation warning for reservation #{$reservation->reservation_id} to {$reservation->guest->email}");
                } else {
                    $this->warn("⚠️  Warning not sent for reservation #{$reservation->reservation_id}" . 
                        ($gmailService ? " (email failed)" : " (Gmail not configured)"));
                }
            } catch (\Exception $e) {
                $this->error("Error processing reservation #{$reservation->reservation_id}: " . $e->getMessage());
                Log::error("Warning processing error for reservation #{$reservation->reservation_id}: " . $e->getMessage());
            }
        }

        // Find pending reservations created 24 hours ago (auto-cancel)
        $cancelTime = $now->copy()->subHours(24);
        $reservationsToCancel = Reservation::with(['guest', 'roomType'])
            ->where('status', 'pending')
            ->where('created_at', '<=', $cancelTime)
            ->whereNull('cancelled_at')
            ->get();

        $this->info("Found {$reservationsToCancel->count()} reservations to cancel");

        foreach ($reservationsToCancel as $reservation) {
            DB::beginTransaction();
            try {
                $reservation->update([
                    'status' => 'cancelled',
                    'cancelled_at' => $now,
                    'cancellation_reason' => 'Auto-cancelled: No payment received within 24 hours'
                ]);

                // Send cancellation email if Gmail is configured
                $emailSent = false;
                if ($gmailService && $gmailService->isAuthenticated()) {
                    $emailSent = $gmailService->sendReservationCancelledEmail($reservation, $reservation->guest);
                }

                DB::commit();

                if ($emailSent) {
                    $this->info("✓ Auto-cancelled reservation #{$reservation->reservation_id} and sent email to {$reservation->guest->email}");
                } else {
                    $this->info("✓ Auto-cancelled reservation #{$reservation->reservation_id}" . 
                        ($gmailService ? " but failed to send email" : " (Gmail not configured)"));
                }
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("Error cancelling reservation #{$reservation->reservation_id}: " . $e->getMessage());
                Log::error("Auto-cancellation error for reservation #{$reservation->reservation_id}: " . $e->getMessage());
            }
        }

        $this->info("Completed pending reservations check at: " . Carbon::now()->format('Y-m-d H:i:s'));
    }
}