<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\CampaignMessage;
use App\Models\EmailAccount;
use App\Models\Lead;
use App\Models\LeadSuppression;
use App\Models\User;
use App\Services\CampaignService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampaignTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_campaign(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('campaigns.store'), [
            'name' => 'Q1 Dental AI Automation',
            'service' => 'AI Automation',
            'daily_limit' => 25,
            'approval_required' => 1,
            'follow_up_days' => 3,
            'template_subject' => 'Grow {{business_name}}',
            'template_body' => 'Hi {{contact_name}}, scaling with {{service}} in {{city}}.',
            'filter_criteria' => ['category' => 'Dental Clinic'],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('campaigns', [
            'name' => 'Q1 Dental AI Automation',
            'user_id' => $user->id,
            'daily_limit' => 25,
        ]);
        $this->assertDatabaseHas('campaign_templates', [
            'subject' => 'Grow {{business_name}}',
        ]);
    }

    public function test_campaign_excludes_suppressed_and_recently_contacted_leads(): void
    {
        $user = User::factory()->create();
        $campaignService = app(CampaignService::class);

        $campaign = $campaignService->createCampaign($user->id, [
            'name' => 'Test Campaign',
            'service' => 'AI Automation',
            'daily_limit' => 50,
            'approval_required' => true,
            'follow_up_days' => 3,
            'template_subject' => 'Test',
            'template_body' => 'Test',
        ]);

        // 1. Eligible lead
        $eligible = Lead::factory()->create([
            'created_by' => $user->id,
            'status' => 'new',
            'last_outbound_at' => null,
        ]);

        // 2. Suppressed lead
        $suppressed = Lead::factory()->create([
            'created_by' => $user->id,
            'status' => 'do_not_contact',
        ]);
        LeadSuppression::create([
            'user_id' => $user->id,
            'lead_id' => $suppressed->id,
            'email' => $suppressed->email,
            'reason' => 'unsubscribed',
        ]);

        // 3. Recently contacted lead (within 7 days)
        $recent = Lead::factory()->create([
            'created_by' => $user->id,
            'status' => 'contacted',
            'last_outbound_at' => now()->subDays(2),
        ]);

        $leads = $campaignService->getEligibleLeads($campaign);
        $leadIds = collect($leads)->pluck('id')->all();

        $this->assertContains($eligible->id, $leadIds);
        $this->assertNotContains($suppressed->id, $leadIds);
        $this->assertNotContains($recent->id, $leadIds);
    }

    public function test_human_approval_workflow(): void
    {
        $user = User::factory()->create();
        $campaignService = app(CampaignService::class);

        $campaign = $campaignService->createCampaign($user->id, [
            'name' => 'Approval Workflow Test',
            'service' => 'AI Chatbot',
            'approval_required' => true,
            'daily_limit' => 10,
            'follow_up_days' => 3,
        ]);

        $lead = Lead::factory()->create(['created_by' => $user->id]);

        $campaignService->generateMessagesForCampaign($campaign);

        $message = CampaignMessage::where('campaign_id', $campaign->id)->first();
        $this->assertEquals('pending_approval', $message->status);

        $response = $this->actingAs($user)->post(route('campaign-messages.approve', $message));
        $response->assertRedirect();

        $message->refresh();
        $this->assertEquals('approved', $message->status);
        $this->assertNotNull($message->approved_at);
    }

    public function test_daily_limit_and_sending_controls(): void
    {
        $user = User::factory()->create();
        $account = EmailAccount::create([
            'user_id' => $user->id,
            'email' => 'sender@gmail.com',
            'provider' => 'gmail',
            'status' => 'connected',
        ]);

        $campaignService = app(CampaignService::class);
        $campaign = $campaignService->createCampaign($user->id, [
            'name' => 'Limit Test',
            'service' => 'AI Automation',
            'email_account_id' => $account->id,
            'daily_limit' => 2,
            'approval_required' => false,
            'follow_up_days' => 3,
        ]);
        $campaign->update(['status' => 'running']);

        Lead::factory(4)->create(['created_by' => $user->id]);

        $campaignService->generateMessagesForCampaign($campaign);

        $sentCount = $campaignService->sendApprovedMessages($campaign);

        $this->assertEquals(2, $sentCount); // Daily limit enforced to 2
        $this->assertEquals(2, CampaignMessage::where('campaign_id', $campaign->id)->where('status', 'sent')->count());
    }
}
