<x-app-layout>
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header -->
        <div>
            <h1 class="text-3xl font-bold text-gray-900">CRM & AI Settings</h1>
            <p class="text-gray-600 mt-1">Configure your default outreach parameters, Gemini AI preferences, and defaults</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
            <form action="{{ route('settings.update') }}" method="POST" class="space-y-8">
                @csrf

                <!-- Profile Section -->
                <div>
                    <h3 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b border-gray-100">👤 User Profile</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Your Name</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Email Address</label>
                            <input type="email" value="{{ $user->email }}" disabled class="w-full px-4 py-2 border border-gray-200 bg-gray-50 rounded-lg text-sm text-gray-500 cursor-not-allowed">
                        </div>
                    </div>
                </div>

                <!-- AI Automation Section -->
                <div>
                    <h3 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b border-gray-100">🤖 Google Gemini AI Configuration</h3>
                    <div class="space-y-4">
                        <div class="max-w-md">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Gemini AI Model</label>
                            <select name="gemini_model" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm">
                                <option value="gemini-2.5-flash" {{ ($setting->gemini_model ?? '') === 'gemini-2.5-flash' ? 'selected' : '' }}>Gemini 2.5 Flash (Fast, Recommended)</option>
                                <option value="gemini-2.5-pro" {{ ($setting->gemini_model ?? '') === 'gemini-2.5-pro' ? 'selected' : '' }}>Gemini 2.5 Pro (Advanced Reasoning)</option>
                                <option value="gemini-1.5-flash" {{ ($setting->gemini_model ?? '') === 'gemini-1.5-flash' ? 'selected' : '' }}>Gemini 1.5 Flash</option>
                                <option value="gemini-1.5-pro" {{ ($setting->gemini_model ?? '') === 'gemini-1.5-pro' ? 'selected' : '' }}>Gemini 1.5 Pro</option>
                            </select>
                        </div>

                        <div class="space-y-2 pt-2">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="enable_ai_analysis" value="1" {{ ($setting->enable_ai_analysis ?? true) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 rounded">
                                <span class="text-sm font-medium text-gray-700">Enable Automated Lead Qualification with Gemini AI</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="enable_ai_personalization" value="1" {{ ($setting->enable_ai_personalization ?? true) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 rounded">
                                <span class="text-sm font-medium text-gray-700">Enable AI Personalized Outreach Draft Generation</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Outreach Defaults -->
                <div>
                    <h3 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b border-gray-100">🚀 Outreach & Sending Controls</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Default Daily Sending Limit</label>
                            <input type="number" name="default_daily_limit" min="1" max="500" value="{{ old('default_daily_limit', $setting->default_daily_limit ?? 50) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Default Follow-up Delay (Days)</label>
                            <input type="number" name="default_follow_up_days" min="1" max="30" value="{{ old('default_follow_up_days', $setting->default_follow_up_days ?? 3) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Default Gmail Account</label>
                            <select name="default_email_account_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                <option value="">Auto-select connected account</option>
                                @foreach($emailAccounts as $account)
                                    <option value="{{ $account->id }}" {{ ($setting->default_email_account_id ?? null) == $account->id ? 'selected' : '' }}>{{ $account->email }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="pt-4">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="default_approval_required" value="1" {{ ($setting->default_approval_required ?? true) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 rounded">
                            <span class="text-sm font-medium text-gray-700">Require Human Approval on all outbound campaign drafts by default</span>
                        </label>
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-100 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow text-sm">
                        Save Preferences
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
