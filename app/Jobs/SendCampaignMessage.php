<?php

namespace App\Jobs;

use App\Models\CampaignMessage;
use App\Services\CampaignService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendCampaignMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public CampaignMessage $message
    ) {}

    public function handle(CampaignService $campaignService): void
    {
        // Idempotency check
        if ($this->message->status === 'sent') {
            return;
        }

        $campaignService->sendMessage($this->message);
    }
}
