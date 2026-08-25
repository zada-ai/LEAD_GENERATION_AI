<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;

class ActivityLogger
{
    /**
     * Log an activity in the system
     */
    public static function log(
        int $userId,
        string $type,
        string $description,
        ?int $leadId = null,
        ?int $campaignId = null,
        ?array $metadata = null
    ): ActivityLog {
        return ActivityLog::create([
            'user_id' => $userId,
            'lead_id' => $leadId,
            'campaign_id' => $campaignId,
            'type' => $type,
            'description' => $description,
            'metadata' => $metadata,
        ]);
    }
}
