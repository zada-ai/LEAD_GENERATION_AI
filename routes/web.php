<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmailAccountController;
use App\Http\Controllers\GoogleOAuthController;
use App\Http\Controllers\InboxController;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Leads
    Route::resource('leads', LeadController::class);
    Route::post('/leads/{lead}/mark-contacted', [LeadController::class, 'markContacted'])->name('leads.markContacted');
    Route::post('/leads/{lead}/schedule-follow-up', [LeadController::class, 'scheduleFollowUp'])->name('leads.scheduleFollowUp');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Email Accounts
    Route::get('/email-accounts', [EmailAccountController::class, 'index'])->name('email-accounts.index');
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

// Google OAuth routes
Route::middleware('auth')->group(function () {
    Route::get('/email-accounts/google/redirect', [GoogleOAuthController::class, 'redirect'])->name('email-accounts.google.redirect');
    Route::get('/email-accounts/google/callback', [GoogleOAuthController::class, 'callback'])->name('email-accounts.google.callback');
});
});

require __DIR__.'/auth.php';
