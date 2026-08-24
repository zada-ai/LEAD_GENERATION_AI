<x-app-layout :title="'Dashboard'">
    <div class="space-y-8">
        <!-- Page Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
                <p class="text-gray-600 mt-1">Welcome back, {{ auth()->user()->name }}!</p>
            </div>
            <x-link-button type="primary" href="{{ route('leads.create') }}">
                + Add Lead
            </x-link-button>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
            <x-stat-card title="Total Leads" :value="$stats['total_leads']" icon="👥" color="blue" />
            <x-stat-card title="New" :value="$stats['new_leads']" icon="🆕" color="blue" />
            <x-stat-card title="Contacted" :value="$stats['contacted']" icon="📞" color="purple" />
            <x-stat-card title="Interested" :value="$stats['interested']" icon="💚" color="green" />
            <x-stat-card title="Follow-up" :value="$stats['follow_up']" icon="⏰" color="yellow" />
            <x-stat-card title="Converted" :value="$stats['converted']" icon="✓" color="emerald" />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Recent Leads -->
            <div class="lg:col-span-2">
                <x-card title="Recent Leads">
                    @if ($recentLeads->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-200">
                                        <th class="text-left py-3 px-4 font-semibold text-gray-700">Business</th>
                                        <th class="text-left py-3 px-4 font-semibold text-gray-700">Contact</th>
                                        <th class="text-left py-3 px-4 font-semibold text-gray-700">Category</th>
                                        <th class="text-left py-3 px-4 font-semibold text-gray-700">Status</th>
                                        <th class="text-left py-3 px-4 font-semibold text-gray-700">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($recentLeads as $lead)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="py-3 px-4 font-medium text-gray-900">{{ Str::limit($lead->business_name, 20) }}</td>
                                            <td class="py-3 px-4 text-gray-600">{{ Str::limit($lead->contact_name, 15) }}</td>
                                            <td class="py-3 px-4 text-gray-600 text-xs">{{ Str::limit($lead->category, 12) }}</td>
                                            <td class="py-3 px-4">
                                                <x-badge :status="$lead->status">
                                                    {{ ucfirst(str_replace('_', ' ', $lead->status)) }}
                                                </x-badge>
                                            </td>
                                            <td class="py-3 px-4">
                                                <x-link-button type="secondary" href="{{ route('leads.show', $lead) }}" class="text-xs">
                                                    View
                                                </x-link-button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4 text-center">
                            <x-link-button type="secondary" href="{{ route('leads.index') }}">
                                View All Leads
                            </x-link-button>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="text-4xl mb-2">📭</div>
                            <p class="text-gray-600 mb-4">No leads yet. Start by creating your first lead.</p>
                            <x-link-button type="primary" href="{{ route('leads.create') }}">
                                Create First Lead
                            </x-link-button>
                        </div>
                    @endif
                </x-card>
            </div>

            <!-- Status Distribution -->
            <div>
                <x-card title="Status Distribution">
                    <div class="space-y-3">
                        @php
                            $distribution = $statusDistribution;
                            $total = collect($distribution)->sum();
                        @endphp

                        @forelse ($distribution as $status => $count)
                            @if ($count > 0)
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-sm font-medium text-gray-700">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                                        <span class="text-sm font-semibold text-gray-900">{{ $count }}</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div 
                                            class="h-2 rounded-full bg-blue-600"
                                            style="width: {{ $total > 0 ? ($count / $total * 100) : 0 }}%"
                                        ></div>
                                    </div>
                                </div>
                            @endif
                        @empty
                            <p class="text-center text-gray-500 py-4">No leads yet</p>
                        @endforelse
                    </div>
                </x-card>
            </div>
        </div>

        <!-- Quick Actions -->
        <x-card title="Quick Actions">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <x-link-button type="primary" href="{{ route('leads.create') }}" class="justify-center">
                    + Add Lead
                </x-link-button>
                <x-link-button type="secondary" href="{{ route('leads.index') }}" class="justify-center">
                    View Leads
                </x-link-button>
                <x-link-button type="secondary" href="{{ route('profile.edit') }}" class="justify-center">
                    Settings
                </x-link-button>
            </div>
        </x-card>
    </div>
</x-app-layout>
