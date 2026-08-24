<x-app-layout :title="'Leads'">
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Leads</h1>
                <p class="text-gray-600 mt-1">Manage and track all your leads</p>
            </div>
            <x-link-button type="primary" href="{{ route('leads.create') }}">
                + Add Lead
            </x-link-button>
        </div>

        <!-- Search and Filters -->
        <x-card>
            <form method="GET" action="{{ route('leads.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    <!-- Search -->
                    <div class="lg:col-span-2">
                        <input
                            type="text"
                            name="search"
                            placeholder="Search by name, email, phone..."
                            value="{{ request('search') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"
                        />
                    </div>

                    <!-- Status Filter -->
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                        <option value="">All Status</option>
                        <option value="new" {{ request('status') === 'new' ? 'selected' : '' }}>New</option>
                        <option value="contacted" {{ request('status') === 'contacted' ? 'selected' : '' }}>Contacted</option>
                        <option value="interested" {{ request('status') === 'interested' ? 'selected' : '' }}>Interested</option>
                        <option value="follow_up" {{ request('status') === 'follow_up' ? 'selected' : '' }}>Follow-up</option>
                        <option value="converted" {{ request('status') === 'converted' ? 'selected' : '' }}>Converted</option>
                        <option value="not_interested" {{ request('status') === 'not_interested' ? 'selected' : '' }}>Not Interested</option>
                        <option value="do_not_contact" {{ request('status') === 'do_not_contact' ? 'selected' : '' }}>Do Not Contact</option>
                    </select>

                    <!-- Priority Filter -->
                    <select name="priority" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                        <option value="">All Priority</option>
                        <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                    </select>

                    <!-- Source Filter -->
                    <select name="source" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                        <option value="">All Sources</option>
                        <option value="Website" {{ request('source') === 'Website' ? 'selected' : '' }}>Website</option>
                        <option value="Facebook" {{ request('source') === 'Facebook' ? 'selected' : '' }}>Facebook</option>
                        <option value="Instagram" {{ request('source') === 'Instagram' ? 'selected' : '' }}>Instagram</option>
                        <option value="LinkedIn" {{ request('source') === 'LinkedIn' ? 'selected' : '' }}>LinkedIn</option>
                        <option value="Google" {{ request('source') === 'Google' ? 'selected' : '' }}>Google</option>
                        <option value="Manual" {{ request('source') === 'Manual' ? 'selected' : '' }}>Manual</option>
                        <option value="Import" {{ request('source') === 'Import' ? 'selected' : '' }}>Import</option>
                        <option value="Other" {{ request('source') === 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        🔍 Search
                    </button>
                    <x-link-button type="secondary" href="{{ route('leads.index') }}">
                        Clear
                    </x-link-button>
                </div>
            </form>
        </x-card>

        <!-- Leads Table -->
        <x-card>
            @if ($leads->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Business</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700 hidden sm:table-cell">Contact</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700 hidden md:table-cell">Email</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700 hidden lg:table-cell">Location</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700 hidden md:table-cell">Source</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Status</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700 hidden sm:table-cell">Priority</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($leads as $lead)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="py-3 px-4 font-medium text-gray-900">
                                        <a href="{{ route('leads.show', $lead) }}" class="hover:text-blue-600">
                                            {{ Str::limit($lead->business_name, 30) }}
                                        </a>
                                    </td>
                                    <td class="py-3 px-4 text-gray-600 hidden sm:table-cell">{{ Str::limit($lead->contact_name, 20) }}</td>
                                    <td class="py-3 px-4 text-gray-600 hidden md:table-cell text-xs">{{ Str::limit($lead->email, 20) }}</td>
                                    <td class="py-3 px-4 text-gray-600 hidden lg:table-cell text-xs">{{ $lead->city ?? 'N/A' }}</td>
                                    <td class="py-3 px-4 text-gray-600 hidden md:table-cell text-xs">{{ Str::limit($lead->source, 12) }}</td>
                                    <td class="py-3 px-4">
                                        <x-badge :status="$lead->status" size="sm">
                                            {{ ucfirst(str_replace('_', ' ', $lead->status)) }}
                                        </x-badge>
                                    </td>
                                    <td class="py-3 px-4 hidden sm:table-cell">
                                        <x-priority-badge :priority="$lead->priority">
                                            {{ ucfirst($lead->priority) }}
                                        </x-priority-badge>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex gap-2">
                                            <a href="{{ route('leads.show', $lead) }}" class="text-xs bg-blue-50 text-blue-600 hover:bg-blue-100 px-2 py-1 rounded transition-colors">View</a>
                                            <a href="{{ route('leads.edit', $lead) }}" class="text-xs bg-gray-100 text-gray-700 hover:bg-gray-200 px-2 py-1 rounded transition-colors">Edit</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6 flex justify-center">
                    {{ $leads->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="text-5xl mb-4">📭</div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Leads Found</h3>
                    <p class="text-gray-600 mb-6">{{ request('search') ? 'Try adjusting your search criteria.' : 'Get started by creating your first lead.' }}</p>
                    <x-link-button type="primary" href="{{ route('leads.create') }}">
                        + Create First Lead
                    </x-link-button>
                </div>
            @endif
        </x-card>
    </div>
</x-app-layout>
