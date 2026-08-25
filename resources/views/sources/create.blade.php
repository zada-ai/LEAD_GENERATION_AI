<x-app-layout>
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('sources.index') }}" class="text-sm font-semibold text-blue-600 hover:underline">← Back to Sources</a>
                <h1 class="text-3xl font-bold text-gray-900 mt-1">Configure Lead Discovery Source</h1>
                <p class="text-gray-600 text-sm mt-1">Setup search criteria for compliant public business discovery</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
            <form action="{{ route('sources.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Basic Source Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Source Name *</label>
                        <input type="text" name="name" required placeholder="e.g. Mumbai Dental Clinics" value="{{ old('name') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Discovery Provider *</label>
                        <select name="type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                            <option value="website_search">Website Discovery Search</option>
                            <option value="manual_import">Manual Lead Ingestion</option>
                            <option value="csv_import">CSV File Ingestion</option>
                        </select>
                    </div>
                </div>

                <!-- Geographic & Category Parameters -->
                <div class="pt-4 border-t border-gray-100">
                    <h3 class="text-md font-bold text-gray-900 mb-4">Discovery Search Parameters</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Country</label>
                            <input type="text" name="config[country]" placeholder="e.g. India" value="{{ old('config.country', 'India') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">State / Province</label>
                            <input type="text" name="config[state]" placeholder="e.g. Maharashtra" value="{{ old('config.state') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">City</label>
                            <input type="text" name="config[city]" placeholder="e.g. Mumbai" value="{{ old('config.city', 'Mumbai') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Area / Locality</label>
                            <input type="text" name="config[area]" placeholder="e.g. Andheri West" value="{{ old('config.area') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Business Category</label>
                            <input type="text" name="config[category]" placeholder="e.g. Dental Clinic" value="{{ old('config.category', 'Dental Clinic') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Keyword</label>
                            <input type="text" name="config[keyword]" placeholder="e.g. dentist clinic" value="{{ old('config.keyword') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        </div>
                    </div>
                </div>

                <!-- Maximum Results -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Max Results per Run</label>
                        <input type="number" name="config[max_results]" min="1" max="50" value="10" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                    <a href="{{ route('sources.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 text-sm">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 shadow text-sm">
                        Save Source Configuration
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
