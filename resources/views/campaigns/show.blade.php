<x-app-layout>
    <div class="max-w-7xl mx-auto space-y-6">
        <!-- Top Navigation -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <a href="{{ route('campaigns.index') }}" class="text-sm font-semibold text-blue-600 hover:underline">← Back to Campaigns</a>
                <div class="flex items-center gap-3 mt-1">
                    <h1 class="text-3xl font-bold text-gray-900">{{ $campaign->name }}</h1>
                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full {{ $campaign->status_badge_color }}">
                        {{ ucfirst($campaign->status) }}
                    </span>
                </div>
                <p class="text-xs text-gray-500 mt-1">Target Service: <span class="font-semibold text-gray-700">{{ $campaign->service }}</span> • Daily Limit: {{ $campaign->daily_limit }}</p>
            </div>

            <!-- Campaign Controls -->
            <div class="flex flex-wrap items-center gap-2">
                @if($campaign->status === 'draft' || $campaign->status === 'paused')
                    <form action="{{ route('campaigns.start', $campaign) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold text-xs rounded-lg shadow-sm">
                            ▶ Start Campaign
                        </button>
                    </form>
                @elseif($campaign->status === 'running')
                    <form action="{{ route('campaigns.pause', $campaign) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white font-semibold text-xs rounded-lg shadow-sm">
                            ⏸ Pause
                        </button>
                    </form>
                @endif

                <form action="{{ route('campaigns.generateDrafts', $campaign) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-semibold text-xs rounded-lg shadow-sm">
                        🤖 Generate AI Drafts
                    </button>
                </form>

                @if($stats['pending'] > 0)
                    <form action="{{ route('campaigns.approveAll', $campaign) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-lg shadow-sm">
                            ✅ Approve All ({{ $stats['pending'] }})
                        </button>
                    </form>
                @endif

                @if($stats['approved'] > 0)
                    <form action="{{ route('campaigns.sendNow', $campaign) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-lg shadow-sm">
                            📤 Send Batch
                        </button>
                    </form>
                @endif

                <form action="{{ route('campaigns.destroy', $campaign) }}" method="POST" onsubmit="return confirm('Delete this campaign?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-3 py-2 bg-red-50 hover:bg-red-100 text-red-600 font-semibold text-xs rounded-lg">
                        Delete
                    </button>
                </form>
            </div>
        </div>

        <!-- Metric Stat Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm text-center">
                <p class="text-xs text-gray-500 font-semibold uppercase">Total</p>
                <h4 class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total'] }}</h4>
            </div>
            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm text-center">
                <p class="text-xs text-gray-500 font-semibold uppercase">Pending Approval</p>
                <h4 class="text-2xl font-bold text-amber-600 mt-1">{{ $stats['pending'] }}</h4>
            </div>
            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm text-center">
                <p class="text-xs text-gray-500 font-semibold uppercase">Approved</p>
                <h4 class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['approved'] }}</h4>
            </div>
            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm text-center">
                <p class="text-xs text-gray-500 font-semibold uppercase">Sent</p>
                <h4 class="text-2xl font-bold text-emerald-600 mt-1">{{ $stats['sent'] }}</h4>
            </div>
            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm text-center">
                <p class="text-xs text-gray-500 font-semibold uppercase">Replies</p>
                <h4 class="text-2xl font-bold text-purple-600 mt-1">{{ $stats['replied'] }}</h4>
            </div>
            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm text-center">
                <p class="text-xs text-gray-500 font-semibold uppercase">Failed</p>
                <h4 class="text-2xl font-bold text-red-600 mt-1">{{ $stats['failed'] }}</h4>
            </div>
        </div>

        <!-- Campaign Messages Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900">Campaign Messages & Outbound Queue</h3>
                <span class="text-xs text-gray-500">Showing personalized drafts and delivery statuses</span>
            </div>

            @if($messages->isEmpty())
                <div class="p-12 text-center text-gray-500">
                    <p>No messages drafted yet.</p>
                    <form action="{{ route('campaigns.generateDrafts', $campaign) }}" method="POST" class="mt-3">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg text-xs font-semibold hover:bg-purple-700">
                            🤖 Generate AI Personalized Drafts Now
                        </button>
                    </form>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase text-left">
                            <tr>
                                <th class="px-6 py-3">Lead / Business</th>
                                <th class="px-6 py-3">Subject</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3">Sent / Approved At</th>
                                <th class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($messages as $msg)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="px-6 py-4">
                                        <a href="{{ route('leads.show', $msg->lead) }}" class="font-bold text-gray-900 hover:text-blue-600">
                                            {{ $msg->lead->business_name }}
                                        </a>
                                        <p class="text-xs text-gray-500">{{ $msg->lead->contact_name }} • {{ $msg->lead->email }}</p>
                                    </td>
                                    <td class="px-6 py-4 max-w-xs">
                                        <p class="font-medium text-gray-900 truncate">{{ $msg->subject }}</p>
                                        <p class="text-xs text-gray-500 truncate mt-0.5">{{ strip_tags($msg->body) }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $msg->status_badge_color }}">
                                            {{ ucfirst(str_replace('_', ' ', $msg->status)) }}
                                        </span>
                                        @if($msg->error_message)
                                            <p class="text-[11px] text-red-600 mt-1 max-w-xs truncate" title="{{ $msg->error_message }}">{{ $msg->error_message }}</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-xs text-gray-500">
                                        {{ $msg->sent_at ? $msg->sent_at->format('M d, H:i') : ($msg->approved_at ? 'Approved ' . $msg->approved_at->diffForHumans() : 'Draft') }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        @if($msg->status === 'pending_approval')
                                            <form action="{{ route('campaign-messages.approve', $msg) }}" method="POST" class="inline-block">
                                                @csrf
                                                <button type="submit" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-xs font-semibold">
                                                    Approve
                                                </button>
                                            </form>
                                        @elseif($msg->status === 'approved')
                                            <span class="text-xs text-blue-600 font-medium">Ready in queue</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-gray-100">
                    {{ $messages->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
