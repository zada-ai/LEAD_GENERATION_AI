<?php

namespace App\Services\Discovery;

use App\Models\LeadSource;

interface LeadDiscoveryProviderInterface
{
    /**
     * Get unique provider key
     */
    public function getKey(): string;

    /**
     * Get human-readable provider name
     */
    public function getName(): string;

    /**
     * Run discovery process based on parameters
     * Returns an array of standardized raw lead data items
     */
    public function discover(array $params, ?LeadSource $source = null): array;
}
