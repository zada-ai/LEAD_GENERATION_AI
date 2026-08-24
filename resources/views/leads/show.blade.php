<x-app-layout :title="'Lead Details'">
    <div class="max-w-4xl space-y-6">
        <!-- Header with Actions -->
        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $lead->business_name }}</h1>
                <p class="text-gray-600 mt-1">{{ $lead->contact_name }}</p>
            </div>
            <div class="flex gap-2 flex-wrap">
                <x-link-button type="primary" href="{{ route('leads.edit', $lead) }}">
                    ✏️ Edit
                </x-link-button>
                <x-link-button type="secondary" href="{{ route('leads.index') }}">
                    ← Back
                </x-link-button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Business Information -->
                <x-card title="Business Information">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm text-gray-600">Business Name</p>
                            <p class="text-lg font-semibold text-gray-900 mt-1">{{ $lead->business_name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Category</p>
                            <p class="text-lg font-semibold text-gray-900 mt-1">{{ $lead->category ?? 'N/A' }}</p>
                        </div>
                        @if ($lead->website)
                            <div>
                                <p class="text-sm text-gray-600">Website</p>
                                <a href="{{ $lead->website }}" target="_blank" class="text-lg font-semibold text-blue-600 hover:underline mt-1">
                                    Visit Website
                                </a>
                            </div>
                        @endif
                        @if ($lead->facebook_url)
                            <div>
                                <p class="text-sm text-gray-600">Facebook</p>
                                <a href="{{ $lead->facebook_url }}" target="_blank" class="text-lg font-semibold text-blue-600 hover:underline mt-1">
                                    Visit Page
                                </a>
                            </div>
                        @endif
                        @if ($lead->instagram_url)
                            <div>
                                <p class="text-sm text-gray-600">Instagram</p>
                                <a href="{{ $lead->instagram_url }}" target="_blank" class="text-lg font-semibold text-blue-600 hover:underline mt-1">
                                    Visit Profile
                                </a>
                            </div>
                        @endif
                        @if ($lead->linkedin_url)
                            <div>
                                <p class="text-sm text-gray-600">LinkedIn</p>
                                <a href="{{ $lead->linkedin_url }}" target="_blank" class="text-lg font-semibold text-blue-600 hover:underline mt-1">
                                    Visit Profile
                                </a>
                            </div>
                        @endif
                    </div>
                </x-card>

                <!-- Contact Information -->
                <x-card title="Contact Information">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm text-gray-600">Contact Person</p>
                            <p class="text-lg font-semibold text-gray-900 mt-1">{{ $lead->contact_name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Email</p>
                            <a href="mailto:{{ $lead->email }}" class="text-lg font-semibold text-blue-600 hover:underline mt-1">
                                {{ $lead->email }}
                            </a>
                        </div>
                        @if ($lead->phone)
                            <div>
                                <p class="text-sm text-gray-600">Phone</p>
                                <a href="tel:{{ $lead->phone }}" class="text-lg font-semibold text-blue-600 hover:underline mt-1">
                                    {{ $lead->phone }}
                                </a>
                            </div>
                        @endif
                    </div>
                </x-card>

                <!-- Location -->
                <x-card title="Location">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm text-gray-600">Country</p>
                            <p class="text-lg font-semibold text-gray-900 mt-1">{{ $lead->country ?? 'N/A' }}</p>
                        </div>
                        @if ($lead->state)
                            <div>
                                <p class="text-sm text-gray-600">State</p>
                                <p class="text-lg font-semibold text-gray-900 mt-1">{{ $lead->state }}</p>
                            </div>
                        @endif
                        @if ($lead->city)
                            <div>
                                <p class="text-sm text-gray-600">City</p>
                                <p class="text-lg font-semibold text-gray-900 mt-1">{{ $lead->city }}</p>
                            </div>
                        @endif
                        @if ($lead->area)
                            <div>
                                <p class="text-sm text-gray-600">Area</p>
                                <p class="text-lg font-semibold text-gray-900 mt-1">{{ $lead->area }}</p>
                            </div>
                        @endif
                    </div>
                </x-card>

                <!-- Notes -->
                @if ($lead->notes)
                    <x-card title="Notes">
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $lead->notes }}</p>
                    </x-card>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Status and Priority -->
                <x-card title="Sales Information">
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-600">Status</p>
                            <div class="mt-2">
                                <x-badge :status="$lead->status">
                                    {{ ucfirst(str_replace('_', ' ', $lead->status)) }}
                                </x-badge>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Priority</p>
                            <div class="mt-2">
                                <x-priority-badge :priority="$lead->priority">
                                    {{ ucfirst($lead->priority) }}
                                </x-priority-badge>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Service Interest</p>
                            <p class="text-lg font-semibold text-gray-900 mt-1">{{ $lead->service_interest }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Source</p>
                            <p class="text-lg font-semibold text-gray-900 mt-1">{{ $lead->source }}</p>
                        </div>
                    </div>
                </x-card>

                <!-- Dates -->
                <x-card title="Timeline">
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-600">Created</p>
                            <p class="text-lg font-semibold text-gray-900 mt-1">{{ $lead->created_at->format('M d, Y') }}</p>
                            <p class="text-xs text-gray-500">{{ $lead->created_at->diffForHumans() }}</p>
                        </div>
                        @if ($lead->last_contacted_at)
                            <div>
                                <p class="text-sm text-gray-600">Last Contacted</p>
                                <p class="text-lg font-semibold text-gray-900 mt-1">{{ $lead->last_contacted_at->format('M d, Y') }}</p>
                                <p class="text-xs text-gray-500">{{ $lead->last_contacted_at->diffForHumans() }}</p>
                            </div>
                        @endif
                        @if ($lead->next_follow_up_at)
                            <div>
                                <p class="text-sm text-gray-600">Next Follow-up</p>
                                <p class="text-lg font-semibold text-gray-900 mt-1">{{ $lead->next_follow_up_at->format('M d, Y') }}</p>
                                <p class="text-xs text-gray-500">{{ $lead->next_follow_up_at->diffForHumans() }}</p>
                            </div>
                        @endif
                    </div>
                </x-card>

                <!-- Quick Actions -->
                <x-card title="Quick Actions">
                    <div class="space-y-3">
                        <form method="POST" action="{{ route('leads.markContacted', $lead) }}">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors font-medium text-sm">
                                📞 Mark Contacted
                            </button>
                        </form>

                        <form method="POST" action="{{ route('leads.scheduleFollowUp', $lead) }}" id="followUpForm" class="space-y-3">
                            @csrf
                            <input
                                type="date"
                                name="next_follow_up_at"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"
                            />
                            <button type="submit" class="w-full px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors font-medium text-sm">
                                ⏰ Schedule Follow-up
                            </button>
                        </form>

                        <form method="POST" action="{{ route('leads.destroy', $lead) }}" onsubmit="return confirm('Are you sure you want to delete this lead? This action cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium text-sm">
                                🗑️ Delete Lead
                            </button>
                        </form>
                    </div>
                </x-card>
            </div>
        </div>
    </div>
</x-app-layout>
