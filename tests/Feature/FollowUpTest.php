<?php

namespace Tests\Feature;

use App\Jobs\ProcessAutomatedFollowUps;
use App\Models\FollowUp;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FollowUpTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_follow_ups_index(): void
    {
        $user = User::factory()->create();
        $lead = Lead::factory()->create(['created_by' => $user->id]);

        FollowUp::create([
            'user_id' => $user->id,
            'lead_id' => $lead->id,
            'scheduled_at' => now(),
            'status' => 'pending',
            'notes' => 'Call lead regarding demo',
        ]);

        $response = $this->actingAs($user)->get(route('follow-ups.index'));

        $response->assertStatus(200);
        $response->assertSee('Call lead regarding demo');
    }

    public function test_user_can_complete_follow_up(): void
    {
        $user = User::factory()->create();
        $lead = Lead::factory()->create(['created_by' => $user->id]);

        $followUp = FollowUp::create([
            'user_id' => $user->id,
            'lead_id' => $lead->id,
            'scheduled_at' => now(),
            'status' => 'pending',
        ]);

        $response = $this->actingAs($user)->post(route('follow-ups.complete', $followUp));

        $response->assertRedirect();
        $followUp->refresh();
        $this->assertEquals('completed', $followUp->status);
        $this->assertNotNull($followUp->completed_at);
    }

    public function test_automated_followup_cancels_when_lead_converts_or_opts_out(): void
    {
        $user = User::factory()->create();
        $lead = Lead::factory()->create([
            'created_by' => $user->id,
            'status' => 'converted',
        ]);

        $followUp = FollowUp::create([
            'user_id' => $user->id,
            'lead_id' => $lead->id,
            'scheduled_at' => now()->subHour(),
            'status' => 'pending',
        ]);

        (new ProcessAutomatedFollowUps())->handle();

        $followUp->refresh();
        $this->assertEquals('cancelled', $followUp->status);
    }
}
