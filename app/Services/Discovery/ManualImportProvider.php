<?php

namespace App\Services\Discovery;

use App\Models\LeadSource;

class ManualImportProvider implements LeadDiscoveryProviderInterface
{
    public function getKey(): string
    {
        return 'manual_import';
    }

    public function getName(): string
    {
        return 'Manual Lead Import';
    }

    public function discover(array $params, ?LeadSource $source = null): array
    {
        $leads = $params['leads'] ?? [];
        $sanitized = [];

        foreach ($leads as $lead) {
            if (empty($lead['business_name']) || empty($lead['email'])) {
                continue;
            }

            $sanitized[] = [
                'business_name' => trim($lead['business_name']),
                'contact_name' => trim($lead['contact_name'] ?? 'Business Owner'),
                'email' => strtolower(trim($lead['email'])),
                'phone' => $lead['phone'] ?? null,
                'website' => $lead['website'] ?? null,
                'country' => $lead['country'] ?? 'India',
                'state' => $lead['state'] ?? null,
                'city' => $lead['city'] ?? null,
                'area' => $lead['area'] ?? null,
                'category' => $lead['category'] ?? 'Other',
                'service_interest' => $lead['service_interest'] ?? 'AI Automation',
                'source' => 'Manual',
                'status' => 'new',
                'priority' => $lead['priority'] ?? 'medium',
                'notes' => $lead['notes'] ?? null,
            ];
        }

        return $sanitized;
    }
}
