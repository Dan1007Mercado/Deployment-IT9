<?php
// app/Console/Commands/SetupGmail.php

namespace App\Console\Commands;

use App\Services\GmailService;
use Illuminate\Console\Command;

class SetupGmail extends Command
{
    protected $signature = 'gmail:setup';
    protected $description = 'Setup Gmail API authentication';

    public function handle()
    {
        $this->info("🏨 Azure Hotel System - Gmail API Setup");
        $this->info("==========================================");
        $this->line("");
        
        // Check if credentials file exists
        if (!file_exists(storage_path('app/credentials.json'))) {
            $this->error("❌ credentials.json not found in storage/app/ directory");
            $this->line("");
            $this->line("📋 To get your credentials.json:");
            $this->line("1. Go to https://console.cloud.google.com/");
            $this->line("2. Create a new project or select existing one");
            $this->line("3. Enable Gmail API");
            $this->line("4. Create OAuth 2.0 Client ID credentials");
            $this->line("5. Set application type to 'Desktop Application'");
            $this->line("6. Download the JSON file and save it as 'storage/app/credentials.json'");
            $this->line("");
            $this->line("Once you have the credentials.json file, run this command again.");
            return 1;
        }
        
        $this->info("✅ credentials.json found");
        
        // Check if token already exists
        if (file_exists(storage_path('app/gmail-token.json'))) {
            $this->info("✅ Gmail token already exists");
            
            // Test authentication
            try {
                $gmailService = new GmailService();
                if ($gmailService->isAuthenticated()) {
                    $this->info("✅ Gmail API is properly authenticated!");
                    $this->line("");
                    $this->line("If you need to re-authenticate, delete 'storage/app/gmail-token.json' and run this command again.");
                } else {
                    $this->error("❌ Authentication failed. Please re-authenticate.");
                    $this->line("Delete 'storage/app/gmail-token.json' and run this command again.");
                    return 1;
                }
            } catch (\Exception $e) {
                $this->error("❌ Authentication test failed: " . $e->getMessage());
                $this->line("Delete 'storage/app/gmail-token.json' and run this command again.");
                return 1;
            }
            
            return 0;
        }
        
        $this->line("");
        $this->info("🔐 Step 1: Get Authorization Code");
        $this->line("Open the following URL in your browser:");
        $this->line("");
        
        $gmailService = new GmailService();
        $authUrl = $gmailService->getAuthUrl();
        
        $this->line($authUrl);
        $this->line("");
        
        $this->info("📝 Step 2: Copy the Authorization Code");
        $this->line("After authorizing, you'll get a code. Paste it below.");
        $this->line("");
        
        $authCode = $this->ask('Enter the authorization code from the browser');
        
        if ($authCode) {
            try {
                $this->info("🔄 Setting up Gmail API...");
                $token = $gmailService->setAuthCode($authCode);
                
                $this->info("✅ Gmail API setup completed successfully!");
                $this->line("Token saved to: storage/app/gmail-token.json");
                $this->line("");
                
                $this->info("🎉 You can now send emails through the reservation system!");
                $this->line("");
                $this->line("Emails will be sent for:");
                $this->line("• New reservations");
                $this->line("• Reservation confirmations");
                $this->line("• Payment reminders");
                $this->line("• Cancellation warnings");
                
            } catch (\Exception $e) {
                $this->error("❌ Setup failed: " . $e->getMessage());
                $this->line("");
                $this->line("Please check your credentials and try again.");
                return 1;
            }
        } else {
            $this->error("No authorization code provided.");
            return 1;
        }
        
        return 0;
    }
}