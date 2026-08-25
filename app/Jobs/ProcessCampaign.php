<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Services\CampaignService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessCampaign implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Campaign $campaign
    ) {}

    public function handle(CampaignService $campaignService): void
    {
        if ($this->campaign->status !== 'running') {
            return;
        }

        // 1. Generate drafts for eligible leads if needed
        $campaignService->generateMessagesForCampaign($this->campaign, 25);

        // 2. If campaign doesn't require approval, send approved messages
        if (!$this->campaign->approval_required) {
            $campaignService->sendApprovedMessages($this->campaign, 20);
        }
    }
}
