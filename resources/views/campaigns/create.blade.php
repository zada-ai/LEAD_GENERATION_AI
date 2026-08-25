<x-app-layout>
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header -->
        <div>
            <a href="{{ route('campaigns.index') }}" class="text-sm font-semibold text-blue-600 hover:underline">← Back to Campaigns</a>
            <h1 class="text-3xl font-bold text-gray-900 mt-1">Create Outreach Campaign</h1>
            <p class="text-gray-600 text-sm mt-1">Configure campaign parameters, AI personalization, and delivery rules</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
            <form action="{{ route('campaigns.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Basic Campaign Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Campaign Name *</label>
                        <input type="text" name="name" required placeholder="e.g. Q3 AI Automation Outreach - Dental" value="{{ old('name') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Target Service *</label>
                        <select name="service" required class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="AI Automation">AI Automation</option>
                            <option value="AI Chatbot">AI Chatbot</option>
                            <option value="Website Development">Website Development</option>
                            <option value="CRM Development">CRM Development</option>
                            <option value="App Development">App Development</option>
                            <option value="SEO">SEO</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Sending Email Account</label>
                        <select name="email_account_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Default Connected Account</option>
                            @foreach($emailAccounts as $account)
                                <option value="{{ $account->id }}">{{ $account->email }} ({{ $account->display_name ?? 'Gmail' }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Limits & Controls -->
                <div class="pt-4 border-t border-gray-100 grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Daily Limit (Emails/day) *</label>
                        <input type="number" name="daily_limit" min="1" max="500" value="{{ old('daily_limit', 50) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <span class="text-[11px] text-gray-400">Strict provider safety limit</span>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Follow-up Delay (Days) *</label>
                        <input type="number" name="follow_up_days" min="1" max="30" value="{{ old('follow_up_days', 3) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <span class="text-[11px] text-gray-400">Auto-schedules follow-up</span>
                    </div>

                    <div class="flex items-center pt-5">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="approval_required" value="1" checked class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                            <span class="text-xs font-semibold text-gray-700">Require Human Approval</span>
                        </label>
                    </div>
                </div>

                <!-- Lead Filter Criteria -->
                <div class="pt-4 border-t border-gray-100">
                    <h3 class="text-md font-bold text-gray-900 mb-1">Target Lead Filters</h3>
                    <p class="text-xs text-gray-500 mb-4">Leave fields blank to target all eligible non-suppressed leads</p>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Category</label>
                            <input type="text" name="filter_criteria[category]" placeholder="e.g. Dental Clinic" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">City</label>
                            <input type="text" name="filter_criteria[city]" placeholder="e.g. Mumbai" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Minimum AI Score (0-100)</label>
                            <input type="number" name="filter_criteria[min_ai_score]" min="0" max="100" placeholder="e.g. 70" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        </div>
                    </div>
                </div>

                <!-- Email Template Base -->
                <div class="pt-4 border-t border-gray-100 space-y-4">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <h3 class="text-md font-bold text-gray-900">Email Template Base</h3>
                            <span class="text-xs text-blue-600">Gemini AI will personalize drafts from this base</span>
                        </div>
                        <p class="text-xs text-gray-500 mb-3">
                            Available variables: <code class="bg-gray-100 px-1 py-0.5 rounded">@{{business_name}}</code>, 
                            <code class="bg-gray-100 px-1 py-0.5 rounded">@{{contact_name}}</code>, 
                            <code class="bg-gray-100 px-1 py-0.5 rounded">@{{city}}</code>, 
                            <code class="bg-gray-100 px-1 py-0.5 rounded">@{{service}}</code>
                        </p>

                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Subject Template *</label>
                                <input type="text" name="template_subject" required value="{{ old('template_subject', 'Modernizing customer automation for {{business_name}}') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Body Template *</label>
                                <textarea name="template_body" rows="6" required class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm font-sans">{{ old('template_body', "Hi {{contact_name}},\n\nI noticed {{business_name}} is leading the {{category}} sector in {{city}}. We specialize in {{service}} to help businesses automate appointment bookings and scale revenue.\n\nWould you be open to a quick 10-minute demo this week?\n\nBest regards,\nOur Sales Team") }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                    <a href="{{ route('campaigns.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 text-sm">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 shadow text-sm">
                        Create Campaign
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
