<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Lead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_lead_list(): void
    {
        $user = User::factory()->create();
        Lead::factory(3)->create(['created_by' => $user->id]);

        $response = $this->actingAs($user)->get(route('leads.index'));

        $response->assertStatus(200);
        $response->assertViewHas('leads');
    }

    public function test_user_can_create_lead(): void
    {
        $user = User::factory()->create();

        $leadData = [
            'business_name' => 'Acme Corporation',
            'contact_name' => 'John Doe',
            'email' => 'john@acme.com',
            'phone' => '1234567890',
            'website' => 'https://acme.com',
            'country' => 'India',
            'city' => 'Mumbai',
            'category' => 'Software Company',
            'service_interest' => 'AI Automation',
            'source' => 'Manual',
            'status' => 'new',
            'priority' => 'high',
            'notes' => 'Very interested in AI solutions.',
        ];

        $response = $this->actingAs($user)->post(route('leads.store'), $leadData);

        $response->assertRedirect(route('leads.index'));
        $this->assertDatabaseHas('leads', [
            'business_name' => 'Acme Corporation',
            'created_by' => $user->id,
        ]);
    }

    public function test_lead_validation_fails_on_missing_required_fields(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('leads.store'), []);

        $response->assertSessionHasErrors(['business_name', 'contact_name', 'email', 'country', 'category']);
    }

    public function test_user_can_view_their_own_lead(): void
    {
        $user = User::factory()->create();
        $lead = Lead::factory()->create(['created_by' => $user->id]);

        $response = $this->actingAs($user)->get(route('leads.show', $lead));

        $response->assertStatus(200);
        $response->assertSee($lead->business_name);
    }

    public function test_user_cannot_view_another_users_lead(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $lead = Lead::factory()->create(['created_by' => $user2->id]);

        $response = $this->actingAs($user1)->get(route('leads.show', $lead));

        $response->assertStatus(403);
    }

    public function test_user_can_update_their_lead(): void
    {
        $user = User::factory()->create();
        $lead = Lead::factory()->create(['created_by' => $user->id]);

        $response = $this->actingAs($user)->patch(route('leads.update', $lead), [
            'business_name' => 'Updated Business Name',
            'contact_name' => $lead->contact_name,
            'email' => $lead->email,
            'country' => $lead->country,
            'category' => $lead->category,
            'service_interest' => 'Website Development',
            'source' => 'Website',
            'status' => 'contacted',
            'priority' => 'medium',
        ]);

        $response->assertRedirect(route('leads.show', $lead));
        $this->assertDatabaseHas('leads', [
            'id' => $lead->id,
            'business_name' => 'Updated Business Name',
            'status' => 'contacted',
        ]);
    }

    public function test_user_can_delete_their_lead(): void
    {
        $user = User::factory()->create();
        $lead = Lead::factory()->create(['created_by' => $user->id]);

        $response = $this->actingAs($user)->delete(route('leads.destroy', $lead));

        $response->assertRedirect(route('leads.index'));
        $this->assertDatabaseMissing('leads', ['id' => $lead->id]);
    }

    public function test_user_can_mark_lead_as_contacted(): void
    {
        $user = User::factory()->create();
        $lead = Lead::factory()->create([
            'created_by' => $user->id,
            'status' => 'new',
        ]);

        $response = $this->actingAs($user)->post(route('leads.markContacted', $lead));

        $response->assertRedirect();
        $lead->refresh();
        $this->assertEquals('contacted', $lead->status);
        $this->assertNotNull($lead->last_contacted_at);
    }

    public function test_user_can_schedule_follow_up(): void
    {
        $user = User::factory()->create();
        $lead = Lead::factory()->create(['created_by' => $user->id]);

        $date = now()->addDays(5)->format('Y-m-d');
        $response = $this->actingAs($user)->post(route('leads.scheduleFollowUp', $lead), [
            'next_follow_up_at' => $date,
        ]);

        $response->assertRedirect();
        $lead->refresh();
        $this->assertEquals('follow_up', $lead->status);
        $this->assertNotNull($lead->next_follow_up_at);
    }
}
