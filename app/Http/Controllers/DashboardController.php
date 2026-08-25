<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Campaign;
use App\Models\CampaignMessage;
use App\Models\Conversation;
use App\Models\Email;
use App\Models\EmailAccount;
use App\Models\FollowUp;
use App\Models\Lead;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index(): View
    {
        $userId = auth()->id();

        $stats = [
            'total_leads' => Lead::where('created_by', $userId)->count(),
            'new_leads' => Lead::where('created_by', $userId)->where('status', 'new')->count(),
            'contacted' => Lead::where('created_by', $userId)->where('status', 'contacted')->count(),
            'interested' => Lead::where('created_by', $userId)->where('status', 'interested')->count(),
            'follow_up' => Lead::where('created_by', $userId)->where('status', 'follow_up')->count(),
            'converted' => Lead::where('created_by', $userId)->where('status', 'converted')->count(),
            'qualified_leads' => Lead::where('created_by', $userId)->whereNotNull('ai_score')->where('ai_score', '>=', 70)->count(),
            'active_campaigns' => Campaign::where('user_id', $userId)->where('status', 'running')->count(),
            'emails_sent_today' => CampaignMessage::whereHas('campaign', fn ($q) => $q->where('user_id', $userId))->where('status', 'sent')->whereDate('sent_at', today())->count(),
            'replies_today' => Email::whereHas('emailAccount', fn ($query) => $query->where('user_id', $userId))->where('direction', 'incoming')->whereDate('received_at', today())->count(),
            'connected_email_accounts' => EmailAccount::where('user_id', $userId)->where('status', 'connected')->count(),
            'unread_emails' => Email::whereHas('emailAccount', fn ($query) => $query->where('user_id', $userId))->where('is_read', false)->count(),
            'open_conversations' => Conversation::whereHas('emailAccount', fn ($query) => $query->where('user_id', $userId))->whereIn('status', ['open', 'waiting'])->count(),
            'follow_ups_due' => FollowUp::where('user_id', $userId)->where('status', 'pending')->whereDate('scheduled_at', '<=', today())->count(),
        ];

        $recentLeads = Lead::where('created_by', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        $recentActivities = ActivityLog::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        $activeCampaigns = Campaign::where('user_id', $userId)
            ->whereIn('status', ['running', 'scheduled', 'draft'])
            ->withCount(['messages as total_messages', 'messages as sent_messages' => fn ($q) => $q->where('status', 'sent')])
            ->limit(4)
            ->get();

        $statusDistribution = [
            'new' => Lead::where('created_by', $userId)->where('status', 'new')->count(),
            'contacted' => Lead::where('created_by', $userId)->where('status', 'contacted')->count(),
            'interested' => Lead::where('created_by', $userId)->where('status', 'interested')->count(),
            'follow_up' => Lead::where('created_by', $userId)->where('status', 'follow_up')->count(),
            'converted' => Lead::where('created_by', $userId)->where('status', 'converted')->count(),
            'not_interested' => Lead::where('created_by', $userId)->where('status', 'not_interested')->count(),
        ];

        return view('dashboard', [
            'stats' => $stats,
            'recentLeads' => $recentLeads,
            'recentActivities' => $recentActivities,
            'activeCampaigns' => $activeCampaigns,
            'statusDistribution' => $statusDistribution,
        ]);
    }
}
