<x-app-layout :title="'Lead Details'">
    <div class="max-w-6xl space-y-6">
        <!-- Header with Actions -->
        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-3xl font-bold text-gray-900">{{ $lead->business_name }}</h1>
                    @if($lead->isSuppressed())
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800 border border-red-200">
                            🚫 Suppressed / Do Not Contact
                        </span>
                    @endif
                </div>
                <p class="text-gray-600 mt-1">{{ $lead->contact_name }} • {{ $lead->category }}</p>
            </div>
            <div class="flex gap-2 flex-wrap">
                <form action="{{ route('leads.analyzeAi', $lead) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm font-semibold shadow-sm transition">
                        🤖 {{ $lead->ai_score !== null ? 'Regenerate AI Analysis' : 'Analyze with Gemini AI' }}
                    </button>
                </form>
                <x-link-button type="primary" href="{{ route('leads.edit', $lead) }}">
                    ✏️ Edit
                </x-link-button>
                <x-link-button type="secondary" href="{{ route('leads.index') }}">
                    ← Back
                </x-link-button>
            </div>
        </div>

        <!-- AI Qualification & Score Banner -->
        @if($lead->ai_score !== null)
            <div class="bg-gradient-to-r from-purple-900 to-indigo-900 rounded-2xl p-6 text-white shadow-md">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <div class="flex items-center gap-3">
                            <span class="text-2xl">🤖</span>
                            <h2 class="text-xl font-bold">Gemini AI Lead Qualification</h2>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-white/20 text-white uppercase tracking-wider">
                                {{ $lead->ai_priority }} Priority
                            </span>
                        </div>
                        <p class="text-purple-200 text-sm mt-2 max-w-2xl whitespace-pre-wrap">{{ $lead->ai_summary }}</p>
                        <div class="mt-4 flex flex-wrap gap-4 text-xs">
                            <span class="bg-white/10 px-3 py-1.5 rounded-lg">🎯 Best Service: <strong class="text-white">{{ $lead->ai_service }}</strong></span>
                            <span class="bg-white/10 px-3 py-1.5 rounded-lg">📅 Evaluated: {{ $lead->ai_analyzed_at?->diffForHumans() }}</span>
                        </div>
                    </div>
                    <div class="text-center bg-white/10 px-6 py-4 rounded-xl border border-white/10 flex-shrink-0">
                        <span class="text-xs uppercase tracking-wider text-purple-200 font-semibold">AI Match Score</span>
                        <div class="text-4xl font-extrabold text-emerald-400 mt-1">{{ $lead->ai_score }}<span class="text-lg text-purple-200 font-normal">/100</span></div>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Business & Contact Profile -->
                <x-card title="🏢 Business Profile">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                        <div>
                            <p class="text-xs text-gray-500 font-semibold uppercase">Business Name</p>
                            <p class="text-base font-bold text-gray-900 mt-1">{{ $lead->business_name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-semibold uppercase">Category</p>
                            <p class="text-base font-medium text-gray-900 mt-1">{{ $lead->category ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-semibold uppercase">Contact Person</p>
                            <p class="text-base font-medium text-gray-900 mt-1">{{ $lead->contact_name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-semibold uppercase">Email</p>
                            <a href="mailto:{{ $lead->email }}" class="text-base font-semibold text-blue-600 hover:underline mt-1 block truncate">
                                {{ $lead->email }}
                            </a>
                        </div>
                        @if ($lead->phone)
                            <div>
                                <p class="text-xs text-gray-500 font-semibold uppercase">Phone</p>
                                <a href="tel:{{ $lead->phone }}" class="text-base font-medium text-blue-600 hover:underline mt-1 block">
                                    {{ $lead->phone }}
                                </a>
                            </div>
                        @endif
                        @if ($lead->website)
                            <div>
                                <p class="text-xs text-gray-500 font-semibold uppercase">Website</p>
                                <a href="{{ $lead->website }}" target="_blank" class="text-base font-medium text-blue-600 hover:underline mt-1 block truncate">
                                    {{ $lead->website }} ↗
                                </a>
                            </div>
                        @endif
                        <div>
                            <p class="text-xs text-gray-500 font-semibold uppercase">Location</p>
                            <p class="text-base font-medium text-gray-900 mt-1">
                                {{ implode(', ', array_filter([$lead->area, $lead->city, $lead->state, $lead->country])) ?: 'N/A' }}
                            </p>
                        </div>
                    </div>
                </x-card>

                <!-- Lead Enrichment Details -->
                <x-card title="🔍 Lead Enrichment & Technology Intelligence">
                    @if($lead->enrichment)
                        <div class="space-y-4 text-sm">
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase">Website Summary</p>
                                <p class="text-gray-800 mt-1">{{ $lead->enrichment->website_summary ?? 'N/A' }}</p>
                            </div>

                            @if(!empty($lead->enrichment->detected_services))
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase mb-1.5">Detected Potential Services</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($lead->enrichment->detected_services as $service)
                                            <span class="px-2.5 py-1 rounded-md text-xs font-semibold bg-blue-50 text-blue-700">
                                                {{ $service }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase">Opportunity Notes</p>
                                <p class="text-gray-800 mt-1">{{ $lead->enrichment->opportunity_notes ?? 'N/A' }}</p>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center justify-between py-2 text-sm">
                            <p class="text-gray-500">No enrichment record found.</p>
                            <form action="{{ route('leads.enrich', $lead) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-lg text-xs font-semibold">
                                    Run Enrichment
                                </button>
                            </form>
                        </div>
                    @endif
                </x-card>

                <!-- Outbound Campaign History -->
                <x-card title="🚀 Outreach Campaign History">
                    @if($lead->campaignMessages->isEmpty())
                        <p class="text-sm text-gray-500 py-2">This lead has not been messaged in any outreach campaign yet.</p>
                    @else
                        <div class="space-y-3">
                            @foreach($lead->campaignMessages as $cm)
                                <div class="p-3.5 rounded-lg border border-gray-100 bg-gray-50/50 flex items-center justify-between gap-3 text-sm">
                                    <div>
                                        <h4 class="font-bold text-gray-900">{{ $cm->campaign?->name ?? 'Campaign' }}</h4>
                                        <p class="text-xs text-gray-500">Subject: {{ $cm->subject }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $cm->status_badge_color }}">
                                            {{ ucfirst(str_replace('_', ' ', $cm->status)) }}
                                        </span>
                                        <p class="text-[11px] text-gray-400 mt-1">{{ $cm->sent_at ? $cm->sent_at->format('M d, Y H:i') : 'Draft' }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </x-card>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Sales Information -->
                <x-card title="📊 Sales Status">
                    <div class="space-y-4 text-sm">
                        <div>
                            <p class="text-xs text-gray-500 font-semibold uppercase">Pipeline Status</p>
                            <div class="mt-1.5">
                                <x-badge :status="$lead->status">
                                    {{ ucfirst(str_replace('_', ' ', $lead->status)) }}
                                </x-badge>
                            </div>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-semibold uppercase">Priority</p>
                            <div class="mt-1.5">
                                <x-priority-badge :priority="$lead->priority">
                                    {{ ucfirst($lead->priority) }}
                                </x-priority-badge>
                            </div>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-semibold uppercase">Target Service</p>
                            <p class="text-sm font-semibold text-gray-900 mt-1">{{ $lead->service_interest }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-semibold uppercase">Source</p>
                            <p class="text-sm font-semibold text-gray-900 mt-1">{{ $lead->source }}</p>
                        </div>
                    </div>
                </x-card>

                <!-- Quick Actions -->
                <x-card title="⚡ Quick Actions">
                    <div class="space-y-3">
                        <form method="POST" action="{{ route('leads.markContacted', $lead) }}">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-semibold text-xs shadow-sm">
                                📞 Mark Contacted
                            </button>
                        </form>

                        <form method="POST" action="{{ route('leads.scheduleFollowUp', $lead) }}" class="space-y-2">
                            @csrf
                            <label class="block text-xs font-semibold text-gray-600">Schedule Follow-up</label>
                            <input
                                type="date"
                                name="next_follow_up_at"
                                value="{{ date('Y-m-d', strtotime('+3 days')) }}"
                                required
                                class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-xs"
                            />
                            <button type="submit" class="w-full px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 font-semibold text-xs shadow-sm">
                                ⏰ Save Follow-up
                            </button>
                        </form>

                        <form method="POST" action="{{ route('leads.destroy', $lead) }}" onsubmit="return confirm('Delete this lead?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-4 py-2 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg font-semibold text-xs">
                                🗑️ Delete Lead
                            </button>
                        </form>
                    </div>
                </x-card>
            </div>
        </div>
    </div>
</x-app-layout>
