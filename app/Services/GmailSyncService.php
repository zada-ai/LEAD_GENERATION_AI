<?php

namespace App\Services;

use App\Models\EmailAccount;
use App\Models\Email;
use App\Models\Conversation;
use App\Models\Lead;
use Exception;

class GmailSyncService
{
    protected GmailService $gmailService;

    public function __construct(GmailService $gmailService)
    {
        $this->gmailService = $gmailService;
    }

    /**
     * Sync recent emails for an email account.
     */
    public function syncAccount(EmailAccount $emailAccount, int $maxMessages = 50): array
    {
        try {
            $this->gmailService->setEmailAccount($emailAccount);

            // Refresh token if expired
            if ($emailAccount->isTokenExpired()) {
                $this->gmailService->refreshToken($emailAccount);
                $emailAccount->refresh();
            }

            // Fetch recent messages
            $query = $emailAccount->last_synced_at 
                ? 'after:' . $emailAccount->last_synced_at->timestamp 
                : '';

            $result = $this->gmailService->listMessages($query, $maxMessages);
            $messages = $result['messages'] ?? [];

            $synced = 0;
            $duplicates = 0;
            $errors = 0;

            foreach ($messages as $messageRef) {
                try {
                    $messageId = $messageRef->getId();

                    // Check if already synced
                    if (Email::where('gmail_message_id', $messageId)->exists()) {
                        $duplicates++;
                        continue;
                    }

                    // Get full message details
                    $messageData = $this->gmailService->getMessage($messageId);

                    // Store email
                    $this->storeEmail($emailAccount, $messageData);
                    $synced++;
                } catch (Exception $e) {
                    \Log::warning('Failed to sync message: ' . $e->getMessage());
                    $errors++;
                }
            }

            // Update last synced timestamp
            $emailAccount->update([
                'last_synced_at' => now(),
                'status' => 'connected',
            ]);

            return [
                'synced' => $synced,
                'duplicates' => $duplicates,
                'errors' => $errors,
                'total' => count($messages),
            ];
        } catch (Exception $e) {
            $emailAccount->update(['status' => 'error']);
            throw new Exception('Sync failed for account ' . $emailAccount->email . ': ' . $e->getMessage());
        }
    }

    /**
     * Store an email in the database and create/update conversation.
     */
    protected function storeEmail(EmailAccount $emailAccount, array $messageData): void
    {
        // Match lead by sender email belonging to this user
        $lead = Lead::where('email', $messageData['from_email'])
            ->where('created_by', $emailAccount->user_id)
            ->first();

        // Create or update conversation
        $conversation = Conversation::updateOrCreate(
            [
                'email_account_id' => $emailAccount->id,
                'gmail_thread_id' => $messageData['gmail_thread_id'],
            ],
            [
                'lead_id' => $lead?->id,
                'subject' => $messageData['subject'] ?? 'No Subject',
                'last_message_at' => $messageData['received_at'] ?? now(),
            ]
        );

        // Create email record
        $email = Email::create([
            'email_account_id' => $emailAccount->id,
            'lead_id' => $lead?->id,
            'gmail_message_id' => $messageData['gmail_message_id'],
            'gmail_thread_id' => $messageData['gmail_thread_id'],
            'message_id' => $messageData['message_id'] ?? null,
            'in_reply_to' => $messageData['in_reply_to'] ?? null,
            'references' => $messageData['references'] ?? null,
            'from_email' => $messageData['from_email'],
            'from_name' => $messageData['from_name'] ?? null,
            'to_email' => $messageData['to_email'] ?? null,
            'subject' => $messageData['subject'] ?? null,
            'body_text' => $messageData['body_text'] ?? null,
            'body_html' => $messageData['body_html'] ?? null,
            'snippet' => $messageData['snippet'] ?? null,
            'direction' => 'incoming',
            'received_at' => $messageData['received_at'] ?? now(),
            'is_read' => $messageData['is_read'] ?? false,
        ]);

        // Update conversation status if it's a first contact
        if ($lead && $lead->status === 'new') {
            $lead->update([
                'status' => 'interested',
                'last_contacted_at' => now(),
            ]);
        }
    }

    /**
     * Store an outgoing email (reply) in the database.
     */
    public function storeOutgoingEmail(
        EmailAccount $emailAccount,
        ?Lead $lead,
        string $threadId,
        string $to,
        string $subject,
        string $body
    ): Email {
        // Find or create conversation
        $conversation = Conversation::updateOrCreate(
            [
                'email_account_id' => $emailAccount->id,
                'gmail_thread_id' => $threadId,
            ],
            [
                'lead_id' => $lead?->id,
                'subject' => $subject,
                'status' => 'replied',
                'last_message_at' => now(),
            ]
        );

        // Create outgoing email record
        $email = Email::create([
            'email_account_id' => $emailAccount->id,
            'lead_id' => $lead?->id,
            'gmail_thread_id' => $threadId,
            'from_email' => $emailAccount->email,
            'from_name' => $emailAccount->display_name,
            'to_email' => $to,
            'subject' => $subject,
            'body_text' => strip_tags($body),
            'body_html' => $body,
            'snippet' => \Illuminate\Support\Str::limit(strip_tags($body), 150),
            'direction' => 'outgoing',
            'sent_at' => now(),
            'received_at' => now(),
            'is_read' => true,
        ]);

        // Update lead's last_contacted_at
        if ($lead) {
            $lead->update(['last_contacted_at' => now()]);
        }

        return $email;
    }

    /**
     * Get email statistics for dashboard.
     */
    public static function getDashboardStats(EmailAccount $emailAccount): array
    {
        $emails = $emailAccount->emails();

        return [
            'total_emails' => $emails->count(),
            'unread_emails' => $emails->where('is_read', false)->count(),
            'incoming_emails' => $emails->where('direction', 'incoming')->count(),
            'outgoing_emails' => $emails->where('direction', 'outgoing')->count(),
        ];
    }
}
