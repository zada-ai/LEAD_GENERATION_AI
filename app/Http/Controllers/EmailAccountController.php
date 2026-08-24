<?php

namespace App\Http\Controllers;

use App\Models\EmailAccount;
use App\Services\GmailService;
use App\Services\GmailSyncService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Exception;

class EmailAccountController extends Controller
{
    public function __construct(
        protected GmailService $gmailService,
        protected GmailSyncService $gmailSyncService
    ) {}

    /**
     * Display list of connected email accounts.
     */
    public function index(): View
    {
        $emailAccounts = auth()->user()->emailAccounts()
            ->orderBy('created_at', 'desc')
            ->get();

        return view('email-accounts.index', [
            'emailAccounts' => $emailAccounts,
        ]);
    }

    /**
     * Display email account details.
     */
    public function show(EmailAccount $emailAccount): View
    {
        // Authorize user
        $this->authorize('view', $emailAccount);

        $emailCount = $emailAccount->emails()->count();
        $unreadCount = $emailAccount->emails()->where('is_read', false)->count();
        $conversationCount = $emailAccount->conversations()->count();

        return view('email-accounts.show', [
            'emailAccount' => $emailAccount,
            'emailCount' => $emailCount,
            'unreadCount' => $unreadCount,
            'conversationCount' => $conversationCount,
        ]);
    }

    /**
     * Sync email account now.
     */
    public function sync(EmailAccount $emailAccount): RedirectResponse
    {
        // Authorize user
        $this->authorize('update', $emailAccount);

        try {
            if ($emailAccount->status === 'disconnected') {
                return back()->with('error', 'Account is disconnected. Please reconnect.');
            }

            $result = $this->gmailSyncService->syncAccount($emailAccount);

            $message = "Sync complete: {$result['synced']} new emails synced";
            if ($result['duplicates'] > 0) {
                $message .= ", {$result['duplicates']} duplicates skipped";
            }
            if ($result['errors'] > 0) {
                $message .= ", {$result['errors']} errors";
            }

            return back()->with('success', $message);
        } catch (Exception $e) {
            return back()->with('error', 'Sync failed: ' . $e->getMessage());
        }
    }
}
