<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\EmailAccount;
use App\Models\Lead;
use App\Services\GmailService;
use App\Services\GmailSyncService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Exception;

class InboxController extends Controller
{
    public function __construct(
        protected GmailService $gmailService,
        protected GmailSyncService $gmailSyncService
    ) {}

    /**
     * Display inbox with conversations list.
     */
    public function index(Request $request): View
    {
        $emailAccounts = auth()->user()->emailAccounts()
            ->where('status', 'connected')
            ->get();

        // Get selected email account or use first one
        $selectedAccountId = $request->query('account_id');
        $selectedAccount = $emailAccounts->firstWhere('id', $selectedAccountId)
            ?? $emailAccounts->first();

        // Build query for conversations
        $conversationsQuery = Conversation::query();

        if ($selectedAccount) {
            $conversationsQuery->where('email_account_id', $selectedAccount->id);
        } else {
            // Show conversations from all accounts
            $conversationsQuery->whereIn('email_account_id', $emailAccounts->pluck('id'));
        }

        // Apply filters
        $filter = $request->query('filter', 'all');
        match ($filter) {
            'unread' => $conversationsQuery->whereHas('emails', fn ($q) => $q->where('is_read', false)),
            'replied' => $conversationsQuery->where('status', 'replied'),
            'waiting' => $conversationsQuery->where('status', 'waiting'),
            'closed' => $conversationsQuery->where('status', 'closed'),
            default => null,
        };

        // Search
        $search = $request->query('search');
        if ($search) {
            $conversationsQuery->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                    ->orWhereHas('lead', fn ($lq) => 
                        $lq->where('business_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                    )
                    ->orWhereHas('emails', fn ($eq) =>
                        $eq->where('from_email', 'like', "%{$search}%")
                            ->orWhere('subject', 'like', "%{$search}%")
                    );
            });
        }

        $conversations = $conversationsQuery
            ->with('lead', 'emailAccount', 'emails')
            ->orderBy('last_message_at', 'desc')
            ->paginate(20);

        return view('inbox.index', [
            'conversations' => $conversations,
            'emailAccounts' => $emailAccounts,
            'selectedAccount' => $selectedAccount,
            'filter' => $filter,
            'search' => $search,
        ]);
    }

    /**
     * Display conversation details.
     */
    public function show(Conversation $conversation): View
    {
        // Authorize user
        $this->authorize('view', $conversation);

        // Mark as read
        if ($conversation->hasUnread()) {
            $conversation->markAsRead();
        }

        $emails = $conversation->emails()
            ->orderBy('received_at', 'asc')
            ->get();

        $unreadCount = auth()->user()->emailAccounts()
            ->pluck('id')
            ->flatMap(fn ($id) => 
                Conversation::where('email_account_id', $id)
                    ->whereHas('emails', fn ($q) => $q->where('is_read', false))
                    ->pluck('id')
            )
            ->count();

        return view('inbox.show', [
            'conversation' => $conversation,
            'emails' => $emails,
            'unreadCount' => $unreadCount,
        ]);
    }

    /**
     * Send reply to a conversation.
     */
    public function reply(Request $request, Conversation $conversation): RedirectResponse
    {
        // Authorize user
        $this->authorize('update', $conversation);

        try {
            $request->validate([
                'message' => 'required|string|max:10000',
            ]);

            $emailAccount = $conversation->emailAccount;

            if ($emailAccount->status !== 'connected') {
                throw new Exception('Email account is not connected.');
            }

            // Set email account for service
            $this->gmailService->setEmailAccount($emailAccount);

            // Refresh token if needed
            if ($emailAccount->isTokenExpired()) {
                $this->gmailService->refreshToken($emailAccount);
                $emailAccount->refresh();
            }

            // Get the recipient email from latest message
            $latestEmail = $conversation->latestEmail();
            $recipientEmail = $latestEmail->from_email;
            $subject = $latestEmail->subject 
                ? ('Re: ' . $latestEmail->subject)
                : 'Re: (no subject)';

            // Send via Gmail
            $this->gmailService->sendReply(
                $conversation->gmail_thread_id,
                $recipientEmail,
                $subject,
                $request->input('message')
            );

            // Store outgoing email record
            $this->gmailSyncService->storeOutgoingEmail(
                $emailAccount,
                $conversation->lead,
                $conversation->gmail_thread_id,
                $recipientEmail,
                $subject,
                $request->input('message')
            );

            // Update conversation status
            $conversation->update([
                'status' => 'replied',
                'last_message_at' => now(),
            ]);

            return back()->with('success', 'Reply sent successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Failed to send reply: ' . $e->getMessage());
        }
    }

    /**
     * Mark conversation as read.
     */
    public function markAsRead(Conversation $conversation): RedirectResponse
    {
        $this->authorize('update', $conversation);

        $conversation->markAsRead();

        return back()->with('success', 'Conversation marked as read.');
    }

    /**
     * Change conversation status.
     */
    public function updateStatus(Request $request, Conversation $conversation): RedirectResponse
    {
        $this->authorize('update', $conversation);

        $request->validate([
            'status' => 'required|in:open,waiting,replied,closed',
        ]);

        $conversation->update(['status' => $request->input('status')]);

        return back()->with('success', 'Conversation status updated.');
    }

    /**
     * Link conversation to a lead.
     */
    public function linkLead(Request $request, Conversation $conversation): RedirectResponse
    {
        $this->authorize('update', $conversation);

        $request->validate([
            'lead_id' => 'required|exists:leads,id',
        ]);

        $lead = Lead::find($request->input('lead_id'));

        // Authorize user has access to this lead
        $this->authorize('view', $lead);

        $conversation->update(['lead_id' => $lead->id]);

        return back()->with('success', 'Conversation linked to lead.');
    }
}
