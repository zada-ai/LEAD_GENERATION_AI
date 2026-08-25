<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessCampaign;
use App\Models\Campaign;
use App\Models\CampaignMessage;
use App\Models\EmailAccount;
use App\Services\CampaignService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Exception;

class CampaignController extends Controller
{
    public function __construct(
        protected CampaignService $campaignService
    ) {}

    public function index(): View
    {
        $campaigns = Campaign::where('user_id', auth()->id())
            ->withCount(['messages as total_messages', 'messages as sent_messages' => function ($q) {
                $q->where('status', 'sent');
            }, 'messages as replied_messages' => function ($q) {
                $q->where('status', 'replied');
            }, 'messages as pending_messages' => function ($q) {
                $q->where('status', 'pending_approval');
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('campaigns.index', compact('campaigns'));
    }

    public function create(): View
    {
        $emailAccounts = EmailAccount::where('user_id', auth()->id())
            ->where('status', 'connected')
            ->get();

        return view('campaigns.create', compact('emailAccounts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'service' => 'required|string|max:100',
            'email_account_id' => 'nullable|exists:email_accounts,id',
            'daily_limit' => 'required|integer|min:1|max:500',
            'approval_required' => 'nullable|boolean',
            'follow_up_days' => 'required|integer|min:1|max:30',
            'template_subject' => 'required|string|max:255',
            'template_body' => 'required|string|max:10000',
            'filter_criteria' => 'nullable|array',
            'filter_criteria.country' => 'nullable|string',
            'filter_criteria.city' => 'nullable|string',
            'filter_criteria.category' => 'nullable|string',
            'filter_criteria.source' => 'nullable|string',
            'filter_criteria.status' => 'nullable|string',
            'filter_criteria.priority' => 'nullable|string',
            'filter_criteria.min_ai_score' => 'nullable|integer|min:0|max:100',
            'filter_criteria.service_interest' => 'nullable|string',
        ]);

        $campaign = $this->campaignService->createCampaign(auth()->id(), $validated);

        return redirect()->route('campaigns.show', $campaign)->with('success', 'Campaign created successfully.');
    }

    public function show(Campaign $campaign): View
    {
        $this->authorizeCampaign($campaign);

        $campaign->load(['template', 'emailAccount']);
        $messages = $campaign->messages()
            ->with(['lead', 'emailAccount'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total' => $campaign->messages()->count(),
            'pending' => $campaign->messages()->where('status', 'pending_approval')->count(),
            'approved' => $campaign->messages()->where('status', 'approved')->count(),
            'sent' => $campaign->messages()->where('status', 'sent')->count(),
            'replied' => $campaign->messages()->where('status', 'replied')->count(),
            'failed' => $campaign->messages()->where('status', 'failed')->count(),
        ];

        return view('campaigns.show', compact('campaign', 'messages', 'stats'));
    }

    public function start(Campaign $campaign): RedirectResponse
    {
        $this->authorizeCampaign($campaign);

        $campaign->update(['status' => 'running']);

        // Dispatch background processing job
        ProcessCampaign::dispatch($campaign);

        return back()->with('success', 'Campaign started. Leads are being prepared.');
    }

    public function pause(Campaign $campaign): RedirectResponse
    {
        $this->authorizeCampaign($campaign);
        $campaign->update(['status' => 'paused']);

        return back()->with('success', 'Campaign paused. No new messages will be sent.');
    }

    public function stop(Campaign $campaign): RedirectResponse
    {
        $this->authorizeCampaign($campaign);
        $campaign->update(['status' => 'completed']);

        return back()->with('success', 'Campaign marked as completed.');
    }

    public function generateDrafts(Campaign $campaign): RedirectResponse
    {
        $this->authorizeCampaign($campaign);

        try {
            $count = $this->campaignService->generateMessagesForCampaign($campaign, 25);
            return back()->with('success', "Generated {$count} personalized email drafts using Gemini AI.");
        } catch (Exception $e) {
            return back()->with('error', 'Draft generation error: ' . $e->getMessage());
        }
    }

    public function approveMessage(CampaignMessage $message): RedirectResponse
    {
        $this->authorizeCampaign($message->campaign);
        $this->campaignService->approveMessage($message);

        return back()->with('success', 'Email approved for sending.');
    }

    public function approveAll(Campaign $campaign): RedirectResponse
    {
        $this->authorizeCampaign($campaign);
        $count = $this->campaignService->approveAllMessages($campaign);

        return back()->with('success', "Approved {$count} pending messages.");
    }

    public function sendNow(Campaign $campaign): RedirectResponse
    {
        $this->authorizeCampaign($campaign);

        try {
            $sent = $this->campaignService->sendApprovedMessages($campaign, 10);
            return back()->with('success', "Sent {$sent} approved messages according to daily limit.");
        } catch (Exception $e) {
            return back()->with('error', 'Send error: ' . $e->getMessage());
        }
    }

    public function destroy(Campaign $campaign): RedirectResponse
    {
        $this->authorizeCampaign($campaign);
        $campaign->delete();

        return redirect()->route('campaigns.index')->with('success', 'Campaign deleted.');
    }

    protected function authorizeCampaign(Campaign $campaign): void
    {
        if ($campaign->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to campaign.');
        }
    }
}
