<?php

namespace Tests\Feature;

use App\Models\Email;
use App\Models\EmailAccount;
use App\Models\Lead;
use App\Models\User;
use App\Services\GeminiService;
use App\Services\LeadQualificationService;
use App\Services\ReplyClassificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GeminiAiTest extends TestCase
{
    use RefreshDatabase;

    public function test_lead_qualification_assigns_valid_score_and_fields(): void
    {
        $user = User::factory()->create();
        $lead = Lead::factory()->create([
            'created_by' => $user->id,
            'business_name' => 'Dr. Paul Dental Center',
            'category' => 'Dental Clinic',
            'priority' => 'high',
            'website' => 'https://paulcare.com',
        ]);

        $qualificationService = app(LeadQualificationService::class);
        $qualified = $qualificationService->qualify($lead);

        $this->assertNotNull($qualified->ai_score);
        $this->assertGreaterThanOrEqual(0, $qualified->ai_score);
        $this->assertLessThanOrEqual(100, $qualified->ai_score);
        $this->assertContains($qualified->ai_priority, ['low', 'medium', 'high', 'very_high']);
        $this->assertNotNull($qualified->ai_service);
        $this->assertNotNull($qualified->ai_summary);
        $this->assertNotNull($qualified->ai_analyzed_at);
    }

    public function test_gemini_generates_personalized_draft_without_hallucinations(): void
    {
        $user = User::factory()->create();
        $lead = Lead::factory()->create([
            'created_by' => $user->id,
            'business_name' => 'Royal Fitness Studio',
            'contact_name' => 'Rajesh Sharma',
            'city' => 'Delhi',
            'category' => 'Gym',
        ]);

        $gemini = app(GeminiService::class);
        $draft = $gemini->generatePersonalizedEmail(
            $lead,
            'AI Chatbot',
            'Growth for {{business_name}}',
            'Hi {{contact_name}}, let us help {{business_name}} in {{city}} with {{service}}.'
        );

        $this->assertArrayHasKey('subject', $draft);
        $this->assertArrayHasKey('body', $draft);
        $this->assertStringContainsString('Royal Fitness Studio', $draft['subject'] . ' ' . $draft['body']);
        $this->assertStringContainsString('Rajesh', $draft['body']);
    }

    public function test_opt_out_reply_classification_triggers_automatic_suppression(): void
    {
        $user = User::factory()->create();
        $account = EmailAccount::create([
            'user_id' => $user->id,
            'email' => 'outreach@demo.com',
            'provider' => 'gmail',
            'status' => 'connected',
        ]);

        $lead = Lead::factory()->create([
            'created_by' => $user->id,
            'email' => 'prospect@business.com',
            'status' => 'contacted',
        ]);

        $email = Email::create([
            'email_account_id' => $account->id,
            'lead_id' => $lead->id,
            'gmail_message_id' => 'msg_reply_999',
            'gmail_thread_id' => 'thread_reply_999',
            'from_email' => 'prospect@business.com',
            'to_email' => $account->email,
            'subject' => 'Re: Outreach',
            'body_text' => 'Please unsubscribe and do not contact our office again. Remove me.',
            'direction' => 'incoming',
        ]);

        $classifier = app(ReplyClassificationService::class);
        $result = $classifier->processIncomingEmail($email);

        $this->assertTrue($result['is_opt_out']);
        $this->assertEquals('Unsubscribe', $result['category']);

        $lead->refresh();
        $this->assertEquals('do_not_contact', $lead->status);
        $this->assertDatabaseHas('lead_suppressions', [
            'email' => 'prospect@business.com',
            'reason' => 'unsubscribed',
        ]);
    }
}
