<x-app-layout>
    <div class="max-w-7xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Campaigns & Outreach</h1>
                <p class="text-gray-600 mt-1">Manage AI personalized outreach campaigns and controlled delivery</p>
            </div>
            <a href="{{ route('campaigns.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 rounded-lg text-sm font-semibold text-white hover:bg-blue-700 shadow transition">
                <span>➕</span> New Campaign
            </a>
        </div>

        @if($campaigns->isEmpty())
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                <span class="text-4xl">🚀</span>
                <h3 class="text-lg font-bold text-gray-900 mt-3">No Campaigns Found</h3>
                <p class="text-gray-500 text-sm mt-1 max-w-md mx-auto">Create targeted outreach campaigns with Gemini AI personalization, human approval, and controlled daily limits.</p>
                <a href="{{ route('campaigns.create') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700">
                    Create First Campaign
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach($campaigns as $campaign)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col lg:flex-row lg:items-center justify-between gap-6 hover:shadow transition">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-3">
                                <h3 class="text-xl font-bold text-gray-900 truncate">{{ $campaign->name }}</h3>
                                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full {{ $campaign->status_badge_color }}">
                                    {{ ucfirst($campaign->status) }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1 line-clamp-1">{{ $campaign->description ?? 'No description' }}</p>
                            <div class="flex flex-wrap items-center gap-4 text-xs text-gray-500 mt-3">
                                <span>Service: <strong class="text-gray-800">{{ $campaign->service }}</strong></span>
                                <span>•</span>
                                <span>Daily Limit: <strong class="text-gray-800">{{ $campaign->daily_limit }} emails/day</strong></span>
                                <span>•</span>
                                <span>Approval: <strong class="text-gray-800">{{ $campaign->approval_required ? 'Required' : 'Automatic' }}</strong></span>
                            </div>
                        </div>

                        <!-- Metric counters -->
                        <div class="flex items-center gap-6 text-center border-t lg:border-t-0 lg:border-l border-gray-100 pt-4 lg:pt-0 lg:pl-6">
                            <div>
                                <p class="text-xs text-gray-500">Drafts</p>
                                <p class="text-lg font-bold text-gray-900">{{ $campaign->total_messages }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Pending</p>
                                <p class="text-lg font-bold text-amber-600">{{ $campaign->pending_messages }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Sent</p>
                                <p class="text-lg font-bold text-blue-600">{{ $campaign->sent_messages }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Replies</p>
                                <p class="text-lg font-bold text-emerald-600">{{ $campaign->replied_messages }}</p>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center gap-2">
                            <a href="{{ route('campaigns.show', $campaign) }}" class="px-4 py-2 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-lg text-xs font-semibold">
                                View Details & Messages →
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $campaigns->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
