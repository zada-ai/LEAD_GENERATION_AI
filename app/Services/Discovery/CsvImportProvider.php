<?php

namespace App\Services\Discovery;

use App\Models\LeadSource;
use Exception;

class CsvImportProvider implements LeadDiscoveryProviderInterface
{
    public function getKey(): string
    {
        return 'csv_import';
    }

    public function getName(): string
    {
        return 'CSV File Import';
    }

    public function discover(array $params, ?LeadSource $source = null): array
    {
        $filePath = $params['file_path'] ?? null;
        $csvContent = $params['csv_content'] ?? null;

        if (!$filePath && !$csvContent) {
            return [];
        }

        $rows = [];

        if ($csvContent) {
            $lines = explode("\n", trim($csvContent));
            foreach ($lines as $line) {
                if (trim($line) !== '') {
                    $rows[] = str_getcsv($line);
                }
            }
        } elseif ($filePath && file_exists($filePath)) {
            $handle = fopen($filePath, 'r');
            while (($data = fgetcsv($handle)) !== false) {
                $rows[] = $data;
            }
            fclose($handle);
        }

        if (empty($rows)) {
            return [];
        }

        // Header mapping with space/hyphen normalization
        $rawHeader = array_shift($rows);
        $header = array_map(function ($col) {
            return preg_replace('/[^a-z0-9_]/', '_', strtolower(trim($col)));
        }, $rawHeader);
        $headerMap = array_flip($header);

        $results = [];

        foreach ($rows as $row) {
            if (empty($row) || count($row) < 2) continue;

            $businessName = $this->getColumnValue($row, $headerMap, ['business_name', 'business', 'company', 'name']);
            $email = $this->getColumnValue($row, $headerMap, ['email', 'email_address', 'contact_email']);

            if (empty($businessName) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            $results[] = [
                'business_name' => $businessName,
                'contact_name' => $this->getColumnValue($row, $headerMap, ['contact_name', 'contact', 'person']) ?: 'Owner',
                'email' => strtolower($email),
                'phone' => $this->getColumnValue($row, $headerMap, ['phone', 'phone_number', 'mobile']),
                'website' => $this->getColumnValue($row, $headerMap, ['website', 'url', 'site']),
                'facebook_url' => $this->getColumnValue($row, $headerMap, ['facebook_url', 'facebook']),
                'instagram_url' => $this->getColumnValue($row, $headerMap, ['instagram_url', 'instagram']),
                'linkedin_url' => $this->getColumnValue($row, $headerMap, ['linkedin_url', 'linkedin']),
                'country' => $this->getColumnValue($row, $headerMap, ['country']) ?: 'India',
                'state' => $this->getColumnValue($row, $headerMap, ['state', 'province']),
                'city' => $this->getColumnValue($row, $headerMap, ['city']),
                'area' => $this->getColumnValue($row, $headerMap, ['area', 'address']),
                'category' => $this->getColumnValue($row, $headerMap, ['category', 'industry']) ?: 'Other',
                'service_interest' => $this->getColumnValue($row, $headerMap, ['service_interest', 'service']) ?: 'AI Automation',
                'source' => 'Import',
                'status' => $this->getColumnValue($row, $headerMap, ['status']) ?: 'new',
                'priority' => $this->getColumnValue($row, $headerMap, ['priority']) ?: 'medium',
                'notes' => $this->getColumnValue($row, $headerMap, ['notes', 'comments']),
            ];
        }

        return $results;
    }

    protected function getColumnValue(array $row, array $headerMap, array $aliases): ?string
    {
        foreach ($aliases as $alias) {
            if (isset($headerMap[$alias]) && isset($row[$headerMap[$alias]])) {
                $val = trim($row[$headerMap[$alias]]);
                return $val !== '' ? $val : null;
            }
        }
        return null;
    }
}
