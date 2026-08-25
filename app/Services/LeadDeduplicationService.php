<?php

namespace App\Services;

use App\Models\Lead;

class LeadDeduplicationService
{
    /**
     * Check if a lead matches an existing record for this user
     */
    public function findDuplicate(int $userId, array $data): ?Lead
    {
        $query = Lead::where('created_by', $userId);

        // 1. Match by email
        if (!empty($data['email'])) {
            $byEmail = (clone $query)->where('email', strtolower(trim($data['email'])))->first();
            if ($byEmail) {
                return $byEmail;
            }
        }

        // 2. Match by website
        if (!empty($data['website'])) {
            $cleanWebsite = $this->normalizeWebsite($data['website']);
            if ($cleanWebsite) {
                $byWebsite = (clone $query)
                    ->whereNotNull('website')
                    ->where('website', 'like', "%{$cleanWebsite}%")
                    ->first();

                if ($byWebsite) {
                    return $byWebsite;
                }
            }
        }

        // 3. Match by business name + city
        if (!empty($data['business_name']) && !empty($data['city'])) {
            $byBusinessAndCity = (clone $query)
                ->where('business_name', trim($data['business_name']))
                ->where('city', trim($data['city']))
                ->first();

            if ($byBusinessAndCity) {
                return $byBusinessAndCity;
            }
        }

        // 4. Match by phone
        if (!empty($data['phone'])) {
            $cleanPhone = preg_replace('/[^\d]/', '', $data['phone']);
            if (strlen($cleanPhone) >= 7) {
                $byPhone = (clone $query)
                    ->whereNotNull('phone')
                    ->where('phone', 'like', "%{$cleanPhone}%")
                    ->first();

                if ($byPhone) {
                    return $byPhone;
                }
            }
        }

        return null;
    }

    /**
     * Create or update lead preventing duplicate insertions
     */
    public function createOrUpdateLead(int $userId, array $data): array
    {
        $existing = $this->findDuplicate($userId, $data);

        if ($existing) {
            // Update existing lead with non-empty incoming fields
            $updateData = array_filter($data, fn ($val) => !is_null($val) && $val !== '');
            unset($updateData['created_by']);
            $existing->update($updateData);

            return [
                'action' => 'updated',
                'lead' => $existing,
                'is_duplicate' => true,
            ];
        }

        $lead = Lead::create([
            ...$data,
            'created_by' => $userId,
        ]);

        return [
            'action' => 'created',
            'lead' => $lead,
            'is_duplicate' => false,
        ];
    }

    protected function normalizeWebsite(string $url): string
    {
        $parsed = parse_url($url);
        $host = $parsed['host'] ?? $parsed['path'] ?? '';
        return preg_replace('/^www\./i', '', trim(strtolower($host)));
    }
}
