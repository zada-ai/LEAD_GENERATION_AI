<x-app-layout>
    <div class="max-w-7xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Follow-up Management</h1>
                <p class="text-gray-600 mt-1">Track and manage scheduled customer touchpoints and reminders</p>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="flex flex-wrap gap-2 border-b border-gray-200 pb-3">
            <a href="{{ route('follow-ups.index', ['tab' => 'due']) }}" class="px-4 py-2 rounded-lg text-sm font-semibold transition {{ $tab === 'due' ? 'bg-amber-100 text-amber-900 font-bold' : 'text-gray-600 hover:bg-gray-100' }}">
                ⏰ Due Today ({{ $counts['due'] }})
            </a>
            <a href="{{ route('follow-ups.index', ['tab' => 'upcoming']) }}" class="px-4 py-2 rounded-lg text-sm font-semibold transition {{ $tab === 'upcoming' ? 'bg-blue-100 text-blue-900 font-bold' : 'text-gray-600 hover:bg-gray-100' }}">
                📅 Upcoming ({{ $counts['upcoming'] }})
            </a>
            <a href="{{ route('follow-ups.index', ['tab' => 'completed']) }}" class="px-4 py-2 rounded-lg text-sm font-semibold transition {{ $tab === 'completed' ? 'bg-green-100 text-green-900 font-bold' : 'text-gray-600 hover:bg-gray-100' }}">
                ✔️ Completed ({{ $counts['completed'] }})
            </a>
            <a href="{{ route('follow-ups.index', ['tab' => 'all']) }}" class="px-4 py-2 rounded-lg text-sm font-semibold transition {{ $tab === 'all' ? 'bg-gray-200 text-gray-900 font-bold' : 'text-gray-600 hover:bg-gray-100' }}">
                All ({{ $counts['all'] }})
            </a>
        </div>

        <!-- Follow-ups Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            @if($followUps->isEmpty())
                <div class="p-12 text-center text-gray-500">
                    <span class="text-3xl">☕</span>
                    <p class="mt-2 text-sm">No follow-ups found in this view.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase text-left">
                            <tr>
                                <th class="px-6 py-3">Lead / Business</th>
                                <th class="px-6 py-3">Campaign</th>
                                <th class="px-6 py-3">Scheduled At</th>
                                <th class="px-6 py-3">Notes</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($followUps as $fu)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="px-6 py-4">
                                        <a href="{{ route('leads.show', $fu->lead) }}" class="font-bold text-gray-900 hover:text-blue-600">
                                            {{ $fu->lead->business_name }}
                                        </a>
                                        <p class="text-xs text-gray-500">{{ $fu->lead->contact_name }} • {{ $fu->lead->email }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-xs text-gray-600">
                                        {{ $fu->campaign?->name ?? 'Manual Follow-up' }}
                                    </td>
                                    <td class="px-6 py-4 text-xs font-medium text-gray-800">
                                        {{ $fu->scheduled_at->format('M d, Y') }}
                                        @if($fu->scheduled_at->isPast() && $fu->status === 'pending')
                                            <span class="ml-1 text-red-600 font-bold">(Overdue)</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-xs text-gray-600 max-w-xs truncate">
                                        {{ $fu->notes ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $fu->status_badge_color }}">
                                            {{ ucfirst($fu->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        @if($fu->status === 'pending')
                                            <form action="{{ route('follow-ups.complete', $fu) }}" method="POST" class="inline-block">
                                                @csrf
                                                <button type="submit" class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-xs font-semibold">
                                                    Mark Done
                                                </button>
                                            </form>
                                            <form action="{{ route('follow-ups.cancel', $fu) }}" method="POST" class="inline-block">
                                                @csrf
                                                <button type="submit" class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded text-xs font-semibold">
                                                    Cancel
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-gray-100">
                    {{ $followUps->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
