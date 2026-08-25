<?php

namespace App\Http\Controllers;

use App\Models\EmailAccount;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        /** @var User $user */
        $user = auth()->user();
        $setting = $user->getOrCreateSetting();

        $emailAccounts = EmailAccount::where('user_id', $user->id)
            ->where('status', 'connected')
            ->get();

        return view('settings.index', compact('user', 'setting', 'emailAccounts'));
    }

    public function update(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'gemini_model' => 'required|string|in:gemini-2.5-flash,gemini-2.5-pro,gemini-1.5-flash,gemini-1.5-pro',
            'enable_ai_analysis' => 'nullable|boolean',
            'enable_ai_personalization' => 'nullable|boolean',
            'default_daily_limit' => 'required|integer|min:1|max:500',
            'default_follow_up_days' => 'required|integer|min:1|max:30',
            'default_approval_required' => 'nullable|boolean',
            'default_email_account_id' => 'nullable|exists:email_accounts,id',
        ]);

        $user->update([
            'name' => $validated['name'],
        ]);

        $setting = $user->getOrCreateSetting();
        $setting->update([
            'gemini_model' => $validated['gemini_model'],
            'enable_ai_analysis' => (bool)($validated['enable_ai_analysis'] ?? false),
            'enable_ai_personalization' => (bool)($validated['enable_ai_personalization'] ?? false),
            'default_daily_limit' => $validated['default_daily_limit'],
            'default_follow_up_days' => $validated['default_follow_up_days'],
            'default_approval_required' => (bool)($validated['default_approval_required'] ?? false),
            'default_email_account_id' => $validated['default_email_account_id'] ?? null,
        ]);

        return back()->with('success', 'Settings updated successfully.');
    }
}
