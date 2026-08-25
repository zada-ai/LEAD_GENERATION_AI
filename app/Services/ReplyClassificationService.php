<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\Email;
use App\Models\Lead;
use App\Models\LeadSuppression;
use App\Models\CampaignMessage;

class ReplyClassificationService
{
    public function __construct(
        protected GeminiService $geminiService
    ) {}

    /**
     * Classify an incoming email reply and take automated action if opt-out
     */
    public function processIncomingEmail(Email $email): array
    {
        $body = $email->body_text ?? strip_tags($email->body_html ?? '');
        $subject = $email->subject ?? '';

        $classification = $this->geminiService->classifyReply($body, $subject);

        // Update conversation status based on classification
        $conversation = $email->conversation;
        if ($conversation) {
            $status = match ($classification['category']) {
                'Interested', 'Pricing', 'Meeting Request' => 'waiting',
                'Not Interested', 'Unsubscribe' => 'closed',
                default => 'open',
            };
            $conversation->update(['status' => $status]);
        }

        // Update campaign messages if linked
        if ($email->lead_id) {
            CampaignMessage::where('lead_id', $email->lead_id)
                ->where('status', 'sent')
                ->update(['status' => 'replied']);
        }

        // Handle Opt-Out / Suppression
        if ($classification['is_opt_out'] || $classification['category'] === 'Unsubscribe') {
            $lead = $email->lead;
            if ($lead) {
                $lead->update(['status' => 'do_not_contact']);

                LeadSuppression::firstOrCreate([
                    'email' => $lead->email,
                ], [
                    'user_id' => $lead->created_by,
                    'lead_id' => $lead->id,
                    'reason' => 'unsubscribed',
                    'source' => 'gemini_reply_opt_out',
                ]);

                ActivityLogger::log(
                    userId: $lead->created_by,
                    type: 'lead_suppressed',
                    description: "Lead automatically suppressed following opt-out reply: {$classification['summary']}",
                    leadId: $lead->id,
                    metadata: $classification
                );
            }
        } elseif (in_array($classification['category'], ['Interested', 'Pricing', 'Meeting Request'])) {
            $lead = $email->lead;
            if ($lead && $lead->status !== 'converted') {
                $lead->update(['status' => 'interested']);

                ActivityLogger::log(
                    userId: $lead->created_by,
                    type: 'email_replied',
                    description: "Positive response classified ({$classification['category']}): {$classification['summary']}",
                    leadId: $lead->id,
                    metadata: $classification
                );
            }
        }

        return $classification;
    }
}
