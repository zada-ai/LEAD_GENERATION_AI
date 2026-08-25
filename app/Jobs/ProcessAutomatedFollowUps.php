<?php

namespace App\Jobs;

use App\Models\FollowUp;
use App\Models\Lead;
use App\Services\ActivityLogger;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessAutomatedFollowUps implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $dueFollowUps = FollowUp::where('status', 'pending')
            ->where('scheduled_at', '<=', now())
            ->with(['lead', 'campaign'])
            ->get();

        foreach ($dueFollowUps as $followUp) {
            $lead = $followUp->lead;

            // Check if lead has already replied, converted, or unsubscribed
            if (in_array($lead->status, ['interested', 'converted', 'do_not_contact', 'not_interested'])) {
                $followUp->update([
                    'status' => 'cancelled',
                    'notes' => ($followUp->notes ?? '') . " [Auto-cancelled: lead status is {$lead->status}]",
                ]);
                continue;
            }

            // Check suppression
            if ($lead->isSuppressed()) {
                $followUp->update([
                    'status' => 'cancelled',
                    'notes' => ($followUp->notes ?? '') . " [Auto-cancelled: lead is suppressed]",
                ]);
                continue;
            }
        }
    }
}
