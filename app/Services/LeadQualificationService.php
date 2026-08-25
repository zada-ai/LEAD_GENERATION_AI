<?php

namespace App\Services;

use App\Models\Lead;

class LeadQualificationService
{
    public function __construct(
        protected GeminiService $geminiService,
        protected LeadEnrichmentService $enrichmentService
    ) {}

    /**
     * Qualify a lead with enrichment and Gemini AI analysis
     */
    public function qualify(Lead $lead): Lead
    {
        // 1. Ensure lead has enrichment details
        $enrichment = $lead->enrichment ?? $this->enrichmentService->enrich($lead);

        // 2. Perform Gemini AI analysis
        $aiResult = $this->geminiService->analyzeLead($lead, $enrichment);

        // 3. Update lead with structured AI fields
        $lead->update([
            'ai_score' => $aiResult['score'],
            'ai_priority' => $aiResult['priority'],
            'ai_service' => $aiResult['best_service'],
            'ai_summary' => $aiResult['summary'] . "\n\nAngle: " . $aiResult['recommended_angle'],
            'ai_analyzed_at' => now(),
        ]);

        ActivityLogger::log(
            userId: $lead->created_by,
            type: 'ai_analyzed',
            description: "Gemini AI qualified lead with Score {$aiResult['score']} ({$aiResult['priority']}) for {$aiResult['best_service']}",
            leadId: $lead->id,
            metadata: $aiResult
        );

        return $lead;
    }
}
