<x-app-layout>
    <div class="max-w-7xl mx-auto space-y-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
                <p class="text-gray-600 mt-1">Real-time overview of your lead pipeline, campaigns, and AI automation</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('sources.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm transition">
                    <span>🔍</span> Discover Leads
                </a>
                <a href="{{ route('campaigns.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 rounded-lg text-sm font-semibold text-white hover:bg-blue-700 shadow transition">
                    <span>🚀</span> New Campaign
                </a>
            </div>
        </div>

        <!-- Key Metrics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- Total Leads -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Leads</p>
                        <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_leads']) }}</h3>
                        <p class="text-xs text-blue-600 font-medium mt-1">{{ $stats['new_leads'] }} new in pipeline</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xl font-bold">
                        👥
                    </div>
                </div>
            </div>

            <!-- AI Qualified -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">AI Qualified (70%+)</p>
                        <h3 class="text-3xl font-bold text-emerald-600 mt-1">{{ number_format($stats['qualified_leads']) }}</h3>
                        <p class="text-xs text-gray-500 mt-1">High conversion potential</p>
                    </div>
                    <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-xl font-bold">
                        🤖
                    </div>
                </div>
            </div>

            <!-- Active Campaigns -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Active Campaigns</p>
                        <h3 class="text-3xl font-bold text-purple-600 mt-1">{{ number_format($stats['active_campaigns']) }}</h3>
                        <p class="text-xs text-gray-500 mt-1">{{ $stats['emails_sent_today'] }} emails sent today</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center text-xl font-bold">
                        🚀
                    </div>
                </div>
            </div>

            <!-- Follow-ups Due -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Follow-ups Due</p>
                        <h3 class="text-3xl font-bold text-amber-600 mt-1">{{ number_format($stats['follow_ups_due']) }}</h3>
                        <p class="text-xs text-gray-500 mt-1">{{ $stats['open_conversations'] }} active conversations</p>
                    </div>
                    <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center text-xl font-bold">
                        ⏰
                    </div>
                </div>
            </div>
        </div>

        <!-- Secondary Overview Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Active Campaigns Quick View -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-900">Campaigns Overview</h2>
                    <a href="{{ route('campaigns.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">View All →</a>
                </div>

                @if($activeCampaigns->isEmpty())
                    <div class="text-center py-8 text-gray-500">
                        <p>No active campaigns running right now.</p>
                        <a href="{{ route('campaigns.create') }}" class="mt-2 inline-block text-sm text-blue-600 font-semibold hover:underline">Create your first outreach campaign</a>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($activeCampaigns as $campaign)
                            <div class="p-4 rounded-lg border border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h4 class="font-bold text-gray-900">{{ $campaign->name }}</h4>
                                        <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ $campaign->status_badge_color }}">{{ ucfirst($campaign->status) }}</span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-0.5">Service: <span class="font-medium text-gray-700">{{ $campaign->service }}</span> • Daily Limit: {{ $campaign->daily_limit }}</p>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="text-right">
                                        <p class="text-xs text-gray-500">Sent / Total</p>
                                        <p class="text-sm font-bold text-gray-900">{{ $campaign->sent_messages }} / {{ $campaign->total_messages }}</p>
                                    </div>
                                    <a href="{{ route('campaigns.show', $campaign) }}" class="px-3 py-1.5 text-xs font-semibold bg-white border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100">
                                        Manage
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Pipeline Status Distribution -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Pipeline Distribution</h2>
                <div class="space-y-3">
                    @php
                        $total = max(1, $stats['total_leads']);
                    @endphp
                    <div>
                        <div class="flex justify-between text-xs font-medium text-gray-600 mb-1">
                            <span>New ({{ $statusDistribution['new'] }})</span>
                            <span>{{ round(($statusDistribution['new'] / $total) * 100) }}%</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full" style="width: {{ ($statusDistribution['new'] / $total) * 100 }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-xs font-medium text-gray-600 mb-1">
                            <span>Contacted ({{ $statusDistribution['contacted'] }})</span>
                            <span>{{ round(($statusDistribution['contacted'] / $total) * 100) }}%</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2">
                            <div class="bg-purple-500 h-2 rounded-full" style="width: {{ ($statusDistribution['contacted'] / $total) * 100 }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-xs font-medium text-gray-600 mb-1">
                            <span>Interested ({{ $statusDistribution['interested'] }})</span>
                            <span>{{ round(($statusDistribution['interested'] / $total) * 100) }}%</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full" style="width: {{ ($statusDistribution['interested'] / $total) * 100 }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-xs font-medium text-gray-600 mb-1">
                            <span>Converted ({{ $statusDistribution['converted'] }})</span>
                            <span>{{ round(($statusDistribution['converted'] / $total) * 100) }}%</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2">
                            <div class="bg-emerald-600 h-2 rounded-full" style="width: {{ ($statusDistribution['converted'] / $total) * 100 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity & Leads -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Activity Feed -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Recent CRM Activity</h2>
                @if($recentActivities->isEmpty())
                    <p class="text-sm text-gray-500 py-4">No activities logged yet.</p>
                @else
                    <div class="space-y-3">
                        @foreach($recentActivities as $activity)
                            <div class="flex items-start gap-3 p-2.5 rounded-lg hover:bg-gray-50 transition">
                                <span class="text-lg">{{ $activity->type_icon }}</span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-800 font-medium">{{ $activity->description }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $activity->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Recent Leads -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-900">Recent Leads</h2>
                    <a href="{{ route('leads.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">View All →</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead>
                            <tr class="text-left text-xs font-semibold text-gray-500 uppercase">
                                <th class="pb-2">Business</th>
                                <th class="pb-2">AI Score</th>
                                <th class="pb-2">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($recentLeads as $lead)
                                <tr>
                                    <td class="py-2.5">
                                        <a href="{{ route('leads.show', $lead) }}" class="font-semibold text-gray-900 hover:text-blue-600">
                                            {{ $lead->business_name }}
                                        </a>
                                        <p class="text-xs text-gray-500">{{ $lead->city ?? $lead->country }}</p>
                                    </td>
                                    <td class="py-2.5">
                                        @if($lead->ai_score !== null)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold border {{ $lead->ai_score_badge_color }}">
                                                {{ $lead->ai_score }}/100
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-400">Unscored</span>
                                        @endif
                                    </td>
                                    <td class="py-2.5">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $lead->status_color }}">
                                            {{ ucfirst(str_replace('_', ' ', $lead->status)) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
