<?php

use App\Jobs\ProcessAutomatedFollowUps;
use App\Jobs\ProcessCampaign;
use App\Jobs\SyncGmailAccount;
use App\Models\Campaign;
use App\Models\EmailAccount;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('gmail:sync {account_id?}', function (?int $account_id = null) {
    $query = EmailAccount::where('status', 'connected');
    if ($account_id) {
        $query->where('id', $account_id);
    }

    $accounts = $query->get();
    $this->info("Dispatching sync jobs for {$accounts->count()} email account(s)...");

    foreach ($accounts as $account) {
        SyncGmailAccount::dispatch($account);
        $this->line(" - Dispatched sync for {$account->email}");
    }
})->purpose('Sync connected Gmail accounts');

Artisan::command('campaigns:process', function () {
    $campaigns = Campaign::where('status', 'running')->get();
    $this->info("Processing {$campaigns->count()} running campaign(s)...");

    foreach ($campaigns as $campaign) {
        ProcessCampaign::dispatch($campaign);
        $this->line(" - Dispatched processor for campaign: {$campaign->name}");
    }
})->purpose('Process and dispatch active campaigns');

Artisan::command('followups:process', function () {
    $this->info('Processing automated follow-ups...');
    ProcessAutomatedFollowUps::dispatch();
    $this->info('Follow-ups processor dispatched.');
})->purpose('Process due follow-ups');

Schedule::command('gmail:sync')->everyTenMinutes();
Schedule::command('campaigns:process')->everyFifteenMinutes();
Schedule::command('followups:process')->hourly();
