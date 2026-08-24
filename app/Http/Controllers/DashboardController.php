<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Email;
use App\Models\EmailAccount;
use App\Models\Conversation;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index(): View
    {
        $stats = [
            'total_leads' => Lead::where('created_by', auth()->id())->count(),
            'new_leads' => Lead::where('created_by', auth()->id())->where('status', 'new')->count(),
            'contacted' => Lead::where('created_by', auth()->id())->where('status', 'contacted')->count(),
            'interested' => Lead::where('created_by', auth()->id())->where('status', 'interested')->count(),
            'follow_up' => Lead::where('created_by', auth()->id())->where('status', 'follow_up')->count(),
            'converted' => Lead::where('created_by', auth()->id())->where('status', 'converted')->count(),
            'connected_email_accounts' => EmailAccount::where('user_id', auth()->id())->where('status', 'connected')->count(),
            'unread_emails' => Email::whereHas('emailAccount', fn ($query) => $query->where('user_id', auth()->id()))->where('is_read', false)->count(),
            'open_conversations' => Conversation::whereHas('emailAccount', fn ($query) => $query->where('user_id', auth()->id()))->whereIn('status', ['open', 'waiting'])->count(),
            'replies_today' => Email::whereHas('emailAccount', fn ($query) => $query->where('user_id', auth()->id()))->where('direction', 'outgoing')->whereDate('sent_at', today())->count(),
            'follow_ups_due' => Lead::where('created_by', auth()->id())->whereNotNull('next_follow_up_at')->where('next_follow_up_at', '<=', now())->count(),
        ];

        $recentLeads = Lead::where('created_by', auth()->id())
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $statusDistribution = [
            'new' => Lead::where('created_by', auth()->id())->where('status', 'new')->count(),
            'contacted' => Lead::where('created_by', auth()->id())->where('status', 'contacted')->count(),
            'interested' => Lead::where('created_by', auth()->id())->where('status', 'interested')->count(),
            'follow_up' => Lead::where('created_by', auth()->id())->where('status', 'follow_up')->count(),
            'converted' => Lead::where('created_by', auth()->id())->where('status', 'converted')->count(),
            'not_interested' => Lead::where('created_by', auth()->id())->where('status', 'not_interested')->count(),
        ];

        return view('dashboard', [
            'stats' => $stats,
            'recentLeads' => $recentLeads,
            'statusDistribution' => $statusDistribution,
        ]);
    }
}
