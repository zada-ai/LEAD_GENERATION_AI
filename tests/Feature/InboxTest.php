<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Lead;
use App\Models\EmailAccount;
use App\Models\Conversation;
use App\Models\Email;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InboxTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_inbox_with_conversations(): void
    {
        $user = User::factory()->create();
        $account = EmailAccount::create([
            'user_id' => $user->id,
            'provider' => 'gmail',
            'email' => 'user@gmail.com',
            'status' => 'connected',
        ]);

        $lead = Lead::factory()->create(['created_by' => $user->id]);

        $conversation = Conversation::create([
            'lead_id' => $lead->id,
            'email_account_id' => $account->id,
            'gmail_thread_id' => 'thread_123',
            'subject' => 'Website redesign request',
            'status' => 'open',
            'last_message_at' => now(),
        ]);

        Email::create([
            'email_account_id' => $account->id,
            'lead_id' => $lead->id,
            'gmail_message_id' => 'msg_123',
            'gmail_thread_id' => 'thread_123',
            'from_email' => $lead->email,
            'to_email' => $account->email,
            'subject' => 'Website redesign request',
            'body_text' => 'Hello, I want to redesign my website.',
            'received_at' => now(),
            'is_read' => false,
        ]);

        $response = $this->actingAs($user)->get(route('inbox.index'));

        $response->assertStatus(200);
        $response->assertSee('Website redesign request');
        $response->assertViewHas('conversations');
    }

    public function test_user_can_view_conversation_and_it_marks_as_read(): void
    {
        $user = User::factory()->create();
        $account = EmailAccount::create([
            'user_id' => $user->id,
            'provider' => 'gmail',
            'email' => 'user@gmail.com',
            'status' => 'connected',
        ]);

        $conversation = Conversation::create([
            'email_account_id' => $account->id,
            'gmail_thread_id' => 'thread_456',
            'subject' => 'Inquiry about pricing',
            'status' => 'open',
            'last_message_at' => now(),
        ]);

        $email = Email::create([
            'email_account_id' => $account->id,
            'gmail_message_id' => 'msg_456',
            'gmail_thread_id' => 'thread_456',
            'from_email' => 'prospect@example.com',
            'to_email' => $account->email,
            'subject' => 'Inquiry about pricing',
            'body_text' => 'What are your rates?',
            'received_at' => now(),
            'is_read' => false,
        ]);

        $this->assertTrue($conversation->hasUnread());

        $response = $this->actingAs($user)->get(route('inbox.show', $conversation));

        $response->assertStatus(200);
        $response->assertSee('What are your rates?');
        $email->refresh();
        $this->assertTrue($email->is_read);
    }

    public function test_user_cannot_view_another_users_conversation(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $account = EmailAccount::create([
            'user_id' => $user2->id,
            'provider' => 'gmail',
            'email' => 'user2@gmail.com',
            'status' => 'connected',
        ]);

        $conversation = Conversation::create([
            'email_account_id' => $account->id,
            'gmail_thread_id' => 'thread_secret',
            'subject' => 'Confidential',
            'status' => 'open',
        ]);

        $response = $this->actingAs($user1)->get(route('inbox.show', $conversation));

        $response->assertStatus(403);
    }

    public function test_user_can_update_conversation_status(): void
    {
        $user = User::factory()->create();
        $account = EmailAccount::create([
            'user_id' => $user->id,
            'provider' => 'gmail',
            'email' => 'user@gmail.com',
            'status' => 'connected',
        ]);

        $conversation = Conversation::create([
            'email_account_id' => $account->id,
            'gmail_thread_id' => 'thread_status',
            'subject' => 'Status test',
            'status' => 'open',
        ]);

        $response = $this->actingAs($user)->post(route('inbox.updateStatus', $conversation), [
            'status' => 'closed',
        ]);

        $response->assertRedirect();
        $conversation->refresh();
        $this->assertEquals('closed', $conversation->status);
    }

    public function test_user_can_link_conversation_to_lead(): void
    {
        $user = User::factory()->create();
        $account = EmailAccount::create([
            'user_id' => $user->id,
            'provider' => 'gmail',
            'email' => 'user@gmail.com',
            'status' => 'connected',
        ]);

        $lead = Lead::factory()->create(['created_by' => $user->id]);

        $conversation = Conversation::create([
            'email_account_id' => $account->id,
            'gmail_thread_id' => 'thread_link',
            'subject' => 'Unlinked subject',
            'status' => 'open',
        ]);

        $response = $this->actingAs($user)->post(route('inbox.linkLead', $conversation), [
            'lead_id' => $lead->id,
        ]);

        $response->assertRedirect();
        $conversation->refresh();
        $this->assertEquals($lead->id, $conversation->lead_id);
    }
}
