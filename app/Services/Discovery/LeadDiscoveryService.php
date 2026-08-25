<?php

namespace App\Services\Discovery;

use App\Models\Lead;
use App\Models\LeadSource;
use App\Services\ActivityLogger;
use App\Services\LeadDeduplicationService;
use App\Services\LeadEnrichmentService;
use Exception;

class LeadDiscoveryService
{
    /** @var array<string, LeadDiscoveryProviderInterface> */
    protected array $providers = [];

    public function __construct(
        protected LeadDeduplicationService $deduplicationService,
        protected LeadEnrichmentService $enrichmentService
    ) {
        $this->registerProvider(new WebsiteSearchProvider());
        $this->registerProvider(new ManualImportProvider());
        $this->registerProvider(new CsvImportProvider());
    }

    public function registerProvider(LeadDiscoveryProviderInterface $provider): void
    {
        $this->providers[$provider->getKey()] = $provider;
    }

    public function getProviders(): array
    {
        return $this->providers;
    }

    public function getProvider(string $key): LeadDiscoveryProviderInterface
    {
        if (!isset($this->providers[$key])) {
            throw new Exception("Lead discovery provider '{$key}' not registered.");
        }

        return $this->providers[$key];
    }

    /**
     * Run discovery and persist deduplicated leads
     */
    public function executeDiscovery(int $userId, string $providerKey, array $params, ?LeadSource $source = null): array
    {
        $provider = $this->getProvider($providerKey);
        $rawLeads = $provider->discover($params, $source);

        $created = 0;
        $updated = 0;
        $duplicates = 0;
        $processedLeads = [];

        foreach ($rawLeads as $leadData) {
            $result = $this->deduplicationService->createOrUpdateLead($userId, $leadData);
            /** @var Lead $lead */
            $lead = $result['lead'];

            // Auto enrich lead
            $this->enrichmentService->enrich($lead);

            if ($result['action'] === 'created') {
                $created++;
            } else {
                $updated++;
                $duplicates++;
            }

            $processedLeads[] = $lead;
        }

        if ($source) {
            $source->increment('leads_count', $created);
            $source->update(['last_run_at' => now()]);
        }

        ActivityLogger::log(
            userId: $userId,
            type: 'lead_created',
            description: "Lead discovery via {$provider->getName()} yielded {$created} new leads ({$updated} updated duplicates)",
            metadata: [
                'provider' => $providerKey,
                'created' => $created,
                'updated' => $updated,
                'total' => count($rawLeads),
            ]
        );

        return [
            'created' => $created,
            'updated' => $updated,
            'duplicates' => $duplicates,
            'total' => count($rawLeads),
            'leads' => $processedLeads,
        ];
    }
}
