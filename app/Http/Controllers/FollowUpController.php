<?php

namespace App\Http\Controllers;

use App\Models\FollowUp;
use App\Models\Lead;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FollowUpController extends Controller
{
    public function index(Request $request): View
    {
        $query = FollowUp::where('user_id', auth()->id())
            ->with(['lead', 'campaign']);

        $tab = $request->input('tab', 'due');

        $counts = [
            'due' => (clone $query)->where('status', 'pending')->whereDate('scheduled_at', '<=', today())->count(),
            'upcoming' => (clone $query)->where('status', 'pending')->whereDate('scheduled_at', '>', today())->count(),
            'completed' => (clone $query)->where('status', 'completed')->count(),
            'all' => (clone $query)->count(),
        ];

        $followUps = match ($tab) {
            'upcoming' => $query->where('status', 'pending')->whereDate('scheduled_at', '>', today())->orderBy('scheduled_at', 'asc')->paginate(15),
            'completed' => $query->where('status', 'completed')->orderBy('completed_at', 'desc')->paginate(15),
            'all' => $query->orderBy('scheduled_at', 'desc')->paginate(15),
            default => $query->where('status', 'pending')->whereDate('scheduled_at', '<=', today())->orderBy('scheduled_at', 'asc')->paginate(15),
        };

        return view('follow-ups.index', compact('followUps', 'tab', 'counts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'scheduled_at' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ]);

        $lead = Lead::findOrFail($validated['lead_id']);
        if ($lead->created_by !== auth()->id()) {
            abort(403);
        }

        FollowUp::create([
            'user_id' => auth()->id(),
            'lead_id' => $lead->id,
            'scheduled_at' => $validated['scheduled_at'],
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
        ]);

        $lead->update([
            'next_follow_up_at' => $validated['scheduled_at'],
            'status' => 'follow_up',
        ]);

        ActivityLogger::log(
            userId: auth()->id(),
            type: 'followup_created',
            description: "Follow-up scheduled for {$lead->business_name} on " . date('M d, Y', strtotime($validated['scheduled_at'])),
            leadId: $lead->id
        );

        return back()->with('success', 'Follow-up scheduled.');
    }

    public function complete(FollowUp $followUp): RedirectResponse
    {
        if ($followUp->user_id !== auth()->id()) {
            abort(403);
        }

        $followUp->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        ActivityLogger::log(
            userId: auth()->id(),
            type: 'followup_completed',
            description: "Completed follow-up for {$followUp->lead->business_name}",
            leadId: $followUp->lead_id
        );

        return back()->with('success', 'Follow-up marked as completed.');
    }

    public function cancel(FollowUp $followUp): RedirectResponse
    {
        if ($followUp->user_id !== auth()->id()) {
            abort(403);
        }

        $followUp->update([
            'status' => 'cancelled',
        ]);

        return back()->with('success', 'Follow-up cancelled.');
    }
}
