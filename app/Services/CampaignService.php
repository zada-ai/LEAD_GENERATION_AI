<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\CampaignMessage;
use App\Models\CampaignTemplate;
use App\Models\EmailAccount;
use App\Models\FollowUp;
use App\Models\Lead;
use App\Models\LeadSuppression;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class CampaignService
{
    public function __construct(
        protected GeminiService $geminiService,
        protected GmailService $gmailService,
        protected GmailSyncService $gmailSyncService
    ) {}

    /**
     * Create a campaign with template
     */
    public function createCampaign(int $userId, array $data): Campaign
    {
        return DB::transaction(function () use ($userId, $data) {
            $campaign = Campaign::create([
                'user_id' => $userId,
                'email_account_id' => $data['email_account_id'] ?? null,
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'service' => $data['service'] ?? 'AI Automation',
                'status' => 'draft',
                'daily_limit' => (int)($data['daily_limit'] ?? 50),
                'approval_required' => (bool)($data['approval_required'] ?? true),
                'filter_criteria' => $data['filter_criteria'] ?? [],
                'follow_up_days' => (int)($data['follow_up_days'] ?? 3),
                'start_at' => !empty($data['start_at']) ? $data['start_at'] : now(),
                'end_at' => !empty($data['end_at']) ? $data['end_at'] : null,
            ]);

            CampaignTemplate::create([
                'campaign_id' => $campaign->id,
                'subject' => $data['template_subject'] ?? "Growth Opportunity for {{business_name}}",
                'body' => $data['template_body'] ?? "Hi {{contact_name}},\n\nI noticed {{business_name}} is leading in {{category}}. We specialize in {{service}} and would love to help you scale.\n\nBest regards,\nOur Team",
            ]);

            ActivityLogger::log(
                userId: $userId,
                type: 'lead_created',
                description: "Created campaign: {$campaign->name}",
                campaignId: $campaign->id
            );

            return $campaign;
        });
    }

    /**
     * Query eligible leads for a campaign based on criteria and safety rules
     */
    public function getEligibleLeads(Campaign $campaign, int $limit = 500): array
    {
        $criteria = $campaign->filter_criteria ?? [];
        $query = Lead::where('created_by', $campaign->user_id);

        // Exclude suppressed and opted-out leads
        $query->where('status', '!=', 'do_not_contact');
        $query->whereNotIn('email', function ($q) {
            $q->select('email')->from('lead_suppressions');
        });

        // 7-day contact frequency protection
        $query->where(function ($q) {
            $q->whereNull('last_outbound_at')
              ->orWhere('last_outbound_at', '<=', now()->subDays(7));
        });

        // Exclude leads already queued/sent in this campaign
        $query->whereNotIn('id', function ($q) use ($campaign) {
            $q->select('lead_id')->from('campaign_messages')->where('campaign_id', $campaign->id);
        });

        // Apply filters
        if (!empty($criteria['country'])) {
            $query->where('country', $criteria['country']);
        }
        if (!empty($criteria['city'])) {
            $query->where('city', $criteria['city']);
        }
        if (!empty($criteria['area'])) {
            $query->where('area', $criteria['area']);
        }
        if (!empty($criteria['category'])) {
            $query->where('category', $criteria['category']);
        }
        if (!empty($criteria['source'])) {
            $query->where('source', $criteria['source']);
        }
        if (!empty($criteria['status'])) {
            $query->where('status', $criteria['status']);
        }
        if (!empty($criteria['priority'])) {
            $query->where('priority', $criteria['priority']);
        }
        if (isset($criteria['min_ai_score']) && is_numeric($criteria['min_ai_score'])) {
            $query->where('ai_score', '>=', (int)$criteria['min_ai_score']);
        }
        if (!empty($criteria['service_interest'])) {
            $query->where('service_interest', $criteria['service_interest']);
        }

        return $query->limit($limit)->get()->all();
    }

    /**
     * Generate campaign message drafts for eligible leads using Gemini AI personalization
     */
    public function generateMessagesForCampaign(Campaign $campaign, int $limit = 50): int
    {
        $leads = $this->getEligibleLeads($campaign, $limit);
        $template = $campaign->template;
        $count = 0;

        foreach ($leads as $lead) {
            $emailAccount = $campaign->emailAccount ?? EmailAccount::where('user_id', $campaign->user_id)->where('status', 'connected')->first();

            // Personalize using Gemini AI
            $personalized = $this->geminiService->generatePersonalizedEmail(
                $lead,
                $campaign->service,
                $template?->subject ?? "Opportunity for {{business_name}}",
                $template?->body ?? "Hi {{contact_name}}, let's connect."
            );

            $status = $campaign->approval_required ? 'pending_approval' : 'approved';

            CampaignMessage::create([
                'campaign_id' => $campaign->id,
                'lead_id' => $lead->id,
                'email_account_id' => $emailAccount?->id,
                'subject' => $personalized['subject'],
                'body' => $personalized['body'],
                'status' => $status,
                'approved_at' => $campaign->approval_required ? null : now(),
            ]);

            $count++;
        }

        ActivityLogger::log(
            userId: $campaign->user_id,
            type: 'email_generated',
            description: "Generated {$count} personalized email drafts for campaign {$campaign->name}",
            campaignId: $campaign->id
        );

        return $count;
    }

    /**
     * Approve a single campaign message
     */
    public function approveMessage(CampaignMessage $message): void
    {
        $message->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        ActivityLogger::log(
            userId: $message->campaign->user_id,
            type: 'email_approved',
            description: "Approved outreach message for {$message->lead->business_name}",
            leadId: $message->lead_id,
            campaignId: $message->campaign_id
        );
    }

    /**
     * Approve all pending messages for a campaign
     */
    public function approveAllMessages(Campaign $campaign): int
    {
        $updated = CampaignMessage::where('campaign_id', $campaign->id)
            ->where('status', 'pending_approval')
            ->update([
                'status' => 'approved',
                'approved_at' => now(),
            ]);

        ActivityLogger::log(
            userId: $campaign->user_id,
            type: 'email_approved',
            description: "Approved {$updated} outreach messages for campaign {$campaign->name}",
            campaignId: $campaign->id
        );

        return $updated;
    }

    /**
     * Send approved campaign messages enforcing daily limit and safety checks
     */
    public function sendApprovedMessages(Campaign $campaign, int $batchSize = 20): int
    {
        if (!in_array($campaign->status, ['running', 'scheduled'])) {
            return 0;
        }

        // Daily limit check
        $sentToday = CampaignMessage::where('campaign_id', $campaign->id)
            ->where('status', 'sent')
            ->whereDate('sent_at', today())
            ->count();

        $remainingAllowance = max(0, $campaign->daily_limit - $sentToday);
        if ($remainingAllowance <= 0) {
            return 0;
        }

        $messages = CampaignMessage::where('campaign_id', $campaign->id)
            ->where('status', 'approved')
            ->limit(min($batchSize, $remainingAllowance))
            ->get();

        $sentCount = 0;

        foreach ($messages as $message) {
            $sent = $this->sendMessage($message);
            if ($sent) {
                $sentCount++;
            }
        }

        return $sentCount;
    }

    /**
     * Send a single campaign message with strict safety checks
     */
    public function sendMessage(CampaignMessage $message): bool
    {
        return DB::transaction(function () use ($message) {
            $lead = $message->lead;
            $campaign = $message->campaign;

            // 1. Suppression check
            if ($lead->isSuppressed() || LeadSuppression::where('email', $lead->email)->exists()) {
                $message->update([
                    'status' => 'skipped',
                    'error_message' => 'Lead is on suppression list or marked do not contact.',
                ]);
                return false;
            }

            // 2. Frequency protection check
            if ($lead->last_outbound_at && $lead->last_outbound_at->greaterThan(now()->subDays(7))) {
                $message->update([
                    'status' => 'skipped',
                    'error_message' => 'Contacted within last 7 days.',
                ]);
                return false;
            }

            // 3. Email account
            $emailAccount = $message->emailAccount ?? $campaign->emailAccount ?? EmailAccount::where('user_id', $campaign->user_id)->where('status', 'connected')->first();
            if (!$emailAccount || $emailAccount->status !== 'connected') {
                $message->update([
                    'status' => 'failed',
                    'failed_at' => now(),
                    'error_message' => 'No connected Gmail account available.',
                ]);
                return false;
            }

            try {
                // Check if real sending or safe test/simulation mode
                $emailRecord = null;
                if (!empty($emailAccount->access_token) && !app()->runningUnitTests()) {
                    $this->gmailService->setEmailAccount($emailAccount);
                    if ($emailAccount->isTokenExpired()) {
                        $this->gmailService->refreshToken($emailAccount);
                        $emailAccount->refresh();
                    }
                    $this->gmailService->sendEmail($lead->email, $message->subject, $message->body);
                }

                // Store in Part 2 outgoing emails
                $emailRecord = $this->gmailSyncService->storeOutgoingEmail(
                    $emailAccount,
                    $lead,
                    'camp-thread-' . uniqid(),
                    $lead->email,
                    $message->subject,
                    $message->body
                );

                // Update message and lead
                $message->update([
                    'status' => 'sent',
                    'email_id' => $emailRecord->id,
                    'sent_at' => now(),
                ]);

                $lead->update([
                    'status' => in_array($lead->status, ['new', 'follow_up']) ? 'contacted' : $lead->status,
                    'last_outbound_at' => now(),
                    'last_contacted_at' => now(),
                    'next_follow_up_at' => now()->addDays($campaign->follow_up_days),
                ]);

                // Create follow up record
                FollowUp::create([
                    'user_id' => $campaign->user_id,
                    'lead_id' => $lead->id,
                    'campaign_id' => $campaign->id,
                    'campaign_message_id' => $message->id,
                    'scheduled_at' => now()->addDays($campaign->follow_up_days),
                    'status' => 'pending',
                    'notes' => "Follow-up for {$campaign->name} sent on " . now()->format('M d, Y'),
                ]);

                ActivityLogger::log(
                    userId: $campaign->user_id,
                    type: 'email_sent',
                    description: "Sent outreach email to {$lead->contact_name} ({$lead->email}) for {$campaign->name}",
                    leadId: $lead->id,
                    campaignId: $campaign->id
                );

                return true;
            } catch (Exception $e) {
                Log::error("Failed to send campaign message {$message->id}: " . $e->getMessage());

                $message->update([
                    'status' => 'failed',
                    'failed_at' => now(),
                    'error_message' => $e->getMessage(),
                ]);

                return false;
            }
        });
    }
}
