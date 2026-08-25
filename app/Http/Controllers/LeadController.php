<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeadRequest;
use App\Http\Requests\UpdateLeadRequest;
use App\Models\Lead;
use App\Models\LeadSuppression;
use App\Services\Discovery\LeadDiscoveryService;
use App\Services\LeadEnrichmentService;
use App\Services\LeadQualificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Exception;

class LeadController extends Controller
{
    /**
     * Display a listing of leads.
     */
    public function index(Request $request): View
    {
        $query = Lead::where('created_by', auth()->id())
            ->with(['enrichment'])
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

        // Filter by AI Score
        if ($request->filled('ai_score_tier')) {
            $tier = $request->input('ai_score_tier');
            if ($tier === 'very_high') $query->where('ai_score', '>=', 85);
            elseif ($tier === 'high') $query->whereBetween('ai_score', [70, 84]);
            elseif ($tier === 'medium') $query->whereBetween('ai_score', [40, 69]);
            elseif ($tier === 'low') $query->where('ai_score', '<', 40);
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
        $lead = Lead::create([
            ...$request->validated(),
            'created_by' => auth()->id(),
        ]);

        // Auto-enrich in background
        app(LeadEnrichmentService::class)->enrich($lead);

        return redirect()->route('leads.index')
            ->with('success', 'Lead created successfully.');
    }

    /**
     * Display the specified lead.
     */
    public function show(Lead $lead): View
    {
        $this->authorize('view', $lead);
        $lead->load(['enrichment', 'suppression', 'campaignMessages.campaign', 'followUps', 'emails', 'conversations']);

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
     * Analyze a single lead with Gemini AI.
     */
    public function analyzeWithAi(Lead $lead, LeadQualificationService $qualificationService): RedirectResponse
    {
        $this->authorize('update', $lead);

        try {
            $qualificationService->qualify($lead);
            return back()->with('success', "Lead analyzed by Gemini AI. Score: {$lead->ai_score}/100 ({$lead->ai_priority}).");
        } catch (Exception $e) {
            return back()->with('error', 'AI analysis failed: ' . $e->getMessage());
        }
    }

    /**
     * Enrich a lead profile.
     */
    public function enrich(Lead $lead, LeadEnrichmentService $enrichmentService): RedirectResponse
    {
        $this->authorize('update', $lead);

        try {
            $enrichmentService->enrich($lead);
            return back()->with('success', 'Lead profile enriched successfully.');
        } catch (Exception $e) {
            return back()->with('error', 'Enrichment failed: ' . $e->getMessage());
        }
    }

    /**
     * Handle bulk actions on selected leads.
     */
    public function bulkAction(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'lead_ids' => 'required|array',
            'lead_ids.*' => 'exists:leads,id',
            'bulk_action' => 'required|string|in:status,priority,service,suppress,delete',
            'bulk_value' => 'nullable|string',
        ]);

        $leads = Lead::where('created_by', auth()->id())
            ->whereIn('id', $validated['lead_ids'])
            ->get();

        $count = $leads->count();

        switch ($validated['bulk_action']) {
            case 'status':
                if (!empty($validated['bulk_value'])) {
                    Lead::whereIn('id', $leads->pluck('id'))->update(['status' => $validated['bulk_value']]);
                }
                break;

            case 'priority':
                if (!empty($validated['bulk_value'])) {
                    Lead::whereIn('id', $leads->pluck('id'))->update(['priority' => $validated['bulk_value']]);
                }
                break;

            case 'service':
                if (!empty($validated['bulk_value'])) {
                    Lead::whereIn('id', $leads->pluck('id'))->update(['service_interest' => $validated['bulk_value']]);
                }
                break;

            case 'suppress':
                foreach ($leads as $lead) {
                    $lead->update(['status' => 'do_not_contact']);
                    LeadSuppression::firstOrCreate([
                        'email' => $lead->email,
                    ], [
                        'user_id' => auth()->id(),
                        'lead_id' => $lead->id,
                        'reason' => 'manual_suppression',
                        'source' => 'bulk_action',
                    ]);
                }
                break;

            case 'delete':
                Lead::whereIn('id', $leads->pluck('id'))->delete();
                break;
        }

        return back()->with('success', "Bulk action completed on {$count} leads.");
    }

    /**
     * Export leads as CSV.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $query = Lead::where('created_by', auth()->id())->orderBy('created_at', 'desc');

        if ($request->filled('status')) $query->where('status', $request->input('status'));
        if ($request->filled('category')) $query->where('category', $request->input('category'));

        $leads = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="leads_export_' . date('Y-m-d_His') . '.csv"',
        ];

        return response()->stream(function () use ($leads) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Business Name', 'Contact Name', 'Email', 'Phone', 'Website',
                'Country', 'City', 'Category', 'Service Interest', 'Source',
                'Status', 'Priority', 'AI Score', 'AI Priority', 'Created At'
            ]);

            foreach ($leads as $lead) {
                fputcsv($handle, [
                    $lead->business_name,
                    $lead->contact_name,
                    $lead->email,
                    $lead->phone,
                    $lead->website,
                    $lead->country,
                    $lead->city,
                    $lead->category,
                    $lead->service_interest,
                    $lead->source,
                    $lead->status,
                    $lead->priority,
                    $lead->ai_score,
                    $lead->ai_priority,
                    $lead->created_at->toDateTimeString(),
                ]);
            }
            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Import CSV file of leads.
     */
    public function importCsv(Request $request, LeadDiscoveryService $discoveryService): RedirectResponse
    {
        $request->validate([
            'csv_file' => 'required|file|max:5120',
        ]);

        try {
            $uploaded = $request->file('csv_file');
            $content = file_get_contents($uploaded->getRealPath());

            $result = $discoveryService->executeDiscovery(
                auth()->id(),
                'csv_import',
                [
                    'file_path' => $uploaded->getRealPath(),
                    'csv_content' => $content,
                ]
            );

            return back()->with('success', "Imported {$result['created']} new leads ({$result['updated']} updated duplicates).");
        } catch (Exception $e) {
            return back()->with('error', 'CSV Import failed: ' . $e->getMessage());
        }
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
}
