<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\User;
use App\Services\Discovery\LeadDiscoveryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class DiscoveryTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_lead_source(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('sources.store'), [
            'name' => 'Delhi Dental Search',
            'type' => 'website_search',
            'config' => [
                'country' => 'India',
                'city' => 'Delhi',
                'category' => 'Dental Clinic',
                'keyword' => 'dentist',
                'max_results' => 10,
            ],
        ]);

        $this->assertDatabaseHas('lead_sources', [
            'name' => 'Delhi Dental Search',
            'user_id' => $user->id,
            'type' => 'website_search',
        ]);
    }

    public function test_website_search_discovery_creates_and_enriches_leads(): void
    {
        $user = User::factory()->create();
        $source = LeadSource::create([
            'user_id' => $user->id,
            'name' => 'Mumbai Clinic Source',
            'type' => 'website_search',
            'config' => [
                'country' => 'India',
                'city' => 'Mumbai',
                'category' => 'Dental Clinic',
                'keyword' => 'dentist',
                'max_results' => 5,
            ],
        ]);

        $response = $this->actingAs($user)->post(route('sources.runSearch', $source), [
            'max_results' => 5,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseCount('leads', 5);
        $this->assertDatabaseCount('lead_enrichments', 5);
    }

    public function test_discovery_deduplicates_leads_by_email_and_website(): void
    {
        $user = User::factory()->create();
        $discoveryService = app(LeadDiscoveryService::class);

        // First run
        $result1 = $discoveryService->executeDiscovery($user->id, 'manual_import', [
            'leads' => [
                [
                    'business_name' => 'Omega Clinic',
                    'contact_name' => 'Dr. Omega',
                    'email' => 'contact@omegaclinic.com',
                    'website' => 'https://www.omegaclinic.com',
                    'city' => 'Mumbai',
                    'category' => 'Clinic',
                ]
            ]
        ]);

        $this->assertEquals(1, $result1['created']);
        $this->assertEquals(0, $result1['updated']);

        // Second run with same email
        $result2 = $discoveryService->executeDiscovery($user->id, 'manual_import', [
            'leads' => [
                [
                    'business_name' => 'Omega Clinic Updated',
                    'contact_name' => 'Dr. Omega 2',
                    'email' => 'contact@omegaclinic.com',
                    'website' => 'https://www.omegaclinic.com',
                    'city' => 'Mumbai',
                    'category' => 'Clinic',
                ]
            ]
        ]);

        $this->assertEquals(0, $result2['created']);
        $this->assertEquals(1, $result2['updated']);
        $this->assertDatabaseCount('leads', 1);
        $this->assertDatabaseHas('leads', ['business_name' => 'Omega Clinic Updated']);
    }

    public function test_csv_file_import_and_export(): void
    {
        $user = User::factory()->create();

        // 1. Import
        $csvContent = "Business Name,Contact Name,Email,Phone,City,Category,Service\n"
            . "Alpha Dental,John Alpha,alpha@dental.in,+919999911111,Mumbai,Dental Clinic,AI Automation\n"
            . "Beta Tech,Jane Beta,beta@tech.in,+919999922222,Bangalore,Software,CRM Development";

        $file = UploadedFile::fake()->createWithContent('leads.csv', $csvContent, 'text/csv');

        $response = $this->actingAs($user)->post(route('leads.importCsv'), [
            'csv_file' => $file,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseCount('leads', 2);
        $this->assertDatabaseHas('leads', ['business_name' => 'Alpha Dental']);

        // 2. Export
        $exportResponse = $this->actingAs($user)->get(route('leads.export'));
        $exportResponse->assertStatus(200);
        $exportResponse->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }
}
