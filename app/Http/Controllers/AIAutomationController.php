<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Services\LeadQualificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Exception;

class AIAutomationController extends Controller
{
    public function __construct(
        protected LeadQualificationService $qualificationService
    ) {}

    public function index(): View
    {
        $userId = auth()->id();

        $stats = [
            'analyzed' => Lead::where('created_by', $userId)->whereNotNull('ai_score')->count(),
            'pending' => Lead::where('created_by', $userId)->whereNull('ai_score')->count(),
            'high_value' => Lead::where('created_by', $userId)->where('ai_score', '>=', 70)->count(),
            'average_score' => round(Lead::where('created_by', $userId)->whereNotNull('ai_score')->avg('ai_score') ?? 0),
        ];

        $pendingLeads = Lead::where('created_by', $userId)
            ->whereNull('ai_score')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $topLeads = Lead::where('created_by', $userId)
            ->whereNotNull('ai_score')
            ->orderBy('ai_score', 'desc')
            ->limit(10)
            ->get();

        return view('ai-automation.index', compact('stats', 'pendingLeads', 'topLeads'));
    }

    public function batchQualify(Request $request): RedirectResponse
    {
        $limit = min((int)$request->input('limit', 10), 25);
        $userId = auth()->id();

        $leads = Lead::where('created_by', $userId)
            ->whereNull('ai_score')
            ->limit($limit)
            ->get();

        $count = 0;
        foreach ($leads as $lead) {
            try {
                $this->qualificationService->qualify($lead);
                $count++;
            } catch (Exception $e) {
                // Continue with next lead
            }
        }

        return back()->with('success', "Batch qualified {$count} leads using Gemini AI.");
    }
}
