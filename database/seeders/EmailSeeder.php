<?php

namespace Database\Seeders;

use App\Models\EmailAccount;
use App\Models\Email;
use App\Models\Conversation;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Seeder;

class EmailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        
        if (!$user) {
            return;
        }

        // Create demo email account (disconnected status)
        $emailAccount = EmailAccount::create([
            'user_id' => $user->id,
            'provider' => 'gmail',
            'email' => $user->email,
            'display_name' => $user->name,
            'access_token' => null,
            'refresh_token' => null,
            'token_expires_at' => null,
            'scope' => 'https://www.googleapis.com/auth/gmail.readonly https://www.googleapis.com/auth/gmail.send https://www.googleapis.com/auth/gmail.modify',
            'status' => 'disconnected',
            'last_synced_at' => null,
        ]);

        // Create demo leads for email associations
        $leads = Lead::where('created_by', $user->id)->take(3)->get();

        if ($leads->count() >= 1) {
            // Create demo conversation and emails for first lead
            $lead = $leads[0];
            
            $conversation = Conversation::create([
                'lead_id' => $lead->id,
                'email_account_id' => $emailAccount->id,
                'gmail_thread_id' => 'thread-' . uniqid(),
                'subject' => 'Website Design Inquiry',
                'status' => 'replied',
                'last_message_at' => now()->subDays(2),
            ]);

            Email::create([
                'email_account_id' => $emailAccount->id,
                'lead_id' => $lead->id,
                'gmail_message_id' => 'msg-' . uniqid(),
                'gmail_thread_id' => $conversation->gmail_thread_id,
                'from_email' => $lead->email,
                'from_name' => $lead->contact_name,
                'to_email' => $user->email,
                'subject' => 'Website Design Inquiry',
                'body_text' => 'Hi,\n\nWe are interested in your web design services for our dental clinic. Can you provide a quote?\n\nBest regards,\n' . $lead->contact_name,
                'body_html' => '<p>Hi,</p><p>We are interested in your web design services for our dental clinic. Can you provide a quote?</p><p>Best regards,<br>' . $lead->contact_name . '</p>',
                'snippet' => 'We are interested in your web design services for our dental clinic. Can you provide a quote?',
                'direction' => 'incoming',
                'received_at' => now()->subDays(3),
                'is_read' => true,
            ]);

            Email::create([
                'email_account_id' => $emailAccount->id,
                'lead_id' => $lead->id,
                'gmail_thread_id' => $conversation->gmail_thread_id,
                'from_email' => $user->email,
                'from_name' => $user->name,
                'to_email' => $lead->email,
                'subject' => 'Re: Website Design Inquiry',
                'body_text' => 'Hi ' . $lead->contact_name . ',\n\nThank you for your inquiry. We\'d be happy to help with your dental clinic website. I\'ll send you a detailed proposal shortly.\n\nBest regards,\n' . $user->name,
                'body_html' => '<p>Hi ' . $lead->contact_name . ',</p><p>Thank you for your inquiry. We\'d be happy to help with your dental clinic website. I\'ll send you a detailed proposal shortly.</p><p>Best regards,<br>' . $user->name . '</p>',
                'snippet' => 'Thank you for your inquiry. We\'d be happy to help with your dental clinic website.',
                'direction' => 'outgoing',
                'sent_at' => now()->subDays(2),
                'is_read' => true,
            ]);
        }

        if ($leads->count() >= 2) {
            // Create demo conversation for second lead
            $lead = $leads[1];
            
            $conversation = Conversation::create([
                'lead_id' => $lead->id,
                'email_account_id' => $emailAccount->id,
                'gmail_thread_id' => 'thread-' . uniqid(),
                'subject' => 'Proposal Follow-up',
                'status' => 'waiting',
                'last_message_at' => now()->subDay(),
            ]);

            Email::create([
                'email_account_id' => $emailAccount->id,
                'lead_id' => $lead->id,
                'gmail_message_id' => 'msg-' . uniqid(),
                'gmail_thread_id' => $conversation->gmail_thread_id,
                'from_email' => $lead->email,
                'from_name' => $lead->contact_name,
                'to_email' => $user->email,
                'subject' => 'Proposal Follow-up',
                'body_text' => 'Hi,\n\nWe received your proposal. Looks good! Can you tell us more about your development timeline?\n\nThanks,\n' . $lead->contact_name,
                'body_html' => '<p>Hi,</p><p>We received your proposal. Looks good! Can you tell us more about your development timeline?</p><p>Thanks,<br>' . $lead->contact_name . '</p>',
                'snippet' => 'We received your proposal. Looks good! Can you tell us more about your development timeline?',
                'direction' => 'incoming',
                'received_at' => now()->subDay(),
                'is_read' => false,
            ]);
        }
    }
}
