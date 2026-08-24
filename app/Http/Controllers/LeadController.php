<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeadRequest;
use App\Http\Requests\UpdateLeadRequest;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeadController extends Controller
{
    /**
     * Display a listing of leads.
     */
    public function index(Request $request): View
    {
        $query = Lead::where('created_by', auth()->id())
            ->orderBy('created_at', 'desc');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('business_name', 'like', "%{$search}%")
                    ->orWhere('contact_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('website', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('area', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->input('priority'));
        }

        // Filter by source
        if ($request->filled('source')) {
            $query->where('source', $request->input('source'));
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        $leads = $query->paginate(15)->appends($request->query());

        return view('leads.index', [
            'leads' => $leads,
            'search' => $request->input('search'),
        ]);
    }

    /**
     * Show the form for creating a new lead.
     */
    public function create(): View
    {
        return view('leads.create');
    }

    /**
     * Store a newly created lead in storage.
     */
    public function store(StoreLeadRequest $request)
    {
        Lead::create([
            ...$request->validated(),
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('leads.index')
            ->with('success', 'Lead created successfully.');
    }

    /**
     * Display the specified lead.
     */
    public function show(Lead $lead): View
    {
        $this->authorize('view', $lead);
        return view('leads.show', ['lead' => $lead]);
    }

    /**
     * Show the form for editing the specified lead.
     */
    public function edit(Lead $lead): View
    {
        $this->authorize('update', $lead);
        return view('leads.edit', ['lead' => $lead]);
    }

    /**
     * Update the specified lead in storage.
     */
    public function update(UpdateLeadRequest $request, Lead $lead)
    {
        $this->authorize('update', $lead);
        $lead->update($request->validated());

        return redirect()->route('leads.show', $lead)
            ->with('success', 'Lead updated successfully.');
    }

    /**
     * Remove the specified lead from storage.
     */
    public function destroy(Lead $lead)
    {
        $this->authorize('delete', $lead);
        $lead->delete();

        return redirect()->route('leads.index')
            ->with('success', 'Lead deleted successfully.');
    }

    /**
     * Mark lead as contacted.
     */
    public function markContacted(Lead $lead)
    {
        $this->authorize('update', $lead);
        $lead->update([
            'status' => 'contacted',
            'last_contacted_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Lead marked as contacted.');
    }

    /**
     * Schedule follow-up.
     */
    public function scheduleFollowUp(Request $request, Lead $lead)
    {
        $this->authorize('update', $lead);

        $validated = $request->validate([
            'next_follow_up_at' => ['required', 'date'],
        ]);

        $lead->update([
            'status' => 'follow_up',
            'next_follow_up_at' => $validated['next_follow_up_at'],
        ]);

        return redirect()->back()
            ->with('success', 'Follow-up scheduled successfully.');
    }

    /**
     * Get dashboard statistics.
     */
    public static function getDashboardStats()
    {
        $userId = auth()->id();

        return [
            'total_leads' => Lead::where('created_by', $userId)->count(),
            'new_leads' => Lead::where('created_by', $userId)->where('status', 'new')->count(),
            'contacted' => Lead::where('created_by', $userId)->where('status', 'contacted')->count(),
            'interested' => Lead::where('created_by', $userId)->where('status', 'interested')->count(),
            'follow_up' => Lead::where('created_by', $userId)->where('status', 'follow_up')->count(),
            'converted' => Lead::where('created_by', $userId)->where('status', 'converted')->count(),
        ];
    }

    /**
     * Get status distribution for dashboard chart.
     */
    public static function getStatusDistribution()
    {
        $userId = auth()->id();

        return [
            'new' => Lead::where('created_by', $userId)->where('status', 'new')->count(),
            'contacted' => Lead::where('created_by', $userId)->where('status', 'contacted')->count(),
            'interested' => Lead::where('created_by', $userId)->where('status', 'interested')->count(),
            'follow_up' => Lead::where('created_by', $userId)->where('status', 'follow_up')->count(),
            'converted' => Lead::where('created_by', $userId)->where('status', 'converted')->count(),
            'not_interested' => Lead::where('created_by', $userId)->where('status', 'not_interested')->count(),
        ];
    }
}
