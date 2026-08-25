<x-app-layout :title="'Leads'">
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Leads Management</h1>
                <p class="text-gray-600 mt-1">Manage, qualify, and organize all potential business prospects</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('leads.export', request()->query()) }}" class="inline-flex items-center gap-2 px-3.5 py-2 bg-white border border-gray-300 rounded-lg text-xs font-semibold text-gray-700 hover:bg-gray-50 shadow-sm transition">
                    📥 Export CSV
                </a>
                <button type="button" onclick="document.getElementById('import-modal').classList.remove('hidden')" class="inline-flex items-center gap-2 px-3.5 py-2 bg-white border border-gray-300 rounded-lg text-xs font-semibold text-gray-700 hover:bg-gray-50 shadow-sm transition">
                    📤 Import CSV
                </button>
                <a href="{{ route('leads.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 rounded-lg text-xs font-semibold text-white hover:bg-blue-700 shadow transition">
                    ➕ Add Lead
                </a>
            </div>
        </div>

        <!-- Search and Filters -->
        <x-card>
            <form method="GET" action="{{ route('leads.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-3 text-sm">
                    <!-- Search -->
                    <div class="lg:col-span-2">
                        <input
                            type="text"
                            name="search"
                            placeholder="Search by name, email, phone, city..."
                            value="{{ request('search') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none text-sm"
                        />
                    </div>

                    <!-- AI Score Tier Filter -->
                    <select name="ai_score_tier" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <option value="">AI Score (All)</option>
                        <option value="very_high" {{ request('ai_score_tier') === 'very_high' ? 'selected' : '' }}>Very High (85 - 100)</option>
                        <option value="high" {{ request('ai_score_tier') === 'high' ? 'selected' : '' }}>High (70 - 84)</option>
                        <option value="medium" {{ request('ai_score_tier') === 'medium' ? 'selected' : '' }}>Medium (40 - 69)</option>
                        <option value="low" {{ request('ai_score_tier') === 'low' ? 'selected' : '' }}>Low (< 40)</option>
                    </select>

                    <!-- Status Filter -->
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <option value="">Status (All)</option>
                        <option value="new" {{ request('status') === 'new' ? 'selected' : '' }}>New</option>
                        <option value="contacted" {{ request('status') === 'contacted' ? 'selected' : '' }}>Contacted</option>
                        <option value="interested" {{ request('status') === 'interested' ? 'selected' : '' }}>Interested</option>
                        <option value="follow_up" {{ request('status') === 'follow_up' ? 'selected' : '' }}>Follow-up</option>
                        <option value="converted" {{ request('status') === 'converted' ? 'selected' : '' }}>Converted</option>
                        <option value="not_interested" {{ request('status') === 'not_interested' ? 'selected' : '' }}>Not Interested</option>
                        <option value="do_not_contact" {{ request('status') === 'do_not_contact' ? 'selected' : '' }}>Do Not Contact</option>
                    </select>

                    <!-- Priority Filter -->
                    <select name="priority" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <option value="">Priority (All)</option>
                        <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                        <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                    </select>

                    <!-- Source Filter -->
                    <select name="source" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <option value="">Source (All)</option>
                        <option value="Website" {{ request('source') === 'Website' ? 'selected' : '' }}>Website</option>
                        <option value="Facebook" {{ request('source') === 'Facebook' ? 'selected' : '' }}>Facebook</option>
                        <option value="Instagram" {{ request('source') === 'Instagram' ? 'selected' : '' }}>Instagram</option>
                        <option value="LinkedIn" {{ request('source') === 'LinkedIn' ? 'selected' : '' }}>LinkedIn</option>
                        <option value="Google" {{ request('source') === 'Google' ? 'selected' : '' }}>Google</option>
                        <option value="Manual" {{ request('source') === 'Manual' ? 'selected' : '' }}>Manual</option>
                        <option value="Import" {{ request('source') === 'Import' ? 'selected' : '' }}>Import</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-xs font-semibold">
                        🔍 Search & Filter
                    </button>
                    <a href="{{ route('leads.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-xs font-semibold">
                        Clear
                    </a>
                </div>
            </form>
        </x-card>

        <!-- Bulk Action Form & Leads Table -->
        <form id="bulk-form" action="{{ route('leads.bulkAction') }}" method="POST">
            @csrf
            <!-- Bulk Action Toolbar -->
            <div id="bulk-toolbar" class="hidden bg-blue-50 border border-blue-200 rounded-xl p-3 mb-4 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-blue-900"><span id="selected-count">0</span> leads selected</span>
                </div>
                <div class="flex items-center gap-2">
                    <select name="bulk_action" id="bulk_action_select" class="text-xs px-3 py-1.5 border border-gray-300 rounded-lg bg-white" onchange="toggleBulkValueInput(this.value)">
                        <option value="status">Set Status to...</option>
                        <option value="priority">Set Priority to...</option>
                        <option value="service">Set Target Service to...</option>
                        <option value="suppress">Mark as Do Not Contact (Suppress)</option>
                        <option value="delete">Delete Selected Leads</option>
                    </select>

                    <input type="text" name="bulk_value" id="bulk_value_input" placeholder="Value (e.g. interested)" class="text-xs px-3 py-1.5 border border-gray-300 rounded-lg bg-white">

                    <button type="submit" onclick="return confirm('Apply bulk action to selected leads?');" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg">
                        Apply
                    </button>
                </div>
            </div>

            <x-card>
                @if ($leads->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-200 text-left text-xs font-semibold text-gray-500 uppercase">
                                    <th class="py-3 px-3 w-8">
                                        <input type="checkbox" id="select-all" onclick="toggleSelectAll(this)" class="w-4 h-4 text-blue-600 rounded">
                                    </th>
                                    <th class="py-3 px-3">Business</th>
                                    <th class="py-3 px-3">Contact</th>
                                    <th class="py-3 px-3">AI Score</th>
                                    <th class="py-3 px-3 hidden md:table-cell">City</th>
                                    <th class="py-3 px-3">Status</th>
                                    <th class="py-3 px-3 hidden sm:table-cell">Priority</th>
                                    <th class="py-3 px-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($leads as $lead)
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="py-3 px-3">
                                            <input type="checkbox" name="lead_ids[]" value="{{ $lead->id }}" class="lead-checkbox w-4 h-4 text-blue-600 rounded" onclick="updateBulkToolbar()">
                                        </td>
                                        <td class="py-3 px-3 font-semibold text-gray-900">
                                            <a href="{{ route('leads.show', $lead) }}" class="hover:text-blue-600 block">
                                                {{ Str::limit($lead->business_name, 28) }}
                                            </a>
                                            <span class="text-xs text-gray-400 font-normal">{{ $lead->category }}</span>
                                        </td>
                                        <td class="py-3 px-3 text-gray-600">
                                            <p class="text-xs font-medium text-gray-800">{{ $lead->contact_name }}</p>
                                            <p class="text-[11px] text-gray-400 truncate max-w-[140px]">{{ $lead->email }}</p>
                                        </td>
                                        <td class="py-3 px-3">
                                            @if($lead->ai_score !== null)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold border {{ $lead->ai_score_badge_color }}">
                                                    {{ $lead->ai_score }}/100
                                                </span>
                                            @else
                                                <span class="text-xs text-gray-400">—</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-3 text-xs text-gray-600 hidden md:table-cell">{{ $lead->city ?? 'N/A' }}</td>
                                        <td class="py-3 px-3">
                                            <x-badge :status="$lead->status" size="sm">
                                                {{ ucfirst(str_replace('_', ' ', $lead->status)) }}
                                            </x-badge>
                                        </td>
                                        <td class="py-3 px-3 hidden sm:table-cell">
                                            <x-priority-badge :priority="$lead->priority">
                                                {{ ucfirst($lead->priority) }}
                                            </x-priority-badge>
                                        </td>
                                        <td class="py-3 px-3 text-right">
                                            <div class="flex justify-end gap-1.5">
                                                <a href="{{ route('leads.show', $lead) }}" class="text-xs bg-blue-50 text-blue-600 hover:bg-blue-100 px-2 py-1 rounded">View</a>
                                                <a href="{{ route('leads.edit', $lead) }}" class="text-xs bg-gray-100 text-gray-700 hover:bg-gray-200 px-2 py-1 rounded">Edit</a>
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
                        <div class="text-4xl mb-3">📭</div>
                        <h3 class="text-lg font-bold text-gray-900 mb-1">No Leads Found</h3>
                        <p class="text-gray-500 text-xs mb-4">{{ request('search') ? 'Try adjusting your search criteria.' : 'Create or discover leads to start outreach.' }}</p>
                        <a href="{{ route('leads.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-xs font-semibold hover:bg-blue-700">
                            + Add New Lead
                        </a>
                    </div>
                @endif
            </x-card>
        </form>
    </div>

    <!-- Import CSV Modal -->
    <div id="import-modal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900">Import Leads from CSV</h3>
                <button type="button" onclick="document.getElementById('import-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 text-xl font-bold">×</button>
            </div>
            <form action="{{ route('leads.importCsv') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Choose CSV File *</label>
                    <input type="file" name="csv_file" accept=".csv,text/csv" required class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="text-[11px] text-gray-400 mt-1">Columns supported: business_name, contact_name, email, phone, website, city, country, category, service_interest.</p>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('import-modal').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-xs font-semibold">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-lg text-xs font-semibold hover:bg-blue-700">Import & Deduplicate</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleSelectAll(master) {
            document.querySelectorAll('.lead-checkbox').forEach(cb => cb.checked = master.checked);
            updateBulkToolbar();
        }

        function updateBulkToolbar() {
            const count = document.querySelectorAll('.lead-checkbox:checked').length;
            const toolbar = document.getElementById('bulk-toolbar');
            const countSpan = document.getElementById('selected-count');

            if (count > 0) {
                toolbar.classList.remove('hidden');
                countSpan.innerText = count;
            } else {
                toolbar.classList.add('hidden');
            }
        }

        function toggleBulkValueInput(action) {
            const input = document.getElementById('bulk_value_input');
            if (action === 'suppress' || action === 'delete') {
                input.classList.add('hidden');
            } else {
                input.classList.remove('hidden');
            }
        }
    </script>
</x-app-layout>
