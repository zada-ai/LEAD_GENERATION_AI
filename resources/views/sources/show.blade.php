<x-app-layout>
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('sources.index') }}" class="text-sm font-semibold text-blue-600 hover:underline">← Back to Sources</a>
                <h1 class="text-3xl font-bold text-gray-900 mt-1">{{ $source->name }}</h1>
                <p class="text-gray-600 text-sm mt-1">Provider: <span class="font-medium text-gray-800">{{ str_replace('_', ' ', $source->type) }}</span></p>
            </div>
            <div class="flex items-center gap-3">
                <form action="{{ route('sources.destroy', $source) }}" method="POST" onsubmit="return confirm('Delete this source?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg text-sm font-semibold">
                        Delete
                    </button>
                </form>
            </div>
        </div>

        <!-- Configuration Overview -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6">
            <h3 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-3">Search Parameters</h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm">
                <div>
                    <span class="text-gray-500 block text-xs">Country</span>
                    <span class="font-semibold text-gray-900">{{ $source->config['country'] ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-gray-500 block text-xs">State / Province</span>
                    <span class="font-semibold text-gray-900">{{ $source->config['state'] ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-gray-500 block text-xs">City</span>
                    <span class="font-semibold text-gray-900">{{ $source->config['city'] ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-gray-500 block text-xs">Area</span>
                    <span class="font-semibold text-gray-900">{{ $source->config['area'] ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-gray-500 block text-xs">Category</span>
                    <span class="font-semibold text-gray-900">{{ $source->config['category'] ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-gray-500 block text-xs">Keyword</span>
                    <span class="font-semibold text-gray-900">{{ $source->config['keyword'] ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-gray-500 block text-xs">Leads Ingested</span>
                    <span class="font-bold text-blue-600">{{ number_format($source->leads_count) }}</span>
                </div>
                <div>
                    <span class="text-gray-500 block text-xs">Last Run</span>
                    <span class="font-medium text-gray-700">{{ $source->last_run_at ? $source->last_run_at->format('M d, Y H:i') : 'Never' }}</span>
                </div>
            </div>

            <!-- Execution Action -->
            <div class="pt-6 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4 bg-gray-50 p-4 rounded-xl">
                <div>
                    <h4 class="text-sm font-bold text-gray-900">Run Discovery Now</h4>
                    <p class="text-xs text-gray-500">Executes search, extracts compliant public records, deduplicates, and enriches new leads.</p>
                </div>
                <form action="{{ route('sources.runSearch', $source) }}" method="POST" class="flex items-center gap-3">
                    @csrf
                    <input type="number" name="max_results" min="1" max="50" value="10" class="w-20 px-3 py-2 border border-gray-300 rounded-lg text-sm" title="Number of results">
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow text-sm whitespace-nowrap">
                        Execute Search
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
