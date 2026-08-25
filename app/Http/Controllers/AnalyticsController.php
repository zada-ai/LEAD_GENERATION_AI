<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\CampaignMessage;
use App\Models\Conversation;
use App\Models\Email;
use App\Models\Lead;
use App\Models\LeadSuppression;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function index(): View
    {
        $userId = auth()->id();

        // 1. Overall Lead Metrics
        $leadMetrics = [
            'total' => Lead::where('created_by', $userId)->count(),
            'new' => Lead::where('created_by', $userId)->where('status', 'new')->count(),
            'qualified' => Lead::where('created_by', $userId)->whereNotNull('ai_score')->where('ai_score', '>=', 70)->count(),
            'contacted' => Lead::where('created_by', $userId)->whereIn('status', ['contacted', 'follow_up', 'interested', 'converted'])->count(),
            'interested' => Lead::where('created_by', $userId)->where('status', 'interested')->count(),
            'converted' => Lead::where('created_by', $userId)->where('status', 'converted')->count(),
            'suppressed' => LeadSuppression::where('user_id', $userId)->count(),
        ];

        // Conversion Rate
        $leadMetrics['conversion_rate'] = $leadMetrics['total'] > 0 
            ? round(($leadMetrics['converted'] / $leadMetrics['total']) * 100, 1) 
            : 0;

        // 2. Email & Campaign Metrics
        $campaignIds = Campaign::where('user_id', $userId)->pluck('id');

        $outreachMetrics = [
            'total_campaigns' => $campaignIds->count(),
            'messages_sent' => CampaignMessage::whereIn('campaign_id', $campaignIds)->where('status', 'sent')->count(),
            'messages_replied' => CampaignMessage::whereIn('campaign_id', $campaignIds)->where('status', 'replied')->count(),
            'messages_pending' => CampaignMessage::whereIn('campaign_id', $campaignIds)->where('status', 'pending_approval')->count(),
        ];

        $outreachMetrics['reply_rate'] = $outreachMetrics['messages_sent'] > 0
            ? round(($outreachMetrics['messages_replied'] / $outreachMetrics['messages_sent']) * 100, 1)
            : 0;

        // 3. Campaign Performance Table
        $campaignStats = Campaign::where('user_id', $userId)
            ->withCount([
                'messages as total_messages',
                'messages as sent_messages' => fn ($q) => $q->where('status', 'sent'),
                'messages as replied_messages' => fn ($q) => $q->where('status', 'replied'),
            ])
            ->get();

        // 4. Service Performance Breakdown
        $serviceBreakdown = Lead::where('created_by', $userId)
            ->select('service_interest', DB::raw('count(*) as count'), DB::raw('sum(case when status = "interested" then 1 else 0 end) as interested_count'), DB::raw('sum(case when status = "converted" then 1 else 0 end) as converted_count'))
            ->groupBy('service_interest')
            ->get();

        // 5. AI Score Distribution
        $aiScoreDistribution = [
            'very_high' => Lead::where('created_by', $userId)->where('ai_score', '>=', 85)->count(),
            'high' => Lead::where('created_by', $userId)->whereBetween('ai_score', [70, 84])->count(),
            'medium' => Lead::where('created_by', $userId)->whereBetween('ai_score', [40, 69])->count(),
            'low' => Lead::where('created_by', $userId)->where('ai_score', '<', 40)->count(),
            'unscored' => Lead::where('created_by', $userId)->whereNull('ai_score')->count(),
        ];

        return view('analytics.index', compact(
            'leadMetrics',
            'outreachMetrics',
            'campaignStats',
            'serviceBreakdown',
            'aiScoreDistribution'
        ));
    }
}
