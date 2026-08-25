<x-app-layout>
    <div class="max-w-7xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Lead Sources & Discovery</h1>
                <p class="text-gray-600 mt-1">Discover, import, and configure multi-channel business lead sources</p>
            </div>
            <a href="{{ route('sources.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 rounded-lg text-sm font-semibold text-white hover:bg-blue-700 shadow transition">
                <span>➕</span> New Lead Source
            </a>
        </div>

        <!-- Sources Grid -->
        @if($sources->isEmpty())
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                <span class="text-4xl">🔍</span>
                <h3 class="text-lg font-bold text-gray-900 mt-3">No Lead Sources Configured</h3>
                <p class="text-gray-500 text-sm mt-1 max-w-md mx-auto">Create a targeted discovery search or import CSV files to automatically populate your lead CRM.</p>
                <a href="{{ route('sources.create') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700">
                    Create Your First Source
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($sources as $source)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between hover:shadow transition">
                        <div>
                            <div class="flex items-center justify-between">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-md bg-blue-50 text-blue-700 uppercase tracking-wider">
                                    {{ str_replace('_', ' ', $source->type) }}
                                </span>
                                <span class="text-xs text-gray-400">
                                    {{ $source->last_run_at ? 'Run ' . $source->last_run_at->diffForHumans() : 'Never run' }}
                                </span>
                            </div>

                            <h3 class="text-lg font-bold text-gray-900 mt-3">{{ $source->name }}</h3>
                            <p class="text-xs text-gray-500 mt-1">
                                Category: <span class="font-medium text-gray-700">{{ $source->config['category'] ?? 'All' }}</span> • 
                                Location: <span class="font-medium text-gray-700">{{ $source->config['city'] ?? $source->config['country'] ?? 'Global' }}</span>
                            </p>

                            <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between text-sm">
                                <span class="text-gray-500">Leads Imported:</span>
                                <span class="font-bold text-gray-900">{{ number_format($source->leads_count) }}</span>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center gap-2 pt-2">
                            <form action="{{ route('sources.runSearch', $source) }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                                    ▶ Run Discovery
                                </button>
                            </form>
                            <a href="{{ route('sources.show', $source) }}" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition">
                                Details
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $sources->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
