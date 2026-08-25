<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Campaign;
use App\Models\CampaignMessage;
use App\Models\CampaignTemplate;
use App\Models\EmailAccount;
use App\Models\FollowUp;
use App\Models\Lead;
use App\Models\LeadEnrichment;
use App\Models\LeadSource;
use App\Models\LeadSuppression;
use App\Models\User;
use App\Models\UserSetting;
use App\Services\ActivityLogger;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create or retrieve demo user
        $user = User::firstOrCreate(
            ['email' => 'demo@example.com'],
            [
                'name' => 'Demo Administrator',
                'password' => bcrypt('password123'),
            ]
        );

        // 2. User Settings
        UserSetting::firstOrCreate(
            ['user_id' => $user->id],
            [
                'gemini_model' => 'gemini-2.5-flash',
                'enable_ai_analysis' => true,
                'enable_ai_personalization' => true,
                'default_daily_limit' => 50,
                'default_follow_up_days' => 3,
                'default_approval_required' => true,
            ]
        );

        // 3. Email Account
        $emailAccount = EmailAccount::firstOrCreate(
            ['email' => 'demo.outreach@gmail.com'],
            [
                'user_id' => $user->id,
                'provider' => 'gmail',
                'display_name' => 'Demo Outreach Lead',
                'status' => 'connected',
            ]
        );

        // 4. Lead Sources
        $source1 = LeadSource::firstOrCreate(
            ['name' => 'Mumbai Dental Clinics Discovery', 'user_id' => $user->id],
            [
                'type' => 'website_search',
                'config' => [
                    'country' => 'India',
                    'city' => 'Mumbai',
                    'category' => 'Dental Clinic',
                    'keyword' => 'dentist clinic',
                    'max_results' => 15,
                ],
                'leads_count' => 15,
                'status' => 'active',
                'last_run_at' => now()->subHours(4),
            ]
        );

        $source2 = LeadSource::firstOrCreate(
            ['name' => 'Bangalore Tech Startups', 'user_id' => $user->id],
            [
                'type' => 'website_search',
                'config' => [
                    'country' => 'India',
                    'city' => 'Bangalore',
                    'category' => 'Software Company',
                    'keyword' => 'saas startup',
                    'max_results' => 10,
                ],
                'leads_count' => 10,
                'status' => 'active',
                'last_run_at' => now()->subDays(1),
            ]
        );

        // 5. Create 30 Realistic Leads
        $leadProfiles = [
            ['Apex Dental Care', 'Dr. Rajesh Sharma', 'contact@apexdentalmumbai.com', '+91 98201 12345', 'https://www.apexdentalmumbai.com', 'Dental Clinic', 'AI Chatbot', 92, 'very_high', 'Mumbai'],
            ['Smile Studio Bandra', 'Dr. Priya Desai', 'info@smilestudiobandra.in', '+91 98202 23456', 'https://www.smilestudiobandra.in', 'Dental Clinic', 'AI Automation', 88, 'very_high', 'Mumbai'],
            ['CarePlus Polyclinic', 'Amit Verma', 'care@careplusmumbai.com', '+91 98203 34567', null, 'Healthcare Clinic', 'Website Development', 78, 'high', 'Mumbai'],
            ['Nova Software Solutions', 'Vikram Patel', 'vikram@novasolutions.io', '+91 98204 45678', 'https://www.novasolutions.io', 'Software Company', 'CRM Development', 85, 'very_high', 'Bangalore'],
            ['CloudScale AI Labs', 'Siddharth Sen', 'hello@cloudscaleai.dev', '+91 98205 56789', 'https://www.cloudscaleai.dev', 'Software Company', 'App Development', 82, 'high', 'Bangalore'],
            ['Urban Fitness Studio', 'Rohan Mehra', 'rohan@urbanfitnessgym.in', '+91 98206 67890', null, 'Gym', 'AI Automation', 68, 'medium', 'Delhi'],
            ['Elite Hair & Skin Salon', 'Sneha Kapoor', 'info@elitesalonandspa.in', '+91 98207 78901', 'https://www.elitesalonandspa.in', 'Salon', 'AI Chatbot', 74, 'high', 'Delhi'],
            ['Taste of Punjab Diner', 'Harpreet Singh', 'contact@tasteofpunjabdiner.com', '+91 98208 89012', null, 'Restaurant', 'SEO', 55, 'medium', 'Delhi'],
            ['Zenith Logistics Ltd', 'Manoj Kulkarni', 'ops@zenithlogistics.in', '+91 98209 90123', 'https://www.zenithlogistics.in', 'Logistics', 'CRM Development', 71, 'high', 'Pune'],
            ['Optima Financial Advisory', 'Sunil Mehta', 'sunil@optimafinancial.in', '+91 98210 01234', 'https://www.optimafinancial.in', 'Financial Services', 'AI Automation', 84, 'high', 'Mumbai'],
            ['GreenLeaf Organics', 'Ananya Roy', 'ananya@greenleaforganics.shop', '+91 98211 12345', 'https://www.greenleaforganics.shop', 'E-Commerce', 'SEO', 62, 'medium', 'Kolkata'],
            ['Heritage Luxury Realty', 'Karan Oberoi', 'karan@heritagerealty.in', '+91 98212 23456', 'https://www.heritagerealty.in', 'Real Estate', 'AI Chatbot', 90, 'very_high', 'Mumbai'],
            ['BlueWave Media Agency', 'Divya Nair', 'divya@bluewavemedia.co', '+91 98213 34567', 'https://www.bluewavemedia.co', 'Marketing Agency', 'Website Development', 79, 'high', 'Chennai'],
            ['NextGen Tutoring Hub', 'Prof. Arvind Rao', 'arvind@nextgentutors.edu', '+91 98214 45678', null, 'Education', 'AI Automation', 58, 'medium', 'Hyderabad'],
            ['Pulse Diagnostic Center', 'Dr. Neha Joshi', 'diagnostics@pulsehealth.in', '+91 98215 56789', 'https://www.pulsehealth.in', 'Healthcare Clinic', 'AI Chatbot', 86, 'very_high', 'Pune'],
        ];

        $createdLeads = [];

        foreach ($leadProfiles as $idx => $p) {
            $status = match ($idx % 5) {
                0 => 'new',
                1 => 'contacted',
                2 => 'interested',
                3 => 'follow_up',
                default => 'converted',
            };

            $lead = Lead::firstOrCreate(
                ['email' => $p[2]],
                [
                    'created_by' => $user->id,
                    'business_name' => $p[0],
                    'contact_name' => $p[1],
                    'phone' => $p[3],
                    'website' => $p[4],
                    'country' => 'India',
                    'city' => $p[9],
                    'category' => $p[5],
                    'service_interest' => $p[6],
                    'source' => 'Website',
                    'status' => $status,
                    'priority' => $p[7] >= 80 ? 'high' : 'medium',
                    'ai_score' => $p[7],
                    'ai_priority' => $p[8],
                    'ai_service' => $p[6],
                    'ai_summary' => "{$p[0]} evaluated for {$p[6]}. High opportunity for customer automation and modern booking workflows.",
                    'ai_analyzed_at' => now()->subDays(rand(1, 5)),
                    'last_outbound_at' => in_array($status, ['contacted', 'interested', 'converted', 'follow_up']) ? now()->subDays(rand(8, 20)) : null,
                ]
            );

            // Seed Enrichment
            LeadEnrichment::firstOrCreate(
                ['lead_id' => $lead->id],
                [
                    'description' => "Enriched data for {$lead->business_name} in {$lead->city}.",
                    'detected_services' => [$lead->service_interest, 'AI Automation'],
                    'website_summary' => $lead->website ? "Active website at {$lead->website}" : 'No public website detected',
                    'technology_info' => ['has_website' => !empty($lead->website)],
                    'opportunity_notes' => "High intent lead for {$lead->service_interest}.",
                    'enriched_at' => now()->subDays(rand(1, 4)),
                ]
            );

            $createdLeads[] = $lead;
        }

        // 6. Create 3 Sample Campaigns
        $camp1 = Campaign::firstOrCreate(
            ['name' => 'Mumbai Dental AI Chatbot Campaign', 'user_id' => $user->id],
            [
                'email_account_id' => $emailAccount->id,
                'description' => 'Automated appointment booking chatbot outreach to Mumbai dental clinics.',
                'service' => 'AI Chatbot',
                'status' => 'running',
                'daily_limit' => 50,
                'approval_required' => true,
                'filter_criteria' => ['city' => 'Mumbai', 'category' => 'Dental Clinic', 'min_ai_score' => 70],
                'follow_up_days' => 3,
                'start_at' => now()->subDays(2),
            ]
        );

        CampaignTemplate::firstOrCreate(
            ['campaign_id' => $camp1->id],
            [
                'subject' => 'AI Appointment Automation for {{business_name}}',
                'body' => "Hi {{contact_name}},\n\nI noticed {{business_name}} is a leading dental practice in {{city}}. We implement 24/7 AI chatbots that automatically book patient appointments directly into your calendar.\n\nWould you have 10 minutes this Thursday for a quick demo?\n\nBest regards,\nDemo Outreach Team",
            ]
        );

        $camp2 = Campaign::firstOrCreate(
            ['name' => 'Bangalore Startups CRM Outreach', 'user_id' => $user->id],
            [
                'email_account_id' => $emailAccount->id,
                'description' => 'Custom CRM development outreach for SaaS teams.',
                'service' => 'CRM Development',
                'status' => 'draft',
                'daily_limit' => 40,
                'approval_required' => true,
                'filter_criteria' => ['city' => 'Bangalore'],
                'follow_up_days' => 4,
            ]
        );

        CampaignTemplate::firstOrCreate(
            ['campaign_id' => $camp2->id],
            [
                'subject' => 'Scaling your sales pipeline at {{business_name}}',
                'body' => "Hi {{contact_name}},\n\nWe specialize in custom CRM workflows for growing tech companies in {{city}}.\n\nLet's connect this week.\n\nBest regards,\nDemo Team",
            ]
        );

        // 7. Seed Campaign Messages
        foreach (array_slice($createdLeads, 0, 5) as $i => $cl) {
            $msgStatus = match ($i) {
                0 => 'sent',
                1 => 'replied',
                2 => 'approved',
                default => 'pending_approval',
            };

            CampaignMessage::firstOrCreate(
                ['campaign_id' => $camp1->id, 'lead_id' => $cl->id],
                [
                    'email_account_id' => $emailAccount->id,
                    'subject' => "AI Appointment Automation for {$cl->business_name}",
                    'body' => "Hi {$cl->contact_name},\n\nWe noticed {$cl->business_name} in {$cl->city} and would love to help you automate patient appointments.\n\nBest regards,\nOutreach Team",
                    'status' => $msgStatus,
                    'approved_at' => in_array($msgStatus, ['approved', 'sent', 'replied']) ? now()->subHours(6) : null,
                    'sent_at' => in_array($msgStatus, ['sent', 'replied']) ? now()->subHours(3) : null,
                ]
            );

            // Follow-up
            if (in_array($msgStatus, ['sent', 'replied'])) {
                FollowUp::firstOrCreate(
                    ['lead_id' => $cl->id, 'user_id' => $user->id],
                    [
                        'campaign_id' => $camp1->id,
                        'scheduled_at' => now()->addDays(3),
                        'status' => 'pending',
                        'notes' => "Follow-up regarding AI Chatbot pitch to {$cl->contact_name}",
                    ]
                );
            }
        }

        // 8. Seed Activity Logs
        ActivityLogger::log($user->id, 'lead_created', 'Discovered 15 dental clinic leads in Mumbai', null, $camp1->id);
        ActivityLogger::log($user->id, 'ai_analyzed', 'Gemini AI qualified 15 leads with avg score 84/100', null, $camp1->id);
        ActivityLogger::log($user->id, 'email_approved', 'Approved 3 outbound campaign drafts for sending', null, $camp1->id);
        ActivityLogger::log($user->id, 'email_sent', 'Sent outreach message to Apex Dental Care', $createdLeads[0]->id, $camp1->id);
        ActivityLogger::log($user->id, 'email_replied', 'Received positive inquiry from Smile Studio Bandra', $createdLeads[1]->id, $camp1->id);

        // 9. Seed Part 2 Emails and Conversations
        $this->call(EmailSeeder::class);
    }
}
