<?php

namespace App\Jobs;

use App\Models\EmailAccount;
use App\Services\GmailSyncService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Exception;

class SyncGmailAccount implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public EmailAccount $emailAccount
    ) {}

    /**
     * Execute the job.
     */
    public function handle(GmailSyncService $gmailSyncService): void
    {
        try {
            $gmailSyncService->syncAccount($this->emailAccount);
        } catch (Exception $e) {
            \Log::error('Gmail sync job failed for account ' . $this->emailAccount->email . ': ' . $e->getMessage());
            throw $e;
        }
    }
}
