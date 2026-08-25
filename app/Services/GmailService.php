<?php

namespace App\Services;

use App\Models\EmailAccount;
use Google\Service\Gmail;
use Google\Client;
use Exception;

class GmailService
{
    protected Client $client;
    protected Gmail $service;

    public function __construct()
    {
        $this->client = new Client();
        $this->configureClient();
        $this->service = new Gmail($this->client);
    }

    /**
     * Configure the Google Client with OAuth credentials.
     */
    protected function configureClient(): void
    {
        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));
        $this->client->setRedirectUri(config('services.google.redirect_uri'));
        $this->client->setScopes([
            'https://www.googleapis.com/auth/gmail.readonly',
            'https://www.googleapis.com/auth/gmail.send',
            'https://www.googleapis.com/auth/gmail.modify',
        ]);
        $this->client->setAccessType('offline');
        $this->client->setPrompt('consent');
    }

    /**
     * Get the Google OAuth redirect URL.
     */
    public function getRedirectUri(): string
    {
        return $this->client->createAuthUrl();
    }

    /**
     * Exchange authorization code for tokens.
     */
    public function getTokenFromCode(string $code): array
    {
        try {
            $accessToken = $this->client->fetchAccessTokenWithAuthCode($code);

            if (isset($accessToken['error'])) {
                throw new Exception('Failed to fetch access token: ' . $accessToken['error']);
            }

            return $accessToken;
        } catch (Exception $e) {
            throw new Exception('OAuth error: ' . $e->getMessage());
        }
    }

    /**
     * Set a token returned by Google's OAuth flow.
     */
    public function setAccessToken(array $token): void
    {
        $this->client->setAccessToken($token);
        $this->service = new Gmail($this->client);
    }

    /**
     * Set the email account's access token and refresh Gmail service.
     */
    public function setEmailAccount(EmailAccount $emailAccount): void
    {
        $accessToken = [
            'access_token' => $emailAccount->access_token,
            'refresh_token' => $emailAccount->refresh_token,
            'expires_in' => $emailAccount->token_expires_at ? 
                now()->diffInSeconds($emailAccount->token_expires_at) : 3599,
        ];

        $this->client->setAccessToken($accessToken);
        $this->service = new Gmail($this->client);
    }

    /**
     * Refresh expired access token.
     */
    public function refreshToken(EmailAccount $emailAccount): bool
    {
        try {
            if (!$emailAccount->refresh_token) {
                return false;
            }

            $this->client->setAccessToken([
                'refresh_token' => $emailAccount->refresh_token,
            ]);

            $newToken = $this->client->fetchAccessTokenWithRefreshToken(
                $emailAccount->refresh_token
            );

            if (isset($newToken['error'])) {
                throw new Exception('Failed to refresh token: ' . $newToken['error']);
            }

            $emailAccount->update([
                'access_token' => $newToken['access_token'],
                'token_expires_at' => now()->addSeconds($newToken['expires_in'] ?? 3600),
            ]);

            $this->setEmailAccount($emailAccount);

            return true;
        } catch (Exception $e) {
            $emailAccount->update(['status' => 'error']);
            throw new Exception('Token refresh failed: ' . $e->getMessage());
        }
    }

    /**
     * Get Gmail profile for authenticated user.
     */
    public function getProfile(): array
    {
        try {
            $profile = $this->service->users_getProfile('me');

            return [
                'email' => $profile->getEmailAddress(),
                'messages_total' => $profile->getMessagesTotal(),
                'messages_unread' => $profile->getMessagesUnread(),
            ];
        } catch (Exception $e) {
            throw new Exception('Failed to get Gmail profile: ' . $e->getMessage());
        }
    }

    /**
     * List Gmail messages with optional query.
     */
    public function listMessages(string $query = '', int $maxResults = 50): array
    {
        try {
            $optParams = [
                'maxResults' => min($maxResults, 100),
                'q' => $query,
            ];

            $results = $this->service->users_messages->listUsersMessages('me', $optParams);
            $messages = $results->getMessages() ?? [];

            return [
                'messages' => $messages,
                'next_page_token' => $results->getNextPageToken(),
                'result_size_estimate' => $results->getResultSizeEstimate(),
            ];
        } catch (Exception $e) {
            throw new Exception('Failed to list messages: ' . $e->getMessage());
        }
    }

    /**
     * Get message details by ID.
     */
    public function getMessage(string $messageId): array
    {
        try {
            $message = $this->service->users_messages->get('me', $messageId, [
                'format' => 'full',
            ]);

            return $this->parseMessage($message);
        } catch (Exception $e) {
            throw new Exception('Failed to get message: ' . $e->getMessage());
        }
    }

    /**
     * Parse Gmail message and extract structured data.
     */
    public function parseMessage(\Google\Service\Gmail\Message $message): array
    {
        $payload = $message->getPayload();
        $headers = $payload->getHeaders() ?? [];
        $parts = $payload->getParts() ?? [];

        $data = [
            'gmail_message_id' => $message->getId(),
            'gmail_thread_id' => $message->getThreadId(),
            'labels' => $message->getLabelIds() ?? [],
            'snippet' => $message->getSnippet(),
            'internal_date' => $message->getInternalDate(),
        ];

        // Extract headers
        foreach ($headers as $header) {
            $name = $header->getName();
            $value = $header->getValue();

            switch ($name) {
                case 'From':
                    $data['from_email'] = $this->extractEmail($value);
                    $data['from_name'] = $this->extractName($value);
                    break;
                case 'To':
                    $data['to_email'] = $this->extractEmail($value);
                    break;
                case 'Subject':
                    $data['subject'] = $value;
                    break;
                case 'Message-ID':
                    $data['message_id'] = $value;
                    break;
                case 'In-Reply-To':
                    $data['in_reply_to'] = $value;
                    break;
                case 'References':
                    $data['references'] = $value;
                    break;
                case 'Date':
                    $data['received_at'] = \Carbon\Carbon::parse($value);
                    break;
            }
        }

        // Extract body
        $body = $this->extractBody($payload);
        if ($body['text']) {
            $data['body_text'] = $body['text'];
        }
        if ($body['html']) {
            $data['body_html'] = $body['html'];
        }

        // Set is_read based on labels
        $data['is_read'] = !in_array('UNREAD', $data['labels']);

        return $data;
    }

    /**
     * Extract email address from a header value.
     */
    protected function extractEmail(string $value): string
    {
        preg_match('/<(.+?)>/', $value, $matches);
        return $matches[1] ?? $value;
    }

    /**
     * Extract name from a header value.
     */
    protected function extractName(string $value): ?string
    {
        preg_match('/^(.+?)\s*</', $value, $matches);
        return $matches[1] ? trim($matches[1], '"') : null;
    }

    /**
     * Extract text and HTML body from message payload.
     */
    protected function extractBody(\Google\Service\Gmail\MessagePart $payload): array
    {
        $body = ['text' => null, 'html' => null];
        $mimeType = $payload->getMimeType();

        if ($mimeType === 'text/plain') {
            $data = $payload->getBody();
            $body['text'] = $this->decodeData($data?->getData());
        } elseif ($mimeType === 'text/html') {
            $data = $payload->getBody();
            $body['html'] = $this->decodeData($data?->getData());
        } elseif (strpos($mimeType, 'multipart') === 0) {
            $parts = $payload->getParts() ?? [];
            foreach ($parts as $part) {
                $subBody = $this->extractBody($part);
                if ($subBody['text'] && !$body['text']) {
                    $body['text'] = $subBody['text'];
                }
                if ($subBody['html'] && !$body['html']) {
                    $body['html'] = $subBody['html'];
                }
            }
        }

        return $body;
    }

    /**
     * Decode base64url encoded data from Gmail.
     */
    protected function decodeData(?string $data): ?string
    {
        if (!$data) {
            return null;
        }

        return base64_decode(strtr($data, '-_', '+/'));
    }

    /**
     * Send an email via Gmail API.
     */
    public function sendEmail(string $to, string $subject, string $body): bool
    {
        try {
            $message = $this->createMessage($to, $subject, $body);
            $this->service->users_messages->send('me', $message);

            return true;
        } catch (Exception $e) {
            throw new Exception('Failed to send email: ' . $e->getMessage());
        }
    }

    /**
     * Send a reply to an existing Gmail message.
     */
    public function sendReply(string $threadId, string $to, string $subject, string $body): bool
    {
        try {
            $message = $this->createMessage($to, $subject, $body, $threadId);
            $this->service->users_messages->send('me', $message, [
                'threadId' => $threadId,
            ]);

            return true;
        } catch (Exception $e) {
            throw new Exception('Failed to send reply: ' . $e->getMessage());
        }
    }

    /**
     * Create a Gmail message object.
     */
    protected function createMessage(string $to, string $subject, string $body, ?string $threadId = null): \Google\Service\Gmail\Message
    {
        $email = "From: me\r\n";
        $email .= "To: {$to}\r\n";
        $email .= "Subject: {$subject}\r\n";
        if ($threadId) {
            $email .= "In-Reply-To: <{$threadId}>\r\n";
            $email .= "References: <{$threadId}>\r\n";
        }
        $email .= "Content-Type: text/html; charset=\"UTF-8\"\r\n\r\n";
        $email .= $body;

        $message = new \Google\Service\Gmail\Message();
        if ($threadId) {
            $message->setThreadId($threadId);
        }
        // Use URL-safe base64 encoding as required by Gmail API
        $raw = rtrim(strtr(base64_encode($email), '+/', '-_'), '=');
        $message->setRaw($raw);

        return $message;
    }

    /**
     * Mark message as read.
     */
    public function markAsRead(string $messageId): bool
    {
        try {
            $this->service->users_messages->modify('me', $messageId, new \Google\Service\Gmail\ModifyMessageRequest([
                'removeLabelIds' => ['UNREAD'],
            ]));

            return true;
        } catch (Exception $e) {
            throw new Exception('Failed to mark message as read: ' . $e->getMessage());
        }
    }

    /**
     * Revoke access token.
     */
    public function revokeToken(string $accessToken): bool
    {
        try {
            $this->client->revokeToken($accessToken);
            return true;
        } catch (Exception $e) {
            // Token revocation may fail if already revoked
            return false;
        }
    }
}
