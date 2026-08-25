<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add AI & Outbound fields to leads table
        Schema::table('leads', function (Blueprint $table) {
            $table->integer('ai_score')->nullable()->after('priority');
            $table->string('ai_priority')->nullable()->after('ai_score');
            $table->string('ai_service')->nullable()->after('ai_priority');
            $table->text('ai_summary')->nullable()->after('ai_service');
            $table->timestamp('ai_analyzed_at')->nullable()->after('ai_summary');
            $table->timestamp('last_outbound_at')->nullable()->after('ai_analyzed_at');

            $table->index('ai_score');
            $table->index('ai_priority');
            $table->index('ai_service');
        });

        // 2. Lead Sources table
        Schema::create('lead_sources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('type'); // website_search, manual_import, csv_import, google, etc.
            $table->json('config')->nullable();
            $table->integer('leads_count')->default(0);
            $table->enum('status', ['active', 'disabled'])->default('active');
            $table->timestamp('last_run_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('type');
            $table->index('status');
        });

        // 3. Lead Enrichments table
        Schema::create('lead_enrichments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->text('description')->nullable();
            $table->json('detected_services')->nullable();
            $table->text('website_summary')->nullable();
            $table->json('technology_info')->nullable();
            $table->text('opportunity_notes')->nullable();
            $table->timestamp('enriched_at')->nullable();
            $table->timestamps();

            $table->index('lead_id');
            $table->index('enriched_at');
        });

        // 4. Lead Suppressions table
        Schema::create('lead_suppressions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('email')->index();
            $table->enum('reason', ['unsubscribed', 'bounced', 'spam_complaint', 'manual_suppression', 'do_not_contact'])->default('manual_suppression');
            $table->string('source')->default('system');
            $table->timestamps();

            $table->index('user_id');
            $table->index('lead_id');
            $table->index('reason');
        });

        // 5. Campaigns table
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('email_account_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('service')->default('AI Automation');
            $table->enum('status', ['draft', 'scheduled', 'running', 'paused', 'completed'])->default('draft');
            $table->integer('daily_limit')->default(50);
            $table->boolean('approval_required')->default(true);
            $table->json('filter_criteria')->nullable();
            $table->integer('follow_up_days')->default(3);
            $table->timestamp('start_at')->nullable();
            $table->timestamp('end_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('email_account_id');
            $table->index('status');
            $table->index('service');
        });

        // 6. Campaign Templates table
        Schema::create('campaign_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->string('subject');
            $table->longText('body');
            $table->timestamps();

            $table->index('campaign_id');
        });

        // 7. Campaign Messages table
        Schema::create('campaign_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->foreignId('email_account_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('email_id')->nullable()->constrained()->nullOnDelete();
            $table->string('subject');
            $table->longText('body');
            $table->enum('status', ['draft', 'pending_approval', 'approved', 'queued', 'sent', 'failed', 'skipped', 'replied'])->default('draft');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index('campaign_id');
            $table->index('lead_id');
            $table->index('email_account_id');
            $table->index('status');
            $table->index('sent_at');
        });

        // 8. Follow-ups table
        Schema::create('follow_ups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->foreignId('campaign_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('campaign_message_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('scheduled_at');
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('lead_id');
            $table->index('campaign_id');
            $table->index('status');
            $table->index('scheduled_at');
        });

        // 9. Activity Logs table
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('campaign_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type'); // lead_created, ai_analyzed, email_generated, email_approved, email_sent, email_replied, etc.
            $table->string('description');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('lead_id');
            $table->index('campaign_id');
            $table->index('type');
            $table->index('created_at');
        });

        // 10. User Settings table
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('gemini_model')->default('gemini-2.5-flash');
            $table->boolean('enable_ai_analysis')->default(true);
            $table->boolean('enable_ai_personalization')->default(true);
            $table->integer('default_daily_limit')->default(50);
            $table->integer('default_follow_up_days')->default(3);
            $table->boolean('default_approval_required')->default(true);
            $table->foreignId('default_email_account_id')->nullable()->constrained('email_accounts')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_settings');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('follow_ups');
        Schema::dropIfExists('campaign_messages');
        Schema::dropIfExists('campaign_templates');
        Schema::dropIfExists('campaigns');
        Schema::dropIfExists('lead_suppressions');
        Schema::dropIfExists('lead_enrichments');
        Schema::dropIfExists('lead_sources');

        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex(['ai_score']);
            $table->dropIndex(['ai_priority']);
            $table->dropIndex(['ai_service']);
            $table->dropColumn([
                'ai_score',
                'ai_priority',
                'ai_service',
                'ai_summary',
                'ai_analyzed_at',
                'last_outbound_at',
            ]);
        });
    }
};
