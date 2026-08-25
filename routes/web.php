<?php

use App\Http\Controllers\AIAutomationController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmailAccountController;
use App\Http\Controllers\FollowUpController;
use App\Http\Controllers\GoogleOAuthController;
use App\Http\Controllers\InboxController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SourceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Leads Custom Routes (must precede resource to avoid route clashes)
    Route::get('/leads/export', [LeadController::class, 'exportCsv'])->name('leads.export');
    Route::post('/leads/import-csv', [LeadController::class, 'importCsv'])->name('leads.importCsv');
    Route::post('/leads/bulk-action', [LeadController::class, 'bulkAction'])->name('leads.bulkAction');
    Route::post('/leads/{lead}/mark-contacted', [LeadController::class, 'markContacted'])->name('leads.markContacted');
    Route::post('/leads/{lead}/schedule-follow-up', [LeadController::class, 'scheduleFollowUp'])->name('leads.scheduleFollowUp');
    Route::post('/leads/{lead}/analyze-ai', [LeadController::class, 'analyzeWithAi'])->name('leads.analyzeAi');
    Route::post('/leads/{lead}/enrich', [LeadController::class, 'enrich'])->name('leads.enrich');
    Route::resource('leads', LeadController::class);

    // Sources & Business Discovery
    Route::post('/sources/{source}/run-search', [SourceController::class, 'runSearch'])->name('sources.runSearch');
    Route::resource('sources', SourceController::class);

    // Campaigns & Outreach
    Route::post('/campaigns/{campaign}/start', [CampaignController::class, 'start'])->name('campaigns.start');
    Route::post('/campaigns/{campaign}/pause', [CampaignController::class, 'pause'])->name('campaigns.pause');
    Route::post('/campaigns/{campaign}/stop', [CampaignController::class, 'stop'])->name('campaigns.stop');
    Route::post('/campaigns/{campaign}/generate-drafts', [CampaignController::class, 'generateDrafts'])->name('campaigns.generateDrafts');
    Route::post('/campaigns/{campaign}/approve-all', [CampaignController::class, 'approveAll'])->name('campaigns.approveAll');
    Route::post('/campaigns/{campaign}/send-now', [CampaignController::class, 'sendNow'])->name('campaigns.sendNow');
    Route::post('/campaign-messages/{message}/approve', [CampaignController::class, 'approveMessage'])->name('campaign-messages.approve');
    Route::resource('campaigns', CampaignController::class);

    // Follow-ups
    Route::get('/follow-ups', [FollowUpController::class, 'index'])->name('follow-ups.index');
    Route::post('/follow-ups', [FollowUpController::class, 'store'])->name('follow-ups.store');
    Route::post('/follow-ups/{followUp}/complete', [FollowUpController::class, 'complete'])->name('follow-ups.complete');
    Route::post('/follow-ups/{followUp}/cancel', [FollowUpController::class, 'cancel'])->name('follow-ups.cancel');

    // AI Automation Dashboard
    Route::get('/ai-automation', [AIAutomationController::class, 'index'])->name('ai-automation.index');
    Route::post('/ai-automation/batch-qualify', [AIAutomationController::class, 'batchQualify'])->name('ai-automation.batchQualify');

    // Analytics
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Email Accounts
    Route::get('/email-accounts', [EmailAccountController::class, 'index'])->name('email-accounts.index');
    Route::get('/email-accounts/google/redirect', [GoogleOAuthController::class, 'redirect'])->name('email-accounts.google.redirect');
    Route::get('/email-accounts/google/callback', [GoogleOAuthController::class, 'callback'])->name('email-accounts.google.callback');
    Route::get('/email-accounts/{emailAccount}', [EmailAccountController::class, 'show'])->name('email-accounts.show');
    Route::post('/email-accounts/{emailAccount}/sync', [EmailAccountController::class, 'sync'])->name('email-accounts.sync');
    Route::post('/email-accounts/{emailAccount}/disconnect', [GoogleOAuthController::class, 'disconnect'])->name('email-accounts.disconnect');

    // Inbox
    Route::get('/inbox', [InboxController::class, 'index'])->name('inbox.index');
    Route::get('/inbox/{conversation}', [InboxController::class, 'show'])->name('inbox.show');
    Route::post('/inbox/{conversation}/reply', [InboxController::class, 'reply'])->name('inbox.reply');
    Route::post('/inbox/{conversation}/mark-read', [InboxController::class, 'markAsRead'])->name('inbox.markAsRead');
    Route::post('/inbox/{conversation}/status', [InboxController::class, 'updateStatus'])->name('inbox.updateStatus');
    Route::post('/inbox/{conversation}/link-lead', [InboxController::class, 'linkLead'])->name('inbox.linkLead');
});

require __DIR__.'/auth.php';
