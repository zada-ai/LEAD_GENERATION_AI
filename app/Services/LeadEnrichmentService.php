<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\LeadEnrichment;

class LeadEnrichmentService
{
    /**
     * Enrich a lead with structured metadata and digital opportunity notes
     */
    public function enrich(Lead $lead): LeadEnrichment
    {
        $detectedServices = $this->detectPotentialServices($lead);
        $technologyInfo = $this->detectTechnologyInfo($lead);
        $websiteSummary = $this->generateWebsiteSummary($lead);
        $opportunityNotes = $this->generateOpportunityNotes($lead, $detectedServices);

        return LeadEnrichment::updateOrCreate(
            ['lead_id' => $lead->id],
            [
                'description' => "Enriched profile for {$lead->business_name} ({$lead->category}) located in {$lead->city}, {$lead->country}.",
                'detected_services' => $detectedServices,
                'website_summary' => $websiteSummary,
                'technology_info' => $technologyInfo,
                'opportunity_notes' => $opportunityNotes,
                'enriched_at' => now(),
            ]
        );
    }

    protected function detectPotentialServices(Lead $lead): array
    {
        $services = [];

        if (empty($lead->website)) {
            $services[] = 'Website Development';
            $services[] = 'SEO';
        } else {
            $services[] = 'AI Chatbot';
            $services[] = 'AI Automation';
        }

        if (in_array($lead->category, ['Dental Clinic', 'Salon', 'Gym', 'Restaurant'])) {
            $services[] = 'CRM Development';
            $services[] = 'AI Automation';
        }

        if ($lead->service_interest && !in_array($lead->service_interest, $services)) {
            $services[] = $lead->service_interest;
        }

        return array_unique($services);
    }

    protected function detectTechnologyInfo(Lead $lead): array
    {
        return [
            'has_website' => !empty($lead->website),
            'has_social_presence' => !empty($lead->facebook_url) || !empty($lead->instagram_url) || !empty($lead->linkedin_url),
            'platform_indications' => !empty($lead->website) ? ['SSL Enabled', 'Responsive Design Opportunity'] : ['No Web Platform Detected'],
        ];
    }

    protected function generateWebsiteSummary(Lead $lead): string
    {
        if (empty($lead->website)) {
            return "No public website currently on record for {$lead->business_name}. Opportunity for new website development and local SEO presence.";
        }

        return "Active domain {$lead->website} associated with {$lead->business_name} in {$lead->category}. Opportunity for automated chat assistance and lead conversion workflows.";
    }

    protected function generateOpportunityNotes(Lead $lead, array $services): string
    {
        $serviceList = implode(', ', $services);
        return "Key opportunities identified: {$serviceList}. Best outreach strategy is pitching customized {$lead->service_interest} targeting {$lead->category} customer acquisition.";
    }
}
