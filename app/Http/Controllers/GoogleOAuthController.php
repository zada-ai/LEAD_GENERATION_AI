<?php

namespace App\Http\Controllers;

use App\Models\EmailAccount;
use App\Services\GmailService;
use App\Services\GmailSyncService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Exception;

class GoogleOAuthController extends Controller
{
    public function __construct(
        protected GmailService $gmailService,
        protected GmailSyncService $gmailSyncService
    ) {}

    /**
     * Redirect user to Google OAuth consent screen.
     */
    public function redirect()
    {
        $authUrl = $this->gmailService->getRedirectUri();
        return redirect($authUrl);
    }

    /**
     * Handle OAuth callback from Google.
     */
    public function callback(Request $request)
    {
        try {
            // Check for errors from Google
            if ($request->has('error')) {
                throw new Exception('Authorization denied by user.');
            }

            // Get authorization code
            $code = $request->query('code');
            if (!$code) {
                throw new Exception('Missing authorization code.');
            }

            // Exchange code for tokens
            $token = $this->gmailService->getTokenFromCode($code);

            // Get Gmail profile
            $this->gmailService->client->setAccessToken($token);
            $profile = $this->gmailService->getProfile();

            // Check if account already connected
            $existingAccount = EmailAccount::where('user_id', auth()->id())
                ->where('email', $profile['email'])
                ->first();

            if ($existingAccount) {
                // Update existing account
                $existingAccount->update([
                    'access_token' => $token['access_token'],
                    'refresh_token' => $token['refresh_token'] ?? $existingAccount->refresh_token,
                    'token_expires_at' => now()->addSeconds($token['expires_in'] ?? 3600),
                    'status' => 'connected',
                ]);
                $emailAccount = $existingAccount;
            } else {
                // Create new account
                $emailAccount = EmailAccount::create([
                    'user_id' => auth()->id(),
                    'provider' => 'gmail',
                    'email' => $profile['email'],
                    'display_name' => $profile['email'],
                    'access_token' => $token['access_token'],
                    'refresh_token' => $token['refresh_token'] ?? null,
                    'token_expires_at' => now()->addSeconds($token['expires_in'] ?? 3600),
                    'scope' => implode(' ', [
                        'https://www.googleapis.com/auth/gmail.readonly',
                        'https://www.googleapis.com/auth/gmail.send',
                        'https://www.googleapis.com/auth/gmail.modify',
                    ]),
                    'status' => 'connected',
                ]);
            }

            return redirect()->route('email-accounts.show', $emailAccount)
                ->with('success', 'Gmail account connected successfully!');
        } catch (Exception $e) {
            return redirect()->route('email-accounts.index')
                ->with('error', 'Failed to connect Gmail: ' . $e->getMessage());
        }
    }

    /**
     * Disconnect a Gmail account.
     */
    public function disconnect(EmailAccount $emailAccount)
    {
        // Authorize user
        $this->authorize('delete', $emailAccount);

        try {
            // Optionally revoke token (may not be necessary)
            if ($emailAccount->access_token) {
                try {
                    $this->gmailService->revokeToken($emailAccount->access_token);
                } catch (Exception $e) {
                    // Token may already be revoked
                }
            }

            // Mark as disconnected but preserve historical data
            $emailAccount->update([
                'status' => 'disconnected',
                'access_token' => null,
                'refresh_token' => null,
            ]);

            return redirect()->route('email-accounts.index')
                ->with('success', 'Gmail account disconnected. Conversations and emails preserved.');
        } catch (Exception $e) {
            return back()->with('error', 'Failed to disconnect: ' . $e->getMessage());
        }
    }
}
