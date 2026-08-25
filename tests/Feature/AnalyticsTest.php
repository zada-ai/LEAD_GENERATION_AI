<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\CampaignMessage;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_analytics_calculates_real_pipeline_and_conversion_metrics(): void
    {
        $user = User::factory()->create();

        Lead::factory(5)->create(['created_by' => $user->id, 'status' => 'new', 'ai_score' => 80]);
        Lead::factory(3)->create(['created_by' => $user->id, 'status' => 'interested', 'ai_score' => 90]);
        Lead::factory(2)->create(['created_by' => $user->id, 'status' => 'converted', 'ai_score' => 95]);

        $campaign = Campaign::create([
            'user_id' => $user->id,
            'name' => 'Metrics Campaign',
            'service' => 'AI Automation',
            'status' => 'running',
            'daily_limit' => 50,
        ]);

        $lead = Lead::where('created_by', $user->id)->first();
        CampaignMessage::create([
            'campaign_id' => $campaign->id,
            'lead_id' => $lead->id,
            'subject' => 'Test Subject',
            'body' => 'Test Body',
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('analytics.index'));

        $response->assertStatus(200);
        $response->assertSee('Performance Reports');
        $response->assertSee('Conversion Rate');
        $response->assertViewHas('leadMetrics');
        $response->assertViewHas('outreachMetrics');

        $metrics = $response->viewData('leadMetrics');
        $this->assertEquals(10, $metrics['total']);
        $this->assertEquals(2, $metrics['converted']);
        $this->assertEquals(20.0, $metrics['conversion_rate']);
    }
}
